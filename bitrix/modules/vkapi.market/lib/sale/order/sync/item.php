<?php

namespace VKapi\Market\Sale\Order\Sync;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Manager;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\ORMException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для удобной работы с парамтерами синхронизации, получение настроек и тп
 * @package VKapi\Market\Sale\Order\Sync
 */
class Item
{
    protected $syncId = 0;
    protected $arSync = null;
    /**
     * @param $sync - идентификатор синхронизации или массив описывающий ее
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function __construct($sync)
    {
        if (is_array($sync)) {
            $this->arSync = $sync;
            $this->syncId = $sync['ID'];
        } else {
            $this->loadSyncData((int) $sync);
        }
    }
    /**
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * Загружает синхронизацию по ID
     * @param $syncId
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    public function loadSyncData($syncId)
    {
        $this->syncId = intval($syncId);
        $ar = \VKapi\Market\Sale\Order\SyncTable::getById($syncId)->fetch();
        if (!$ar) {
            throw new \VKapi\Market\Exception\BaseException($this->manager()->getMessage('LIB.SALE.ORDER.SYNC.ITEM.SYNC_ID_NOT_FOUND', ['#ID#' => $syncId]), 'ERROR_SALE_ORDER_SYNC_ITEM_ID_NOT_FOUND');
        }
        $this->arSync = $ar;
    }
    public function isActive()
    {
        return (bool) $this->arSync['ACTIVE'];
    }
    /**
     * Вренет идентификтаор синхронизации
     * @return int
     */
    public function getId()
    {
        return (int) $this->syncId;
    }
    /**
     * Вернет идентификтаор аккаунта сохраненного локлаьно
     * @return int
     */
    public function getAccountId()
    {
        return (int) $this->arSync['ACCOUNT_ID'];
    }
    /**
     * Вернет идентфикатор группы
     * @return int
     */
    public function getGroupId()
    {
        return (int) $this->arSync['GROUP_ID'];
    }
    /**
     * ПРвоерка включен ли прием событий из вк
     * @return bool
     */
    public function isEventEnabled()
    {
        return (bool) $this->arSync['EVENT_ENABLED'];
    }
    /**
     * Вернет секретную строку которая приходит вместе с событием от вк
     * @return mixed
     */
    public function getEventSecret()
    {
        return $this->arSync['EVENT_SECRET'];
    }
    /**
     * Вренет код подтверждения для отдачи вк при доабвлении Callback API сервера
     * @return mixed
     */
    public function getEventCode()
    {
        return $this->arSync['EVENT_CODE'];
    }
    /**
     * Вернет включ доступа сообщества
     * @return string
     */
    public function getGroupAccessToken()
    {
        return $this->arSync['GROUP_ACCESS_TOKEN'];
    }
    /**
     * Вернет идентфииктаор сайта к которому нужно привязывать заказы
     * @return string
     */
    public function getSiteId()
    {
        return $this->arSync['SITE_ID'];
    }
    /**
     * Вернет идентфииктаор сайта к которому нужно привязывать заказы
     * @return int
     */
    public function getStartImportTimestamp()
    {
        return (int) ($this->arSync['PARAMS']['IMPORT_START_TIMESTAMP'] ?? 0);
    }
    /**
     * Вернет колчиество импортируемых последниех заказов, чтобы только новые заказы импортировать
     * @return int
     */
    public function getImportLastCount()
    {
        return (int) ($this->arSync['PARAMS']['IMPORT_LAST_COUNT'] ?? 0);
    }
    /**
     * Вернет идентифкатор типа плательщика
     * @return int
     */
    public function getPersonalTypeId()
    {
        return (int) $this->manager()->getParam('PERSONAL_TYPE', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор службы доставки по умолчанию. самовывоз
     * @return int
     */
    public function getDeliveryId()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор службы доставки - курьером
     * @return int
     */
    public function getDeliveryIdCourier()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_COURIER', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор службы доставки - почта
     * @return int
     */
    public function getDeliveryIdPochta()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_POCHTA', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор службы доставки - пункт самовывоза
     * @return int
     */
    public function getDeliveryIdPoint()
    {
        return (int) $this->manager()->getParam('DELIVERY_ID_POINT', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор споособа оплаты
     * @return int
     */
    public function getPaymentId()
    {
        return (int) $this->manager()->getParam('PAYMENT_ID', 0, $this->getSiteId());
    }
    /**
     * Вернет свойство заказа для сохранения фио покупателя
     * @return int
     */
    public function getFioPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_FIO', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор свойства заказа для хранения нмоера телефонва покупателя
     * @return int
     */
    public function getPhonePropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_PHONE', 0, $this->getSiteId());
    }
    /**
     * Вернет идентфикатор свойства заказа для сохранения адреса доставки
     * @return int
     */
    public function getAddressPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_ADDRESS', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения идентфикиатора заказа в вк
     * @return int
     */
    public function getVkOrderPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_VKORDER', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения идентфикиатора заказа в вк
     * @return int
     */
    public function getCommentForUserPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_COMMENT_FOR_USER', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения ширины
     * @return int
     */
    public function getWidthPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_WIDTH', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения высоты
     * @return int
     */
    public function getHeightPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_HEIGHT', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения длины
     * @return int
     */
    public function getLengthPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_LENGTH', 0, $this->getSiteId());
    }
    /**
     * Вернет идентификатор свойства заказа для хранения веса
     * @return int
     */
    public function getWeightPropertyId()
    {
        return (int) $this->manager()->getParam('SALE_PROPERTY_WEIGHT', 0, $this->getSiteId());
    }
    /**
     * Вернет код статуса локальный для конкретного статуса в ВК
     * @param $vkStatus
     * @return string
     */
    public function getStatusIdByVkStatus($vkStatus)
    {
        $vkStatus = (int) $vkStatus;
        switch ($vkStatus) {
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_NEW:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_AGREE:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_COLLECT:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_DELIVER:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_COMPLETED:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_CANCELED:
            case \VKapi\Market\Sale\Order\Item::VK_STATUS_RETURNED:
                return $this->manager()->getParam('STATUS_' . $vkStatus, '', $this->getSiteId());
        }
        return '';
    }
    /**
     * Вернет идентификатор статус в вк по локлаьному идентфикатору статуса
     * @param $orderStatusId
     * @return mixed|null
     */
    public function getVkStatusByStatusId($orderStatusId)
    {
        $arStatus = [];
        $arVkStatusList = [\VKapi\Market\Sale\Order\Item::VK_STATUS_NEW, \VKapi\Market\Sale\Order\Item::VK_STATUS_AGREE, \VKapi\Market\Sale\Order\Item::VK_STATUS_COLLECT, \VKapi\Market\Sale\Order\Item::VK_STATUS_DELIVER, \VKapi\Market\Sale\Order\Item::VK_STATUS_COMPLETED, \VKapi\Market\Sale\Order\Item::VK_STATUS_CANCELED, \VKapi\Market\Sale\Order\Item::VK_STATUS_RETURNED];
        foreach ($arVkStatusList as $vkStatusId) {
            $arStatus[$this->manager()->getParam('STATUS_' . $vkStatusId, '', $this->getSiteId())] = $vkStatusId;
        }
        if (!empty($arStatus[$orderStatusId])) {
            return $arStatus[$orderStatusId];
        }
        return null;
    }
}
?>