<?php

namespace VKapi\Market\Sale\Order;

use Bitrix\Main\Localization\Loc;
use VKapi\Market\Manager;
use VKapi\Market\Exception\BaseException;
use VKapi\Market\Exception\ORMException;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
/**
 * Класс для работы с заказо полученны от вк или передача изменнеий в вк
 */
class Item
{
    const PAYMENT_STATUS_NOT_PAID = 'not_paid';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_RETURNED = 'returned';
    public const VK_STATUS_NEW = 0;
    public const VK_STATUS_AGREE = 1;
    public const VK_STATUS_COLLECT = 2;
    public const VK_STATUS_DELIVER = 3;
    public const VK_STATUS_COMPLETED = 4;
    public const VK_STATUS_CANCELED = 5;
    public const VK_STATUS_RETURNED = 6;
    /**
     * @var string Исходный объект описывающйи заказ
     */
    private $vkOrderItem = null;
    /**
     * @var string Код заказа 11212-867
     */
    private $vkOrderDisplayId = '';
    /**
     * @var int Номер заказа
     */
    private $vkOrderId = 0;
    /**
     * @var int Группа в которой создаг заказ
     */
    private $vkOrderGroupId = 0;
    /**
     * @var int Польвзаотель сохдавший заказа
     */
    private $vkOrderUserId = 0;
    /**
     * @var int Дата создания, Unixtime
     */
    private $vkOrderDate = 0;
    /**
     * @var int Статус заказа 0-6
     */
    private $vkOrderStatus = 0;
    /**
     * @var string Статус оплаты - not_paid, paid, returned
     */
    private $vkOrderPaymentStatus = self::PAYMENT_STATUS_NOT_PAID;
    /**
     * Полная цена заказа
     * @var int
     */
    private $vkOrderTotalPrice = 0;
    /**
     * Скидка по промокоду
     * @var int
     */
    private $vkOrderPromocodeDiscount = 0;
    private $vkOrderPromocodeDiscountCurrency = 'RUB';
    /**
     * Стоимость доставки
     * @var int
     */
    private $vkOrderDeliveryPrice = 0;
    private $vkOrderDeliveryPriceCurrency = 'RUB';
    /**
     * Валюта
     * @var int
     */
    private $vkOrderCurrency = 'RUB';
    /**
     * @var string Комментарий покупателя
     */
    private $vkOrderComment = '';
    /**
     * @var string Комментарий продавца, внутренний
     */
    private $vkOrderMerchantComment = '';
    /**
     * @var string Комментарий для пользователя от продавца, с версии 5.139
     */
    private $vkOrderCommentForUser = '';
    /**
     * @var int Ширина
     */
    private $vkOrderWidth = 0;
    /**
     * @var int Длина
     */
    private $vkOrderLength = 0;
    /**
     * @var int Высота
     */
    private $vkOrderHeight = 0;
    /**
     * @var int Вес
     */
    private $vkOrderWeight = 0;
    /**
     * @var string Ссылка на электронный чек, с версии 5.159
     */
    private $vkOrderReceiptLink = '';
    /**
     * @var array Описание доставки
     */
    private $vkOrderDelivery = [];
    /**
     * @var string FIO покупателя
     */
    private $vkOrderBuyerName = '';
    /**
     * @var string Телефон покупателя
     */
    private $vkOrderBuyerPhone = '';
    /**
     * @var array -массив полученных товарво из ВК
     */
    private $vkOrderItems = [];
    /**
     * Настройки синхронизации
     * @var Sync\Item
     */
    private $oSyncItem = null;
    /**
     * @param $oSyncItem - настройки синхронизации
     */
    public function __construct(\VKapi\Market\Sale\Order\Sync\Item $oSyncItem)
    {
        $this->oSyncItem = $oSyncItem;
        if (!$this->manager()->isInstalledSaleModule()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('MODULE_SALE_IS_NOT_INSTALLED'), 'ERROR_MODULE_SALE_NOT_FOUND');
        }
        if (!$this->manager()->isInstalledCatalogModule()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('MODULE_CATALOG_IS_NOT_INSTALLED'), 'ERROR_MODULE_CATALOG_NOT_FOUND');
        }
        if (!$this->manager()->isInstalledIblockModule()) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('MODULE_IBLOCK_IS_NOT_INSTALLED'), 'ERROR_MODULE_IBLOCK_NOT_FOUND');
        }
    }
    /**
     * 
     * Верент ссылку на объект \CUser
     * @return \CUser
     */
    public function oldUser()
    {
        if (!isset($this->oOldUser)) {
            $this->oOldUser = new \CUser();
        }
        return $this->oOldUser;
    }
    /**
     * Вернет ссылку на Manager
     * @return \VKapi\Market\Manager
     */
    public function manager()
    {
        return \VKapi\Market\Manager::getInstance();
    }
    /**
     * @return \VKapi\Market\Sale\Order\Sync\Item
     */
    public function syncItem()
    {
        return $this->oSyncItem;
    }
    /**
     * Вернет ссылку объект для работы с таблицей связей локальных заказов и заказов в ВК
     * Fields: ID:int, ORDER_ID:int, VKORDER_ID:int, VKUSER_ID:int, GROUP_ID:int, SYNC_ID:int
     */
    public function syncRefTable()
    {
        if (!isset($this->oSyncRefTable)) {
            $this->oSyncRefTable = new \VKapi\Market\Sale\Order\Sync\RefTable();
        }
        return $this->oSyncRefTable;
    }
    /**
     * Вернет сообщение
     */
    public function getMessage($name, $arReplace = null)
    {
        return $this->manager()->getMessage('LIB.SALE.ORDER.ITEM.' . $name, $arReplace);
    }
    /**
     * Передаем описание заказа полцченое от вк, дял последующей обработки
     * @param $arItem
     */
    public function setVkOrder(array $arItem)
    {
        $this->vkOrderItem = $arItem;
        $this->vkOrderId = (int) $arItem['id'];
        $this->vkOrderGroupId = (int) $arItem['group_id'];
        $this->vkOrderUserId = (int) $arItem['user_id'];
        $this->vkOrderDisplayId = (string) $arItem['display_order_id'];
        if (empty($this->vkOrderDisplayId)) {
            $this->vkOrderDisplayId = sprintf('%s-%s', $this->vkOrderUserId, $this->vkOrderId);
        }
        $this->vkOrderDate = (int) $arItem['date'];
        $this->vkOrderDeliveryPrice = (int) $arItem['delivery_price'];
        $this->vkOrderStatus = (int) $arItem['status'];
        $this->vkOrderPaymentStatus = (string) $arItem['payment']['payment_status'];
        $this->vkOrderCurrency = $arItem['total_price']['currency']['name'];
        $this->vkOrderTotalPrice = (int) $arItem['total_price']['amount'] / 100;
        $this->vkOrderComment = (string) $arItem['comment'];
        $this->vkOrderMerchantComment = (string) $arItem['merchant_comment'];
        $this->vkOrderCommentForUser = (string) ($arItem['comment_for_user'] ?: '');
        $this->vkOrderWidth = (int) $arItem['dimensions']['width'];
        $this->vkOrderLength = (int) $arItem['dimensions']['length'];
        $this->vkOrderHeight = (int) $arItem['dimensions']['height'];
        $this->vkOrderWeight = (int) $arItem['weight'];
        $this->vkOrderReceiptLink = (string) ($arItem['receipt_link'] ?: '');
        $this->vkOrderDelivery = $arItem['delivery'];
        $this->vkOrderBuyerName = (string) $arItem['recipient']['name'];
        $this->vkOrderBuyerPhone = (string) $arItem['recipient']['phone'];
        $this->vkOrderPromocodeDiscount = 0;
        $this->vkOrderDeliveryPrice = 0;
        if (!isset($arItem['price_details'])) {
            return $this;
        }
        $arPriceDetails = array_combine(array_column($arItem['price_details'], 'title'), $arItem['price_details']);
        if (isset($arPriceDetails[$this->getMessage('DELIVERY_COST')])) {
            $this->vkOrderDeliveryPrice = abs((int) $arPriceDetails[$this->getMessage('DELIVERY_COST')]['price']['amount'] ?? 0);
            $this->vkOrderDeliveryPriceCurrency = (string) $arPriceDetails[$this->getMessage('DELIVERY_COST')]['price']['currency']['name'] ?? 'RUB';
            if ($this->vkOrderDeliveryPrice > 0) {
                $this->vkOrderDeliveryPrice = $this->vkOrderDeliveryPrice / 100;
            }
        }
        if (isset($arPriceDetails[$this->getMessage('PROMOCODE_COST')])) {
            $this->vkOrderPromocodeDiscount = abs((int) $arPriceDetails[$this->getMessage('PROMOCODE_COST')]['price']['amount'] ?? 0);
            $this->vkOrderPromocodeDiscountCurrency = (string) $arPriceDetails[$this->getMessage('PROMOCODE_COST')]['price']['currency']['name'] ?? 'RUB';
            if ($this->vkOrderPromocodeDiscount > 0) {
                $this->vkOrderPromocodeDiscount = $this->vkOrderPromocodeDiscount / 100;
            }
        }
        return $this;
    }
    /**
     * Передаем товары заказа полученные от вк
     * @param array $arVkOrderItems
     */
    public function setVkOrderItems(array $arVkOrderItems)
    {
        $this->vkOrderItems = $arVkOrderItems;
    }
    /**
     * @return array
     */
    public function getVkOrderItems()
    {
        return $this->vkOrderItems;
    }
    /**
     * Проверяем существует ли заказа локально
     * @return false
     */
    public function isExistOrder()
    {
        if (!empty($this->getOrderId())) {
            return true;
        }
        return false;
    }
    /**
     * Поиск ID локального заказа добавленного ранеее
     * @return mixed|null
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function getOrderId()
    {
        // проверяем наличие заказа в таблице синхронизаций
        $dbr = $this->syncRefTable()->getList(['filter' => ['VKORDER_ID' => $this->vkOrderId, 'VKUSER_ID' => $this->vkOrderUserId], 'limit' => 1]);
        if ($ar = $dbr->fetch()) {
            return $ar['ORDER_ID'];
        }
        return null;
    }
    /**
     * Сохраняет привязку локального заказа к заказу в ВК
     * @param $orderId
     * @throws \VKapi\Market\Exception\BaseException
     * @throws \VKapi\Market\Exception\ORMException
     */
    public function saveRef($orderId)
    {
        // проверяем наличие заказа в таблице синхронизаций
        $ar = $this->syncRefTable()->getList(['filter' => ['VKORDER_ID' => $this->vkOrderId, 'VKUSER_ID' => $this->vkOrderUserId], 'limit' => 1])->fetch();
        if ($ar) {
            // если сущесвтует
            if ($ar['ORDER_ID'] != $orderId) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_ORDER_ID_IS_DIFFERENT', ['#OLD_ID#' => $ar['ORDER_ID'], '#ID#' => $orderId]));
            }
        } else {
            $arFields = ['VKORDER_ID' => $this->vkOrderId, 'VKUSER_ID' => $this->vkOrderUserId, 'ORDER_ID' => $orderId, 'GROUP_ID' => $this->vkOrderGroupId, 'SYNC_ID' => $this->syncItem()->getId()];
            $result = $this->syncRefTable()->add($arFields);
            if (!$result->isSuccess()) {
                throw new \VKapi\Market\Exception\ORMException($result);
            }
        }
    }
    /**
     * Создание заказа, вызывается после передачи всех данных полученных от вк
     */
    public function createOrder()
    {
        $registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
        $userId = $this->findOrCreateUserId();
        // для подсстраховки
        $this->createSaleFUser($userId);
        /**
         * @var $basket \Bitrix\Sale\Basket
         */
        $basketClassName = $registry->getBasketClassName();
        $basket = $basketClassName::create($this->syncItem()->getSiteId());
        if (count($this->vkOrderItems)) {
            // считаем стоимость всех товаров в корзине вк
            $vkBasketItemsCost = 0;
            foreach ($this->vkOrderItems as $vkOrderbasetItem) {
                $vkBasketItemsCost += (int) $vkOrderbasetItem['price']['amount'] / 100 * $vkOrderbasetItem["quantity"];
            }
            // коэффициент для равномерного распределения скидки
            $promocodeDiscountKoef = $this->vkOrderPromocodeDiscount / $vkBasketItemsCost;
            // для применения к последнему товару остатка скидки
            $promocodeDiscount = $this->vkOrderPromocodeDiscount;
            $lastIndex = count($this->vkOrderItems) - 1;
            foreach ($this->vkOrderItems as $vkOrderBasketItemIndex => $vkOrderBasketItem) {
                // определяем идентфиикатор товара каталога
                $arProduct = $this->getProductByVkOrderItem($vkOrderBasketItem);
                if (empty($arProduct)) {
                    throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_VKITEM_NOT_FOUND', ['#NAME#' => $vkOrderBasketItem['title'], '#ID#' => $vkOrderBasketItem['item_id'], '#VKORDER_ID#' => $this->vkOrderDisplayId, '#GROUP_ID#' => $this->vkOrderGroupId]), 'ERROR_VK_ORDER_ITEM_NOT_FOUND', $vkOrderBasketItem);
                }
                $productFields = ["PRODUCT_ID" => $arProduct['ID'], "BASE_PRICE" => $arProduct['PRICE'], "CURRENCY" => $arProduct['CURRENCY'], "QUANTITY" => $arProduct['QUANTITY'], "LID" => $this->syncItem()->getSiteId(), "DELAY" => "N", "CAN_BUY" => "Y", "NAME" => $arProduct['NAME'], 'MODULE' => 'catalog', 'PRODUCT_PROVIDER_CLASS' => \Bitrix\Catalog\Product\Basket::getDefaultProviderName()];
                $r = \Bitrix\Catalog\Product\Basket::addProductToBasket($basket, $productFields, ['USER_ID' => $userId, 'SITE_ID' => $this->syncItem()->getSiteId()]);
                if ($r->isSuccess()) {
                    $resultData = $r->getData();
                    if (isset($resultData['BASKET_ITEM'])) {
                        /**
 * @var \Bitrix\Sale\BasketItem $basketItem
 */
                        $basketItem = $resultData['BASKET_ITEM'];
                    } else {
                        throw new \VKapi\Market\Exception\BaseException('ERROR_ADD_BASKET_ITEM_TO_ORDER', 'ERROR_ADD_BASKET_ITEM_TO_ORDER');
                    }
                } else {
                    throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_ADD_PRODUCT_TO_BASKET', ['#PRODUCT_ID#' => $arProduct['ID'], '#MESSAGE#' => $r->getErrorCollection()->current()->getMessage()]), 'ERROR_ADD_BASKET_ITEM_TO_ORDER');
                }
                if (!$this->vkOrderPromocodeDiscount) {
                    $basketItem->setPrice($arProduct['PRICE'], true);
                } else {
                    if ($lastIndex > $vkOrderBasketItemIndex) {
                        $value = floor($arProduct['PRICE'] * $promocodeDiscountKoef);
                        $basketItem->setPrice($arProduct['PRICE'] - $value, true);
                        $promocodeDiscount -= $value * $arProduct['QUANTITY'];
                    } else {
                        $basketItem->setPrice($arProduct['PRICE'] - $promocodeDiscount / $arProduct['QUANTITY'], true);
                    }
                }
            }
        }
        /**
         * @var $order \Bitrix\Sale\Order
         */
        $orderClassName = $registry->getOrderClassName();
        $order = $orderClassName::create($this->syncItem()->getSiteId(), $userId);
        $order->setPersonTypeId($this->syncItem()->getPersonalTypeId());
        $order->setBasket($basket);
        /**
         * @var $shipment \Bitrix\Sale\Shipment
         */
        $shipmentCollection = $order->getShipmentCollection();
        $deliveryType = mb_strtolower($this->vkOrderDelivery["type"]);
        if (strpos($deliveryType, mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT'))) !== false) {
            $deliveryType = mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT'));
        }
        // тип доставки
        $shipment = null;
        switch ($deliveryType) {
            case mb_strtolower($this->getMessage('DELIVERY_TYPE_COURIER')):
                // 'address' => 'Россия, Москва, Улица, дом: улица, Подъезд: подъезд, Код домофона: код домофона, Этаж: этаж, Квартира/офис: квартира',
                // 'type' => 'Курьерская доставка',
                if ($this->syncItem()->getDeliveryIdCourier()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdCourier()));
                } elseif ($this->syncItem()->getDeliveryId()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                }
                break;
            case mb_strtolower($this->getMessage('DELIVERY_TYPE_POCHTA')):
                // 'address' => '143921, Россия, Москва, Балашиза, дю Черное, Агрогородов, 1, 112',
                // 'type' => 'В ближайшее почтовое отделение',
                if ($this->syncItem()->getDeliveryIdPochta()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdPochta()));
                } elseif ($this->syncItem()->getDeliveryId()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                }
                break;
            case mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT')):
                // [
                // 'address' => 'Суздальская ул, д.26, корпус 2, Москва, +7 (499) 391-56-22',
                // 'type' => 'Доставка в пункт выдачи Boxberry',
                // 'delivery_point' =>
                // [
                // 'id' => 1870,
                // 'external_id' => '10.039',
                // 'outpost_only' => false,
                // 'cash_only' => false,
                // 'address' =>
                // [
                // 'id' => 59208,
                // 'additional_address' => 'Метро Новокосино.
                // 17-тиэтажный дом.
                // Этаж - 1
                // Первый жилой дом от метро, вход со стороный улицы.',
                // 'address' => 'Суздальская ул, д.26, корпус 2',
                // 'city_id' => 1,
                // 'country_id' => 1,
                // 'latitude' => 55.743365,
                // 'longitude' => 37.861474,
                // 'metro_station_id' => 37,
                // 'phone' => '+7 (499) 391-56-22',
                // 'timetable' =>
                // [
                // 'fri' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'mon' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'sat' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'sun' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'thu' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'tue' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // 'wed' =>
                // [
                // 'close_time' => 1200,
                // 'open_time' => 600,
                // 'break_close_time' => 0,
                // 'break_open_time' => 0,
                // ],
                // ],
                // 'title' => 'Москва Суздальская_7739_С',
                // 'work_info_status' => 'timetable',
                // ],
                // 'display_title' => 'Boxberry, Выхино',
                // 'service_id' => 2,
                // ],
                // ]
                if ($this->syncItem()->getDeliveryIdPoint()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdPoint()));
                } elseif ($this->syncItem()->getDeliveryId()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                }
                break;
            case mb_strtolower($this->getMessage('DELIVERY_TYPE_PICKUP')):
            default:
                // 'address' => '',
                // 'type' => 'Самовывоз',
                if ($this->syncItem()->getDeliveryId()) {
                    $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                }
        }
        if (!is_null($shipment)) {
            $shipment->setBasePriceDelivery($this->vkOrderDeliveryPrice, true);
            $shipment->setField('PRICE_DELIVERY', $this->vkOrderDeliveryPrice);
            if (!empty($this->vkOrderDelivery['track_number'])) {
                $shipment->setField('TRACKING_NUMBER', $this->vkOrderDelivery['track_number']);
            }
            /**
 * @var \Bitrix\Sale\BasketItem $basketItem
 */
            $shipmentItemCollection = $shipment->getShipmentItemCollection();
            foreach ($basket as $basketItem) {
                $item = $shipmentItemCollection->createItem($basketItem);
                $item->setQuantity($basketItem->getQuantity());
            }
        }
        // оплата -----
        $paymentCollection = $order->getPaymentCollection();
        if ($this->syncItem()->getPaymentId()) {
            $payment = $paymentCollection->createItem(\Bitrix\Sale\PaySystem\Manager::getObjectById($this->syncItem()->getPaymentId()));
            $payment->setField("CURRENCY", $this->vkOrderCurrency);
            $payment->setField("SUM", $order->getPrice());
            if ($this->vkOrderPaymentStatus == self::PAYMENT_STATUS_PAID) {
                $payment->setPaid('Y');
                $payment->setField('COMMENTS', $this->getMessage('SET_PAID_Y', ['#DATE#' => date('d.m.Y H:i:s')]));
            }
        }
        /**
         * @var $propValue \Bitrix\Sale\PropertyValue
         */
        $propertyCollection = $order->getPropertyCollection();
        if ($this->syncItem()->getFioPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getFioPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderBuyerName);
            }
        }
        if ($this->syncItem()->getPhonePropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getPhonePropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderBuyerPhone);
            }
        }
        if ($this->syncItem()->getVkOrderPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getVkOrderPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderUserId . '-' . $this->vkOrderId);
            }
        }
        if ($this->syncItem()->getAddressPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getAddressPropertyId());
            if ($propValue) {
                $propValue->setValue((string) $this->vkOrderDelivery['address']);
            }
        }
        if ($this->syncItem()->getCommentForUserPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getCommentForUserPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderCommentForUser);
            }
        }
        if ($this->syncItem()->getWidthPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWidthPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderWidth);
            }
        }
        if ($this->syncItem()->getHeightPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getHeightPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderHeight);
            }
        }
        if ($this->syncItem()->getLengthPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getLengthPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderLength);
            }
        }
        if ($this->syncItem()->getWeightPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWeightPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderWeight);
            }
        }
        $order->setField('DATE_INSERT', \Bitrix\Main\Type\DateTime::createFromTimestamp($this->vkOrderDate));
        $order->setField('USER_DESCRIPTION', $this->vkOrderComment);
        $order->setField('COMMENTS', $this->vkOrderMerchantComment);
        if ($this->vkOrderPromocodeDiscount) {
            $order->setField('COMMENTS', $this->getMessage('PROMOCODE_COST_COMMENT', ['#COMMENT#' => $this->vkOrderMerchantComment, '#DISCOUNT#' => $this->vkOrderPromocodeDiscount, '#CURRENCY#' => $this->vkOrderPromocodeDiscountCurrency]));
        }
        $statusId = $this->syncItem()->getStatusIdByVkStatus($this->vkOrderStatus);
        if (!empty($statusId)) {
            $resultSetStatus = $order->setField('STATUS_ID', $statusId);
            if (!$resultSetStatus->isSuccess()) {
                throw new \VKapi\Market\Exception\BaseException($resultSetStatus->getErrorCollection()->current()->getMessage(), 'ERROR_SALE_ORDER_ITEM_SET_STATUS');
            }
        }
        // событие
        $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_ORDER_CREATE, ['order' => $order, 'item' => $this]);
        $result = $order->save();
        if (!$result->isSuccess()) {
            throw new \VKapi\Market\Exception\ORMException($result);
        }
        $this->saveRef($result->getId());
        // событие
        $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_AFTER_ORDER_CREATE, ['order' => $order, 'item' => $this]);
        return $result->getId();
    }
    /**
     * Обновление заказа, вызывается после передачи основного массива заказа полученного от ВК
     */
    public function updateOrder()
    {
        $orderId = (int) $this->getOrderId();
        $registry = \Bitrix\Sale\Registry::getInstance(\Bitrix\Sale\Registry::REGISTRY_TYPE_ORDER);
        /**
         * @var $orderClassName \Bitrix\Sale\Order
         * @var $order \Bitrix\Sale\Order
         */
        $orderClassName = $registry->getOrderClassName();
        $order = $orderClassName::load($orderId);
        if (empty($order)) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_ORDER_NOT_FOUND', ['#ORDER_ID#' => $orderId, '#VKORDER_ID#' => $this->vkOrderDisplayId, '#GROUP_ID#' => $this->vkOrderGroupId]), 'ERROR_ORDER_NOT_FOUND');
        }
        /**
         * @var $payment \Bitrix\Sale\Payment
         */
        $paymentCollection = $order->getPaymentCollection();
        $payment = $paymentCollection->getItemByIndex(0);
        if (!is_null($payment)) {
            if ($this->vkOrderPaymentStatus == self::PAYMENT_STATUS_PAID && !$payment->isPaid()) {
                $payment->setField('COMMENTS', $this->getMessage('SET_PAID_Y', ['#DATE#' => date('d.m.Y H:i:s')]));
                $resPayment = $payment->setPaid('Y');
            } elseif ($this->vkOrderPaymentStatus != self::PAYMENT_STATUS_PAID && $payment->isPaid()) {
                $payment->setField('COMMENTS', $this->getMessage('SET_PAID_N', ['#DATE#' => date('d.m.Y H:i:s')]));
                $resPayment = $payment->setPaid('N');
            }
            if ($payment->getSum() <= 0 && $payment->getSumPaid() <= 0) {
                $payment->setField("SUM", $order->getPrice());
            }
            if ($resPayment instanceof \Bitrix\Sale\Result && !$resPayment->isSuccess()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_SALE_ORDER_ITEM_SET_PAYMENT_STATUS', ['#ORDER_ID#' => $orderId, '#VKORDER_ID#' => $this->vkOrderDisplayId, '#GROUP_ID#' => $this->vkOrderGroupId, '#MSG#' => $resPayment->getErrorCollection()->current()->getMessage()]), 'ERROR_SALE_ORDER_ITEM_SET_PAYMENT_STATUS');
            }
        }
        $order->setField('COMMENTS', $this->vkOrderMerchantComment);
        /**
         * @var $propValue \Bitrix\Sale\PropertyValue
         */
        $propertyCollection = $order->getPropertyCollection();
        if ($this->syncItem()->getCommentForUserPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getCommentForUserPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderCommentForUser);
            }
        }
        if ($this->syncItem()->getWidthPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWidthPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderWidth);
            }
        }
        if ($this->syncItem()->getHeightPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getHeightPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderHeight);
            }
        }
        if ($this->syncItem()->getLengthPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getLengthPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderLength);
            }
        }
        if ($this->syncItem()->getWeightPropertyId()) {
            $propValue = $this->findPropValueByPropId($propertyCollection, $this->syncItem()->getWeightPropertyId());
            if ($propValue) {
                $propValue->setValue($this->vkOrderWeight);
            }
        }
        $order->setField('USER_DESCRIPTION', $this->vkOrderComment);
        $order->setField('COMMENTS', $this->vkOrderMerchantComment);
        $statusId = $this->syncItem()->getStatusIdByVkStatus($this->vkOrderStatus);
        if (!empty($statusId) && $order->getField('STATUS_ID') != $statusId) {
            $resultSetStatus = $order->setField('STATUS_ID', $statusId);
            if (!$resultSetStatus->isSuccess()) {
                throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_SALE_ORDER_ITEM_SET_STATUS', ['#ORDER_ID#' => $orderId, '#VKORDER_ID#' => $this->vkOrderDisplayId, '#GROUP_ID#' => $this->vkOrderGroupId, '#MSG#' => $resPayment->getErrorCollection()->current()->getMessage()]), 'ERROR_SALE_ORDER_ITEM_SET_STATUS');
            }
        }
        /**
         * @var $shipmentItem \Bitrix\Sale\Shipment
         */
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = null;
        foreach ($shipmentCollection as $shipmentItem) {
            if ($shipmentItem->isSystem()) {
                continue;
            }
            $shipment = $shipmentItem;
            break;
        }
        if (is_null($shipment)) {
            $shipment = null;
            $deliveryType = mb_strtolower($this->vkOrderDelivery["type"]);
            if (strpos($deliveryType, mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT'))) !== false) {
                $deliveryType = mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT'));
            }
            switch ($deliveryType) {
                case mb_strtolower($this->getMessage('DELIVERY_TYPE_COURIER')):
                    // 'address' => 'Россия, Москва, Улица, дом: улица, Подъезд: подъезд, Код домофона: код домофона, Этаж: этаж, Квартира/офис: квартира',
                    // 'type' => 'Курьерская доставка',
                    if ($this->syncItem()->getDeliveryIdCourier()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdCourier()));
                    } elseif ($this->syncItem()->getDeliveryId()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                    }
                    break;
                case mb_strtolower($this->getMessage('DELIVERY_TYPE_POCHTA')):
                    // 'address' => '143921, Россия, Москва, Балашиза, дю Черное, Агрогородов, 1, 112',
                    // 'type' => 'В ближайшее почтовое отделение',
                    if ($this->syncItem()->getDeliveryIdPochta()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdPochta()));
                    } elseif ($this->syncItem()->getDeliveryId()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                    }
                    break;
                case mb_strtolower($this->getMessage('DELIVERY_TYPE_POINT')):
                    // [
                    // 'address' => 'Суздальская ул, д.26, корпус 2, Москва, +7 (499) 391-56-22',
                    // 'type' => 'Доставка в пункт выдачи Boxberry',
                    // 'delivery_point' =>
                    // [
                    // 'id' => 1870,
                    // 'external_id' => '10.039',
                    // 'outpost_only' => false,
                    // 'cash_only' => false,
                    // 'address' =>
                    // [
                    // 'id' => 59208,
                    // 'additional_address' => 'Метро Новокосино.
                    // 17-тиэтажный дом.
                    // Этаж - 1
                    // Первый жилой дом от метро, вход со стороный улицы.',
                    // 'address' => 'Суздальская ул, д.26, корпус 2',
                    // 'city_id' => 1,
                    // 'country_id' => 1,
                    // 'latitude' => 55.743365,
                    // 'longitude' => 37.861474,
                    // 'metro_station_id' => 37,
                    // 'phone' => '+7 (499) 391-56-22',
                    // 'timetable' =>
                    // [
                    // 'fri' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'mon' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'sat' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'sun' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'thu' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'tue' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // 'wed' =>
                    // [
                    // 'close_time' => 1200,
                    // 'open_time' => 600,
                    // 'break_close_time' => 0,
                    // 'break_open_time' => 0,
                    // ],
                    // ],
                    // 'title' => 'Москва Суздальская_7739_С',
                    // 'work_info_status' => 'timetable',
                    // ],
                    // 'display_title' => 'Boxberry, Выхино',
                    // 'service_id' => 2,
                    // ],
                    // ]
                    if ($this->syncItem()->getDeliveryIdPoint()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryIdPoint()));
                    } elseif ($this->syncItem()->getDeliveryId()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                    }
                    break;
                case mb_strtolower($this->getMessage('DELIVERY_TYPE_PICKUP')):
                default:
                    // 'address' => '',
                    // 'type' => 'Самовывоз',
                    if ($this->syncItem()->getDeliveryId()) {
                        $shipment = $shipmentCollection->createItem(\Bitrix\Sale\Delivery\Services\Manager::getObjectById($this->syncItem()->getDeliveryId()));
                    }
            }
        }
        if (!is_null($shipment) && !empty($this->vkOrderDelivery['track_number']) && $this->vkOrderDelivery['track_number'] != $shipment->getField('TRACKING_NUMBER')) {
            $shipment->setField('TRACKING_NUMBER', $this->vkOrderDelivery['track_number']);
        }
        // событие
        $this->manager()->sendEvent(\VKapi\Market\Manager::EVENT_ON_BEFORE_ORDER_UPDATE, ['order' => $order, 'item' => $this]);
        $result = $order->save();
        if (!$result->isSuccess()) {
            throw new \VKapi\Market\Exception\ORMException($result);
        }
        return $result->getId();
    }
    /**
     * Подготовка номера телефона, убирает все симфолы кроме цифр
     * @param $phone
     * @return array|string|string[]|null
     */
    protected function preparePhone($phone)
    {
        $phone = preg_replace('/[^\\d]+/', '', $phone);
        $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phone);
        // подготовка украинского номера
        if (!$phoneNumber->isValid() || is_null($phoneNumber->getCountry())) {
            $phoneTmp = preg_replace('/^0([0-9]{9})$/', '380\\1', $phone);
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phoneTmp);
        }
        // замена 8 на 7 в росийских номерах
        if (!$phoneNumber->isValid() || is_null($phoneNumber->getCountry())) {
            $phoneTmp = preg_replace('/^8([0-9]{10})$/', '7\\1', $phone);
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phoneTmp);
        }
        // получаем первый вариант номера подготовленного
        $phone = preg_replace('/^\\+/', '', $phoneNumber->format(\Bitrix\Main\PhoneNumber\Format::E164));
        // если не проходит валидацию
        // подстановим 7 в начало номера
        if (!$phoneNumber->isValid() && strlen($phone) == 10) {
            $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+7' . $phone);
            if ($phoneNumber->isValid()) {
                $phone = preg_replace('/^\\+/', '', $phoneNumber->format(\Bitrix\Main\PhoneNumber\Format::E164));
            }
        }
        return $phone;
    }
    /**
     * Проверка валидности номера телефона для поиск польвзаотеля или создания нового
     * @param $phone
     * @return bool
     */
    protected function isValidPhone($phone)
    {
        $phone = $this->preparePhone($phone);
        $phoneNumber = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse('+' . $phone);
        if ($phoneNumber->isValid()) {
            return true;
        }
        return false;
    }
    /**
     * Поиск пользователя по номеру телефона
     * @param $phone
     * @return array|false
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    protected function findUserByPhone($phone)
    {
        $phone = $this->preparePhone($this->vkOrderBuyerPhone);
        if (strlen(trim($phone)) <= 0) {
            return false;
        }
        $arFilter = ['PHONE_NUMBER' => '+' . $phone];
        $dbrUser = \Bitrix\Main\UserTable::getList(['limit' => 1, 'order' => ['ID' => 'ASC'], 'filter' => $arFilter, 'select' => ['ID', 'PHONE_NUMBER' => 'PHONE_AUTH.PHONE_NUMBER', 'ACTIVE']]);
        $arUser = $dbrUser->fetch();
        return $arUser;
    }
    /**
     * Проверка является ли номер теелефона обязательным при регистрации
     * @return bool|mixed
     */
    public function isRequiredUserPhone()
    {
        static $flag;
        if (!isset($flag)) {
            $flag = \Bitrix\Main\Config\Option::get('main', 'new_user_phone_required', 'N') == 'Y';
        }
        return $flag;
    }
    /**
     * Найдем пользваотеля по номеру телеофна, если нет, то создадим и вернем идентификатор
     * @return int|mixed|string
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     * @throws \VKapi\Market\Exception\BaseException
     */
    protected function findOrCreateUserId()
    {
        $arAddFields = [];
        $phone = $this->preparePhone($this->vkOrderBuyerPhone);
        if ($this->isValidPhone($phone)) {
            // ищем пользователя
            $arUser = $this->findUserByPhone($phone);
            if ($arUser) {
                return $arUser['ID'];
            }
            $arAddFields['PHONE_NUMBER'] = '+' . $phone;
        } elseif ($this->isRequiredUserPhone()) {
            // телефон по умолчанию если передан не валидный номер телефона
            $arAddFields['PHONE_NUMBER'] = '+79999999999';
        }
        // ищем по XML_ID-------------------------------
        $dbrUser = \Bitrix\Main\UserTable::getList(['limit' => 1, 'filter' => ["XML_ID" => 'vkapi_market_user_' . $this->vkOrderUserId], 'select' => ['ID']]);
        if ($arUser = $dbrUser->fetch()) {
            return $arUser['ID'];
        }
        // не найшли, создаем -----------------------
        // фио ---
        $arParts = explode(' ', $this->vkOrderBuyerName);
        $arParts = array_map('trim', $arParts);
        $arParts = array_values(array_diff($arParts, ['']));
        if (count($arParts) > 2) {
            $arAddFields['NAME'] = $arParts[0];
            $arAddFields['LAST_NAME'] = $arParts[1];
            $arAddFields['SECOND_NAME'] = $arParts[2];
        } elseif (count($arParts) > 1) {
            $arAddFields['NAME'] = $arParts[0];
            $arAddFields['LAST_NAME'] = $arParts[1];
        } elseif (count($arParts) > 0) {
            $arAddFields['NAME'] = $arParts[0];
        }
        $userId = $this->createUser($arAddFields);
        return $userId;
    }
    /**
     * Добавление пользователя
     * @param $arFields
     * @return int|mixed|string
     * @throws \VKapi\Market\Exception\BaseException
     */
    protected function createUser($arFields)
    {
        global $APPLICATION, $DB, $USER_FIELD_MANAGER;
        $oUserTypeEntity = new \CUserTypeEntity();
        $oUserFieldEnum = new \CUserFieldEnum();
        $APPLICATION->ResetException();
        $randomEmail = \Bitrix\Main\Security\Random::getString(10) . '@local.loc';
        $checkword = md5(uniqid() . \CMain::GetServerUniqID());
        $arFields = array_merge(["CHECKWORD" => \Bitrix\Main\Security\Password::hash($checkword), "~CHECKWORD_TIME" => $DB->CurrentTimeFunction(), "EMAIL" => $randomEmail, "ACTIVE" => "Y", "NAME" => "", "XML_ID" => 'vkapi_market_user_' . (int) $this->vkOrderUserId, "LAST_NAME" => "", "SITE_ID" => $this->syncItem()->getSiteId(), "LANGUAGE_ID" => LANGUAGE_ID], $arFields);
        // группы пользователя
        if (!isset($arFields["GROUP_ID"])) {
            $def_group = \COption::GetOptionString("main", "new_user_registration_def_group", "");
            if ($def_group != "") {
                $arFields["GROUP_ID"] = explode(",", $def_group);
            } else {
                $arFields["GROUP_ID"] = [];
            }
        }
        $arFields["PASSWORD"] = $arFields["CONFIRM_PASSWORD"] = \CUser::GeneratePasswordByPolicy($arFields["GROUP_ID"]);
        $arFields["LID"] = $arFields["SITE_ID"];
        $arFields["CHECKWORD"] = $checkword;
        // собирем обязательные поля для заполнения и подставим пустые значения или значения поумолчанию, чтобы не вознимкало сложностей
        $dbrUserTypeEntity = $oUserTypeEntity->GetList([], ['ENTITY_ID' => 'USER', 'MANDATORY' => 'Y']);
        while ($arUserTypeEntity = $dbrUserTypeEntity->Fetch()) {
            if (array_key_exists($arUserTypeEntity['FIELD_NAME'], $arFields)) {
                continue;
            }
            switch ($arUserTypeEntity['USER_TYPE_ID']) {
                case 'string':
                    if ($arUserTypeEntity['MIN_LENGTH']) {
                        $arFields[$arUserTypeEntity['FIELD_NAME']] = str_repeat('0', $arUserTypeEntity['MIN_LENGTH']);
                    }
                    break;
                case 'integer':
                case 'double':
                case 'file':
                    $arFields[$arUserTypeEntity['FIELD_NAME']] = 0;
                    break;
                case 'datetime':
                    $arFields[$arUserTypeEntity['FIELD_NAME']] = date('d.m.Y H:i:s');
                    break;
                case 'date':
                    $arFields[$arUserTypeEntity['FIELD_NAME']] = date('d.m.Y');
                    break;
                case 'boolean':
                    $arFields[$arUserTypeEntity['FIELD_NAME']] = false;
                    break;
                case 'enumeration':
                    $dbrEnumValues = $oUserFieldEnum->GetList([], ['USER_FIELD_ID' => $arUserTypeEntity['ID'], 'DEF' => 'Y']);
                    if ($arEnumValue = $dbrEnumValues->Fetch()) {
                        $arFields[$arUserTypeEntity['FIELD_NAME']] = $arEnumValue['ID'];
                        break;
                    }
                    $dbrEnumValues = $oUserFieldEnum->GetList(['SORT' => 'ASC'], ['USER_FIELD_ID' => $arUserTypeEntity['ID']]);
                    if ($arEnumValue = $dbrEnumValues->Fetch()) {
                        $arFields[$arUserTypeEntity['FIELD_NAME']] = $arEnumValue['ID'];
                    }
                    break;
                case 'video':
                case 'hlblock':
                case 'iblock_section':
                case 'iblock_element':
                case 'string_formatted':
                    break;
            }
        }
        foreach (GetModuleEvents("vkapi.market", "OnBeforeUserAdd", true) as $arEvent) {
            if (ExecuteModuleEventEx($arEvent, [&$arFields]) === false) {
                // wait true or \VKapi\Market\Exception\BaseException
            }
        }
        $bRandLogin = false;
        if (!is_set($arFields, "LOGIN")) {
            $arFields["LOGIN"] = \Bitrix\Main\Security\Random::getString(50);
            $bRandLogin = true;
        }
        $userId = (int) $this->oldUser()->add($arFields);
        if (!$userId) {
            throw new \VKapi\Market\Exception\BaseException($this->getMessage('ERROR_CREATE_USER', ['#MSG#' => $this->oldUser()->LAST_ERROR]), 'ERROR_CREATE_USER', ['vkOrderId' => $this->vkOrderDisplayId, 'arFields' => $arFields]);
        }
        if ($bRandLogin) {
            $this->oldUser()->Update($userId, ["LOGIN" => "user" . $userId]);
            $arFields["LOGIN"] = "user" . $userId;
        }
        // удаление временного email
        if ($arFields['EMAIL'] == $randomEmail) {
            $DB->Query("UPDATE b_user SET EMAIL='' WHERE ID=" . $userId);
            $arFields['EMAIL'] = '';
        }
        // удаление временного номера телефона
        if ($this->isRequiredUserPhone() && $arFields['PHONE_NUMBER'] == '+79999999999') {
            $this->deleteTemporaryPhone($userId);
        }
        $arFields["USER_ID"] = $userId;
        $arEventFields = $arFields;
        unset($arEventFields["PASSWORD"]);
        unset($arEventFields["CONFIRM_PASSWORD"]);
        foreach (GetModuleEvents("vkapi.market", "OnAfterUserAdd", true) as $arEvent) {
            if (ExecuteModuleEventEx($arEvent, [&$arFields]) === false) {
                // wait true or \VKapi\Market\Exception\BaseException
            }
        }
        return $userId;
    }
    /**
     * Удалит временный номер телеофна
     * @return void
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public function deleteTemporaryPhone($userId = 0)
    {
        $filter = ['PHONE_NUMBER' => '+79999999999'];
        if ($userId > 0) {
            $filter['USER_ID'] = $userId;
        }
        $dbr = \Bitrix\Main\UserPhoneAuthTable::getList(['select' => ['ID'], 'filter' => $filter]);
        if ($ar = $dbr->fetch()) {
            \Bitrix\Main\UserPhoneAuthTable::delete($ar['ID']);
        }
    }
    /**
     * СОздаем пользвоателя для корзины, ищз за обращения в обсуждених к модулю
     * @param $userId
     * @return false|int|mixed|null
     */
    public function createSaleFUser($userId)
    {
        /**
         * #bx_651765591_15581938
         * Даниил Логвинов (logvinov@braind.agecy) Ответить29 марта 2022 14:15
         * Здравствуйте, при ручной выгрузке заказов возникает ошибка: Call to undefined method Bitrix\Sale\ResultError::getField()
         * при автоматической: Событие по Callback API - [100] Argument 'FUSER_ID' is null or empty
         */
        global $DB;
        $userId = intval($userId);
        if (!$userId) {
            return 0;
        }
        $arFUserListTmp = \CSaleUser::GetList(["USER_ID" => $userId]);
        if (!empty($arFUserListTmp)) {
            return $arFUserListTmp['ID'];
        }
        $arFields = ["=DATE_INSERT" => $DB->GetNowFunction(), "=DATE_UPDATE" => $DB->GetNowFunction(), "USER_ID" => $userId, "CODE" => md5(time() . \Bitrix\Main\Security\Random::getString(10, true))];
        $fUserId = (int) \CSaleUser::_Add($arFields);
        return $fUserId;
    }
    /**
     * Сформирует более удобнвое описание товара в заказе
     * @param $arVkOrderItem
     * @return null|mixed
     */
    public function getProductByVkOrderItem($arVkOrderItem)
    {
        if (!isset($arVkOrderItem['item'])) {
            return null;
        }
        $vkItemId = (int) $arVkOrderItem['item']['id'];
        $vkGroupId = abs((int) $arVkOrderItem['item']['owner_id']);
        $historyGood = new \VKapi\Market\Export\History\Good();
        $arElement = $historyGood->findElementByVkIdGroupId($vkItemId, $vkGroupId);
        if (!$arElement) {
            return null;
        }
        $arReturn = ['ID' => $arElement['ID'], 'NAME' => $arElement['NAME'] ?? $arVkOrderItem['item']['name'], 'PRICE' => (int) $arVkOrderItem['price']['amount'] / 100, 'CURRENCY' => (string) $arVkOrderItem['price']['currency']['name'], 'QUANTITY' => (int) $arVkOrderItem['quantity']];
        return $arReturn;
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