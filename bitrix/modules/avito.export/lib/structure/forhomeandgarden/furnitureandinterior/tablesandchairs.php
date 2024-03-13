<?php
namespace Avito\Export\Structure\ForHomeAndGarden\FurnitureAndInterior;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class TablesAndChairs implements Category, CategoryLevel
{
	use Concerns\HasLocale;
	use Concerns\HasOnce;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\NoValue();
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			$factory = new Factory(self::getLocalePrefix());
			$factory->categoryLevel(CategoryLevel::GOODS_SUB_TYPE);

			return $factory->make([
				'Components',
				'Lunch Group',
				'Benches' => [
					'dictionary' => new Dictionary\Fixed(['Model' => []])
				],
				'Tables' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/tablesandchairs/tables.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Chairs' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/tablesandchairs/chairs.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Stools' => [
					'dictionary' => new Dictionary\Fixed(['Model' => []])
				],
			]);
		});
	}
}
