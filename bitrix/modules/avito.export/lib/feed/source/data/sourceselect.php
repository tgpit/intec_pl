<?php
namespace Avito\Export\Feed\Source\Data;

class SourceSelect
{
	protected $map = [];

	public function sources() : array
	{
		return array_keys($this->map);
	}

	public function fields(string $type) : array
	{
		return $this->map[$type] ?? [];
	}

	public function add(string $type, string $field) : void
	{
		if (!isset($this->map[$type]))
		{
			$this->map[$type] = [
				$field,
			];
		}
		else if (!in_array($field, $this->map[$type], true))
		{
			$this->map[$type][] = $field;
		}
	}
}