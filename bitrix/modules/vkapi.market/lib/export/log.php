<?php

namespace VKapi\Market\Export;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 *  ласс дл€ аботы с таблицей логировани€ действий по типам
 * Class LogTable
 * 
 * @package VKapi\Market\Export
 */
class LogTable extends \Bitrix\Main\Entity\DataManager
{
    const TYPE_ERROR = 1;
    const TYPE_NOTICE = 2;
    const TYPE_OK = 4;
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_log';
    }
    public static function getMap()
    {
        return array(new \Bitrix\Main\Entity\IntegerField('ID', array('primary' => true, 'autocomplete' => true)), new \Bitrix\Main\Entity\IntegerField('EXPORT_ID', array(
            //идентификато экспорта
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('TYPE', array(
            //тип
            'required' => true,
        )), new \Bitrix\Main\Entity\DatetimeField('CREATE_DATE', array(
            //дата
            'required' => true,
            'default_value' => new \Bitrix\Main\Type\DateTime(),
        )), new \Bitrix\Main\Entity\StringField('MSG', array(
            //сообщение
            'required' => true,
        )), new \Bitrix\Main\Entity\TextField('MORE', array(
            //подробности
            'serialized' => true,
            'default_value' => array(),
        )), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'), new \Bitrix\Main\Entity\ReferenceField('EXPORT', '\\VKapi\\Market\\ExportTable', array('=this.EXPORT_ID' => 'ref.ID'), array('join_type' => 'LEFT')));
    }
    public function clear()
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('TRUNCATE TABLE %s', $connection->getSqlHelper()->quote($entity->getDbTableName())));
        return true;
    }
}
/**
 * Class Log
 * 
 * @package VKapi\Market\Export
 */
class Log
{
    const LEVEL_NONE = 1;
    // ничгео не записывать
    const LEVEL_OK = 2;
    // только успешные записи
    const LEVEL_NOTICE = 4;
    // записи о процессе, промежуточные
    const LEVEL_ERROR = 8;
    // только ошибки
    const LEVEL_DEBUG = 16;
    // все подр€д
    /**
 * @var int - текущий уровень логировани€
 */
    protected $level = 1;
    /**
 * @var int - объем используемой пам€ти на момент начала логировани€
 */
    protected $memoryUsedOnStart = 0;
    /**
 * @var \VKapi\Market\Export\LogTable
 */
    protected $table = null;
    /**
 * @var int идентификатор экспорта
 */
    protected $exportId = null;
    /**
 *  онструктор класса дл€ логирвоани€ действий, ошибок и тп
 * 
 * @param int $logLevel - уровень логировани€
 */
    public function __construct($logLevel = self::LEVEL_NONE)
    {
        $this->level = $logLevel;
        $this->table = new \VKapi\Market\Export\LogTable();
        $this->memoryUsedOnStart = $this->getMemoryUsed();
    }
    /**
 * ¬озвращает количество потребл€емой пам€ти
 * 
 * @return float
 */
    protected function getMemoryUsed()
    {
        return sprintf('%0.2f', memory_get_usage() / 1024 / 1024);
    }
    /**
 * ”становка нового уровн€ логировани€
 * 
 * @param $level
 */
    public function setLevel($level)
    {
        $this->level = $level;
    }
    /**
 * ‘иксируем идентфиикатор экспорта, дл€ прив€зки логов
 * 
 * @param $exportId
 */
    public function setExportId($exportId)
    {
        $this->exportId = intval($exportId);
    }
    public function exception(\Throwable $ex)
    {
        if ($ex instanceof \VKapi\Market\Exception\BaseException) {
            $this->error($ex->getMessage(), ['file' => $ex->getFile() . ':' . $ex->getLine(), 'trace' => $ex->getTraceAsString()]);
        } else {
            $this->error($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine() . ' | ' . $ex->getTraceAsString());
        }
    }
    /**
 * «апись ошибок
 * 
 * @param $text - текст
 * @param array $arData - дополнительные даныне
 * @param null $exportId - идентификатор экспорта, по умолчанию текущий
 * @throws \Exception
 * @return bool
 */
    public function error($text, $arData = array(), $exportId = null)
    {
        // проверка уровн€
        if ($this->level & ~(self::LEVEL_ERROR | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // запись
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_ERROR));
        return true;
    }
    /**
 * «апись сообщений об успешных операци€х
 * 
 * @param $text - текст
 * @param array $arData - дополнительные даныне
 * @param null $exportId - идентификатор экспорта, по умолчанию текущий
 * @throws \Exception
 * @return bool
 */
    public function ok($text, $arData = array(), $exportId = null)
    {
        // проверка уровн€
        if ($this->level & ~(self::LEVEL_OK | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // запись
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_OK));
        return true;
    }
    /**
 * «апись детальна€ всего что происходит, не ошибка и не успех
 * 
 * @param $text - текст
 * @param array $arData - дополнительные даныне
 * @param null $exportId - идентификатор экспорта, по умолчанию текущий
 * @throws \Exception
 * @return bool
 */
    public function notice($text, $arData = array(), $exportId = null)
    {
        // проверка уровн€
        if ($this->level & ~(self::LEVEL_NOTICE | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // запись
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_NOTICE));
        return true;
    }
    /**
 * ƒобавл€ет в массив с данными дополнительную информацию
 * + размер используемой пам€ти
 * + прирост по исопльзвоанию пам€ти
 * @param $arData
 */
    protected function extendData(&$arData)
    {
        $arData['MEMORY'] = $this->getMemoryUsed();
        $arData['MEMORY_INCREASE'] = round($arData['MEMORY'] - $this->memoryUsedOnStart, 2);
    }
    /**
 * ¬ернет список возможных уровней логировани€, дл€ использовани€ в SelectBoxFromArray
 * @return array
 */
    static function getLevelListForSelect()
    {
        $arReturn = array('REFERENCE_ID' => array(self::LEVEL_NONE, self::LEVEL_OK, self::LEVEL_NOTICE, self::LEVEL_ERROR, self::LEVEL_DEBUG), 'REFERENCE' => array(\VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_NONE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_OK'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_NOTICE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_ERROR'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_DEBUG')));
        return $arReturn;
    }
    /**
 * ¬ернет список возможных типов записей в лог, дл€ использовани€ в SelectBoxFromArray
 * @return array
 */
    static function getTypeListForSelect()
    {
        $arReturn = array('REFERENCE_ID' => array('', \VKapi\Market\Export\LogTable::TYPE_OK, \VKapi\Market\Export\LogTable::TYPE_NOTICE, \VKapi\Market\Export\LogTable::TYPE_ERROR), 'REFERENCE' => array(\VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_NOT_SELECTED'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_OK'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_NOTICE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_ERROR')));
        return $arReturn;
    }
}
?>