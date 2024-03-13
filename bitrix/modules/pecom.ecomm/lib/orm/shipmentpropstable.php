<?php


namespace Pecom\Ecomm\ORM;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Pecom\Ecomm\ORM\Base\DataManager;

Loc::loadMessages(__FILE__);

class ShipmentPropsTable extends DataManager
{
    public static function getTableName()
    {
        return 'pecom_ecomm_shipment_props';
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
            new TextField(
                'CODE',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_CODE_FIELD')
                ]
            ),
            new TextField(
                'NAME',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_NAME_FIELD')
                ]
            ),
        ];
    }
}