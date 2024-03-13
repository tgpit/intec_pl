<?php
namespace Pecom\Ecomm\Widget;

use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;
use CUser;
use Pec\Delivery\Tools;
use Pecom\Ecomm\AbstractHelper;
use Bitrix\Sale\Shipment;
use CIBlockElement;
use Bitrix\Sale\Delivery\Services\Manager;

use Pecom\Delivery\Bitrix\Adapter\Cargoes;
use Pecom\Delivery\Bitrix\Handler\GoodsPicker;

class Helper extends AbstractHelper
{
    /** @var string Module name */
    const MODULE_ID = 'pecom.ecomm';

    /**
     * Returns info for widget loader
     * @return array
     */
    public static function getConfigByShipment(Shipment $shipment)
    {
        $cargoesAdapter = new Cargoes();

        $goods = GoodsPicker::fromShipmentObject($shipment);
        $cargoesAdapter->fromGoodsArray($goods);

        $totalWeight = (float)($cargoesAdapter->getCoreCargoes()->getTotalWeight()/1000); // To Kg
        $totalDims   = $cargoesAdapter->getCoreCargoes()->getTotalDimensions();
        $totalVolume = round(($totalDims->getLength()/1000) * ($totalDims->getWidth()/1000) * ($totalDims->getHeight()/1000), 2, PHP_ROUND_HALF_UP);

        $order = $shipment->getCollection()->getOrder();
        $result = [
            'ADDRESS' => static::getAddress($shipment),
            'DIMENSION' => [
                'WIDTH' => $totalDims->getWidth()/1000,
                'HEIGHT' => $totalDims->getHeight()/1000,
                'LENGTH' => $totalDims->getLength()/1000,
                'VOLUME' => $totalVolume
            ],
            'FROM_ADDRESS' => Option::get(static::MODULE_ID, "PEC_STORE_ADDRESS", ''),
            'FROM_TYPE' => Option::get(static::MODULE_ID, "PEC_STORE_PZZ", ''),
            'FROM_DEPARTMENT_UID' => Option::get(static::MODULE_ID, "PEC_STORE_DEPARTMENT_UID", ''),
            'MAIN' => [
                'marginType' => Manager::getById(Tools::getDeliveryID())['CONFIG']['MAIN']['MARGIN_TYPE'],
                'marginValue' => Manager::getById(Tools::getDeliveryID())['CONFIG']['MAIN']['MARGIN_VALUE'],
            ],
            'PEC_COST_OUT' => Option::get(static::MODULE_ID, "PEC_COST_OUT", '1'),
            'PEC_SHOW_TYPE_WIDGET' => Option::get(static::MODULE_ID, 'PEC_SHOW_TYPE_WIDGET', 'modal'),
            'PEC_SHOW_WIDGET' => (int)Option::get(static::MODULE_ID, "PEC_SHOW_WIDGET"),
            'PRICE' => $order->getPrice() - $order->getDeliveryPrice(),
            'VOLUME' => $totalVolume,
            'WEIGHT' => $totalWeight,
            'SELF_PACK' => (int)static::getSelfPack($shipment),
            'WIDGET_URL' => Option::get(static::MODULE_ID, 'PEC_WIDGET_URL', 'https://calc.pecom.ru/iframe/e-store-calculator'),
            'deliveryId' => Tools::getDeliveryID(),
            'options' => [
                'ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK' => Option::get(static::MODULE_ID, 'ID_FOR_INSERT_AFTER_PEC_PICKUP_BLOCK', 'bx-soa-delivery'),
            ],
            'text' => [
                'address' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_ADDRESS'),
                'change' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_CHANGE'),
                'address_to' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_ADDRESS_TO'),
                'term' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_TERM'),
                'btn' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_BTN'),
                'error' => GetMessage('PEC_DELIVERY_WIDGET_CONFIG_ERROR'),
            ],
            'transportationType' => Option::get(static::MODULE_ID, 'PEC_API_TYPE_DELIVERY', 'auto'),
            'INN' => Option::get(static::MODULE_ID, "PEC_STORE_INN", '0'),
            'KPP' => Option::get(static::MODULE_ID, "PEC_STORE_KPP", '0'),
        ];
        return $result;
    }

    /**
     * @deprecated
     */
    protected static function getVolume(Shipment $shipment)
    {
        $order = $shipment->getCollection()->getOrder();
        $volume = 0;
        foreach ($order->getBasket() as $item) {
            $dimensions = unserialize($item->getField('DIMENSIONS'));
            $itemQuantity = $item->getQuantity();
            if ($dimensions['WIDTH'] != 0 && $dimensions['HEIGHT'] != 0 && $dimensions['LENGTH'] != 0) {
                $itemVolume = Tools::calculateVolumeM3($dimensions);
            } else {
                $itemVolume = floatval(Option::get(static::MODULE_ID, "PEC_VOLUME", 0.001));
            }
            $volume += $itemVolume * $itemQuantity;
        }
        return $volume;
    }

    /**
     * @deprecated
     */
    protected static function getWeight(Shipment $shipment)
    {
        $order = $shipment->getCollection()->getOrder();
        $weight = 0;
        foreach ($order->getBasket() as $item) {
            $itemQuantity = $item->getQuantity();
            $itemWeight = (float)$item->getWeight() / 1000;
            $minWeight = (float)Option::get(static::MODULE_ID, "PEC_WEIGHT", 0.05);
            $itemWeight = max($itemWeight, $minWeight);
            $weight += $itemWeight * $itemQuantity;
        }
        return $weight;
    }

    protected static function getAddress(Shipment $shipment)
    {
        global $USER;
        if (Option::get(static::MODULE_ID, "PEC_GET_USER_ADDRESS", '') == 'personal') {
            $result = CUser::GetByID($USER->GetID())->Fetch()['PERSONAL_CITY'];
            if ($result) {
                return $result;
            }
        }
        if (Option::get(static::MODULE_ID, "PEC_GET_USER_ADDRESS", '') == 'work') {
            $result = CUser::GetByID($USER->GetID())->Fetch()['WORK_CITY'];
            if ($result) {
                return $result;
            }
        }

        $locProp = $shipment->getCollection()->getOrder()->getPropertyCollection()->getDeliveryLocation();
        if($locProp) {
            $locationCode = $locProp->getValue();
            if($locationCode != '') {
                $result = \Bitrix\Sale\Location\LocationTable::getByCode($locationCode, array(
                    'filter' => array('=NAME.LANGUAGE_ID' => LANGUAGE_ID),
                    'select' => array('NAME_RU' => 'NAME.NAME')
                ))->fetch()['NAME_RU'];
                if ($result) {
                    return $result;
                }
            }
        }

        return Option::get(static::MODULE_ID, "PEC_STORE_ADDRESS", '');
    }

    /**
     * @deprecated
     */
    protected static function getDimension(Shipment $shipment)
    {
        $order = $shipment->getCollection()->getOrder();

        $result = ['WIDTH' => 0, 'HEIGHT' => 0, 'LENGTH' => 0];
        foreach ($order->getBasket() as $item) {
            $dimensions = unserialize($item->getField('DIMENSIONS'));
            $quantity = $item->getQuantity();
            if (!$dimensions['WIDTH']) $dimensions['WIDTH'] = Option::get('pecom.ecomm', "PEC_MAX_SIZE", '0.2')*1000;
            if (!$dimensions['HEIGHT']) $dimensions['HEIGHT'] = Option::get('pecom.ecomm', "PEC_MAX_SIZE", '0.2')*1000;
            if (!$dimensions['LENGTH']) $dimensions['LENGTH'] = Option::get('pecom.ecomm', "PEC_MAX_SIZE", '0.2')*1000;

            $result['VOLUME'] +=
                $dimensions['LENGTH']/1000
                * $dimensions['HEIGHT']/1000
                * $dimensions['WIDTH']/1000
                * $quantity;
            sort($dimensions);
            $dimensions[0] = $dimensions[0] * $quantity;
            rsort($dimensions);

            if ($result['WIDTH'] < $dimensions[2]) $result['WIDTH'] = $dimensions[2];
            if ($result['HEIGHT'] < $dimensions[1]) $result['HEIGHT'] = $dimensions[1];
            if ($result['LENGTH'] < $dimensions[0]) $result['LENGTH'] = $dimensions[0];
        }

        $result['WIDTH'] = $result['WIDTH'] / 1000;
        $result['HEIGHT'] = $result['HEIGHT'] / 1000;
        $result['LENGTH'] = $result['LENGTH'] / 1000;

        return $result;
    }

    protected static function getSelfPack(Shipment $shipment)
    {
        if (Option::get(static::MODULE_ID, "PEC_SELF_PACK", 0)) {
            return true;
        }

        $self_pack_prop = Option::get(static::MODULE_ID, "PEC_SELF_PACK_INPUT", '');
        if (empty($self_pack_prop)) {
            return false;
        }

        foreach ($shipment->getCollection()->getOrder()->getBasket() as $item) {
            $product_id = $item->getField('PRODUCT_ID');
            $fragile = CIBlockElement::GetByID($product_id)->GetNextElement()->GetProperties()[$self_pack_prop]['VALUE'];
            if ($fragile == 'Y') {
                return true;
            }
        }

        return false;
    }
}
