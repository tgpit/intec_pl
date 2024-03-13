<?php
namespace Avito\Export\Dictionary;

use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;

class XmlTree implements Dictionary
{
	protected $file;
	protected $useParent;

	public function __construct($file, array $parameters = [])
	{
		$this->file = new File\XmlFile($file);
		$this->useParent = $parameters['parent'] ?? true;
	}

	public function useParent() : bool
	{
		return $this->useParent;
	}

	public function attributes(array $values = []) : array
	{
		try
		{
			$values = $this->input($values);
			$used = [];

			foreach ($this->searchLevel($values, $this->file->root()) as $tag)
			{
				$children = $tag->children();

				if ($children === null) { continue; }

				foreach ($this->flatChildren($children) as $child)
				{
					$used[$child->getName()] = true;
				}
			}

			$result = array_keys(array_diff_key($used, $values));
		}
		catch (Exceptions\AttributeRequired $exception)
		{
			if (array_key_exists($exception->attributeName(), $values))
			{
				throw $exception;
			}

			$result = [ $exception->attributeName() ];
		}

		return $result;
	}

	public function variants(string $attribute, array $values = []) : ?array
	{
		$result = null;
		$values = $this->input($values);
		$chain = $this->searchLevel($values, $this->file->root());

		foreach (array_reverse($chain) as $tag)
		{
			$children = $tag->children();

			if ($children === null) { continue; }

			foreach ($this->flatChildren($children) as $child)
			{
				$name = $child->getName();

				if ($name !== $attribute) { continue; }

				if ($child['variable'] !== null)
				{
					$result = [];
					break;
				}

				if ($result === null) { $result = []; }

				$result[] = $this->nodeValue($child);
			}

			if ($result !== null) { break; }
		}

		return $this->output($result);
	}

	protected function searchLevel(array $values, \SimpleXMLElement $root) : array
	{
		$children = $root->children();

		if ($children === null) { return []; }

		$matched = [];
		$empty = [];
		$found = [];
		$children = $this->flatChildren($children);

		foreach ($children as $child)
		{
			$name = $child->getName();

			if (!isset($values[$name])) { continue; }

			if ((string)$values[$name] === '')
			{
				$empty[$name] = true;
			}
			else
			{
				$found[$name] = true;
			}

			if (
				$child['variable'] !== null
				|| (string)$values[$name] === $this->nodeValue($child)
			)
			{
				$matched[$name] = $child;
			}
		}

		if (empty($matched))
		{
			if (!empty($empty))
			{
				$name = key($empty);

				throw new Exceptions\AttributeRequired($name);
			}

			if (!empty($found))
			{
				$name = key($found);

				throw new Exceptions\UnknownValue($name, $this->output($values[$name]));
			}
		}

		$left = array_diff_key($values, $matched);
		$result = [
			$root,
		];

		foreach ($matched as $child)
		{
			$childMatched = $this->searchLevel($left, $child);

			if (!empty($childMatched))
			{
				array_push($result, ...$childMatched);
			}
			else
			{
				$result[] = $child;
			}
		}

		return $result;
	}

	/**
	 * @param \SimpleXMLElement $children
	 *
	 * @return \SimpleXMLElement[]
	 */
	protected function flatChildren(\SimpleXMLElement $children) : array
	{
		$result = [];

		foreach ($children as $child)
		{
			if ($child['name'] !== null || $child['variable'] !== null)
			{
				$result[] = $child;
			}
			else
			{
				$nextChildren = $child->children();

				if ($nextChildren !== null && $nextChildren->count() > 0)
				{
					$nextChildren = $this->flatChildren($nextChildren);

					if (!empty($nextChildren))
					{
						array_push($result, ...$nextChildren);
					}
				}
				else
				{
					$result[] = $child;
				}
			}
		}

		return $result;
	}

	protected function nodeValue(\SimpleXMLElement $node) : string
	{
		return (string)($node['name'] ?? $node);
	}

	/**
	 * @template T
	 * @param T $parameter
	 *
	 * @return T
	 */
	protected function input($parameter)
	{
		if (Application::isUtfMode()) { return $parameter; }

		return Encoding::convertEncoding($parameter, LANG_CHARSET, 'UTF-8');
	}

	/**
	 * @template T
	 * @param T $parameter
	 *
	 * @return T
	 */
	protected function output($parameter)
	{
		if (Application::isUtfMode()) { return $parameter; }

		return Encoding::convertEncoding($parameter, 'UTF-8', LANG_CHARSET);
	}
}