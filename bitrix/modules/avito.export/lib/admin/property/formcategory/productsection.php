<?php
namespace Avito\Export\Admin\Property\FormCategory;

use Bitrix\Main;
use Bitrix\Iblock;
use Avito\Export\Assert;
use Avito\Export\Concerns;

/** @noinspection PhpUnused */
class ProductSection extends Section
{
	use HasSku;
	use Concerns\HasLocale;

	public function title() : string
	{
		return self::getLocale('TITLE');
	}

	public function options(array $property, string $field) : array
	{
		$skuIblockId = $this->propertyIblockId($property);
		$skuPropertyId = $this->skuPropertyId($property);

		if ($skuIblockId <= 0 || $skuPropertyId <= 0) { return []; }

		return [
			'skuName' => 'PROP[' . $skuPropertyId . ']',
			'skuIblockId' => $skuIblockId,
			'property' => $field,
		];
	}

	protected function formIblockId(array $form) : int
	{
		Assert::notNull($form['skuIblockId'], 'skuIblockId');

		return $form['skuIblockId'];
	}

	protected function formSections(array $form) : array
	{
		Assert::notNull($form['skuIblockId'], 'iblockId');
		Assert::notNull($form['property'], 'property');

		$sku = (int)($form['sku'] ?? 0);

		if ($sku <= 0)
		{
			throw new Main\ArgumentException(self::getLocale('FORM_SKU_REQUIRED'));
		}

		if (!Main\Loader::includeModule('iblock'))
		{
			throw new Main\SystemException('IBLOCK_REQUIRED');
		}

		$queryRow = Iblock\ElementTable::getList([
			'filter' => [ '=ID' => $sku ],
			'select' => [ 'IBLOCK_SECTION_ID', 'IBLOCK_ID' ],
			'limit' => 1,
		]);

		$row = $queryRow->fetch();

		if ($row === false)
		{
			throw new Main\ArgumentException(self::getLocale('SKU_NOT_FOUND'));
		}

		$sections = [
			(int)$row['IBLOCK_SECTION_ID'],
		];

		$querySectionLinks = \CIBlockElement::GetElementGroups($sku, true, [ 'ID' ]);

		while ($sectionLink = $querySectionLinks->Fetch())
		{
			$sections[] = $sectionLink['ID'];
		}

		Main\Type\Collection::normalizeArrayValuesByInt($sections, false);

		if (empty($sections))
		{
			throw new Main\ArgumentException(self::getLocale('SKU_SECTION_REQUIRED'));
		}

		return $sections;
	}

	public function elementValues(string $propertyId, array $elementIds) : array
	{
		$skuMap = $this->mapOffersSku($elementIds);
		$skuValues = parent::elementValues($propertyId, array_unique($skuMap));

		return $this->makeOfferValues($skuValues, $skuMap);
	}

	public function saveValues(string $propertyId, array $elementIds, string $value) : void
	{
		$skuMap = $this->mapOffersSku($elementIds);
		$skuIds = array_unique($skuMap);

		parent::saveValues($propertyId, $skuIds, $value);
	}
}