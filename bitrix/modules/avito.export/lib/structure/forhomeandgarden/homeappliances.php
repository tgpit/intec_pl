<?php
namespace Avito\Export\Structure\ForHomeAndGarden;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class HomeAppliances implements Category, CategoryLevel
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function categoryLevel() : ?string
	{
		return CategoryLevel::CATEGORY;
	}

	public function dictionary() : Dictionary\Dictionary
	{
		return new Dictionary\NoValue();
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			return (new Factory(self::getLocalePrefix()))->make([
				'Vacuum Cleaners',
				'Washing Machines',
				'Irons',
				'Sewing Machines',
				'Razors And Trimmers',
				'Hair Clippers',
				'Hair Dryers And Styling Appliances',
				'Epilators',
				'Extractors',
				'Small Kitchen Appliances',
				'Microwave Ovens',
				'Stoves',
				'Dishwashers',
				new HomeAppliances\RefrigeratorsAndFreezers(),
				'Fans',
				'Air conditioners',
				'Heaters',
				'Air Purifiers',
				'Thermometers And Weather Stations',
				'Other'
			]);
		});
	}
}