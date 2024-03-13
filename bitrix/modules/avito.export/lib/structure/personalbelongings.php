<?php
namespace Avito\Export\Structure;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;

class PersonalBelongings implements Category
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
            return [
                new PersonalBelongings\ClothingShoesAccessories(),
                new PersonalBelongings\ChildrenClothingAndShoes(),
                new PersonalBelongings\GoodsForChildrenAndToys(),
                new PersonalBelongings\WatchesAndJewelry(),
	            new PersonalBelongings\BeautyAndHealth(),
            ];
        });
    }
}