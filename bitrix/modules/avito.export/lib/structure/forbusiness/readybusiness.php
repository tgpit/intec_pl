<?php
namespace Avito\Export\Structure\ForBusiness;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class ReadyBusiness implements Category, CategoryLevel
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
		return new Dictionary\Fixed([ 'DealGoal' => new Readybusiness\Properties\DealGoal() ]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			return (new Factory(self::getLocalePrefix()))->make([
				self::getLocale('ONLINE_SHOPPING_AND_IT') => [
					'oldNames' => self::getLocale('ONLINE_SHOPPING_AND_IT_OLD_NAMES')
				],
				self::getLocale('CATERING') => [
					'oldNames' => self::getLocale('CATERING_OLD_NAMES')
				],
				'Production',
				'Entertainment',
				self::getLocale('AGRICULTURE') => [
					'oldNames' => self::getLocale('AGRICULTURE_OLD_NAMES')
				],
				'Construction',
				'Services',
				'Stores and Ordering Outlets',
				self::getLocale('AUTOBUSINESS') => [
					'oldNames' => self::getLocale('AUTOBUSINESS_OLD_NAMES')
				],
				'Beauty and Care',
				self::getLocale('DENTISTRY_AND_MEDICAL_BUSINESSES') => [
					'oldNames' => self::getLocale('DENTISTRY_AND_MEDICAL_BUSINESSES_OLD_NAMES')
				],
				self::getLocale('TOURISM') => [
					'oldNames' => self::getLocale('TOURISM_OLD_NAMES')
				],
				'Other',
			]);
		});
	}
}