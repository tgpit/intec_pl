<?php

namespace Avito\Export\Admin\Property\ValueInherit;

use Bitrix\Main;
use Bitrix\Iblock;
use Avito\Export\Admin;
use Avito\Export\Concerns;
use Avito\Export\Feed\Source;
use Avito\Export\Admin\Property\FormCategory;
use Avito\Export\Utils\ArrayHelper;

class Characteristic
{
	use Concerns\HasOnceStatic;

	public static function properties(int $iblockId) : array
	{
		$cacheKey = implode(':', ['properties', $iblockId]);

		return static::onceStatic($cacheKey, static function() use ($iblockId) {
			$result = [];

			foreach (static::iblockSources($iblockId) as $type => $loopIblockId)
			{
				foreach (static::iblockProperties($loopIblockId) as $property)
				{
					$result[] = [
						$type,
						$property['ID'],
					];
				}
			}

			return $result;
		});
	}

	public static function parentValues(array $elementIds, int $iblockId) : array
	{
		if (empty($elementIds)) { return []; }

		$context = Source\ContextPool::iblockInstance($iblockId);
		$productIblockId = $context->productIblockId();

		if ($productIblockId === null) { return []; }

		$productMap = \CCatalogSKU::getProductList($elementIds, $iblockId);

		if (empty($productMap)) { return []; }

		$firstProduct = reset($productMap);
		$productToOffers = ArrayHelper::groupBy($productMap, 'ID');
		$leftProducts = array_column($productMap, 'ID', 'ID');
		$result = [];

		foreach (static::iblockProperties($firstProduct['IBLOCK_ID']) as $property)
		{
			$elementValues = static::elementValues(array_keys($leftProducts), $property);

			foreach ($leftProducts as $productId)
			{
				if (empty($elementValues[$productId])) { continue; }

				$value = static::extractValue($elementValues[$productId], $property);

				if (empty($value)) { continue; }

				foreach ($productToOffers[$productId] as $offerId => $offerLink)
				{
					$result[$offerId] = $value;
				}

				unset($leftProducts[$productId]);
			}

			if (empty($leftProducts)) { break; }
		}

		return $result;
	}

	public static function parentValue(int $elementId, int $iblockId)
	{
		$cacheKey = implode(':', ['parentValue', $elementId, $iblockId]);

		return static::onceStatic($cacheKey, static function() use ($elementId, $iblockId) {
			$context = Source\ContextPool::iblockInstance($iblockId);
			$productIblockId = $context->productIblockId();

			if ($productIblockId === null) { return null; }

			$productInfo = \CCatalogSKU::GetProductInfo($elementId, $iblockId);

			if ($productInfo === false) { return null; }

			$result = null;

			foreach (static::iblockProperties($productInfo['IBLOCK_ID']) as $property)
			{
				$elementValue = static::elementValue($productInfo['ID'], $property);

				if (empty($elementValue)) { continue; }

				$value = static::extractValue($elementValue, $property);

				if (!empty($value))
				{
					$result = $value;
					break;
				}
			}

			return $result;
		});
	}

	protected static function iblockSources(int $iblockId) : array
	{
		$context = Source\ContextPool::iblockInstance($iblockId);
		$productIblockId = $context->productIblockId();

		if ($productIblockId !== null)
		{
			$result = [
				FormCategory\Registry::ELEMENT => $context->iblockId(),
				FormCategory\Registry::PRODUCT_ELEMENT => $context->productIblockId(),
			];
		}
		else
		{
			$result = [
				FormCategory\Registry::ELEMENT => $context->iblockId(),
			];
		}

		return $result;
	}

	protected static function iblockProperties(int $iblockId) : array
	{
		$cacheKey = implode(':', ['iblockProperties', $iblockId]);

		return static::onceStatic($cacheKey, static function() use ($iblockId) {
			if (!Main\Loader::includeModule('iblock')) { return []; }

			$query = Iblock\PropertyTable::getList([
				'select' => [ 'IBLOCK_ID', 'ID', 'MULTIPLE' ],
				'filter' => [
					'=IBLOCK_ID' => $iblockId,
					'=ACTIVE' => true,
					'=USER_TYPE' => Admin\Property\CharacteristicProperty::USER_TYPE,
				],
				'order' => [
					'SORT' => 'ASC',
					'ID' => 'ASC',
				],
			]);

			return $query->fetchAll();
		});
	}

	protected static function elementValues(array $elementIds, array $property) : array
	{
		if (empty($elementIds)) { return []; }

		$isMultiple = ($property['MULTIPLE'] === 'Y');

		$query = \CIBlockElement::GetPropertyValues($property['IBLOCK_ID'], [ '=ID' => $elementIds ], $isMultiple, [ 'ID' => $property['ID'] ]);
		$result = [];

		while ($row = $query->Fetch())
		{
			$elementId = (int)$row['IBLOCK_ELEMENT_ID'];

			$result[$elementId] = $row;
		}

		return $result;
	}

	protected static function elementValue(int $elementId, array $property) : ?array
	{
		$values = static::elementValues([ $elementId ], $property);

		return $values[$elementId] ?? null;
	}

	protected static function extractValue(array $row, array $property) : ?array
	{
		$isMultiple = ($property['MULTIPLE'] === 'Y');

		if (empty($row[$property['ID']])) { return null; }

		if ($isMultiple)
		{
			if (empty($row['DESCRIPTION'][$property['ID']])) { return null; }

			return array_combine(
				(array)$row['DESCRIPTION'][$property['ID']],
				(array)$row[$property['ID']]
			);
		}

		$value = Admin\Property\CharacteristicProperty::convertFromDB($property, [ 'VALUE' => $row[$property['ID']] ]);

		return $value['VALUE'];
	}
}