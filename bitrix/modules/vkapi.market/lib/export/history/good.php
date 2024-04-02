<?php

namespace VKapi\Market\Export\History;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\Validators\DateValidator;
use Bitrix\Main\ORM\Fields\Validators\LengthValidator;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;
use VKapi\Market\Exception\GoodLimitException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для хранения истории ранее выгруженных товаров, чтоы после очистки сообщества от товаров можно было сопоставить заказы
 */
class GoodTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_export_history_good';
    }
    /**
 * @return array
 * @throws \Bitrix\Main\SystemException
 * fields: ID:int, EXPORT_ID:int, GROUP_ID:int, VK_ID:int, CREATED: datetime
 */
    public static function getMap()
    {
        return [(new \Bitrix\Main\Entity\IntegerField('ID'))->configurePrimary()->configureAutocomplete(), (new \Bitrix\Main\Entity\IntegerField('GROUP_ID'))->configureRequired(), (new \Bitrix\Main\Entity\IntegerField('PRODUCT_ID'))->configureRequired(), (new \Bitrix\Main\Entity\IntegerField('PRODUCT_IBLOCK_ID'))->configureNullable(), (new \Bitrix\Main\Entity\StringField('PRODUCT_XML_ID'))->configureNullable()->configureDefaultValue(null)->configureSize(70), (new \Bitrix\Main\Entity\IntegerField('OFFER_ID'))->configureRequired(), (new \Bitrix\Main\Entity\IntegerField('OFFER_IBLOCK_ID'))->configureNullable(), (new \Bitrix\Main\Entity\StringField('OFFER_XML_ID'))->configureNullable()->configureDefaultValue(null)->configureSize(70), (new \Bitrix\Main\Entity\StringField('SKU'))->configureNullable()->configureDefaultValue(null)->configureSize(50), (new \Bitrix\Main\Entity\IntegerField('VK_ID'))->configureRequired(), (new \Bitrix\Main\Entity\DatetimeField('CREATED'))->configureDefaultValue(new \Bitrix\Main\Type\DateTime()), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)')];
    }
    /**
 * Удаляет старые записи
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public static function deleteOld()
    {
        $date = new \Bitrix\Main\Type\DateTime();
        $date->add('- 2 year');
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['<CREATED' => $date])));
    }
}
/**
 * Класс для работы с историей ранее выгруженных товароd, например после очистки понадобится сопоставит заказы,
 * для это сохраним имеющиеся на текущий момент соответствия
 */
class Good
{
    /**
 * @var \VKapi\Market\Export\History\GoodTable
 */
    protected $oTable;
    public function __construct()
    {
    }
    /**
 * fields: ID:int, GROUP_ID:int, VK_ID:int, CREATED: datetime, PRODUCT_ID:int, PRODUCT_XML_ID:string, OFFER_ID:int, OFFER_XML_ID:string
 * @return \VKapi\Market\Export\History\GoodTable
 */
    public function table()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Export\History\GoodTable();
        }
        return $this->oTable;
    }
    /**
 * Добавляет в историю запись о добавленом товаре в вк
 * @param \VKapi\Market\Good\Export\Item $preparedItem
 * @param $vkId
 * @return bool
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function append(\VKapi\Market\Good\Export\Item $preparedItem, $vkId)
    {
        $vkId = (int) $vkId;
        if (!$vkId) {
            return false;
        }
        $arRow = $this->table()::getRow(['filter' => ['GROUP_ID' => (int) $preparedItem->exportItem()->getGroupId(), 'VK_ID' => $vkId]]);
        if ($arRow) {
            // patch
            if ($arRow['OFFER_ID'] && ($arRow['OFFER_XML_ID'] == $arRow['OFFER_IBLOCK_ID'] || empty($arRow['OFFER_IBLOCK_ID']))) {
                $this->table()::update($arRow['ID'], ['OFFER_IBLOCK_ID' => $preparedItem->getOfferIblockId()]);
            }
            return true;
        }
        $arFields = ['GROUP_ID' => (int) $preparedItem->exportItem()->getGroupId(), 'VK_ID' => $vkId, 'PRODUCT_ID' => $preparedItem->getProductId(), 'PRODUCT_XML_ID' => $preparedItem->getProductXmlId(), 'PRODUCT_IBLOCK_ID' => $preparedItem->getProductIblockId(), 'OFFER_ID' => $preparedItem->getOfferId(), 'OFFER_XML_ID' => $preparedItem->getOfferXmlId(), 'OFFER_IBLOCK_ID' => $preparedItem->getOfferIblockId(), 'SKU' => $preparedItem->getFieldSku()];
        if (empty($arFields['SKU'])) {
            unset($arFields['SKU']);
        }
        $result = $this->table()->add($arFields);
        return $result->isSuccess();
    }
    /**
 * Поиск элемента инфоблока
 * @param $vkId
 * @param $groupId
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function findElementByVkIdGroupId($vkId, $groupId)
    {
        if (!\VKapi\Market\Manager::getInstance()->isInstalledIblockModule()) {
            return null;
        }
        $arHistoryRow = $this->table()::getRow(['filter' => ['GROUP_ID' => (int) $groupId, 'VK_ID' => (int) $vkId]]);
        if (!$arHistoryRow) {
            return null;
        }
        do {
            // patch
            $iblockId = $arHistoryRow['OFFER_XML_ID'] == $arHistoryRow['OFFER_IBLOCK_ID'] ? null : $arHistoryRow['OFFER_IBLOCK_ID'];
            if ($element = $this->findIblockElementById($arHistoryRow['OFFER_ID'], $iblockId)) {
                break;
            }
            if ($element = $this->findIblockElementByXmlId($arHistoryRow['OFFER_XML_ID'], $iblockId)) {
                break;
            }
            if ($element = $this->findIblockElementById($arHistoryRow['PRODUCT_ID'], $arHistoryRow['PRODUCT_IBLOCK_ID'])) {
                break;
            }
            if ($element = $this->findIblockElementByXmlId($arHistoryRow['PRODUCT_XML_ID'], $arHistoryRow['PRODUCT_IBLOCK_ID'])) {
                break;
            }
            return null;
        } while (false);
        $arReturn = ['NAME' => $element['NAME'], 'ID' => $element['ID']];
        return $arReturn;
    }
    /**
 * Поиск элемента по идентификатору
 * @param $id
 * @param $iblockId
 * @return array|null
 */
    public function findIblockElementById($id, $iblockId = null)
    {
        $id = (int) $id;
        if (empty($id)) {
            return null;
        }
        $filter = ['ID' => $id];
        if (!is_null($iblockId)) {
            $filter['IBLOCK_ID'] = (int) $iblockId;
        }
        $dbr = \CIBlockElement::GetList([], $filter, false, ['nPageSize' => 1], ['IBLOCK_ID', 'ID', 'XML_ID', 'NAME', 'ACTIVE']);
        if ($ar = $dbr->Fetch()) {
            return $ar;
        }
        return null;
    }
    /**
 * Поиск элемента по XML_ID
 * @param $xmlId
 * @param $iblockId
 * @return array|null
 */
    public function findIblockElementByXmlId($xmlId, $iblockId = null)
    {
        $xmlId = trim((string) $xmlId);
        if (empty($xmlId)) {
            return null;
        }
        $filter = ['XML_ID' => $xmlId];
        if (!is_null($iblockId)) {
            $filter['IBLOCK_ID'] = (int) $iblockId;
        }
        $dbr = \CIBlockElement::GetList([], $filter, false, ['nPageSize' => 1], ['IBLOCK_ID', 'ID', 'XML_ID', 'NAME', 'ACTIVE']);
        if ($ar = $dbr->Fetch()) {
            return $ar;
        }
        return null;
    }
}
?>