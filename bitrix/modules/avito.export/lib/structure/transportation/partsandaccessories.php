<?php
namespace Avito\Export\Structure\Transportation;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Factory;
use Avito\Export\Structure\Category;

class PartsAndAccessories implements Category, CategoryLevel
{
    use Concerns\HasOnce;
    use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::CATEGORY;
	}

	public function name() : string
    {
        return self::getLocale('NAME');
    }

    public function dictionary() : Dictionary\Dictionary
    {
	    return new Dictionary\Compound([
            new Dictionary\Fixed([
                'AdType' => new Dictionary\Listing\AdType(),
                'Condition' => new Dictionary\Listing\Condition(),
            ]),
        ]);
    }

    public function children() : array
    {
        return $this->once('children', static function() {
            self::includeLocale();

	        return (new Factory(self::getLocalePrefix()))->make([
				new PartsAndAccessories\Parts([
					'name' => self::getLocale('PARTS'),
				]),
				new PartsAndAccessories\OilsAndChemicals(),
				'Accessories' => [
					'dictionary' => new Dictionary\XmlTree('transportation/partsandaccessories/accessories.xml'),
				],
				'GPS navigators',
				'Auto Makeup & Auto Chemicals',
				'Audio and video equipment' => [
					'dictionary' => new Dictionary\XmlTree('transportation/partsandaccessories/audio_and_video_equipment.xml'),
				],
				'Trunks and towbars',
				'Tools',
				'Trailers',
				'Antitheft devices' => [
					'dictionary' =>
						new Dictionary\Fixed([
							'DeviceType' => new PartsAndAccessories\Properties\DeviceType()
						]),
		        ],
				'Tuning',
				new PartsAndAccessories\TiresRimsAndWheels(),
				'Equipment',
	        ]);
		});
    }
}