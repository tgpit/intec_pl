<?php
namespace Avito\Export\Structure\HobbiesAndRecreation;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class SportAndRecreation implements Category, CategoryLevel
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
		return new Dictionary\Fixed([ 'Condition' => new Dictionary\Listing\Condition() ]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			return (new Factory(self::getLocalePrefix()))->make([
				'Billiards and Bowling',
				'Diving and watersports',
				'Martial Arts',
				'Winter Sports',
				'Ball Games',
				'Board Games',
				'Paintball and Strikeball',
				'Roller skating and skateboarding',
				'Tennis, badminton, ping pong',
				'Tourism',
				'Fitness and Fitness Equipment',
				'Sports nutrition',
				'Other',
			]);
		});
	}
}