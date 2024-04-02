<?php

namespace VKapi\Market\Album;

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use VKapi\Market\Exception\ApiResponseException;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\TimeoutException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ����� ��������� �������� � � ���������
 * Class ExportTable
 * 
 * ����
 * + ID:int
 * + GROUP_ID:int
 * + ALBUM_ID:int
 * + VK_ID:int
 * + HASH:string md5
 * @package VKapi\Market\Album
 */
class ExportTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_album_export_item';
    }
    /**
 * @return array
 * @throws \Bitrix\Main\SystemException
 */
    public static function getMap()
    {
        return [new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]), new \Bitrix\Main\Entity\IntegerField('GROUP_ID', [
            //������������� ������
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('ALBUM_ID', [
            //������������� �������� ���������
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('VK_ID', [
            //������������� �������� � ��, ������������� ����� ����� ���  null
            'required' => false,
            'default_value' => NULL,
        ]), new \Bitrix\Main\Entity\StringField('HASH', [
            //hash �������������� �����, ��� ���������� ������ ���������� �� ������� ��
            'required' => true,
        ]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'), new \Bitrix\Main\Entity\ReferenceField('ITEM', '\\VKapi\\Market\\Album\\ItemTable', ['=this.ALBUM_ID' => 'ref.ID'], ['join_type' => 'LEFT'])];
    }
    /**
 * �������� ���������� � ���������� ������� � �� �� ��� ���������� ID
 * 
 * @param $albumId
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public static function deleteAllByAlbumId($albumId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['ALBUM_ID' => intval($albumId)])));
    }
    /**
 * �������� ���� ������ ����� ���������� ����������
 * � ���������� � �� �� �������������� ������
 * 
 * @param int $groupId
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public static function deleteAllByGroupId($groupId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        return $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['GROUP_ID' => intval($groupId)])));
    }
}
/**
 * �������� �������� � ���������
 * Class Export
 * @package VKapi\Market\Album
 */
class Export
{
    /**
 * @var \VKapi\Market\Album\ExportTable
 */
    protected $oAlbumExportTable = null;
    /**
 * @var \VKapi\Market\Export\Item
 */
    protected $oExportItem = null;
    /**
 * @var \VKapi\Market\Export\Log
 */
    protected $oLog = null;
    /**
 * @var \VKapi\Market\Export\Photo
 */
    protected $oPhoto = null;
    /**
 * @var \VKapi\Market\State ���������
 */
    protected $oState = null;
    /**
 * @var \VKapi\Market\Album\Item
 */
    protected $oAlbumItem = null;
    /**
 * @var \VKapi\Market\ExportTable
 */
    protected $oExportTable = null;
    /**
 * @param \VKapi\Market\Export\Item $oExportItem
 */
    public function __construct(\VKapi\Market\Export\Item $oExportItem)
    {
        $this->oExportItem = $oExportItem;
    }
    /**
 * ������� ����������� ��������
 * ����
 * + ID:int
 * + GROUP_ID:int
 * + ALBUM_ID:int
 * + VK_ID:int
 * + HASH:string md5
 * 
 * @return \VKapi\Market\Album\ExportTable
 */
    public function albumExportTable()
    {
        if (is_null($this->oAlbumExportTable)) {
            $this->oAlbumExportTable = new \VKapi\Market\Album\ExportTable();
        }
        return $this->oAlbumExportTable;
    }
    /**
 * @return \VKapi\Market\Album\Item
 */
    public function item()
    {
        if (is_null($this->oAlbumItem)) {
            $this->oAlbumItem = new \VKapi\Market\Album\Item();
        }
        return $this->oAlbumItem;
    }
    /**
 * @return \VKapi\Market\Manager
 */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
 * ������ ������ ��� ������ � ������������ ����������
 * 
 * @return \VKapi\Market\Export\Photo
 */
    public function photo()
    {
        if (is_null($this->oPhoto)) {
            $this->oPhoto = new \VKapi\Market\Export\Photo();
            $this->oPhoto->setExportItem($this->exportItem());
        }
        return $this->oPhoto;
    }
    /**
 * ������ ������ ��� ������ � ����������� ��������
 * 
 * @return \VKapi\Market\Export\Item
 */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
 * ������ ������ ��� �������� ���������
 * 
 * @return \VKapi\Market\Export\Log
 */
    public function log()
    {
        if (is_null($this->oLog)) {
            $this->oLog = new \VKapi\Market\Export\Log($this->manager()->getLogLevel());
            $this->oLog->setExportId($this->exportItem()->getId());
        }
        return $this->oLog;
    }
    /**
 * ������ ������ ��� �������� ���������
 * 
 * @return \VKapi\Market\State
 */
    public function state()
    {
        if (is_null($this->oState)) {
            $this->oState = new \VKapi\Market\State('albums_' . intval($this->exportItem()->getId()), '/album');
        }
        return $this->oState;
    }
    /**
 * @param $name
 * @param null $arReplace
 * 
 * @return string
 */
    public function getMessage($name, $arReplace = [])
    {
        return \VKapi\Market\Manager::getInstance()->getMessage('ALBUM.EXPORT.' . $name, $arReplace);
    }
    /**
 * ������������ ������ �� ��������� (��������) � ���������
 * 
 * @return \VKapi\Market\Result - ����������� ��������� � �������
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRun()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        // �������� ����������
        if (!empty($data) && $data['run'] && $data['timeStart'] > time() - 60 * 3) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('WAIT_FINISH'), 'WAIT_FINISH');
        }
        // ��������� �������� ���������
        if (empty($data) || !isset($data['step']) || $data['complete']) {
            $this->state()->set(['complete' => false, 'percent' => 0, 'step' => 1, 'steps' => [
                //��� ����, ������� ����, � �������� ������, ����� �������� ���������, �������� � �������� 2 �� 10
                1 => ['name' => $this->getMessage('STEP1'), 'percent' => 0, 'error' => false],
                2 => ['name' => $this->getMessage('STEP2'), 'percent' => 0, 'error' => false],
                3 => ['name' => $this->getMessage('STEP3'), 'percent' => 0, 'error' => false],
                4 => ['name' => $this->getMessage('STEP4'), 'percent' => 0, 'error' => false],
                5 => ['name' => $this->getMessage('STEP5'), 'percent' => 0, 'error' => false],
                6 => ['name' => $this->getMessage('STEP6'), 'percent' => 0, 'error' => false],
            ]]);
            $data = $this->state()->get();
            $this->log()->notice($this->getMessage('EXPORT_ALBUMS.START'));
        }
        // ��������� ������
        $this->state()->set(['run' => true, 'timeStart' => time()])->save();
        try {
            switch ($data['step']) {
                case 1:
                    $this->exportItem()->checkApiAccess();
                    $data['step']++;
                    $data['steps'][1]['percent'] = 100;
                    $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 1, '#STEP_NAME#' => $this->getMessage('STEP1')]));
                    break;
                case 2:
                    // ��������� ����� ������� �� ����� � ��,
                    // ����� ���� ������� � ����� �������� �� ��������,
                    // ����� ������� �� �������� � ����� ������� �� ��
                    // �� ��������� �� ������� �������� � �������� � � ��
                    $resultCheckAlbumVk = $this->exportRunCheckAlbumInVk();
                    $data['steps'][2]['percent'] = $resultCheckAlbumVk->getData('percent');
                    // ���� �������� ���������
                    if ($resultCheckAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][2]['name'] = $this->getMessage('STEP2');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 2, '#STEP_NAME#' => $this->getMessage('STEP2')]));
                    } else {
                        $data['steps'][2]['name'] = $resultCheckAlbumVk->getData('name');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name'], '#PERCENT#' => $data['steps'][2]['percent']]));
                    }
                    break;
                // ������������� ��������
                case 3:
                    // �������� � �������� �������� ��������
                    $resultCheckAlbumPhotoInVk = $this->exportRunCheckAlbumPhotoInVk();
                    $data['steps'][3]['percent'] = $resultCheckAlbumPhotoInVk->getData('percent');
                    $data['steps'][3]['name'] = $resultCheckAlbumPhotoInVk->getData('name');
                    // ���� �������� ���������
                    if ($resultCheckAlbumPhotoInVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][3]['name'] = $this->getMessage('STEP3');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 3, '#STEP_NAME#' => $this->getMessage('STEP3')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name'], '#PERCENT#' => $data['steps'][3]['percent']]));
                    }
                    break;
                // ���������� � �������� ����� ����������� ��������, ��������
                case 4:
                    // ��������� ������� �������
                    $resultUpdateAlbumVk = $this->exportRunUpdateAlbumInVK();
                    $data['steps'][4]['percent'] = $resultUpdateAlbumVk->getData('percent');
                    $data['steps'][4]['name'] = $resultUpdateAlbumVk->getData('name');
                    // ���� �������� ���������
                    if ($resultUpdateAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][4]['name'] = $this->getMessage('STEP4');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 4, '#STEP_NAME#' => $this->getMessage('STEP4')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 4, '#STEP_NAME#' => $data['steps'][4]['name'], '#PERCENT#' => $data['steps'][4]['percent']]));
                    }
                    break;
                // ���������� ������������� ��������
                case 5:
                    // ��������� ������������� �������
                    $resultAddAlbumVk = $this->exportRunAddAlbumToVK();
                    $data['steps'][5]['percent'] = $resultAddAlbumVk->getData('percent');
                    $data['steps'][5]['name'] = $resultAddAlbumVk->getData('name');
                    // ���� �������� ���������
                    if ($resultAddAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][5]['name'] = $this->getMessage('STEP5');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 5, '#STEP_NAME#' => $this->getMessage('STEP5')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name'], '#PERCENT#' => $data['steps'][5]['percent']]));
                    }
                    break;
                // ��������� �������
                case 6:
                    // ��������� ������������� �������
                    $resultReorderAlbumInVK = $this->exportRunReorderAlbumInVK();
                    $data['steps'][6]['percent'] = $resultReorderAlbumInVK->getData('percent');
                    $data['steps'][6]['name'] = $resultReorderAlbumInVK->getData('name');
                    // ���� �������� ���������
                    if ($resultReorderAlbumInVK->getData('complete')) {
                        $data['step']++;
                        $data['steps'][6]['name'] = $this->getMessage('STEP6');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 6, '#STEP_NAME#' => $this->getMessage('STEP6')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 6, '#STEP_NAME#' => $data['steps'][6]['name'], '#PERCENT#' => $data['steps'][6]['percent']]));
                    }
                    break;
            }
        } catch (\VKapi\Market\Exception\BaseException $e) {
            $this->log()->error($e->getMessage(), $e->getCustomData());
        }
        // ������� ���������� �������
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] == 100) {
            $data['complete'] = true;
            $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STOP'));
        }
        if (\CModule::IncludeModuleEx("v" . "ka" . "pi.mar" . "" . "ke" . "t") === constant("M" . "ODULE_D" . "EMO_EXPI" . "R" . "" . "" . "" . "E" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEM" . "O_E" . "" . "XP" . "IR" . "E" . "" . "" . "D"), "BXMAKE" . "R_DEMO_E" . "XPIRE" . "D");
        }
        // �����������
        $this->state()->set(['run' => false, 'step' => $data['step'], 'steps' => $data['steps'], 'complete' => $data['complete'], 'percent' => $data['percent']]);
        $result->setDataArray($this->state()->get());
        if ($result->isSuccess()) {
            $this->state()->save();
        } else {
            $this->state()->clean();
        }
        return $result;
    }
    /**
 * ��������� ������� � �� ��������,
 * ���� ��� �� �����, ������ ������ � ������ ��������� ��������
 * 
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunCheckAlbumInVk()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckAlbumInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'name' => '', "percent" => 0, 'step' => 1, 'steps' => [1 => ['name' => $this->getMessage('CHECK_ALBUM_IN_VK.STEP1'), 'percent' => 0, 'error' => false], 2 => ['name' => $this->getMessage('CHECK_ALBUM_IN_VK.STEP2'), 'percent' => 0, 'error' => false], 3 => ['name' => $this->getMessage('CHECK_ALBUM_IN_VK.STEP3'), 'percent' => 0, 'error' => false]]];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            // �������� ������� ������� ���������
            $arExportedAlbum = $this->getAlbums();
            // �������� ������� ������� ���� � ��
            $resultVkAlbums = $this->getVkAlbums();
            // ���� � �� ��� �������� ----------------------------------
            if ($resultVkAlbums->getData('count') <= 0) {
                // ������� ��� ����� � ��������� � ��, ����� ���������� ������
                foreach ($arExportedAlbum as $arExportedAlbumItem) {
                    $this->albumExportTable()->delete($arExportedAlbumItem['ID']);
                }
                // ����������� �������
                foreach ($state['steps'] as $step => $stepValue) {
                    $state['steps'][$step]['percent'] = 100;
                }
                // ������� ���������� �������
                $state['step'] = count($state['steps']);
                $state['percent'] = 100;
                $state['complete'] = true;
                // ��������� ���������
                $this->state()->setField($stateKey, $state)->save();
                // ���������� ������ ��������
                $result->setDataArray($state);
                $this->log()->notice($this->getMessage('CHECK_ALBUM_VK.ALBUMS_NOT_FOUND'));
                return $result;
            }
            // ���� � �� ���� ������� -------------------
            // ����� ��� �������
            $arVkItems = $resultVkAlbums->getData('items');
            // ������ ������������ [ itemId => localALbumId, ...]
            $arExportedAlbumVkId2LocalAlbumId = array_column($arExportedAlbum, 'ALBUM_ID', 'VK_ID');
            // ������ ������������ ��� ������� ������, ���� �� ������ �������� [vkAlbumId => localAlbumId, ...]
            $arVkId2LocalAlbumId = $this->getVkItemId2LocalAlbumId($arVkItems, $arExportedAlbumVkId2LocalAlbumId);
            // ��� ������� ������� ����������� � ������� ������
            $arOtherExportsAlbumId = $this->getOtherExportsAlbumId();
            // ������� �� ��������� ���� �����, ������� ��� � ��
            if ($state['step'] == 1) {
                // ���������� ������������� � �� [localAlbumId, ...] - ������� ���� � ��� ����
                $arDiff = array_diff(array_values($arExportedAlbumVkId2LocalAlbumId), $arVkId2LocalAlbumId);
                if (count($arDiff)) {
                    $this->deleteByAlbumId($arDiff);
                    // �� ������������� ������ ���� �������
                    $arVkId2LocalAlbumId = array_flip($arVkId2LocalAlbumId);
                    foreach ($arDiff as $localAlbumId) {
                        unset($arVkId2LocalAlbumId[$localAlbumId]);
                    }
                }
                $state['step']++;
                $state['steps'][1]['percent'] = 100;
                $state['name'] = $state['steps'][1]['name'];
            } elseif ($state['step'] == 2) {
                // ������ ��������� ������� � ��, ������� ���� �� ������
                $arDiff = array_diff($arVkId2LocalAlbumId, $arOtherExportsAlbumId);
                if (count($arDiff)) {
                    $resultDelete = $this->exportRunCheckAlbumInVkActionDeleteLocalAlbumFromVk($arDiff);
                    $data['steps'][2]['percent'] = $resultDelete->getData('percent');
                    $data['steps'][2]['name'] = $resultDelete->getData('name');
                    // ������� �������
                    if ($resultDelete->getData('complete')) {
                        $state['step']++;
                    }
                } else {
                    $state['step']++;
                    $state['steps'][2]['percent'] = 100;
                }
                $state['name'] = $state['steps'][2]['name'];
            } elseif ($state['step'] == 3) {
                // ������ ��������� ������� � ��, ������� ���� �� ������
                $arDiff = array_diff($arVkId2LocalAlbumId, $arOtherExportsAlbumId);
                // �������� ������� ��������� ��������� ��
                $arDiffUnknown = array_intersect($arDiff, [0]);
                if (count($arDiffUnknown)) {
                    $resultDelete = $this->exportRunCheckAlbumInVkActionDeleteUnknownAlbumFromVk(array_keys($arDiffUnknown));
                    $data['steps'][3]['percent'] = $resultDelete->getData('percent');
                    $data['steps'][3]['name'] = $resultDelete->getData('name');
                    if ($resultDelete->getData('complete')) {
                        $state['step']++;
                    }
                    $state['name'] = $state['steps'][3]['name'];
                } else {
                    $state['step']++;
                    $state['steps'][3]['percent'] = 100;
                }
                $state['name'] = $state['steps'][3]['name'];
            } elseif ($state['step'] == 4) {
                foreach ($state['steps'] as $step => $stepValue) {
                    $state['steps'][$step]['percent'] = 100;
                }
            }
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.market") === constant("MODULE_D" . "EMO_EXPIRED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_" . "" . "" . "EXPIR" . "E" . "D"), "B" . "XMAKER_DEMO_EXPIRE" . "" . "" . "" . "D");
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray($state);
        return $result;
    }
    /**
 * ������ ������ �������� �� ������ ���������
 * 
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function getVkAlbums()
    {
        $result = new \VKapi\Market\Result();
        $arItems = [];
        try {
            $arParams = ['owner_id' => '-' . $this->exportItem()->getGroupId(), 'offset' => 0, 'count' => 100];
            $bStop = false;
            while (!$bStop) {
                $resultRequest = $this->exportItem()->connection()->method('market.getAlbums', $arParams);
                $response = $resultRequest->getData('response');
                // ���� �������� �� ��� �������, �� ��������� ������
                if ($response['count'] > $arParams['count'] + $arParams['offset']) {
                    $arParams['offset'] += $arParams['count'];
                } else {
                    $bStop = true;
                }
                $arItems = array_merge($arItems, $response['items']);
            }
        } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
            $this->log()->error($this->getMessage('GET_VK_ALBUMS', ['#MSG#' => $apiEx->getMessage()]));
        }
        $result->setData('items', $arItems);
        $result->setData('count', count($arItems));
        return $result;
    }
    /**
 * ������ ������ �������� ������� ��� ���� � ��������� ����
 * 
 * @return array - ������ ����������� �������� [localId => arItem, localId=> arItem, ...]
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function getAlbums()
    {
        $arAlbums = [];
        $dbrAlbum = $this->albumExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId()]]);
        while ($arAlbum = $dbrAlbum->fetch()) {
            $arAlbums[$arAlbum['ID']] = $arAlbum;
        }
        return $arAlbums;
    }
    /**
 * ������ ������ ������������ ��������� �������� {localAlbumId => arLocalAlbum, ...}
 * 
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getLocalAlbums()
    {
        $arAlbums = [];
        $dbrAlbum = $this->item()->table()->getList(['order' => ['ID' => 'ASC'], 'filter' => []]);
        while ($arAlbum = $dbrAlbum->fetch()) {
            $arAlbums[$arAlbum['ID']] = $arAlbum;
        }
        return $arAlbums;
    }
    /**
 * ������ ��������������� �������� ���������, ������� ����� ������� �� ��
 * 
 * @param array $arLocalAlbumId - ������ �������� ���������, ������� ����� ������� � ��
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunCheckAlbumInVkActionDeleteLocalAlbumFromVk(array $arLocalAlbumId)
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckAlbumInVkActionDeleteLocalAlbumFromVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => '', 'complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'deleted' => 0, 'arItems' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���������� ��� �������������
        if ($this->exportItem()->isDisabledOldAlbumDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $this->state()->setField($stateKey, $state)->save();
            // ���������� ������ ��������
            $result->setDataArray(['offset' => 0, 'count' => 0, 'complete' => $state['complete'], 'percent' => $state['percent'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // �������� �������� ������ ���������� ��������
            $arExportedAlbum = $this->getAlbums();
            // ��������� ������������ ����������� �������� [localAlbumId => itemId, ...]
            $arExportedAlbumId2ItemId = array_column($arExportedAlbum, 'ID', 'ALBUM_ID');
            // ������� ������ ������������ ������
            $arLocalAlbumId = array_intersect($arLocalAlbumId, array_keys($arExportedAlbumId2ItemId));
            if (empty($state['arItems'])) {
                $state['arItems'] = $arLocalAlbumId;
                $state['count'] = count($state['arItems']);
            }
            if (\CModule::IncludeModuleEx("vkapi.mark" . "" . "e" . "t") === constant("MODULE_DEM" . "O_EXPIRE" . "" . "" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXP" . "IRED"), "BXMAK" . "ER_DEM" . "O_" . "EXPIRED");
            }
            // �������� ��������
            while (count($state['arItems'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arItems'], 0, 25);
                // ���������� ������ ��� ��������
                $code = [];
                foreach ($arPart as $localAlbumId) {
                    $code[] = '"' . $localAlbumId . '" : API.market.deleteAlbum({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"album_id" : "' . $arExportedAlbum[$arExportedAlbumId2ItemId[$localAlbumId]]['VK_ID'] . '"})';
                }
                try {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    $executeErrors = $resultRequest->getData('execute_errors');
                    $executeErrorIndex = -1;
                    foreach ($response as $localAlbumId => $arAlbumDeleteResult) {
                        if ($arAlbumDeleteResult == 1) {
                            $state['deleted']++;
                            $this->log()->ok($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.DELETED', ['#ALBUM_ID#' => $localAlbumId]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->notice($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.DELETE_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $localAlbumId]));
                            }
                        }
                    }
                    // ������� �������� ������� � �������� ��������
                    // ���� �� �� ������� �������
                    $this->deleteByAlbumId($arPart);
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
                    throw $apiEx;
                }
                $state['arItems'] = array_slice($state['arItems'], 25);
                $state['offset'] += count($arPart);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arItems']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => $state['deleted'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
 * ������ ��������������� �������� ��� �������� �� ��
 * 
 * @param array $arVkAlbumId - ������ �������� � ��, ������� ����� ������� � ��
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunCheckAlbumInVkActionDeleteUnknownAlbumFromVk(array $arVkAlbumId)
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckAlbumInVkActionDeleteUnknownAlbumFromVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => '', 'complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'deleted' => 0, 'arItems' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ����� ���������� �������� �� ��
        if ($this->exportItem()->isDisabledOldAlbumDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            // ��������� ���������
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // ���������� ������ ��������
            $result->setDataArray(['count' => 0, 'offset' => 0, 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => 0, 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.DISABLED')]);
            return $result;
        }
        if (empty($state['arItems'])) {
            $state['arItems'] = array_diff($arVkAlbumId, [0, -1]);
            $state['count'] = count($state['arItems']);
        }
        try {
            // �������� ��������
            while (count($state['arItems'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arItems'], 0, 25);
                // ���������� ������ ��� ��������
                $code = [];
                foreach ($arPart as $albumId) {
                    $code[] = '"' . $albumId . '" : API.market.deleteAlbum({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"album_id" : "' . $albumId . '"})';
                }
                if (\CModule::IncludeModuleEx("vkapi.m" . "ar" . "" . "ke" . "" . "" . "t") == constant("MODULE_DEMO_E" . "XPIRED")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPI" . "" . "RED"), "BXMAK" . "ER_DEMO_EXPIRED");
                }
                try {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    $executeErrors = $resultRequest->getData('execute_errors');
                    $executeErrorIndex = -1;
                    foreach ($response as $albumId => $arAlbumDeleteResult) {
                        if ($arAlbumDeleteResult == 1) {
                            $state['deleted']++;
                            $this->log()->ok($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.DELETED', ['#ALBUM_ID#' => $albumId]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->notice($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.DELETE_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $albumId]));
                            }
                        }
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
                    throw $apiEx;
                }
                $state['arItems'] = array_slice($state['arItems'], 25);
                $state['offset'] += count($arPart);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arItems']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => $state['deleted'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
 * ��������� ����� ����������� �������� � �� ��� �������,
 * ��������� �� �������� � ��������� ����� ��� ��������
 * 
 * @return \VKapi\Market\Result - ���������, ����������� ������ ������� {complete: bool, count: int, offset: int}
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunCheckAlbumPhotoInVk()
    {
        $result = new \VKapi\Market\Result();
        // �������� ������ �������� ������� ������ ���� ��������� � ���������
        $arAlbumId = $this->exportItem()->getAlbumIds();
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckAlbumPhotoInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => false, 'complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'arId' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ��� ��������, ������ ������ ������ �� ����
        if (empty($arAlbumId)) {
            return $result->setDataArray(['name' => '', 'count' => 0, 'offset' => 0, 'complete' => true, 'percent' => 100]);
        }
        try {
            if (empty($state['arId'])) {
                $state['arId'] = $this->exportRunCheckAlbumPhotoInVkActionGetIds();
                $state['count'] = count($state['arId']);
            }
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $arAlbumIdPart = array_slice($state['arId'], 0, 1);
                // �������� �������� ��� ������� �������
                $arAlbumIdToPictureId = [];
                $dbr = $this->item()->table()->getList(['filter' => ['ID' => $arAlbumIdPart], 'select' => ['ID', 'PICTURE']]);
                while ($arAlbumItem = $dbr->fetch()) {
                    $arAlbumIdToPictureId[$arAlbumItem['ID']] = $arAlbumItem['PICTURE'];
                }
                try {
                    // �������� �������, ������ �������� ����� ��� ���������
                    // ��������� � ��������� �����
                    $resultExportAlbumPictures = $this->photo()->exportAlbumPictures(array_values($arAlbumIdToPictureId));
                    $arFileIdToAlbumId = array_flip($arAlbumIdToPictureId);
                    $photoItems = $resultExportAlbumPictures->getData('items');
                    foreach ($photoItems as $fileId => $fileResult) {
                        /**
 * @var \VKapi\Market\Result $fileResult
 */
                        if (!$fileResult->isSuccess()) {
                            $this->log()->error($this->getMessage('CHECK_ALBUM_PHOTO_IN_VK.FILE_ERROR', ['#FILE_ID#' => $fileId, '#ALBUM_ID#' => $arFileIdToAlbumId[$fileId], '#MSG#' => $fileResult->getFirstErrorMessage()]));
                            break;
                        }
                    }
                    if (\Bitrix\Main\Loader::includeSharewareModule("vkap" . "i.market") === constant("MODULE_DEMO_" . "EXP" . "I" . "" . "RE" . "D")) {
                        throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXMAKER_DEM" . "O_EX" . "PIRE" . "D");
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('CHECK_ALBUM_PHOTO_IN_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
                }
                $state['arId'] = array_slice($state['arId'], count($arAlbumIdPart));
                $state['offset'] += count($arAlbumIdPart);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'name' => $this->getMessage('CHECK_ALBUM_PHOTO_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * ������ �������������� �������� � ������������ ����������
 * @return int[]
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function exportRunCheckAlbumPhotoInVkActionGetIds()
    {
        $arReturn = [];
        $arAlbums = $this->exportItem()->getAlbumIds();
        if (empty($arAlbums)) {
            return $arReturn;
        }
        // ���� ���� �������, �������� ����� ����������
        $dbr = $this->item()->table()->getList(['filter' => ['ID' => $arAlbums, '!PICTURE' => null]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['ID'];
        }
        $sliced = array_slice($arReturn, 0, 2);
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.ma" . "rket") == constant("MOD" . "ULE_DE" . "M" . "" . "O")) {
            return $sliced;
        }
        return $arReturn;
    }
    /**
 * ��������� �������� � ���������
 * 
 * @return \VKapi\Market\Result - ���������, ����������� ������ ������� {complete: bool, count: int, offset: int}
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunUpdateAlbumInVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunUpdateAlbumInVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => '', 'complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'updated' => 0];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            $arExportAlbumsId = $this->exportItem()->getAlbumIds();
            if (empty($state['count'])) {
                if (!empty($arExportAlbumsId)) {
                    // �������� ������ ---
                    $state['count'] = $this->albumExportTable()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId(), 'ALBUM_ID' => $arExportAlbumsId]);
                }
            }
            // ���� ���� ��� ���������
            while ($state['count'] > $state['offset']) {
                $this->manager()->checkTime();
                // �������� ������ ---
                $arAlbumsIdNeedUpdate = [];
                $dbr = $this->albumExportTable()->getList(['select' => ['*', 'VK_NAME' => 'ITEM.VK_NAME', 'PICTURE' => 'ITEM.PICTURE', 'PHOTO_ID' => 'PHOTO.PHOTO_ID'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'ALBUM_ID' => $arExportAlbumsId], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('PHOTO', '\\VKapi\\Market\\Export\\PhotoTable', ['=this.PICTURE' => 'ref.FILE_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])], 'offset' => $state['offset'], 'limit' => 25]);
                while ($ar = $dbr->fetch()) {
                    if ($this->getHash($ar) != $ar['HASH']) {
                        $arAlbumsIdNeedUpdate[$ar['ALBUM_ID']] = $ar;
                    }
                    $state['offset']++;
                }
                if (empty($arAlbumsIdNeedUpdate)) {
                    continue;
                }
                // ���������� ������ ��� ��������
                $code = [];
                foreach ($arAlbumsIdNeedUpdate as $albumId => $albumData) {
                    $arCodeFields = ["owner_id" => '-' . $this->exportItem()->getGroupId(), "album_id" => $albumData['VK_ID'], "title" => $albumData['VK_NAME']];
                    if (intval($albumData['PHOTO_ID'])) {
                        $arCodeFields['photo_id'] = $albumData['PHOTO_ID'];
                    }
                    $code[] = '"' . $albumId . '" : API.market.editAlbum(' . $this->manager()->toJsonString($arCodeFields) . ')';
                }
                if (\CModule::IncludeModuleEx("vkap" . "i.mark" . "e" . "" . "t") == constant("MODU" . "LE_DEMO_EXPI" . "R" . "E" . "" . "" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_" . "" . "E" . "XPIRED"), "BXMAK" . "ER_DEMO_EXP" . "" . "" . "IRE" . "" . "D");
                }
                try {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    $executeErrors = $resultRequest->getData('execute_errors');
                    $executeErrorIndex = -1;
                    foreach ($response as $albumId => $operationResult) {
                        if ($operationResult == 1) {
                            $state['updated']++;
                            // ������� ���
                            $this->albumExportTable()->update($albumId, ['HASH' => $this->getHash($arAlbumsIdNeedUpdate[$albumId])]);
                            $this->log()->ok($this->getMessage('UPDATE_ALBUM_IN_VK.UPDATED', ['#ALBUM_ID#' => $albumId, '#VK_ID#' => $albumData['VK_ID']]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->error($this->getMessage('UPDATE_ALBUM_IN_VK.UPDATE_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $albumId]));
                                if ($executeErrors[$executeErrorIndex]['error_code'] == \VKapi\Market\Api::ERROR_100 && preg_match('/\\:\\s+photo\\s+/', $executeErrors[$executeErrorIndex]['error_msg'])) {
                                    // ������� ��������
                                    $this->photo()->deleteByPhotoId((array) intval($arAlbumsIdNeedUpdate[$albumId]['PHOTO_ID']), $this->exportItem()->getGroupId());
                                }
                            }
                        }
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('UPDATE_ALBUM_IN_VK.ERROR', ['#MSG#' => $apiEx->getMessage(), '#ID#' => implode(',', array_keys($arAlbumsIdNeedUpdate))]));
                }
            }
            if (\CModule::IncludeModuleEx("vkap" . "i.mar" . "ket") == constant("MODULE_DEMO_E" . "" . "X" . "P" . "IRED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_E" . "XPIRE" . "" . "" . "" . "" . "" . "D"), "BXMAKER_DE" . "MO_EXPI" . "RED");
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // �������� ���������
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'name' => $this->getMessage('UPDATE_ALBUM_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * ��������� �������� � ���������
 * 
 * @return \VKapi\Market\Result - ���������, ����������� ������ ������� {complete: bool, count: int, offset: int}
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunAddAlbumToVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunAddAlbumToVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'added' => 0, 'arId' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            if (empty($state['arId'])) {
                // ������� ������� ������ ����
                $arExportAlbumsId = $this->exportItem()->getAlbumIds();
                // �������� ����������� �������
                $arExportedAlbums = $this->getAlbums();
                $arExportedAlbumsId2ItemId = array_column($arExportedAlbums, 'ID', 'ALBUM_ID');
                // [ albumId => itemId, ...]
                $state['arId'] = array_diff($arExportAlbumsId, array_keys($arExportedAlbumsId2ItemId));
                $state['count'] = count($state['arId']);
            }
            // ���� ���� ��� ���������
            while (!empty($state['arId'])) {
                $this->manager()->checkTime();
                // �������� ������
                $arAlbumsIdAdd = array_splice($state['arId'], 0, $state['limit']);
                $arAlbumsData = $this->item()->getItemsById($arAlbumsIdAdd);
                $arPicturesId = array_column($arAlbumsData, 'PICTURE', 'ID');
                $arAlbumsDataPhoto = $this->photo()->getItemsByFileId($arPicturesId, $this->exportItem()->getGroupId());
                // ���������� ������ ��� ��������
                $code = [];
                foreach ($arAlbumsIdAdd as $albumId) {
                    $arCodeFields = ['owner_id' => '-' . $this->exportItem()->getGroupId(), 'title' => $arAlbumsData[$albumId]['VK_NAME']];
                    if (intval($arAlbumsDataPhoto[$arAlbumsData[$albumId]['PICTURE']]['PHOTO_ID'])) {
                        $arCodeFields['photo_id'] = $arAlbumsDataPhoto[$arAlbumsData[$albumId]['PICTURE']]['PHOTO_ID'];
                    }
                    $code[] = '"' . $albumId . '" : API.market.addAlbum(' . $this->manager()->toJsonString($arCodeFields) . ')';
                }
                try {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    $executeErrors = $resultRequest->getData('execute_errors');
                    $executeErrorIndex = -1;
                    if (\CModule::IncludeModuleEx("vkap" . "i.ma" . "r" . "ket") == constant("MODULE_DEMO_E" . "XPIR" . "" . "" . "ED")) {
                        throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "P" . "IRE" . "D"), "BXMAKER_DEMO_EXPIRED");
                    }
                    foreach ($response as $albumId => $arAlbumAddResultArray) {
                        if (isset($arAlbumAddResultArray['market_album_id'])) {
                            $state['added']++;
                            // ��� ��� ��� ����������, �� ��������� �����
                            $this->albumExportTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'ALBUM_ID' => $albumId, 'VK_ID' => $arAlbumAddResultArray['market_album_id'], 'HASH' => $this->getHash($arAlbumsData[$albumId])]);
                            $this->log()->ok($this->getMessage('ADD_ALBUM_TO_VK.ADDED', ['#ALBUM_ID#' => $albumId, '#VK_ID#' => $arAlbumAddResultArray['market_album_id']]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->error($this->getMessage('ADD_ALBUM_TO_VK.ADD_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $albumId]));
                                if ($executeErrors[$executeErrorIndex]['error_code'] == \VKapi\Market\Api::ERROR_100 && preg_match('/\\:\\s+photo\\s+/', $executeErrors[$executeErrorIndex]['error_msg'])) {
                                    // ������� ��������
                                    $this->photo()->deleteByPhotoId((array) intval($arAlbumsDataPhoto[$arAlbumsData[$albumId]['PICTURE']]['PHOTO_ID']), $this->exportItem()->getGroupId());
                                }
                            }
                        }
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('ADD_ALBUM_TO_VK.ERROR', ['#MSG#' => $apiEx->getMessage(), '#ALBUM_ID#' => implode(',', array_keys($arAlbumsIdAdd))]));
                }
                $state['offset'] += count($arAlbumsIdAdd);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // �������� ���������
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'added' => $state['added'], 'name' => $this->getMessage('ADD_ALBUM_TO_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * ��������� ������� �������� � ���������
 * 
 * @return \VKapi\Market\Result - ���������, ����������� ������ ������� {complete: bool, count: int, offset: int}
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    private function exportRunReorderAlbumInVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunReorderAlbumInVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => '', 'complete' => false, 'percent' => 0, 'count' => count($this->exportItem()->getAlbumIds()), 'offset' => 0, 'limit' => 25];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "ke" . "" . "" . "" . "t") == constant("MODULE_DEMO" . "_EXPIRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "RK" . "ET.DEMO_EXPIRE" . "D"), "B" . "" . "XMAKER_DEMO_EXPIR" . "" . "E" . "D");
        }
        try {
            // ������� ������� ������ ����
            $arExportAlbumsId = $this->exportItem()->getAlbumIds();
            // �������� ����������� �������
            $arExportedAlbums = $this->getAlbums();
            $arExportedAlbumsId2VkId = array_column($arExportedAlbums, 'VK_ID', 'ALBUM_ID');
            // [ albumId => itemId, ...]
            $arExportVkAlbumId = [];
            foreach ($arExportAlbumsId as $albumId) {
                $arExportVkAlbumId[] = $arExportedAlbumsId2VkId[$albumId];
            }
            // ������ ������ ������� ��������
            $arNeedTree = $this->getAlbumOrderTree($arExportVkAlbumId);
            // ��������� ��������� �� �� ----------------
            $arVkAlbumId = [];
            $resultVkAlbums = $this->getVkAlbums();
            foreach ($resultVkAlbums->getData('items') as $item) {
                $arVkAlbumId[] = $item['id'];
            }
            $arVkTree = $this->getAlbumOrderTree(array_values(array_intersect($arVkAlbumId, $arExportVkAlbumId)));
            $state['count'] = count($arNeedTree);
            // ���������� ������ ��� ��������
            $arCode = [];
            foreach ($arNeedTree as $vkSortId => $vkNeedSort) {
                // ���� � �� ���� ����� ������
                if (isset($arVkTree[$vkSortId])) {
                    // ��������� �������� ������
                    // ���� ���� after � ������ after ���� � ��
                    if ($vkNeedSort['a'] && $arVkTree[$vkNeedSort['a']]) {
                        if ($arVkTree[$vkSortId]['a'] != $vkNeedSort['a']) {
                            $arCode[] = '"' . $vkSortId . '" : API.market.reorderAlbums({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"album_id" : "' . $vkSortId . '", "after" : ' . $vkNeedSort['a'] . '})';
                        } else {
                            $state['offset']++;
                        }
                    } elseif ($vkNeedSort['b'] && $arVkTree[$vkNeedSort['b']]) {
                        if ($arVkTree[$vkSortId]['b'] != $vkNeedSort['b']) {
                            $arCode[] = '"' . $vkSortId . '" : API.market.reorderAlbums({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"album_id" : "' . $vkSortId . '", "before" : ' . $vkNeedSort['b'] . '})';
                        } else {
                            $state['offset']++;
                        }
                    } else {
                        $state['offset']++;
                    }
                } else {
                    $state['offset']++;
                }
            }
            // ���� ���� ��� ���������
            while (count($arCode) > 0) {
                $this->manager()->checkTime();
                // �������� ������
                $arCodeExec = array_splice($arCode, 0, $state['limit']);
                if (!count($arCodeExec)) {
                    break;
                }
                try {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $arCodeExec) . '};']);
                    $response = $resultRequest->getData('response');
                    $state['offset'] += count($arCodeExec);
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('REORDER_ALBUM_IN_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // �������� ���������
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'name' => $this->getMessage('REORDER_ALBUM_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * ������ ������ ����������, {albumId : {b:int,a:int}}
 * 
 * @param $arAlbumId [albumId, ]
 * @return array
 */
    public function getAlbumOrderTree($arAlbumId)
    {
        $arReturn = [];
        foreach ($arAlbumId as $albumIndex => $albumId) {
            $arSort = [
                'a' => false,
                //after
                'b' => false,
            ];
            if ($albumIndex > 0) {
                $arSort['a'] = $arAlbumId[$albumIndex - 1];
            }
            if ($albumIndex + 1 < count($arAlbumId)) {
                $arSort['b'] = $arAlbumId[$albumIndex + 1];
            }
            $arReturn[$albumId] = $arSort;
        }
        return $arReturn;
    }
    /**
 * ���������� ������ ������������ �������� �� ��, ��������� ���������
 * 
 * @param array $arVkItems - ������ ��������, ���������� �� �� [{id => 620,..}, ...]
 * @param array $arExportedALbumVkId2LocalAlbumId - ������ ������������ ����������� ����� �������� � �� � ��
 * ��������� ��������������� [619 => 1, 620 => 2, ...]
 * @return array [620 => 2, ...]
 */
    protected function getVkItemId2LocalAlbumId($arVkItems, $arExportedALbumVkId2LocalAlbumId)
    {
        $arReturn = [];
        // ������ ������ �������������� �������� �� ��
        $arVkItemsId = array_column($arVkItems, 'id');
        // [620,621,625,630]
        foreach ($arVkItemsId as $id) {
            if (isset($arExportedALbumVkId2LocalAlbumId[$id])) {
                $arReturn[$id] = $arExportedALbumVkId2LocalAlbumId[$id];
            } else {
                // �������� ������
                $arReturn[$id] = 0;
            }
        }
        return $arReturn;
    }
    /**
 * �������� ������� � ����������� �������� � ��
 * 
 * @param array $arAlbumId - ������ ��������� ��������, ������ � ������� ����� �������
 * @return bool
 * @throws \Exception
 */
    protected function deleteByAlbumId($arAlbumId)
    {
        $arExportedAlbums = $this->getAlbums();
        $arAlbumId2ItemId = array_column($arExportedAlbums, 'ID', 'ALBUM_ID');
        $arItemsId = [];
        foreach ($arAlbumId as $localAlbumId) {
            $itemId = $arAlbumId2ItemId[$localAlbumId];
            if ($itemId) {
                $arItemsId[] = $itemId;
            }
        }
        // ����������� ���� � ���������
        if (count($arItemsId)) {
            $arAlbumItems = $this->item()->getItemsById(array_keys($arAlbumId2ItemId));
            foreach ($arItemsId as $itemId) {
                // �������� ������ ������ �� ������ � ��
                $this->albumExportTable()->delete($itemId);
                $this->log()->notice($this->getMessage('DELETE_BY_ALBUM_ID', ['#ALBUM_ID#' => $arExportedAlbums[$itemId]['ALBUM_ID']]), ['ALBUM_ID' => $arExportedAlbums[$itemId]['ALBUM_ID']]);
                // �������� ��������
                if (isset($arAlbumItems[$arAlbumId2ItemId[$itemId]]) && intval($arAlbumItems[$arAlbumId2ItemId[$itemId]]['PICTURE'])) {
                    $this->photo()->deleteByFileId($arAlbumItems[$arAlbumId2ItemId[$itemId]]['PICTURE'], $this->exportItem()->getGroupId());
                }
            }
        }
        return true;
    }
    /**
 * ������ ������� � ������� ���������� ��������
 * [1,2,5] � [1,6] -> [2,5]
 * [1,5] � [1,6,5] -> [5]
 * 
 * @param $arCompare - ������������ �������
 * @param $arOriginal - ������������ �������, � ������� ��������� ����
 * @return array
 */
    public function getOutsideSort($arCompare, $arOriginal)
    {
        $arDiff = [];
        $bAll = false;
        $arCompare = array_values($arCompare);
        $arOriginal = array_values($arOriginal);
        foreach ($arCompare as $key => $value) {
            if ($bAll) {
                $arDiff[] = $value;
            } elseif (!isset($arOriginal[$key]) || $arOriginal[$key] != $value) {
                $arDiff[] = $value;
                $bAll = true;
            }
        }
        return $arDiff;
    }
    /**
 * ������ ��� ����� ������� ����������� � ��, ����� ����� �������
 * 
 * @param $arFieldsForVk
 * @return string
 */
    public function getHash($arFieldsForVk)
    {
        $arCheck = array_intersect_key($arFieldsForVk, array_flip(['VK_NAME', 'PICTURE']));
        ksort($arCheck);
        return md5(serialize($arCheck));
    }
    /**
 * ������ ������ �������� �� ���� �������� ��� ������� ������
 * 
 * @return array - [localAlbumId, localAlbumId, ...]
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    protected function getOtherExportsAlbumId()
    {
        $arReturn = [];
        if ($this->exportItem()->getGroupId()) {
            $dbr = $this->manager()->exportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'ACTIVE' => true]]);
            while ($ar = $dbr->fetch()) {
                $arReturn = array_merge($arReturn, $ar['ALBUMS']);
            }
        }
        return array_values(array_unique($arReturn));
    }
}
?>