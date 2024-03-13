<?php
namespace Pecom\Delivery\Bitrix\Handler;

use Bitrix\Main\Event;
use Bitrix\Main\EventResult;
use Bitrix\Sale\Shipment;

IncludeModuleLangFile(__FILE__);

/**
 * Class GoodsPicker
 * @package Pecom\Delivery\Bitrix
 * @subpackage Handler
 */
class GoodsPicker
{
    /**
     * @var string if constant defined, complects will be handled as separated goods (not recommended at all)
     */
    protected static $complectsBlocker = 'PECOM_ECOMM_DOWNCOMPLECTS';

    /**
     * @var string if constant defined, CAN_BUY check skipped when receiving basket goods
     */
    protected static $ignoreCanBuy = 'PECOM_ECOMM_IGNORECANBUY';

    /**
     * Returns array of important product field codes
     * @return string[]
     */
    protected static function getChosenFields()
    {
        return [
            'ORDER_ID',
            'ID',
            'LID',
            'PRODUCT_ID',
            'TYPE',
            'SET_PARENT_ID',
            'NAME',
            'CAN_BUY',
            'DELAY',
            'PRICE',
            'BASE_PRICE',
            'CURRENCY',
            'VAT_RATE',
            'VAT_INCLUDED',
            'MEASURE_CODE',
            'MEASURE_NAME',
            'QUANTITY',
            'WEIGHT',
            'DIMENSIONS'
        ];
    }

    /**
     * Call onItemsListReady event
     * @param array $goods
     */
    protected static function callOnItemsListReady(&$goods)
    {
        // onItemsListReady module event
        $event = new Event(PECOM_ECOMM, "onItemsListReady", ['ITEMS' => $goods]);
        $event->send();

        $results = $event->getResults();
        if (is_array($results) && !empty($results)) {
            foreach ($results as $eventResult) {
                if ($eventResult->getType() !== EventResult::SUCCESS)
                    continue;

                $params = $eventResult->getParameters();
                if (isset($params["ITEMS"]))
                    $goods = $params["ITEMS"];
            }
        }
    }

    /**
     * Get goods from current user's basket
     * @return array
     */
    public static function fromBasket()
    {
        return self::getBasketGoods(["ORDER_ID" => "NULL", "FUSER_ID" => \CSaleBasket::GetBasketUserID(), "LID" => SITE_ID]);
    }

    /**
     * Get goods from specified Order
     * @param $orderId
     * @return array|false
     */
    public static function fromOrder($orderId)
    {
        if ($orderId) {
            return self::getBasketGoods(['ORDER_ID' => $orderId]);
        }

        return false;
    }

    /**
     * Get goods from specified Shipment
     * @return false
     */
    public static function fromShipment()
    {
        // TODO: implement

        return false;
    }

    /**
     * Get goods from Bitrix Shipment object
     * @param Shipment $shipment
     * @return array
     */
    public static function fromShipmentObject($shipment)
    {
        $goods = [];
        $chosenFields = array_flip(self::getChosenFields());

        // Get shipment items data
        $shipmentItemCollection = $shipment->getShipmentItemCollection();

        /** @var \Bitrix\Sale\ShipmentItem $shipmentItem */
        foreach ($shipmentItemCollection as $shipmentItem) {
            $basketItem = $shipmentItem->getBasketItem();
            if (!$basketItem)
                continue;

            // if ($basketItem->isBundleChild())
            //    continue;

            $item = $basketItem->getFieldValues();

            if (!is_array($item["DIMENSIONS"]) && !empty($item["DIMENSIONS"]) && is_string($item["DIMENSIONS"])) {
                $item["DIMENSIONS"] = unserialize($item["DIMENSIONS"], ['allowed_classes' => false]);
            }

            $goods[] = array_intersect_key($item, $chosenFields);
        }

        self::handleComplects($goods);
        self::callOnItemsListReady($goods);

        return $goods;
    }

    /**
     * Get goods from array with specific structure @link https://dev.1c-bitrix.ru/api_help/sale/delivery.php
     * @param array $items
     * @return array
     */
    public static function fromArray($items)
    {
        $goods  = [];
        $chosenFields = array_flip(self::getChosenFields());

        foreach ($items as $item) {
            if (!is_array($item["DIMENSIONS"]) && !empty($item["DIMENSIONS"]) && is_string($item["DIMENSIONS"])) {
                $item["DIMENSIONS"] = unserialize($item["DIMENSIONS"], ['allowed_classes' => false]);
            }

            $goods[] = array_intersect_key($item, $chosenFields);
        }

        self::handleComplects($goods);
        self::callOnItemsListReady($goods);

        return $goods;
    }

    /**
     * Get basket goods that match da filter
     * @param array $filter conditions
     * @return array
     */
    protected static function getBasketGoods($filter = [])
    {
        $goods  = [];
        $noCanBuy = (defined(self::$ignoreCanBuy) && constant(self::$ignoreCanBuy) === true);

        $dbBasketItems = \CSaleBasket::GetList([], $filter, false, false, self::getChosenFields());
        while ($item = $dbBasketItems->Fetch()) {
            if (($item['CAN_BUY'] == 'Y' || $noCanBuy) && $item['DELAY'] == 'N') {
                $item['DIMENSIONS'] = unserialize($item['DIMENSIONS'], ['allowed_classes' => false]);
                $goods[] = $item;
            }
        }

        self::handleComplects($goods);
        self::callOnItemsListReady($goods);

        return $goods;
    }

    /**
     * Do magic with type SET basket goods
     * @param $goods
     */
    protected static function handleComplects(&$goods)
    {
        $complects = array();
        foreach ($goods as $good) {
            if (
                array_key_exists('SET_PARENT_ID', $good) &&
                $good['SET_PARENT_ID'] &&
                $good['SET_PARENT_ID'] != $good['ID']
            ) {
                $complects[$good['SET_PARENT_ID']] = true;
            }
        }

        if (defined(self::$complectsBlocker) && constant(self::$complectsBlocker) === true) {
            foreach ($goods as $key => $good) {
                if (array_key_exists($good['ID'], $complects)) {
                    unset($goods[$key]);
                }
            }
        } else {
            foreach ($goods as $key => $good) {
                if (
                    array_key_exists('SET_PARENT_ID', $good) &&
                    array_key_exists($good['SET_PARENT_ID'], $complects) &&
                    $good['SET_PARENT_ID'] != $good['ID']
                ) {
                    unset($goods[$key]);
                }
            }
        }
    }

    /**
     * Add marking codes to basket goods data
     * @param array $goods array of basket goods
     * @param int $orderId Bitrix order id
     */
    public static function addGoodsQRs(&$goods, $orderId)
    {
        $isMarkingAvailable = method_exists('\\Bitrix\\Sale\\ShipmentItemStore', 'getMarkingCode');
        $order = \Bitrix\Sale\Order::load($orderId);

        $shipments = $order->getShipmentCollection();
        foreach ($shipments as $shipment) {
            $items = $shipment->getShipmentItemCollection();
            foreach ($items as $item) {
                /** @var \Bitrix\Sale\BasketItem $basketItem */
                $basketItem = $item->getBasketItem();

                $stores = $item->getShipmentItemStoreCollection();
                foreach ($stores as $store) {
                    /** @var \Bitrix\Sale\ShipmentItemStore $store */
                    $mark = ($isMarkingAvailable) ? $store->getMarkingCode() : '';

                    foreach ($goods as $key => $stuff) {
                        if ((int)$goods[$key]['PRODUCT_ID'] === $basketItem->getProductId()) {
                            if (!array_key_exists('QR', $goods[$key])) {
                                $goods[$key]['QR'] = array();
                            }
                            $goods[$key]['QR'][] = $mark;
                        }
                    }
                }
            }
        }
    }

    /**
     * Add property values to basket goods data
     * @param array $goods array of basket goods
     * @param string[] $propertyCodes array of IBlock element property codes
     */
    public static function addBasketGoodProperties(&$goods, $propertyCodes)
    {
        if (\CModule::IncludeModule('iblock')) {
            $itemsProperties = [];
            $itemsToIblocks  = [];
            $itemsIds        = [];
            $offersToParents = [];
            $propertyCodes   = array_values(array_filter($propertyCodes));

            foreach ($goods as $good) {
                // Search for iblock required
                $itemsIds[] = $good['PRODUCT_ID'];

                // Already know where they are
                if ($parent = \CCatalogSku::GetProductInfo($good['PRODUCT_ID'])) {
                    $itemsToIblocks[$parent['IBLOCK_ID']][$parent['ID']]['SKU_CHILDS'][] = $good['PRODUCT_ID'];
                    $offersToParents[$good['PRODUCT_ID']] = $parent['ID'];
                }
            }

            $elementsDB = \CIBlockElement::GetList([], ['=ID' => $itemsIds], false, false, ['ID', 'IBLOCK_ID']);
            while ($tmp = $elementsDB->fetch()) {
                $itemsToIblocks[$tmp['IBLOCK_ID']][$tmp['ID']] = [];

                if (array_key_exists($tmp['ID'], $offersToParents)) {
                    $itemsToIblocks[$tmp['IBLOCK_ID']][$tmp['ID']]['SKU_PARENT'] = $offersToParents[$tmp['ID']];
                }
            }
            unset($elementsDB);

            // Collect property values for all elements
            foreach ($itemsToIblocks as $iblockId => $elements) {
                $propsData = self::getElementPropertyValues($iblockId, array_keys($elements), $propertyCodes);

                foreach ($elements as $elementId => $data) {
                    $itemsProperties[$elementId] = $itemsToIblocks[$iblockId][$elementId];

                    if (!empty($propsData) && is_array($propsData[$elementId])) {
                        $itemsProperties[$elementId]['PROPERTIES'] = $propsData[$elementId];
                    }
                }
            }
            unset($itemsToIblocks);

            // Assign property values
            foreach ($goods as $key => $arGood) {
                $goods[$key]['PROPERTIES'] = [];

                $hasOwnProps = is_array($itemsProperties[$arGood['PRODUCT_ID']]['PROPERTIES']);
                foreach ($propertyCodes as $propertyCode) {
                    // Take own property value first
                    $goods[$key]['PROPERTIES'][$propertyCode] = ($hasOwnProps && array_key_exists($propertyCode, $itemsProperties[$arGood['PRODUCT_ID']]['PROPERTIES'])) ?
                        $itemsProperties[$arGood['PRODUCT_ID']]['PROPERTIES'][$propertyCode] : '';

                    // Try SKU parent property if own property are empty and parent exists
                    if (empty($goods[$key]['PROPERTIES'][$propertyCode]) && array_key_exists($arGood['PRODUCT_ID'], $itemsProperties) && array_key_exists('SKU_PARENT', $itemsProperties[$arGood['PRODUCT_ID']])) {
                        $parentId = $itemsProperties[$arGood['PRODUCT_ID']]['SKU_PARENT'];
                        if (is_array($itemsProperties[$parentId]['PROPERTIES']) && array_key_exists($propertyCode, $itemsProperties[$parentId]['PROPERTIES'])) {
                            $goods[$key]['PROPERTIES'][$propertyCode] = $itemsProperties[$parentId]['PROPERTIES'][$propertyCode];
                        }
                    }
                }
            }
        }
    }

    /**
     * Get iblock element property values
     * @param int $iblockId IBlock Id
     * @param int[] $elementIds array of element Ids
     * @param string[] $propertyCodes array of IBlock element property codes
     * @return array
     */
    public static function getElementPropertyValues($iblockId, $elementIds, $propertyCodes)
    {
        $result = [];

        if (\CModule::IncludeModule('iblock')) {
            $propertyResult = array_fill_keys($elementIds, ['PROPERTIES' => []]);
            $filter         = ['=ID' => $elementIds];
            $propertyFilter = ['CODE' => $propertyCodes];
            $options        = ['USE_PROPERTY_ID' => 'N', 'GET_RAW_DATA' => 'Y', 'PROPERTY_FIELDS' => ['DEFAULT_VALUE', 'MULTIPLE']];

            \CIBlockElement::GetPropertyValuesArray($propertyResult, (int)$iblockId, $filter, $propertyFilter, $options);

            foreach ($propertyResult as $elementId => $elementData) {
                if (!empty($elementData['PROPERTIES'])) {
                    foreach ($propertyCodes as $propertyCode) {
                        $result[$elementId][$propertyCode] = (array_key_exists($propertyCode, $propertyResult[$elementId]['PROPERTIES'])) ?
                            $propertyResult[$elementId]['PROPERTIES'][$propertyCode]['VALUE'] : '';

                        // Take first value if prop are multiple (normally no multiple props supported cause API handle only scalar values)
                        if ($propertyResult[$elementId]['PROPERTIES'][$propertyCode]['MULTIPLE'] === 'Y' && !empty($result[$elementId][$propertyCode])) {
                            $result[$elementId][$propertyCode] = array_shift($result[$elementId][$propertyCode]);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Makes good with optionally given params
     * @param array $params
     * @return array
     */
    public static function makeSimpleGood($params = array())
    {
        $good = array(
            "MODULE"        => PECOM_ECOMM.'Delivery',
            "ORDER_ID"      => '',
            "LID"           => (array_key_exists("LID", $params)) ? $params["LID"] : SITE_ID,
            "TYPE"          => '',
            "SET_PARENT_ID" => (array_key_exists("SET_PARENT_ID", $params)) ? $params['SET_PARENT_ID'] : '',
            "NAME"          => 'testGood',
            "CAN_BUY"       => 'Y',
            "DELAY"         => 'N',
            "CURRENCY"      => (array_key_exists("CURRENCY", $params)) ? $params['CURRENCY'] : 'RUB',
            "VAT_RATE"      => (array_key_exists("VAT_RATE", $params)) ? $params['VAT_RATE'] : 0.0000,
            "VAT_INCLUDED"  => (array_key_exists("VAT_INCLUDED", $params)) ? $params['VAT_INCLUDED'] : 'Y',
            "MEASURE_CODE"  => 796, // Pieces
            "MEASURE_NAME"  => \GetMessage('PECOM_ECOMM_MEASURE_PCE'),
            "QUANTITY"      => (array_key_exists("QUANTITY", $params)) ? $params["QUANTITY"] : 1,
            "WEIGHT"        => (array_key_exists("WEIGHT", $params)) ? $params["WEIGHT"] : 0,
            "DIMENSIONS"    => array(
                "WIDTH"     => (array_key_exists("WIDTH", $params))  ? $params["WIDTH"]  : 0,
                "HEIGHT"    => (array_key_exists("HEIGHT", $params)) ? $params["HEIGHT"] : 0,
                "LENGTH"    => (array_key_exists("LENGTH", $params)) ? $params["LENGTH"] : 0
            )
        );

        foreach (['ID', 'PRODUCT_ID', 'PRICE', 'BASE_PRICE'] as $key) {
            $good[$key] = (array_key_exists($key, $params)) ? $params[$key] : 0;
        }

        return $good;
    }
}