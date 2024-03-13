<?php


namespace Pecom\Ecomm\ORM;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Pecom\Ecomm\ORM\Base\DataManager;

Loc::loadMessages(__FILE__);

class ShipmentPropsValueTable extends DataManager
{
    public static function getTableName()
    {
        return 'pecom_ecomm_shipment_props_value';
    }

    public static function getMap()
    {
        return [
            new IntegerField(
                'ID',
                [
                    'primary' => true,
                    'autocomplete' => true,
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_ID_FIELD')
                ]
            ),
            new IntegerField(
                'ORDER_ID',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_ORDER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'SHIPMENT_ID',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_SHIPMENT_ID_FIELD')
                ]
            ),
            new TextField(
                'PROPS_CODE',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_PROPS_ID_FIELD')
                ]
            ),
            new TextField(
                'VALUE',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_VALUE_FIELD')
                ]
            ),
            new Reference(
                'PROPS',
                ShipmentPropsTable::class,
                ['=this.PROPS_CODE' => 'ref.CODE'],
                ['join_type' => 'LEFT']
            )
        ];
    }
}