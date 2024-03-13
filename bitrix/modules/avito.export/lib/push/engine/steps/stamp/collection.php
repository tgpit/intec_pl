<?php
namespace Avito\Export\Push\Engine\Steps\Stamp;

class Collection extends EO_Repository_Collection
{
	/** @return array<string, Collection> */
	public function groupByType() : array
	{
		$result = [];

		foreach ($this->getAll() as $model)
		{
			$type = $model->getType();

			if (!isset($result[$type]))
			{
				$result[$type] = new static();
			}

			$result[$type]->add($model);
		}

		return $result;
	}
	/** @return Collection[] */
	public function chunk(int $size) : array
	{
		$result = [];

		foreach (array_chunk($this->getAll(), $size) as $chunk)
		{
			$collection = new static();

			foreach ($chunk as $model)
			{
				$collection->add($model);
			}

			$result[] = $collection;
		}

		return $result;
	}

	public function increaseRepeat() : void
	{
		foreach ($this->getAll() as $model)
		{
			$model->increaseRepeat();
		}
	}
}