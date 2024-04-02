<?php

namespace VKapi\Market;

use Bitrix\Main\Web\HttpClient;

class Agent
{
    public static $lastError = null;
    /**
 * clear anticaptcha result in db agent
 * 
 * @return string
 */
    public static function clearAntiCaptchaResults()
    {
        $oManager = \VKapi\Market\Manager::getInstance();
        try {
            $oAntiCaptcha = \VKapi\Market\AntiCaptcha::getInstance();
            $oAntiCaptcha->clearAgent();
        } catch (\Throwable $ex) {
            $oLog = new \VKapi\Market\Export\Log($oManager->getLogLevel());
            $oLog->setExportId(0);
            $oLog->exception($ex);
            self::$lastError = $ex->getTraceAsString();
        }
        return '\\' . __METHOD__ . '();';
    }
    /**
 * Выгрузка подборок и товаров
 * 
 * @param int $exportId
 * @return string
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public static function exportData($exportId = 0)
    {
        try {
            $oManager = \VKapi\Market\Manager::getInstance();
            $timeout = max(10, intval($oManager->getParam('TIMEOUT', 45, '')));
            set_time_limit($timeout + 40);
            ignore_user_abort($timeout);
            $oVkExportParam = \VKapi\Market\Param::getInstance();
            // если нужно ожидать следюущего запуска
            if (!static::canRun()) {
                return '\\' . __METHOD__ . '(' . intval($exportId) . ');';
            }
            // если автоэкспорт работает
            if ($oVkExportParam->get('AUTO_EXPORT_STOP', 'N') == 'N') {
                // получаем спсиок выгрузок активных
                $arList = [];
                $dbr = \VKapi\Market\Manager::getInstance()->exportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['ACTIVE' => true, 'AUTO' => true]]);
                while ($ar = $dbr->fetch()) {
                    $arList[] = $ar['ID'];
                }
                // елси в спсике нет текущей выгрузки - то переходим к следующей
                if (!in_array($exportId, $arList)) {
                    $exportId = \VKapi\Market\Manager::getInstance()->getNextItem($arList, $exportId);
                }
                $exportId = intval($exportId);
                // если есть выгрузки, то выгружаем
                if ($exportId > 0) {
                    $result = \VKapi\Market\Manager::getInstance()->agentExportToVk($exportId);
                }
                // если включен периодический запуск и есть резхультат выгрузки
                if ($result instanceof \VKapi\Market\Result) {
                    // проверяем состояние выполнености
                    $stateData = $result->getData('state');
                    if ($stateData['complete']) {
                        if (end($arList) == $exportId) {
                            if ($nextStart = static::completeAll()) {
                                // то добавляем нового агента, который начент экспорт по заданному времени
                                $oAgent = new \CAgent();
                                $oAgent->AddAgent('\\VKapi\\Market\\Agent::exportData();', 'vkapi.market', 'N', 50, "", 'Y', $nextStart->format('d.m.Y H:i:s'));
                                // а текущий будет автоматически удален
                                return '';
                            }
                        }
                        $exportId = \VKapi\Market\Manager::getInstance()->getNextItem($arList, $exportId);
                    }
                } else {
                    if ($nextStart = static::completeAll()) {
                        // то добавляем нового агента, который начент экспорт по заданному времени
                        $oAgent = new \CAgent();
                        $oAgent->AddAgent('\\VKapi\\Market\\Agent::exportData();', 'vkapi.market', 'N', 50, "", 'Y', $nextStart->format('d.m.Y H:i:s'));
                        // а текущий будет автоматически удален
                        return '';
                    }
                }
            }
        } catch (\Throwable $ex) {
            $oLog = new \VKapi\Market\Export\Log($oManager->getLogLevel());
            $oLog->setExportId($exportId);
            $oLog->exception($ex);
            self::$lastError = $ex->getTraceAsString();
        }
        return '\\' . __METHOD__ . '(' . intval($exportId) . ');';
    }
    /**
 * Выгрузка подборок и товаров по крон заданию
 * 
 * @param int $exportId
 * @return string
 * @throws \Bitrix\Main\ObjectPropertyException
 * @throws \Bitrix\Main\SystemException
 * @throws \Bitrix\Main\ArgumentException
 */
    public static function cron()
    {
        $oVkExportParam = \VKapi\Market\Param::getInstance();
        $oState = new \VKapi\Market\State('cron');
        $oManager = \VKapi\Market\Manager::getInstance();
        $timeout = max(10, intval($oManager->getParam('TIMEOUT', 45, '')));
        set_time_limit($timeout + 40);
        ignore_user_abort(false);
        if (!static::canRun()) {
            return false;
        }
        $cronStateData = array_merge(['exportId' => 0], $oState->get());
        $exportId = $cronStateData['exportId'];
        try {
            // если автоэкспорт работает
            if ($oVkExportParam->get('AUTO_EXPORT_STOP', 'N') == 'N') {
                // получаем спсиок выгрузок активных
                $arList = [];
                $dbr = \VKapi\Market\Manager::getInstance()->exportTable()->getList(['order' => ['ID' => 'ASC'], 'filter' => ['ACTIVE' => true, 'AUTO' => true]]);
                while ($ar = $dbr->fetch()) {
                    $arList[] = $ar['ID'];
                }
                if (!in_array($exportId, $arList)) {
                    $exportId = \VKapi\Market\Manager::getInstance()->getNextItem($arList, $exportId);
                }
                $exportId = intval($exportId);
                // если есть выгрузки, то выгружаем
                if ($exportId > 0) {
                    $result = \VKapi\Market\Manager::getInstance()->agentExportToVk($exportId);
                }
                // если включен периодический запуск и есть резхультат выгрузки
                if ($result instanceof \VKapi\Market\Result) {
                    // проверяем состояние выполнености
                    $stateData = $result->getData('state');
                    if ($stateData['complete']) {
                        if (end($arList) == $exportId) {
                            static::completeAll();
                        }
                        $exportId = \VKapi\Market\Manager::getInstance()->getNextItem($arList, $exportId);
                    }
                }
            }
        } catch (\Throwable $ex) {
            $oLog = new \VKapi\Market\Export\Log($oManager->getLogLevel());
            $oLog->setExportId($exportId);
            $oLog->exception($ex);
        }
        $oState->set(['exportId' => intval($exportId)]);
        $oState->save();
    }
    /**
 * Првоерк можно ли запустить
 * @return false|void
 */
    public static function canRun()
    {
        $timeToExec = \VKapi\Market\Manager::getInstance()->base()->getParam('TIME_TO_START_EXEC', '', '');
        if (!preg_match('/^(\\d\\d):(\\d\\d)$/', trim($timeToExec), $match)) {
            return true;
        }
        $oVkExportParam = \VKapi\Market\Param::getInstance();
        // текущее
        $curTime = new \Bitrix\Main\Type\DateTime();
        // граница
        $borderTime = new \Bitrix\Main\Type\DateTime();
        $borderTime->setTime(intval($match[1]), intval($match[2]));
        // время следующего запуска
        $nextTimestamp = intval($oVkExportParam->get('CRON_EXEC_NEXT', '0'));
        if ($nextTimestamp <= 0) {
            $nextTimestamp = time();
        }
        $nextTime = \Bitrix\Main\Type\DateTime::createFromTimestamp($nextTimestamp);
        // если текущее время еще не дошло до барьера, останавливаем экспорт
        if ($curTime->getTimestamp() < $nextTime->getTimestamp()) {
            return false;
        }
        return true;
    }
    /**
 * @return \Bitrix\Main\Type\DateTime|bool
 */
    public static function completeAll()
    {
        $timeToExec = \VKapi\Market\Manager::getInstance()->base()->getParam('TIME_TO_START_EXEC', '', '');
        if (!preg_match('/^(\\d\\d):(\\d\\d)$/', trim($timeToExec), $match)) {
            return false;
        }
        $oVkExportParam = \VKapi\Market\Param::getInstance();
        $curTime = new \Bitrix\Main\Type\DateTime();
        // граница
        $borderTime = new \Bitrix\Main\Type\DateTime();
        $borderTime->setTime(intval($match[1]), intval($match[2]));
        if (intval($curTime->format('Hi')) > intval($borderTime->format('Hi'))) {
            $borderTime->add('P1D');
        }
        $oVkExportParam->set('CRON_EXEC_NEXT', $borderTime->getTimestamp());
        return $borderTime;
    }
    /**
 * Очистка старых записей в таблице выгруженых товаров в течение времени для соблюдения лимитов
 * 
 * @return string
 */
    public static function clearLimit()
    {
        \VKapi\Market\Export\Limit\GoodTable::deleteOld();
        return '\\' . __METHOD__ . '();';
    }
}
?>