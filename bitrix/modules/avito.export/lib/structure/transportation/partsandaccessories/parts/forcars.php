<?php
namespace Avito\Export\Structure\Transportation\PartsAndAccessories\Parts;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class ForCars implements Structure\Category, Structure\CategoryLevel
{
    use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return Structure\CategoryLevel::PRODUCT_TYPE;
	}

	public function name() : string
	{
		return self::getLocale('NAME');
	}

	public function children() : array
	{
		return [];
	}

	public function dictionary() : Dictionary\Dictionary
    {
		return new Dictionary\Compound(
		    (new Structure\DictionaryFactory(self::getLocalePrefix()))->make([
			    'all' => [
				    'other_tags' => 'transportation/partsandaccessories/parts/producttype/for_cars/for_cars.xml',
				    'autoCatalog' => 'transportation/partsandaccessories/parts/autocatalog.xml',
			    ],
			    'SparePartType-->SPARE_PART_TYPE_BODY' => [
				    'BodySparePartType' => 'transportation/partsandaccessories/parts/producttype/for_cars/bodyspareparttype.xml',
			    ],
			    'SparePartType-->SPARE_PART_TYPE_BATTERIES' => [
				    'transportation/partsandaccessories/parts/producttype/for_cars/batteries/capacity.xml',
				    'transportation/partsandaccessories/parts/producttype/for_cars/batteries/dcl.xml',
				    'transportation/partsandaccessories/parts/producttype/for_cars/batteries/length.xml',
				    'transportation/partsandaccessories/parts/producttype/for_cars/batteries/width.xml',
				    'transportation/partsandaccessories/parts/producttype/for_cars/batteries/height.xml',
			    ],
			    'Originality-->ORIGINALITY_ANALOG' => [ 'transportation/partsandaccessories/parts/originalvendor.xml' ],
		    ])
	    );
    }
}
