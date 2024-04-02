<?php

namespace VKapi\Market\Sale\Order;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
     * ����� ��� ������� � ����������� ������������� �������
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
            //������������� ������������ ��������, �� ����� �������� ��������
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', array(
            //������������� ������ � ���������, ������������� ����� �����
            'required' => true,
        )), new \Bitrix\Main\Entity\StringField('GROUP_NAME', array(
            // �������� ������ � ���������, ��� ������ � ������
            'required' => true,
        )), new \Bitrix\Main\Entity\BooleanField('EVENT_ENABLED', array(
            // �������� �� ��������� �������
            'default_value' => true,
        )), new \Bitrix\Main\Entity\StringField('EVENT_SECRET', array(
            //��������� ���� ������������ ������ � ��������
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('EVENT_CODE', array(
            // ����������� ��� ��� ���������� ������� � �� � ���������� ��������
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('GROUP_ACCESS_TOKEN', array(
            // ���� ������� ���������� ��� ��������� �������
            'default_value' => '',
        )), new \Bitrix\Main\Entity\StringField('SITE_ID', array(
            // ������������� �����, ��� �������� �������
            'required' => true,
        )), new \Bitrix\Main\Entity\TextField('PARAMS', array('required' => true, 'serialized' => true, 'default_value' => [])), new \Bitrix\Main\Entity\ReferenceField('ACCOUNT', '\\VKapi\\Market\\ConnectTable', array('=this.ACCOUNT_ID' => 'ref.ID'), array('join_type' => 'LEFT')), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
}
?>