<?php

namespace VKapi\Market\Group;

use VKapi\Market\Connect;
use VKapi\Market\Exception\ApiResponseException;
use VKapi\Market\Result;
use VKapi\Market\Exception\BaseException;
use Bitrix\Main\Localization\Loc;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для очистики группы.сообщества
 * Class ClearManager
 * 
 * @package VKapi\Market\Group;
 */
class ClearManager
{
    /**
 * @var \VKapi\Market\Connect
 */
    protected $oConnection = null;
    protected $groupId = 0;
    public function __construct()
    {
        $this->oConnection = new \VKapi\Market\Connect();
    }
    /**
 * установка аккаунту от имени коотрого работаем
 * @param $accountId
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 */
    public function setAccountId($accountId)
    {
        $this->oConnection->initAccountId($accountId);
    }
    /**
 * Устанвока идентфикиатора группы с которой рабоатем
 * @param $groupId
 */
    public function setGroupId($groupId)
    {
        $this->groupId = abs((int) $groupId);
    }
    /**
 * вернет идентфиикатор группы с которйо рабоатем
 * @return int
 */
    public function getGroupId()
    {
        return $this->groupId;
    }
    /**
 * Вренет объект для запросок к api
 * @return Connect
 */
    public function connection()
    {
        return $this->oConnection;
    }
    /**
 * Вернет объект для хранения состояния
 * 
 * @return \VKapi\Market\State
 */
    public function state()
    {
        if (is_null($this->oState)) {
            $this->oState = new \VKapi\Market\State('group_' . $this->getGroupId(), '/clear');
        }
        return $this->oState;
    }
    /**
 * вренет языкозависимое сообщение
 * @param $name
 * @param array $arReplace
 * @return string|null
 */
    public function getMessage($name, $arReplace = [])
    {
        return \Bitrix\Main\Localization\Loc::getMessage('VKAPI.MARKET.CLEARMANAGER.' . $name, $arReplace);
    }
    /**
 * Вренет массив групп пользователя
 * @return array
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 */
    public function getGroups()
    {
        $result = $this->connection()->method('groups.get', array('filter' => 'editor', 'extended' => 1));
        if ($result->isSuccess()) {
            $response = $result->getData('response');
            return $response['items'];
        }
        return [];
    }
    /**
 * Запуск очистки группы
 * @return Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 */
    public function clearGroup()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        if (empty($data) || !isset($data['step']) || $data['complete']) {
            $data = array('complete' => false, 'percent' => 0, 'step' => 1, 'name' => '', 'steps' => array(1 => array('name' => $this->getMessage('CLEAR_GROUP.STEP1'), 'percent' => 0, 'error' => false), 2 => array('name' => $this->getMessage('CLEAR_GROUP.STEP2'), 'percent' => 0, 'error' => false), 3 => array('name' => $this->getMessage('CLEAR_GROUP.STEP3'), 'percent' => 0, 'error' => false)));
            $this->state()->set($data)->save();
        }
        if (\CModule::IncludeModuleEx("vkapi.market") == constant("MODULE_DEMO_E" . "X" . "" . "P" . "IRE" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "" . "RKET.DEMO_EXPI" . "" . "R" . "" . "E" . "" . "D"), "BXMAKER_D" . "EMO_EX" . "P" . "" . "" . "IRE" . "" . "D");
        }
        switch ($data['step']) {
            case 1:
                $resultClearGroupAlbum = $this->clearGroupAlbums();
                $data['name'] = $resultClearGroupAlbum->getData('name');
                $data['steps'][1]['percent'] = $resultClearGroupAlbum->getData('percent');
                if ($resultClearGroupAlbum->getData('complete')) {
                    $data['step']++;
                }
                break;
            case 2:
                $resultClearGroupProperties = $this->clearGroupGoods();
                $data['name'] = $resultClearGroupProperties->getData('name');
                $data['steps'][3]['percent'] = $resultClearGroupProperties->getData('percent');
                if ($resultClearGroupProperties->getData('complete')) {
                    $data['step']++;
                }
                break;
            case 3:
                $resultClearGroupProperties = $this->clearGroupProperties();
                $data['name'] = $resultClearGroupProperties->getData('name');
                $data['steps'][2]['percent'] = $resultClearGroupProperties->getData('percent');
                if ($resultClearGroupProperties->getData('complete')) {
                    $data['step']++;
                }
                break;
            default:
                $this->state()->clean();
                break;
        }
        // считаем выполненый процент
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] >= 100) {
            $data['complete'] = true;
            $data['name'] = $this->getMessage('CLEAR_GROUP.OK');
        }
        if (\CModule::IncludeModuleEx("vkap" . "i.market") === constant("MO" . "DULE_DEMO_EXPIR" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "P" . "" . "IRE" . "" . "D"), "BXMAKER_DEMO" . "_EXPI" . "RE" . "D");
        }
        // запишем
        $this->state()->set($data)->save();
        return $result->setDataArray(['name' => $data['name'], 'percent' => $data['percent'], 'complete' => $data['complete'], 'repeat' => !$data['complete']]);
    }
    /**
 * удаление подборок
 * @return Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 */
    public function clearGroupAlbums()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        $stateKey = 'clearGroupAlbums';
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = array('name' => '', 'complete' => false, 'percent' => 0, 'count' => null, 'offset' => 0);
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // удаление данных о выгруженных альбомах
            \VKapi\Market\Album\ExportTable::deleteAllByGroupId($this->getGroupId());
        }
        $state = $data[$stateKey];
        $oManager = \VKapi\Market\Manager::getInstance();
        $count = 1;
        try {
            while (!$oManager->isTimeout() && $count) {
                $requestCount = $this->connection()->method('market.getAlbums', array('owner_id' => '-' . $this->getGroupId(), 'count' => 25));
                $response = $requestCount->getData('response');
                $count = (int) $response['count'];
                // если количество еще не записывали
                if (is_null($state['count'])) {
                    $state['count'] = $count;
                    $this->state()->setField($stateKey, $state)->save();
                }
                if ($count) {
                    $code = ' var results = []; ' . "\n";
                    foreach ($response['items'] as $item) {
                        if ((int) $item['id'] <= 0) {
                            continue;
                        }
                        $code .= ' results.push([' . $item['id'] . ', API.market.deleteAlbum({"owner_id" : "-' . $this->getGroupId() . '","album_id" :' . $item['id'] . '}) ]);' . "\n";
                    }
                    $code .= ' return results;';
                    $requestDelete = $this->connection()->method('execute', array('code' => $code));
                    $response = $requestDelete->getData('response');
                    if (is_array($response)) {
                        foreach ($response as $row) {
                            // если 1, то удалено
                            if ($row[1]) {
                                $state['offset']++;
                            }
                        }
                    }
                } else {
                    $state['offset'] = $state['count'];
                }
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
        }
        // считаем выполненый процент
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        $state['name'] = $this->getMessage('CLEAR_GROUP_ALBUMS.PROCESS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count']]);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
        }
        if ($ex) {
            $state['name'] = $ex->getMessage();
            // запишем
            $this->state()->setField($stateKey, $state)->save();
            throw $ex;
        }
        if (\Bitrix\Main\Loader::includeSharewareModule("vk" . "ap" . "i.ma" . "rket") == constant("MODULE_DEMO" . "_EXPIR" . "E" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKE" . "T.DEM" . "O_E" . "XP" . "IR" . "ED"), "BXMAKER_" . "DEMO_EXPIRED");
        }
        // запишем
        $this->state()->setField($stateKey, $state)->save();
        return $result->setDataArray(['name' => $state['name'], 'percent' => $state['percent'], 'complete' => $state['complete']]);
    }
    /**
 * удаление свойств
 * @return Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 */
    public function clearGroupProperties()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        $stateKey = 'clearGroupProperties';
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = array('name' => '', 'complete' => false, 'percent' => 0, 'count' => null, 'offset' => 0);
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // удаление выгруженных свойств
            \VKapi\Market\Property\PropertyTable::deleteAllByGroupId($this->getGroupId());
            // удаление выгруженных вараинтов
            \VKapi\Market\Property\VariantTable::deleteAllByGroupId($this->getGroupId());
        }
        $state = $data[$stateKey];
        $oManager = \VKapi\Market\Manager::getInstance();
        $count = 1;
        try {
            while (!$oManager->isTimeout() && $count) {
                $requestCount = $this->connection()->method('market.getProperties', array('group_id' => $this->getGroupId()));
                $response = $requestCount->getData('response');
                $count = (int) $response['count'];
                if (\Bitrix\Main\Loader::includeSharewareModule("vkapi" . ".marke" . "" . "t") == constant("MODULE_DE" . "MO" . "_EXPIRE" . "" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET." . "DEMO_EXP" . "IRE" . "" . "" . "D"), "BXMAKER_DEMO_EXP" . "IRE" . "" . "D");
                }
                // если количество еще не записывали
                if (is_null($state['count'])) {
                    $state['count'] = $count;
                    $this->state()->setField($stateKey, $state)->save();
                }
                if ($count) {
                    $code = ' var results = []; ' . "\n";
                    $response['items'] = array_slice($response['items'], 0, 1);
                    foreach ($response['items'] as $item) {
                        if ((int) $item['id'] <= 0) {
                            continue;
                        }
                        $code .= ' results.push([' . $item['id'] . ', API.market.deleteProperty({"group_id" : "' . $this->getGroupId() . '","property_id" :' . $item['id'] . '}) ]);' . "\n";
                    }
                    $code .= ' return results;';
                    $requestDelete = $this->connection()->method('execute', array('code' => $code));
                    $response = $requestDelete->getData('response');
                    $execute_errors = $requestDelete->getData('execute_errors') ?? [];
                    if (is_array($response)) {
                        foreach ($response as $row) {
                            // если 1, то удалено
                            if ($row[1]) {
                                $state['offset']++;
                            } else {
                                throw new \VKapi\Market\Exception\ApiResponseException($execute_errors[0]);
                            }
                        }
                    }
                } else {
                    $state['offset'] = $state['count'];
                }
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            if ($ex instanceof \VKapi\Market\Exception\ApiResponseException && $ex->is(\VKapi\Market\Api::ERROR_1409)) {
                $state['offset'] = 0;
                $state['count'] = 0;
            }
        }
        // считаем выполненый процент
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        $state['name'] = $this->getMessage('CLEAR_GROUP_PROPERTIES.PROCESS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count']]);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
        }
        if ($ex) {
            if ($ex instanceof \VKapi\Market\Exception\ApiResponseException && $ex->is(\VKapi\Market\Api::ERROR_1409)) {
                // ничего не меняем, просто пропускаем
            } else {
                $state['name'] = $ex->getMessage();
                // запишем
                $this->state()->setField($stateKey, $state)->save();
                throw $ex;
            }
        }
        // запишем
        $this->state()->setField($stateKey, $state)->save();
        return $result->setDataArray(['name' => $state['name'], 'percent' => $state['percent'], 'complete' => $state['complete']]);
    }
    /**
 * удаление товаров
 * @return Result
 * @throws \Bitrix\Main\ArgumentException
 * @throws \Bitrix\Main\IO\FileNotFoundException
 */
    public function clearGroupGoods()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        $stateKey = 'clearGroupGoods';
        if (!isset($data[$stateKey])) {
            $data[$stateKey] = array('name' => '', 'complete' => false, 'percent' => 0, 'count' => null, 'offset' => 0);
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // удаляем данные по выгруженным товарам -------
            \VKapi\Market\Good\ExportTable::deleteAllByGroupId($this->getGroupId());
            // сразу удаляем данные по выгруженным картинкам в указанную группу
            \VKapi\Market\Export\PhotoTable::deleteAllByGroupId($this->getGroupId());
        }
        $state = $data[$stateKey];
        $oManager = \VKapi\Market\Manager::getInstance();
        $count = 1;
        $bSet = false;
        try {
            while (!$oManager->isTimeout() && $count) {
                $requestCount = $this->connection()->method('market.get', array('owner_id' => -1 * $this->getGroupId(), 'with_disabled' => 1, 'need_variants' => 1, 'count' => 25));
                $response = $requestCount->getData('response');
                $count = (int) $response['count'];
				/*
                // bug fix возвращаетс яне вреное количество товара
                if (count($response['items']) < 25 && count($response['items']) < $count) {
                    $count = count($response['items']);
                }
				throw new \VKapi\Market\Exception\ApiResponseException($response);
                */
				// если количество еще не записывали
                if (!$bSet) {
                    $bSet = true;
                    $state['count'] = $count;
                    $this->state()->setField($stateKey, $state)->save();
                }
                if (\CModule::IncludeModuleEx("vkapi." . "mar" . "ke" . "t") === constant("MODULE_DEMO_EXPIRE" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.M" . "ARKET.DEMO_" . "E" . "X" . "PI" . "R" . "ED"), "BXMAKER_DEMO_E" . "XP" . "IRED");
                }
                if ($count) {
                    $code = ' var results = []; ' . "\n";;
                    $response['items'] = array_slice($response['items'], 0, 25);
                    foreach ($response['items'] as $item) {
                        if ((int) $item['id'] <= 0) {
                            continue;
                        }
                        $code .= ' results.push([' . $item['id'] . ', API.market.delete({"owner_id" : "-' . $this->getGroupId() . '","item_id" :' . $item['id'] . '}) ]);' . "\n";
                    }
                    $code .= ' return results;';
                    $requestDelete = $this->connection()->method('execute', array('code' => $code));
                    $response = $requestDelete->getData('response');
                    $execute_errors = $requestDelete->getData('execute_errors') ?? [];
                    if (is_array($response)) {
                        foreach ($response as $row) {
                            // если 1, то удалено
                            if ($row[1]) {
                                $state['offset']++;
                            } else {
                                throw new \VKapi\Market\Exception\ApiResponseException($execute_errors[0]);
                            }
                        }
                    }
                } else {
                    $state['offset'] = $state['count'];
                }
                $this->state()->setField($stateKey, $state)->save();
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
        }
        // считаем выполненый процент
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        $state['name'] = $this->getMessage('CLEAR_GROUP_GOODS.PROCESS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count']]);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
        }
        if ($ex) {
            $state['name'] = $ex->getMessage();
            // запишем
            $this->state()->setField($stateKey, $state)->save();
            throw $ex;
        }
        // запишем
        $this->state()->setField($stateKey, $state)->save();
        return $result->setDataArray(['name' => $state['name'], 'percent' => $state['percent'], 'complete' => $state['complete']]);
    }
}
?>