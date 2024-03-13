<?php
namespace Avito\Export\Push\Engine\Steps;

use Avito\Export\Config;
use Avito\Export\Watcher;

class Submitter extends Step
{
	public const TYPE = 'submitter';

	public function getName() : string
	{
		return static::TYPE;
	}

	public function start(string $action, $offset = null) : void
	{
		do
		{
			$stampQueue = $this->stampQueue();

			foreach ($stampQueue->groupByType() as $type => $typeQueue)
			{
				$controller = Submitter\Factory::make($type, $this);
				$controller->process($typeQueue);

				if ($this->controller->isTimeExpired())
				{
					throw new Watcher\Exception\TimeExpired($this);
				}
			}
		}
		while ($stampQueue->count() > 0);
	}

	protected function stampQueue() : Stamp\Collection
	{
		$query = Stamp\RepositoryTable::getList([
			'filter' => [
				'=PUSH_ID' => $this->controller->getSetup()->getId(),
				'=STATUS' => Stamp\RepositoryTable::STATUS_WAIT,
			],
			'limit' => max(1, (int)Config::getOption('push_submit_limit', 500)),
		]);

		$result = $query->fetchCollection();
		$result->fillServicePrimary();

		return $result;
	}
}