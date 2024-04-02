<?php

namespace VKapi\Market\Good;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Хранит данные по выгруженным товарам в вк, на основе списков
 * Class ExportTable
 * Индексы
 * + KEY `ix_group` (`GROUP_ID`),
 * + KEY `ix_product_group` (`PRODUCT_ID`,`GROUP_ID`),
 * + KEY `ix_offer_group` (`OFFER_ID`,`GROUP_ID`)
 * 
 * @package VKapi\Market\Good
 */
class ExportTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_good_export';
    }
    /**
     * @return array
     * @throws \Bitrix\Main\SystemException
     */
    public static function getMap()
    {
        return [new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', [
            //группа для выгрузки
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('PRODUCT_ID', []), new \Bitrix\Main\Entity\IntegerField('OFFER_ID', []), new \Bitrix\Main\Entity\IntegerField('VK_ID', [
            //идентификатор товара в вк,
            'default_value' => null,
        ]), new \Bitrix\Main\Entity\StringField('HASH', [
            //hash подготовленных полей, для исключения лишних обновлений на стороне вк
            'required' => true,
        ]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)')];
    }
    /**
     * Удаление всех записей по выгруженным товарам в конкретную группу
     * 
     * @param $groupId -идентификатор группы в которую выгружали
     * @return \Bitrix\Main\DB\Result
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function deleteAllByGroupId($groupId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => abs(intval($groupId))])));
    }
    /**
     * Веренет идентфикаторы дубликатов по группе вк
     * 
     * @param $groupId
     * @return array - {rowId => {}}, ...}
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function getDoublesIdByGroupId($groupId)
    {
        $arReturn = [];
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $dbr = $connection->query(sprintf('select ID, VK_ID, CONCAT(PRODUCT_ID,\'_\' , OFFER_ID) as DOUBLES
                    from %s
                    WHERE %s
                    GROUP BY DOUBLES
                    HAVING COUNT(DOUBLES) > 1', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => abs(intval($groupId))])));
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['ID'];
        }
        return $arReturn;
    }
    /**
     * Удалит дубликаты ссылок на один и тот же товар, сформирвоанные при использвоании
     * базового режима и объединении торговых предложений
     * 
     * @param $groupId
     * @throws \Bitrix\Main\Db\SqlQueryException
     */
    public static function deleteDoublesVkIdByGroupId($groupId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $groupId = abs(intval($groupId));
        $sql = sprintf('
            DELETE FROM %s  WHERE VK_ID in (
                select VK_ID from (
                    select  ID, VK_ID
                               from %s
                               WHERE GROUP_ID = %s
                               GROUP BY VK_ID
                               HAVING COUNT(VK_ID) > 1
                    )
                    as TID
            )
            ', $connection->getSqlHelper()->quote($entity->getDbTableName()), $connection->getSqlHelper()->quote($entity->getDbTableName()), $groupId);
        $connection->query($sql);
        return true;
    }
    /**
     * Проставит всем торговым предложениям единые данные, используется только при базовом режиме с объединением тп
     * @param int $groupId
     * @param int $productId
     * @param [] $arFields
     * @return false|void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\SystemException
     */
    public static function updateByGroupIdProductId($groupId, $productId, $arFields)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        unset($arFields['PRODUCT_ID'], $arFields['OFFER_ID'], $arFields['ID']);
        if (empty($arFields)) {
            return false;
        }
        $update = $connection->getSqlHelper()->prepareUpdate($entity->getDbTableName(), $arFields);
        $sql = sprintf('UPDATE %s ' . ' SET %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), $update[0], \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => abs(intval($groupId)), 'PRODUCT_ID' => intval($productId)]));
        $connection->query($sql);
    }
}
?>