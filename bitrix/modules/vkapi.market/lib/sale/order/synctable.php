<?php

namespace VKapi\Market\Sale\Order;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
     * Класс для записей с настройками синхронизаций заказов
     * Fields: ID:int, ACTIVE:bool, ACCOUNT_ID:int, GROUP_ID:int, GROUP_NAME:str, PARAMS:array
     * 
     */
class SyncTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_sale_order_sync';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true)), new \Bitrix\Main\Entity\BooleanField('ACTIVE', array('required' => true, 'default_value' => false)), new \Bitrix\Main\Entity\IntegerField('ACCOUNT_ID', array(
            //идентификатор добавленного аккаунта, от имени которого работать
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', array(
            //идентификатор группы в вконткате, положительное целое число
            'required' => true,
        )), new \Bitrix\Main\Entity\StringField('GROUP_NAME', array(
            // название группы в вконтакте, для вывода в списке
            'required' => true,
        )), new \Bitrix\Main\Entity\BooleanField('EVENT_ENABLED', array(
            // вклчюена ли обработка событий
            'default_value' => true,
        )), new \Bitrix\Main\Entity\StringField('EVENT_SECRET', array(
            //секретный ключ передаваемый вместе с запросом
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('EVENT_CODE', array(
            // проверочный код при добавлении сервера в вк и выполнении проверки
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('GROUP_ACCESS_TOKEN', array(
            // ключ доступа сообщества для получения заказов
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('SITE_ID', array(
            // идентификтаор сайта, дял привязки заказов
            'required' => true,
        )), new \Bitrix\Main\Entity\TextField('PARAMS', array('required' => true, 'serialized' => true, 'default_value' => [])), new \Bitrix\Main\Entity\ReferenceField('ACCOUNT', '\\VKapi\\Market\\ConnectTable', array('=this.ACCOUNT_ID' => 'ref.ID'), array('join_type' => 'LEFT')), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
}
?>