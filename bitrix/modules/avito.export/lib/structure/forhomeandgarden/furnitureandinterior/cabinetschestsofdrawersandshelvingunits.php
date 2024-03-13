<?php
namespace Avito\Export\Structure\ForHomeAndGarden\FurnitureAndInterior;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class CabinetsChestsOfDrawersAndShelvingUnits implements Category, CategoryLevel
{
	use Concerns\HasLocale;
	use Concerns\HasOnce;

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
		return new Dictionary\NoValue();
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			$factory = new Factory(self::getLocalePrefix());
			$factory->categoryLevel(CategoryLevel::GOODS_SUB_TYPE);

			return $factory->make([
				'Cabinets' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/cabinets.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Chests' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/chests.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Shelving And Bookcase' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/shelving_and_bookcase.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Shelves' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/shelves.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Hallways And Shoe Racks' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/hallways_and_shoe_racks.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Wardrobe Systems And Hangers' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/cabinetschestsofdrawersandshelvingunits/wardrobe_systems_and_hangers.xml'),
						new Dictionary\Fixed(['Color' => new Properties\Color()]),
					])
				],
				'Sets And Kits',
				'Components' => [
					'dictionary' => new Dictionary\Fixed([
						'ComponentsType' => new Properties\ComponentsType(),
					]),
				],
			]);
		});
	}
}