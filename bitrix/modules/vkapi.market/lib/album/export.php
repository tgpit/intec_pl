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
 * Связь локальных подборок и в вконтакте
 * Class ExportTable
 * 
 * Поля
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
            //идентификатор группы
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('ALBUM_ID', [
            //идентификатор подборки локальный
            'required' => true,
        ]), new \Bitrix\Main\Entity\IntegerField('VK_ID', [
            //идентификатор подборки в вк, положительное целое число или  null
            'required' => false,
            'default_value' => NULL,
        ]), new \Bitrix\Main\Entity\StringField('HASH', [
            //hash подготовленных полей, для исключения лишних обновлений на стороне вк
            'required' => true,
        ]), new \Bitrix\Main\Entity\ExpressionField('CNT', 'COUNT(*)'), new \Bitrix\Main\Entity\ReferenceField('ITEM', '\\VKapi\\Market\\Album\\ItemTable', ['=this.ALBUM_ID' => 'ref.ID'], ['join_type' => 'LEFT'])];
    }
    /**
 * Удаление информации о выгруженом альбоме в вк по его локальному ID
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
 * Удаление всех связей между локальными подборками
 * и подборками в вк по идентификатору группы
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
 * Выгрузка подборок в вконтакте
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
 * @var \VKapi\Market\State Состояние
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
 * Таблица выгруженных разделов
 * Поля
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
 * Вернет объект для работы с парамтерами выгрузки
 * 
 * @return \VKapi\Market\Export\Item
 */
    public function exportItem()
    {
        return $this->oExportItem;
    }
    /**
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
 * Вернет объект для хранения состояния
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
 * Экспортирует данные по подборкам (альбомам) в вконтакте
 * 
 * @return \VKapi\Market\Result - веозвращает результат в объекте
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
                //все шаги, которые есть, в процессе работы, могут меняться сообщения, например о работано 2 из 10
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
        // фиксируем запуск
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
                    // проверяем какие альбомы на месте в вк,
                    // какие были удалены и нужно добавить их повторно,
                    // какие удалены из выгрузки и нужно удалить из вк
                    // не изменился ли порядок альбомов в выгрузке и в вк
                    $resultCheckAlbumVk = $this->exportRunCheckAlbumInVk();
                    $data['steps'][2]['percent'] = $resultCheckAlbumVk->getData('percent');
                    // если операция закончена
                    if ($resultCheckAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][2]['name'] = $this->getMessage('STEP2');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 2, '#STEP_NAME#' => $this->getMessage('STEP2')]));
                    } else {
                        $data['steps'][2]['name'] = $resultCheckAlbumVk->getData('name');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name'], '#PERCENT#' => $data['steps'][2]['percent']]));
                    }
                    break;
                // экпортируются кратинки
                case 3:
                    // проверка и выгрузка картинок альбомов
                    $resultCheckAlbumPhotoInVk = $this->exportRunCheckAlbumPhotoInVk();
                    $data['steps'][3]['percent'] = $resultCheckAlbumPhotoInVk->getData('percent');
                    $data['steps'][3]['name'] = $resultCheckAlbumPhotoInVk->getData('name');
                    // если операция закончена
                    if ($resultCheckAlbumPhotoInVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][3]['name'] = $this->getMessage('STEP3');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 3, '#STEP_NAME#' => $this->getMessage('STEP3')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 3, '#STEP_NAME#' => $data['steps'][3]['name'], '#PERCENT#' => $data['steps'][3]['percent']]));
                    }
                    break;
                // обновление у альбомов ранее выгруженных названий, картинок
                case 4:
                    // обновляем альбомы альбомы
                    $resultUpdateAlbumVk = $this->exportRunUpdateAlbumInVK();
                    $data['steps'][4]['percent'] = $resultUpdateAlbumVk->getData('percent');
                    $data['steps'][4]['name'] = $resultUpdateAlbumVk->getData('name');
                    // если операция закончена
                    if ($resultUpdateAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][4]['name'] = $this->getMessage('STEP4');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 4, '#STEP_NAME#' => $this->getMessage('STEP4')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 4, '#STEP_NAME#' => $data['steps'][4]['name'], '#PERCENT#' => $data['steps'][4]['percent']]));
                    }
                    break;
                // добавление отсутствующих альбомов
                case 5:
                    // добавляем отсутствующие альбомы
                    $resultAddAlbumVk = $this->exportRunAddAlbumToVK();
                    $data['steps'][5]['percent'] = $resultAddAlbumVk->getData('percent');
                    $data['steps'][5]['name'] = $resultAddAlbumVk->getData('name');
                    // если операция закончена
                    if ($resultAddAlbumVk->getData('complete')) {
                        $data['step']++;
                        $data['steps'][5]['name'] = $this->getMessage('STEP5');
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.OK', ['#STEP#' => 5, '#STEP_NAME#' => $this->getMessage('STEP5')]));
                    } else {
                        $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STEP.PROCESS', ['#STEP#' => 5, '#STEP_NAME#' => $data['steps'][5]['name'], '#PERCENT#' => $data['steps'][5]['percent']]));
                    }
                    break;
                // изменение порядка
                case 6:
                    // добавляем отсутствующие альбомы
                    $resultReorderAlbumInVK = $this->exportRunReorderAlbumInVK();
                    $data['steps'][6]['percent'] = $resultReorderAlbumInVK->getData('percent');
                    $data['steps'][6]['name'] = $resultReorderAlbumInVK->getData('name');
                    // если операция закончена
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
        // считаем выполненый процент
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] == 100) {
            $data['complete'] = true;
            $this->log()->notice($this->getMessage('EXPORT_ALBUMS.STOP'));
        }
        if (\CModule::IncludeModuleEx("v" . "ka" . "pi.mar" . "" . "ke" . "t") === constant("M" . "ODULE_D" . "EMO_EXPI" . "R" . "" . "" . "" . "E" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEM" . "O_E" . "" . "XP" . "IR" . "E" . "" . "" . "D"), "BXMAKE" . "R_DEMO_E" . "XPIRE" . "D");
        }
        // заканчиваем
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
 * Проверяет наличие в вк альбомов,
 * если все на месте, удалит лишние и вернут результат операции
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
            // собираем альбомы которые добавлены
            $arExportedAlbum = $this->getAlbums();
            // собираем альбомы котоыре есть в вк
            $resultVkAlbums = $this->getVkAlbums();
            // если в вк нет альбомов ----------------------------------
            if ($resultVkAlbums->getData('count') <= 0) {
                // удаляем все связи с альбомами в вк, чтобы добавились заново
                foreach ($arExportedAlbum as $arExportedAlbumItem) {
                    $this->albumExportTable()->delete($arExportedAlbumItem['ID']);
                }
                // проставляем отметки
                foreach ($state['steps'] as $step => $stepValue) {
                    $state['steps'][$step]['percent'] = 100;
                }
                // считаем выполненый процент
                $state['step'] = count($state['steps']);
                $state['percent'] = 100;
                $state['complete'] = true;
                // фиксируем состояние
                $this->state()->setField($stateKey, $state)->save();
                // возвращаем статус опреации
                $result->setDataArray($state);
                $this->log()->notice($this->getMessage('CHECK_ALBUM_VK.ALBUMS_NOT_FOUND'));
                return $result;
            }
            // если в вк есть альбомы -------------------
            // берем все альбомы
            $arVkItems = $resultVkAlbums->getData('items');
            // строим соответствия [ itemId => localALbumId, ...]
            $arExportedAlbumVkId2LocalAlbumId = array_column($arExportedAlbum, 'ALBUM_ID', 'VK_ID');
            // строим соответствия для текущей группы, даже из других выгрузок [vkAlbumId => localAlbumId, ...]
            $arVkId2LocalAlbumId = $this->getVkItemId2LocalAlbumId($arVkItems, $arExportedAlbumVkId2LocalAlbumId);
            // все альбомы которые выгружаются в текущую группу
            $arOtherExportsAlbumId = $this->getOtherExportsAlbumId();
            // удаляем из локлаьной базы связи, которых нет в вк
            if ($state['step'] == 1) {
                // определяем отсутствующие в вк [localAlbumId, ...] - удалены были в соц сети
                $arDiff = array_diff(array_values($arExportedAlbumVkId2LocalAlbumId), $arVkId2LocalAlbumId);
                if (count($arDiff)) {
                    $this->deleteByAlbumId($arDiff);
                    // из предрасчетных данных тоже убираем
                    $arVkId2LocalAlbumId = array_flip($arVkId2LocalAlbumId);
                    foreach ($arDiff as $localAlbumId) {
                        unset($arVkId2LocalAlbumId[$localAlbumId]);
                    }
                }
                $state['step']++;
                $state['steps'][1]['percent'] = 100;
                $state['name'] = $state['steps'][1]['name'];
            } elseif ($state['step'] == 2) {
                // теперь проверяем альбомы в вк, которых быть не должно
                $arDiff = array_diff($arVkId2LocalAlbumId, $arOtherExportsAlbumId);
                if (count($arDiff)) {
                    $resultDelete = $this->exportRunCheckAlbumInVkActionDeleteLocalAlbumFromVk($arDiff);
                    $data['steps'][2]['percent'] = $resultDelete->getData('percent');
                    $data['steps'][2]['name'] = $resultDelete->getData('name');
                    // успешно удалено
                    if ($resultDelete->getData('complete')) {
                        $state['step']++;
                    }
                } else {
                    $state['step']++;
                    $state['steps'][2]['percent'] = 100;
                }
                $state['name'] = $state['steps'][2]['name'];
            } elseif ($state['step'] == 3) {
                // теперь проверяем альбомы в вк, которых быть не должно
                $arDiff = array_diff($arVkId2LocalAlbumId, $arOtherExportsAlbumId);
                // првоерим наличие неучетных альбомовв вк
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray($state);
        return $result;
    }
    /**
 * Вернет массив альбомов из группы ВКонтакте
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
                // если отобрали не все альбомы, то повторяем запрос
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
 * Вренет массив альбомов которые уже есть в локальной базе
 * 
 * @return array - массив выгруженных альбомов [localId => arItem, localId=> arItem, ...]
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
 * Вернет массив существующих локальных альбомов {localAlbumId => arLocalAlbum, ...}
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
 * Массив идентификаторов подборок локальных, которые нужно удалить из вк
 * 
 * @param array $arLocalAlbumId - массив подборок локальных, которые нужно удалить в вк
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
        // пропускаем при необходимости
        if ($this->exportItem()->isDisabledOldAlbumDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            $this->state()->setField($stateKey, $state)->save();
            // возвращаем статус опреации
            $result->setDataArray(['offset' => 0, 'count' => 0, 'complete' => $state['complete'], 'percent' => $state['percent'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.DISABLED')]);
            return $result;
        }
        try {
            // получаем описание связей выгруженых альбомов
            $arExportedAlbum = $this->getAlbums();
            // формируем соответствия выгруженных альбомов [localAlbumId => itemId, ...]
            $arExportedAlbumId2ItemId = array_column($arExportedAlbum, 'ID', 'ALBUM_ID');
            // оставим только существующие записи
            $arLocalAlbumId = array_intersect($arLocalAlbumId, array_keys($arExportedAlbumId2ItemId));
            if (empty($state['arItems'])) {
                $state['arItems'] = $arLocalAlbumId;
                $state['count'] = count($state['arItems']);
            }
            if (\CModule::IncludeModuleEx("vkapi.mark" . "" . "e" . "t") === constant("MODULE_DEM" . "O_EXPIRE" . "" . "" . "D")) {
                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXP" . "IRED"), "BXMAK" . "ER_DEM" . "O_" . "EXPIRED");
            }
            // проверка таймаута
            while (count($state['arItems'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arItems'], 0, 25);
                // подготовим массив для выгрузки
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
                    // удаляем локально альбомы и картинки альбомов
                    // если из вк успешно удалили
                    $this->deleteByAlbumId($arPart);
                } catch (\VKapi\Market\Exception\ApiResponseException $apiEx) {
                    $this->log()->error($this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.ERROR', ['#MSG#' => $apiEx->getMessage()]));
                    throw $apiEx;
                }
                $state['arItems'] = array_slice($state['arItems'], 25);
                $state['offset'] += count($arPart);
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arItems']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => $state['deleted'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_LOCAL_ALBUM_FROM_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
 * Массив идентификаторов альбомов для удаления из вк
 * 
 * @param array $arVkAlbumId - массив подборок в вк, которые нужно удалить в вк
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
        // если нужно пропустить удаление из вк
        if ($this->exportItem()->isDisabledOldAlbumDeleting()) {
            $state['complete'] = true;
            $state['percent'] = 100;
            // фиксируем состояние
            $this->state()->setField($stateKey, $data[$stateKey])->save();
            // возвращаем статус опреации
            $result->setDataArray(['count' => 0, 'offset' => 0, 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => 0, 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.DISABLED')]);
            return $result;
        }
        if (empty($state['arItems'])) {
            $state['arItems'] = array_diff($arVkAlbumId, [0, -1]);
            $state['count'] = count($state['arItems']);
        }
        try {
            // проверка таймаута
            while (count($state['arItems'])) {
                $this->manager()->checkTime();
                $arPart = array_slice($state['arItems'], 0, 25);
                // подготовим массив для выгрузки
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
            unset($state['arItems']);
        }
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'deleted' => $state['deleted'], 'name' => $this->getMessage('CHECK_ALBUM_IN_VK_ACTION_DELETE_UNKNOWN_ALBUM_FROM_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset'], '#DELETED#' => $state['deleted']])]);
        return $result;
    }
    /**
 * Проверяет какие загруженные картинки в вк еще активны,
 * обновляет не активные и загружает новые для альбомов
 * 
 * @return \VKapi\Market\Result - результат, сождержащий статус операци {complete: bool, count: int, offset: int}
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public function exportRunCheckAlbumPhotoInVk()
    {
        $result = new \VKapi\Market\Result();
        // получаем массив альбомов которые должны быть выгружены в вконтакте
        $arAlbumId = $this->exportItem()->getAlbumIds();
        $result = new \VKapi\Market\Result();
        $stateKey = 'exportRunCheckAlbumPhotoInVk';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => false, 'complete' => false, 'percent' => 0, 'count' => 0, 'offset' => 0, 'limit' => 25, 'arId' => null];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        // елси нет альбомов, значит ничего делать не надо
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
                // собираем картинки для каждого альбома
                $arAlbumIdToPictureId = [];
                $dbr = $this->item()->table()->getList(['filter' => ['ID' => $arAlbumIdPart], 'select' => ['ID', 'PICTURE']]);
                while ($arAlbumItem = $dbr->fetch()) {
                    $arAlbumIdToPictureId[$arAlbumItem['ID']] = $arAlbumItem['PICTURE'];
                }
                try {
                    // картинки собрали, теперь прверяем какие уже выгружены
                    // обновляем и загружаем новые
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // возвращаем статус опреации
        $result->setDataArray(['offset' => $state['offset'], 'count' => $state['count'], 'complete' => $state['complete'], 'percent' => $state['percent'], 'name' => $this->getMessage('CHECK_ALBUM_PHOTO_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * Вернет идентификаторы альбомов с загруженными картинками
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
        // елси есть альбомы, получаем общее количество
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
 * Обновляет подборки в вконтакте
 * 
 * @return \VKapi\Market\Result - результат, сождержащий статус операци {complete: bool, count: int, offset: int}
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
                    // получаем партию ---
                    $state['count'] = $this->albumExportTable()->getCount(['GROUP_ID' => $this->exportItem()->getGroupId(), 'ALBUM_ID' => $arExportAlbumsId]);
                }
            }
            // если есть что добавлять
            while ($state['count'] > $state['offset']) {
                $this->manager()->checkTime();
                // получаем партию ---
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
                // подготовим массив для выгрузки
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
                            // обновим хэш
                            $this->albumExportTable()->update($albumId, ['HASH' => $this->getHash($arAlbumsIdNeedUpdate[$albumId])]);
                            $this->log()->ok($this->getMessage('UPDATE_ALBUM_IN_VK.UPDATED', ['#ALBUM_ID#' => $albumId, '#VK_ID#' => $albumData['VK_ID']]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->error($this->getMessage('UPDATE_ALBUM_IN_VK.UPDATE_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $albumId]));
                                if ($executeErrors[$executeErrorIndex]['error_code'] == \VKapi\Market\Api::ERROR_100 && preg_match('/\\:\\s+photo\\s+/', $executeErrors[$executeErrorIndex]['error_msg'])) {
                                    // удаляем картинки
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // передаем результат
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'name' => $this->getMessage('UPDATE_ALBUM_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * Выгружает подборки в вконтакте
 * 
 * @return \VKapi\Market\Result - результат, сождержащий статус операци {complete: bool, count: int, offset: int}
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
                // альбомы которые должны быть
                $arExportAlbumsId = $this->exportItem()->getAlbumIds();
                // получаем выгруженные альбомы
                $arExportedAlbums = $this->getAlbums();
                $arExportedAlbumsId2ItemId = array_column($arExportedAlbums, 'ID', 'ALBUM_ID');
                // [ albumId => itemId, ...]
                $state['arId'] = array_diff($arExportAlbumsId, array_keys($arExportedAlbumsId2ItemId));
                $state['count'] = count($state['arId']);
            }
            // если есть что добавлять
            while (!empty($state['arId'])) {
                $this->manager()->checkTime();
                // обрезаем массив
                $arAlbumsIdAdd = array_splice($state['arId'], 0, $state['limit']);
                $arAlbumsData = $this->item()->getItemsById($arAlbumsIdAdd);
                $arPicturesId = array_column($arAlbumsData, 'PICTURE', 'ID');
                $arAlbumsDataPhoto = $this->photo()->getItemsByFileId($arPicturesId, $this->exportItem()->getGroupId());
                // подготовим массив для выгрузки
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
                            // так как это добавление, то добавляем связи
                            $this->albumExportTable()->add(['GROUP_ID' => $this->exportItem()->getGroupId(), 'ALBUM_ID' => $albumId, 'VK_ID' => $arAlbumAddResultArray['market_album_id'], 'HASH' => $this->getHash($arAlbumsData[$albumId])]);
                            $this->log()->ok($this->getMessage('ADD_ALBUM_TO_VK.ADDED', ['#ALBUM_ID#' => $albumId, '#VK_ID#' => $arAlbumAddResultArray['market_album_id']]));
                        } else {
                            $executeErrorIndex++;
                            if (isset($executeErrors[$executeErrorIndex])) {
                                $this->log()->error($this->getMessage('ADD_ALBUM_TO_VK.ADD_ERROR', ['#MSG#' => $executeErrors[$executeErrorIndex]['error_code'] . ' ' . $executeErrors[$executeErrorIndex]['error_msg'], '#ALBUM_ID#' => $albumId]));
                                if ($executeErrors[$executeErrorIndex]['error_code'] == \VKapi\Market\Api::ERROR_100 && preg_match('/\\:\\s+photo\\s+/', $executeErrors[$executeErrorIndex]['error_msg'])) {
                                    // удаляем картинки
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        // очистка диреткорий
        $this->photo()->deleteTemporaryDirectories();
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // передаем результат
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'added' => $state['added'], 'name' => $this->getMessage('ADD_ALBUM_TO_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * Изменение порядка альбомов в вконтакте
 * 
 * @return \VKapi\Market\Result - результат, сождержащий статус операци {complete: bool, count: int, offset: int}
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
            // альбомы которые должны быть
            $arExportAlbumsId = $this->exportItem()->getAlbumIds();
            // получаем выгруженные альбомы
            $arExportedAlbums = $this->getAlbums();
            $arExportedAlbumsId2VkId = array_column($arExportedAlbums, 'VK_ID', 'ALBUM_ID');
            // [ albumId => itemId, ...]
            $arExportVkAlbumId = [];
            foreach ($arExportAlbumsId as $albumId) {
                $arExportVkAlbumId[] = $arExportedAlbumsId2VkId[$albumId];
            }
            // строим дерево порядка альбомов
            $arNeedTree = $this->getAlbumOrderTree($arExportVkAlbumId);
            // ограничим альбомами из вк ----------------
            $arVkAlbumId = [];
            $resultVkAlbums = $this->getVkAlbums();
            foreach ($resultVkAlbums->getData('items') as $item) {
                $arVkAlbumId[] = $item['id'];
            }
            $arVkTree = $this->getAlbumOrderTree(array_values(array_intersect($arVkAlbumId, $arExportVkAlbumId)));
            $state['count'] = count($arNeedTree);
            // подготовим массив для выгрузки
            $arCode = [];
            foreach ($arNeedTree as $vkSortId => $vkNeedSort) {
                // если в вк есть такой альбом
                if (isset($arVkTree[$vkSortId])) {
                    // проверяем значения старта
                    // если есть after и альбом after есть в вк
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
            // если есть что добавлять
            while (count($arCode) > 0) {
                $this->manager()->checkTime();
                // обрезаем массив
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
            // елси таймаут по времени, то сохраняем состояние и идем дальше
        }
        $state['percent'] = $this->state()->calcPercentByData($state);
        if ($state['percent'] == 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // передаем результат
        $result->setDataArray(['complete' => $state['complete'], 'percent' => $state['percent'], 'offset' => $state['offset'], 'count' => $state['count'], 'name' => $this->getMessage('REORDER_ALBUM_IN_VK.STATUS', ['#COUNT#' => $state['count'], '#OFFSET#' => $state['offset']])]);
        return $result;
    }
    /**
 * Вернет дерево сортировки, {albumId : {b:int,a:int}}
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
 * Сформирует массив соответствий альбомов из вк, локлаьным подборкам
 * 
 * @param array $arVkItems - массив подборок, полученных из вк [{id => 620,..}, ...]
 * @param array $arExportedALbumVkId2LocalAlbumId - массив соответствий выгруженных ранее альбомов в вк и их
 * локальным идентификаторам [619 => 1, 620 => 2, ...]
 * @return array [620 => 2, ...]
 */
    protected function getVkItemId2LocalAlbumId($arVkItems, $arExportedALbumVkId2LocalAlbumId)
    {
        $arReturn = [];
        // строим массив идентфикаторов альбомов из вк
        $arVkItemsId = array_column($arVkItems, 'id');
        // [620,621,625,630]
        foreach ($arVkItemsId as $id) {
            if (isset($arExportedALbumVkId2LocalAlbumId[$id])) {
                $arReturn[$id] = $arExportedALbumVkId2LocalAlbumId[$id];
            } else {
                // дубликат похоже
                $arReturn[$id] = 0;
            }
        }
        return $arReturn;
    }
    /**
 * Удаление записей о выгруженных альбомах в вк
 * 
 * @param array $arAlbumId - массив локальных подборок, записи о которых нужно удалить
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
        // запрашиваем инфу о подборках
        if (count($arItemsId)) {
            $arAlbumItems = $this->item()->getItemsById(array_keys($arAlbumId2ItemId));
            foreach ($arItemsId as $itemId) {
                // удаление записи ссылки на альбом в вк
                $this->albumExportTable()->delete($itemId);
                $this->log()->notice($this->getMessage('DELETE_BY_ALBUM_ID', ['#ALBUM_ID#' => $arExportedAlbums[$itemId]['ALBUM_ID']]), ['ALBUM_ID' => $arExportedAlbums[$itemId]['ALBUM_ID']]);
                // удаление картинки
                if (isset($arAlbumItems[$arAlbumId2ItemId[$itemId]]) && intval($arAlbumItems[$arAlbumId2ItemId[$itemId]]['PICTURE'])) {
                    $this->photo()->deleteByFileId($arAlbumItems[$arAlbumId2ItemId[$itemId]]['PICTURE'], $this->exportItem()->getGroupId());
                }
            }
        }
        return true;
    }
    /**
 * Вернет отличия в порядке следования значений
 * [1,2,5] и [1,6] -> [2,5]
 * [1,5] и [1,6,5] -> [5]
 * 
 * @param $arCompare - сравниваемый порядок
 * @param $arOriginal - оригинальный порядок, с которым сравнение идет
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
 * Вернет хэш полей альбома выгруаемого в вк, чтобы затем сверять
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
 * Вернет массив альбомов со всех выгрузок для текущей группы
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