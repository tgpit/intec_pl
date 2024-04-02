<?php

namespace VKapi\Market\Export;

use Bitrix\Main\Entity;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Query\Query;
use VKapi\Market\Error;
use VKapi\Market\Exception\BaseException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ����� ��� ������ � �������� �������� ������� � ���������
 * ������������ � ����� ������ �������� �������� � �������
 * 
 * ����
 * + ID int(11) NOT NULL AUTO_INCREMENT,
 * + FILE_ID int(11) NOT NULL,
 * + GROUP_ID int(11) NOT NULL,
 * + PHOTO_ID int(11) NOT NULL,
 * + MAIN tinyint(1) NOT NULL DEFAULT '0',
 * + PID int(11) NOT NULL,
 * + HASH varchar(255) NOT NULL,
 * + WM_HASH varchar(255) DEFAULT NULL,
 * �������
 * + PRIMARY KEY (`ID`),
 * + KEY `ix_gid_eid` (`FILE_ID`, `GROUP_ID`, `MAIN`, `PID`) USING BTREE ,
 * + KEY `ix_fid_git_m` (`GROUP_ID`,`FILE_ID`,`PID`) USING BTREE,
 * + KEY `ix_photo_group` (`PHOTO_ID`,`GROUP_ID`) USING BTREE
 * @package VKapi\Market\Export
 */
class PhotoTable extends \Bitrix\Main\Entity\DataManager
{
    public static function getFilePath()
    {
        return __FILE__;
    }
    public static function getTableName()
    {
        return 'vkapi_market_export_photo_list';
    }
    public static function getMap()
    {
        return [
            new \Bitrix\Main\Entity\IntegerField('ID', ['primary' => true, 'autocomplete' => true]),
            // ������������� ������ � ���������, ����� ������������� �����
            new \Bitrix\Main\Entity\IntegerField('GROUP_ID', ['required' => true]),
            ///������������� ����� � ��������
            new \Bitrix\Main\Entity\IntegerField('FILE_ID', ['required' => true]),
            //������������� �������� � ���������
            new \Bitrix\Main\Entity\IntegerField('PHOTO_ID', ['required' => false]),
            //������������� ������, �������� ����������� ��������
            new \Bitrix\Main\Entity\IntegerField('PID', ['required' => false, 'default' => null]),
            //������������� �����
            new \Bitrix\Main\Entity\IntegerField('OID', ['required' => false, 'default' => null]),
            // ������� �������� ������
            new \Bitrix\Main\Entity\IntegerField('MAIN', ['default' => 0, 'required' => false]),
            //��� ��������, ��� ������ �������� � ������ �����  ���� � ���������
            new \Bitrix\Main\Entity\StringField('HASH', ['default' => '']),
            // ���  ������� �����, ��� ������ �������� � ������ ��������� ���������� ��������� ������� �����
            new \Bitrix\Main\Entity\StringField('WM_HASH', []),
            new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(ID)'),
        ];
    }
    /**
 * �������� ���� �������� ����������� � ������,
 * ������������ ��� ������� ������
 * 
 * @param $groupId
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
 * �������� ������ � ����������� �������� � �� �� �������������� ���������� ����� � ������
 * 
 * @param int $fileId - ������������� ����� ����������
 * @param int $groupId - ������������� ������ � ������� ��� �������� ���, ����� ���� �� �����, ����� �������� ��� ���� �����
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public static function deleteByFileId($fileId, $groupId = null)
    {
        $arFilter = ['FILE_ID' => intval($fileId)];
        if (!is_null($groupId)) {
            $arFilter['GROUP_ID'] = abs(intval($groupId));
        }
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, $arFilter)));
    }
    /**
 * �������� ������ � ����������� �������� � �� �� �������������� ���������� ����� � ������
 * 
 * @param $arPhotoId - ������������� ����� ����������
 * @param $groupId - ������������� ������ � ������� ��� �������� ����
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public function deleteByPhotoId($arPhotoId, $groupId)
    {
        $arPhotoId = (array) $arPhotoId;
        $arPhotoId = array_map('intval', $arPhotoId);
        $arPhotoId = array_diff($arPhotoId, [0]);
        if (empty($arPhotoId)) {
            $arPhotoId = [0];
        }
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['PHOTO_ID' => $arPhotoId, 'GROUP_ID' => abs(intval($groupId))])));
    }
    /**
 * �������� ������ � ����������� ��������� � ��
 * @param $productId
 * @param $offerId
 * @param $groupId
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\DB\SqlQueryException
 * @throws \Bitrix\Main\SystemException
 */
    public function deleteByProduct($productId, $offerId, $groupId)
    {
        $entity = static::getEntity();
        $connection = $entity->getConnection();
        $connection->query(sprintf('DELETE FROM %s WHERE %s', $connection->getSqlHelper()->quote($entity->getDbTableName()), \Bitrix\Main\ORM\Query\Query::buildFilterSql($entity, ['PID' => intval($productId), 'OID' => intval($offerId), 'GROUP_ID' => abs(intval($groupId))])));
    }
}
/**
 * ����� ��� ������ � ���������� ��� ��
 * ������������ � ����� ������ �������� �������� � �������
 * Class Photo
 * 
 * @package VKapi\Market\Export
 */
class Photo
{
    /**
 * @var \VKapi\Market\Export\PhotoTable
 */
    protected $oTable = null;
    /**
 * @var \VKapi\Market\Export\Log �����������
 */
    protected $oLog = null;
    /**
 * @var \VKapi\Market\Export\Item
 */
    protected $oExportItem = null;
    /**
 * @var string - ��� ���������� �������� �������� �������
 */
    protected $productHash = null;
    /**
 * @var string - ��� ���������� ��������� ������� �����
 */
    protected $watermarkHash = null;
    /**
 * @var array|false - �������� ������� �����
 */
    protected $watermarkParams = null;
    /**
 * @var \CFile - ������ ����� ��� ������ � �������
 */
    protected $oFile = null;
    /**
 * @var int - ����� �������������
 */
    protected $previewMode = 0;
    /**
 * �����������. � ������� ���������� �������� ������ ���������� ������� (��������)
 * ��� ���������� ���� ��������, ������ ������ � ��, ��� ������������ ������������� �
 * �������� ��������, ����������� ������� � ��
 * 
 */
    public function __construct()
    {
        $this->createTemporaryDirectories();
    }
    /**
 * ��������� ������ ������
 * 
 * @param $flag
 */
    public function setModePreview($flag)
    {
        $this->previewMode = (bool) $flag;
        $this->createTemporaryDirectories();
    }
    /**
 * �������� ������ ������
 */
    public function isModePreview()
    {
        return $this->previewMode;
    }
    /**
 * ��������� ����������� ��������� ���������� ��� ������
 */
    public function createTemporaryDirectories()
    {
        \Bitrix\Main\IO\Directory::createDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getWatermarkDir());
        \Bitrix\Main\IO\Directory::createDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getCanvasDir());
        \Bitrix\Main\IO\Directory::createDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getCloudDir());
    }
    /**
 * ������� ��������� ����������
 */
    public function deleteTemporaryDirectories()
    {
        try {
            \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getWatermarkDir());
            \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getCanvasDir());
            \Bitrix\Main\IO\Directory::deleteDirectory(\Bitrix\Main\Application::getDocumentRoot() . $this->getCloudDir());
        } catch (\Throwable $e) {
            // �������
        }
    }
    /**
 * @param $oExportItem
 */
    public function setExportItem($oExportItem)
    {
        $this->oExportItem = $oExportItem;
        $this->log()->setExportId($oExportItem->getId());
    }
    /**
 * ������ ������ ��� ������ � ������� ���������
 * @return \VKapi\Market\Export\Item|null
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
    protected function log()
    {
        if (is_null($this->oLog)) {
            $this->oLog = new \VKapi\Market\Export\Log($this->manager()->getLogLevel());
            $this->oLog->setExportId($this->exportItem()->getId());
        }
        return $this->oLog;
    }
    /**
 * @return \VKapi\Market\Manager
 */
    private function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
 * @param $name
 * @param null $arReplace
 * 
 * @return string
 */
    protected function getMessage($name, $arReplace = null)
    {
        return $this->manager()->getMessage('EXPORT.PHOTO.' . $name, $arReplace);
    }
    /**
 * ������ ������ ��� ������ � ����� �������� ��������
 * 
 * @return \VKapi\Market\Export\PhotoTable
 */
    public function getTable()
    {
        if (is_null($this->oTable)) {
            $this->oTable = new \VKapi\Market\Export\PhotoTable();
        }
        return $this->oTable;
    }
    /**
 * ������ ������ ��� ������ � �������
 * 
 * @return \CFile
 */
    public function getFile()
    {
        if (is_null($this->oFile)) {
            $this->oFile = new \CFile();
        }
        return $this->oFile;
    }
    /**
 * ������ ������ ��� ��������� � ��� ���������
 * 
 * @return \VKapi\Market\Connect
 */
    protected function getConnection()
    {
        return $this->exportItem()->connection();
    }
    /**
 * ������ ��� �� ���������� �������� �������� ��� �������
 * 
 * @return string
 */
    public function getHash()
    {
        if (is_null($this->productHash)) {
            $arValues = [$this->exportItem()->getOfferPhoto(), $this->exportItem()->getOfferMorePhoto(), $this->exportItem()->getProductPhoto(), $this->exportItem()->getProductMorePhoto(), $this->exportItem()->isEnabledExtendedGoods(), $this->exportItem()->isEnabledOfferCombine()];
            $this->productHash = md5(implode('|', $arValues));
        }
        return $this->productHash;
    }
    /**
 * ������ ��� �� ���������� ��������� ������� ����� �� �������� ��������(��������)
 * 
 * @return string
 */
    public function getWatermarkHash()
    {
        if (is_null($this->watermarkHash)) {
            $arValues = [$this->exportItem()->getWatermark(), $this->exportItem()->getWatermarkPosition(), $this->exportItem()->getWatermarkOpacity(), $this->exportItem()->getWatermarkCoefficient()];
            $this->watermarkHash = md5(implode('|', $arValues));
        }
        return $this->watermarkHash;
    }
    /**
 * �������� ���������� ���� ��������, ���� �������� ��������� �������� ��������
 * ��� ������� �����, �� ������ false, ����� true
 * 
 * @param $arPhoto
 * @return bool
 */
    public function isValidHash($arPhoto)
    {
        return $this->getWatermarkHash() == $arPhoto['WM_HASH'] && $this->getHash() == $arPhoto['HASH'];
    }
    /**
 * �������� ������ � ���������� �������� �� ������������� ����� � ������
 * 
 * @param $fileId - ������������� ���������� �����
 * @param $groupId - ������������ ������ � ��, ���� �������� ��� ����
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public function deleteByFileId($fileId, $groupId)
    {
        $this->log()->notice($this->getMessage('DELETE_BY_FILE_ID', ['#FILE_ID#' => $fileId, '#GROUP_ID#' => $groupId]), ['FILE_ID' => $fileId, 'GROUP_ID' => $groupId]);
        $this->getTable()->deleteByFileId($fileId, $groupId);
    }
    /**
 * �������� ������� � ���������� ��������� �� ������������� �������� � �� � ������
 * 
 * @param array $arPhotoId - �������������� �������� � ��
 * @param int $groupId - ������������ ������ � ��, ���� �������� ��� ����
 * @return \Bitrix\Main\DB\Result
 * @throws \Bitrix\Main\Db\SqlQueryException
 */
    public function deleteByPhotoId($arPhotoId, $groupId)
    {
        $this->log()->notice($this->getMessage('DELETE_BY_PHOTO_ID', ['#PHOTO_ID#' => implode(', ', $arPhotoId), '#GROUP_ID#' => $groupId]), ['PHOTO_ID' => $arPhotoId, 'GROUP_ID' => $groupId]);
        $this->getTable()->deleteByPhotoId($arPhotoId, $groupId);
    }
    /**
 * ��������� �������� �������� � ���������, ��������� � ��������� �����,
 * ���������� ������ ���������� � �������
 * { items : [
 * fileId => \VKapi\Market\Result {ID :int, PHOTO_ID:int},
 * ...
 * ]}
 * + ERROR_TIMEOUT
 * 
 * @param $arFileId
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportAlbumPictures($arFileId)
    {
        $result = new \VKapi\Market\Result();
        // ��� �������� ����� ������ ���������
        $arFileId = array_combine($arFileId, $arFileId);
        $arFileIdToResult = [];
        // �������������� �������� � ��, [fileId => Result {ID:int, PHOTO_ID: int}, ...]
        if (\Bitrix\Main\Loader::includeSharewareModule("vk" . "ap" . "i.ma" . "rk" . "et") === constant("MODULE_D" . "EMO_EXP" . "IRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DE" . "M" . "O_EXPIR" . "" . "" . "E" . "D"), "BXMAKER_DEMO_EXPI" . "RED");
        }
        // �������� ������ �������� ������� ��� ���� � ��������� ��� ��������
        $dbrPhoto = $this->getTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'FILE_ID' => array_values($arFileId)]]);
        while ($arPhoto = $dbrPhoto->fetch()) {
            // �������� ����
            if ($this->getWatermarkHash() != $arPhoto['WM_HASH'] || !$arPhoto['PHOTO_ID']) {
                $this->log()->notice($this->getMessage('EXPORT_ALBUM_PICTURES.DELETE_OLD_ROW', ['#FILE_ID#' => $arPhoto['FILE_ID']]), ['FILE_ID' => $arPhoto['FILE_ID']]);
                $this->getTable()->delete($arPhoto['ID']);
            } else {
                // ��������� �� ����������
                unset($arFileId[$arPhoto['FILE_ID']]);
                // ��������� ������� ��� ��������
                $arFileIdToResult[$arPhoto['FILE_ID']] = \VKapi\Market\Result::create(['ID' => $arPhoto['FILE_ID'], 'PHOTO_ID' => $arPhoto['PHOTO_ID']]);
            }
        }
        // �� �� ������������� ����������� ��������� ������������ ��������
        // � ����� ������� ��
        // ���������� �������� � ��
        if (count($arFileId)) {
            $resultAdd = $this->addAlbumPhotoToVk($arFileId);
            if ($resultAdd->isSuccess()) {
                foreach ($resultAdd->getData('items') as $fileId => $fileResult) {
                    $arFileIdToResult[$fileId] = $fileResult;
                }
            } else {
                // ���� ���� ������,������ ��� �����, ������� ���������� ������
                return $result->setError($resultAdd->getFirstError());
            }
        }
        if (\CModule::IncludeModuleEx("v" . "kapi.ma" . "rket") === constant("MODULE_DEM" . "O_EXPIR" . "" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_E" . "XPIRED"), "B" . "XMAKER_DEMO_EXP" . "IRE" . "D");
        }
        // ������������ id ������ �������� � ��
        $result->setData('items', $arFileIdToResult);
        return $result;
    }
    /**
 * �� ������������. �� �� ������������� ����������� ������� �������� ��������
 * ������� �������� �� ��.
 * 
 * @param $arPhotoIdToVkId - ������ �������������� ������� �������� [id => vk_id]
 * @return \VKapi\Market\Result - �������� ���� items [photoId => true/false, ...]
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 * @internal
 * @depricated
 */
    public function deleteAlbumPhotoFromVk($arPhotoIdToVkId)
    {
        $result = new \VKapi\Market\Result();
        if (empty($arPhotoIdToVkId)) {
            return $result;
        }
        $arItems = [];
        // ��������� �� �����
        $arParts = array_chunk($arPhotoIdToVkId, 25, true);
        foreach ($arParts as $arPart) {
            if (empty($arPart)) {
                continue;
            }
            $code = [];
            foreach ($arPart as $photoId => $photoVkId) {
                $code[] = '"p' . $photoId . '" : API.photos.delete({"owner_id" : "-' . $this->exportItem()->getGroupId() . '","photo_id" : ' . $photoVkId . '})';
            }
            $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
            if ($resultRequest->isSuccess()) {
                $response = $resultRequest->getData('response');
                foreach ($arPart as $photoId => $photoVkId) {
                    $arItems[$photoId] = $response['p' . $photoId];
                }
            } else {
                return $result->setError($resultRequest->getFirstError());
            }
        }
        if (\Bitrix\Main\Loader::includeSharewareModule("vk" . "api.market") == constant("MOD" . "UL" . "" . "E_DEMO_EX" . "" . "" . "" . "" . "P" . "IRE" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI." . "MARKET.DEMO_EXPIRED"), "BXMAKER_DEMO_EXPI" . "RE" . "D");
        }
        $result->setData('items', $arItems);
        return $result;
    }
    /**
 * �� ������������. �� �� ���� ����������� ��������� ������������� �������� ��������
 * �������� ������������� �������� � ������ [id => arPhoto]
 * ������ ���������, � �������� �������� �������� [items => [id, ...]]
 * 
 * @param $arPhotos
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 * @internal
 * @depricated
 */
    public function checkExistsAlbumPhotoFromVk($arPhotos)
    {
        $result = new \VKapi\Market\Result();
        if (empty($arPhotos)) {
            return $result;
        }
        $arExists = [];
        // �������������� ��������, ���������� �� ������� �����
        $arIdToPhotoId = [];
        // ������������� �������� � �� => id ��������� ������
        $ownerId = null;
        // ��������� �� ����������
        foreach ($arPhotos as $arPhoto) {
            $arIdToPhotoId[$arPhoto['ID']] = '-' . $arPhoto['GROUP_ID'] . '_' . $arPhoto['PHOTO_ID'];
        }
        // ������ -------
        $resultRequest = $this->exportItem()->connection()->method('photos.getById', ['photos' => implode(',', array_values($arIdToPhotoId)), 'photo_sizes' => 0]);
        if ($resultRequest->isSuccess()) {
            $response = $resultRequest->getData('response');
            // ������������� ������
            $arPhotoIdToId = array_flip($arIdToPhotoId);
            foreach ($response as $arVkPhoto) {
                $arExists[] = $arPhotoIdToId[$arVkPhoto['owner_id'] . '_' . $arVkPhoto['id']];
            }
        } else {
            return $result->setError($resultRequest->getFirstError());
        }
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mark" . "" . "et") === constant("MODULE_DEM" . "O" . "_" . "EXPI" . "RE" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI." . "MARKET.DEMO_E" . "XPIRE" . "D"), "BXMAKER_" . "DE" . "MO_EXPI" . "RE" . "D");
        }
        $result->setData('items', $arExists);
        return $result;
    }
    /**
 * ��������� � �� �������� �������� [fileId, ....]
 * ������ ���������, � �������� ������������ [items => [fileId => vkPhotId, ...]]
 * + ERROR_TIMEOUT
 * 
 * @param $arPhotos
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function addAlbumPhotoToVk($arFileId)
    {
        $result = new \VKapi\Market\Result();
        $arFileIdToVkPhotoAddResult = [];
        // ������������ id ����� => ��������� ���������� ��������
        $arFileIdToPath = [];
        // ������ ������ [fileId=>filePath, ...]
        if (empty($arFileId)) {
            return $result;
        }
        // ������� �������� ����� ������� -----
        $uploadServerUrl = null;
        $requestUploadServer = $this->getAlbumUploadServer();
        if ($requestUploadServer->isSuccess()) {
            $uploadServerUrl = $requestUploadServer->getData('upload_url');
        } else {
            $this->log()->error($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_ALBUM_UPLOAD_SERVER', ['#CODE#' => $requestUploadServer->getFirstErrorCode(), '#MSG#' => $requestUploadServer->getFirstErrorMessage()]), ['ERROR_MORE' => $requestUploadServer->getFirstErrorMore()]);
            return $requestUploadServer;
        }
        if (\CModule::IncludeModuleEx("vkapi." . "ma" . "" . "r" . "ket") === constant("M" . "OD" . "ULE_" . "DEMO_EXP" . "IR" . "" . "E" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEM" . "O_EX" . "" . "" . "PI" . "R" . "E" . "" . "D"), "BXMAKE" . "R_DEM" . "O_EXPI" . "RED");
        }
        // ���������� �������� ��� ������ ������� ------
        $resultPrepireFiles = $this->prepareAlbumFiles($arFileId);
        if ($resultPrepireFiles->isSuccess()) {
            $arFileIdToPathResult = $resultPrepireFiles->getData('items');
            if (empty($arFileIdToPathResult)) {
                return $result;
            }
        } else {
            if (!$resultPrepireFiles->isTimeoutError()) {
                $this->log()->error($resultPrepireFiles->getFirstErrorMessage(), $resultPrepireFiles->getFirstErrorMore());
            }
            return $resultPrepireFiles;
        }
        /**
 * @var \VKapi\Market\Result $filePathResult
 */
        foreach ($arFileIdToPathResult as $fileId => $filePathResult) {
            // �������� ��������
            $this->manager()->checkTime();
            // ���� ��� �� �� ��� � ������, �������� ������ ������
            if (!$filePathResult->isSuccess()) {
                $arFileIdToVkPhotoAddResult[$fileId] = $filePathResult;
                continue;
            }
            try {
                $resultSendFile = $this->exportItem()->connection()->sendFiles($uploadServerUrl, ['file' => $filePathResult->getData('PATH')]);
                if ($resultSendFile->isSuccess()) {
                    // ������� �������� ��� ����������
                    $arPhotoFields = $resultSendFile->getData();
                    if (isset($arPhotoFields['gid'])) {
                        $arPhotoFields['group_id'] = $arPhotoFields['gid'];
                    }
                    // ���������
                    $resultPhotoSave = $this->exportItem()->connection()->method('photos.saveMarketAlbumPhoto', $arPhotoFields);
                    if ($resultPhotoSave->isSuccess()) {
                        $responsePhotoSave = $resultPhotoSave->getData('response');
                        $responsePhotoSave = $responsePhotoSave[0];
                        if (intval($responsePhotoSave['id']) > 0) {
                            $arAddFields = ['FILE_ID' => $fileId, 'GROUP_ID' => $this->exportItem()->getGroupId(), 'PHOTO_ID' => $responsePhotoSave['id'], 'PID' => 0, 'OID' => 0, 'MAIN' => 0, 'HASH' => $this->getHash(), 'WM_HASH' => $this->getWatermarkHash()];
                            // ���������
                            $resultAddAlbumPhoto = $this->getTable()->add($arAddFields);
                            if ($resultAddAlbumPhoto->isSuccess()) {
                                // ���� ��� ��, �������� ������������ �������� ��
                                $fileAddResult = new \VKapi\Market\Result();
                                $fileAddResult->setData('PHOTO_ID', $responsePhotoSave['id']);
                                $fileAddResult->setData('ID', $resultAddAlbumPhoto->getId());
                                $arFileIdToVkPhotoAddResult[$fileId] = $fileAddResult;
                                $this->log()->ok($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.SAVE_ALBUM_FILE_OK', ['#FILE_ID#' => $fileId, '#PHOTO_ID#' => $responsePhotoSave['id']]), ['FILE_ID' => $fileId, 'PHOTO_ID' => $responsePhotoSave['id']]);
                            } else {
                                $errors = $resultAddAlbumPhoto->getErrorCollection()->toArray();
                                $errorAddAlbumPhotoTable = reset($errors);
                                /**
 * @var $errorAddAlbumPhotoTable \Bitrix\Main\Error
 */
                                $arFileIdToVkPhotoAddResult[$fileId] = new \VKapi\Market\Result(new \VKapi\Market\Error($errorAddAlbumPhotoTable->getMessage(), $errorAddAlbumPhotoTable->getCode()));
                                $this->log()->error($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_LOCAL_SAVE_ROW_ALBUM_FILE', ['#FILE_ID#' => $fileId, '#CODE#' => $errorAddAlbumPhotoTable->getCode(), '#MSG#' => $errorAddAlbumPhotoTable->getMessage()]), ['FILE_ID' => $fileId, 'FIELDS' => $arAddFields]);
                            }
                        } else {
                            $arFileIdToVkPhotoAddResult[$fileId] = new \VKapi\Market\Result(new \VKapi\Market\Error($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_SAVE_ALBUM_PHOTO_EMPTY_ID', ['#FILE_ID#' => $fileId])));
                            $this->log()->error($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_SAVE_ALBUM_PHOTO_EMPTY_ID', ['#FILE_ID#' => $fileId]), ['FILE_ID' => $fileId, 'RESPONSE' => $resultPhotoSave]);
                        }
                    } else {
                        $arFileIdToVkPhotoAddResult[$fileId] = $resultPhotoSave;
                        $this->log()->notice($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_SAVE_ALBUM_FILE', ['#FILE_ID#' => $fileId, '#CODE#' => $resultPhotoSave->getFirstErrorCode(), '#MSG#' => $resultPhotoSave->getFirstErrorMessage()]), ['FILE_ID' => $fileId, 'ERROR_MORE' => $resultPhotoSave->getFirstErrorMore()]);
                    }
                } else {
                    $arFileIdToVkPhotoAddResult[$fileId] = $resultSendFile;
                    $this->log()->notice($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_SEND_ALBUM_FILE', ['#FILE_ID#' => $fileId, '#CODE#' => $resultSendFile->getFirstErrorCode(), '#MSG#' => $resultSendFile->getFirstErrorMessage()]), ['FILE_ID' => $fileId, 'ERROR_MORE' => $resultSendFile->getFirstErrorMore()]);
                }
            } catch (\VKapi\Market\Exception\UnknownResponseException $ex) {
                $resultSendFile = new \VKapi\Market\Result();
                $resultSendFile->addError($ex->getMessage(), $ex->getCustomCode());
                $arFileIdToVkPhotoAddResult[$fileId] = $resultSendFile;
                $this->log()->notice($this->getMessage('ADD_ALBUM_PHOTO_TO_VK.ERROR_SEND_ALBUM_FILE', ['#FILE_ID#' => $fileId, '#CODE#' => $ex->getCustomCode(), '#MSG#' => $ex->getMessage()]), ['FILE_ID' => $fileId, 'ERROR_MORE' => []]);
            }
        }
        $result->setData('items', $arFileIdToVkPhotoAddResult);
        return $result;
    }
    /**
 * ������ ����� ������� ��� �������� �������� {upload_url: string}
 * 
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function getAlbumUploadServer()
    {
        $result = new \VKapi\Market\Result();
        // �������� ����� ������� ��� ��������
        $requestResult = $this->exportItem()->connection()->method('photos.getMarketAlbumUploadServer', ['group_id' => $this->exportItem()->getGroupId()]);
        if ($requestResult->isSuccess()) {
            $response = $requestResult->getData('response');
            if (isset($response['upload_url'])) {
                $result->setData('upload_url', $response['upload_url']);
            }
        } else {
            return $requestResult;
        }
        return $result;
    }
    /**
 * ���������� �����, ��������������� ���� ���� ����������
 * � ������ ������ {items => [fileId => Result(), ...]]}
 * 
 * @param $arFileId
 * @return \VKapi\Market\Result - {PATH: string ���������� ����, ID :int}
 */
    public function prepareAlbumFiles($arFileId)
    {
        $result = new \VKapi\Market\Result();
        $arFilePath = [];
        // ������ ���������� ����� ��� ������
        foreach ($arFileId as $fileId) {
            $fileResult = new \VKapi\Market\Result();
            $fileResult->setData('ID', $fileId);
            // �������� ��������
            $this->manager()->checkTime();
            do {
                // �������� �������� �����
                $arFile = $this->getFile()->GetFileArray(intval($fileId));
                if (!$arFile) {
                    $fileResult->addError($this->getMessage('PREPIRE_ALBUM_FILES.NOT_FOUND', ['#FILE_ID#' => $fileId]), 'FILE_NOT_FOUND', ['FILE_ID' => $fileId]);
                    break;
                }
                // ��������� ��� �����
                if (!preg_match('/\\.jpe?g$|\\.png$|\\.gif|\\.webp$/i', $arFile['SRC'], $match)) {
                    $fileResult->addError($this->getMessage('PREPIRE_ALBUM_FILES.ERROR_FILE_FORMAT', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC']]), 'FILE_FORMAT', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC']]);
                    break;
                }
                // ��������� �� ������ ���� ����
                $this->downloadFileFromCloud($arFile);
                // ��������� ������������ ������������� �����
                // ���� �� � ������, �� ���������� �������� --
                if (!file_exists(\Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC'])) {
                    $fileResult->addError($this->getMessage('PREPIRE_ALBUM_FILES.NOT_FOUND_ON_DISK', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC']]), 'FILE_NOT_FOUND', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC']]);
                    break;
                }
                // ������������� �� webp
                $this->convertFromWebp($arFile);
                // ����������� �������
                $this->restoreRealFileSizes($arFile);
                // �������� ������� � ��������� ��������
                $this->prepareCanvas($arFile, 1280, 720);
                $fileResult->setData('PATH', \Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC']);
            } while (false);
            $arFilePath[$arFile['ID']] = $fileResult;
        }
        $result->setData('items', $arFilePath);
        return $result;
    }
    /**
 * ��������� �� ������ ��� ���������� � �������� � ��
 * 
 * @param $arFile
 * @return bool
 */
    public function downloadFileFromCloud(&$arFile)
    {
        if (!intval($arFile['HANDLER_ID']) || mb_substr($arFile['SRC'], 0, 4) != 'http') {
            return false;
        }
        $dir = $this->getCloudDir(true);
        $root = \Bitrix\Main\Application::getDocumentRoot();
        $arFilenameParts = explode('.', $arFile['SRC']);
        $filename = $arFile['ID'] . '.' . end($arFilenameParts);
        \Bitrix\Main\IO\Directory::createDirectory($root . $dir);
        // �������� �� ����������� �� �����
        if (!file_exists($root . $dir . $filename)) {
            $oHttpClient = new \Bitrix\Main\Web\HttpClient();
            $oHttpClient->download($arFile['SRC'], $root . $dir . $filename);
            // ������ ��� � ������ ������������ �����, ������� �������� ������
            if ($oHttpClient->getStatus() == 200) {
                $arFile['SRC'] = $dir . $filename;
            }
        } else {
            $arFile['SRC'] = $dir . $filename;
        }
    }
    /**
 * ���������� �������� � ���������
 * @param $arFile - �������� �����
 */
    public function prepareToSquare(&$arFile)
    {
        $width = max($arFile['WIDTH'], $arFile['HEIGHT']);
        if ($arFile['WIDTH'] == $arFile['HEIGHT']) {
            return true;
        }
        $this->prepareCanvas($arFile, $width, $width, true);
    }
    /**
 * ��������� �������� ��� ��������, ���� ���� ������ ��������� �������
 * 
 * @param $arFile - �������� �����
 * @param int $minWidth - ����������� ������
 * @param int $minHeight - ����������� ������
 * @param bool $bEqual - ���� �������� ������ ���� ���������� *
 */
    public function prepareCanvas(&$arFile, $minWidth = 400, $minHeight = 400, $bEqual = false)
    {
        $root = \Bitrix\Main\Application::getDocumentRoot();
        $dir = $this->getCanvasDir() . '/m' . $minWidth . 'x' . $minHeight . '/';
        \Bitrix\Main\IO\Directory::createDirectory($root . $dir);
        // �������� ����
        $arFile['SOURCE'] = $arFile['SRC'];
        $arFilenameParts = explode('.', $arFile['SRC']);
        $filename = $arFile['ID'] . '.' . end($arFilenameParts);
        // �������� �������
        $destWidth = max($arFile['WIDTH'], $minWidth);
        $destHeight = max($arFile['HEIGHT'], $minHeight);
        // ���� ����� �������
        if ($bEqual) {
            $destWidth = $destHeight = min(max($destWidth, $destHeight), 7000);
        }
        // ���� ����� ������ ������ �����������
        $destSum = $destWidth + $destHeight;
        if ($destSum > 14000) {
            $k = 14000 / $destSum;
            $destWidth = $destWidth * $k;
            $destHeight = $destHeight * $k;
        }
        try {
            // ���� �������� �������� ������ ��� ����� - ������������
            $this->prepareMaxSize($arFile, $destWidth, $destHeight);
            // ������� �������
            $sourceWidth = $arFile['WIDTH'];
            $sourceHeight = $arFile['HEIGHT'];
            // ���� �������� ������������� ����������� ��������
            if ($sourceWidth < $destWidth || $sourceHeight < $destHeight) {
                // ���� ���� ����� �� ����������, ��������
                if (!file_exists($root . $dir . $filename)) {
                    // ������� �����������
                    if (function_exists('imagecreatetruecolor')) {
                        $canvas = \imagecreatetruecolor($destWidth, $destHeight);
                    } else {
                        $canvas = \ImageCreate($destWidth, $destHeight);
                    }
                    // �������� �����
                    $white = \imagecolorallocate($canvas, 255, 255, 255);
                    imagefill($canvas, 0, 0, $white);
                    // �������� ���������� ��������
                    if ($arFile['CONTENT_TYPE'] == 'image/gif') {
                        $picture = \Imagecreatefromgif($root . $arFile['SRC']);
                    } else {
                        if ($arFile['CONTENT_TYPE'] == 'image/png') {
                            $picture = \Imagecreatefrompng($root . $arFile['SRC']);
                        } else {
                            $picture = \Imagecreatefromjpeg($root . $arFile['SRC']);
                        }
                    }
                    // ����������� �������� �� �����
                    $x = (int) (($destWidth - $sourceWidth) / 2);
                    $y = (int) (($destHeight - $sourceHeight) / 2);
                    if (function_exists('imagecopyresampled')) {
                        \ImageCopyResampled($canvas, $picture, $x, $y, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);
                    } else {
                        \ImageCopyResized($canvas, $picture, $x, $y, 0, 0, $sourceWidth, $sourceHeight, $sourceWidth, $sourceHeight);
                    }
                    // ���������
                    \imagejpeg($canvas, $root . $dir . $filename, 100);
                }
                // ����� ����
                $arFile['SRC'] = $dir . $filename;
                // ����� �������
                $arFile['WIDTH'] = $destWidth;
                $arFile['HEIGHT'] = $destHeight;
                unset($canvas, $x, $y, $picture, $type, $white, $arrImgInfo, $sourceWidth, $sourceHeight, $root, $bEqual);
            }
        } catch (\Throwable $e) {
            // ������
            $this->log()->notice($e->getMessage(), ['arFile' => [$arFile['ID'], $arFile['SRC']], 'EXCEPTION' => [$e->getMessage(), $e->getTraceAsString()]]);
        }
    }
    /**
 * ���� ������ ���������� ������� �����, ������� ����� ����� � ������� ������ ����
 * �������� ���� �� ����� �� ���� �� ����� � ������ ������
 * 
 * @param $arFile
 * @return bool
 */
    public function prepareWatermark(&$arFile)
    {
        $arWatermark = $this->getWatermarkParams();
        // ���� ������ ������� ��������� ������� ����� --
        if ($arWatermark && file_exists($arWatermark['SRC'])) {
            $root = \Bitrix\Main\Application::getDocumentRoot();
            $dir = $this->getWatermarkDir() . DIRECTORY_SEPARATOR;
            $arFilenameParts = explode('.', $arFile['SRC']);
            $filename = $arFile['ID'] . '.' . end($arFilenameParts);
            $sourceFilePath = $root . $arFile['SRC'];
            $distFilePath = $root . $dir . $filename;
            if (file_exists($distFilePath . '.hash') && file_get_contents($distFilePath . '.hash') == $arWatermark['HASH']) {
                $arFile['SRC'] = $dir . $filename;
            } else {
                $arImageFilter = ["name" => "watermark", "position" => $arWatermark['POSITION'], "size" => "real", 'type' => 'image', 'alpha_level' => $arWatermark['OPACITY'], 'file' => $arWatermark['SRC']];
                // �������
                if ($arWatermark['POSITION'] == 'FILL') {
                    $arImageFilter['position'] = 'tl';
                    $arImageFilter['fill'] = 'repeat';
                } else {
                    // ���������
                    $arImageFilter['size'] = 'big';
                    $arImageFilter['fill'] = 'resize';
                    $arImageFilter['coefficient'] = $arWatermark['COEFFICIENT'];
                }
                \Bitrix\Main\IO\Directory::createDirectory($root . $dir);
                @unlink($distFilePath);
                $this->getFile()->ResizeImageFile($sourceFilePath, $distFilePath, [], BX_RESIZE_IMAGE_PROPORTIONAL_ALT, $arImageFilter, 100);
                $arFile['SRC'] = $dir . $filename;
                // ��������� ���
                file_put_contents($distFilePath . '.hash', $arWatermark['HASH']);
                unset($arImageFilter);
            }
            unset($sourceFilePath, $distFilePath);
        }
        unset($arWatermark, $dir, $filename);
    }
    /**
 * ������ ������ ����������� ��������� ������� �����
 * {SRC:string? POSITION:int, OPACITY:int, COEFFICIENT:int, HASH:md5}
 * 
 * @return array|false
 */
    public function getWatermarkParams()
    {
        if (is_null($this->watermarkParams)) {
            $this->watermarkParams = false;
            if ($this->exportItem()->getWatermark()) {
                $arPositionKey = ['TL', 'TC', 'TR', 'ML', 'MC', 'MR', 'BL', 'BC', 'BR'];
                $position = in_array($this->exportItem()->getWatermarkPosition(), $arPositionKey) ? strtolower($this->exportItem()->getWatermarkPosition()) : 'mc';
                $arFile = $this->getFile()->GetFileArray($this->exportItem()->getWatermark());
                // ����������� �������
                $this->restoreRealFileSizes($arFile);
                $this->watermarkParams = ['SRC' => \Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC'], 'POSITION' => $position, 'OPACITY' => abs(100 - $this->exportItem()->getWatermarkOpacity()), 'COEFFICIENT' => $this->exportItem()->getWatermarkCoefficient()];
                $this->watermarkParams['HASH'] = md5(serialize($this->watermarkParams));
            }
        }
        return $this->watermarkParams;
    }
    /**
 * �������� �������� �������� �� ��������������� ��������
 * ������ ������ ������������
 * 
 * @param int[] $arFileId
 * @param int $groupId - ��� ���������� �� �������
 * @return array - [fileId => arPhotoItem, ...]
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function getItemsByFileId(array $arFileId, $groupId = null)
    {
        $arReturn = [];
        $arFileId = array_diff(array_map('intval', $arFileId), [0]);
        if (empty($arFileId)) {
            return $arReturn;
        }
        $arFilter = ['FILE_ID' => $arFileId];
        if (!is_null($groupId)) {
            $arFilter['GROUP_ID'] = $groupId;
        }
        $dbr = $this->getTable()->getList(['filter' => $arFilter]);
        while ($ar = $dbr->fetch()) {
            $arReturn[$ar['FILE_ID']] = $ar;
        }
        return $arReturn;
    }
    /**
 * ��������� ������� �������� ������� � ���������, ��������� � ��������� �����,
 * ���������� ������ ���������� � �������
 * { items : [
 * fileId => \VKapi\Market\Result {FILE_ID :int, PHOTO_ID:int},
 * ...
 * ]}
 * 
 * +ERROR_TIMEOUT
 * @param int[] $arFileId - ������ �������������� ������ ��������
 * @param bool $mainPhoto - ������ � ��������� ���������� ������
 * @param int $productId - ������������� �������� ��� �������� �������� � ������
 * @param int $offerId - ������������� ��������� �����������
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportProductPictures($arFileId, $mainPhoto = false, $productId = 0, $offerId = 0)
    {
        $result = new \VKapi\Market\Result();
        $arFileIdToResult = [];
        // �������������� �������� � ��, [fileId => Result {ID:int, PHOTO_ID: int}, ...]
        if (empty($arFileId)) {
            return $result->setData('items', $arFileIdToResult);
        }
        // ��� �������� ����� ����� ���������
        $arFileId = array_combine($arFileId, $arFileId);
        $arFilter = ['GROUP_ID' => $this->exportItem()->getGroupId(), 'FILE_ID' => array_values($arFileId), 'PID' => $productId, 'OID' => $offerId, 'MAIN' => 0];
        if ($mainPhoto) {
            $arFilter['MAIN'] = 1;
        }
        // ���� ��������� ���������� ��������, �� ��������� � ������� �������
        // � ��������� ������������ ���������� ������
        if ($this->manager()->isDisabledUpdatePicture()) {
            $arRestoreFilter = ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PID' => $productId, 'OID' => $offerId, 'MAIN' => 0];
            if ($mainPhoto) {
                $arRestoreFilter['MAIN'] = 1;
            }
            $bFind = false;
            $dbrPhoto = $this->getTable()->getList(['filter' => $arRestoreFilter]);
            while ($arPhoto = $dbrPhoto->fetch()) {
                $bFind = true;
                // ��������� ������� ��� ��������
                $arFileIdToResult[$arPhoto['FILE_ID']] = \VKapi\Market\Result::create(['ID' => $arPhoto['ID'], 'FILE_ID' => $arPhoto['FILE_ID'], 'PHOTO_ID' => $arPhoto['PHOTO_ID']]);
            }
            if ($bFind) {
                // ������������ id ������ �������� � ��
                $result->setData('items', $arFileIdToResult);
                return $result;
            }
        }
        if (\CModule::IncludeModuleEx("v" . "ka" . "pi." . "ma" . "r" . "ke" . "t") === constant("MODULE_DEM" . "O_" . "E" . "XPIR" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAP" . "I.MARKET.D" . "EMO_EX" . "" . "" . "PIRE" . "" . "D"), "B" . "XMAKER_DEMO_E" . "XPI" . "RE" . "D");
        }
        // �������� ������ �������� ������� ��� ���� � ��������� ��� ��������
        $dbrPhoto = $this->getTable()->getList(['filter' => $arFilter]);
        while ($arPhoto = $dbrPhoto->fetch()) {
            // �������� ����
            if ($this->getWatermarkHash() != $arPhoto['WM_HASH'] || !$arPhoto['PHOTO_ID']) {
                $this->log()->notice($this->getMessage('EXPORT_PRODUCT_PICTURES.ERROR_LOCAL_SAVE_ROW_ALBUM_FILE', ['#FILE_ID#' => $arPhoto['FILE_ID']]), ['FILE_ID' => $arPhoto['FILE_ID']]);
                $this->getTable()->delete($arPhoto['ID']);
            } else {
                // ��������� �� ����������
                unset($arFileId[$arPhoto['FILE_ID']]);
                // ��������� ������� ��� ��������
                $arFileIdToResult[$arPhoto['FILE_ID']] = \VKapi\Market\Result::create(['ID' => $arPhoto['ID'], 'FILE_ID' => $arPhoto['FILE_ID'], 'PHOTO_ID' => $arPhoto['PHOTO_ID']]);
            }
        }
        // �� �� ������������� ����������� ��������� ������������ ��������
        // � ����� ������� ��
        // ���������� �������� � ��
        if (count($arFileId)) {
            $resultAdd = $this->addProductPhotoToVk($arFileId, $mainPhoto, $productId, $offerId);
            if ($resultAdd->isSuccess()) {
                $resultAddItems = $resultAdd->getData('items');
                foreach ($resultAddItems as $fileId => $fileResult) {
                    $arFileIdToResult[$fileId] = $fileResult;
                }
            } else {
                // ���� ���� ������,������ ��� �����, ������� ���������� ������
                return $result->setError($resultAdd->getFirstError());
            }
        }
        // ������������ id ������ �������� � ��
        $result->setData('items', $arFileIdToResult);
        return $result;
    }
    /**
 * ��������� � �� �������� [fileId, ....]
 * ������ ���������, � �������� ������������ [items => [fileId => Result{ID:int, FILE_ID:int,
 * PHOTO_ID(VK_ID):int}, ...]]
 * + ERROR_TIMEOUT
 * 
 * @param $arPhotos
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function addProductPhotoToVk($arFileId, $mainPhoto = false, $productId = 0, $offerId = 0)
    {
        $result = new \VKapi\Market\Result();
        $arFileIdToVkPhotoAddResult = [];
        // ������������ id ����� => ��������� ���������� ��������
        $arFileIdToPathResult = [];
        // ������ ������ [fileId=>filePath, ...]
        $result->setData('items', $arFileIdToVkPhotoAddResult);
        if (empty($arFileId)) {
            return $result;
        }
        // ������� �������� ����� ������� -----
        $uploadServerUrl = null;
        $requestUploadServer = $this->getProductUploadServer($mainPhoto);
        if ($requestUploadServer->isSuccess()) {
            $uploadServerUrl = $requestUploadServer->getData('upload_url');
        } else {
            $this->log()->error($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.ERROR_UPLOAD_SERVER', ['#CODE#' => $requestUploadServer->getFirstErrorCode(), '#MSG#' => $requestUploadServer->getFirstErrorMessage()]), ['ERROR_MORE' => $requestUploadServer->getFirstErrorMore()]);
            return $requestUploadServer;
        }
        // ���������� �������� ��� ������ ������� ------
        $resultPrepireFiles = $this->prepareProductFiles($arFileId);
        if ($resultPrepireFiles->isSuccess()) {
            $arFileIdToPathResult = $resultPrepireFiles->getData('items');
        } else {
            if (!$resultPrepireFiles->isTimeoutError()) {
                $this->log()->error($resultPrepireFiles->getFirstErrorMessage(), $resultPrepireFiles->getFirstErrorMore());
            }
            return $resultPrepireFiles;
        }
        // ���������� � ��������
        foreach ($arFileIdToPathResult as $fileId => $filePathResult) {
            if (!$filePathResult->isSuccess()) {
                $arFileIdToVkPhotoAddResult[$fileId] = $filePathResult;
                unset($arFileIdToPathResult[$fileId]);
            }
        }
        if (empty($arFileIdToPathResult)) {
            return $result;
        }
        // ��������� �� ����� �� 5������
        $arParts = array_chunk($arFileIdToPathResult, 5, true);
        foreach ($arParts as $arPart) {
            if (empty($arPart)) {
                continue;
            }
            // �������� ��������
            $this->manager()->checkTime();
            // ����� ��� ������, [0 => fileId, ...]
            $arKey2FileId = array_keys($arPart);
            $arSendFileSrc = [];
            $iFile = 1;
            /**
 * @var \VKapi\Market\Result $filePathResult
 */
            foreach ($arPart as $fileId => $filePathResult) {
                $arSendFileSrc['file' . $iFile++] = $filePathResult->getData('PATH');
            }
            try {
                $resultSendFile = $this->exportItem()->connection()->sendFiles($uploadServerUrl, $arSendFileSrc);
                if ($resultSendFile->isSuccess()) {
                    // ������� �������� ��� ����������
                    $arPhotoFields = $resultSendFile->getData();
                    $arPhotoFields['group_id'] = $this->exportItem()->getGroupId();
                    // ���������
                    $resultPhotoSave = $this->exportItem()->connection()->method('photos.saveMarketPhoto', $arPhotoFields);
                    if ($resultPhotoSave->isSuccess()) {
                        $responsePhotoSave = $resultPhotoSave->getData('response');
                        foreach ($responsePhotoSave as $fileKey => $fileSaveResponse) {
                            $fileId = $arKey2FileId[$fileKey];
                            $arAddFields = ['FILE_ID' => $fileId, 'GROUP_ID' => $this->exportItem()->getGroupId(), 'PHOTO_ID' => $fileSaveResponse['id'], 'PID' => $productId, 'OID' => $offerId, 'MAIN' => $mainPhoto ? 1 : 0, 'HASH' => $this->getHash(), 'WM_HASH' => $this->getWatermarkHash()];
                            // ���������
                            $resultAddPhoto = $this->getTable()->add($arAddFields);
                            if ($resultAddPhoto->isSuccess()) {
                                // ���� ��� ��, �������� ������������ �������� ��
                                $fileAddResult = new \VKapi\Market\Result();
                                $fileAddResult->setData('PHOTO_ID', $fileSaveResponse['id']);
                                $fileAddResult->setData('FILE_ID', $fileId);
                                $fileAddResult->setData('ID', $resultAddPhoto->getId());
                                $arFileIdToVkPhotoAddResult[$fileId] = $fileAddResult;
                                $this->log()->ok($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.SAVE_ALBUM_FILE_OK', ['#FILE_ID#' => $fileId, '#PHOTO_ID#' => $fileSaveResponse['id']]));
                            } else {
                                $errorAddAlbumPhotoTableList = $resultAddPhoto->getErrorCollection()->toArray();
                                $errorAddAlbumPhotoTable = reset($errorAddAlbumPhotoTableList);
                                /**
 * @var $errorAddAlbumPhotoTable \Bitrix\Main\Error
 */
                                $arFileIdToVkPhotoAddResult[$fileId] = new \VKapi\Market\Result(new \VKapi\Market\Error($errorAddAlbumPhotoTable->getMessage(), $errorAddAlbumPhotoTable->getCode()));
                                $this->log()->notice($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.ERROR_LOCAL_SAVE_ROW_ALBUM_FILE', ['#FILE_ID#' => $fileId, '#CODE#' => $errorAddAlbumPhotoTable->getFirstErrorCode(), '#MSG#' => $errorAddAlbumPhotoTable->getFirstErrorMessage()]), ['FILE_ID' => $fileId, 'FIELDS' => $arAddFields]);
                            }
                        }
                    } else {
                        foreach ($arKey2FileId as $fileId) {
                            $arFileIdToVkPhotoAddResult[$fileId] = $resultPhotoSave;
                        }
                        $this->log()->notice($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.ERROR_SAVE_FILE', ['#FILE_ID#' => implode(', ', $arKey2FileId), '#CODE#' => $resultPhotoSave->getFirstErrorCode(), '#MSG#' => $resultPhotoSave->getFirstErrorMessage()]), ['FILE_ID' => $arKey2FileId, 'ERROR_MORE' => $resultPhotoSave->getFirstErrorMore()]);
                    }
                } else {
                    foreach ($arKey2FileId as $fileId) {
                        $arFileIdToVkPhotoAddResult[$fileId] = $resultSendFile;
                    }
                    $this->log()->notice($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.ERROR_SEND_FILE', ['#FILE_ID#' => implode(', ', $arKey2FileId), '#CODE#' => $resultSendFile->getFirstErrorCode(), '#MSG#' => $resultSendFile->getFirstErrorMessage()]), ['FILE_ID' => $arKey2FileId, 'ERROR_MORE' => $resultSendFile->getFirstErrorMore()]);
                }
            } catch (\VKapi\Market\Exception\UnknownResponseException $ex) {
                $resultSendFile = new \VKapi\Market\Result();
                $resultSendFile->addError($ex->getMessage(), $ex->getCustomCode());
                foreach ($arKey2FileId as $fileId) {
                    $arFileIdToVkPhotoAddResult[$fileId] = $resultSendFile;
                }
                $this->log()->notice($this->getMessage('ADD_PRODUCT_PHOTO_TO_VK.ERROR_SEND_FILE', ['#FILE_ID#' => implode(', ', $arKey2FileId), '#CODE#' => $ex->getCustomCode(), '#MSG#' => $ex->getMessage()]), ['FILE_ID' => $arKey2FileId, 'ERROR_MORE' => []]);
            }
        }
        $result->setData('items', $arFileIdToVkPhotoAddResult);
        return $result;
    }
    /**
 * ������ ����� ������� ��� �������� �������� {upload_url: string}
 * 
 * @param bool $mainPhoto - ���������� �� ������� ����� ��� �������� ������� �������� ������
 * @return \VKapi\Market\Result
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function getProductUploadServer($mainPhoto = false)
    {
        $result = new \VKapi\Market\Result();
        $arParams = ['group_id' => $this->exportItem()->getGroupId()];
        if ($mainPhoto) {
            $arParams['main_photo'] = 1;
        }
        // �������� ����� ������� ��� ��������
        $requestResult = $this->exportItem()->connection()->method('photos.getMarketUploadServer', $arParams);
        if ($requestResult->isSuccess()) {
            $response = $requestResult->getData('response');
            if (isset($response['upload_url'])) {
                $result->setData('upload_url', $response['upload_url']);
            }
        } else {
            return $requestResult;
        }
        return $result;
    }
    /**
 * ���������� �����, ��������������� ���� ���� ����������
 * � ������ ������ {items => [fileId => absFilePath, ...]]}
 * 
 * + ERROR_TIMEOUT
 * @param int[] $arFileId - id ������ ��� ����������
 * @return \VKapi\Market\Result
 */
    public function prepareProductFiles($arFileId)
    {
        $result = new \VKapi\Market\Result();
        $arFilePath = [];
        // ������ ���������� ����� ��� ������
        foreach ($arFileId as $fileId) {
            // �������� ��������
            $this->manager()->checkTime();
            $filePathResult = new \VKapi\Market\Result();
            $filePathResult->setData('ID', $fileId);
            do {
                // �������� �������� �����
                $arFile = $this->getFile()->GetFileArray(intval($fileId));
                if (!$arFile) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.FILE_NOT_FOUND', ['#FILE_ID#' => $fileId]), 'FILE_NOT_FOUND', ['FILE_ID' => $fileId]);
                    break;
                }
                // ��������� ��� �����
                if (!preg_match('/\\.jpe?g$|\\.png$|\\.gif|\\.webp$/i', $arFile['SRC'], $match)) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.ERROR_FILE_FORMAT', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC']]), 'FILE_FORMAT', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC']]);
                    break;
                }
                // �������� ����������� �� �������� ��������
                if ($arFile['WIDTH'] + $arFile['HEIGHT'] > 14000) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.ERROR_FILE_MAX_SIZE', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC'], '#SIZE#' => $arFile['WIDTH'] + $arFile['HEIGHT']]), 'FILE_MAX_SIZE', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC'], 'SIZE' => $arFile['WIDTH'] + $arFile['HEIGHT']]);
                    break;
                }
                // ��������� �� ������ ���� ����
                $this->downloadFileFromCloud($arFile);
                // ��������� ������������ ������������� �����
                if (!file_exists(\Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC'])) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.NOT_FOUND_ON_DISK', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC']]), 'FILE_NOT_FOUND_ON_DISC', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC']]);
                    break;
                }
                // ����������� �������
                $this->restoreRealFileSizes($arFile);
                // ��� ���� ������ ���
                // / //�������� ����������� �� �������� ��������
                if ($arFile['WIDTH'] + $arFile['HEIGHT'] > 14000) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.ERROR_FILE_MAX_SIZE', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC'], '#SIZE#' => $arFile['WIDTH'] + $arFile['HEIGHT']]), 'FILE_MAX_SIZE', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC'], 'SIZE' => $arFile['WIDTH'] + $arFile['HEIGHT']]);
                    break;
                }
                // ������������� �� webp
                $this->convertFromWebp($arFile);
                // ���������� �������� ������ � ��������
                if ($this->exportItem()->isEnabledImageToSquare()) {
                    $this->prepareToSquare($arFile);
                }
                // �������� ������� � ��������� ��������
                $this->prepareCanvas($arFile, 400, 400);
                // ���������� ������� �����
                $this->prepareWatermark($arFile);
                // �������� ������� ����� �� ������
                if (filesize(\Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC']) > 50 * 1024 * 1024 * 8) {
                    $filePathResult->addError($this->getMessage('PREPIRE_PRODUCT_FILES.ERROR_FILESIZE', ['#FILE_ID#' => $fileId, '#FILE_SRC#' => $arFile['SRC']]), 'FILESIZE', ['FILE_ID' => $fileId, 'FILE_SRC' => $arFile['SRC']]);
                    break;
                }
                $filePathResult->setData('PATH', \Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC']);
                $filePathResult->setData('SRC', $arFile['SRC']);
            } while (false);
            $arFilePath[$fileId] = $filePathResult;
        }
        $result->setData('items', $arFilePath);
        return $result;
    }
    /**
 * ������ ���������� ��� �������� � ������� �������
 * 
 * @return string
 */
    public function getWatermarkDir($bSlash = false)
    {
        if ($this->isModePreview()) {
            return '/upload/vkapi.market/preview/watermark' . ($bSlash ? '/' : '');
        }
        return '/upload/vkapi.market/watermark' . ($bSlash ? '/' : '');
    }
    /**
 * ������ ���������� ��� �������� � ����������
 * 
 * @return string
 */
    public function getCanvasDir($bSlash = false)
    {
        if ($this->isModePreview()) {
            return '/upload/vkapi.market/preview/canvas' . ($bSlash ? '/' : '');
        }
        return '/upload/vkapi.market/canvas' . ($bSlash ? '/' : '');
    }
    /**
 * ������ ���������� ��� �������� � ������
 * 
 * @return string
 */
    public function getCloudDir($bSlash = false)
    {
        if ($this->isModePreview()) {
            return '/upload/vkapi.market/preview/cloud' . ($bSlash ? '/' : '');
        }
        return '/upload/vkapi.market/cloud' . ($bSlash ? '/' : '');
    }
    /**
 * ������ ������ ������� ������� �����, {id : name}
 * 
 * @return array
 */
    public function getWatermarkPositionList()
    {
        return ['FILL' => $this->getMessage('WATERMARK_POSITION_FILL'), 'TL' => $this->getMessage('WATERMARK_POSITION_TL'), 'TC' => $this->getMessage('WATERMARK_POSITION_TC'), 'TR' => $this->getMessage('WATERMARK_POSITION_TR'), 'ML' => $this->getMessage('WATERMARK_POSITION_ML'), 'MC' => $this->getMessage('WATERMARK_POSITION_MC'), 'MR' => $this->getMessage('WATERMARK_POSITION_MR'), 'BL' => $this->getMessage('WATERMARK_POSITION_BL'), 'BC' => $this->getMessage('WATERMARK_POSITION_BC'), 'BR' => $this->getMessage('WATERMARK_POSITION_BR')];
    }
    /**
 * ������ ������ ����������� ����� ������� ����� ��� ������������� � SelectBoxFromArray
 * 
 * @return array
 */
    public function getWatermarkPositionSelectList()
    {
        $arList = $this->getWatermarkPositionList();
        return ['REFERENCE_ID' => array_keys($arList), 'REFERENCE' => array_values($arList)];
    }
    /**
 * ������ ������ ��������� ������������ ������� �����, {id : name}
 * 
 * @return array
 */
    public function getWatermarkOpacityList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arReturn = [];
            for ($i = 0; $i <= 100; $i += 2) {
                $arReturn[$i] = $i . '%';
            }
        }
        return $arReturn;
    }
    /**
 * ������� ������ ��������� ������������ ������� ����� ��� �������������� � SelectBoxFromArray
 * 
 * @return array
 */
    public function getWatermarkOpacitySelectList()
    {
        $arList = $this->getWatermarkOpacityList();
        return ['REFERENCE_ID' => array_keys($arList), 'REFERENCE' => array_values($arList)];
    }
    /**
 * ������ ������ ��������� ������������ ��������������� ������� �����, {id : name}
 * 
 * @return array
 */
    public function getWatermarkKoefficientList()
    {
        static $arReturn;
        if (!isset($arReturn)) {
            $arKeys = ['1', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1'];
            $arReturn = array_combine($arKeys, $arKeys);
        }
        return $arReturn;
    }
    /**
 * ������� ������ ��������� ������������ ��������������� ������� ����� ��� �������������� � SelectBoxFromArray
 * 
 * @return array
 */
    public function getWatermarkKoefficientSelectList()
    {
        $arList = $this->getWatermarkKoefficientList();
        return ['REFERENCE_ID' => array_keys($arList), 'REFERENCE' => array_values($arList)];
    }
    /**
 * ������������ �������� ������� �����, ������ ����������� � ����
 * @param $arFile
 * @return void
 */
    public function restoreRealFileSizes(&$arFile)
    {
        if (isset($arFile['SRC'])) {
            // ��������� ������� �������
            $imageInfo = (new \Bitrix\Main\File\Image(\Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC']))->getInfo();
            if ($imageInfo) {
                $arFile['WIDTH'] = $imageInfo->getWidth();
                $arFile['HEIGHT'] = $imageInfo->getHeight();
            }
        }
    }
    /**
 * ������������ � jpg
 * @param $arFile
 * @return void
 */
    public function convertFromWebp(&$arFile)
    {
        if (preg_match('/\\.webp$/i', $arFile['SRC'])) {
            $image = new \Bitrix\Main\File\Image(\Bitrix\Main\Application::getDocumentRoot() . $arFile['SRC']);
            $image->load();
            $dir = $this->getCanvasDir() . '/from_webp/';
            $root = \Bitrix\Main\Application::getDocumentRoot();
            \Bitrix\Main\IO\Directory::createDirectory($root . $dir);
            $filename = 'from_webp_' . $arFile['ID'] . '.jpg';
            if ($res = $image->saveAs($root . $dir . $filename, 100, \Bitrix\Main\File\Image::FORMAT_JPEG)) {
                $arFile['SRC'] = $dir . $filename;
            }
        }
    }
    /**
 * �������������� �������� ����� ������� ����� ���������� � �������� �������
 * @param $arFile
 * @param $maxWidth
 * @param $maxHeight
 * @return false|void
 */
    public function prepareMaxSize(&$arFile, $maxWidth, $maxHeight)
    {
        $sourceImage = new \Bitrix\Main\File\Image($this->root() . $arFile['SRC']);
        $sourceInfo = $sourceImage->getInfo();
        if ($sourceInfo === null || !$sourceInfo->isSupported()) {
            return false;
        }
        $sourceRectangle = $sourceInfo->toRectangle();
        $destinationRectangle = new \Bitrix\Main\File\Image\Rectangle($maxWidth, $maxHeight);
        if ($sourceRectangle->resize($destinationRectangle, \Bitrix\Main\File\Image::RESIZE_PROPORTIONAL)) {
            $sourceImage->load();
            if ($sourceImage->resize($sourceRectangle, $destinationRectangle)) {
                $dir = $this->getCanvasDir() . '/max/';
                $filename = $arFile['ID'] . '.jpg';
                \Bitrix\Main\IO\Directory::createDirectory($this->root() . $dir);
                $sourceImage->saveAs($this->root() . $dir . $filename, 100, \Bitrix\Main\File\Image::FORMAT_JPEG);
                $arFile['SRC'] = $dir . $filename;
                $arFile['HEIGHT'] = $destinationRectangle->getHeight();
                $arFile['WIDTH'] = $destinationRectangle->getWidth();
            }
        }
    }
    /**
 * ������ ���� �� ����� ����� DOCUMENT_ROOT
 * @return string|null
 */
    public function root()
    {
        return \Bitrix\Main\Application::getDocumentRoot();
    }
}
?>