<?php
namespace Avito\Export\Structure\ForHomeAndGarden\FurnitureAndInterior;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;

class KitchenSets implements Category, CategoryLevel
{
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/kitchensets.xml');
	}

	public function children() : array
	{
		return [];
	}
}