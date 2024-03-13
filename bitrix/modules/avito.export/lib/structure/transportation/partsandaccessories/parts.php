<?php
namespace Avito\Export\Structure\Transportation\PartsAndAccessories;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\CategoryLevel;
use Avito\Export\Structure\Custom;

class Parts extends Custom
{
    use Concerns\HasOnce;
    use Concerns\HasLocale;

	public function categoryLevel() : ?string
	{
		return CategoryLevel::GOODS_TYPE;
	}

	public function dictionary() : Dictionary\Dictionary
    {
	    /** @noinspection SpellCheckingInspection */
	    return new Dictionary\Compound(array_merge(
			[
				new Dictionary\Fixed([
					'ProductType' => [
						self::getLocale('PRODUCT_TYPE_FOR_AUTO'),
						self::getLocale('PRODUCT_TYPE_FOR_MOTORCYCLES'),
						self::getLocale('PRODUCT_TYPE_FOR_SPECIAL_VEHICLES'),
						self::getLocale('PRODUCT_TYPE_FOR_WATER_VEHICLES'),
					],
				]),
		    ],
		    $this->groupDictionaryProductTypeForCars(),
		    $this->groupDictionaryProductTypeForMotorcycles(),
		    $this->groupDictionaryProductTypeForSpecialVehicles(),
		    [
			    new Dictionary\XmlTree('transportation/partsandaccessories/parts/partsbrands.xml'),
		    ]
	    ));
    }

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryProductTypeForCars() : array
	{
		$availability = new Dictionary\Fixed(['Availability' => new Dictionary\Listing\Availability()]);
		$map = [
			'all' => [
				'other_tags' => 'transportation/partsandaccessories/parts/producttype/for_cars/for_cars.xml',
				'autoCatalog' => 'transportation/partsandaccessories/parts/autocatalog.xml',
			],
			'SparePartType-->SPARE_PART_TYPE_BODY' => [
				'BodySparePartType' => 'transportation/partsandaccessories/parts/producttype/for_cars/bodyspareparttype.xml',
				$availability,
			],
			'SparePartType-->SPARE_PART_TYPE_AUTOLIGHT' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_BATTERIES' => [
				'transportation/partsandaccessories/parts/producttype/for_cars/batteries/capacity.xml',
				'transportation/partsandaccessories/parts/producttype/for_cars/batteries/dcl.xml',
				'transportation/partsandaccessories/parts/producttype/for_cars/batteries/length.xml',
				'transportation/partsandaccessories/parts/producttype/for_cars/batteries/width.xml',
				'transportation/partsandaccessories/parts/producttype/for_cars/batteries/height.xml',
				$availability,
			],
			'SparePartType-->SPARE_PART_TYPE_ENGINE' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_MAINTENANCE_PARTS' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_SUSPENSION' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_COOLING_SYSTEM' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_STEERING_SYSTEM' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_GLASSES' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_FUEL_AND_EXHAUST_SYSTEM' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_BRAKING_SYSTEM' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_TRANSMISSION_AND_DRIVE_SYSTEM' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_ELECTRICAL_EQUIPMENT' => [ $availability ],
			'SparePartType-->SPARE_PART_TYPE_INTERIOR' => [ $availability ],
			'Originality-->ORIGINALITY_ANALOG' => $this->dictionaryOriginalVendor(),
		];

		return $this->constructDictionaries($map, 'PRODUCT_TYPE_FOR_AUTO');
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryProductTypeForMotorcycles() : array
	{
		return $this->constructDictionaries([
			'all' => [
				'other_tags' => 'transportation/partsandaccessories/parts/producttype/for_motorcycles/for_motorcycles.xml',
			],
		],'PRODUCT_TYPE_FOR_MOTORCYCLES');
	}

	/** @return Dictionary\Dictionary[] */
	protected function groupDictionaryProductTypeForSpecialVehicles() : array
	{
		$map = [
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
			'Originality-->ORIGINALITY_ANALOG' => $this->dictionaryOriginalVendor(),
		];

		return $this->constructDictionaries($map, 'PRODUCT_TYPE_FOR_SPECIAL_VEHICLES');
	}

	/** @return Dictionary\Dictionary[] */
	protected function dictionaryOriginalVendor() : array
	{
		return  [
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/parts/partsbrands.xml'), [
				'rename' => [ 'Brand' => 'OriginalVendor' ]
			]),
		];
	}
}
