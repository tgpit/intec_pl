<?php
namespace Avito\Export\Feed\Source\Region;

use Avito\Export\Concerns;
use Avito\Export\Feed\Source;
use Avito\Export\Config;
use Avito\Export\Utils\Value;
use Bitrix\Main;
use Bitrix\Iblock;
use Bitrix\Catalog;

class Fetcher extends Source\FetcherSkeleton
	implements Source\FetcherCloneable
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public const FIELD_PRICE = 'PRICE';
	public const FIELD_PRICE_DISCOUNT = 'PRICE_DISCOUNT';

	protected const PRICE_MAP = [
		self::FIELD_PRICE => 'VALUE',
		self::FIELD_PRICE_DISCOUNT => 'DISCOUNT'
	];

	/** @var Source\Fetcher[] */
	protected $sources;
	protected $fetchedRegionIds;

	public function __construct()
	{
		$this->sources = [
			'ELEMENT' => new Source\Element\Fetcher(),
			'PROPERTY' => new Source\ElementProperty\Fetcher(),
		];
	}

	public function listener() : Source\Listener
	{
		return new Source\NoValue\Listener();
	}

	public function title() : string
	{
		return self::getLocale('TITLE');
	}

	public function modules() : array
	{
		return [ 'iblock' ];
	}

	public function order() : int
	{
		return 600;
	}

	public function extend(array $fields, Source\Data\SourceSelect $sources, Source\Context $context) : void
	{
		$priceSelect = array_intersect_key(self::PRICE_MAP, array_flip($fields));

		if (empty($priceSelect)) { return; }

		$childContext = new Source\Context($context->regionIblockId(), $context->siteId());
		$regionPrices = $this->regionsPriceTypes($childContext);

		if (empty($regionPrices)) { return; }

		$priceTypes = array_unique(array_merge(...array_values($regionPrices)));

		foreach ($priceTypes as $priceType)
		{
			foreach ($priceSelect as $subfield)
			{
				$sources->add('PRICE', "{$priceType}_{$subfield}");
			}
		}
	}

	public function fields(Source\Context $context) : array
	{
		if ($context->regionIblockId() === null) { return []; }

		return $this->once('fields', function() use ($context) {
			$childContext = new Source\Context($context->regionIblockId(), $context->siteId());
			$result = [];

			foreach ($this->sources as $prefix => $source)
			{
				$childFields = $source->fields($childContext);

				foreach ($childFields as $childField)
				{
					$result[] = $childField->copy([
						'ID' => $prefix . '_' . $childField->id(),
						'FILTERABLE' => false,
					]);
				}
			}

			if (Main\ModuleManager::isModuleInstalled('catalog') && $this->hasPriceProperty($childContext))
			{
				$result[] = new Source\Field\NumberField([
					'ID' => static::FIELD_PRICE,
					'NAME' => self::getLocale('FIELD_PRICE'),
					'FILTERABLE' => false,
				]);
				$result[] = new Source\Field\NumberField([
					'ID' => static::FIELD_PRICE_DISCOUNT,
					'NAME' => self::getLocale('FIELD_PRICE_DISCOUNT'),
					'FILTERABLE' => false,
				]);
			}

			return $result;
		});
	}

	public function values(array $elements, array $parents, array $siblings, array $select, Source\Context $context) : array
	{
		if ($context->regionIblockId() === null) { return []; }

		$regionSelect = array_diff($select, array_keys(static::PRICE_MAP));
		$priceSelect = array_intersect_key(static::PRICE_MAP, array_flip($select));
		$elementIds = array_keys($elements);

		$childContext = new Source\Context($context->regionIblockId(), $context->siteId());
		$regionValues = $this->regionValues($childContext, $regionSelect);
		$priceValues = $this->priceValues($elementIds, $siblings, $priceSelect, $childContext);

		return $this->combineValues(
			array_fill_keys($elementIds, $regionValues),
			$priceValues
		);
	}

	protected function combineValues(array ...$partials) : array
	{
		$result = array_shift($partials);

		foreach ($partials as $partial)
		{
			foreach ($partial as $elementId => $regionValues)
			{
				foreach ($regionValues as $regionId => $values)
				{
					if (!isset($result[$elementId][$regionId]))
					{
						$result[$elementId][$regionId] = $values;
					}
					else
					{
						$result[$elementId][$regionId] += $values;
					}
				}
			}
		}

		return $result;
	}

	protected function priceValues(array $elementIds, array $siblings, array $priceSelect, Source\Context $context) : array
	{
		if (empty($priceSelect)) { return []; }

		$result = [];

		foreach ($this->regionsPriceTypes($context) as $regionId => $priceTypes)
		{
			foreach ($elementIds as $elementId)
			{
				foreach ($priceSelect as $field => $siblingField)
				{
					$fieldValues = [];

					foreach ($priceTypes as $priceType)
					{
						$name = "{$priceType}_{$siblingField}";
						$value = $siblings[$elementId][Source\Registry::PRICE_FIELD][$name] ?? null;

						if (Value::isEmpty($value)) { continue; }

						$fieldValues[] = (float)$value;
					}

					if (empty($fieldValues)) { continue; }

					$result[$elementId][$regionId][$field] = min($fieldValues);
				}
			}
		}

		return $result;
	}

	protected function regionValues(Source\Context $context, array $select) : array
	{
		$cacheSign = $context->iblockId() . ':' . implode('|', $select);

		return $this->once($cacheSign, function() use ($context, $select) {
			$regions = $this->regions($context, $this->splitSelect($select, 'ELEMENT'));
			$result = [];

			foreach ($this->sources as $prefix => $source)
			{
				$sourceSelect = $this->splitSelect($select, $prefix);

				if (empty($sourceSelect)) { continue; }

				$sourceValues = $source->values($regions, [], [], $sourceSelect, $context);

				foreach ($sourceValues as $regionId => $sourceData)
				{
					if (!isset($result[$regionId])) { $result[$regionId] = []; }

					foreach ($sourceData as $sourceCode => $sourceValue)
					{
						$fieldName = $prefix . '_' . $sourceCode;

						$result[$regionId][$fieldName] = $sourceValue;
					}
				}
			}

			return $result;
		});
	}

	protected function regions(Source\Context $context, array $select = []) : array
	{
		if ($this->fetchedRegionIds !== null && empty($this->fetchedRegionIds)) { return []; }

		$result = [];
		$select = array_merge(['ID'], $select);

		$query = \CIBlockElement::GetList(
			[
				'SORT' => 'ASC',
				'ID' => 'ASC',
			],
			$this->regionFilter($context),
			false,
			[ 'nTopCount' => 100 ],
			$select
		);

		while ($row = $query->Fetch())
		{
			$result[$row['ID']] = $row;
		}

		$this->fetchedRegionIds = array_keys($result);

		return $result;
	}

	protected function regionFilter(Source\Context $context) : array
	{
		if ($this->fetchedRegionIds !== null)
		{
			return [
				'IBLOCK_ID' => $context->iblockId(),
				'=ID' => $this->fetchedRegionIds,
			];
		}

		$filter = [
			'IBLOCK_ID' => $context->iblockId(),
			'ACTIVE' => 'Y',
			'ACTIVE_DATE' => 'Y',
		];

		if ($this->hasAvitoExportProperty($context))
		{
			$filter['!=PROPERTY_AVITO_EXPORT'] = false;
		}

		return $filter;
	}

	protected function splitSelect(array $select, string $prefix) : array
	{
		$result = [];
		$prefixLength = mb_strlen($prefix . '_');

		foreach ($select as $selectCode)
		{
			if (mb_strpos($selectCode, $prefix . '_') !== 0) { continue; }

			$result[] = mb_substr($selectCode, $prefixLength);
		}

		return $result;
	}

	protected function hasAvitoExportProperty(Source\Context $context) : bool
	{
		$iterator = Iblock\PropertyTable::getList([
			'select' => [ 'ID' ],
			'filter' => [
				'=IBLOCK_ID' => $context->iblockId(),
				'=CODE' => 'AVITO_EXPORT'
			],
			'limit' => 1,
		]);

		return (bool)$iterator->fetch();
	}

	protected function hasPriceProperty(Source\Context $context) : bool
	{
		$iterator = Iblock\PropertyTable::getList([
			'select' => ['ID'],
			'filter' => [
				'=IBLOCK_ID' => $context->iblockId(),
				'=CODE' => $this->priceTypeCode(),
				'=ACTIVE' => true
			],
			'limit' => 1,
		]);

		return (bool)$iterator->fetch();
	}

	protected function regionsPriceTypes(Source\Context $context)
	{
		$cacheSign = 'regionsPriceTypes:' . $context->iblockId();

		return $this->once($cacheSign, function() use ($context) {
			$regionIds = array_keys($this->regions($context));
			$priceProperties = $this->pricePropertyValues($regionIds, $context);
			$propertyMap = $this->mapPriceProperties($priceProperties);

			return $this->combinePriceTypes($priceProperties, $propertyMap);
		});
	}

	protected function pricePropertyValues(array $regionIds, Source\Context $context) : array
	{
		if (empty($regionIds)) { return []; }

		$code = $this->priceTypeCode();
		$properties = [];

		\CIBlockElement::GetPropertyValuesArray($properties, $context->iblockId(), [ 'ID' => $regionIds ], [
			'CODE' => [ $code ],
		]);

		$result = array_fill_keys($regionIds, []);

		foreach ($properties as $regionId => $values)
		{
			if (empty($values[$code]['VALUE'])) { continue; }

			$result[$regionId] = (array)$values[$code]['VALUE'];
		}

		return $result;
	}

	protected function combinePriceTypes(array $regionProperties, array $propertyMap) : array
	{
		$result = [];
		$basePriceType = $this->basePriceType();

		foreach ($regionProperties as $regionId => $priceValues)
		{
			$regionPrices = [];

			foreach ($priceValues as $priceValue)
			{
				if (!isset($propertyMap[$priceValue])) { continue; }

				$regionPrices[] = (int)$propertyMap[$priceValue];
			}

			if (empty($regionPrices) && $basePriceType !== null)
			{
				$regionPrices[] = $basePriceType;
			}

			$result[$regionId] = $regionPrices;
		}

		return $result;
	}

	protected function mapPriceProperties(array $priceProperties) : array
	{
		if (empty($priceProperties) || !Main\Loader::includeModule('catalog')) { return []; }

		$values = array_merge(...array_values($priceProperties));
		$field = $this->priceTypeField();

		$query = Catalog\GroupTable::getList([
			'filter' => [ "=$field" => array_unique($values) ],
			'select' => [ 'ID', $field ]
		]);

		return array_column($query->fetchAll(), 'ID', 'NAME');
	}

	protected function basePriceType() : ?int
	{
		if (!Main\Loader::includeModule('catalog')) { return null; }

		return \CCatalogGroup::GetBaseGroup()['ID'] ?? null;
	}

	protected function priceTypeCode() : string
	{
		return Config::getOption('region_price_code', 'AVITO_PRICE_TYPE');
	}

	protected function priceTypeField() : string
	{
		$allowedFields = ['ID', 'NAME', 'XML_ID'];
		$field = Config::getOption('region_price_field', 'NAME');

		return in_array($field, $allowedFields, true) ? $field : 'NAME';
	}
}