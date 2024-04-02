<?php

namespace VKapi\Market\Export\Limit;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use Bitrix\Main\Type\DateTime;
use VKapi\Market\Exception\GoodLimitException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для записи времени добавления товаров, для дальнешего лимитирования *
 */
class GoodTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_export_limit_good';
    }
    /**
 * @return array
 * @throws \Bitrix\Main\SystemException
 * fields: ID:int, EXPORT_ID:int, GROUP_ID:int, VK_ID:int, CREATED: datetime
 */
    public static function getMap()
    {
        return [new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]), new \Bitrix\Main\Entity\IntegerField('EXPORT_ID', [
            //идентификтор экспорта
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', [
            // идентификтаор группы
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('VK_ID', [
            // идентификтаор товара в VK
            'required' => true,
        ]), new \Bitrix\Main\Entity\DatetimeField('CREATED', ['required' => true, 'default_value' => new \Bitrix\Main\Type\DateTime()]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)')];
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
        $date->add('- 1 day');
        $date->add('- 1 minute');
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['<CREATED' => $date])));
    }
}
/**
 * Класс для учета добавленных товаров и выдерживания лимитов
 */
class Good
{
    const HOUR_LIMIT = 1000;
    const DAY_LIMIT = 7000;
    /**
 * @var \VKapi\Market\Export\Item
 */
    protected $oExportItem;
    /**
 * @var \VKapi\Market\Export\Limit\GoodTable
 */
    protected $oTable;
    public function __construct(\VKapi\Market\Export\Item $oExportItem)
    {
        $this->oExportItem = $oExportItem;
    }
    /**
 * fields: ID:int, EXPORT_ID:int, GROUP_ID:int, VK_ID:int, CREATED: datetime
 * @return \VKapi\Market\Export\Limit\GoodTable
 */
    public function table()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Export\Limit\GoodTable();
        }
        return $this->oTable;
    }
    /**
 * @return \VKapi\Market\Export\Item
 */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
 * Добавленеи записи в таблицу о добавленом товаре
 * @param $vkId
 * @return null
 * @throws \Exception
 */
    public function append($vkId)
    {
        $arFields = ['EXPORT_ID' => $this->exportItem()->getId(), 'GROUP_ID' => $this->exportItem()->getGroupId(), 'VK_ID' => (int) $vkId];
        $result = $this->table()->add($arFields);
        if ($result->isSuccess()) {
            $result->getId();
        }
        return null;
    }
    /**
 * Проверит не достигнуты ли лимиты и если достигнуты, выбросит исключение
 * @return void
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \VKapi\Market\Exception\GoodLimitException
 */
    public function check()
    {
        $hour = new \Bitrix\Main\Type\DateTime();
        $hour->add('- 1 hour');
        $count = $this->table()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId(), '>CREATED' => $hour]);
        if ($count >= self::HOUR_LIMIT) {
            throw new \VKapi\Market\Exception\GoodLimitException();
        }
        $day = new \Bitrix\Main\Type\DateTime();
        $day->add('- 24 hours');
        $count = $this->table()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId(), '>CREATED' => $day]);
        if ($count >= self::DAY_LIMIT) {
            throw new \VKapi\Market\Exception\GoodLimitException();
        }
    }
}
?>