<?php
namespace Avito\Export\Structure;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;

class ForHomeAndGarden implements Category
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
			'AdType' => new Dictionary\Listing\AdType()
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			$factory = new Factory(self::getLocalePrefix());
			$factory->categoryLevel(CategoryLevel::CATEGORY);

			return array_merge(
				[
					new ForHomeAndGarden\FurnitureAndInterior(),
				],
				$factory->make([
					'Plants',
					'Foods',
				]),
				[
					new ForHomeAndGarden\RepairAndConstruction(),
					new ForHomeAndGarden\DishesAndProductsKitchen(),
					new ForHomeAndGarden\HomeAppliances(),
				]
			);
		});
	}
}