<?php
namespace Avito\Export\Structure\ForHomeAndGarden;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class FurnitureAndInterior implements Category, CategoryLevel
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
			'Availability' => new Dictionary\Listing\Availability(),
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			self::includeLocale();

			return (new Factory(self::getLocalePrefix()))->make([
				new FurnitureAndInterior\BedsSofasAndChairs(),
				new FurnitureAndInterior\CabinetsChestsOfDrawersAndShelvingUnits(),
				new FurnitureAndInterior\TablesAndChairs(),
				'Textiles And Carpets' => [
					'dictionary' => new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/textiles_and_carpets.xml'),
				],
				new FurnitureAndInterior\KitchenSets(),
				'Interior Decorations Art',
				'Lighting',
				'Computer Desks And Chairs' => [
					'dictionary' => new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/computer_desks_and_chairs.xml'),
				],
				'Stands And Tables' => [
					'dictionary' => new Dictionary\XmlTree('forhomeandgarden/furnitureandinterior/stands_and_tables.xml'),
				],
			]);
		});
	}
}