<?php

namespace VKapi\Market\Property;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
/**
 * ����� ��� ������ � �������� ��������� �������
 * 
 * ����
 * + ID int(11) NOT NULL AUTO_INCREMENT,
 * + GROUP_ID int(11) NOT NULL,
 * + PROPERTY_ID int(11) NOT NULL,
 * + ENUM_ID int(11) NOT NULL,
 * + VK_VARIANT_ID int(11) NOT NULL,
 * �������
 * + PRIMARY KEY (`ID`),
 * + KEY `ix_gid_pid_eid` (`GROUP_ID`, `PROPERTY_ID`, `ENUM_ID`) USING BTREE ,
 * @package VKapi\Market\Property
 */
class VariantTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_property_variant';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', array(
            // ������������� ������ � ���������, ����� ������������� �����
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('PROPERTY_ID', array(
            //id ��������
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('ENUM_ID', array(
            //id �������� ��������
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('VK_VARIANT_ID', array(
            // id ���������������� � ��
            'required' => true,
        )), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
    /**
 * �������� ���� ������� ����������� � ������,
 * ������������ ��� ������� ������
 * 
 * @param $groupId
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public static function deleteAllByGroupId($groupId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => abs(intval($groupId))])));
    }
    /**
 * �������� �������� ������� ����������� � ������
 * @param $groupId
 * @param $arPropertyId
 * @return \Bitrix\Main\DB\Result|null
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public static function deleteByGroupIdPropertyId($groupId, $arPropertyId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $arPropertyId = (array) $arPropertyId;
        if (empty($arPropertyId)) {
            return null;
        }
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => abs(intval($groupId)), 'PROPERTY_ID' => $arPropertyId])));
    }
}
?>