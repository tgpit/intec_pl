<?php
namespace Avito\Export\Structure\ForHomeAndGarden\RepairAndConstruction;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class BuildingMaterials implements Category, CategoryLevel
{
	use Concerns\HasLocale;

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
		self::includeLocale();

		$factory = new Factory(self::getLocalePrefix());
		$factory->categoryLevel(CategoryLevel::GOODS_SUB_TYPE);

		return $factory->make([
			'Isolation',
			'Fasteners',
			'Roof and gutter',
			'Varnishes and paints',
			'Rolled metal',
			'Finishing' => [ 'forhomeandgarden/buildingmaterials/finishing.xml' ],
			new BuildingMaterials\Lumber(),
			'Construction mixtures',
			'Construction of walls',
			'Electrics',
			'Other',
			'Sheet materials',
			'Ladders and accessories' => [
				'dictionary' => new Dictionary\XmlTree('forhomeandgarden/buildingmaterials/laddersandaccessories.xml')
			],
			'Gates and fences' => [
				'dictionary' => new Dictionary\XmlTree('forhomeandgarden/buildingmaterials/gatesandfences.xml')
			],
			'Bulk materials' => [
				'dictionary' => new Dictionary\XmlTree('forhomeandgarden/buildingmaterials/bulkmaterials.xml')
			],
			new BuildingMaterials\Ironmongery(),
			new BuildingMaterials\Piles(),
		]);
	}
}