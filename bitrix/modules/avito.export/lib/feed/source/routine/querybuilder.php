<?php
namespace Avito\Export\Feed\Source\Routine;

use Avito\Export\Feed\Setup;
use Avito\Export\Feed\Source;

class QueryBuilder
{
	protected $fetcherPool;

	public function __construct(Source\FetcherPool $fetcherPool)
	{
		$this->fetcherPool = $fetcherPool;
	}

	public function bootSources(Source\Data\SourceSelect $tagSources, Source\Context $context) : void
	{
		$previousSources = [];

		do
		{
			$sources = array_flip($tagSources->sources());
			$sources = array_diff_key($sources, $previousSources);

			foreach ($sources as $type => $dummy)
			{
				$fetcher = $this->fetcherPool->some($type);
				$select = $tagSources->fields($type);

				$fetcher->extend($select, $tagSources, $context);
			}

			$previousSources += $sources;
		}
		while (!empty($sources));
	}

	public function compileFilters(Setup\FilterMap $filterMap, Source\Context $context, array $changesFilter = null) : array
	{
		$command = new QueryBuilder\Filter($this->fetcherPool);

		return $command->compile($filterMap, $context, $changesFilter);
	}

	public function fetch(Source\Data\SourceSelect $tagSources, array $elements, array $parents, Source\Context $context) : array
	{
		$result = [];
		$types = $tagSources->sources();
		$sources = array_combine($types, array_map(
			function(string $type) { return $this->fetcherPool->some($type); },
			$types
		));

		uasort($sources, static function(Source\Fetcher $sourceA, Source\Fetcher $sourceB) {
			return $sourceA->order() <=> $sourceB->order();
		});

		foreach ($sources as $type => $fetcher)
		{
			$select = $tagSources->fields($type);

			foreach ($fetcher->values($elements, $parents, $result, $select, $context) as $elementId => $values)
			{
				if (!isset($result[$elementId])) { $result[$elementId] = []; }
				if (!isset($result[$elementId][$type])) { $result[$elementId][$type] = []; }

				$result[$elementId][$type] += $values;
			}
		}

		return $result;
	}
}