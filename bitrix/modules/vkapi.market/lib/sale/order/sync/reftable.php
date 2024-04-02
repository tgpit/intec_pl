<?php

namespace VKapi\Market\Sale\Order\Sync;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
     * Класс для работы с таблицей соответствий заказов в вк и локальных
     * для последующей их синхронизации, обнволения полей
     * Fields: ID:int, ORDER_ID:int, VKORDER_ID:int, VKUSER_ID:int, GROUP_ID:int, SYNC_ID:int
     * 
     */
class RefTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_sale_order_sync_ref';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true)), new \Bitrix\Main\Entity\IntegerField('ORDER_ID', array()), new \Bitrix\Main\Entity\StringField('VKORDER_ID', array(
            // заказ в вк
            'required' => true,
        )), new \Bitrix\Main\Entity\StringField('VKUSER_ID', array(
            // идентификтаор польвзаоетля которому принадлежит заказа
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', array(
            //идентификатор группы в вк
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('SYNC_ID', array(
            //идентификатор синхронизации
            'required' => true,
        )), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
}
?>