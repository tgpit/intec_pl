<?php


namespace Pecom\Ecomm\ORM;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\IntegerField;
use Bitrix\Main\ORM\Fields\TextField;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Pecom\Ecomm\ORM\Base\DataManager;

Loc::loadMessages(__FILE__);

class BasketPropsValueTable extends DataManager
{
    public static function getTableName()
    {
        return 'pecom_ecomm_basket_props_value';
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
                'FUSER_ID',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_FUSER_ID_FIELD')
                ]
            ),
            new IntegerField(
                'BASKET_ID',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_BASKET_ID_FIELD')
                ]
            ),
            new IntegerField(
                'PROPS_CODE',
                [
                    'title' => Loc::getMessage('PROPS_VALUE_ENTITY_PROPS_CODE_FIELD')
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
                BasketPropsTable::class,
                ['=this.PROPS_CODE' => 'ref.CODE'],
                ['join_type' => 'LEFT']
            )
        ];
    }
}