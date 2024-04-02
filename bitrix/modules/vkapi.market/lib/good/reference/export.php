<?php

namespace VKapi\Market\Good\Reference;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Result;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ������ ����� ����� �������� � ����������
 * 
 * ����
 * + ID :int NOT NULL AUTO_INCREMENT,
 * + EXPORT_ID :int NOT NULL,
 * + PRODUCT_ID :int DEFAULT NULL,
 * + OFFER_ID :int DEFAULT NULL,
 * + FLAG :int
 * �������
 * + KEY `ix_export_flag` (`EXPORT_ID`, `FLAG`),
 * + KEY `ix_product_export` (`PRODUCT_ID`,`EXPORT_ID`),
 * + KEY `ix_offer_export` (`OFFER_ID`,`EXPORT_ID`)
 * @package VKapi\Market\Good\Reference
 */
class ExportTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_good_reference_export';
    }
    /**
 * @throws \Bitrix\Main\SystemException
 * @return array
 */
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)), new \Bitrix\Main\Entity\IntegerField('EXPORT_ID', array('required' => true)), new \Bitrix\Main\Entity\IntegerField('PRODUCT_ID', array('required' => true)), new \Bitrix\Main\Entity\IntegerField('OFFER_ID', array(
            // ����� 0  � �������� ������,
            'required' => true,
            'default_value' => 0,
        )), new \Bitrix\Main\Entity\IntegerField('FLAG', array('default_value' => \VKapi\Market\Good\Reference\Export::FLAG_NEED_UPDATE)), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'), new \Bitrix\Main\Entity\ExpressionField('CNT_DISTINCT_PRODUCT_ID', 'COUNT(DISTINCT %s)', 'PRODUCT_ID'));
    }
    /**
 * ���������� ���� ������� �� �������� ���� ������������� ����������
 * 
 * @param $exportId -������������� ��������
 * @throws \Bitrix\Main\Db\SqlQueryException
 * @return \Bitrix\Main\DB\Result
 */
    public static function setNeedUpdateByExportId($exportId)
    {
        $exportId = intval($exportId);
        $connection = \Bitrix\Main\Application::getConnection();
        return $connection->query("UPDATE `" . self::getTableName() . "` SET FLAG=" . intval(\VKapi\Market\Good\Reference\Export::FLAG_NEED_UPDATE) . " WHERE EXPORT_ID=" . intval($exportId));
    }
    /**
 * �������� ������ ���������� ��� ������� ��� ������
 * @param $elementId
 * 
 * @throws \Bitrix\Main\Db\SqlQueryException
 * @return \Bitrix\Main\DB\Result
 */
    public static function setUpdateFlagByElementId($elementId)
    {
        $elementId = intval($elementId);
        $connection = \Bitrix\Main\Application::getConnection();
        return $connection->query("UPDATE `" . self::getTableName() . "` SET FLAG=" . intval(\VKapi\Market\Good\Reference\Export::FLAG_NEED_UPDATE) . " WHERE PRODUCT_ID=" . intval($elementId) . " OR OFFER_ID=" . intval($elementId));
    }
    /**
 * �������� ���������� �������� � ������� ������� � �� ������� ���� ������� �� ��������
 * @param $arAlbumId - ������ ����������� ��������
 * @param $productIblockId - �������� �������
 * @param $offerIblockId - �������� �������� �����������
 * @return bool
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public static function deleteNotExistsYet($exportId, $productIblockId, $offerIblockId)
    {
        $productIblockId = intval($productIblockId);
        $offerIblockId = intval($offerIblockId);
        $exportId = intval($exportId);
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        // �������� ����� ---------
        $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.ID')->where('ref.IBLOCK_ID', $productIblockId)))->where('EXPORT_ID', $exportId)->whereNull('ELEMENT.ID');
        $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
        $connection->query($sql);
        // �����
        if ($offerIblockId) {
            $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.OFFER_ID', 'ref.ID')->where('ref.IBLOCK_ID', $offerIblockId)))->where('EXPORT_ID', $exportId)->whereNot('OFFER_ID', '0')->whereNull('ELEMENT.ID');
            $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
            $connection->query($sql);
        }
        return true;
    }
    /**
 * ������� ���� ������ ���������� �������, ����� ����� ������� ��������������� - ����� ������������ ����� ������
 * @param $exportId
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public function setMarkForAllByExportId($exportId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $sql = sprintf('UPDATE %s SET FLAG=%s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), (int) \VKapi\Market\Good\Reference\Export::FLAG_MARKED, \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->where('EXPORT_ID', (int) $exportId)));
        return $connection->query($sql);
    }
    /**
 * ������� ��� ���������� ������, ����������� ����� ������������ ������ ������� ���������� ��� �������
 * @param $exportId
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public function deleteAllMarkedByExportId($exportId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        // �������� ����� ---------
        $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->where('FLAG', \VKapi\Market\Good\Reference\Export::FLAG_MARKED)->where('EXPORT_ID', (int) $exportId)));
        return $connection->query($sql);
    }
    public function setFlagSkip($ID)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $sql = sprintf('UPDATE  %s SET FLAG=%s  WHERE ID=%s', $connection->getSqlHelper()->quote($entity->getDbTableName()), (int) \VKapi\Market\Good\Reference\Export::FLAG_NEED_SKIP, (int) $ID);
        return $connection->query($sql);
    }
}
/**
 * ������ �� ������� ������� � ��������, ����������� ������� ���������������� ������ ��� ��������
 * Class Export
 * 
 * @package VKapi\Market\Good\Reference
 */
class Export
{
    const FLAG_NEED_SKIP = 0;
    // ������ ����������, ����������
    const FLAG_NEED_UPDATE = 1;
    // ���������� �������� ������ �� ������ � ��
    const FLAG_NEED_DELETE = 2;
    // ���������� ������� ������ �� ������ � ��
    const FLAG_MARKED = 3;
    // ���������� ������� ������ �� ������ � ��
    /**
 * @var \VKapi\Market\Good\Reference\Export
 */
    private static $instance = null;
    /**
 * @var \VKapi\Market\Good\Reference\ExportTable
 */
    private $oTable = null;
    public function __construct()
    {
    }
    /**
 * ������ ������ ������� � ��������
 * 
 * @return \VKapi\Market\Good\Reference\Export
 */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            $c = __CLASS__;
            self::$instance = new $c();
        }
        return self::$instance;
    }
    /**
 * ������� ������ ������� � �������� (������� ��������������� ������ �������)
 * 
 * ����
 * + ID :int NOT NULL AUTO_INCREMENT,
 * + EXPORT_ID :int NOT NULL,
 * + PRODUCT_ID :int DEFAULT NULL,
 * + OFFER_ID :int DEFAULT NULL,
 * + FLAG :int
 * @return \VKapi\Market\Good\Reference\ExportTable
 */
    public function getTable()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Good\Reference\ExportTable();
        }
        return $this->oTable;
    }
    /**
 * �������� �������� � ������� ��� ������ �������
 * 
 * @param array $arElementExports - {elementId : {offerId : [exportId, ...] ...}, ...}
 * @param array $arExportId - ������ ��������������� �������� [exportId, ...]
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @return bool
 */
    public function updateElementReferenceList(array $arElementExports, array $arExportId)
    {
        // �������� ����� ���� ���������
        if (empty($arExportId) || empty($arElementExports)) {
            return false;
        }
        // �������� ������ ������������
        $dbrItems = $this->getTable()->getList(array('filter' => array('PRODUCT_ID' => array_keys($arElementExports), 'EXPORT_ID' => $arExportId)));
        while ($arItem = $dbrItems->fetch()) {
            // ���� � ����� �������� ��� ������
            if (!isset($arElementExports[$arItem['PRODUCT_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['EXPORT_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } else {
                $this->getTable()->setFlagSkip($arItem['ID']);
                // � ���� ������, ��������� ��� ������, �� ���������� �� ������, ����� �� �������� ��������
                unset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['EXPORT_ID']]);
            }
        }
        // ��������� �������������
        if (is_array($arElementExports) && count($arElementExports)) {
            foreach ($arElementExports as $elementId => $arElementRef) {
                foreach ($arElementRef as $offerId => $arOfferRef) {
                    // ���� ���� �������, ���������
                    if (is_array($arOfferRef) && count($arOfferRef)) {
                        foreach ($arOfferRef as $exportId) {
                            $this->getTable()->add(array('EXPORT_ID' => $exportId, 'PRODUCT_ID' => $elementId, 'OFFER_ID' => $offerId, 'FLAG' => self::FLAG_NEED_UPDATE));
                        }
                    }
                }
            }
        }
        return true;
    }
}
?>