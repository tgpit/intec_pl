<?php

namespace VKapi\Market\Property;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use VKapi\Market\Error;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для работы с таблицей свойств,
 * хранит соответствия локлаьных свойств и выгружаенных
 * 
 * Поля
 * + ID int(11) NOT NULL AUTO_INCREMENT,
 * + GROUP_ID int(11) NOT NULL,
 * + PROPERTY_ID int(11) NOT NULL,
 * + VK_PROPERTY_ID int(11) NOT NULL,
 * Индексы
 * + PRIMARY KEY (`ID`),
 * + KEY `ix_gid_pid` (`GROUP_ID`, `PROPERTY_ID`) USING BTREE ,
 * @package VKapi\Market\Property
 */
class PropertyTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_property';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', array(
            // идентификатор группы в вконтакте, целое положительное число
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('PROPERTY_ID', array(
            //id свойства
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('VK_PROPERTY_ID', array(
            // id сыойства в вк
            'required' => true,
        )), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'));
    }
    /**
 * Удаление всех свойсвта выгруженных в группу,
 * используется при очистке группы
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
 * Удаление свойсвтв выгруженных в группу
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
/**
 * Класс для работы со свойствами
 * Class Property
 * 
 * @package VKapi\Market\Property
 */
class Property
{
    /**
 * @var PropertyTable
 */
    protected $oTable = null;
    /**
 * @var \VKapi\Market\Property\VariantTable
 */
    protected $oVariantTable = null;
    public function __construct()
    {
        if (!\VKapi\Market\Manager::getInstance()->isInstalledIblockModule()) {
            // exception
        }
    }
    /**
 * Вернет объект для работы с таблицей свойств
 * 
 * Поля
 * + ID int(11) NOT NULL AUTO_INCREMENT,
 * + GROUP_ID int(11) NOT NULL,
 * + PROPERTY_ID int(11) NOT NULL,
 * + VK_PROPERTY_ID int(11) NOT NULL,
 * @return \VKapi\Market\Property\PropertyTable
 */
    public function table()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Property\PropertyTable();
        }
        return $this->oTable;
    }
    /**
 * Вернет объект для работы с таблицей вараинтов свойств
 * 
 * @return \VKapi\Market\Property\VariantTable
 */
    public function variantTable()
    {
        if (is_null($this->oVariantTable)) {
            $this->oVariantTable = new \VKapi\Market\Property\VariantTable();
        }
        return $this->oVariantTable;
    }
    /**
 * Удаляет из таблицы связей данные о выгруженных свойствах
 * и вараинтах по списку идентфикаторов свойств битркиса
 * @param $groupId
 * @param $arPropertyId
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public function deleteByGroupIdPropertyId($groupId, $arPropertyId)
    {
        $this->table()->deleteByGroupIdPropertyId($groupId, $arPropertyId);
        $this->variantTable()->deleteByGroupIdPropertyId($groupId, $arPropertyId);
    }
    /**
 * Вернет список выгруженных свойств по идентифкитаору группы
 * @param $groupId
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getListByGroupId($groupId)
    {
        $arReturn = array();
        $dbr = $this->table()->getList(['filter' => ['GROUP_ID' => $groupId]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar;
        }
        return $arReturn;
    }
    /**
 * Вернет список выгруженных вараинтов свойства
 * @param $groupId
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getVariantsByGroupIdPropertyId($groupId, $propertyId)
    {
        $arReturn = array();
        $dbr = $this->variantTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $groupId, 'PROPERTY_ID' => $propertyId]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar;
        }
        return $arReturn;
    }
    /**
 * Вернет вараинты свойства, для типа список и справочника, иначе пустой массив
 * @param $propertyId
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getPropertyVariants($propertyId)
    {
        $arReturn = [];
        $arProperty = $this->getIblockPropertyById($propertyId);
        if (is_null($arProperty)) {
            return $arReturn;
        }
        if ($arProperty['PROPERTY_TYPE'] == 'L') {
            $arReturn = $this->getIblockPropertyEnumList($arProperty);
        } elseif ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['USER_TYPE'] == 'directory') {
            $arReturn = $this->getHighloadValuesList($arProperty);
        }
        return $arReturn;
    }
    /**
 * Вернет спсиок вариантов для свойства типа спсиок
 * @param $arProperty
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getIblockPropertyEnumList($arProperty)
    {
        $arReturn = [];
        $dbr = \Bitrix\Iblock\PropertyEnumerationTable::getList(['order' => ['ID' => 'ASC'], 'filter' => ['PROPERTY_ID' => $arProperty['ID']]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = ['ID' => $ar['ID'], 'XML_ID' => $ar['XML_ID'], 'NAME' => $ar['VALUE']];
        }
        return $arReturn;
    }
    /**
 * Вернет спсиок вариантов хайлоадблока
 * @param $arProperty
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getHighloadValuesList($arProperty)
    {
        $arReturn = [];
        if (!\VKapi\Market\Manager::getInstance()->isInstalledHighloadBlockModule()) {
            return $arReturn;
        }
        $tableName = null;
        if (isset($arProperty['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'])) {
            $tableName = $arProperty['USER_TYPE_SETTINGS_LIST']['TABLE_NAME'];
        } elseif (isset($arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'])) {
            $tableName = $arProperty['USER_TYPE_SETTINGS']['TABLE_NAME'];
        }
        if (is_null($tableName)) {
            return $arReturn;
        }
        // получаем класс для работы
        $arHLBlock = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('select' => array('*'), 'order' => array('NAME' => 'ASC'), 'filter' => array('TABLE_NAME' => $tableName)))->fetch();
        if (!$arHLBlock) {
            return $arReturn;
        }
        // затем инициализировать класс сущности
        $obEntity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arHLBlock);
        $strEntityDataClass = $obEntity->getDataClass();
        $arHighloadClasses[$tableName] = new $strEntityDataClass();
        $dbrRows = $arHighloadClasses[$tableName]->getList([]);
        while ($ar = $dbrRows->fetch()) {
            $arReturn[] = ['ID' => $ar['ID'], 'XML_ID' => $ar['UF_XML_ID'] ?? $ar['ID'], 'NAME' => $ar['UF_NAME'] ?? ''];
        }
        return $arReturn;
    }
    /**
 * Вернет массив со свойствами отобаранными по спсику идентфикаторов
 * @param $arId
 * @return array
 */
    public function getIblockPropertiesById($arId)
    {
        $arReturn = [];
        if (empty($arId)) {
            return $arReturn;
        }
        $dbr = \Bitrix\Iblock\PropertyTable::getList(['filter' => ['ID' => $arId]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar;
        }
        return $arReturn;
    }
    /**
 * Вернет данные по свойству
 * @param $propertyId
 * @return array|false|null
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getIblockPropertyById($propertyId)
    {
        $dbr = \Bitrix\Iblock\PropertyTable::getList(['filter' => ['ID' => intval($propertyId)]]);
        if ($ar = $dbr->fetch()) {
            return $ar;
        }
        return null;
    }
}
?>