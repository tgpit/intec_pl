<?php /** @noinspection PhpDeprecationInspection */
namespace Avito\Export\Structure\Transportation\PartsAndAccessories;

use Avito\Export\Concerns;
use Avito\Export\Dictionary;
use Avito\Export\Structure\Custom;

/** @deprecated no actaul */

class TypeId extends Custom
{
    use Concerns\HasOnce;
    use Concerns\HasLocale;

    public function dictionary() : Dictionary\Dictionary
    {
	    return new Dictionary\Compound(array_merge([
			    new Dictionary\XmlTree('transportation/partsandaccessories/typeid.xml'),
		    ],
		    $this->dictionaryProductTypeAttributesParts(),
			$this->dictionaryProductTypeAttributesTires(),
		    $this->dictionaryTiresBrand(),
		    $this->dictionaryTiresModels()
	    ));
    }

	/** @return Dictionary\Dictionary[] */
	protected function dictionaryTiresBrand() : array
	{
		return [
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/tiresbrands.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/tyres_st_brands.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE_FOR_TRUCK_AND_SPECIAL_VEHICALS'),
				],
			])
		];
	}

	/** @return Dictionary\Dictionary[] */
	protected function dictionaryTiresModels() : array
	{
		return [
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/tires/models.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/tiresmodels.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE_FOR_TRUCK_AND_SPECIAL_VEHICALS'),
				],
			])
		];
	}

	/** @return Dictionary\Dictionary[] */
	protected function dictionaryProductTypeAttributesParts() : array
	{
		return [
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/parts/producttype/for_cars_type_id.xml'), [
				'wait' => [
					'TypeId' => [
						'11-618',   // � ��������
						'19-2855',  // � ���������� �� ��������
						'11-619',   // � ������������
						'16-827',   // � ��������� / ���� ���������, �������, ������
						'16-828',   // � ��������� / ��������� �������
						'16-829',   // � ��������� / ����������, ��������
						'16-830',   // � ��������� / ��������� � �����
						'16-831',   // � ��������� / ������� ���������, �����, ���������
						'16-832',   // � ��������� / ��������� ������
						'16-833',   // � ��������� / ��������, �������
						'16-834',   // � ��������� / ����������
						'16-835',   // � ��������� / ��������� ���������
						'16-836',   // � ��������� / �������� �����, ������� ������
						'16-837',   // � ��������� / �������� ����������
						'16-838',   // � ��������� / ������, ������, ������
						'16-839',   // � ��������� / ��������� �����, ����������
						'16-840',   // � ��������� / ��������� � ������������
						'16-841',   // � ��������� / �����, ����, �������� ���
						'16-842',   // � ��������� / �������, �����������
						'16-843',   // � ��������� / ���������������� � ����������
						'11-621',   // � �������� ��� ��
						'16-805',   // � ����� / �����, ���������
						'16-806',   // � ����� / �������
						'16-807',   // � ����� / ����������
						'16-808',   // � ����� / �����
						'16-809',   // � ����� / ��������
						'16-810',   // � ����� / �����
						'16-811',   // � ����� / ������
						'16-812',   // � ����� / �������
						'16-813',   // � ����� / ������
						'16-814',   // � ����� / �����
						'16-815',   // � ����� / ���������
						'16-816',   // � ����� / ������
						'16-817',   // � ����� / �����
						'16-818',   // � ����� / ������, ����� ���������
						'16-819',   // � ����� / ����� �� ������
						'16-820',   // � ����� / ����� �������
						'16-821',   // � ����� / ����� ���������
						'16-822',   // � ����� / ��������, ��������
						'16-823',   // � ����� / ������
						'16-824',   // � ����� / ����
						'16-825',   // � ����� / ������� ���������
						'16-826',   // � ����� / ������ ������
						'11-623',   // � ��������
						'11-624',   // � ������� ����������
						'11-625',   // � �����
						'16-521',   // � ������� ����������
						'11-626',   // � ������
						'11-627',   // � ��������� � ��������� �������
						'11-628',   // � ��������� �������
						'11-629',   // � ����������� � ������
						'11-630',   // � �������������������
					],
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/parts/producttype/for_motorcycles.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_FOR_MOTORCYCLES'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/parts/producttype/for_special_vehicles.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_FOR_SPECIAL_VEHICLES'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/parts/producttype/for_water_vehicles.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_FOR_WATER_VEHICLES'),
				],
			]),
		];
	}

	/** @return Dictionary\Dictionary[] */
	protected function dictionaryProductTypeAttributesTires() : array
	{
		return [
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/tires/tires.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/tires_for_trucks_and_special_equipment.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_TIRE_FOR_TRUCK_AND_SPECIAL_VEHICALS'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/moto_tires.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_MOTO_TIRE'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/rims.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_RIMS'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/wheels.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_WHEELS'),
				],
			]),
			new Dictionary\Decorator(new Dictionary\XmlTree('transportation/partsandaccessories/tiresrimsandwheels/producttype/caps.xml'), [
				'wait' => [
					'TypeId' => self::getLocale('TYPE_ID_CAPS'),
				],
			])
		];
	}
}
