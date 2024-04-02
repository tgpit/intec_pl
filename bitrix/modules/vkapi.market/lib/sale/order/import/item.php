<?php

namespace VKapi\Market\Sale\Order\Import;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Connect;
use VKapi\Market\Manager;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\TimeoutException;
use VKapi\Market\Exception\ApiResponseException;
use VKapi\Market\Exception\ORMException;
use VKapi\Market\Result;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для импортирования заказов из под конкретной настройке ссинхронизации. Запрашивает данные из ВК и сохраняет/обновляет локальные данные
 */
class Item
{
    protected $syncId;
    public function __construct($syncId)
    {
        if (!$this->manager()->isInstalledSaleModule()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('MODULE_SALE_IS_NOT_INSTALLED'), 'ERROR_MODULE_SALE_NOT_FOUND');
        }
        $this->syncId = (int) $syncId;
    }
    /**
     * Вернет ссылку на Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * Вернет ссылку на Manager
     * @return \VKapi\Market\Sale\Order\Sync\Item
     */
    public function syncItem()
    {
        if (!isset($this->oSyncItem)) {
            $this->oSyncItem = new \VKapi\Market\Sale\Order\Sync\Item($this->syncId);
        }
        return $this->oSyncItem;
    }
    /**
     * Вернет ссылку объект для рабоыт с текущим состоянием выполнения
     * @return \VKapi\Market\State
     */
    public function state()
    {
        if (!isset($this->oState)) {
            $this->oState = new \VKapi\Market\State('sync_' . $this->syncItem()->getId(), 'order_import');
        }
        return $this->oState;
    }
    /**
     * Вернет объект для рабоыт с логом
     * @return \VKapi\Market\Export\Log
     */
    public function log()
    {
        if (!isset($this->oLog)) {
            $this->oLog = new \VKapi\Market\Export\Log($this->manager()->getLogLevel());
            $this->oLog->setExportId(0);
        }
        return $this->oLog;
    }
    /**
     * Вернет объект для запросов к вк
     * @return \VKapi\Market\Connect
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function connection()
    {
        if (is_null($this->oConnection)) {
            $this->oConnection = new \VKapi\Market\Connect();
            $result = $this->oConnection->initAccountId($this->syncItem()->getAccountId());
            if (!$result->isSuccess()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_INIT_CONNECTION', ['#MSG#' => $result->getFirstErrorMessage(), '#CODE#' => $result->getFirstErrorCode()]), 'ERROR_INIT_CONNECTION');
            }
        }
        return $this->oConnection;
    }
    /**
     * Вернет сообщение
     */
    public function getMessage($name, $arReplace = null)
    {
        return $this->manager()->getMessage('LIB.SALE.ORDER.IMPORT.ITEM.' . $name, $arReplace);
    }
    /**
     * Проверка наличия доступа к API с нужными правами
     * @return \VKapi\Market\Result
     * @throws \VKapi\Market\Exception\ApiResponseException
     * @throws \VKapi\Market\Exception\BaseException
     * @throws \VKapi\Market\Exception\UnknownResponseException
     */
    public function checkApiAccess()
    {
        $result = new \VKapi\Market\Result();
        try {
            $resultOperation = $this->connection()->method('market.getGroupOrders', ['access_token' => $this->syncItem()->getGroupAccessToken(), 'group_id' => $this->syncItem()->getGroupId(), 'offset' => 0, 'count' => 1, 'extended' => 1]);
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            $result->addError($ex->getMessage(), $ex->getCustomCode(), $ex->getCustomData());
        }
        return $result;
    }
    /**
     * Запуск импорта
     * @return \VKapi\Market\Result
     */
    public function run()
    {
        $result = new \VKapi\Market\Result();
        $data = $this->state()->get();
        // ожидание завершения
        if (!empty($data) && $data['run'] && $data['timeStart'] > time() - 60 * 3) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('RUN.WAIT_FINISH'), 'WAIT_FINISH');
        }
        // установка базового состояния
        if (empty($data) || !isset($data['step']) || $data['complete']) {
            $this->state()->set(['complete' => false, 'percent' => 0, 'step' => 1, 'steps' => [1 => ['name' => $this->getMessage('RUN.STEP1'), 'percent' => 0, 'error' => false], 2 => ['name' => $this->getMessage('RUN.STEP2'), 'percent' => 0, 'error' => false]]]);
            $data = $this->state()->get();
            $this->log()->notice($this->getMessage('RUN.START'));
        }
        // фиксируем запуск
        $this->state()->set(['run' => true, 'timeStart' => time()])->save();
        // выполнение
        try {
            switch ($data['step']) {
                case 1:
                    $result = $this->checkApiAccess();
                    if ($result->isSuccess()) {
                        $data['step']++;
                        $data['steps'][1]['percent'] = 100;
                        $this->log()->notice($this->getMessage('RUN.STEP1.OK', ['#STEP#' => 1, '#STEP_NAME#' => $this->getMessage('RUN.STEP1')]));
                    } else {
                        $data['steps'][1]['error'] = true;
                        $this->log()->error($result->getFirstErrorMessage(), $result->getFirstErrorMore());
                        return $result;
                    }
                    break;
                case 2:
                    $resultOrdersImport = $this->runOrdersImport();
                    $data['steps'][2]['percent'] = $resultOrdersImport->getData('percent');
                    $data['steps'][2]['name'] = $resultOrdersImport->getData('name');
                    // если операция закончена
                    if ($resultOrdersImport->getData('complete')) {
                        $data['step']++;
                        $this->log()->notice($this->getMessage('RUN.STEP2.OK', ['#STEP#' => 2, '#STEP_NAME#' => $this->getMessage('RUN.STEP2')]));
                    } else {
                        $this->log()->notice($this->getMessage('RUN.STEP2.PROCESS', ['#STEP#' => 2, '#STEP_NAME#' => $data['steps'][2]['name'], '#PERCENT#' => $data['steps'][2]['percent']]));
                    }
                    break;
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            $this->log()->error($ex->getMessage(), $ex->getCustomData());
        }
        // считаем выполненый процент
        $data['percent'] = $this->state()->calcPercentByData($data);
        if ($data['percent'] == 100) {
            $data['complete'] = true;
            $this->log()->notice($this->getMessage('RUN.STOP'));
        }
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.marke" . "" . "" . "" . "" . "t") === constant("MOD" . "ULE_DE" . "MO_EXPIR" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_" . "EXPIRE" . "D"), "BXMAKER_DEMO_EXPIRE" . "D");
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
     * Импорт заказов
     * @return \VKapi\Market\Result|void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\ApiResponseException
     * @throws \VKapi\Market\Exception\BaseException
     * @throws \VKapi\Market\Exception\UnknownResponseException
     */
    public function runOrdersImport()
    {
        $result = new \VKapi\Market\Result();
        $stateKey = 'runOrdersImport';
        $data = $this->state()->get();
        if (!isset($data[$stateKey]) || $data[$stateKey]['complete']) {
            $data[$stateKey] = ['name' => $this->getMessage('RUN_ORDER_IMPORT'), 'complete' => false, 'count' => 0, 'offset' => 0, 'percent' => 0];
            $this->state()->setField($stateKey, $data[$stateKey])->save();
        }
        $state = $data[$stateKey];
        try {
            while (true) {
                // проверка таймаута
                $this->manager()->checkTime();
                $arQuery = ['access_token' => $this->syncItem()->getGroupAccessToken(), 'group_id' => $this->syncItem()->getGroupId(), 'offset' => $state['offset'], 'count' => 25, 'extended' => 1];
                $resultRequest = $this->connection()->method('market.getGroupOrders', $arQuery);
                $response = $resultRequest->getData('response');
                $state['count'] = $response['count'];
                if ($this->syncItem()->getImportLastCount()) {
                    $state['count'] = min($response['count'], $this->syncItem()->getImportLastCount());
                }
                if (!count($response['items'])) {
                    break;
                }
                if ($this->syncItem()->getImportLastCount()) {
                    $response['items'] = array_slice($response['items'], 0, max(0, $this->syncItem()->getImportLastCount() - $state['offset']));
                }
                \Bitrix\Main\Type\Collection::sortByColumn($response['items'], ['date' => SORT_ASC]);
                while ($item = array_shift($response['items'])) {
                    $this->manager()->checkTime();
                    // ограничение по дате начала импорта
                    if ($item['date'] < $this->syncItem()->getStartImportTimestamp()) {
                        $state['offset']++;
                        continue;
                    }
                    $this->log()->notice($this->getMessage('RUN_ORDER_IMPORT_ITEM', ['#VKORDER_ID#' => $item['display_order_id'], '#GROUP_ID#' => $item['group_id']]));
                    $this->runOrdersImportActionUpdateItem($item);
                    $state['offset']++;
                }
                if ($state['offset'] >= $state['count']) {
                    break;
                }
                if ($this->syncItem()->getImportLastCount() && $state['offset'] >= $this->syncItem()->getImportLastCount()) {
                    break;
                }
            }
        } catch (\VKapi\Market\Exception\TimeoutException $ex) {
        }
        // сохраняем
        $state['percent'] = $this->state()->calcPercent($state['count'], $state['offset']);
        if ($state['percent'] >= 100) {
            $state['complete'] = true;
        }
        $this->state()->setField($stateKey, $state)->save();
        // завершаем
        $result->setDataArray(['name' => $this->getMessage('RUN_ORDER_IMPORT_PROCESS', ['#OFFSET#' => $state['offset'], '#COUNT#' => $state['count']]), 'complete' => $state['complete'], 'percent' => $state['percent']]);
        return $result;
    }
    /**
     * Обработка конкретного заказа, обновление локальное
     * @param $arVkOrderItem
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function runOrdersImportActionUpdateItem($arVkOrderItem)
    {
        try {
            // обработка заказа
            $orderItem = new \VKapi\Market\Sale\Order\Item($this->syncItem());
            // подгружаем данные по заказа
            if ($arVkOrderItemExtend = $this->loadOrderByItem($arVkOrderItem)) {
                $orderItem->setVkOrder($arVkOrderItemExtend);
            } else {
                $orderItem->setVkOrder($arVkOrderItem);
            }
            if (!$orderItem->isExistOrder()) {
                $arItems = $this->loadVkOrderItems($arVkOrderItem);
                $orderItem->setVkOrderItems($arItems);
                $createdOrderId = $orderItem->createOrder();
                $this->log()->ok($this->getMessage('RUN_ORDER_IMPORT_ITEM_CREATED_ORDER', ['#VKORDER_ID#' => $arVkOrderItem['display_order_id'], '#GROUP_ID#' => $arVkOrderItem['group_id'], '#ORDER_ID#' => intval($createdOrderId)]));
            } else {
                $updatedOrderId = $orderItem->updateOrder();
                $this->log()->ok($this->getMessage('RUN_ORDER_IMPORT_ITEM_UPDATED_ORDER', ['#VKORDER_ID#' => $arVkOrderItem['display_order_id'], '#GROUP_ID#' => $arVkOrderItem['group_id'], '#ORDER_ID#' => intval($updatedOrderId)]));
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            if ($ex instanceof \VKapi\Market\Exception\ORMException) {
                $this->log()->error($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine(), $ex->getCustomData());
                return false;
            }
            $this->log()->error($ex->getMessage(), $ex->getCustomData());
            return false;
        }
        return true;
    }
    /**
     * Вренет массив товаров заказа из вк
     * @param $arVkOrderItem
     * @return array
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\ApiResponseException
     * @throws \VKapi\Market\Exception\BaseException
     * @throws \VKapi\Market\Exception\UnknownResponseException
     */
    public function loadVkOrderItems($arVkOrderItem)
    {
        $arReturn = [];
        $arQuery = ['access_token' => $this->syncItem()->getGroupAccessToken(), 'user_id' => $arVkOrderItem['user_id'], 'order_id' => $arVkOrderItem['id'], 'offset' => 0, 'count' => 100];
        while (true) {
            $result = $this->connection()->method('market.getOrderItems', $arQuery);
            $response = $result->getData('response');
            $state['count'] = $response['count'];
            if (!count($response['items'])) {
                break;
            }
            $arReturn = array_merge($arReturn, $response['items']);
            $arQuery['offset'] += count($response['items']);
            if ($arQuery['offset'] >= $response['count']) {
                break;
            }
        }
        return $arReturn;
    }
    /**
     * Загрузка полного объема данных по заказу на основе его краткого описания
     * @param $arVkOrderItem
     * @return mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\IO\FileNotFoundException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function loadOrderByItem($arVkOrderItem)
    {
        $arQuery = ['access_token' => $this->syncItem()->getGroupAccessToken(), 'order_id' => $arVkOrderItem['id'], 'user_id' => $arVkOrderItem['user_id'], 'extended' => 1];
        $result = $this->connection()->method('market.getOrderById', $arQuery);
        $response = $result->getData('response');
        if (isset($response['order'])) {
            return $response['order'];
        }
        return null;
    }
    public function sendOrderChangesToVK(\Bitrix\Sale\OrderBase $order, $arRef = null)
    {
        try {
            /**
             * @var $order \Bitrix\Sale\Order
             */
            if (is_null($arRef)) {
                $arRef = \VKapi\Market\Sale\Order\Sync\RefTable::getList(['filter' => ['ORDER_ID' => $order->getId()], 'limit' => 1])->fetch();
            }
            $arFields = ['access_token' => $this->syncItem()->getGroupAccessToken(), 'user_id' => $arRef['VKUSER_ID'], 'order_id' => $arRef['VKORDER_ID'], 'merchant_comment' => $order->getField('COMMENTS')];
            // основнйо статус заказа ---
            $mainStatusId = $this->syncItem()->getVkStatusByStatusId($order->getField('STATUS_ID'));
            if (!empty($mainStatusId)) {
                $arFields['status'] = $mainStatusId;
            }
            /**
             * @var $shipment \Bitrix\Sale\Shipment
             */
            // статус из статусов доставки ---
            $shipmentCollection = $order->getShipmentCollection();
            foreach ($shipmentCollection as $shipment) {
                if ($shipment->isSystem()) {
                    continue;
                }
                $statusId = $this->syncItem()->getVkStatusByStatusId($shipment->getField('STATUS_ID'));
                if (!empty($statusId) && $statusId > $mainStatusId) {
                    $arFields['status'] = $statusId;
                }
                break;
            }
            // если отменен заказ ---
            if ($order->isCanceled()) {
                $arFields['status'] = \VKapi\Market\Sale\Order\Item::VK_STATUS_CANCELED;
            } elseif ($order->getFields()->isChanged('CANCELED')) {
                // если нет никакого статуса, то ставим статус
                $arFields['status'] = \VKapi\Market\Sale\Order\Item::VK_STATUS_NEW;
            }
            $trackNumber = $order->getField('TRACKING_NUMBER');
            if ($trackNumber) {
                $arFields['track_number'] = $trackNumber;
            }
            if ($order->getDeliveryPrice() > 0) {
                $arFields['delivery_price'] = (int) (100 * $order->getDeliveryPrice());
            } else {
                $arFields['delivery_price'] = 0;
            }
            // удаляем, так как если передать стоимость доставки, то стоимость
            // заказа возрастает на сумму доставки
            // при том что дсотавка идет отдельным полем
            unset($arFields['delivery_price']);
            /**
             * @var $payment \Bitrix\Sale\Payment
             */
            $paymentCollection = $order->getPaymentCollection();
            $payment = $paymentCollection->getItemByIndex(0);
            if (!empty($payment)) {
                if ($payment->isReturn()) {
                    $arFields['payment_status'] = \VKapi\Market\Sale\Order\Item::PAYMENT_STATUS_RETURNED;
                } elseif ($payment->isPaid()) {
                    $arFields['payment_status'] = \VKapi\Market\Sale\Order\Item::PAYMENT_STATUS_PAID;
                } else {
                    $arFields['payment_status'] = \VKapi\Market\Sale\Order\Item::PAYMENT_STATUS_NOT_PAID;
                }
                if ($payment->isReturn() || $payment->isPaid()) {
                    $check = \Bitrix\Sale\Cashbox\CheckManager::getLastPrintableCheckInfo($payment);
                    if (!empty($check['LINK'])) {
                        $arFields['receipt_link'] = $check['LINK'];
                    }
                }
            } else {
                $arFields['payment_status'] = \VKapi\Market\Sale\Order\Item::PAYMENT_STATUS_NOT_PAID;
            }
            /**
             * @var $propValue \Bitrix\Sale\PropertyValue
             */
            $propertyCollection = $order->getPropertyCollection();
            if ($this->syncItem()->getCommentForUserPropertyId()) {
                $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getCommentForUserPropertyId());
                if ($propValue) {
                    $arFields['comment_for_user'] = $propValue->getValue();
                }
            }
            // wait api version 5.139
            unset($arFields['comment_for_user']);
            if ($this->syncItem()->getWidthPropertyId()) {
                $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWidthPropertyId());
                if ($propValue) {
                    $arFields['width'] = $propValue->getValue();
                }
            }
            if ($this->syncItem()->getHeightPropertyId()) {
                $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getHeightPropertyId());
                if ($propValue) {
                    $arFields['height'] = $propValue->getValue();
                }
            }
            if ($this->syncItem()->getLengthPropertyId()) {
                $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getLengthPropertyId());
                if ($propValue) {
                    $arFields['length'] = $propValue->getValue();
                }
            }
            if ($this->syncItem()->getWeightPropertyId()) {
                $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWeightPropertyId());
                if ($propValue) {
                    $arFields['weight'] = $propValue->getValue();
                }
            }
            $result = $this->connection()->method('market.editOrder', $arFields);
            $response = $result->getData('response');
            if ($response) {
                $this->log()->ok($this->getMessage('SEND_ORDER_CHANGES_TO_VK_OK', ['#VKORDER_ID#' => $arRef['VKORDER_ID'], '#GROUP_ID#' => $arRef['GROUP_ID'], '#ORDER_ID#' => (int) $order->getId()]));
            }
        } catch (\VKapi\Market\Exception\BaseException $ex) {
            if ($ex instanceof \VKapi\Market\Exception\ORMException) {
                $this->log()->error($ex->getMessage() . ' | ' . $ex->getFile() . ':' . $ex->getLine(), $ex->getCustomData());
                return;
            }
            $this->log()->error($ex->getMessage(), $ex->getCustomData());
        }
    }
    /**
     * Вернет ссылку на значение свойства по коду свойства
     * @param $propCollection
     * @param $propId
     * @return mixed|null
     */
    public function findPropValueByPropId($propCollection, $propId)
    {
        /**
         * @var \Bitrix\Sale\Internals\CollectableEntity[]
         * @var $item \Bitrix\Sale\Internals\CollectableEntity|\Bitrix\Sale\PropertyValue
         * 
         */
        foreach ($propCollection as $item) {
            if ($item->getPropertyId() > 0 && $propId == $item->getPropertyId()) {
                return $propCollection[$item->getInternalIndex()];
            }
        }
        return null;
    }
}
?>