<?php
namespace Avito\Export\Structure;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;

class Animals implements Category
{
	use Concerns\HasOnce;
	use Concerns\HasLocale;

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
			$factory->categoryLevel(CategoryLevel::CATEGORY);

			return $factory->make([
				'Pet products' => [
					'dictionary' => new Dictionary\Fixed([
						'Condition' => new Dictionary\Listing\Condition(),
					]),
				],
				'Cats' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('animals/cats.xml'),
						new Dictionary\Fixed([
							'AdType' => new Animals\Listing\AdType(),
						]),
					]),
				],
				'Dogs' => [
					'dictionary' => new Dictionary\Compound([
						new Dictionary\XmlTree('animals/dogs.xml'),
						new Dictionary\Fixed([
							'AdType' => new Animals\Listing\AdType(),
						]),
					]),
				],
				'Birds',
				'Aquarium',
				'Other animals' => [
					'categoryLevel' => CategoryLevel::CATEGORY,
					'children' => (new Factory(self::getLocalePrefix()))->make([
						'Amphibians',
						'Rodents',
						'Rabbits',
						'Horses',
						'Agricultural animals',
						'Ferrets',
						'Other',
					]),
				]
			]);
		});
	}
}