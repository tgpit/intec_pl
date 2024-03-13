<?php
namespace Avito\Export\Push\Engine\Steps;

use Bitrix\Main;
use Avito\Export\Config;
use Avito\Export\DB;
use Avito\Export\Feed;
use Avito\Export\Push;
use Avito\Export\Watcher;

class Collector extends Step
{
	public const TYPE = 'collector';

	protected $fetcherPool;

	public function __construct(Push\Engine\Controller $controller)
	{
		parent::__construct($controller);
		$this->fetcherPool = new Feed\Source\FetcherPool();
	}

	public function getName() : string
	{
		return static::TYPE;
	}

	public function start(string $action, $offset = null) : void
	{
		$feed = $this->controller->getSetup()->getExchange()->fillFeed();

		if ($feed === null) { return; }

		$settings = $this->controller->getSetup()->getSettings();
		$fieldMapCollection = $settings->fieldMapCollection($feed);
		$changesFilter = null;

		if ($action === Watcher\Engine\Controller::ACTION_CHANGE)
		{
			$changesFilter = $this->feedChangesFilter();

			if ($changesFilter === null) { return; }
		}

		do
		{
			[$feedOffers, $offset, $hasNext] = $this->feedOffers($changesFilter, $offset);

			foreach ($this->groupByIblock($feedOffers) as $iblockId => $iblockOffers)
			{
				$context = $this->getFeed()->iblockContext($iblockId);
				$fieldMap = $fieldMapCollection->byIblockId($iblockId);
				$tagSources = $fieldMap->select();
				$queryBuilder = new Feed\Source\Routine\QueryBuilder($this->fetcherPool);

				$queryBuilder->bootSources($tagSources, $context);

				[$elements, $parents] = $this->emulateElements($iblockOffers, $context);

				$sourceValues = $queryBuilder->fetch($tagSources, $elements, $parents, $context);
				$targetValues = $this->collectValues($fieldMap, $sourceValues);
                $targetValues = $this->extendValues($targetValues, $context);

				$this->writeStamp($iblockOffers, $targetValues);
			}
		}
		while ($hasNext);
	}

	public function afterChange() : void
	{
		$changesFilter = $this->storageChangesFilter();

		if ($changesFilter === null) { return; }

		$this->resetByFilter([
			'filter' => array_merge(
				[ '=PUSH_ID' => $this->getPush()->getId() ],
				$changesFilter,
				[ '<TIMESTAMP_X' => $this->getParameter('INIT_TIME') ]
 		    ),
		]);
	}

	public function afterRefresh() : void
	{
		$this->resetByFilter([
			'filter' => [
				'=PUSH_ID' => $this->getPush()->getId(),
				'<TIMESTAMP_X' => $this->getParameter('INIT_TIME'),
				'!=VALUE' => Stamp\RepositoryTable::VALUE_NULL,
			],
		]);
	}

	protected function resetByFilter(array $filter) : void
	{
		$batch = new DB\Facade\BatchUpdate(Stamp\RepositoryTable::class);
		$batch->run($filter, [
			'VALUE' => Stamp\RepositoryTable::VALUE_NULL,
			'STATUS' => Stamp\RepositoryTable::STATUS_WAIT,
			'REPEAT' => 0,
			'TIMESTAMP_X' => new Main\Type\DateTime(),
		]);
	}

	protected function feedOffers(array $changesFilter = null, $offset = null) : array
	{
		$filter = [
			'=FEED_ID' => $this->getFeed()->getId(),
		];

		if ($offset !== null)
		{
			$filter['>ELEMENT_ID'] = (int)$offset;
		}

		if ($changesFilter !== null)
		{
			$filter = array_merge($filter, $changesFilter);
		}

		$filter['=STATUS'] = true;

		$query = Feed\Engine\Steps\Offer\Table::getList([
			'select' => [ 'ELEMENT_ID', 'PARENT_ID', 'REGION_ID', 'PRIMARY', 'IBLOCK_ID' ],
			'filter' => $filter,
			'order' => [ 'ELEMENT_ID' => 'ASC' ],
			'limit' => max(1, (int)Config::getOption('push_collect_limit', 500)),
		]);

		$elements = $query->fetchAll();
		$hasNext = false;

		if (!empty($elements))
		{
			$last = end($elements);
			$hasNext = true;
			$offset = $last['ELEMENT_ID'];
		}

		return [$elements, $offset, $hasNext];
	}

	protected function feedChangesFilter() : ?array
	{
		$changes = $this->getParameter('CHANGES');
		$offerChanges = $changes[Feed\Engine\Steps\Offer::TYPE] ?? null;

		if (empty($offerChanges) || !is_array($offerChanges)) { return null; }

		$query = Feed\Engine\Steps\Offer\Table::getList([
			'filter' => [
				'=FEED_ID' => $this->getFeed()->getId(),
				'=PARENT_ID' => $offerChanges,
			],
			'select' => [ 'ELEMENT_ID', 'PARENT_ID' ],
		]);

		$rows = $query->fetchAll();
		$existsParents = array_column($rows, 'PARENT_ID');

		if (empty($existsParents))
		{
			return [ '=ELEMENT_ID' => $offerChanges ];
		}

		$aloneElements = array_diff($offerChanges, $existsParents);
		$aloneElements = array_diff($aloneElements, array_column($rows, 'ELEMENT_ID'));

		if (empty($aloneElements))
		{
			return [ '=PARENT_ID' => $existsParents ];
		}

		return [
			[
				'LOGIC' => 'OR',
				[ '=ELEMENT_ID' => $aloneElements ],
				[ '=PARENT_ID' => $existsParents ],
			],
		];
	}

	protected function storageChangesFilter() : ?array
	{
		$changes = $this->getParameter('CHANGES');
		$offerChanges = $changes[Feed\Engine\Steps\Offer::TYPE] ?? null;

		if (empty($offerChanges) || !is_array($offerChanges)) { return null; }

		$query = Feed\Engine\Steps\Offer\Table::getList([
			'filter' => [
				'=FEED_ID' => $this->getFeed()->getId(),
				'=PARENT_ID' => $offerChanges,
			],
			'select' => [ 'PARENT_ID', 'ELEMENT_ID' ],
		]);

		$rows = $query->fetchAll();
		$changesMap = array_flip($offerChanges);
		$changesMap = array_diff_key($changesMap, array_column($rows, 'PARENT_ID', 'PARENT_ID'));
		$changesMap += array_column($rows, 'ELEMENT_ID', 'ELEMENT_ID');

		return [
			'=ELEMENT_ID' => array_keys($changesMap),
		];
	}

	protected function groupByIblock(array $elements) : array
	{
		$result = [];

		foreach ($elements as $element)
		{
			$iblockId = $element['IBLOCK_ID'];

			if (!isset($result[$iblockId]))
			{
				$result[$iblockId] = [];
			}

			$result[$iblockId][] = $element;
		}

		return $result;
	}

	protected function emulateElements(array $feedOffers, Feed\Source\Context $context) : array
	{
		$elements = [];
		$parents = [];

		foreach ($feedOffers as $feedOffer)
		{
			if (!empty($feedOffer['PARENT_ID']) && $context->hasOffers())
			{
				$parents[$feedOffer['PARENT_ID']] = [
					'IBLOCK_ID' => $context->iblockId(),
					'ID' => $feedOffer['PARENT_ID'],
				];

				$elements[$feedOffer['ELEMENT_ID']] = [
					'IBLOCK_ID' => $context->offerIblockId(),
					'ID' => $feedOffer['ELEMENT_ID'],
				];
			}
			else
			{
				$elements[$feedOffer['ELEMENT_ID']] = [
					'IBLOCK_ID' => $context->iblockId(),
					'ID' => $feedOffer['ELEMENT_ID'],
				];
			}
		}

		return [$elements, $parents];
	}

	protected function collectValues(Push\Setup\FieldMap $fieldMap, array $sourceValues) : array
	{
		$result = [];

		foreach ($sourceValues as $elementId => $elementValues)
		{
			$targetValues = new Push\Engine\Data\TargetValues();

			foreach ($fieldMap->all() as $tagLink)
			{
				if (!isset($tagLink['TYPE'], $tagLink['FIELD'])) { continue; }

				$target = $tagLink['TARGET'];
				$value = $elementValues[$tagLink['TYPE']][$tagLink['FIELD']] ?? null;

				if ($value === null) { continue; }

				$targetValues->add($target, $value);
			}

			$result[$elementId] = $targetValues;
		}

		return $result;
	}

    protected function extendValues(array $targetValues, Feed\Source\Context $context) : array
    {
        $event = new Main\Event(Config::getModuleName(), Push\EventActions::OFFER_EXTEND, [
            'VALUES' => $targetValues,
            'FEED_NAME' => $this->getFeed()->getName(),
            'FEED_ID' => $this->getFeed()->getId(),
            'FILE_NAME' => $this->getFeed()->getFileName(),
            'CONTEXT' => $context,
        ]);

        $event->send();

		return $targetValues;
    }

	protected function writeStamp(array $feedOffers, array $targetValues) : void
	{
		$command = new Push\Engine\Command\StampWriter($this->controller->getSetup());

		$command->write($feedOffers, $targetValues);
	}
}