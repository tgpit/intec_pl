<?php
namespace Avito\Export\Structure\ForHomeAndGarden;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class RepairAndConstruction implements Category, CategoryLevel
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\Fixed([
			'AdType' => new Dictionary\Listing\AdType(),
			'Condition' => new Dictionary\Listing\Condition(),
		]);
	}

	public function categoryLevel() : ?string
	{
		return CategoryLevel::CATEGORY;
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			/** @noinspection SpellCheckingInspection */
			return (new Factory(self::getLocalePrefix()))->make([
				new RepairAndConstruction\PrefabricatedStructuresAndLogCabins([
					'name' => self::getLocale('PREFABRICATED_STRUCTURES_AND_LOG_CABINS'),
				]),
				new RepairAndConstruction\BuildingMaterials([
					'name' => self::getLocale('BUILDING_MATERIALS'),
				]),
				new RepairAndConstruction\PlumbingWaterAndSauna([
					'name' => self::getLocale('PLUMBING_WATER_AND_SAUNA'),
					'oldNames' => self::getLocale('PLUMBING_WATER_AND_SAUNA_OLD_NAMES')
				]),
				new RepairAndConstruction\Tools([
					'name' => self::getLocale('TOOLS'),
				]),
				'Windows And Balconies',
				'Ceilings',
				'Doors',
				'Fireplaces And Heaters' => [
					'dictionary' =>	new Dictionary\XmlCascade('forhomeandgarden/repairandconstruction/fireplaces_and_heaters.xml'),
				],
				'Garden Appliances' => [
					'dictionary' =>	new Dictionary\Decorator(
						new Dictionary\XmlCascade('forhomeandgarden/repairandconstruction/brendy_instrumentov.xml'),
						[
							'rename' => [
								'brand' => 'Brand',
							],
						]
					),
				],
			]);
		});
	}
}
