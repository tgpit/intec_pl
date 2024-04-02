<?php
namespace Avito\Export\Structure\Transportation\PartsAndAccessories\Parts;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure;

class ForSpecialVehicles implements Structure\Category, Structure\CategoryLevel, Structure\CategoryCompatible
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

	public function oldNames() : array
	{
		return [
			self::getLocale('NAME_OLD'),
		];
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
				    'other_tags' => 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/for_special_vehicles.xml',
			    ],
			    'Technic-->TECHNIC_BUSES' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/bus.xml' ],
			    'Technic-->TECHNIC_MOTOR_HOMES' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/motorhome.xml' ],
			    'Technic-->TECHNIC_TRUCK_CRANES' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/autocrane.xml' ],
			    'Technic-->TECHNIC_BULLDOZERS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/bulldozer.xml' ],
			    'Technic-->TECHNIC_TRUCKS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/truck_catalog.xml' ],
			    'Technic-->TECHNIC_LOADERS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/loader.xml' ],
			    'Technic-->TECHNIC_TRAILERS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/trailer_catalog.xml' ],
			    'Technic-->TECHNIC_AGRICULTURAL_MACHINERY' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/agricultural_machinery.xml' ],
			    'Technic-->TECHNIC_CONSTRUCTION_EQUIPMENT' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/construction_machinery.xml' ],
			    'Technic-->TECHNIC_TRACTORS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/cab_catalog.xml' ],
			    'Technic-->TECHNIC_EXCAVATORS' => [ 'transportation/partsandaccessories/parts/producttype/for_special_vehicles/excavators.xml' ],
			    'Originality-->ORIGINALITY_ANALOG' => [ 'transportation/partsandaccessories/parts/originalvendor.xml' ],
		    ])
	    );
    }
}
