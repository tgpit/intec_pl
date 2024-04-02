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
 * Хранит связи между товарами и выгрузками
 * 
 * Поля
 * + ID :int NOT NULL AUTO_INCREMENT,
 * + EXPORT_ID :int NOT NULL,
 * + PRODUCT_ID :int DEFAULT NULL,
 * + OFFER_ID :int DEFAULT NULL,
 * + FLAG :int
 * Индексы
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
            // будет 0  у простого товара,
            'required' => true,
            'default_value' => 0,
        )), new \Bitrix\Main\Entity\IntegerField('FLAG', array('default_value' => \VKapi\Market\Good\Reference\Export::FLAG_NEED_UPDATE)), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'), new \Bitrix\Main\Entity\ExpressionField('CNT_DISTINCT_PRODUCT_ID', 'COUNT(DISTINCT %s)', 'PRODUCT_ID'));
    }
    /**
 * Установить всем товарам из выгрузки флаг необходимости обновления
 * 
 * @param $exportId -идентификатор выгрузки
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
 * Устновка флагов обновления для товаров или оферов
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
 * Удаление добавленых привязок к альбому товаров и тп которые были удалены из битркиса
 * @param $arAlbumId - массив проверяемых альбомов
 * @param $productIblockId - инфоблок катлога
 * @param $offerIblockId - инфоблок торговых предложений
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
        // основной товар ---------
        $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.ID')->where('ref.IBLOCK_ID', $productIblockId)))->where('EXPORT_ID', $exportId)->whereNull('ELEMENT.ID');
        $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
        $connection->query($sql);
        // оффер
        if ($offerIblockId) {
            $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.OFFER_ID', 'ref.ID')->where('ref.IBLOCK_ID', $offerIblockId)))->where('EXPORT_ID', $exportId)->whereNot('OFFER_ID', '0')->whereNull('ELEMENT.ID');
            $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
            $connection->query($sql);
        }
        return true;
    }
    /**
 * Отметим весь список отобранных товаров, чтобы потом удалить необхработанные - после формирвоания новго списка
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
 * Удаляем все отмеченные записи, запускается после формирования спсика товаров подходящих под условия
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
        // основной товар ---------
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
 * Работа со связями товаров и выгрузок, заполняется агентом подготавливающим товары для выгрузки
 * Class Export
 * 
 * @package VKapi\Market\Good\Reference
 */
class Export
{
    const FLAG_NEED_SKIP = 0;
    // запись обработана, пропускаем
    const FLAG_NEED_UPDATE = 1;
    // необходимо обнвоить данные по товару в вк
    const FLAG_NEED_DELETE = 2;
    // необходимо удалить данные по товару в вк
    const FLAG_MARKED = 3;
    // необходимо удалить данные по товару в вк
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
 * Объект связей товаров и выгрузок
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
 * Таблица связей товаров и выгрузок (таблица подготовленного списка товаров)
 * 
 * Поля
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
 * Проверит привязки и обновит для спсика товаров
 * 
 * @param array $arElementExports - {elementId : {offerId : [exportId, ...] ...}, ...}
 * @param array $arExportId - массив идентификаторов выгрузок [exportId, ...]
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @return bool
 */
    public function updateElementReferenceList(array $arElementExports, array $arExportId)
    {
        // проверим чтобы было заполнено
        if (empty($arExportId) || empty($arElementExports)) {
            return false;
        }
        // собираем данные существующие
        $dbrItems = $this->getTable()->getList(array('filter' => array('PRODUCT_ID' => array_keys($arElementExports), 'EXPORT_ID' => $arExportId)));
        while ($arItem = $dbrItems->fetch()) {
            // если в новом вараинте нет товара
            if (!isset($arElementExports[$arItem['PRODUCT_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['EXPORT_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } else {
                $this->getTable()->setFlagSkip($arItem['ID']);
                // в ином случае, оставляем эту запись, но исключаяем из масива, чтобы не добавить дубликат
                unset($arElementExports[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['EXPORT_ID']]);
            }
        }
        // добавляем отсутствующие
        if (is_array($arElementExports) && count($arElementExports)) {
            foreach ($arElementExports as $elementId => $arElementRef) {
                foreach ($arElementRef as $offerId => $arOfferRef) {
                    // если етсь альбомы, добавляем
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