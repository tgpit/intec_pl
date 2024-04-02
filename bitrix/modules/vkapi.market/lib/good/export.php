<?php

namespace VKapi\Market\Good;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Fields\ExpressionField;
use Bitrix\Main\ORM\Query\Query;
use VKapi\Market\Api;
use VKapi\Market\Exception\ApiResponseException;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\GoodLimitException;
use VKapi\Market\Exception\TimeoutException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * 
 * ������ c ������������ �������� � ���������, ��������� ������ � ��, �������������� � ��
 * Class Export
 * 
 * @package VKapi\Market\Good
 */
class Export
{
    const PRODUCT_TYPE_SIMPLE = 1;
    // ������� �����
    const PRODUCT_TYPE_HAS_OFFERS = 2;
    // ����� � ��������� �������������
    const PROPERTY_TYPE_L = 'L';
    // L - ������
    const PROPERTY_TYPE_S = 'S';
    // S - ������
    const PROPERTY_TYPE_N = 'N';
    // N - �����
    const PROPERTY_TYPE_F = 'F';
    // F - ����
    const PROPERTY_TYPE_G = 'G';
    // G - �������� � �������
    const PROPERTY_TYPE_E = 'E';
    // E - �������� � ��������
    /**
     * 
     * @var \VKapi\Market\Export\Item
     */
    protected $oExportItem = null;
    /**
     * 
     * ������ �����������
     * 
     * @var \VKapi\Market\Good\ExportTable
     */
    private $oGoodExportTable = null;
    /**
     * 
     * @var \VKapi\Market\Album\Export - ������ �������� �������� � ��
     */
    protected $oAlbumExport = null;
    /**
     * 
     * @var \VKapi\Market\Album\Item - ������ ��� ������ � ���������� ����������
     */
    protected $oAlbumItem = null;
    /**
     * 
     * @var \VKapi\Market\Export\Photo
     */
    protected $oPhoto = null;
    /**
     * 
     * @var \VKapi\Market\Export\Log �����������
     */
    protected $oLog = null;
    /**
     * 
     * @var \VKapi\Market\State ���������
     */
    protected $oState = null;
    /**
     * 
     * @var \CIBLockElement
     */
    protected $oIblockElementOld = null;
    /**
     * 
     * @var array ������ ��������� ��������, ����� �������� �� ��������, �������� ������������ ������� �� ��������
     * � ��
     */
    protected $arPrepiredPropValue = [];
    /**
     * 
     * @var array ������ �������� � �� [albumId => vkId]
     */
    protected $arAlbumsInVk = null;
    /**
     * 
     * @param \VKapi\Market\Export\Item $oExportItem
     */
    public function __construct(\VKapi\Market\Export\Item $oExportItem)
    {
        $this->oExportItem = $oExportItem;
        if (!\VKapi\Market\Manager::getInstance()->isInstalledIblockModule()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('MODULE_IBLOCK_IS_NOT_INSTALLED'), 'MODULE_NOT_INSTALLED');
        }
    }
    /**
     * 
     * @param $name
     * @param null $arReplace
     * 
     * @return string
     */
    public function getMessage($name, $arReplace = null)
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.GOOD.EXPORT.' . $name, $arReplace);
    }
    /**
     * 
     * ������ � �������� ���������� ������� � ��,
     * �������� ����:
     * + ID :int
     * + GROUP_ID :int
     * + PRODUCT_ID :int
     * + OFFER_ID :int
     * + VK_ID :int
     * + HASH :string
     * 
     * @return \VKapi\Market\Good\ExportTable
     */
    public function goodExportTable()
    {
        if (is_null($this->oGoodExportTable)) {
            $this->oGoodExportTable = new \VKapi\Market\Good\ExportTable();
        }
        return $this->oGoodExportTable;
    }
    /**
     * 
     * ������ ��� ������ � �������� ������ ������� � ����������
     * �������� ���������� ����� �������� ������� �������� ��� ����������� ������
     * @return \VKapi\Market\Good\Reference\Export
     */
    public function goodReferenceExport()
    {
        return \VKapi\Market\Good\Reference\Export::getInstance();
    }
    public function goodReferenceExportTable()
    {
        return \VKapi\Market\Good\Reference\Export::getInstance();
    }
    /**
     * 
     * @return Reference\Album
     */
    public function goodReferenceAlbum()
    {
        return \VKapi\Market\Good\Reference\Album::getInstance();
    }
    /**
     * 
     * ������ ������ ��� ������ � ����������� ��������
     * 
     * @return \VKapi\Market\Export\Item
     */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
     * 
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
     * 
     * ������ ������ ��� �������� ���������
     * 
     * @return \VKapi\Market\State
     */
    public function state()
    {
        if (is_null($this->oState)) {
            $this->oState = new \VKapi\Market\State('export_' . intval($this->exportItem()->getId()), '/good');
        }
        return $this->oState;
    }
    /**
     * 
     * ����� ��� ������������ ���������� ������� � ��� 1000, � ����� 7000
     * @return \VKapi\Market\Export\Limit\Good
     */
    public function limit()
    {
        if (is_null($this->oLimit)) {
            $this->oLimit = new \VKapi\Market\Export\Limit\Good($this->exportItem());
        }
        return $this->oLimit;
    }
    /**
     * 
     * ����� ��� �������� ������� ����� ���������� �������
     * @return \VKapi\Market\Export\History\Good
     */
    public function history()
    {
        if (is_null($this->oHistory)) {
            $this->oHistory = new \VKapi\Market\Export\History\Good($this->exportItem());
        }
        return $this->oHistory;
    }
    /**
     * 
     * ������ ������ �������� �������� � ��
     * s
     * 
     * @return \VKapi\Market\Album\Export
     */
    public function albumExport()
    {
        if (is_null($this->oAlbumExport)) {
            $this->oAlbumExport = new \VKapi\Market\Album\Export($this->exportItem());
        }
        return $this->oAlbumExport;
    }
    /**
     * 
     * ������ ������ ��� ������ � ���������� ����������
     * 
     * @return \VKapi\Market\Album\Item
     */
    public function albumItem()
    {
        if (is_null($this->oAlbumItem)) {
            $this->oAlbumItem = new \VKapi\Market\Album\Item();
        }
        return $this->oAlbumItem;
    }
    /**
     * 
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
     * 
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * 
     * @return \CIBlockElement
     */
    public function iblockElementOld()
    {
        if (is_null($this->oIblockElementOld)) {
            $this->oIblockElementOld = new \CIBlockElement();
        }
        return $this->oIblockElementOld;
    }
    /**
     * 
     * ������ hash �������� ����� ������, ������������ � ��, ��� �������� ������� ���������
     * 
     * @return int
     */
    public function getHash($arFields, $arAlbums)
    {
        ksort($arFields);
        ksort($arAlbums);
        return md5(serialize([$arFields, $arAlbums]));
    }
    /**
     * 
     * ������������ ������ �� ������� � ���������
     * 
     * @return \VKapi\Market\Result - ����������� ��������� � �������
     * 
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
                //��� ����, ������� ����, � �������� ������, ����� �������� ���������, �������� ���������� 2 �� 10
                1 => ['name' => $this->getMessage('STEP1'), 'percent' => 0, 'error' => false],
                2 => ['name' => $this->getMessage('STEP2'), 'percent' => 0, 'error' => false],
                3 => ['name' => $this->getMessage('STEP3'), 'percent' => 0, 'error' => false],
                4 => ['name' => $this->getMessage('STEP4'), 'percent' => 0, 'error' => false],
                5 => ['name' => $this->getMessage('STEP5'), 'percent' => 0, 'error' => false],
                6 => ['name' => $this->getMessage('STEP6'), 'percent' => 0, 'error' => false],
                7 => ['name' => $this->getMessage('STEP7'), 'percent' => 0, 'error' => false],
                8 => ['name' => $this->getMessage('STEP8'), 'percent' => 0, 'error' => false],
                9 => ['name' => $this->getMessage('STEP9'), 'percent' => 0, 'error' => false],
            ]]);
            $data = $this->state()->get();
            $this->log()->notice($this->getMessage('EXPORT_GOODS.START'));
        }
        // ��������� ������
        $this->state()->set(['run' => true, 'timeStart' => time()])->save();
        try {
            if (\CModule::IncludeModuleEx("vkapi." . "mar" . "ke" . "" . "" . "" . "" . "t") == constant("MODULE_DEM" . "O_EX" . "PIRE" . "" . "" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO" . "" . "_E" . "XPI" . "RED"), "BXM" . "A" . "KER_DE" . "MO_EX" . "PI" . "RED");
            }
            switch ($data['step']) {
                case 1:
                    $this->exportItem()->checkApiAccess();
                    $data['step']++;
                    $data['steps'][1]['percent'] = 100;
                    $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 1, '#STEP_NAME#' => $data['steps'][1]['name']]));
                    break;
                case 2:
                    // ������������ ������ ������� ��� ��������
                    // ������������ ������ ������� �� ���������
                    $resultAction = $this->exportRunPrepareList();
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $data['steps'][2]['percent'] = 100;
                        $data['steps'][2]['name'] = $resultAction->getData('message');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name']]));
                    } else {
                        $data['steps'][2]['name'] = $resultAction->getData('message');
                        $data['steps'][2]['percent'] = $resultAction->getData('percent');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name'], '#PERCENT#' => $data['steps'][2]['percent']]));
                    }
                    break;
                case 3:
                    // �������� ����� ����������� �������
                    $resultAction = $this->exportRunCheckExistsInVk();
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $data['steps'][3]['percent'] = 100;
                        $data['steps'][3]['name'] = $resultAction->getData('message');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name']]));
                    } else {
                        $data['steps'][3]['name'] = $resultAction->getData('message');
                        $data['steps'][3]['percent'] = $resultAction->getData('percent');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name'], '#PERCENT#' => $data['steps'][3]['percent']]));
                    }
                    break;
                case 4:
                    // �������� ������ ������������ �������� � ��
                    $this->getAlbumIdInVkList(true);
                    // ���������� �������
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // ������ ������� � ������������ ��
                        $resultAction = $this->exportRunUpdateInVkBaseMode();
                    } else {
                        $resultAction = $this->exportRunUpdateInVk();
                    }
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $data['steps'][4]['percent'] = 100;
                        $data['steps'][4]['name'] = $resultAction->getData('message');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 4, '#STEP_NAME#' => $data['steps'][4]['name']]));
                    } else {
                        $data['steps'][4]['name'] = $resultAction->getData('message');
                        $data['steps'][4]['percent'] = $resultAction->getData('percent');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 4, '#STEP_NAME#' => $data['steps'][4]['name'], '#PERCENT#' => $data['steps'][4]['percent']]));
                    }
                    break;
                case 5:
                    // �������� ������, �� ������ ����� �������
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // ������ ������� � ������������ ��
                        $resultAction = $this->exportRunDeleteOldFromVKBaseMode();
                    } else {
                        $resultAction = $this->exportRunDeleteOldFromVK();
                    }
                    $data['steps'][5]['name'] = $resultAction->getData('message');
                    $data['steps'][5]['percent'] = $resultAction->getData('percent');
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name'], '#PERCENT#' => $data['steps'][5]['percent']]));
                    }
                    break;
                case 6:
                    // �������� ����������
                    $resultAction = $this->exportRunDeleteLocalDoublesFormVK();
                    $data['steps'][6]['name'] = $resultAction->getData('message');
                    $data['steps'][6]['percent'] = $resultAction->getData('percent');
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 6, '#STEP_NAME#' => $data['steps'][6]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 6, '#STEP_NAME#' => $data['steps'][6]['name'], '#PERCENT#' => $data['steps'][6]['percent']]));
                    }
                    break;
                case 7:
                    // ���������� ����� �������
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // ������ ������� � ������������ ��
                        $resultAction = $this->exportRunAddToVkBaseMode();
                    } else {
                        $resultAction = $this->exportRunAddToVk();
                    }
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $data['steps'][7]['percent'] = 100;
                        $data['steps'][7]['name'] = $resultAction->getData('message');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 7, '#STEP_NAME#' => $data['steps'][6]['name']]));
                    } else {
                        $data['steps'][7]['name'] = $resultAction->getData('message');
                        $data['steps'][7]['percent'] = $resultAction->getData('percent');
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 7, '#STEP_NAME#' => $data['steps'][7]['name'], '#PERCENT#' => $data['steps'][7]['percent']]));
                    }
                    break;
                case 8:
                    // ������� ����������� ������� �� ��
                    $resultAction = $this->exportRunDeleteUnknownInVK();
                    $data['steps'][8]['name'] = $resultAction->getData('message');
                    $data['steps'][8]['percent'] = $resultAction->getData('percent');
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 8, '#STEP_NAME#' => $data['steps'][8]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 8, '#STEP_NAME#' => $data['steps'][8]['name'], '#PERCENT#' => $data['steps'][8]['percent']]));
                    }
                    break;
                case 9:
                    // �����������
                    $resultAction = $this->exportRunGroupUngroupItem();
                    $data['steps'][9]['name'] = $resultAction->getData('message');
                    $data['steps'][9]['percent'] = $resultAction->getData('percent');
                    // ���� �������� ���������
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 9, '#STEP_NAME#' => $data['steps'][9]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 9, '#STEP_NAME#' => $data['steps'][9]['name'], '#PERCENT#' => $data['steps'][9]['percent']]));
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
            $this->log()->notice($this->getMessage('EXPORT_GOODS.STOP'));
        }
        // �����������
        $this->state()->set(['run' => false, 'step' => $data['step'], 'steps' => $data['steps'], 'complete' => $data['complete'], 'percent' => $data['percent']])->save();
        $result->setDataArray($this->state()->get());
        if ($result->isSuccess()) {
            $this->state()->save();
        } else {
            $this->state()->clean();
        }
        return $result;
    }
    /**
     * 
     * ����� ������� � ���������, �������� �������, ���������� � ������ � �������� �� �������
     */
    public function exportRunPrepareList()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunPrepareList';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                'count' => 0,
                'offset' => 0,
                // ����� �� ��������
                'limit' => 10,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // ������� ��� �������� ������
            $this->goodReferenceExport()->getTable()->setMarkForAllByExportId($this->exportItem()->getId());
        }
        $state = $data[$stateKey];
        // �������� �������� � �� ������� -----------
        $arAlbumId = $this->exportItem()->getAlbumIds();
        // ������� ��������� � ��������
        $arAlbums = $this->albumItem()->getItemsById($arAlbumId);
        // ������� �������� � �������� �������, ������� ��� ��� � �������
        \VKapi\Market\Good\Reference\AlbumTable::deleteNotExistsYet($arAlbumId, $this->exportItem()->getProductIblockId(), $this->exportItem()->getOfferIblockId());
        // ������� �������� � �������� �������, ������� ��� ��� � �������
        \VKapi\Market\Good\Reference\ExportTable::deleteNotExistsYet($this->exportItem()->getId(), $this->exportItem()->getProductIblockId(), $this->exportItem()->getOfferIblockId());
        if (\CModule::IncludeModuleEx("vkapi.market") === constant("MODUL" . "E_DEMO_EXPI" . "R" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.M" . "ARKET.DEMO_EXPIRE" . "D"), "BXMAKER" . "_DEMO_EXPIRE" . "D");
        }
        // ���������� ���������� �������
        $baseProductFilter = $this->exportRunPrepareListActionGetFilter();
        $countQuery = \Bitrix\Iblock\ElementTable::query();
        $countQuery->addSelect(new \Bitrix\Main\ORM\Fields\ExpressionField('CNT', 'COUNT(DISTINCT %s)', 'ID'));
        $countQuery->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT_SECTION', '\\Bitrix\\Iblock\\SectionElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.ID', 'ref.IBLOCK_ELEMENT_ID')));
        $countQuery->where($baseProductFilter);
        $dbrCount = $countQuery->exec();
        if ($arCount = $dbrCount->fetch()) {
            $state['count'] = $arCount['CNT'];
        }
        try {
            // ������� ������
            while ($state['count'] > $state['offset']) {
                $this->manager()->checkTime();
                $elementQuery = \Bitrix\Iblock\ElementTable::query();
                $elementQuery->addSelect(new \Bitrix\Main\ORM\Fields\ExpressionField('DISTINCT_ID', 'DISTINCT %s', 'ID'));
                $elementQuery->registerRuntimeField(new \Bitrix\Main\ORM\Fields\Relations\Reference('ELEMENT_SECTION', '\\Bitrix\\Iblock\\SectionElementTable', \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.ID', 'ref.IBLOCK_ELEMENT_ID')));
                $elementQuery->where($baseProductFilter);
                $elementQuery->setOffset($state['offset']);
                $elementQuery->setLimit($state['limit']);
                $elementQuery->setOrder(['ID' => 'ASC']);
                $dbrElement = $elementQuery->exec();
                while ($arElement = $dbrElement->fetch()) {
                    $this->manager()->checkTime();
                    if ($this->exportRunPrepareListActionCheckElement($arElement['DISTINCT_ID'], $arAlbums)) {
                        $state['offset'] += 1;
                    }
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ���� ������� �� �������, �� ��������� ��������� � ���� ������
        }
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            // ������� ��� ������
            $this->goodReferenceExport()->getTable()->deleteAllMarkedByExportId($this->exportItem()->getId());
        }
        $arCountAll = $this->goodReferenceExport()->getTable()->getList(['select' => ['CNT_DISTINCT_PRODUCT_ID'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId()]])->fetch();
        $state['validProduct'] = $arCountAll['CNT_DISTINCT_PRODUCT_ID'] ?? 0;
        $state['valid'] = $this->goodReferenceExport()->getTable()->getCount(['EXPORT_ID' => $this->exportItem()->getId()]);
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray([
            'count' => $state['offset'],
            // ��������
            'all' => $state['count'],
            // �����
            'complete' => $state['complete'],
            // ���� ���������� �����
            'percent' => $state['percent'],
            //���������
            'message' => $this->getMessage('PREPARE_LIST', ['#COUNT#' => $state['offset'], '#ALL#' => $state['count'], '#VALID#' => $state['valid'], '#VALID_PRODUCT#' => $state['validProduct']]),
        ]);
        return $result;
    }
    /**
     * 
     * ������ ��������� ���������� ��� ������������ �������� ������ ������� ��� ����������
     * @return \Bitrix\Main\ORM\Query\Filter\ConditionTree
     */
    public function exportRunPrepareListActionGetFilter()
    {
        $filterBase = \Bitrix\Main\ORM\Query\Query::filter();
        $filterBase->logic(\Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_AND);
        $filterBase->where('IBLOCK_ID', '=', $this->exportItem()->getProductIblockId());
        $filterBase->where(\Bitrix\Main\ORM\Query\Query::filter()->logic(\Bitrix\Main\ORM\Query\Filter\ConditionTree::LOGIC_OR)->whereNull('WF_PARENT_ELEMENT_ID')->where('WF_PARENT_ELEMENT_ID', '=', 0));
        $oCondition = new \VKapi\Market\Condition\Manager();
        $arConditions = $this->exportItem()->getConditions();
        $subFilter = $oCondition->parseBaseFilter($arConditions, $this->exportItem()->getProductIblockId());
        $filterBase->where($subFilter);
        [$filterNew] = $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_GET_FILTER_FOR_PREPARE_LIST, ['filter' => $filterBase, 'arExportData' => $this->exportItem()->getData()], true);
        if (isset($filterNew) && $filterNew instanceof \Bitrix\Main\ORM\Query\Filter\ConditionTree) {
            return $filterNew;
        }
        return $filterBase;
    }
    /**
     * 
     * ������ ����� ���������� ������� ��� ����������,
     * �� ������� �������� �������
     * @return int|mixed
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunPrepareListActionGetAllCount()
    {
        // ���������� �������� �������
        $count = \Bitrix\Iblock\ElementTable::getCount($this->exportRunPrepareListActionGetFilter());
        // ���� ���� �������� �����������, �� ������� ���������� ��� � �������� �����������
        if ($this->exportItem()->hasOffers()) {
            $arOfferCount = $this->iblockElementOld()->getList(['cnt' => 'cnt'], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false, 'PROPERTY_' . $this->exportItem()->getLinkPropertyId() . '.IBLOCK_ID' => $this->exportItem()->getProductIblockId()], ['IBLOCK_ID'])->fetch();
            $count += $arOfferCount['CNT'];
            // �������� ���������� �������� ������� �� ����� ������
            $arProductWithOfferCount = $this->iblockElementOld()->getList(['cnt' => 'cnt'], ['IBLOCK_ID' => $this->exportItem()->getProductIblockId(), 'WF_PARENT_ELEMENT_ID' => false, ["ID" => $this->iblockElementOld()->SubQuery("PROPERTY_" . $this->exportItem()->getLinkPropertyId(), ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false])]], ['IBLOCK_ID'])->fetch();
            $count -= $arProductWithOfferCount['CNT'];
        }
        return $count;
    }
    /**
     * 
     * ���������� ����� � ��� �������� �����������,
     * �������� �������� �� �� �� �������� � ���� ��������
     * ������� � ��������������� ������� ������
     * ����� ���� ������ ������� ���������� ������������ ������� ��� �������� �����������
     * @param $productId
     * @param $arAlbums
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunPrepareListActionCheckElement($productId, $arAlbums)
    {
        $oCondition = new \VKapi\Market\Condition\Manager();
        // ������� ����� ���� ��� �������� �������
        $arElements = $oCondition->getPreparedElementFieldsById([$productId], false, $this->exportItem()->getProductPriceUserGroupIds(), $this->exportItem()->getSiteId());
        $arElement = $arElements[$productId];
        /**
         * 
         * ������ ����� �������� {elementId: {offerId : [albumId, ....], ...}, ...}
         */
        $arElementAlbumReference = [];
        $arElementExportReference = [];
        // ���� ���� ��������, ��������� ������� ��� �������� ----------
        $arElementAlbumReference[$productId][0] = [];
        // ���������� �������� �� ����� ��� �������� � ��������
        foreach ($arAlbums as $albumId => $arAlbum) {
            if ($oCondition->isMatchCondition($arAlbum['PARAMS']['CONDITIONS'], $arElement)) {
                $arElementAlbumReference[$productId][0][$albumId] = $albumId;
            }
        }
        // ������� ������ �� ��������� ������ ----------------
        // �� ����� ���� ����� ������������� ��������� ����������� � �� ��������� ������
        // ����� ��� ������� �� ����������
        $arElementExportReference[$productId][0] = [];
        if ($oCondition->isMatchCondition($this->exportItem()->getConditions(), $arElement)) {
            $arElementExportReference[$productId][0][$this->exportItem()->getId()] = $this->exportItem()->getId();
        }
        // �������� �������� �����������
        if ($this->exportItem()->hasOffers()) {
            $arOffers = [];
            $dbrOffer = \CIBlockElement::getList(['ID' => 'ASC'], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false, 'PROPERTY_' . $this->exportItem()->getLinkPropertyId() => $productId], false, false, ['ID', 'PROPERTY_' . $this->exportItem()->getLinkPropertyId()]);
            while ($arOffer = $dbrOffer->fetch()) {
                $arOffers[$arOffer['ID']] = [];
            }
            if (count($arOffers)) {
                unset($arElementAlbumReference[$productId][0]);
                unset($arElementExportReference[$productId][0]);
                // ���������� ����� ������
                $arOffersConditions = $oCondition->getPreparedElementFieldsById(array_keys($arOffers), true, $this->exportItem()->getOfferPriceUserGroupIds(), $this->exportItem()->getSiteId());
                foreach ($arOffersConditions as $offerId => $offerFields) {
                    $arOffers[$offerId] = array_replace($arElement, $offerFields);
                }
                // ��������� ������� ��� ��������
                foreach ($arOffers as $offerId => $arOffer) {
                    // ������� �������� � �������� -------------
                    if (count($arAlbums)) {
                        $arElementAlbumReference[$productId][$offerId] = [];
                        foreach ($arAlbums as $albumId => $arAlbum) {
                            if ($oCondition->isMatchCondition($arAlbum['PARAMS']['CONDITIONS'], $arOffer)) {
                                $arElementAlbumReference[$productId][$offerId][$albumId] = $albumId;
                            }
                        }
                    }
                    // �������� � �������� ---------------------------
                    $arElementExportReference[$productId][$offerId] = [];
                    if ($oCondition->isMatchCondition($this->exportItem()->getConditions(), $arOffer)) {
                        $arElementExportReference[$productId][$offerId][$this->exportItem()->getId()] = $this->exportItem()->getId();
                    }
                }
            }
        }
        // ��������� ����� ������ �� ��������
        $this->goodReferenceAlbum()->updateElementReferenceList($arElementAlbumReference, array_keys($arAlbums));
        // ��������� ����� ������ �� ��������� � ���������
        $this->goodReferenceExport()->updateElementReferenceList($arElementExportReference, [$this->exportItem()->getId()]);
        return true;
    }
    /**
     * 
     * �������� ����� ����������� ������� � ��
     * �������� �� ���� ������� � ��������� ���� � ��������� ������� � ��
     * ���� ����, �� ������� �� ��������� ���� ����������� �������
     */
    public function exportRunCheckExistsInVk()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckExistsInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => 250,
                //�����������
                'losted' => 0,
                'vkItems' => [],
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            // ���������� ���������� ----
            $state['count'] = $this->goodExportTable()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId()]);
            // �������� ������ ������� � ��
            $vkItemIds = $this->getVkItemIdList($state['vkItems']);
            $vkItemIds = array_combine($vkItemIds, $vkItemIds);
            if (\Bitrix\Main\Loader::includeSharewareModule("vkap" . "i.market") == constant("MODULE_DEMO_" . "E" . "XPI" . "RED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXMAKER_DEMO_EXPIR" . "E" . "" . "" . "D");
            }
            while ($state['count'] > $state['offset']) {
                $this->manager()->checkTime();
                // �������� ������ � ������� ---------
                $dbrItems = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId()], 'select' => ['ID', 'VK_ID', 'PRODUCT_ID', 'OFFER_ID'], 'limit' => $state['limit'], 'offset' => $state['offset']]);
                while ($arItem = $dbrItems->fetch()) {
                    $this->manager()->checkTime();
                    if (isset($vkItemIds[$arItem['VK_ID']])) {
                        $state['offset']++;
                    } else {
                        $state['count']--;
                        $state['losted']++;
                        $this->goodExportTable()->delete($arItem['ID']);
                        // ������� ��������
                        $this->photo()->getTable()->deleteByProduct($arItem['PRODUCT_ID'], $arItem['OFFER_ID'], $this->exportItem()->getGroupId());
                        if ($arItem['OFFER_ID'] > 0) {
                            $this->log()->notice($this->getMessage('CHECK_EXISTS_IN_VK_DELETE_OFFER', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID'], '#OFFER_ID#' => $arItem['OFFER_ID']]));
                        } else {
                            $this->log()->notice($this->getMessage('CHECK_EXISTS_IN_VK_DELETE_PRODUCT', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID']]));
                        }
                    }
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['vkItems']);
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'] + $state['losted'], 'count' => $state['count'] + $state['losted'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('CHECK_EXISTS_IN_VK', ['#OFFSET#' => $state['offset'] + $state['losted'], '#COUNT#' => $state['count'] + $state['losted'], '#LOSTED#' => $state['losted']])]);
        return $result;
    }
    /**
     * 
     * ������ ������ ���������������� ������� � ��
     * 
     * @return int[]
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getVkItemIdList(&$state)
    {
        // �������� ����������
        $resultRequest = $this->exportItem()->connection()->method('market.get', ["owner_id" => '-' . $this->exportItem()->getGroupId(), "count" => 1, "extended" => 1, "with_disabled" => 1]);
        $response = $resultRequest->getData('response');
        $count = $response['count'];
        if (empty($state)) {
            $state = ['limit' => 20, 'offset' => 0, 'repeat' => 1, 'items' => []];
        }
        if ($count) {
            while ($state['offset'] < $count) {
                $this->manager()->checkTime();
                $code = '
                        var items = [];
                        var ownerId  = -' . $this->exportItem()->getGroupId() . ';
                        var limit = ' . $state['limit'] . ';
                        var offset = ' . $state['offset'] . ';
                        var i = ' . $state['repeat'] . ';
                        var variants = [];
                        var variantsItem = false;
                        var res = false;
                        while(i > 0){
                            res = API.market.get({ "owner_id": ownerId, "count" : limit, "offset" : offset, "extended" : 1,"need_variants" : 1, "with_disabled" : 1});
                            i = i-1;
                            offset = offset + limit;
                            items = items +  res.items@.id;
                            
                            variants = res.items@.variants;
                            if(variants.length)
                            {
                                while(variants.length > 0)
                                {
                                     variantsItem = variants.pop();
                                    if(variantsItem)
                                    {
                                        items = items + variantsItem@.item_id;
                                    }
                                }
                            }
                        }
                        return items;';
                $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => $code]);
                $state['offset'] += $state['limit'] * $state['repeat'];
                $response = $resultRequest->getData('response');
                $state['items'] = array_merge($state['items'], $response);
                $state['items'] = array_values(array_unique($state['items']));
            }
        }
        return $state['items'];
    }
    /**
     * 
     * ���������� ������� � ���������, ����� �����������, ������� ���� ����� ��������� ��������� � ��
     * 
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * 
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunUpdateInVk()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunUpdateInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => $this->manager()->getExportPackLimit(),
                'updated' => 0,
                'skipped' => 0,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        $state['limit'] = $this->manager()->getExportPackLimit();
        try {
            if ($this->exportItem()->isEnabledExtendedGoods() || !$this->exportItem()->isEnabledOfferCombine()) {
                // ������� ����������� ������ ������ �� ����� � �� ��� ������ ������,
                // ������� ����� ������������� ��� ��������� ������ � ���������� ����������� �������� �����������
                $this->goodExportTable()->deleteDoublesVkIdByGroupId($this->exportItem()->getGroupId());
            }
            // �������� ������ � �������� ������ ��� ��������, ������� ����� ������������ ������ � ��
            $state['count'] = $this->exportRunUpdateInVkActionGetCount();
            while ($state['offset'] < $state['count']) {
                $this->manager()->checkTime();
                // �������� ������ � ������� ---------------------------------------------------
                $dbrItems = $this->goodReferenceExport()->getTable()->getList(['order' => ['ID' => 'ASC'], 'select' => ['*'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), '!=GOOD_EXPORT.VK_ID' => null], 'limit' => $state['limit'], 'offset' => $state['offset'], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_EXPORT', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
                while ($arItem = $dbrItems->fetch()) {
                    if ($this->exportRunUpdateInVkActionPrepareItem($arItem)) {
                        $state['updated']++;
                    } else {
                        $state['skipped']++;
                    }
                    $state['offset']++;
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "ket") === constant("MOD" . "ULE_DEMO_EXPI" . "RE" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKA" . "PI.MARKET.DEMO_" . "EXPI" . "" . "R" . "E" . "D"), "BXMAKER_DEMO_EXPI" . "" . "" . "RE" . "D");
        }
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_UPDATE_IN_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#UPDATED#' => $state['updated'], '#SKIPPED#' => $state['skipped']])]);
        return $result;
    }
    /**
     * 
     * ������ ����� ���������� ������� ������� ���������� ��������
     * 
     * @return int
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunUpdateInVkActionGetCount()
    {
        $dbrCount = $this->goodReferenceExport()->getTable()->getList(['select' => ['COUNT' => 'CNT'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), '!=GOOD_EXPORT.VK_ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_EXPORT', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        if ($arCount = $dbrCount->fetch()) {
            return $arCount['COUNT'];
        }
        return 0;
    }
    /**
     * 
     * ���������� ������ � ����� ���������� ������, ��� ������������� ������� ���
     * @param $arItem
     * @return bool
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunUpdateInVkActionPrepareItem($arItem)
    {
        // �������������� �������� ������� -------------------------------
        $preparedItem = $this->getPreparedItem($arItem['PRODUCT_ID'], (array) $arItem['OFFER_ID']);
        try {
            $arFields = $preparedItem->getFields();
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // ����� ������ � ����������� ������� � ��, ����� �������� ���� ����� ��������� ----------------------
            $arGoodExportRow = $this->goodExportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // ������ � ������� ������� ����������� ��� ��������
            $arGoodReferenceExportRow = $this->goodReferenceExport()->getTable()->getList(['order' => ['ID' => 'ASC'], 'select' => ['ID', 'PRODUCT_ID', 'OFFER_ID', 'FLAG'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // ������� ������ ���� �����������
            $this->history()->append($preparedItem, $arGoodExportRow['VK_ID']);
            if ($arFields['price'] < 0.01) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.PRICE_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $newHash = $this->getHash($arFields, $arVkAlbumIds);
            if ($arGoodExportRow['HASH'] == $newHash) {
                $this->log()->notice($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.NOT_CHANGED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            // ������ ���������� ��������
            if ($this->manager()->isDisabledUpdatePicture()) {
                unset($arFields['main_photo_id'], $arFields['photo_ids']);
            } elseif (!(int) $arFields['main_photo_id']) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.edit', array_merge($arFields, ['item_id' => $arGoodExportRow['VK_ID']]));
            $response = $resultApi->getData('response');
            // ����� ����������� ������ � ��������
            $this->deleteVkItemIdFromAllAlbums($arGoodExportRow['VK_ID']);
            $this->addVkItemIdToVkAlbums($arGoodExportRow['VK_ID'], $arVkAlbumIds);
            // ��������� ������ � ����� ����
            $resultUpdateGoodExport = $this->goodExportTable()->update($arGoodExportRow['ID'], ['HASH' => $this->getHash($arFields, $arVkAlbumIds)]);
            $this->log()->ok($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.UPDATED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
        } catch (\VKapi\Market\Exception\ApiResponseException $ex) {
            if ($ex->is(\VKapi\Market\Api::ERROR_100) && preg_match('/\\:\\s+photo\\s+/', $ex->getMessage()) && isset($arFields)) {
                $arPhotoId = (array) $arFields['main_photo_id'];
                $arPhotoId = array_merge($arPhotoId, explode(',', $arFields['photo_ids']));
                $this->photo()->deleteByPhotoId($arPhotoId, $this->exportItem()->getGroupId());
            }
            $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.ERROR', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds()), '#MSG#' => $ex->getMessage()]));
            return false;
        }
        return true;
    }
    /**
     * 
     * ���������� ������� � ���������, ����� �����������, ������� ���� ����� ��������� ��������� � ��
     * ������� ��� ����������� �������� ����������� � ������� ������ �������
     * 
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * 
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunUpdateInVkBaseMode()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunUpdateInVkBaseMode';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => 25,
                'updated' => 0,
                'skipped' => 0,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        $data['limit'] = $this->manager()->getExportPackLimit();
        try {
            // �������� ������ � �������� ������ ��� ��������, ������� ����� ������������ ������ � ��
            $state['count'] = $this->exportRunUpdateInVkBaseModeActionGetCount();
            while ($productId = $this->exportRunUpdateInVkBaseModeActionGetNext($state['offset'])) {
                $this->manager()->checkTime();
                if ($this->exportRunUpdateInVkBaseModeActionUpdate($productId)) {
                    $state['updated']++;
                } else {
                    $state['skipped']++;
                }
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.marke" . "t") == constant("MODULE" . "_DEMO_E" . "XPI" . "RE" . "" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET." . "DE" . "MO_EXPI" . "" . "RED"), "BXMAKER_DEMO_E" . "XPIRED");
        }
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_UPDATE_IN_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#UPDATED#' => $state['updated'], '#SKIPPED#' => $state['skipped']])]);
        return $result;
    }
    /**
     * 
     * ������ ����� ���������� ������� ������� ���������� ��������
     * 
     * @return int
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunUpdateInVkBaseModeActionGetCount()
    {
        $count = 0;
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => [new \Bitrix\Main\ORM\Fields\ExpressionField('COUNT', 'COUNT(DISTINCT(%s))', ['PRODUCT_ID'])], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), '!=GOOD_EXPORT.VK_ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_EXPORT', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        if ($ar = $dbr->fetch()) {
            $count = $ar['COUNT'];
        }
        return (int) $count;
    }
    /**
     * 
     * ������ ��������� �����, ������� ��� �� ��������
     * @param $offset - ���������� �����������, ������� �� ������ ���������
     * @return mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunUpdateInVkBaseModeActionGetNext($offset)
    {
        $productId = null;
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => ['PRODUCT_ID'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), '!=GOOD_EXPORT.VK_ID' => null], 'limit' => 1, 'offset' => $offset, 'group' => ['PRODUCT_ID'], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_EXPORT', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        if ($ar = $dbr->fetch()) {
            $productId = $ar['PRODUCT_ID'];
        }
        return $productId;
    }
    /**
     * 
     * ���������� ������ � ����� ���������� ������, ��� ������������� ������� ���
     * @param $arItem
     * @return bool
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunUpdateInVkBaseModeActionUpdate($productId)
    {
        // �������� ������
        $arRows = $this->exportRunAddToVkBaseModeActionAddGetRows($productId);
        if (empty($arRows)) {
            $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.REFERENCE_PRODUCT_ITEMS_NOT_FOUND', ['#ID#' => $productId]));
            return false;
        }
        // ������� �� ��������� �� �������� ������ ��� �� ��������� � ��
        $this->exportRunUpdateInVkBaseModeActionCreateExportedRow($arRows);
        // �������� �������� ����������
        $arOfferIds = array_column($arRows, 'OFFER_ID');
        $preparedItem = $this->getPreparedItem($productId, $arOfferIds);
        try {
            $arFields = $preparedItem->getFields();
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // ����� ������ � ����������� ������� � ��, ����� �������� ���� ����� ��������� ----------------------
            $arGoodExportRow = $this->goodExportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // ������� ������ ���� �����������
            $this->history()->append($preparedItem, $arGoodExportRow['VK_ID']);
            if ($arFields['price'] < 0.01) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.PRICE_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            if ($arGoodExportRow['HASH'] == $this->getHash($arFields, $arVkAlbumIds)) {
                $this->log()->notice($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.NOT_CHANGED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            // ������ ���������� ��������
            if ($this->manager()->isDisabledUpdatePicture()) {
                unset($arFields['main_photo_id'], $arFields['photo_ids']);
            } elseif (!(int) $arFields['main_photo_id']) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.edit', array_merge($arFields, ['item_id' => $arGoodExportRow['VK_ID']]));
            $response = $resultApi->getData('response');
            // ����� ����������� ������ � ��������
            $this->deleteVkItemIdFromAllAlbums($arGoodExportRow['VK_ID']);
            $this->addVkItemIdToVkAlbums($arGoodExportRow['VK_ID'], $arVkAlbumIds);
            // ��������� ������ � ����� ����
            $this->goodExportTable()->updateByGroupIdProductId($this->exportItem()->getGroupId(), $preparedItem->getProductId(), ['HASH' => $this->getHash($arFields, $arVkAlbumIds), 'VK_ID' => $arGoodExportRow['VK_ID']]);
            $this->log()->ok($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.UPDATED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
        } catch (\VKapi\Market\Exception\ApiResponseException $ex) {
            if ($ex->is(\VKapi\Market\Api::ERROR_100) && preg_match('/\\:\\s+photo\\s+/', $ex->getMessage()) && isset($arFields)) {
                $arPhotoId = (array) $arFields['main_photo_id'];
                $arPhotoId = array_merge($arPhotoId, explode(',', $arFields['photo_ids']));
                $this->photo()->deleteByPhotoId($arPhotoId, $this->exportItem()->getGroupId());
            }
            $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.API_ERROR', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds()), '#MSG#' => $ex->getMessage()]));
            return false;
        }
        return true;
    }
    /**
     * 
     * ���� �������� ��� ���� ����� � ��������, �� ��������� ��� � ������ ����������
     * � ��������� ������������� ����������� ������ ��� � ��������� ������
     * @param $arRows
     * @return false|void
     * @throws \Exception
     */
    public function exportRunUpdateInVkBaseModeActionCreateExportedRow($arRows)
    {
        $arExists = array_filter($arRows, function ($row) {
            return !is_null($row['VK_ID']);
        });
        if (empty($arExists)) {
            return false;
        }
        $arRowSource = reset($arExists);
        // �������� ������������ ������
        $arNeedAdd = array_filter($arRows, function ($row) {
            return is_null($row['VK_ID']);
        });
        if (empty($arNeedAdd)) {
            return false;
        }
        foreach ($arNeedAdd as $arRow) {
            $this->goodExportTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $arRow['PRODUCT_ID'], 'OFFER_ID' => $arRow['OFFER_ID'], 'VK_ID' => $arRowSource['VK_ID'], 'HASH' => $arRowSource['HASH']]);
        }
    }
    /**
     * 
     * ���������� ������� � ���������
     * @return \VKapi\Market\Result
     * @throws ApiResponseException
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVk()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunAddToVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => 25,
                'added' => 0,
                'skipped' => 0,
                'arId' => null,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        $state['limit'] = $this->manager()->getExportPackLimit();
        $isOverLimit = false;
        try {
            // ��������� ����� ���� ������
            if (is_null($state['arId'])) {
                $state['arId'] = $this->exportRunAddToVkActionGetIds();
                $state['count'] = count($state['arId']);
            }
            if (\CModule::IncludeModuleEx("vkapi" . ".ma" . "rket") == constant("MOD" . "ULE_DEMO_EXPIRED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_E" . "XPIRE" . "" . "D"), "BXMAKER_DEMO_EXPIR" . "E" . "D");
            }
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $this->limit()->check();
                $refId = $state['arId'][0];
                $exported = $this->goodExportTable()->getCount();
                if (\Bitrix\Main\Loader::includeSharewareModule("vk" . "a" . "pi.market") == constant("MODULE_D" . "EM" . "O")) {
                    if ($exported >= 50) {
                        break;
                    }
                }
                // ������ ����
                $this->goodReferenceExport()->getTable()->update($refId, ['FLAG' => \VKapi\Market\Good\Reference\Export::FLAG_NEED_SKIP]);
                if ($this->exportRunAddToVkActionAddByRefId($refId)) {
                    $state['added']++;
                } else {
                    $state['skipped']++;
                }
                // ���� ��� ����, ��
                array_shift($state['arId']);
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        } catch (\VKapi\Market\Exception\GoodLimitException $limitException) {
            $isOverLimit = true;
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        if ($isOverLimit) {
            $state['complete'] = true;
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $arReturn = ['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_ADD_TO_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']])];
        if ($isOverLimit) {
            $arReturn['message'] = $this->getMessage('EXPORT_RUN_ADD_TO_VK_LIMIT.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']]);
        }
        $result->setDataArray($arReturn);
        return $result;
    }
    /**
     * 
     * ������ id ������� ���������� �� ������� �������� � ��, ������� ���� ��� �� ���� ���������
     * 
     * @return int[]
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunAddToVkActionGetIds()
    {
        $arReturn = [];
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => ['ID'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), 'GOOD_ITEM.ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_ITEM', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['ID'];
        }
        return $arReturn;
    }
    /**
     * 
     * ���������� ������ �� id ������ ���������� ������ ����������� ��� ������� ��������
     * ������ true - ���� ����� ��������
     * ������ false - ���� ����� �������� �� ����� �� �������
     * @param $refId
     * @return bool
     * @throws ApiResponseException
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkActionAddByRefId($refId)
    {
        $arGoodReferenceExport = $this->goodReferenceExport()->getTable()->getById($refId)->fetch();
        if (!$arGoodReferenceExport) {
            $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.REFERENCE_ITEM_NOT_FOUND', ['#ID#' => $refId]));
            return true;
        }
        $preparedItem = $this->getPreparedItem($arGoodReferenceExport['PRODUCT_ID'], (array) $arGoodReferenceExport['OFFER_ID']);
        try {
            $arFields = $preparedItem->getFields();
            if ($arFields['price'] < 0.01) {
                $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.PRICE_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            if (!intval($arFields['main_photo_id'])) {
                $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.add', $arFields);
            $response = $resultApi->getData('response');
            $vkItemId = (int) $response['market_item_id'];
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // ����� ����������� ������ � ��������
            $this->deleteVkItemIdFromAllAlbums($vkItemId);
            $this->addVkItemIdToVkAlbums($vkItemId, $arVkAlbumIds);
            // �������
            $this->history()->append($preparedItem, $vkItemId);
            // �����
            $this->limit()->append($vkItemId);
            // ��������� ���� ��� ����� ��������
            $resultAddGoodExport = $this->goodExportTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $arGoodReferenceExport['PRODUCT_ID'], 'OFFER_ID' => $arGoodReferenceExport['OFFER_ID'], 'VK_ID' => $vkItemId, 'HASH' => $this->getHash($arFields, $arVkAlbumIds)]);
            $this->log()->ok($this->getMessage('EXPORT_RUN_ADD_TO_VK.ADDED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
        } catch (\VKapi\Market\Exception\ApiResponseException $ex) {
            if ($ex->is(\VKapi\Market\Api::ERROR_100) && preg_match('/\\:\\s+photo\\s+/', $ex->getMessage()) && isset($arFields)) {
                $arPhotoId = (array) $arFields['main_photo_id'];
                $arPhotoId = array_merge($arPhotoId, explode(',', $arFields['photo_ids']));
                $this->photo()->deleteByPhotoId($arPhotoId, $this->exportItem()->getGroupId());
            }
            $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.API_ERROR', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds()), '#MSG#' => $ex->getMessage()]));
            return false;
        }
        return true;
    }
    /**
     * 
     * ���������� ������� � ��������� � ������� ������ ������� c ������������ ������
     * @return \VKapi\Market\Result
     * @throws ApiResponseException
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkBaseMode()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunAddToVkBaseMode';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => 25,
                'added' => 0,
                'skipped' => 0,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        $isOverLimit = false;
        try {
            $state['count'] = $state['added'] + $this->exportRunAddToVkBaseModeActionGetCount();
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "k" . "" . "e" . "" . "" . "" . "" . "t") == constant("MODULE_DEMO_EXPIR" . "ED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VK" . "API.MARKET.DEMO_EX" . "P" . "I" . "R" . "E" . "D"), "BXMAKER_DEMO_EXPI" . "" . "RED");
            }
            while ($productId = $this->exportRunAddToVkBaseModeActionGetNext($state['skipped'])) {
                $this->manager()->checkTime();
                $this->limit()->check();
                $exported = $this->goodExportTable()->getCount();
                if (\CModule::IncludeModuleEx("vkapi.marke" . "t") === constant("MODULE_DE" . "M" . "O")) {
                    if ($exported >= 50) {
                        break;
                    }
                }
                if ($this->exportRunAddToVkBaseModeActionAdd($productId)) {
                    $state['added']++;
                } else {
                    $state['skipped']++;
                }
                // ���� ��� ����, ��
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        } catch (\VKapi\Market\Exception\GoodLimitException $limitException) {
            $isOverLimit = true;
        }
        // ������� ����������
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        if ($isOverLimit) {
            $state['complete'] = true;
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $arReturnResult = ['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_ADD_TO_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']])];
        if ($isOverLimit) {
            $arReturnResult['message'] = $this->getMessage('EXPORT_RUN_ADD_TO_VK_LIMIT.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']]);
        }
        $result->setDataArray($arReturnResult);
        return $result;
    }
    /**
     * 
     * ��������� � ������ ���������� �������, ������� �� ���������
     * @return int|mixed
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkBaseModeActionGetCount()
    {
        $count = 0;
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => [new \Bitrix\Main\ORM\Fields\ExpressionField('COUNT', 'COUNT(DISTINCT(%s))', ['PRODUCT_ID'])], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), 'GOOD_ITEM.ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_ITEM', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        if ($ar = $dbr->fetch()) {
            $count = $ar['COUNT'];
        }
        return $count;
    }
    /**
     * 
     * ������ ��������� �����, ������� ��� �� ��������
     * @param $offset - ���������� �����������, ������� �� ������ ���������
     * @return mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkBaseModeActionGetNext($offset)
    {
        $productId = null;
        \Bitrix\Main\Application::getConnection()->startTracker();
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => ['PRODUCT_ID'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), 'GOOD_ITEM.ID' => null], 'group' => ['PRODUCT_ID'], 'limit' => 1, 'offset' => $offset, 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_ITEM', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        if ($ar = $dbr->fetch()) {
            $productId = $ar['PRODUCT_ID'];
        }
        $n = \Bitrix\Main\Application::getConnection()->getTracker()->getQueries();
        return $productId;
    }
    /**
     * 
     * ���������� ����������� ������� � �� �� productId ���������� ������ ����������� ��� ������� ��������
     * ������ true - ���� ����� ��������
     * ������ false - ���� ����� �������� �� ����� �� �������
     * @param $productId
     * @return bool
     * @throws ApiResponseException
     * @throws BaseException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\Db\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkBaseModeActionAdd($productId)
    {
        // �������� ������
        $arRows = $this->exportRunAddToVkBaseModeActionAddGetRows($productId);
        if (empty($arRows)) {
            $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.REFERENCE_PRODUCT_ITEMS_NOT_FOUND', ['#ID#' => $productId]));
            return false;
        }
        // �������� �������� ����������
        $arOfferIds = array_column($arRows, 'OFFER_ID');
        $preparedItem = $this->getPreparedItem($productId, $arOfferIds);
        try {
            $arFields = $preparedItem->getFields();
            if ($arFields['price'] < 0.01) {
                $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.PRICE_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds())]));
                return false;
            }
            if (!(int) $arFields['main_photo_id']) {
                $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.add', $arFields);
            $response = $resultApi->getData('response');
            $vkItemId = (int) $response['market_item_id'];
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // ������
            $this->limit()->append($vkItemId);
            // �������
            $this->history()->append($preparedItem, $vkItemId);
            // ����� ����������� ������ � ��������
            $this->deleteVkItemIdFromAllAlbums($vkItemId);
            $this->addVkItemIdToVkAlbums($vkItemId, $arVkAlbumIds);
            // ��������� ���� ��� ����� ��������
            foreach ($preparedItem->getOfferIds() as $offerId) {
                $this->goodExportTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $offerId, 'VK_ID' => $vkItemId, 'HASH' => $this->getHash($arFields, $arVkAlbumIds)]);
            }
            $this->log()->ok($this->getMessage('EXPORT_RUN_ADD_TO_VK.ADDED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds())]));
        } catch (\VKapi\Market\Exception\ApiResponseException $ex) {
            if ($ex->is(\VKapi\Market\Api::ERROR_100) && preg_match('/\\:\\s+photo\\s+/', $ex->getMessage()) && isset($arFields)) {
                $arPhotoId = (array) $arFields['main_photo_id'];
                $arPhotoId = array_merge($arPhotoId, explode(',', $arFields['photo_ids']));
                $this->photo()->deleteByPhotoId($arPhotoId, $this->exportItem()->getGroupId());
            }
            $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.API_ERROR', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(',', $preparedItem->getOfferIds()), '#MSG#' => $ex->getMessage()]));
            return false;
        }
        return true;
    }
    /**
     * 
     * ������ ������ � �� ������ ��� �������� �� ������ ����������
     * @param $productId
     * @return array - [{"ID":"98","EXPORT_ID":"5","PRODUCT_ID":"4135","OFFER_ID":"5171","FLAG":"0","VK_ID":null,"HASH":null}, ...]
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunAddToVkBaseModeActionAddGetRows($productId)
    {
        $arReturn = [];
        // 'EXPORT_ID' => $this->exportItem()->getId(),
        $dbr = $this->goodReferenceExport()->getTable()->getList(['select' => ['*', 'VK_ID' => 'GOOD_ITEM.VK_ID', 'HASH' => 'GOOD_ITEM.HASH'], 'filter' => ['PRODUCT_ID' => $productId], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('GOOD_ITEM', '\\VKapi\\Market\\Good\\ExportTable', ['=this.PRODUCT_ID' => 'ref.PRODUCT_ID', '=this.OFFER_ID' => 'ref.OFFER_ID', '=ref.GROUP_ID' => new \Bitrix\Main\DB\SqlExpression('?i', $this->exportItem()->getGroupId())], ['join_type' => 'LEFT'])]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar;
        }
        return $arReturn;
    }
    /**
     * 
     * ������ ������ ��� ������ � ������������� �������, �������� �����, ������� � ������
     * @param $productId
     * @param int[] $arOfferId
     * @return Export\Item
     */
    public function getPreparedItem($productId, $arOfferId)
    {
        $item = new \VKapi\Market\Good\Export\Item($productId, $arOfferId, $this->exportItem());
        return $item;
    }
    /**
     * 
     * ������ ����� �� ���� �������� ����� ������
     * @param int $vkItemId - id ������
     * @param int[] $arVkAlbumId - ������ ID ��������
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function deleteVkItemIdFromNotAlbums($vkItemId, $arVkAlbumId)
    {
        $arExistVkAlbumId = $this->getAlbumIdInVkList();
        $arVkAlbumIdForDelete = array_diff($arExistVkAlbumId, $arVkAlbumId);
        if (count($arVkAlbumIdForDelete)) {
            $resultRequest = $this->exportItem()->connection()->method('market.removeFromAlbum', ['owner_id' => -1 * $this->exportItem()->getGroupId(), 'item_id' => $vkItemId, 'album_ids' => $arVkAlbumIdForDelete]);
        }
        return true;
    }
    /**
     * 
     * ������ ����� �� ���� �������� ����� ������
     * @param int $vkItemId - id ������
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function deleteVkItemIdFromAllAlbums($vkItemId)
    {
        $arAlbumIds = $this->getAlbumIdInVkList();
        if (empty($arAlbumIds)) {
            return true;
        }
        $resultRequest = $this->exportItem()->connection()->method('market.removeFromAlbum', ['owner_id' => -1 * $this->exportItem()->getGroupId(), 'item_id' => $vkItemId, 'album_ids' => $arAlbumIds]);
        return true;
    }
    /**
     * 
     * ��������� ����� � ������ �������
     * 
     * @param int $vkItemId - id ������
     * @param int[] $arVkAlbumId - ������ id ��������
     * 
     * @param $vkItemId
     * @param $arVkAlbumId
     * @return bool
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function addVkItemIdToVkAlbums($vkItemId, $arVkAlbumId)
    {
        if (count($arVkAlbumId)) {
            $resultRequest = $this->exportItem()->connection()->method('market.addToAlbum', ['owner_id' => -1 * $this->exportItem()->getGroupId(), 'item_id' => $vkItemId, 'album_ids' => $arVkAlbumId]);
        }
        return true;
    }
    /**
     * 
     * �������� ������� �� ��, ������� ������ ����������
     * � ��������� ������ ������������� ��� ��������
     * �������� ���� ��������� ����������, �����������,
     * ������ �� �������� ��� ������� ��������
     */
    public function exportRunDeleteOldFromVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunDeleteOldFromVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'deleted' => 0, 'arId' => []];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ����� ������������� �������� ������, ���� ���������
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // ��������� ���������
            $this->state()->setField($stateKey, $state)->save();
            // ���������� ������ ��������
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            if (empty($state['arId'])) {
                // �������� ������, ������� ���� � ������� ����������� �� ��� � ������� �������������
                $state['arId'] = $this->exportRunDeleteOldFromVKActionGetIdForDelete();
                $state['count'] = count($state['arId']);
                $this->state()->setField($stateKey, $state)->save();
            }
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi." . "mark" . "" . "" . "" . "" . "et") == constant("M" . "ODULE_DE" . "MO_EXPIRE" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "RKET.DEMO_EXPIRED"), "BXMAKER_" . "DEMO_" . "EXP" . "IR" . "E" . "D");
            }
            // ����
            while (count($state['arId']) > 0) {
                $this->manager()->checkTime();
                $part = array_slice($state['arId'], 0, $state['limit']);
                $arItems = [];
                $code = [];
                $dbr = $this->goodExportTable()->getList(['filter' => ['ID' => $part]]);
                while ($ar = $dbr->fetch()) {
                    $arItems[$ar['ID']] = $ar;
                    $code[] = '"' . $ar['ID'] . '" : API.market.delete({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"item_id" : "' . $ar['VK_ID'] . '"})';
                }
                // ������ ������ � �� ---
                if (count($code)) {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    foreach ($response as $rowId => $resultAction) {
                        if ($resultAction == 1) {
                            if (isset($arItems[$rowId])) {
                                // ������� ������
                                $this->goodExportTable()->delete($rowId);
                                // ������� ��������
                                $this->photo()->getTable()->deleteByProduct($arItems[$rowId]['PRODUCT_ID'], $arItems[$rowId]['OFFER_ID'], $this->exportItem()->getGroupId());
                                $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETED', ['#PRODUCT_ID#' => $arItems[$rowId]['PRODUCT_ID'], '#OFFER_ID#' => $arItems[$rowId]['OFFER_ID']]));
                                $state['deleted']++;
                            }
                            unset($arItems[$rowId]);
                        }
                    }
                    // ���� �������� ������� ���� ������� � ��
                    foreach ($arItems as $arItem) {
                        // ������� ������
                        $this->goodExportTable()->delete($arItem['ID']);
                        // ������� ��������
                        $this->photo()->getTable()->deleteByProduct($arItem['PRODUCT_ID'], $arItem['OFFER_ID'], $this->exportItem()->getGroupId());
                        $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETED', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID'], '#OFFER_ID#' => $arItem['OFFER_ID']]));
                        $state['deleted']++;
                    }
                }
                // ������ ������ � ������ ���������� id
                $state['offset'] += count($part);
                // ��������
                $state['arId'] = array_slice($state['arId'], $state['limit']);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * ������ ������ ID ������� � ����������� ������� � ��, ������� ���������� ������� �� ��,
     * ������ ��� ��� ����������� � �������������� ������ ��� ��������
     * 
     * �������� �������, ������� ������ �� ������ ����, ����� ����������
     * 
     * @return int[]
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunDeleteOldFromVKActionGetIdForDelete()
    {
        // ������� ��� �������������� �������� ��������, � ���� ������
        $arExportIds = $this->getActiveExportIds();
        $arReturn = [];
        $dbr = $this->goodExportTable()->getList(['select' => ['ID'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'EXPORT_REFERENCE.ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('EXPORT_REFERENCE', \VKapi\Market\Good\Reference\ExportTable::class, \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.PRODUCT_ID')->whereColumn('this.OFFER_ID', 'ref.OFFER_ID')->whereIn('ref.EXPORT_ID', $arExportIds), ['join_type' => \Bitrix\Main\ORM\Query\Join::TYPE_LEFT])]]);
        while ($arCount = $dbr->fetch()) {
            $arReturn[] = $arCount['ID'];
        }
        return $arReturn;
    }
    /**
     * 
     * �������� ������� �� ��, ������� ������ ����������
     * � ��������� ������ ������������� ��� ��������
     * �������� ���� ��������� ����������, �����������,
     * ������ �� �������� ��� ������� ��������
     * ��� ������� ������� ����������
     */
    public function exportRunDeleteOldFromVKBaseMode()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunDeleteOldFromVKBaseMode';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'deleted' => 0, 'arNeedDelete' => []];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ����� ������������� �������� ������, ���� ���������
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // ��������� ���������
            $this->state()->setField($stateKey, $state)->save();
            // ���������� ������ ��������
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // ������� ��� �������������� �������� ��������, � ���� ������
            $arExportIds = $this->getActiveExportIds();
            $state['count'] = $this->exportRunDeleteOldFromVKBaseModeActionGetCount($arExportIds);
            if (\CModule::IncludeModuleEx("vkapi.mark" . "" . "" . "e" . "" . "t") === constant("MODULE_DEM" . "O_EXP" . "" . "" . "IRE" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET." . "DEMO_EXPIR" . "E" . "D"), "BXMAKER_DEMO_EXPIRE" . "D");
            }
            // ����
            while ($arExportedItem = $this->exportRunDeleteOldFromVKBaseModeActionGetNext($arExportIds)) {
                $this->manager()->checkTime();
                $isHasMore = $this->exportRunDeleteOldFromVKBaseModeActionIsHashMore($arExportedItem['ID'], $arExportedItem['VK_ID'], $arExportIds);
                if (!$isHasMore) {
                    $state['arNeedDelete'][$arExportedItem['VK_ID']][] = $arExportedItem;
                }
                // ������ ������� ������ �� �����������
                $this->goodExportTable()->delete($arExportedItem['ID']);
                // ������� ��������� ��������? offerId = 0, ������ ��� ��� ������� ������ � ����������� ���� ����������� ��� 0
                $this->photo()->getTable()->deleteByProduct($arExportedItem['PRODUCT_ID'], 0, $this->exportItem()->getGroupId());
                // ������ ������ � ������ ���������� id
                $state['offset']++;
                if (count($state['arNeedDelete']) > 20) {
                    $state['deleted'] += $this->exportRunDeleteOldFromVKBaseModeActionDeleteInVkIds($state['arNeedDelete']);
                    $state['arNeedDelete'] = [];
                }
            }
            // ���� ���������� ������, �� ������� �� ��������� ������ ��� �����
            if (count($state['arNeedDelete']) > 0) {
                $state['deleted'] += $this->exportRunDeleteOldFromVKBaseModeActionDeleteInVkIds($state['arNeedDelete']);
                $state['arNeedDelete'] = [];
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * ������ ������ ID ������� � ����������� ������� � ��, ������� ���������� ������� �� ��,
     * ������ ��� ��� ����������� � �������������� ������ ��� ��������
     * 
     * �������� �������, ������� ������ �� ������ ����, ����� ����������
     * 
     * @return int
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunDeleteOldFromVKBaseModeActionGetCount($arExportIds)
    {
        $count = 0;
        $dbr = $this->goodExportTable()->getList(['select' => ['CNT'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'EXPORT_REFERENCE.ID' => null], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('EXPORT_REFERENCE', \VKapi\Market\Good\Reference\ExportTable::class, \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.PRODUCT_ID')->whereColumn('this.OFFER_ID', 'ref.OFFER_ID')->whereIn('ref.EXPORT_ID', $arExportIds), ['join_type' => \Bitrix\Main\ORM\Query\Join::TYPE_LEFT])]]);
        if ($ar = $dbr->fetch()) {
            $count = $ar['CNT'];
        }
        return (int) $count;
    }
    /**
     * 
     * ������ ������ � ������� � ���������� ������ � ��, ������� ���������� ������� �� ��,
     * ������ ��� �� ����������� � �������������� ������ ��� ��������
     * 
     * @return array|null - {"ID":"32","GROUP_ID":"208868957","PRODUCT_ID":"3926","OFFER_ID":"0","VK_ID":"5283465","HASH":"58052e3495acc0411e8f5ad2c87df117"}
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunDeleteOldFromVKBaseModeActionGetNext($arExportIds)
    {
        $arItem = null;
        $dbr = $this->goodExportTable()->getList(['select' => ['*'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'EXPORT_REFERENCE.ID' => null], 'limit' => 1, 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('EXPORT_REFERENCE', \VKapi\Market\Good\Reference\ExportTable::class, \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.PRODUCT_ID')->whereColumn('this.OFFER_ID', 'ref.OFFER_ID')->whereIn('ref.EXPORT_ID', $arExportIds), ['join_type' => \Bitrix\Main\ORM\Query\Join::TYPE_LEFT])]]);
        if ($ar = $dbr->fetch()) {
            $arItem = $ar;
        }
        return $arItem;
    }
    /**
     * 
     * �������� ���� �� � ������ ������ �� ������ ����������� ������� ����� �� ������������� ������ ���������
     * 
     * @return bool
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunDeleteOldFromVKBaseModeActionIsHashMore($id, $vkId, $arExportIds)
    {
        $bFind = false;
        $dbr = $this->goodExportTable()->getList(['select' => ['*'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'VK_ID' => $vkId, '!ID' => $id, '!EXPORT_REFERENCE.ID' => null], 'limit' => 1, 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('EXPORT_REFERENCE', \VKapi\Market\Good\Reference\ExportTable::class, \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.PRODUCT_ID')->whereColumn('this.OFFER_ID', 'ref.OFFER_ID')->whereIn('ref.EXPORT_ID', $arExportIds), ['join_type' => \Bitrix\Main\ORM\Query\Join::TYPE_LEFT])]]);
        if ($ar = $dbr->fetch()) {
            $bFind = true;
        }
        return $bFind;
    }
    /**
     * 
     * ������� ������ � ��, ���������� VK_ID �� ����� 25
     * @param $arVkIds
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\DB\SqlQueryException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     * 
     */
    public function exportRunDeleteOldFromVKBaseModeActionDeleteInVkIds($arVkIdToItems)
    {
        $deleted = 0;
        $arProductIds = [];
        foreach ($arVkIdToItems as $vkId => $arItems) {
            $arProductIds = array_merge($arProductIds, array_column($arItems, 'PRODUCT_ID'));
            $code[] = '"' . $vkId . '" : API.market.delete({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"item_id" : "' . $vkId . '"})';
        }
        // ������ ������ � �� ---
        if (count($code)) {
            try {
                $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                $response = $resultRequest->getData('response');
                $deleted += count($code);
                // ���� �������� ������� ���� ������� � ��
                foreach ($arVkIdToItems as $vkId => $arItems) {
                    foreach ($arItems as $arItem) {
                        // ������� ��������, � ������� ������ � ����������� ���� ����������� ��� 0
                        $this->photo()->getTable()->deleteByProduct($arItem['PRODUCT_ID'], 0, $this->exportItem()->getGroupId());
                    }
                    $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETED', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID'], '#OFFER_ID#' => implode(', ', array_column($arItems, 'OFFER_ID'))]));
                }
            } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                $this->log()->error($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETE_API_ERROR', ['#PRODUCT_ID#' => implode(', ', $arProductIds), '#MSG#' => $apiEx->getMessage()]));
            }
        }
        return $deleted;
    }
    /**
     * 
     * ������ ������ �������������� �������� �������� ��� ������� ������
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getActiveExportIds()
    {
        $arReturn = [];
        $dbr = $this->manager()->exportTable()->getList(['filter' => ['ACTIVE' => true, 'GROUP_ID' => $this->exportItem()->getGroupId()], 'select' => ['ID']]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['ID'];
        }
        return $arReturn;
    }
    /**
     * 
     * �������� ��������� ����������� ��� ������, ������� �����������, �������� �� ����� {PRODUCT_ID}_{OFFER_ID}
     * �������� ��������� ���������� �� ��, ������� ���� �������� ��������� � ���� � ����
     */
    public function exportRunDeleteLocalDoublesFormVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunDeleteLocalDoublesFormVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //���������
                'complete' => false,
                //������� ����������
                'percent' => 0,
                // �����
                'count' => 0,
                // ������
                'offset' => 0,
                // ����� �� ��������
                'limit' => 20,
                'deleted' => 0,
                'arId' => null,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ����� ������������� �������� ������, ���� ���������
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // ��������� ���������
            $this->state()->setField($stateKey, $state)->save();
            // ���������� ������ ��������
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // �������� ���������� ����������
            if (empty($state['arId'])) {
                $state['arId'] = $this->goodExportTable()->getDoublesIdByGroupId($this->exportItem()->getGroupId());
                $state['count'] = count($state['arId']);
            }
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "ke" . "t") == constant("MODULE_DE" . "MO" . "_EXP" . "IR" . "" . "" . "ED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "PIR" . "E" . "" . "" . "D"), "BXMAKER_DEMO_EXPI" . "" . "RE" . "" . "" . "" . "" . "" . "D");
            }
            // ����
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arId'], 0, $state['limit']);
                // ������ ������ � �� ---
                $code = [];
                $arItems = [];
                $dbr = $this->goodExportTable()->getList(['filter' => ['ID' => $arPart]]);
                while ($ar = $dbr->fetch()) {
                    $arItems[$ar['ID']] = $ar;
                    $code[] = '"' . $ar['ID'] . '" : API.market.delete({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"item_id" : "' . $ar['VK_ID'] . '"})';
                }
                $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                $response = $resultRequest->getData('response');
                foreach ($response as $rowId => $resultAction) {
                    if ($resultAction == 1) {
                        if (isset($arItems[$rowId])) {
                            $this->goodExportTable()->delete($arItems[$rowId]['ID']);
                            $state['deleted']++;
                            $state['offset']++;
                            $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.DELETED', ['#PRODUCT_ID#' => $arItems[$rowId]['PRODUCT_ID'], '#OFFER_ID#' => $arItems[$rowId]['OFFER_ID']]));
                        }
                    }
                    unset($arItems[$rowId]);
                }
                foreach ($arItems as $arItem) {
                    $state['deleted']++;
                    $state['offset']++;
                    $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.DELETED', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID'], '#OFFER_ID#' => $arItem['OFFER_ID']]));
                }
                // ������� ������������
                $state['arId'] = array_slice($state['arId'], $state['limit']);
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * �������� ������ � ��, ��������� ���� �� ����� � ������� �����������,
     * ���� ����, ������� �� ��
     */
    public function exportRunDeleteUnknownInVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunDeleteUnknownInVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 20, 'deleted' => 0, 'vkItems' => null, 'vkIds' => []];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // ���� ����� ������������� �������� ������, ���� ���������
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['deleted'] = 0;
            $state['offset'] = 0;
            $state['count'] = 0;
            $state['percent'] = 100;
            $state['complete'] = true;
            // ��������� ���������
            $this->state()->setField($stateKey, $state)->save();
            // ���������� ������ ��������
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_UNKNOWN_IN_VK.DISABLED')]);
            return $result;
        }
        try {
            // �������� ������ ������� � ��
            if (empty($state['vkIds'])) {
                $state['vkIds'] = $this->getVkItemIdList($state['vkItems']);
                $state['count'] = count($state['vkIds']);
            }
            while (count($state['vkIds']) > 0) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['vkIds'], 0, $state['limit']);
                $arPartIds = array_combine($arPart, $arPart);
                // ��������� ����� ������ ��� ��������
                $dbr = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => intval($this->exportItem()->getGroupId()), 'VK_ID' => $arPart]]);
                while ($ar = $dbr->fetch()) {
                    unset($arPartIds[$ar['VK_ID']]);
                }
                // ������� ����������
                // ������ ������ � �� ---
                if (count($arPartIds) > 0) {
                    $code = [];
                    foreach ($arPartIds as $vkId) {
                        $code[] = '"' . $vkId . '" : API.market.delete({"owner_id" : -' . $this->exportItem()->getGroupId() . ',"item_id" : "' . $vkId . '"})';
                    }
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $state['deleted'] += count($arPartIds);
                }
                $state['vkIds'] = array_slice($state['vkIds'], $state['limit']);
                $state['offset'] += count($arPart);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        if (\Bitrix\Main\Loader::includeSharewareModule("vkap" . "i" . "." . "" . "marke" . "" . "" . "t") === constant("MODULE" . "_DEMO_E" . "" . "XPIRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "PI" . "RE" . "" . "D"), "BXM" . "AKER_DEMO_EXPIR" . "E" . "" . "" . "" . "" . "" . "D");
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['vkItems']);
            unset($state['vkIds']);
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_UNKNOWN_IN_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * ����������� � ������ � ������������ �������
     */
    public function exportRunGroupUngroupItem()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunGroupUngroupItem';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'grouped' => 0, 'arId' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            if (empty($state['arId'])) {
                $state['arId'] = $this->exportRunGroupUngroupItemActionGetProductIds();
                $state['count'] = count($state['arId']);
            }
            // ���������� ��� �������� ������
            if (!$this->exportItem()->isEnabledExtendedGoods()) {
                $state['count'] = 0;
                $state['offset'] = 0;
                $state['arId'] = [];
            }
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $productId = $state['arId'][0];
                $arItems = [];
                // ��������� ����� ������ ��� ��������
                $dbr = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $productId]]);
                while ($ar = $dbr->fetch()) {
                    $arItems[] = $ar;
                }
                $arVkIds = array_column($arItems, 'VK_ID');
                try {
                    if ($this->exportItem()->isEnabledOfferCombine()) {
                        // ���������� ������ ���� ���������� ������ 1
                        if (count($arVkIds) > 1) {
                            $arCode = [];
                            $arCode[] = ' API.market.ungroupItems({group_id : ' . $this->exportItem()->getGroupId() . ', item_group_id: ' . $productId . ' })';
                            $arCode[] = ' API.market.groupItems({group_id : ' . $this->exportItem()->getGroupId() . ', item_group_id: ' . $productId . ', item_ids: "' . implode(',', $arVkIds) . '" })';
                            $resultApi = $this->exportItem()->connection()->method('execute', ['code' => 'return [' . implode(',', $arCode) . '];']);
                            $executeErrors = $resultApi->getData('execute_errors');
                            if (isset($executeErrors[0])) {
                                throw new \VKapi\Market\Exception\ApiResponseException($executeErrors[0]);
                            }
                            $state['grouped']++;
                            $this->log()->ok($this->getMessage('EXPORT_RUN_GROUP_UNGROUP_ITEM.GROUPPED', ['#PRODUCT_ID#' => $productId, '#OFFER_ID#' => implode(',', array_column($arItems, 'OFFER_ID'))]));
                        }
                    } else {
                        $resultApi = $this->exportItem()->connection()->method('market.ungroupItems', ['group_id' => $this->exportItem()->getGroupId(), 'item_group_id' => $productId]);
                        $this->log()->ok($this->getMessage('EXPORT_RUN_GROUP_UNGROUP_ITEM.UNGROUPPED', ['#PRODUCT_ID#' => $productId, '#OFFER_ID#' => implode(',', array_column($arItems, 'OFFER_ID'))]));
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('EXPORT_RUN_GROUP_UNGROUP_ITEM.ERROR', ['#PRODUCT_ID#' => $productId, '#OFFER_ID#' => implode(',', array_column($arItems, 'OFFER_ID')), '#MSG#' => $apiEx->getMessage()]));
                }
                $state['offset']++;
                array_shift($state['arId']);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // ����������
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // ��������� ���������
        $this->state()->setField($stateKey, $state)->save();
        // ���������� ������ ��������
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_GROUP_UNGROUP_ITEM.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#GROUPED#' => $state['grouped']])]);
        return $result;
    }
    /**
     * 
     * ������ ���������� ���������� ������� � ��������� �������������
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunGroupUngroupItemActionGetProductIds()
    {
        $arReturn = [];
        $dbr = $this->goodExportTable()->getList(['select' => ['PRODUCT_ID'], 'filter' => ['!OFFER_ID' => 0, 'GROUP_ID' => $this->exportItem()->getGroupId(), '!EXPORT_REFERENCE.ID' => null], 'group' => ['PRODUCT_ID'], 'runtime' => [new \Bitrix\Main\Entity\ReferenceField('EXPORT_REFERENCE', \VKapi\Market\Good\Reference\ExportTable::class, \Bitrix\Main\ORM\Query\Query::filter()->whereColumn('this.PRODUCT_ID', 'ref.PRODUCT_ID')->whereColumn('this.OFFER_ID', 'ref.OFFER_ID')->where('ref.EXPORT_ID', $this->exportItem()->getId()), ['join_type' => \Bitrix\Main\ORM\Query\Join::TYPE_LEFT])]]);
        while ($ar = $dbr->fetch()) {
            $arReturn[] = $ar['PRODUCT_ID'];
        }
        return $arReturn;
    }
    /**
     * 
     * ������ ������ ��������������� �������� � ���������
     * 
     * @return array|mixed
     */
    public function getAlbumIdInVkList($bRefresh = false)
    {
        static $list = [];
        if (!isset($list[$this->exportItem()->getGroupId()]) || $bRefresh) {
            $list[$this->exportItem()->getGroupId()] = [];
            $resultRequest = $this->exportItem()->connection()->method('market.getAlbums', ['owner_id' => '-' . $this->exportItem()->getGroupId(), 'count' => 100]);
            if ($resultRequest->isSuccess()) {
                $result = $resultRequest->getData('response');
                if (!is_null($result) && isset($result['items'])) {
                    foreach ($result['items'] as $item) {
                        if (in_array($item['id'], [0, -1])) {
                            continue;
                        }
                        $list[$this->exportItem()->getGroupId()][] = $item['id'];
                    }
                }
            }
        }
        return $list[$this->exportItem()->getGroupId()];
    }
    /**
     * 
     * ������ ������ �������� ����������� � �� ��� ���������� ������ �� ������� ��������
     * 
     * @return array {albumId => vkId, ...}
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getAlbumsInVk()
    {
        if (is_null($this->arAlbumsInVk)) {
            $this->arAlbumsInVk = [];
            $arVkId = [];
            // ��������� ��������� �� �� ----------------
            $resultVkAlbums = $this->albumExport()->getVkAlbums();
            if ($resultVkAlbums->isSuccess()) {
                foreach ($resultVkAlbums->getData('items') as $item) {
                    $arVkId[$item['id']] = $item['id'];
                }
            } else {
                $this->log()->notice($this->getMessage('GET_ALBUMS_IN_VK_ERROR', ['#MSG#' => $resultVkAlbums->getFirstErrorMessage(), '#CODE#' => $resultVkAlbums->getFirstErrorCode()]));
            }
            // ����� ������������ ������� -----
            $dbr = $this->albumExport()->albumExportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId()]]);
            while ($ar = $dbr->fetch()) {
                if (!!$ar['VK_ID'] && isset($arVkId[$ar['VK_ID']])) {
                    $this->arAlbumsInVk[$ar['ALBUM_ID']] = $ar['VK_ID'];
                }
            }
        }
        return $this->arAlbumsInVk;
    }
    /**
     * 
     * ������ ������ � ����������� ���������� ������ ������� � ��
     * 
     * @return \VKapi\Market\Result
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\LoaderException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getPreviewForVk($bOffer = false)
    {
        $result = new \VKapi\Market\Result();
        $oExport = new \VKapi\Market\Export();
        // �������� ������ ����������� ��������
        $parseResult = $oExport->parseExportDataFromPostData();
        if ($parseResult->isSuccess()) {
            $this->exportItem()->setData($parseResult->getData('FIELDS'));
        } else {
            return $parseResult;
        }
        // ��������� ������ ---
        $productId = $this->exportItem()->getProductIdForPreview();
        $offerId = $this->exportItem()->getOfferIdForPreview();
        $this->exportItem()->setPreviewMode(true);
        $arOfferIds = [];
        if (\Bitrix\Main\Loader::includeSharewareModule("vkap" . "i.marke" . "t") == constant("MODULE_DEMO_E" . "X" . "PIRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "RKET.DEMO_EXPIRE" . "D"), "BXMAKER_D" . "EMO_EX" . "P" . "IR" . "E" . "D");
        }
        if ($bOffer) {
            if (!$offerId) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_ID'), 'ERROR_OFFER_ID');
            }
            $arOffer = $this->iblockElementOld()->GetList([], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'ID' => $offerId], false, ['nTopCount' => 1], ['ID', 'IBLOCK_ID', 'PROPERTY_' . $this->exportItem()->getLinkPropertyId()])->Fetch();
            if (!$arOffer) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_ID_NOT_FOUND'), 'ERROR_OFFER_ID_NOT_FOUND');
            }
            // ���� ������������ id
            $productId = intval($arOffer['PROPERTY_' . $this->exportItem()->getLinkPropertyId() . '_VALUE']);
            if (!$productId) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_PRODUCT_ID_NOT_FOUND'), 'ERROR_OFFER_PRODUCT_ID_NOT_FOUND');
            }
            // ��������� ������� ��������� ������ ----------------
            $dbrElement = $this->iblockElementOld()->GetList([], ['IBLOCK_ID' => $this->exportItem()->getProductIblockId(), 'ID' => $productId], false, ['nTopCount' => 1], ['ID', 'IBLOCK_ID']);
            if (!$dbrElement->Fetch()) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_PRODUCT_ID_NOT_FOUND'), 'ERROR_OFFER_PRODUCT_ID_NOT_FOUND');
            }
            // ���� ��� �����
            $arOfferIds[] = $offerId;
            $dbr = $this->iblockElementOld()->GetList([], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'PROPERTY_' . $this->exportItem()->getLinkPropertyId() => $productId], false, ['nTopCount' => 3], ['ID']);
            while ($ar = $dbr->Fetch()) {
                if (!in_array($ar['ID'], $arOfferIds)) {
                    $arOfferIds[] = $ar['ID'];
                }
            }
        } else {
            $offerId = 0;
            if (!$productId) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_PRODUCT_ID'), 'ERROR_PRODUCT_ID');
            }
            $dbrElement = $this->iblockElementOld()->GetList([], ['IBLOCK_ID' => $this->exportItem()->getProductIblockId(), 'ID' => $productId], false, ['nTopCount' => 1], ['ID', 'IBLOCK_ID']);
            if (!$dbrElement->Fetch()) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_PRODUCT_ID_NOT_FOUND'), 'ERROR_PRODUCT_ID_NOT_FOUND');
            }
        }
        $preparedItem = new \VKapi\Market\Good\Export\Item($productId, $arOfferIds, $this->exportItem());
        $result->setDataArray(array_merge($preparedItem->getFields(), ['isOffer' => $preparedItem->isOffer()]));
        return $result;
    }
}
?>