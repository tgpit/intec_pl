<?php

namespace VKapi\Market\Export;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Type\DateTime;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ����� ��� ����� � �������� ����������� �������� �� �����
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
            //������������ ��������
            'required' => true,
        )), new \Bitrix\Main\Entity\IntegerField('TYPE', array(
            //���
            'required' => true,
        )), new \Bitrix\Main\Entity\DatetimeField('CREATE_DATE', array(
            //����
            'required' => true,
            'default_value' => new \Bitrix\Main\Type\DateTime(),
        )), new \Bitrix\Main\Entity\StringField('MSG', array(
            //���������
            'required' => true,
        )), new \Bitrix\Main\Entity\TextField('MORE', array(
            //�����������
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
    // ������ �� ����������
    const LEVEL_OK = 2;
    // ������ �������� ������
    const LEVEL_NOTICE = 4;
    // ������ � ��������, �������������
    const LEVEL_ERROR = 8;
    // ������ ������
    const LEVEL_DEBUG = 16;
    // ��� ������
    /**
 * @var int - ������� ������� �����������
 */
    protected $level = 1;
    /**
 * @var int - ����� ������������ ������ �� ������ ������ �����������
 */
    protected $memoryUsedOnStart = 0;
    /**
 * @var \VKapi\Market\Export\LogTable
 */
    protected $table = null;
    /**
 * @var int ������������� ��������
 */
    protected $exportId = null;
    /**
 * ����������� ������ ��� ����������� ��������, ������ � ��
 * 
 * @param int $logLevel - ������� �����������
 */
    public function __construct($logLevel = self::LEVEL_NONE)
    {
        $this->level = $logLevel;
        $this->table = new \VKapi\Market\Export\LogTable();
        $this->memoryUsedOnStart = $this->getMemoryUsed();
    }
    /**
 * ���������� ���������� ������������ ������
 * 
 * @return float
 */
    protected function getMemoryUsed()
    {
        return sprintf('%0.2f', memory_get_usage() / 1024 / 1024);
    }
    /**
 * ��������� ������ ������ �����������
 * 
 * @param $level
 */
    public function setLevel($level)
    {
        $this->level = $level;
    }
    /**
 * ��������� ������������� ��������, ��� �������� �����
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
 * ������ ������
 * 
 * @param $text - �����
 * @param array $arData - �������������� ������
 * @param null $exportId - ������������� ��������, �� ��������� �������
 * @throws \Exception
 * @return bool
 */
    public function error($text, $arData = array(), $exportId = null)
    {
        // �������� ������
        if ($this->level & ~(self::LEVEL_ERROR | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // ������
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_ERROR));
        return true;
    }
    /**
 * ������ ��������� �� �������� ���������
 * 
 * @param $text - �����
 * @param array $arData - �������������� ������
 * @param null $exportId - ������������� ��������, �� ��������� �������
 * @throws \Exception
 * @return bool
 */
    public function ok($text, $arData = array(), $exportId = null)
    {
        // �������� ������
        if ($this->level & ~(self::LEVEL_OK | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // ������
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_OK));
        return true;
    }
    /**
 * ������ ��������� ����� ��� ����������, �� ������ � �� �����
 * 
 * @param $text - �����
 * @param array $arData - �������������� ������
 * @param null $exportId - ������������� ��������, �� ��������� �������
 * @throws \Exception
 * @return bool
 */
    public function notice($text, $arData = array(), $exportId = null)
    {
        // �������� ������
        if ($this->level & ~(self::LEVEL_NOTICE | self::LEVEL_DEBUG)) {
            return false;
        }
        $this->extendData($arData);
        // ������
        $this->table->add(array('EXPORT_ID' => intval($exportId) ?: $this->exportId, 'MSG' => $text, 'MORE' => $arData, 'TYPE' => \VKapi\Market\Export\LogTable::TYPE_NOTICE));
        return true;
    }
    /**
 * ��������� � ������ � ������� �������������� ����������
 * + ������ ������������ ������
 * + ������� �� ������������� ������
 * @param $arData
 */
    protected function extendData(&$arData)
    {
        $arData['MEMORY'] = $this->getMemoryUsed();
        $arData['MEMORY_INCREASE'] = round($arData['MEMORY'] - $this->memoryUsedOnStart, 2);
    }
    /**
 * ������ ������ ��������� ������� �����������, ��� ������������� � SelectBoxFromArray
 * @return array
 */
    static function getLevelListForSelect()
    {
        $arReturn = array('REFERENCE_ID' => array(self::LEVEL_NONE, self::LEVEL_OK, self::LEVEL_NOTICE, self::LEVEL_ERROR, self::LEVEL_DEBUG), 'REFERENCE' => array(\VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_NONE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_OK'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_NOTICE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_ERROR'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.LEVEL_DEBUG')));
        return $arReturn;
    }
    /**
 * ������ ������ ��������� ����� ������� � ���, ��� ������������� � SelectBoxFromArray
 * @return array
 */
    static function getTypeListForSelect()
    {
        $arReturn = array('REFERENCE_ID' => array('', \VKapi\Market\Export\LogTable::TYPE_OK, \VKapi\Market\Export\LogTable::TYPE_NOTICE, \VKapi\Market\Export\LogTable::TYPE_ERROR), 'REFERENCE' => array(\VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_NOT_SELECTED'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_OK'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_NOTICE'), \VKapi\Market\Manager::getInstance()->getMessage('LOG.TYPE_ERROR')));
        return $arReturn;
    }
}
?>