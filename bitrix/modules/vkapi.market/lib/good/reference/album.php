<?php

namespace VKapi\Market\Good\Reference;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity;
use Bitrix\Main\Error;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Result;
use VKapi\Market\Exception\BaseException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ������ ����� ����� ��������, ��������� ������������� � ����������
 * Class AlbumTable
 * 
 * ����
 * + ID :int
 * + ALBUM_ID :int
 * + PRODUCT_ID :int
 * + OFFER_ID :int
 * �������
 * + KEY `ix_album` (`ALBUM_ID`)
 * + KEY `ix_product_album` (`PRODUCT_ID`, `ALBUM_ID`),
 * + KEY `ix_offer` (`OFFER_ID`)
 * @package VKapi\Market\Good\Reference
 */
class AlbumTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_good_reference_album';
    }
    /**
 * @return array
 * @throws \Bitrix\Main\SystemException
 */
    public static function getMap()
    {
        return [new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]), new \Bitrix\Main\Entity\IntegerField('ALBUM_ID', [
            //������������� ��������
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('PRODUCT_ID', [
            //������������� ������
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('OFFER_ID', [
            //������������� ��������� �����������
            'required' => true,
            'default_value' => 0,
        ]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'), new \Bitrix\Main\Entity\ReferenceField('ALBUM', '\\VKapi\\Market\\Album\\ItemTable', ['=this.ALBUM_ID' => 'ref.ID'], ['join_type' => 'LEFT'])];
    }
    /**
 * ������� ������ �� ������ ��������������� �������
 * @param array $arId
 * 
 * @return bool
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public function deleteByIdList(array $arId)
    {
        $arId = array_map('intval', $arId);
        $arId = array_diff($arId, [0]);
        $arId = array_values(array_unique($arId));
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        if (count($arId)) {
            $arIdParts = array_chunk($arId, 100);
            foreach ($arIdParts as $arPart) {
                if (empty($arPart)) {
                    continue;
                }
                $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['ID' => $arPart])));
            }
        }
        return true;
    }
    /**
 * �������� ���������� � �������� ������� � ������� �� ���������� ID �������
 * 
 * @param $albumId
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public static function deleteAllByAlbumId($albumId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['ALBUM_ID' => intval($albumId)])));
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
    public static function deleteNotExistsYet($arAlbumId, $productIblockId, $offerIblockId)
    {
        $arAlbumId = array_map('intval', $arAlbumId);
        $arAlbumId = array_diff($arAlbumId, [0]);
        $arAlbumId = array_values(array_unique($arAlbumId));
        // ���� ��� �������� �����
        $arAlbumId[] = 0;
        $productIblockId = intval($productIblockId);
        $offerIblockId = intval($offerIblockId);
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        // \Bitrix\Main\Application::getConnection()->startTracker();
        // �������� ����� ---------
        $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.ID')->where('ref.IBLOCK_ID', $productIblockId)))->whereIn('ALBUM_ID', $arAlbumId)->whereNull('ELEMENT.ID');
        $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
        $connection->query($sql);
        // �����
        if ($offerIblockId) {
            $subQuery = static::query()->addSelect('ID')->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT', '\\Bitrix\\Iblock\\ElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.OFFER_ID', 'ref.ID')->where('ref.IBLOCK_ID', $offerIblockId)))->whereIn('ALBUM_ID', $arAlbumId)->whereNot('OFFER_ID', '0')->whereNull('ELEMENT.ID');
            $sql = sprintf('DELETE FROM %s  WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, \Bitrix\Main\ORM\Query\Query::filter()->whereExpr('%s IN (SELECT `ID` FROM (' . $subQuery->getQuery() . ') as TID )', ['ID'])));
            $connection->query($sql);
        }
        return true;
    }
}
/**
 * ������ �� �������� �������� � ������� ������ ������ � �������� �����������
 * Class Album
 * 
 * @package VKapi\Market\Good\Reference
 */
class Album
{
    /**
 * @var \VKapi\Market\Good\Reference\Album
 */
    private static $instance = null;
    /**
 * @var \VKapi\Market\Good\Reference\AlbumTable
 */
    private $oTable = null;
    public function __construct()
    {
        if (!\VKapi\Market\Manager::getInstance()->isInstalledIblockModule()) {
            throw new \VKapi\Market\Exception\BaseException('MODULE_IBLOCK_IS_NOT_INSTALLED', 'MODULE_NOT_INSTALLED');
        }
    }
    private function __clone()
    {
    }
    /**
 * ������ ������ ������� � ������� (��������)
 * 
 * @return \VKapi\Market\Good\Reference\Album
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
 * ������� ������ ������� � �������� (������� ��������������� ������)
 * ����
 * + ID :int
 * + ALBUM_ID :int
 * + PRODUCT_ID :int
 * + OFFER_ID :int
 * 
 * �������
 * + KEY `ix_album` (`ALBUM_ID`)
 * + KEY `ix_product_album` (`PRODUCT_ID`, `ALBUM_ID`),
 * + KEY `ix_offer` (`OFFER_ID`)
 * @return \VKapi\Market\Good\Reference\AlbumTable
 */
    public function getTable()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Good\Reference\AlbumTable();
        }
        return $this->oTable;
    }
    /**
 * �������� �������� � ������� ��� ������ �������
 * 
 * @param array $arElementAlbums - {elementId : {offerId : [albumId, ...] ...}, ...}
 * @param array $arAlbumId ������ �������� [albumId, ...]
 * @return bool
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function updateElementReferenceList(array $arElementAlbums, array $arAlbumId)
    {
        // �������� ����� ���� ���������
        if (empty($arElementAlbums)) {
            return false;
        }
        $arFilter = ['PRODUCT_ID' => array_keys($arElementAlbums)];
        if (!empty($arAlbumId)) {
            $arFilter['ALBUM_ID'] = $arAlbumId;
        }
        // �������� ������ ������������
        $dbrItems = $this->getTable()->getList(['filter' => $arFilter]);
        while ($arItem = $dbrItems->fetch()) {
            // ���� � ����� �������� ��� ������
            if (!isset($arElementAlbums[$arItem['PRODUCT_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementAlbums[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } elseif (!isset($arElementAlbums[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['ALBUM_ID']])) {
                $this->getTable()->delete($arItem['ID']);
            } else {
                // � ���� ������, ��������� ��� ������, �� ���������� �� ������, ����� �� �������� ��������
                unset($arElementAlbums[$arItem['PRODUCT_ID']][$arItem['OFFER_ID']][$arItem['ALBUM_ID']]);
            }
        }
        // ��������� �������������
        if (count($arElementAlbums)) {
            foreach ($arElementAlbums as $elementId => $arElementRef) {
                foreach ($arElementRef as $offerId => $arOfferRef) {
                    // ���� ���� �������, ���������
                    if (count((array) $arOfferRef)) {
                        foreach ((array) $arOfferRef as $albumId) {
                            $this->getTable()->add(['ALBUM_ID' => $albumId, 'PRODUCT_ID' => $elementId, 'OFFER_ID' => $offerId]);
                        }
                    }
                }
            }
        }
        return true;
    }
}
?>