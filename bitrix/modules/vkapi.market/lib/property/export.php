<?php

namespace VKapi\Market\Property;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Exception\TimeoutException;
use VKapi\Market\Exception\ApiResponseException;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\ResponseErrorException;
use VKapi\Market\Exception\ORMException;
use VKapi\Market\Export\Item;
use VKapi\Market\Result;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * ������ c ������������ ����������
 * Class Export
 * 
 * @package VKapi\Market\Property
 */
class Export
{
    /**
 * @var array - �������� ���������� �������� (��������)
 */
    protected $arExportData = [];
    /**
 * @var \VKapi\Market\Connect - ����������� ��� ����������� �������� � ��
 */
    protected $oConnection = null;
    /**
 * @var \VKapi\Market\Export\Log �����������
 */
    protected $oLog;
    /**
 * @var \VKapi\Market\State ���������
 */
    protected $oState = null;
    /**
 * @var \VKapi\Market\Property\Property
 */
    protected $oProperty = null;
    /**
 * @var \VKapi\Market\Export\Item
 */
    protected $oExportItem = null;
    /**
 * @param \VKapi\Market\Export\Item $oExportItem
 */
    public function __construct(\VKapi\Market\Export\Item $oExportItem)
    {
        $this->oExportItem = $oExportItem;
    }
    /**
 * @param $name
 * @param null $arReplace
 * 
 * @return string
 */
    public function getMessage($name, $arReplace = null)
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.PROPERTY.EXPORT.' . $name, $arReplace);
    }
    /**
 * @return Property
 */
    public function property()
    {
        if (is_null($this->oProperty)) {
            $this->oProperty = new \VKapi\Market\Property\Property();
        }
        return $this->oProperty;
    }
    /**
 * @return \VKapi\Market\Manager
 */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
 * ������ ������ ��� �������� ���������
 * 
 * @return \VKapi\Market\Export\Log
 */
    public function log()
    {
        if (empty($this->oLog)) {
            $this->oLog = new \VKapi\Market\Export\Log(\VKapi\Market\Manager::getInstance()->getLogLevel());
            $this->oLog->setExportId($this->exportItem()->getId());
        }
        return $this->oLog;
    }
    /**
 * ������ ������ ��� ������ � ���������� ���������
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
 * @return \VKapi\Market\State
 */
    public function state()
    {
        if (is_null($this->oState)) {
            $this->oState = new \VKapi\Market\State('export_' . intval($this->exportItem()->getId()), '/property');
        }
        return $this->oState;
    }
    /**
 * ���� � ����������
 * @return array|mixed
 */
    public function getSteps()
    {
        $data = $this->state()->get();
        if (isset($data['steps'])) {
            return $data['steps'];
        }
        return [];
    }
    /**
 * ������� ����������
 * @return int
 */
    public function getPercent()
    {
        $data = $this->state()->get();
        return $this->state()->calcPercentByData($data);
    }
    public function isComplete()
    {
        $data = $this->state()->get();
        if (array_key_exists('complete', $data)) {
            return $data['complete'];
        }
        return false;
    }
    /**
 * ������ �������� ������� ��� ����
 * @throws BaseException
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function exportRun()
    {
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
            ]]);
            $data = $this->state()->get();
            $this->log()->notice($this->getMessage('STARTED'));
        }
        // ���� ������� �����, �� ����������
        if (!$this->exportItem()->isEnabledExtendedGoods()) {
            $this->log()->notice($this->getMessage('DISABLED_EXTENDED_GOODS', ['#STEP#' => 1, '#STEP_NAME#' => $data['steps'][1]['name']]));
            foreach ($data['steps'] as &$step) {
                $step['percent'] = 100;
            }
            unset($step);
            // �����������
            $this->state()->set(['run' => false, 'steps' => $data['steps'], 'complete' => true, 'percent' => 100])->save();
            return true;
        }
        // ��������� ������
        $this->state()->set(['run' => true, 'timeStart' => time()])->save();
        try {
            switch ($data['step']) {
                case 1:
                    $this->exportItem()->checkApiAccess();
                    $data['step']++;
                    $data['steps'][1]['percent'] = 100;
                    $this->log()->notice($this->getMessage('STEP.OK', ['#STEP#' => 1, '#STEP_NAME#' => $data['steps'][1]['name']]));
                    break;
                case 2:
                    if (\Bitrix\Main\Loader::includeSharewareModule("v" . "kapi.m" . "arket") === constant("MODULE_DEMO_EXPIRED")) {
                        throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "PIRE" . "" . "" . "" . "" . "D"), "BXMAKER_DEMO_EXP" . "IRE" . "D");
                    }
                    // �������� ����� �������
                    $resultAction = $this->exportRunExportProperties();
                    if ($resultAction->isSuccess()) {
                        if ($resultAction->getData('complete')) {
                            $data['step']++;
                            $data['steps'][2]['percent'] = 100;
                            $data['steps'][2]['name'] = $this->getMessage('STEP2');
                            $this->log()->notice($this->getMessage('STEP.OK', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name']]));
                        } else {
                            $data['steps'][2]['percent'] = $resultAction->getData('percent');
                            $data['steps'][2]['name'] = $resultAction->getData('name');
                            $this->log()->notice($this->getMessage('STEP.PROCESS', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name'], '#PERCENT#' => $data['steps'][2]['percent']]));
                        }
                    } else {
                        $data['steps'][2]['error'] = true;
                        $result = $resultAction;
                    }
                    break;
                case 3:
                    if (\CModule::IncludeModuleEx("vkapi.marke" . "t") === constant("MODULE_DEMO_EXPIR" . "E" . "D")) {
                        throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXM" . "AKER_DE" . "MO_" . "EXPIRE" . "D");
                    }
                    // �������� ����� �������
                    $resultAction = $this->exportRunExportVariants();
                    if ($resultAction->isSuccess()) {
                        if ($resultAction->getData('complete')) {
                            $data['step']++;
                            $data['steps'][3]['percent'] = 100;
                            $data['steps'][3]['name'] = $this->getMessage('STEP3');
                            $this->log()->notice($this->getMessage('STEP.OK', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name']]));
                        } else {
                            $data['steps'][3]['percent'] = $resultAction->getData('percent');
                            $data['steps'][3]['name'] = $resultAction->getData('name');
                            $this->log()->notice($this->getMessage('STEP.PROCESS', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name'], '#PERCENT#' => $data['steps'][3]['percent']]));
                        }
                    } else {
                        $data['steps'][3]['error'] = true;
                        $result = $resultAction;
                    }
                    break;
                default:
                    $data['percent'] = 100;
                    $data['complete'] = true;
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            $this->log()->error($ex->getMessage(), $ex->getCustomData());
        }
        // ������� ���������� �������
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] == 100) {
            $data['complete'] = true;
            $this->log()->notice($this->getMessage('COMPLETE'));
        }
        // �����������
        $this->state()->set(['run' => false, 'step' => $data['step'], 'steps' => $data['steps'], 'complete' => $data['complete'], 'percent' => $data['percent']])->save();
        if (isset($ex) && $ex instanceof \VKapi\Market\Exception\ApiResponseException) {
            throw $ex;
        }
    }
    /**
 * ������ ������ ������� � ��
 * @return array
 */
    public function getPropertiesFromVk()
    {
        $arReturn = [];
        try {
            $resultRequest = $this->exportItem()->connection()->method('market.getProperties', ["group_id" => $this->exportItem()->getGroupId()]);
            $response = $resultRequest->getData('response');
            $arReturn = $response['items'];
        } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
            $this->log()->error($this->getMessage('GET_PROPERTIES_FROM_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
            if ($apiEx->is(\VKapi\Market\Api::ERROR_1409)) {
                throw $apiEx;
            }
        }
        return $arReturn;
    }
    /**
 * �������� ����� ����������� �������� �������� ������������� � ��
 * @return Result {name:string, percent:int, complete:bool}
 * @throws ResponseErrorException
 */
    public function exportRunExportProperties()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        $stateKey = 'exportProperties';
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'name' => '', 'step' => 1, 'steps' => [1 => ['percent' => 0, 'name' => $this->getMessage('EXPORT_PROPERTIES.STEP1')], 2 => ['percent' => 0, 'name' => $this->getMessage('EXPORT_PROPERTIES.STEP2')]]];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        switch ($state['step']) {
            case '1':
                $result = $this->exportPropertiesActionDelete();
                $state['name'] = $state['steps'][1]['name'];
                $state['steps'][1]['percent'] = $result->getData('percent');
                if ($result->getData('complete')) {
                    $state['step']++;
                } else {
                    $state['name'] .= ' ' . $state['steps'][1]['percent'] . '%';
                }
                break;
            case '2':
                $result = $this->exportPropertiesActionAdd();
                $state['name'] = $state['steps'][2]['name'];
                $state['steps'][2]['percent'] = $result->getData('percent');
                if ($result->getData('complete')) {
                    $state['step']++;
                } else {
                    $state['name'] .= ' ' . $state['steps'][2]['percent'] . '%';
                }
                break;
            default:
                $state['complete'] = true;
        }
        // ������� ���������� �������
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // �������
        $this->state()->setField($stateKey, $state)->save();
        return $result->setDataArray(['name' => $state['name'], 'percent' => $state['percent'], 'complete' => $state['complete']]);
    }
    /**
 * @return Result {complete: bool, percent:int}
 */
    public function exportPropertiesActionDelete()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportPropertiesActionDelete';
        $data = $this->state()->get();
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = ['complete' => false, 'count' => 0, 'offset' => 0, 'percent' => 0];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            if (!isset($state['properties'])) {
                $state['properties'] = $this->getPropertiesFromVk();
                $this->state()->setField($stateKey, $state)->save();
            }
            // ������� �������� ���������� � ���� ������
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedPropertiesVkId = array_column($arExportedProperties, 'VK_PROPERTY_ID');
            $arExportedPropertiesVkIdToPropertyId = array_column($arExportedProperties, 'PROPERTY_ID', 'VK_PROPERTY_ID');
            // �������� � ��
            $arVkProperties = $state['properties'];
            $arVkPropertiesId = array_column($arVkProperties, 'id');
            // ������ �������� �������� ������� ��� � �� (������� � ��) ---------------------------------
            $arNeedLocalDeleteVkId = array_diff($arExportedPropertiesVkId, $arVkPropertiesId);
            if (count($arNeedLocalDeleteVkId)) {
                $arNeedLocalDeleteVkIdFlip = array_flip($arNeedLocalDeleteVkId);
                $arPropertyIdForDelete = array_intersect_key($arExportedPropertiesVkIdToPropertyId, $arNeedLocalDeleteVkIdFlip);
                $this->property()->deleteByGroupIdPropertyId($this->exportItem()->getGroupId(), array_values($arPropertyIdForDelete));
            }
            // ������� ������ �������� (������� � ���������� ��������) --------------------------------------------------
            $arNeedPropertiesId = $this->getNeedExistsPropertyId();
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedPropertiesId = array_column($arExportedProperties, 'PROPERTY_ID');
            $arNeedDeleteId = array_diff($arExportedPropertiesId, $arNeedPropertiesId);
            if (count($arNeedDeleteId)) {
                $this->property()->deleteByGroupIdPropertyId($this->exportItem()->getGroupId(), array_values($arNeedDeleteId));
            }
            // ������ ������ � �� (������� � ���������� ��������) -----------------
            // ���������
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedVkId = array_column($arExportedProperties, 'VK_PROPERTY_ID');
            $arExportedVkIdToId = array_column($arExportedProperties, 'PROPERTY_ID', 'VK_PROPERTY_ID');
            // �� ������ ���� � ��
            $arVkId = array_column($arVkProperties, 'id');
            $arNeedVkDeleteVkId = array_diff($arVkId, $arExportedVkId);
            $state['count'] = count($arNeedVkDeleteVkId);
            $arNeedVkDeleteVkId = array_slice($arNeedVkDeleteVkId, $state['offset']);
            while (count($arNeedVkDeleteVkId)) {
                $this->manager()->checkTime();
                $propertyId = array_shift($arNeedVkDeleteVkId);
                try {
                    $resultApi = $this->exportItem()->connection()->method('market.deleteProperty', ['group_id' => $this->exportItem()->getGroupId(), 'property_id' => $propertyId]);
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('EXPORT_PROPERTIES_ACTION_DELETE.ERROR', ['#MSG#' => $apiEx->getMessage(), '#PROPERTY_ID#' => $arExportedVkIdToId[$propertyId], '#VK_PROPERTY_ID#' => $propertyId]));
                }
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
        }
        // ���������
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
            unset($state['properties']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent']]);
        return $result;
    }
    /**
 * @return Result {complete: bool, percent:int}
 */
    public function exportPropertiesActionAdd()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportPropertiesActionAdd';
        $data = $this->state()->get();
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = ['count' => 0, 'offset' => 0, 'percent' => 0];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            // ��������� ��������� ������
            if (!isset($state['properties'])) {
                $state['properties'] = $this->getPropertiesFromVk();
                $this->state()->setField($stateKey, $state)->save();
            }
            // �������� ����������� ��������
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedPropertiesId = array_column($arExportedProperties, 'PROPERTY_ID');
            // �������� �������� ������� ������ ����
            $arNeedExistsPropertyId = $this->getNeedExistsPropertyId();
            // ��������� ������� ����� ���������
            $arNotFoundPropertyId = array_diff($arNeedExistsPropertyId, $arExportedPropertiesId);
            $state['count'] = count($arNotFoundPropertyId);
            $arNeedAdd = array_slice($arNotFoundPropertyId, $state['offset']);
            while (count($arNeedAdd)) {
                $this->manager()->checkTime();
                $propertyId = array_shift($arNeedAdd);
                $this->addPropertyToVk($propertyId);
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
        }
        // ���������
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
            unset($state['properties']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent']]);
        return $result;
    }
    /**
 * ������� � �� ���������� ��������
 * @param $propertyId
 */
    public function addPropertyToVk($propertyId)
    {
        $ar = \CIBlockProperty::GetByID($propertyId)->fetch();
        if (!$ar) {
            $this->log()->error($this->getMessage('ADD_PROPERTY_TO_VK.NOT_FOUND', ['#ID#' => $propertyId]));
            return true;
        }
        // ��������� � ��
        $arFields = ['group_id' => $this->exportItem()->getGroupId(), 'title' => $ar['NAME'], 'type' => 'text'];
        try {
            $result = $this->exportItem()->connection()->method('market.addProperty', $arFields);
            $response = $result->getData('response');
            if ($response['property_id']) {
                // �������� ������������� � ��������� ���
                $this->property()->table()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'PROPERTY_ID' => $propertyId, 'VK_PROPERTY_ID' => intval($response['property_id'])]);
                $this->log()->ok($this->getMessage('ADD_PROPERTY_TO_VK.OK', ['#ID#' => $propertyId . ' ' . $ar['NAME'], '#VK_ID#' => intval($response['property_id'])]));
            }
        } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
            $this->log()->error($this->getMessage('ADD_PROPERTY_TO_VK.ERROR', ['#MSG#' => $apiEx->getMessage(), '#ID#' => $propertyId . ' ' . $ar['NAME']]));
        }
        return true;
    }
    /**
 * �������� ����� ����������� �������� ������� � ������� ������������� � ��
 * @return Result {name:string, percent:int, complete:bool}
 * @throws ResponseErrorException
 */
    public function exportRunExportVariants()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        $stateKey = 'exportVariants';
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = ['complete' => false, 'percent' => 0, 'name' => '', 'step' => 1, 'steps' => [1 => ['percent' => 0, 'name' => $this->getMessage('EXPORT_VARIANTS.STEP1')], 2 => ['percent' => 0, 'name' => $this->getMessage('EXPORT_VARIANTS.STEP2')]]];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        switch ($state['step']) {
            case '1':
                $result = $this->exportRunExportVariantsActionUpdate();
                $state['name'] = $state['steps'][1]['name'];
                $state['steps'][1]['percent'] = $result->getData('percent');
                if ($result->getData('complete')) {
                    $state['step']++;
                } else {
                    $state['name'] .= ' - ' . $result->getData('name');
                }
                break;
            case '2':
                if (\CModule::IncludeModuleEx("vkapi.market") == constant("MODUL" . "E_DEMO_EXP" . "" . "IRE" . "" . "" . "" . "" . "" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPI" . "" . "" . "R" . "" . "ED"), "B" . "XMA" . "KER_DEMO" . "" . "_EXPI" . "RE" . "D");
                }
                $result = $this->exportRunExportVariantsActionAdd();
                $state['name'] = $state['steps'][2]['name'];
                $state['steps'][2]['percent'] = $result->getData('percent');
                if ($result->getData('complete')) {
                    $state['step']++;
                } else {
                    $state['name'] .= ' - ' . $result->getData('name');
                }
                break;
            default:
                $state['complete'] = true;
        }
        // ������� ���������� �������
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // �������
        $this->state()->setField($stateKey, $state)->save();
        return $result->setDataArray(['name' => $state['name'], 'percent' => $state['percent'], 'complete' => $state['complete']]);
    }
    /**
 * @return Result {complete: bool, percent:int}
 */
    public function exportRunExportVariantsActionUpdate()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportVariantsActionUpdate';
        $data = $this->state()->get();
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = [
                'complete' => false,
                'percent' => 0,
                'count' => 0,
                'offset' => 0,
                // ������ �� ���������
                'subCount' => 0,
                // �������� ��������
                'subOffset' => 0,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            // ��������� ��������
            $state['properties'] = $this->getPropertiesFromVk();
            $this->state()->setField($stateKey, $state)->save();
            $arVkPropertyIdToVariants = array_column($state['properties'], 'variants', 'id');
            // �������� ����������� ��������
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedPropertiesId = array_column($arExportedProperties, 'PROPERTY_ID');
            $arProperties = $this->property()->getIblockPropertiesById($arExportedPropertiesId);
            $arPropertiesNames = array_column($arProperties, 'NAME', 'ID');
            $state['count'] = count($arExportedProperties);
            // ���������� ������������
            $arExportedPropertiesWait = array_slice($arExportedProperties, $state['offset']);
            // ������� �������� ������� ���� � ��
            $offset = 0;
            while (count($arExportedPropertiesWait)) {
                $this->manager()->checkTime();
                // ����� �������� �������� �������� ����� ���������
                $arPropertyWait = array_shift($arExportedPropertiesWait);
                // �������� ���� �� ����� �������� � ��
                if (!isset($arVkPropertyIdToVariants[$arPropertyWait['VK_PROPERTY_ID']])) {
                    $state['offset']++;
                    continue;
                }
                // ������ ��������� ��� ��
                $arVkVariants = $arVkPropertyIdToVariants[$arPropertyWait['VK_PROPERTY_ID']];
                // ������ ��������� ��������� �� ��������� �������
                $arLocalVariants = $this->property()->getPropertyVariants($arPropertyWait['PROPERTY_ID']);
                $arLocalVariantsIdToName = array_column($arLocalVariants, 'NAME', 'ID');
                $state['subCount'] = count($arVkVariants);
                $arVkVariantsNotCheck = array_slice($arVkVariants, $state['subOffset']);
                // ��������� �������
                while (count($arVkVariantsNotCheck)) {
                    $this->manager()->checkTime();
                    $arVkVariant = array_shift($arVkVariantsNotCheck);
                    // ���� ����� ������� ������ �� ��������, ������ ��������
                    if (!isset($arLocalVariantsIdToName[$arVkVariant['value']])) {
                        $state['subOffset']++;
                        continue;
                    }
                    // ���� �������� �� ����������, ����������
                    if ($arLocalVariantsIdToName[$arVkVariant['value']] == $arVkVariant['title']) {
                        $state['subOffset']++;
                        continue;
                    }
                    // ������ �������
                    try {
                        $resultApi = $this->exportItem()->connection()->method('market.editPropertyVariant', ['group_id' => $this->exportItem()->getGroupId(), 'variant_id' => $arVkVariant['id'], 'title' => $arLocalVariantsIdToName[$arVkVariant['value']], 'value' => $arVkVariant['value']]);
                    } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                        $this->log()->error($this->getMessage('ERROR_EXPORT_VARIANT_ACTION_UPDATE', ['#MSG#' => $apiEx->getMessage(), '#PROPERTY_ID#' => $arPropertyWait['PROPERTY_ID'], '#VARIANT_ID#' => $arVkVariant['value']]));
                    }
                    $state['subOffset']++;
                }
                $state['name'] = $arPropertiesNames[$arPropertyWait['PROPERTY_ID']] ?? '[' . $arPropertyWait['PROPERTY_ID'] . ']';
                $state['name'] .= ' ';
                $state['name'] .= $this->state()->calcPercent($state['subCount'], $state['subOffset']) . '%';
                // ���� ������ ��� ��������
                if ($state['subOffset'] >= $state['subCount']) {
                    $state['subOffset'] = 0;
                    $state['subCount'] = 0;
                    $state['offset']++;
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
        }
        // ���������
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
            unset($state['properties']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������
        $result->setDataArray(['name' => $state['name'], 'complete' => $state['complete'], 'percent' => $state['percent']]);
        return $result;
    }
    /**
 * @return Result {complete: bool, percent:int}
 */
    public function exportRunExportVariantsActionAdd()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportVariantsActionAdd';
        $data = $this->state()->get();
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = ['count' => 0, 'offset' => 0, 'percent' => 0, 'complete' => false, 'subCount' => 0, 'subOffset' => 0, 'name' => ''];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            $state['properties'] = $this->getPropertiesFromVk();
            $arVkPropertyIdToVariants = array_column($state['properties'], 'variants', 'id');
            // �������� ����������� ��������
            $arExportedProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
            $arExportedPropertiesId = array_column($arExportedProperties, 'PROPERTY_ID');
            $arProperties = $this->property()->getIblockPropertiesById($arExportedPropertiesId);
            $arPropertiesNames = array_column($arProperties, 'NAME', 'ID');
            $state['count'] = count($arExportedProperties);
            // ���������� ������������
            $arExportedPropertiesWait = array_slice($arExportedProperties, $state['offset']);
            // ������� �������� ������� ���� � ��
            while (count($arExportedPropertiesWait)) {
                $this->manager()->checkTime();
                // ����� �������� �������� �������� ����� ���������
                $arPropertyWait = array_shift($arExportedPropertiesWait);
                // �������� ���� �� ����� �������� � ��
                if (!isset($arVkPropertyIdToVariants[$arPropertyWait['VK_PROPERTY_ID']])) {
                    $state['offset']++;
                    continue;
                }
                // �������� ���������� �������� �� ��������� �������
                $arExportedVariants = $this->property()->getVariantsByGroupIdPropertyId($this->exportItem()->getGroupId(), $arPropertyWait['PROPERTY_ID']);
                $arExportedVariantsEnumIdToId = array_column($arExportedVariants, 'ID', 'ENUM_ID');
                // ������ ��������� ��� ��
                $arVkVariants = $arVkPropertyIdToVariants[$arPropertyWait['VK_PROPERTY_ID']];
                $arVkVariantsValues = array_column($arVkVariants, 'value', 'value');
                // ������ ��������� ��������� �� ��������� �������
                $arLocalVariants = $this->property()->getPropertyVariants($arPropertyWait['PROPERTY_ID']);
                $state['subCount'] = count($arLocalVariants);
                $arLocalVariantsNotCheck = array_slice($arLocalVariants, $state['subOffset']);
                try {
                    // ��������� �������
                    while (count($arLocalVariantsNotCheck)) {
                        $this->manager()->checkTime();
                        $arLocalVariant = array_shift($arLocalVariantsNotCheck);
                        if (isset($arVkVariantsValues[$arLocalVariant['ID']])) {
                            $state['subOffset']++;
                            continue;
                        }
                        // ��� ���� � ��, �� ���� � ��������� ����, �� ������ ��������� ������
                        if (isset($arExportedVariantsEnumIdToId[$arLocalVariant['ID']])) {
                            $this->property()->variantTable()->delete($arExportedVariantsEnumIdToId[$arLocalVariant['ID']]);
                        }
                        // ������ ���������
                        $this->addVariantToVk($arPropertyWait['PROPERTY_ID'], $arPropertyWait['VK_PROPERTY_ID'], $arLocalVariant);
                        $state['subOffset']++;
                    }
                    $state['name'] = $arPropertiesNames[$arPropertyWait['PROPERTY_ID']] ?? '[' . $arPropertyWait['PROPERTY_ID'] . ']';
                    $state['name'] .= ' ';
                    $state['name'] .= $this->state()->calcPercent($state['subCount'], $state['subOffset']) . '%';
                    // ���� ������ ��� ��������
                    if ($state['subOffset'] >= $state['subCount']) {
                        $state['subOffset'] = 0;
                        $state['subCount'] = 0;
                        $state['offset']++;
                    }
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    // ���������� ����������
                    if ($apiEx->is(\VKapi\Market\Api::ERROR_1419)) {
                        $state['subOffset'] = 0;
                        $state['subCount'] = 0;
                        $state['offset']++;
                    }
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
        }
        // ���������
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
            unset($state['properties']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // ���������
        $result->setDataArray(['name' => $state['name'], 'complete' => $state['complete'], 'percent' => $state['percent']]);
        return $result;
    }
    /**
 * ��������� ������� � ��
 * @param $propertyId
 * @param $vkPropertyId
 * @param $arVariant
 * @return bool
 * @throws BaseException
 * @throws ORMException
 * @throws ResponseErrorException
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function addVariantToVk($propertyId, $vkPropertyId, $arVariant)
    {
        // ��������� � ��
        $arFields = ['group_id' => $this->exportItem()->getGroupId(), 'property_id' => $vkPropertyId, 'title' => $arVariant['NAME'], 'value' => $arVariant['ID']];
        try {
            $result = $this->exportItem()->connection()->method('market.addPropertyVariant', $arFields);
            $response = $result->getData('response');
            if ((int) $response['variant_id']) {
                // �������� ������������� � ��������� ���
                $resultAdd = $this->property()->variantTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'PROPERTY_ID' => $propertyId, 'ENUM_ID' => $arVariant['ID'], 'VK_VARIANT_ID' => (int) $response['variant_id']]);
                $this->log()->ok($this->getMessage('ADD_VARIANT_TO_VK.OK', ['#PROPERTY_ID#' => $propertyId, '#VK_PROPERTY_ID#' => $vkPropertyId, '#VARIANT_ID#' => $arVariant['ID'] . ' ' . $arVariant['NAME']]));
            }
        } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
            if ($apiEx->is(\VKapi\Market\Api::ERROR_1419)) {
                $this->log()->error($this->getMessage('ADD_VARIANT_TO_VK.ERROR_LIMIT', ['#MSG#' => $apiEx->getMessage(), '#VARIANT_ID#' => $arVariant['ID'] . ' ' . $arVariant['NAME'], '#PROPERTY_ID#' => $propertyId, '#VK_PROPERTY_ID#' => $vkPropertyId]));
                throw $apiEx;
            }
            $this->log()->error($this->getMessage('ADD_VARIANT_TO_VK.ERROR', ['#MSG#' => $apiEx->getMessage(), '#VARIANT_ID#' => $arVariant['ID'] . ' ' . $arVariant['NAME'], '#PROPERTY_ID#' => $propertyId, '#VK_PROPERTY_ID#' => $vkPropertyId]));
        }
        return true;
    }
    /**
 * ������ ������ �������������� ������� ������� ������ ���� ��������� � ������
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getNeedExistsPropertyId()
    {
        $arReturn = [];
        // ������ ��� �������� ������� ������ ���� � ������
        $item = new \VKapi\Market\Export\Item();
        $dbr = \VKapi\Market\ExportTable::getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'ACTIVE' => true]]);
        while ($ar = $dbr->fetch()) {
            $item->setData($ar);
            if ($item->isEnabledExtendedGoods()) {
                $arReturn = array_merge($arReturn, $item->getPropertyIds());
            }
        }
        unset($item);
        $arReturn = array_values(array_unique($arReturn));
        return $arReturn;
    }
    /**
 * ������ �������������� ������� ������� �� ����e���
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function getIdNotExists()
    {
        // �������� �� ������� ��� ���������
        $arProperties = $this->property()->getListByGroupId($this->exportItem()->getGroupId());
        $arIdExists = array_column($arProperties, 'PROPERTY_ID');
        // �������� �� ������� ���
        $arIdNotExists = array_diff($this->exportItem()->getPropertyIds(), $arIdExists);
        return $arIdNotExists;
    }
}
?>