<?php

namespace VKapi\Market\Sale\Order;

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Text\Encoding;
use VKapi\Market\Connect;
use VKapi\Market\Manager;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для синхронизаций заказов
 */
class Sync
{
    public function __construct()
    {
    }
    /**
     * Ссылка на объект работы с таблицей настроек синхрониаций заказов
     * Fields: ID:int, ACTIVE:bool, ACCOUNT_ID:int, GROUP_ID:int, GROUP_NAME:str, PARAMS:array
     * @return \VKapi\Market\Sale\Order\SyncTable
     */
    public function table()
    {
        if (!isset($this->oTable)) {
            $this->oTable = new \VKapi\Market\Sale\Order\SyncTable();
        }
        return $this->oTable;
    }
    /**
     * Вернет ссылку на Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * Вернет сообщение
     */
    public function getMessage($name, $arReplace = null)
    {
        return $this->manager()->getMessage('LIB.SALE.ORDER.SYNC.' . $name, $arReplace);
    }
    /**
     * Вернет ссылку для указания в настройках Callback API сервера в ВКонтакте
     * @param $id
     * @return string
     */
    public function getApiCallbackUrl($id)
    {
        $id = intval($id);
        $req = \Bitrix\Main\Application::getInstance()->getContext()->getRequest();
        $uri = new \Bitrix\Main\Web\Uri(($req->isHttps() ? 'https://' : 'http://') . $req->getHttpHost() . $req->getRequestUri());
        $uri->deleteParams(['ID', 'lang']);
        $uri->setPath('/bitrix/tools/vkapi.market/callback.php');
        $uri->addParams(['syncId' => $id]);
        return $uri->getLocator();
    }
    /**
     * Основной обработчик запросов Callback API, который распределяет запросы по остальным
     */
    public function apiCallback()
    {
        try {
            $content = file_get_contents('php://input');
            $arData = \Bitrix\Main\Web\Json::decode($content);
            $syncId = \Bitrix\Main\Application::getInstance()->getContext()->getRequest()->get('syncId');
            $orderImport = new \VKapi\Market\Sale\Order\Import\Item($syncId);
            if (!$orderImport->syncItem()->isActive()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_ORDER_SYNC_NOT_ACTIVE', ['#ID#' => $orderImport->syncItem()->getId()]), 'ERROR_ORDER_SYNC_NOT_ACTIVE');
            }
            if (!$orderImport->syncItem()->isEventEnabled()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_CALLBACK_API_IS_DISABLED', ['#ID#' => $orderImport->syncItem()->getId()]), 'ERROR_CALLBACK_API_IS_DISABLED');
            }
            if ($orderImport->syncItem()->getEventSecret() != $arData['secret']) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_CALLBACK_API_SECRET', ['#ID#' => $orderImport->syncItem()->getId()]), 'ERROR_CALLBACK_API_SECRET');
            }
            switch ($arData['type']) {
                case 'confirmation':
                    $code = $this->apiCallbackActionConfirmation($orderImport, $arData);
                    echo $code;
                    break;
                case 'market_order_new':
                case 'market_order_edit':
                    $this->apiCallbackActionOrderCreteOrUpdate($orderImport, $arData);
                    echo $this->getMessage('API_CALLBACK_OK');
                    break;
                default:
                    throw new \VKapi\Market\Exception\BaseException($this->getMessage('UNKNOWN_API_CALLBACK_TYPE'), 'UNKNOWN_API_CALLBACK_TYPE');
            }
        } catch (\Throwable $ex) {
            $arErrorMore = ['TYPE' => 'SALE_ORDER_SYNC_API_CALLBACK'];
            if ($ex instanceof \Bitrix\Main\DB\SqlQueryException) {
                $arErrorMore['QUERY'] = $ex->getQuery();
            }
            if (isset($orderImport)) {
                $orderImport->log()->error($this->getMessage('ERROR_CALLBACK_API', ['#MSG#' => '[' . $ex->getCode() . '] ' . $ex->getMessage()]), $arErrorMore);
            }
            echo \Bitrix\Main\Text\Encoding::convertEncoding($ex->getMessage(), LANG_CHARSET, 'cp-1251');
        }
        \Bitrix\Main\Application::getInstance()->end();
    }
    /**
     * Проверит полученые данные и вернет код в случае если все верно,
     * иначе выбросит исключение
     * @param $oOrderImport Import\Item
     * @param $arData
     * @return mixed
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function apiCallbackActionConfirmation(\VKapi\Market\Sale\Order\Import\Item $oOrderImport, $arData)
    {
        return $oOrderImport->syncItem()->getEventCode();
    }
    /**
     * Добавление или изменнеие заков по событию из вк
     * @param \VKapi\Market\Sale\Order\Import\Item $oOrderImport
     * @param $arData
     */
    public function apiCallbackActionOrderCreteOrUpdate(\VKapi\Market\Sale\Order\Import\Item $oOrderImport, $arData)
    {
        $arVkOrderItem = $arData['object'];
        try {
            // обработка заказа
            $orderItem = new \VKapi\Market\Sale\Order\Item($oOrderImport->syncItem());
            // подгружаем данные по заказа
            if ($arVkOrderItemExtend = $oOrderImport->loadOrderByItem($arVkOrderItem)) {
                $orderItem->setVkOrder($arVkOrderItemExtend);
            } else {
                $orderItem->setVkOrder($arVkOrderItem);
            }
            if (!$orderItem->isExistOrder()) {
                $arItems = $oOrderImport->loadVkOrderItems($arVkOrderItem);
                $orderItem->setVkOrderItems($arItems);
                $createdOrderId = $orderItem->createOrder();
                $oOrderImport->log()->ok($this->getMessage('CREATED_ORDER', ['#VKORDER_ID#' => $arVkOrderItem['display_order_id'], '#GROUP_ID#' => $arVkOrderItem['group_id'], '#ORDER_ID#' => (int) $createdOrderId]));
            } else {
                $updatedOrderId = $orderItem->updateOrder();
                $oOrderImport->log()->ok($this->getMessage('UPDATED_ORDER', ['#VKORDER_ID#' => $arVkOrderItem['display_order_id'], '#GROUP_ID#' => $arVkOrderItem['group_id'], '#ORDER_ID#' => (int) $updatedOrderId]));
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            if ($ex instanceof \VKapi\Market\Exception\ORMException) {
                $oOrderImport->log()->error($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine(), $ex->getCustomData());
                return;
            }
            $oOrderImport->log()->error($ex->getMessage(), $ex->getCustomData());
        }
    }
}
?>