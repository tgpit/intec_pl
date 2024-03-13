<?php
namespace Avito\Export\Feed\Engine\Data;

use Avito\Export\Feed\Tag;

class TagValues
{
	protected $values = [];
	protected $mixedTags = [
		Tag\Param::NAME,
		Tag\Characteristic::NAME,
	];

	public function remove(string $name) : void
	{
		if (isset($this->values[$name]) || array_key_exists($name, $this->values))
		{
			unset($this->values[$name]);
		}

		foreach ($this->mixedTags as $mixedTag)
		{
			if (!isset($this->values[$mixedTag]) || !is_array($this->values[$mixedTag])) { continue; }

			if (isset($this->values[$mixedTag][$name]) || array_key_exists($name, $this->values[$mixedTag]))
			{
				unset($this->values[$mixedTag][$name]);
			}
		}
	}

	public function get(string $name)
	{
		return (
			$this->values[$name]
			?? $this->values[Tag\Characteristic::NAME][$name]
			?? $this->values[Tag\Param::NAME][$name]
			?? null
		);
	}

	public function set(string $name, $value) : void
	{
		if (isset($this->values[$name]) || array_key_exists($name, $this->values))
		{
			$this->values[$name] = $value;
			return;
		}

		$foundMixed = false;

		foreach ($this->mixedTags as $mixedTag)
		{
			if (!isset($this->values[$mixedTag]) || !is_array($this->values[$mixedTag])) { continue; }

			if (isset($this->values[$mixedTag][$name]) || array_key_exists($name, $this->values[$mixedTag]))
			{
				$foundMixed = true;
				$this->values[$mixedTag][$name] = $value;
				break;
			}
		}

		if (!$foundMixed)
		{
			$this->values[$name] = $value;
		}
	}

	public function getRaw(string $name)
	{
		return $this->values[$name] ?? null;
	}

	public function setRaw(string $name, $value) : void
	{
		$this->values[$name] = $value;
	}

	public function asArray() : array
	{
		return $this->values;
	}
}