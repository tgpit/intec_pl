<?php
namespace Avito\Export\Feed\Source\Product;

use Avito\Export\Concerns;
use Avito\Export\Feed\Source;
use Avito\Export\Feed\Source\Context;
use Bitrix\Catalog;

class Fetcher extends Source\FetcherSkeleton
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function listener() : Source\Listener
	{
		return new Listener();
	}

	public function title() : string
	{
		return self::getLocale('TITLE');
	}

	public function modules() : array
	{
		return [ 'catalog' ];
	}

	public function fields(Source\Context $context) : array
	{
		return $this->once('fields', function() {
			return [
				new AvailableField([
					'ID' => 'AVAILABLE',
					'NAME' => self::getLocale('FIELD_AVAILABLE'),
				]),
				new Source\Field\NumberField([
					'ID' => 'QUANTITY',
					'NAME' => self::getLocale('FIELD_QUANTITY'),
				]),
				new Source\Field\NumberField([
					'ID' => 'WIDTH',
					'NAME' => self::getLocale('FIELD_WIDTH'),
				]),
				new Source\Field\NumberField([
					'ID' => 'HEIGHT',
					'NAME' => self::getLocale('FIELD_HEIGHT'),
				]),
				new Source\Field\NumberField([
					'ID' => 'LENGTH',
					'NAME' => self::getLocale('FIELD_LENGTH'),
				]),
				new Source\Field\NumberField([
					'ID' => 'WEIGHT',
					'NAME' => self::getLocale('FIELD_WEIGHT'),
				]),
				new Source\Field\EnumField([
					'ID' => 'TYPE',
					'NAME' => self::getLocale('FIELD_TYPE'),
					'VARIANTS' => array_filter(array_map(function (string $type) {
						$constant = $this->typeConstant($type);

						if ($constant === null) { return null; }

						return [
							'ID' => $constant,
							'VALUE' => self::getLocale('FIELD_TYPE_' . $type),
						];
					}, [
						'PRODUCT',
						'SET',
						'OFFER',
						'SERVICE',
					])),
				]),
			];
		});
	}

	public function filter(array $conditions, Source\Context $context) : array
	{
		$typeConditions = array_filter($conditions, static function(array $condition) { return $condition['FIELD'] === 'TYPE'; });
		$otherConditions = array_diff_key($conditions, $typeConditions);

		return $this->typeFilter($typeConditions, $context) + $this->commonFilter($otherConditions, $context);
	}

	protected function typeFilter(array $conditions, Source\Context $context) : array
	{
		if (empty($conditions)) { return []; }

		$filter = Source\Routine\QueryFilter::make($conditions, $this->fields($context));
		$type = 'CATALOG';

		foreach ($filter as $field => $values)
		{
			if (!is_array($values)) { continue; }

			$inverse = (mb_strpos($field, '!') === 0);
			$elementValues = array_diff($values, [ $this->typeConstant('OFFER') ]);
			$offerValues = array_intersect($values, [ $this->typeConstant('OFFER') ]);

			if (!empty($elementValues) && !empty($offerValues)) { break; }

			if (!empty($elementValues))
			{
				$type = $inverse ? 'OFFER' : 'ELEMENT';
			}
			else
			{
				$type = $inverse ? 'ELEMENT' : 'OFFER';
			}
		}

		return [
			$type => $filter,
		];
	}

	protected function typeConstant(string $name) : ?int
	{
		$newConstant = Catalog\ProductTable::class . '::TYPE_' . $name;

		if (defined($newConstant)) { return constant($newConstant); }

		$oldConstant = \CCatalogProduct::class . '::TYPE_' . $name;

		if (defined($oldConstant)) { return constant($oldConstant); }

		return null;
	}

	protected function commonFilter(array $conditions, Source\Context $context) : array
	{
		if (empty($conditions)) { return []; }

		return [
			'CATALOG' => Source\Routine\QueryFilter::make($conditions, $this->fields($context)),
		];
	}

	public function values(array $elements, array $parents, array $siblings, array $select, Context $context) : array
	{
		$result = [];
		$selectMap = array_flip($select);

		$query = Catalog\ProductTable::getList([
			'select' => array_merge(['ID'], $select),
			'filter' => [ '=ID' => array_keys($elements) ],
		]);

		while ($row = $query->fetch())
		{
			$result[$row['ID']] = array_intersect_key($row, $selectMap);
		}

		return $result;
	}
}