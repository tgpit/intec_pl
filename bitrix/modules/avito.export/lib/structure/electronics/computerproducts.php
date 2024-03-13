<?php
namespace Avito\Export\Structure\Electronics;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;
use Avito\Export\Structure\Category;

class ComputerProducts implements Category, CategoryLevel
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
		return new Dictionary\Fixed([
			'Condition' => new Dictionary\Listing\Condition(),
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			$customFactory = new Factory(self::getLocalePrefix());

			return $customFactory->make([
				'Acoustics',
				'Webcams',
				'Joysticks and rudders',
				'Keyboards and mice',
				'Monitors',
				'Portable hard drives',
				'TV tuners',
				'Network Equipment',
				'Flash drives and memory cards',
				'Accessories',
				'CD,DVD and Blu-ray drives',
				'Power Supplies',
				'Video cards' => [
					'dictionary' => new Dictionary\XmlCascade('electronics/gpus.xml'),
				],
				'Hard drives',
				'Sound cards',
				'Controllers',
				'Hulls',
				'Motherboards',
				'Main memory',
				'Processors',
				'Cooling systems'
			]);
		});
	}
}