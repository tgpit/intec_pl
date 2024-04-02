<?php
namespace Avito\Export\Structure\ForBusiness;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Category;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;

class EquipmentForBusiness implements Category, CategoryLevel
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
		return new Dictionary\Compound([
			new Dictionary\XmlTree('forbusiness/equipmentforbusiness/common.xml'),
			new Dictionary\XmlTree('forbusiness/equipmentforbusiness/country.xml'),
			new Dictionary\Fixed([
				'Condition' => new Dictionary\Listing\Condition(),
				'Availability' => new Dictionary\Listing\Availability(),
			])
		]);
	}

	public function children() : array
	{
		return $this->once('children', static function() {
			return (new Factory(self::getLocalePrefix()))->make([
				self::getLocale('INDUSTRIAL') => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/industrial.xml'),
				],
				self::getLocale('LOGISTICS_WAREHOUSE') => [
                    'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/logistics.xml'),
                ],
				self::getLocale('FOOD') => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/food.xml'),
					'oldNames' => self::getLocale('FOR_RESTAURANT'),
				],
				self::getLocale('FOR_BEAUTY_SALON') => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/for_beauty_salon.xml'),
				],
				self::getLocale('FOR_CAR_BUSINESS') => [
                    'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/for_car_business.xml'),
                ],

				self::getLocale('MINING'),
				self::getLocale('LABORATORY') => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/laboratory.xml'),
				],
				self::getLocale('MEDICAL') => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/medical.xml'),
				],
				self::getLocale('TELECOMMUNICATION'),
				'Trade' => [
					'dictionary' => new Dictionary\XmlTree('forbusiness/equipmentforbusiness/trade.xml'),
				],
				self::getLocale('OTHER'),
			]);
		});
	}
}