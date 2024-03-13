<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Sale;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Order;
use Bitrix\Sale\PaySystem;
use intec\core\helpers\ArrayHelper;

/**
 * @var $APPLICATION CMain
 * @var $USER CUser
 */

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('intec.core') || !Loader::includeModule('sale'))
    return;

class CatalogDelivery extends CBitrixComponent
{

    protected $arDeliveryServiceAll = [];
    protected $arPaySystemServiceAll = [];

    public function initShipment($order) {
        $shipmentCollection = $order->getShipmentCollection();
        $shipment = $shipmentCollection->createItem();
        $shipmentItemCollection = $shipment->getShipmentItemCollection();
        $shipment->setField('CURRENCY', $order->getCurrency());

        foreach ($order->getBasket() as $item) {
            $shipmentItem = $shipmentItemCollection->createItem($item);
            $shipmentItem->setQuantity($item->getQuantity());
        }

        return $shipment;
    }

    public function getCurrentShipment($order) {
        foreach ($order->getShipmentCollection() as $shipment)
            if (!$shipment->isSystem())
                return $shipment;

        return null;
    }

    protected function getOrderClone($order) {
        $orderClone = $order->createClone();

        $clonedShipment = $this->getCurrentShipment($orderClone);
        if (!empty($clonedShipment))
            $clonedShipment->setField('CUSTOM_PRICE_DELIVERY', 'N');

        return $orderClone;
    }

    protected function calculateDeliveries($order) {
        $this->arResult['DELIVERIES'] = [];

        $shipment = $this->initShipment($order);

        $this->arDeliveryServiceAll = Delivery\Services\Manager::getRestrictedObjectsList($shipment);

        if (!empty($this->arDeliveryServiceAll)) {
            $orderClone = null;

            foreach ($this->arDeliveryServiceAll as $deliveryId => $deliveryObj) {
                $arDelivery = [];
                $arDelivery['ID'] = $deliveryObj->getId();
                $arDelivery['NAME'] = $deliveryObj->isProfile() ? $deliveryObj->getNameWithParent() : $deliveryObj->getName();
                $arDelivery['OWN_NAME'] = $deliveryObj->getName();
                $arDelivery['DESCRIPTION'] = $deliveryObj->getDescription();
                $arDelivery["CURRENCY"] = $order->getCurrency();

                if (intval($deliveryObj->getLogotip()) > 0)
                    $arDelivery["LOGO"] = CFile::GetPath($deliveryObj->getLogotip());

                if (empty($orderClone))
                    $orderClone = $this->getOrderClone($order);

                $orderClone->isStartField();

                $clonedShipment = $this->getCurrentShipment($orderClone);
                $clonedShipment->setField('DELIVERY_ID', $deliveryId);

                $calculationResult = $orderClone->getShipmentCollection()->calculateDelivery();
                if ($calculationResult->isSuccess()) {
                    $calcDeliveries = $calculationResult->get('CALCULATED_DELIVERIES');
                    $calcResult = reset($calcDeliveries);
                } else {
                    $calcResult = new Delivery\CalculationResult();
                    $calcResult->addErrors($calculationResult->getErrors());
                }

                $orderClone->doFinalAction(true);

                $calcOrder = $orderClone;

                if ($calcResult->isSuccess()) {
                    $arDelivery['PRICE'] = Sale\PriceMaths::roundPrecision($calcResult->getPrice());

                    if ($arDelivery['PRICE'] > 0) {
                        $arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['PRICE'], $calcOrder->getCurrency());
                    } else {
                        $arDelivery['PRICE_FORMATED'] = Loc::getMessage('C_CATALOG_DELIVERY_PRICE_FREE');
                    }

                    $currentCalcDeliveryPrice = Sale\PriceMaths::roundPrecision($calcOrder->getDeliveryPrice());
                    if ($currentCalcDeliveryPrice >= 0 && $arDelivery['PRICE'] != $currentCalcDeliveryPrice) {
                        $arDelivery['DELIVERY_DISCOUNT_PRICE'] = $currentCalcDeliveryPrice;
                        $arDelivery['DELIVERY_DISCOUNT_PRICE_FORMATED'] = SaleFormatCurrency($arDelivery['DELIVERY_DISCOUNT_PRICE'], $calcOrder->getCurrency());
                    }

                    if (strlen($calcResult->getPeriodDescription()) > 0) {
                        $arDelivery['PERIOD_TEXT'] = $calcResult->getPeriodDescription();
                    }
                } else {
                    if (count($calcResult->getErrorMessages()) > 0) {
                        foreach ($calcResult->getErrorMessages() as $message) {
                            $arDelivery['CALCULATE_ERRORS'] .= $message.'<br>';
                        }
                    } else
                        $arDelivery['CALCULATE_ERRORS'] = Loc::getMessage('C_CATALOG_DELIVERY_CALCULATE_ERROR');
                }

                $arDelivery['CALCULATE_DESCRIPTION'] = $calcResult->getDescription();
                $arDelivery['PAY_SYSTEMS'] = $this->getPaySystems($calcOrder);;

                $this->arResult['DELIVERIES'][$deliveryId] = $arDelivery;
            }
        }

        if (!empty($problemDeliveries))
            $this->arResult['DELIVERIES'] += $problemDeliveries;
    }

    public function getInnerPayment(Order $order) {
        foreach ($order->getPaymentCollection() as $payment)
            if ($payment->getPaymentSystemId() == PaySystem\Manager::getInnerPaySystemId())
                return $payment;

        return null;
    }

    public function getExternalPayment(Order $order) {
        foreach ($order->getPaymentCollection() as $payment)
            if ($payment->getPaymentSystemId() != PaySystem\Manager::getInnerPaySystemId())
                return $payment;

        return null;
    }

    protected function getPaySystems(Order $order) {
        $innerPaySystemList = [];

        $innerPaySystemId = PaySystem\Manager::getInnerPaySystemId();

        $paymentCollection = $order->getPaymentCollection();
        $remainingSum = $order->getPrice() - $paymentCollection->getSum();

        $extPayment = $paymentCollection->createItem();
        $extPayment->setField('SUM', $remainingSum);

        $extPaySystemList = PaySystem\Manager::getListWithRestrictions($extPayment);

        if (empty($innerPaySystemList[$innerPaySystemId])) {
            unset($extPaySystemList[$innerPaySystemId]);
        } elseif (empty($extPaySystemList[$innerPaySystemId])) {
            $extPaySystemList[$innerPaySystemId] = $innerPaySystemList[$innerPaySystemId];
        }

        foreach ($extPaySystemList as $paySystemKey=>$paySystem) {
            if (array_key_exists($paySystem['ID'], $this->arPaySystemServiceAll)) {
                $extPaySystemList[$paySystemKey] = $this->arPaySystemServiceAll[$paySystem['ID']];
            } else {
                $arPaySystem = [
                    'ID' => $paySystem['ID'],
                    'NAME' => $paySystem['NAME'],
                    'LOGO' => ($paySystem['LOGOTIP']>0) ? CFile::GetPath($paySystem['LOGOTIP']) : false
                ];

                $this->arPaySystemServiceAll[$paySystem['ID']] = $arPaySystem;
                $extPaySystemList[$paySystemKey] = $arPaySystem;
            }
        }

        return ($extPaySystemList);
    }

    public function getExistsItemBasket($moduleId, $productId, $basket) {
        foreach ($basket as $basketItem)
            if ($basketItem->getField('PRODUCT_ID') == $productId && $basketItem->getField('MODULE') == $moduleId)
                return $basketItem;

        return null;
    }

    public function getOrder($userId) {

        $productItem = [
            'ID' => $this->arParams['PRODUCT_ID'],
            'QUANTITY'   => $this->arParams['PRODUCT_QUANTITY_VALUE']
        ];

        if ($this->arParams['USE_BASKET'] == 'Y') {
            $basket = $this->getBasket();
        } else {
            $basket = \Bitrix\Sale\Basket::create(SITE_ID);
        }

        if (!$basketItem = $this->getExistsItemBasket('catalog', $productItem['ID'], $basket))
            $basketItem = $basket->createItem('catalog', $productItem['ID']);

        $basketItem->setFields([
            'QUANTITY' => $productItem['QUANTITY'],
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider'
        ]);

        $order = \Bitrix\Sale\Order::create(SITE_ID, $userId);
        $order->setPersonTypeId($this->arParams['PERSON_ID']);
        $order->setBasket($basket);

        $orderProperties = $order->getPropertyCollection();
        $orderDeliveryLocation = $orderProperties->getDeliveryLocation();

        if (!empty($this->arParams['CITY_ID'])) {
            $orderDeliveryLocation->setValue($this->arParams['CITY_ID']);

            $dbLocation = CSaleLocation::GetList(
                [],
                ['CODE' => $this->arParams['CITY_ID']],
                false,
                false,
                ['ID']
            );

            $arLocation = $dbLocation->Fetch();

            if (!empty($arLocation)) {
                $dbZIP = CSaleLocation::GetLocationZIP($arLocation['ID']);

                $arZIP = $dbZIP->Fetch();
                if (!empty($arZIP) && intval($arZIP['ZIP'])>0) {
                    $zip = $arZIP['ZIP'];

                    $propertyZIP = $orderProperties->getDeliveryLocationZip();
                    if (!empty($propertyZIP))
                        $propertyZIP->setValue($zip);
                }
            }
        }

        return $order;
    }

    protected function getBasket() {
        $basketStorage = Sale\Basket\Storage::getInstance(Sale\Fuser::getId(), SITE_ID);
        $basket = $basketStorage->getBasket();

        $availableBasket = $basket->getOrderableItems();

        return $availableBasket;
    }

    protected function getLocation() {

        if (!empty($_SESSION['CATALOG_DELIVERY_CITY_ID'])) {
            return $this->arParams['CITY_ID'] = $_SESSION['CATALOG_DELIVERY_CITY_ID'];
        } else {
            $ip = \Bitrix\Main\Service\GeoIp\Manager::getRealIp();
            $location = \Bitrix\Sale\Location\GeoIp::getLocationCode($ip);

            if (!empty($location)) {
                return $this->arParams['CITY_ID'] = $location;
            }
        }
        return null;
    }

    public function executeComponent() {
        if (!empty($this->arParams['PRODUCT_ID']) && intval($this->arParams['PRODUCT_ID'])>0) {
            $this->arParams['PRODUCT_ID'] = intval($this->arParams['PRODUCT_ID']);
        } else {
            ShowError(Loc::getMessage('C_CATALOG_DELIVERY_ERROR_PRODUCT_ID'));
            return;
        }

        if (!$this->arParams['USE_BASKET'] == 'Y')
            $this->arParams['USE_BASKET'] = 'N';

        if (intval($this->arParams['PERSON_ID']) > 0)
            $this->arParams['PERSON_ID'] = intval($this->arParams['PERSON_ID']);

        if ($this->arParams['PRODUCT_QUANTITY_VALUE'] <= 0)
            $this->arParams['PRODUCT_QUANTITY_VALUE'] = 1;

        if (empty($this->arParams['CITY_ID']))
            $this->getLocation();

        global $USER;

        $userId = $USER->GetID();
        if (!$userId)
            $userId = CSaleUser::GetAnonymousUserID();

        $order = $this->getOrder($userId);
        $this->calculateDeliveries($order);

        if (!empty($this->arParams['CITY_ID'])) {
            $arCity = CSaleLocation::GetByID($this->arParams['CITY_ID']);
            $this->arParams['CITY_NAME'] = ArrayHelper::getValue($arCity, 'CITY_NAME_LANG');
        }

        $this->includeComponentTemplate();
    }
}