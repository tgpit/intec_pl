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
 * Работа c выгруженными товарами в вконтакте, выгружает товары в вк, синхронизирует и тп
 * Class Export
 * 
 * @package VKapi\Market\Good
 */
class Export
{
    const PRODUCT_TYPE_SIMPLE = 1;
    // простой товар
    const PRODUCT_TYPE_HAS_OFFERS = 2;
    // товар с торговыми предложениями
    const PROPERTY_TYPE_L = 'L';
    // L - список
    const PROPERTY_TYPE_S = 'S';
    // S - строка
    const PROPERTY_TYPE_N = 'N';
    // N - число
    const PROPERTY_TYPE_F = 'F';
    // F - файл
    const PROPERTY_TYPE_G = 'G';
    // G - привязка к разделу
    const PROPERTY_TYPE_E = 'E';
    // E - привязка к элементу
    /**
     * 
     * @var \VKapi\Market\Export\Item
     */
    protected $oExportItem = null;
    /**
     * 
     * тблица выгруженных
     * 
     * @var \VKapi\Market\Good\ExportTable
     */
    private $oGoodExportTable = null;
    /**
     * 
     * @var \VKapi\Market\Album\Export - объект экспорта альбомов в вк
     */
    protected $oAlbumExport = null;
    /**
     * 
     * @var \VKapi\Market\Album\Item - объект для работы с локлаьными подборками
     */
    protected $oAlbumItem = null;
    /**
     * 
     * @var \VKapi\Market\Export\Photo
     */
    protected $oPhoto = null;
    /**
     * 
     * @var \VKapi\Market\Export\Log Логирование
     */
    protected $oLog = null;
    /**
     * 
     * @var \VKapi\Market\State Состояние
     */
    protected $oState = null;
    /**
     * 
     * @var \CIBLockElement
     */
    protected $oIblockElementOld = null;
    /**
     * 
     * @var array Массив расчетных значений, чтобы повторно не выяснять, например наименования товаров по привязке
     * и тп
     */
    protected $arPrepiredPropValue = [];
    /**
     * 
     * @var array Массив альбомов в вк [albumId => vkId]
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
     * Работа с таблицей выгруженых товаров в вк,
     * содержит поля:
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
     * Объект для работы с списоком связей товаров и выгрузками
     * привязки появляются после проверки условий выгрузки для конкретного товара
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
     * Вернет объект для работы с парамтерами выгрузки
     * 
     * @return \VKapi\Market\Export\Item
     */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
     * 
     * Вернет объект для хранения состояния
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
     * Вернет объект для хранения состояния
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
     * Класс для лимитирвания добавления товаров в час 1000, в сутки 7000
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
     * Класс для хранения истории ранее выгруженых товаров
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
     * Вернет объект экспорта альбомов в вк
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
     * Вернет объект для работы с локальными подборками
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
     * Вернет объект для работы с выгружаемыми картинками
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
     * Вернет hash описания полей товара, выгружаемого в ВК, для проверки наличия изменений
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
     * Экспортирует данные по товарам в вконтакте
     * 
     * @return \VKapi\Market\Result - веозвращает результат в объекте
     * 
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRun()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        // ожидание завершения
        if (!empty($data) && $data['run'] && $data['timeStart'] > time() - 60 * 3) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('WAIT_FINISH'), 'WAIT_FINISH');
        }
        // установка базового состояния
        if (empty($data) || !isset($data['step']) || $data['complete']) {
            $this->state()->set(['complete' => false, 'percent' => 0, 'step' => 1, 'steps' => [
                //все шаги, которые есть, в процессе работы, могут меняться сообщения, например обработано 2 из 10
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
        // фиксируем запуск
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
                    // формирование списка товаров для экспорта
                    // формирвоание списка товаров по подборкам
                    $resultAction = $this->exportRunPrepareList();
                    // если операция закончена
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
                    // Проверка ранее выгруженных товаров
                    $resultAction = $this->exportRunCheckExistsInVk();
                    // если операция закончена
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
                    // получаем массив существующих альбомов в вк
                    $this->getAlbumIdInVkList(true);
                    // обновление товаров
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // старый вараинт с объединением тп
                        $resultAction = $this->exportRunUpdateInVkBaseMode();
                    } else {
                        $resultAction = $this->exportRunUpdateInVk();
                    }
                    // если операция закончена
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
                    // Удаление старых, не нужных более товаров
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // старый вараинт с объединением тп
                        $resultAction = $this->exportRunDeleteOldFromVKBaseMode();
                    } else {
                        $resultAction = $this->exportRunDeleteOldFromVK();
                    }
                    $data['steps'][5]['name'] = $resultAction->getData('message');
                    $data['steps'][5]['percent'] = $resultAction->getData('percent');
                    // если операция закончена
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name'], '#PERCENT#' => $data['steps'][5]['percent']]));
                    }
                    break;
                case 6:
                    // Удаление дубликатов
                    $resultAction = $this->exportRunDeleteLocalDoublesFormVK();
                    $data['steps'][6]['name'] = $resultAction->getData('message');
                    $data['steps'][6]['percent'] = $resultAction->getData('percent');
                    // если операция закончена
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 6, '#STEP_NAME#' => $data['steps'][6]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 6, '#STEP_NAME#' => $data['steps'][6]['name'], '#PERCENT#' => $data['steps'][6]['percent']]));
                    }
                    break;
                case 7:
                    // добавления новых товаров
                    if ($this->exportItem()->isEnabledOfferCombine() && !$this->exportItem()->isEnabledExtendedGoods()) {
                        // старый вараинт с объединением тп
                        $resultAction = $this->exportRunAddToVkBaseMode();
                    } else {
                        $resultAction = $this->exportRunAddToVk();
                    }
                    // если операция закончена
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
                    // Удалени неизвестных товаров из вк
                    $resultAction = $this->exportRunDeleteUnknownInVK();
                    $data['steps'][8]['name'] = $resultAction->getData('message');
                    $data['steps'][8]['percent'] = $resultAction->getData('percent');
                    // если операция закончена
                    if ($resultAction->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.OK', ['#STEP#' => 8, '#STEP_NAME#' => $data['steps'][8]['name']]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_GOODS.STEP.PROCESS', ['#STEP#' => 8, '#STEP_NAME#' => $data['steps'][8]['name'], '#PERCENT#' => $data['steps'][8]['percent']]));
                    }
                    break;
                case 9:
                    // группировка
                    $resultAction = $this->exportRunGroupUngroupItem();
                    $data['steps'][9]['name'] = $resultAction->getData('message');
                    $data['steps'][9]['percent'] = $resultAction->getData('percent');
                    // если операция закончена
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
        // считаем выполненый процент
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] == 100) {
            $data['complete'] = true;
            $this->log()->notice($this->getMessage('EXPORT_GOODS.STOP'));
        }
        // заканчиваем
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
     * Обход товаров в инфоблоке, проверка условий, добавление в списки и удаление из списков
     */
    public function exportRunPrepareList()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunPrepareList';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                'count' => 0,
                'offset' => 0,
                // лимит на итерацию
                'limit' => 10,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // помечем все элементы списка
            $this->goodReferenceExport()->getTable()->setMarkForAllByExportId($this->exportItem()->getId());
        }
        $state = $data[$stateKey];
        // получаем подборки и их условия -----------
        $arAlbumId = $this->exportItem()->getAlbumIds();
        // альбомы выбранные в выгрузке
        $arAlbums = $this->albumItem()->getItemsById($arAlbumId);
        // удаляем привязки к альбомам товаров, которых уже нет в системе
        \VKapi\Market\Good\Reference\AlbumTable::deleteNotExistsYet($arAlbumId, $this->exportItem()->getProductIblockId(), $this->exportItem()->getOfferIblockId());
        // удаляем привязки к экспорту товаров, которых уже нет в системе
        \VKapi\Market\Good\Reference\ExportTable::deleteNotExistsYet($this->exportItem()->getId(), $this->exportItem()->getProductIblockId(), $this->exportItem()->getOfferIblockId());
        if (\CModule::IncludeModuleEx("vkapi.market") === constant("MODUL" . "E_DEMO_EXPI" . "R" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.M" . "ARKET.DEMO_EXPIRE" . "D"), "BXMAKER" . "_DEMO_EXPIRE" . "D");
        }
        // подсчитаем количество товаров
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
            // обходим товары
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            // удаляем все лишние
            $this->goodReferenceExport()->getTable()->deleteAllMarkedByExportId($this->exportItem()->getId());
        }
        $arCountAll = $this->goodReferenceExport()->getTable()->getList(['select' => ['CNT_DISTINCT_PRODUCT_ID'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId()]])->fetch();
        $state['validProduct'] = $arCountAll['CNT_DISTINCT_PRODUCT_ID'] ?? 0;
        $state['valid'] = $this->goodReferenceExport()->getTable()->getCount(['EXPORT_ID' => $this->exportItem()->getId()]);
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray([
            'count' => $state['offset'],
            // пройдено
            'all' => $state['count'],
            // всего
            'complete' => $state['complete'],
            // флаг завершения цикла
            'percent' => $state['percent'],
            //сообщение
            'message' => $this->getMessage('PREPARE_LIST', ['#COUNT#' => $state['offset'], '#ALL#' => $state['count'], '#VALID#' => $state['valid'], '#VALID_PRODUCT#' => $state['validProduct']]),
        ]);
        return $result;
    }
    /**
     * 
     * Вренет парамтеры фильтрации для формирвоания базового списка товаров при подготовке
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
     * Вернет общее количество товаров для подготовки,
     * за вычетом основных товаров
     * @return int|mixed
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function exportRunPrepareListActionGetAllCount()
    {
        // количество основных товаров
        $count = \Bitrix\Iblock\ElementTable::getCount($this->exportRunPrepareListActionGetFilter());
        // если есть торговые предложения, то считаем количество еще и торговых предложений
        if ($this->exportItem()->hasOffers()) {
            $arOfferCount = $this->iblockElementOld()->getList(['cnt' => 'cnt'], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false, 'PROPERTY_' . $this->exportItem()->getLinkPropertyId() . '.IBLOCK_ID' => $this->exportItem()->getProductIblockId()], ['IBLOCK_ID'])->fetch();
            $count += $arOfferCount['CNT'];
            // вычетаем количество основных товаров из этого списка
            $arProductWithOfferCount = $this->iblockElementOld()->getList(['cnt' => 'cnt'], ['IBLOCK_ID' => $this->exportItem()->getProductIblockId(), 'WF_PARENT_ELEMENT_ID' => false, ["ID" => $this->iblockElementOld()->SubQuery("PROPERTY_" . $this->exportItem()->getLinkPropertyId(), ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false])]], ['IBLOCK_ID'])->fetch();
            $count -= $arProductWithOfferCount['CNT'];
        }
        return $count;
    }
    /**
     * 
     * Обработает товар и его торговые предложения,
     * проверит проходит ли он по условиям и если проходит
     * добавит в соответствующие таблицы записи
     * после чего вернет счетчик количества обработанных товаров или торговых предложений
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
        // готовим сразу поля для првоерки условий
        $arElements = $oCondition->getPreparedElementFieldsById([$productId], false, $this->exportItem()->getProductPriceUserGroupIds(), $this->exportItem()->getSiteId());
        $arElement = $arElements[$productId];
        /**
         * 
         * Массив новых привязок {elementId: {offerId : [albumId, ....], ...}, ...}
         */
        $arElementAlbumReference = [];
        $arElementExportReference = [];
        // если есть подборки, првоеряем условия для альбомов ----------
        $arElementAlbumReference[$productId][0] = [];
        // првоверяем подходит ли товар для привязки к подборке
        foreach ($arAlbums as $albumId => $arAlbum) {
            if ($oCondition->isMatchCondition($arAlbum['PARAMS']['CONDITIONS'], $arElement)) {
                $arElementAlbumReference[$productId][0][$albumId] = $albumId;
            }
        }
        // условия только по основному товару ----------------
        // но может быть задан идентификатор торгового предложения а не основного товара
        // тогда это условие не выполнится
        $arElementExportReference[$productId][0] = [];
        if ($oCondition->isMatchCondition($this->exportItem()->getConditions(), $arElement)) {
            $arElementExportReference[$productId][0][$this->exportItem()->getId()] = $this->exportItem()->getId();
        }
        // собираем торговые предложения
        if ($this->exportItem()->hasOffers()) {
            $arOffers = [];
            $dbrOffer = \CIBlockElement::getList(['ID' => 'ASC'], ['IBLOCK_ID' => $this->exportItem()->getOfferIblockId(), 'WF_PARENT_ELEMENT_ID' => false, 'PROPERTY_' . $this->exportItem()->getLinkPropertyId() => $productId], false, false, ['ID', 'PROPERTY_' . $this->exportItem()->getLinkPropertyId()]);
            while ($arOffer = $dbrOffer->fetch()) {
                $arOffers[$arOffer['ID']] = [];
            }
            if (count($arOffers)) {
                unset($arElementAlbumReference[$productId][0]);
                unset($arElementExportReference[$productId][0]);
                // подготовка полей оферов
                $arOffersConditions = $oCondition->getPreparedElementFieldsById(array_keys($arOffers), true, $this->exportItem()->getOfferPriceUserGroupIds(), $this->exportItem()->getSiteId());
                foreach ($arOffersConditions as $offerId => $offerFields) {
                    $arOffers[$offerId] = array_replace($arElement, $offerFields);
                }
                // проверяем условия для привязок
                foreach ($arOffers as $offerId => $arOffer) {
                    // условия привязки к альбомам -------------
                    if (count($arAlbums)) {
                        $arElementAlbumReference[$productId][$offerId] = [];
                        foreach ($arAlbums as $albumId => $arAlbum) {
                            if ($oCondition->isMatchCondition($arAlbum['PARAMS']['CONDITIONS'], $arOffer)) {
                                $arElementAlbumReference[$productId][$offerId][$albumId] = $albumId;
                            }
                        }
                    }
                    // привязка к выгрузке ---------------------------
                    $arElementExportReference[$productId][$offerId] = [];
                    if ($oCondition->isMatchCondition($this->exportItem()->getConditions(), $arOffer)) {
                        $arElementExportReference[$productId][$offerId][$this->exportItem()->getId()] = $this->exportItem()->getId();
                    }
                }
            }
        }
        // сохраняем новые данные по альбомам
        $this->goodReferenceAlbum()->updateElementReferenceList($arElementAlbumReference, array_keys($arAlbums));
        // сохраняем новые данные по привязкам к выгрузкам
        $this->goodReferenceExport()->updateElementReferenceList($arElementExportReference, [$this->exportItem()->getId()]);
        return true;
    }
    /**
     * 
     * Проверка ранее выгруженных товаров в ВК
     * Проходит по всем записям в локлаьной базе и проверяет наличие в вк
     * если нету, то удаляет из локальной базы выгруженных товаров
     */
    public function exportRunCheckExistsInVk()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckExistsInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
                'limit' => 250,
                //отсутствуют
                'losted' => 0,
                'vkItems' => [],
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            // подсчитаем количество ----
            $state['count'] = $this->goodExportTable()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId()]);
            // получаем список товаров в вк
            $vkItemIds = $this->getVkItemIdList($state['vkItems']);
            $vkItemIds = array_combine($vkItemIds, $vkItemIds);
            if (\Bitrix\Main\Loader::includeSharewareModule("vkap" . "i.market") == constant("MODULE_DEMO_" . "E" . "XPI" . "RED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXMAKER_DEMO_EXPIR" . "E" . "" . "" . "D");
            }
            while ($state['count'] > $state['offset']) {
                $this->manager()->checkTime();
                // собираем записи о товарах ---------
                $dbrItems = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId()], 'select' => ['ID', 'VK_ID', 'PRODUCT_ID', 'OFFER_ID'], 'limit' => $state['limit'], 'offset' => $state['offset']]);
                while ($arItem = $dbrItems->fetch()) {
                    $this->manager()->checkTime();
                    if (isset($vkItemIds[$arItem['VK_ID']])) {
                        $state['offset']++;
                    } else {
                        $state['count']--;
                        $state['losted']++;
                        $this->goodExportTable()->delete($arItem['ID']);
                        // удаляем картинки
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
            // продолжаем
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['vkItems']);
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'] + $state['losted'], 'count' => $state['count'] + $state['losted'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('CHECK_EXISTS_IN_VK', ['#OFFSET#' => $state['offset'] + $state['losted'], '#COUNT#' => $state['count'] + $state['losted'], '#LOSTED#' => $state['losted']])]);
        return $result;
    }
    /**
     * 
     * Вернет массив тидентификаторов товаров в ВК
     * 
     * @return int[]
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function getVkItemIdList(&$state)
    {
        // получаем количество
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
     * Обновление товаров в вконтакте, ранее выгруженные, сверяем хэши чтобы исключить дубликаты и тп
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
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
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
                // Удаляем дублирующие записи ссылок на товар в вк для разных оверов,
                // которые могут формирвоаться при баззорвом режиме и вклчюенном объединении торговых предложений
                $this->goodExportTable()->deleteDoublesVkIdByGroupId($this->exportItem()->getGroupId());
            }
            // отбираем товары в такблице спсика для выгрузки, которые имеют идентфикатор товара в вк
            $state['count'] = $this->exportRunUpdateInVkActionGetCount();
            while ($state['offset'] < $state['count']) {
                $this->manager()->checkTime();
                // собираем записи о товарах ---------------------------------------------------
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
            // продолжаем
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "ket") === constant("MOD" . "ULE_DEMO_EXPI" . "RE" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKA" . "PI.MARKET.DEMO_" . "EXPI" . "" . "R" . "E" . "D"), "BXMAKER_DEMO_EXPI" . "" . "" . "RE" . "D");
        }
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_UPDATE_IN_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#UPDATED#' => $state['updated'], '#SKIPPED#' => $state['skipped']])]);
        return $result;
    }
    /**
     * 
     * Верент общее количество товаров которое необходиом обнвоить
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
     * обработает запись о ранее вгыруженом товаре, при необходимости обновит его
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
        // подготавливаем описания товаров -------------------------------
        $preparedItem = $this->getPreparedItem($arItem['PRODUCT_ID'], (array) $arItem['OFFER_ID']);
        try {
            $arFields = $preparedItem->getFields();
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // берем данные о выгруженных товарах в вк, чтобы сравнить хэши перед выгрузкой ----------------------
            $arGoodExportRow = $this->goodExportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // запись в таблице товаров отображнных для выгрзуик
            $arGoodReferenceExportRow = $this->goodReferenceExport()->getTable()->getList(['order' => ['ID' => 'ASC'], 'select' => ['ID', 'PRODUCT_ID', 'OFFER_ID', 'FLAG'], 'filter' => ['EXPORT_ID' => $this->exportItem()->getId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // добавим запись если отсутствует
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
            // запрет обнволения картинок
            if ($this->manager()->isDisabledUpdatePicture()) {
                unset($arFields['main_photo_id'], $arFields['photo_ids']);
            } elseif (!(int) $arFields['main_photo_id']) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.edit', array_merge($arFields, ['item_id' => $arGoodExportRow['VK_ID']]));
            $response = $resultApi->getData('response');
            // сразу привязываем товары к альбомам
            $this->deleteVkItemIdFromAllAlbums($arGoodExportRow['VK_ID']);
            $this->addVkItemIdToVkAlbums($arGoodExportRow['VK_ID'], $arVkAlbumIds);
            // сохраняем данные о новом хэше
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
     * Обновление товаров в вконтакте, ранее выгруженные, сверяем хэши чтобы исключить дубликаты и тп
     * Вариант для объединения торговых предложений в базовом режиме товаров
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
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
                'limit' => 25,
                'updated' => 0,
                'skipped' => 0,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        $data['limit'] = $this->manager()->getExportPackLimit();
        try {
            // отбираем товары в такблице спсика для выгрузки, которые имеют идентфикатор товара в вк
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
            // продолжаем
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.marke" . "t") == constant("MODULE" . "_DEMO_E" . "XPI" . "RE" . "" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET." . "DE" . "MO_EXPI" . "" . "RED"), "BXMAKER_DEMO_E" . "XPIRED");
        }
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_UPDATE_IN_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#UPDATED#' => $state['updated'], '#SKIPPED#' => $state['skipped']])]);
        return $result;
    }
    /**
     * 
     * Верент общее количество товаров которое необходиом обнвоить
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
     * Вернет следующий товар, который еще не выгружен
     * @param $offset - количество пропущенных, которые не смогли выгрузить
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
     * обработает запись о ранее вгыруженом товаре, при необходимости обновит его
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
        // собираем записи
        $arRows = $this->exportRunAddToVkBaseModeActionAddGetRows($productId);
        if (empty($arRows)) {
            $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.REFERENCE_PRODUCT_ITEMS_NOT_FOUND', ['#ID#' => $productId]));
            return false;
        }
        // смотрим не появились ли элементы оторые еще не выгружены в вк
        $this->exportRunUpdateInVkBaseModeActionCreateExportedRow($arRows);
        // собираем торговые прдложения
        $arOfferIds = array_column($arRows, 'OFFER_ID');
        $preparedItem = $this->getPreparedItem($productId, $arOfferIds);
        try {
            $arFields = $preparedItem->getFields();
            $arVkAlbumIds = $preparedItem->getAlbumsVkIds();
            // берем данные о выгруженных товарах в вк, чтобы сравнить хэши перед выгрузкой ----------------------
            $arGoodExportRow = $this->goodExportTable()->getList(['filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $preparedItem->getProductId(), 'OFFER_ID' => $preparedItem->getOfferIds()], 'limit' => 1])->fetch();
            // добавим запись если отсутствует
            $this->history()->append($preparedItem, $arGoodExportRow['VK_ID']);
            if ($arFields['price'] < 0.01) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.PRICE_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            if ($arGoodExportRow['HASH'] == $this->getHash($arFields, $arVkAlbumIds)) {
                $this->log()->notice($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.NOT_CHANGED', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            // запрет обновления картинок
            if ($this->manager()->isDisabledUpdatePicture()) {
                unset($arFields['main_photo_id'], $arFields['photo_ids']);
            } elseif (!(int) $arFields['main_photo_id']) {
                $this->log()->error($this->getMessage('EXPORT_RUN_UPDATE_IN_VK.MAIN_PHOTO_ID_EMPTY', ['#PRODUCT_ID#' => $preparedItem->getProductId(), '#OFFER_ID#' => implode(', ', $preparedItem->getOfferIds())]));
                return false;
            }
            $resultApi = $this->exportItem()->connection()->method('market.edit', array_merge($arFields, ['item_id' => $arGoodExportRow['VK_ID']]));
            $response = $resultApi->getData('response');
            // сразу привязываем товары к альбомам
            $this->deleteVkItemIdFromAllAlbums($arGoodExportRow['VK_ID']);
            $this->addVkItemIdToVkAlbums($arGoodExportRow['VK_ID'], $arVkAlbumIds);
            // сохраняем данные о новом хэше
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
     * Если появился еще один оффер к выгрузке, то добавляем его в список выгруженых
     * и указываем идентификатор выгруженого товара как у остальных оферов
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
        // собираем недобавленые записи
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
     * Добавление товаров в вконтакте
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
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
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
            // сохраняем сразу весь список
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
                // меняем флаг
                $this->goodReferenceExport()->getTable()->update($refId, ['FLAG' => \VKapi\Market\Good\Reference\Export::FLAG_NEED_SKIP]);
                if ($this->exportRunAddToVkActionAddByRefId($refId)) {
                    $state['added']++;
                } else {
                    $state['skipped']++;
                }
                // если все норм, то
                array_shift($state['arId']);
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // продолжаем
        } catch (\VKapi\Market\Exception\GoodLimitException $limitException) {
            $isOverLimit = true;
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        if ($isOverLimit) {
            $state['complete'] = true;
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $arReturn = ['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_ADD_TO_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']])];
        if ($isOverLimit) {
            $arReturn['message'] = $this->getMessage('EXPORT_RUN_ADD_TO_VK_LIMIT.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']]);
        }
        $result->setDataArray($arReturn);
        return $result;
    }
    /**
     * 
     * Верент id записей подходящих по условия выгрузки в вк, которые пока еще не были выгружены
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
     * Добавление товара по id записи отобраного товара подходящего под условия выгрузки
     * вернет true - елси товар добавлен
     * вернет false - если товар пропущен по какой то причине
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
            // сразу привязываем товары к альбомам
            $this->deleteVkItemIdFromAllAlbums($vkItemId);
            $this->addVkItemIdToVkAlbums($vkItemId, $arVkAlbumIds);
            // история
            $this->history()->append($preparedItem, $vkItemId);
            // лимит
            $this->limit()->append($vkItemId);
            // сохраняем инфу что товар добавлен
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
     * Добавление товаров в вконтакте в базовом режиме товаров c объединением оферов
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
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
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
                // если все норм, то
                $state['offset']++;
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // продолжаем
        } catch (\VKapi\Market\Exception\GoodLimitException $limitException) {
            $isOverLimit = true;
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        if ($isOverLimit) {
            $state['complete'] = true;
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $arReturnResult = ['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_ADD_TO_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']])];
        if ($isOverLimit) {
            $arReturnResult['message'] = $this->getMessage('EXPORT_RUN_ADD_TO_VK_LIMIT.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#ADDED#' => $state['added'], '#SKIPPED#' => $state['skipped']]);
        }
        $result->setDataArray($arReturnResult);
        return $result;
    }
    /**
     * 
     * Посчитает и вернет количество товаров, которые не выгружены
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
     * Вернет следующий товар, который еще не выгружен
     * @param $offset - количество пропущенных, которые не смогли выгрузить
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
     * Добавление объединеных товаров в вк по productId отобраного товара подходящего под условия выгрузки
     * вернет true - елси товар добавлен
     * вернет false - если товар пропущен по какой то причине
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
        // собираем записи
        $arRows = $this->exportRunAddToVkBaseModeActionAddGetRows($productId);
        if (empty($arRows)) {
            $this->log()->error($this->getMessage('EXPORT_RUN_ADD_TO_VK.REFERENCE_PRODUCT_ITEMS_NOT_FOUND', ['#ID#' => $productId]));
            return false;
        }
        // собираем торговые прдложения
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
            // лимиты
            $this->limit()->append($vkItemId);
            // история
            $this->history()->append($preparedItem, $vkItemId);
            // сразу привязываем товары к альбомам
            $this->deleteVkItemIdFromAllAlbums($vkItemId);
            $this->addVkItemIdToVkAlbums($vkItemId, $arVkAlbumIds);
            // сохраняем инфу что товар добавлен
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
     * Вернет записи о ТП товара для выгрузки из списка отобранных
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
     * Вернет объект для работы с подготовленым товаром, описание полей, альбомы и прочее
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
     * Удалит товар из всех альбомов кроме нужных
     * @param int $vkItemId - id товара
     * @param int[] $arVkAlbumId - массив ID альбомов
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
     * Удалит товар из всех альбомов кроме нужных
     * @param int $vkItemId - id товара
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
     * Добавляет товар в нужные альбомы
     * 
     * @param int $vkItemId - id товара
     * @param int[] $arVkAlbumId - массив id альбомов
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
     * Удаление товаров из вк, которые больше недоступны
     * в локальном списке подготовленых для выгрузки
     * например если сменилась активность, доступность,
     * больше не подходит под условия экспорта
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
        // если нужно предотвратить удаление старых, либо дуликтаов
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // фиксируем состояние
            $this->state()->setField($stateKey, $state)->save();
            // возвращаем статус опреации
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            if (empty($state['arId'])) {
                // отбираем товары, которые есть в таблице выгруженных но нет в таблице подготовленых
                $state['arId'] = $this->exportRunDeleteOldFromVKActionGetIdForDelete();
                $state['count'] = count($state['arId']);
                $this->state()->setField($stateKey, $state)->save();
            }
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi." . "mark" . "" . "" . "" . "" . "et") == constant("M" . "ODULE_DE" . "MO_EXPIRE" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "RKET.DEMO_EXPIRED"), "BXMAKER_" . "DEMO_" . "EXP" . "IR" . "E" . "D");
            }
            // цикл
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
                // делаем запрос к вк ---
                if (count($code)) {
                    $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                    $response = $resultRequest->getData('response');
                    foreach ($response as $rowId => $resultAction) {
                        if ($resultAction == 1) {
                            if (isset($arItems[$rowId])) {
                                // удаляем запись
                                $this->goodExportTable()->delete($rowId);
                                // удаляем картинки
                                $this->photo()->getTable()->deleteByProduct($arItems[$rowId]['PRODUCT_ID'], $arItems[$rowId]['OFFER_ID'], $this->exportItem()->getGroupId());
                                $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETED', ['#PRODUCT_ID#' => $arItems[$rowId]['PRODUCT_ID'], '#OFFER_ID#' => $arItems[$rowId]['OFFER_ID']]));
                                $state['deleted']++;
                            }
                            unset($arItems[$rowId]);
                        }
                    }
                    // если например вручную были удалены в вк
                    foreach ($arItems as $arItem) {
                        // удаляем запись
                        $this->goodExportTable()->delete($arItem['ID']);
                        // удаляем картинки
                        $this->photo()->getTable()->deleteByProduct($arItem['PRODUCT_ID'], $arItem['OFFER_ID'], $this->exportItem()->getGroupId());
                        $this->log()->ok($this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.ITEM_DELETED', ['#PRODUCT_ID#' => $arItem['PRODUCT_ID'], '#OFFER_ID#' => $arItem['OFFER_ID']]));
                        $state['deleted']++;
                    }
                }
                // меняем отступ и список оставшихся id
                $state['offset'] += count($part);
                // обрезаем
                $state['arId'] = array_slice($state['arId'], $state['limit']);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // продолжаем
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * Вернет массив ID записей о выгруженных товарах в вк, которые необходимо удалить из вк,
     * потому что они отсутствуют в подготовленном списке для выгрузки
     * 
     * Удаление товаров, которых больше не должно быть, ранее выгруженых
     * 
     * @return int[]
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \Bitrix\Main\ArgumentException
     */
    public function exportRunDeleteOldFromVKActionGetIdForDelete()
    {
        // находим все идентификаторы активных выгрузок, в туже группу
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
     * Удаление товаров из вк, которые больше недоступны
     * в локальном списке подготовленых для выгрузки
     * например если сменилась активность, доступность,
     * больше не подходит под условия экспорта
     * для старого варанта реализации
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
        // если нужно предотвратить удаление старых, либо дуликтаов
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // фиксируем состояние
            $this->state()->setField($stateKey, $state)->save();
            // возвращаем статус опреации
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // находим все идентификаторы активных выгрузок, в туже группу
            $arExportIds = $this->getActiveExportIds();
            $state['count'] = $this->exportRunDeleteOldFromVKBaseModeActionGetCount($arExportIds);
            if (\CModule::IncludeModuleEx("vkapi.mark" . "" . "" . "e" . "" . "t") === constant("MODULE_DEM" . "O_EXP" . "" . "" . "IRE" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET." . "DEMO_EXPIR" . "E" . "D"), "BXMAKER_DEMO_EXPIRE" . "D");
            }
            // цикл
            while ($arExportedItem = $this->exportRunDeleteOldFromVKBaseModeActionGetNext($arExportIds)) {
                $this->manager()->checkTime();
                $isHasMore = $this->exportRunDeleteOldFromVKBaseModeActionIsHashMore($arExportedItem['ID'], $arExportedItem['VK_ID'], $arExportIds);
                if (!$isHasMore) {
                    $state['arNeedDelete'][$arExportedItem['VK_ID']][] = $arExportedItem;
                }
                // просто удаляем запись из выгруженных
                $this->goodExportTable()->delete($arExportedItem['ID']);
                // удаляем связанные кратинки? offerId = 0, потому что при базовом режиме и объединении офер указывается как 0
                $this->photo()->getTable()->deleteByProduct($arExportedItem['PRODUCT_ID'], 0, $this->exportItem()->getGroupId());
                // меняем отступ и список оставшихся id
                $state['offset']++;
                if (count($state['arNeedDelete']) > 20) {
                    $state['deleted'] += $this->exportRunDeleteOldFromVKBaseModeActionDeleteInVkIds($state['arNeedDelete']);
                    $state['arNeedDelete'] = [];
                }
            }
            // если закончился проход, но товароа не набралось больше чем нужно
            if (count($state['arNeedDelete']) > 0) {
                $state['deleted'] += $this->exportRunDeleteOldFromVKBaseModeActionDeleteInVkIds($state['arNeedDelete']);
                $state['arNeedDelete'] = [];
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // продолжаем
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_OLD_FROM_VK', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * Вернет массив ID записей о выгруженных товарах в вк, которые необходимо удалить из вк,
     * потому что они отсутствуют в подготовленном списке для выгрузки
     * 
     * Удаление товаров, которых больше не должно быть, ранее выгруженых
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
     * Вернет массив с данными о выгрженном товаре в вк, которые необходимо удалить из вк,
     * потому что он отсутствуют в подготовленном списке для выгрузки
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
     * Проверит есть ли у другой записи из спсика выгруженных товаров такой же идентификатор товара вконтакте
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
     * Удаляет товары в вк, передавать VK_ID не более 25
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
        // делаем запрос к вк ---
        if (count($code)) {
            try {
                $resultRequest = $this->exportItem()->connection()->method('execute', ['code' => 'return {' . implode(',', $code) . '};']);
                $response = $resultRequest->getData('response');
                $deleted += count($code);
                // если например вручную были удалены в вк
                foreach ($arVkIdToItems as $vkId => $arItems) {
                    foreach ($arItems as $arItem) {
                        // удаляем картинки, в базовом режиме и объединении офер указывается как 0
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
     * Вернет массив идентификаторв активных выгрузок для текущей группы
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
     * Собираем втаблдице выгруженных все записи, которые дублируются, проверяя по ключу {PRODUCT_ID}_{OFFER_ID}
     * Удаление локльаных дубликатов из вк, которые были повторно добавлены и есть в базе
     */
    public function exportRunDeleteLocalDoublesFormVK()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunDeleteLocalDoublesFormVK';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = [
                //завершено
                'complete' => false,
                //процент выполнения
                'percent' => 0,
                // всего
                'count' => 0,
                // отступ
                'offset' => 0,
                // лимит на итерацию
                'limit' => 20,
                'deleted' => 0,
                'arId' => null,
            ];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // если нужно предотвратить удаление старых, либо дуликтаов
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $state['deleted'] = 0;
            $state['offset'] = 0;
            // фиксируем состояние
            $this->state()->setField($stateKey, $state)->save();
            // возвращаем статус опреации
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // собираем количество дубликатов
            if (empty($state['arId'])) {
                $state['arId'] = $this->goodExportTable()->getDoublesIdByGroupId($this->exportItem()->getGroupId());
                $state['count'] = count($state['arId']);
            }
            if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "ke" . "t") == constant("MODULE_DE" . "MO" . "_EXP" . "IR" . "" . "" . "ED")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX" . "PIR" . "E" . "" . "" . "D"), "BXMAKER_DEMO_EXPI" . "" . "RE" . "" . "" . "" . "" . "" . "D");
            }
            // цикл
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arId'], 0, $state['limit']);
                // делаем запрос к вк ---
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
                // удаляем обработанное
                $state['arId'] = array_slice($state['arId'], $state['limit']);
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_LOCAL_DOUBLES_FROM_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * Собирает товары в вк, проверяет есть ли такие в таблице выгруженных,
     * если нету, удаляет из вк
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
        // если нужно предотвратить удаление старых, либо дуликтаов
        if ($this->exportItem()->isDisabledOldItemDeleting()) {
            $state['deleted'] = 0;
            $state['offset'] = 0;
            $state['count'] = 0;
            $state['percent'] = 100;
            $state['complete'] = true;
            // фиксируем состояние
            $this->state()->setField($stateKey, $state)->save();
            // возвращаем статус опреации
            $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_UNKNOWN_IN_VK.DISABLED')]);
            return $result;
        }
        try {
            // получаем список товаров в вк
            if (empty($state['vkIds'])) {
                $state['vkIds'] = $this->getVkItemIdList($state['vkItems']);
                $state['count'] = count($state['vkIds']);
            }
            while (count($state['vkIds']) > 0) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['vkIds'], 0, $state['limit']);
                $arPartIds = array_combine($arPart, $arPart);
                // посомтрим какие товары нам известны
                $dbr = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => intval($this->exportItem()->getGroupId()), 'VK_ID' => $arPart]]);
                while ($ar = $dbr->fetch()) {
                    unset($arPartIds[$ar['VK_ID']]);
                }
                // удаляем оставшиеся
                // делаем запрос к вк ---
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
            // продолжаем
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
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_DELETE_UNKNOWN_IN_VK.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
     * 
     * Объединение в группы и разъединение товаров
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
            // пропускаем для базового режима
            if (!$this->exportItem()->isEnabledExtendedGoods()) {
                $state['count'] = 0;
                $state['offset'] = 0;
                $state['arId'] = [];
            }
            while (count($state['arId'])) {
                $this->manager()->checkTime();
                $productId = $state['arId'][0];
                $arItems = [];
                // посомтрим какие товары нам известны
                $dbr = $this->goodExportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['GROUP_ID' => $this->exportItem()->getGroupId(), 'PRODUCT_ID' => $productId]]);
                while ($ar = $dbr->fetch()) {
                    $arItems[] = $ar;
                }
                $arVkIds = array_column($arItems, 'VK_ID');
                try {
                    if ($this->exportItem()->isEnabledOfferCombine()) {
                        // объединяем только если количество больше 1
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
            // продолжаем
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arId']);
        }
        // фиксируем состояние
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'message' => $this->getMessage('EXPORT_RUN_GROUP_UNGROUP_ITEM.STATUS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count'], '#GROUPED#' => $state['grouped']])]);
        return $result;
    }
    /**
     * 
     * Вернет количество уникальных товаров с торговыми предложеняими
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
     * Вернет массив идентификаторов альбомов в вконтакте
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
     * Вернет массив альбомов выгруженных в вк для конкретной группы из текущей выгрузки
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
            // ограничим альбомами из вк ----------------
            $resultVkAlbums = $this->albumExport()->getVkAlbums();
            if ($resultVkAlbums->isSuccess()) {
                foreach ($resultVkAlbums->getData('items') as $item) {
                    $arVkId[$item['id']] = $item['id'];
                }
            } else {
                $this->log()->notice($this->getMessage('GET_ALBUMS_IN_VK_ERROR', ['#MSG#' => $resultVkAlbums->getFirstErrorMessage(), '#CODE#' => $resultVkAlbums->getFirstErrorCode()]));
            }
            // берем существующие альбомы -----
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
     * Вернет объект с результатом подготовки превью товаров в вк
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
        // собираем массив описывающий выгрузку
        $parseResult = $oExport->parseExportDataFromPostData();
        if ($parseResult->isSuccess()) {
            $this->exportItem()->setData($parseResult->getData('FIELDS'));
        } else {
            return $parseResult;
        }
        // формируем данные ---
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
            // ищем родительский id
            $productId = intval($arOffer['PROPERTY_' . $this->exportItem()->getLinkPropertyId() . '_VALUE']);
            if (!$productId) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_PRODUCT_ID_NOT_FOUND'), 'ERROR_OFFER_PRODUCT_ID_NOT_FOUND');
            }
            // првоеряем наличие основного товара ----------------
            $dbrElement = $this->iblockElementOld()->GetList([], ['IBLOCK_ID' => $this->exportItem()->getProductIblockId(), 'ID' => $productId], false, ['nTopCount' => 1], ['ID', 'IBLOCK_ID']);
            if (!$dbrElement->Fetch()) {
                return $result->addError($this->getMessage('PREVIEW_FOR_VK.ERROR_OFFER_PRODUCT_ID_NOT_FOUND'), 'ERROR_OFFER_PRODUCT_ID_NOT_FOUND');
            }
            // ищем все оферы
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