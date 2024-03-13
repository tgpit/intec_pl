<?php
namespace Avito\Export\Feed\Source\Store;

use Avito\Export\Concerns;
use Avito\Export\Feed\Source;
use Avito\Export\Feed\Source\Context;
use Bitrix\Catalog;

class Fetcher extends Source\FetcherSkeleton
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	protected const STORE_AMOUNT_PREFIX = 'STORE_AMOUNT_';

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
			$result = [];
			$factory = new Source\Field\Factory();

			$query = Catalog\StoreTable::getList([
				'filter' => [ '=ACTIVE' => 'Y' ],
				'select' => [ 'ID', 'TITLE', 'ADDRESS' ],
			]);

			while ($row = $query->fetch())
			{
				$result[] = $factory->make([
					'ID' => static::STORE_AMOUNT_PREFIX . $row['ID'],
					'NAME' => sprintf('[%s] %s', $row['ID'], $row['TITLE'] ?: $row['ADDRESS']),
					'TYPE' => 'N',
				]);
			}

			return $result;
		});
	}

	public function filter(array $conditions, Source\Context $context) : array
	{
		return [
			'CATALOG' => Source\Routine\QueryFilter::make($conditions, $this->fields($context)),
		];
	}

	public function values(array $elements, array $parents, array $siblings, array $select, Context $context) : array
	{
		$result = [];
		$storeIds = $this->selectStores($select);

		if (!empty($storeIds))
		{
			$iterator = Catalog\StoreProductTable::getList([
				'filter' => [
					'=PRODUCT_ID' => array_keys($elements),
					'=STORE_ID' => $storeIds,
				],
				'select' => [
					'STORE_ID',
					'PRODUCT_ID',
					'AMOUNT',
				]
			]);

			while ($row = $iterator->fetch())
			{
				$result[$row['PRODUCT_ID']][static::STORE_AMOUNT_PREFIX . $row['STORE_ID']] = $row['AMOUNT'];
			}
		}

		return $result;
	}

	protected function selectStores($select) : array
	{
		$storeIds = [];

		foreach ($select as $name)
		{
			if (mb_strpos($name, static::STORE_AMOUNT_PREFIX) !== 0) { continue; }

			$storeIds[] = (int)str_replace(static::STORE_AMOUNT_PREFIX, '', $name);
		}

		return $storeIds;
	}
}