<?php
namespace Avito\Export\Dictionary;

use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;

class XmlCascade implements Dictionary
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
			$tag = $this->findTag($values);

			if ($tag === null) { return []; }

			$children = $tag->children();

			if ($children === null || count($children) === 0) { return []; }

			$first = $children[0];

			$result = [ $first->getName() ];
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
		$values = $this->input($values);
		$tag = $this->findTag($values, $attribute);

		if ($tag === null) { return null; }

		$result = [];

		foreach ($tag->children() as $child)
		{
			$name = $child->getName();

			if ($name !== $attribute)
			{
				throw new Exceptions\AttributeRequired($name);
			}

			$result[] = (string)$child['name'];
		}

		return $this->output($result);
	}

	protected function findTag(array $values, string $until = null) : ?\SimpleXMLElement
	{
		$left = $values;
		$tag = $this->file->root();
		$result = null;

		do
		{
			if ($until === null && empty($left))
			{
				$result = $tag;
				break;
			}

			$level = $tag->children();

			if (count($level) === 0)
			{
				$result = null;
				break;
			}

			$matched = null;
			$name = null;

			foreach ($level as $child)
			{
				$name = $child->getName();

				if ($until !== null && $until === $name)
				{
					$result = $tag;
					break;
				}

				if (!isset($left[$name]) || (string)$left[$name] === '')
				{
					throw new Exceptions\AttributeRequired($name);
				}

				if ((string)$left[$name] === (string)$child['name'])
				{
					$matched = $child;
					break;
				}
			}

			if ($result !== null) { break; }

			if ($matched === null)
			{
				throw new Exceptions\UnknownValue($name, $this->output($left[$name]));
			}

			$tag = $matched;
			unset($left[$name]);
		}
		while (true);

		return $result;
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