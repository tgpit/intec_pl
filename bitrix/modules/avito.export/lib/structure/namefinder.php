<?php
namespace Avito\Export\Structure;

use Bitrix\Main;
use Avito\Export\Concerns;

class NameFinder
{
	use Concerns\HasLocale;

	/** @return Category[] */
	public function tags(Category $root, array $values) : array
	{
		if (empty($values)) { return []; }

		$result = [];
		$parent = $root;

		while ($children = $parent->children())
		{
			$found = null;

			foreach ($children as $category)
			{
				if (!($category instanceof CategoryLevel))  { continue; }

				$categoryLevel = $category->categoryLevel();

				if (
					isset($categoryLevel, $values[$categoryLevel])
					&& $values[$categoryLevel] === $category->name()
				)
				{
					$found = $category;
					unset($values[$categoryLevel]);
					break;
				}
			}

			if ($found === null) { break; }

			$result[] = $found;
			$parent = $found;
		}

		return $result;
	}

	/** @return Category[] */
	public function path(Category $root, string $path) : array
	{
		$nameChain = explode(' / ', $path);
		$level = $root;
		$result = [];

		foreach ($nameChain as $name)
		{
			$matched = null;

			foreach ($level->children() as $child)
			{
				if ($this->matchCategoryName($child, $name))
				{
					$matched = $child;
					break;
				}
			}

			if ($matched === null)
			{
				throw new Main\ArgumentException(self::getLocale('NOT_FOUND', [
					'#NAME#' => $name,
				]));
			}

			$level = $matched;
			$result[] = $matched;
		}

		return $result;
	}

	protected function matchCategoryName(Category $category, string $name) : bool
	{
		$result = false;

		if ($this->compareName($category->name(), $name))
		{
			$result = true;
		}
		else if ($category instanceof CategoryCompatible)
		{
			foreach ($category->oldNames() as $oldName)
			{
				if ($this->compareName($oldName, $name))
				{
					$result = true;
					break;
				}
			}
		}

		return $result;
	}

	protected function compareName(string $first, string $second) : bool
	{
		return mb_strtolower(trim($first)) === mb_strtolower(trim($second));
	}
}