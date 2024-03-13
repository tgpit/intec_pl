<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Sale\Order;
use Pec\Delivery\PecomEcommDb;
use Pecom\Ecomm\ORM\ShipmentPropsValueTable;
use Pec\Delivery\Tools;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Shipment;

use Pecom\Delivery\Bitrix\Adapter\Cargoes;

/** @global CMain $APPLICATION */

//error_reporting(E_ERROR | E_WARNING | E_PARSE);
//ini_set('display_errors', 1);

Loader::includeModule('pecom.ecomm');
Loader::includeModule('sale');

if (empty($_POST['ID']) || (int)$_POST['ID'] <= 0) {
    echo GetMessage('PEC_DELIVERY_FORM_ERROR_NO_ORDER');
    die();
}

$orderId = $_POST['ID'];
Tools::$ORDER_ID = $orderId;
$order = Order::load($orderId);

if (is_null($order)) {
    echo sprintf(GetMessage('PEC_DELIVERY_FORM_ERROR_NO_ORDER').'%s', $orderId);
    die();
}

$shipmentCollection = [];
foreach ($order->getShipmentCollection() as $shipment) {
    if (!$shipment->isSystem() && get_class($shipment->getDelivery()) == 'Sale\Handlers\Delivery\PecomEcommHandler') {
        $shipmentCollection[] = $shipment;
    }
}

if (empty($shipmentCollection)) {
    echo sprintf(GetMessage('PEC_DELIVERY_FORM_ERROR_NO_SHIPMENT').'%s', $orderId);
    die();
}

$shipment = $shipmentCollection[0];
$shipmentId = $shipment->getId();

function getOrderFieldValue($order, $optionName)
{
    $propsName = Option::get('pecom.ecomm', $optionName, null);
    if (empty($propsName)) {
        return '';
    }
    $propertyCollection = $order->getPropertyCollection();

    foreach($propertyCollection as $property)
    {
        if ($property->getField('CODE') == $propsName) {
            return $property->getField('VALUE');
        }
    }
}

function setOrderFieldValue($order, $optionName, $value)
{
    $propsName = Option::get('pecom.ecomm', $optionName, null);
    if (empty($propsName)) {
        return '';
    }
    $propertyCollection = $order->getPropertyCollection();

    foreach($propertyCollection as $property)
    {
        if ($property->getField('CODE') == $propsName) {
            $property->setField('VALUE', $value);
        }
    }
}

$shipmentData = ShipmentPropsValueTable::query()
    ->setSelect(['PROPS_CODE', 'VALUE'])
    ->setFilter(['=SHIPMENT_ID' => $shipmentId])
    ->exec()
    ->fetchAll();
$shipmentData = array_combine(
    array_column($shipmentData, 'PROPS_CODE'),
    array_column($shipmentData, 'VALUE')
);

if (!empty($_POST['action']) && ($_POST['action'] == 'save' || $_POST['action'] == 'update')) {

    $action = $_POST['action'];
    unset($_POST['action']);

    foreach ($_POST as $postKey => $postValue) {
        if (!isset($shipmentData[$postKey])) {
            ShipmentPropsValueTable::add([
                'ORDER_ID' => $orderId,
                'SHIPMENT_ID' => $shipmentId,
                'PROPS_CODE' => $postKey,
                'VALUE' => $postValue
            ]);
        } elseif ($shipmentData[$postKey] != $postValue) {
            $propValueId = ShipmentPropsValueTable::query()
                ->setSelect(['ID'])
                ->setFilter(['=PROPS_CODE' => $postKey, '=SHIPMENT_ID' => $shipmentId])
                ->setLimit(1)
                ->exec()
                ->fetch()['ID'];
            ShipmentPropsValueTable::update($propValueId, [
                'ORDER_ID' => $orderId,
                'SHIPMENT_ID' => $shipmentId,
                'PROPS_CODE' => $postKey,
                'VALUE' => $postValue,
            ]);
            $shipmentData[$postKey] = $postValue;
        }
    }

    if (!empty($_POST['PAYER_NAME'])) {
        $order->getPropertyCollection()->getPayerName()->setValue($_POST['PAYER_NAME']);
    }
    if (!empty($_POST['DOCUMENT_TYPE'])) {
        setOrderFieldValue($order, 'PEC_DOCUMENT_TYPE', $_POST['DOCUMENT_TYPE']);
    }
    if (!empty($_POST['DOCUMENT_SERIES'])) {
        setOrderFieldValue($order, 'PEC_DOCUMENT_SERIES', $_POST['DOCUMENT_SERIES']);
    }
    if (!empty($_POST['DOCUMENT_NUMBER'])) {
        setOrderFieldValue($order, 'PEC_DOCUMENT_NUMBER', $_POST['DOCUMENT_NUMBER']);
    }
    if (!empty($_POST['DOCUMENT_DATE'])) {
        setOrderFieldValue($order, 'PEC_DOCUMENT_DATE', $_POST['DOCUMENT_DATE']);
    }
    if (!empty($_POST['PAYER_PHONE'])) {
        $order->getPropertyCollection()->getPhone()->setValue($_POST['PAYER_PHONE']);
    }
    if (!empty($_POST['PAYER_EMAIL'])) {
        $order->getPropertyCollection()->getUserEmail()->setValue($_POST['PAYER_EMAIL']);
    }
    if (!empty($_POST['COMMENT'])) {
        $order->setField('USER_DESCRIPTION', $_POST['COMMENT']);
    }
    if (!empty($_POST['pec_address'])) {
        $order->getPropertyCollection()->getAddress()->setValue($_POST['pec_address']);
    }

    $order->save();

    if (!empty($_POST['pec_price']) && $_POST['pec_cost_out'] === '0') {
        $_SESSION['pec_post']['price'] = $_POST['pec_price'];
        $shipment->setBasePriceDelivery($_POST['pec_price']);
        $ordProp = CSaleOrder::GetByID($orderId);
        $db_props = CSaleOrderPropsValue::GetOrderProps($orderId);
        $arFieldsDev = array(
            "PRICE_DELIVERY" => $_POST['pec_price'],
            "PRICE" => (int)$ordProp["PRICE"] - (int)$_POST['OLD_DELIVERY_PRICE'] + (int)$_POST['pec_price']
        );
        CSaleOrder::Update($orderId, $arFieldsDev);
    }
    if (!empty($_POST['pec_widget_data'])) {
        $widgetData = json_decode($_POST['pec_widget_data']);
        PecomEcommDb::AddOrderData($orderId, 'WIDGET', serialize($widgetData));
        $_SESSION['MAIN']['marginType'] = Pecom\Ecomm\Widget\Helper::getConfigByShipment($shipment)['MAIN']['marginType'];
        $_SESSION['MAIN']['marginValue'] = Pecom\Ecomm\Widget\Helper::getConfigByShipment($shipment)['MAIN']['marginValue'];
    }

    CSaleOrder::Update($orderId, []);

    ?><?=GetMessage('PEC_DELIVERY_FORM_MESS_SAVED')?>
    <script type="text/javascript">
        location.reload();
    </script><?php

    die();
}

// Makes cargoes for default form values
$cargoesAdapter = new Cargoes();
$cargoesAdapter->fromOrder($orderId);

$totalWeight = (float)($cargoesAdapter->getCoreCargoes()->getTotalWeight()/1000); // To Kg
$totalDims   = $cargoesAdapter->getCoreCargoes()->getTotalDimensions();
$totalVolume = round(($totalDims->getLength()/1000) * ($totalDims->getWidth()/1000) * ($totalDims->getHeight()/1000), 2, PHP_ROUND_HALF_UP);

?><form method="POST" action="/bitrix/js/pecom.ecomm/form.php" id="pec_order_edit_form">
    <table class="adm-detail-content-table">
        <tbody>
        <tr class="heading">
            <td colspan="2">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_ORDER')?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_ORDER_ID')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="ID" value="<?=$orderId?>"type="hidden" required>
                            <input name="ORDER_ID" value="<?=$orderId?>" pattern="<?=$orderId?>" type="text" disabled required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_STORE_TITLE')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input type="text" name="STORE_NAME" required disabled
                                value="<?=Option::get('pecom.ecomm', 'PEC_STORE_TITLE')?>">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_RECEIVER')?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_FIO')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="PAYER_NAME" value="<?=$order->getPropertyCollection()->getPayerName()->getValue()?>" type="text" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DOC_TYPE')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <?php $arVariants = Application::getInstance()->getConnection()->query(sprintf('
                                        SELECT `variant`.`VALUE`, `variant`.`NAME`
                                        FROM `b_sale_order_props` `props`
                                        INNER JOIN `b_sale_order_props_variant` AS `variant` ON `props`.ID = `variant`.ORDER_PROPS_ID
                                        WHERE `props`.`CODE` = "%s"
                                    ', Option::get('pecom.ecomm', 'PEC_DOCUMENT_TYPE')))->fetchAll();
                            $selectedVariant = getOrderFieldValue($order, 'PEC_DOCUMENT_TYPE');?>
                            <select name="DOCUMENT_TYPE" required>
                                <?php foreach ($arVariants as $arVariant) {?>
                                    <option value="<?=$arVariant['VALUE']?>" <?=$selectedVariant == $arVariant['VALUE'] ? 'selected' : ''?>><?=$arVariant['NAME']?></option>
                                <?php }?>
                            </select>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DOC_SERIES')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="DOCUMENT_SERIES" value="<?=getOrderFieldValue($order, 'PEC_DOCUMENT_SERIES')?>" type="text" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DOC_NUM')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="DOCUMENT_NUMBER" value="<?=getOrderFieldValue($order, 'PEC_DOCUMENT_NUMBER')?>" type="text" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DOC_DATE')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <?php $APPLICATION->IncludeComponent(
                                "bitrix:main.calendar",
                                "",
                                Array(
                                    "FORM_NAME" => "",
                                    "HIDE_TIMEBAR" => "Y",
                                    "INPUT_NAME" => "DOCUMENT_DATE",
                                    "INPUT_NAME_FINISH" => "",
                                    "INPUT_VALUE" => getOrderFieldValue($order, 'PEC_DOCUMENT_DATE'),
                                    "INPUT_VALUE_FINISH" => "",
                                    "SHOW_INPUT" => "Y",
                                    "SHOW_TIME" => "N"
                                )
                            );?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_PHONE')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="PAYER_PHONE" value="<?=$order->getPropertyCollection()->getPhone()->getValue()?>" type="text" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_EMAIL')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="PAYER_EMAIL" value="<?=$order->getPropertyCollection()->getUserEmail()->getValue()?>" type="text" required>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_COMMENT')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <textarea name="COMMENT" style="width: 300px; height: 47px;"><?=$order->getField("USER_DESCRIPTION")?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DETAILS')?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_T_WEIGHT')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input id="PEC_WEIGHT" disabled name="WEIGHT" value="<?=$shipmentData['WEIGHT'] ?? $totalWeight?>" required type="text" onchange="
                                let container = document.getElementById('pec_widget_container');
                                let frame = container.querySelector('iframe');
                                let src = new window.URL(frame.src);
                                let attrs = {weight: document.getElementById('PEC_WEIGHT').value};
                                for (const [key, value] of Object.entries(attrs)) {
                                    src.searchParams.set(key, value);
                                }
                                frame.src = src.toString();
                            "> <?=GetMessage('PEC_DELIVERY_FORM_LBL_KG')?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_T_VOLUME')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input id="PEC_VOLUME" disabled name="VOLUME" value="<?=$shipmentData['VOLUME'] ?? $totalVolume?>" required type="text" onchange="
                                let container = document.getElementById('pec_widget_container');
                                let frame = container.querySelector('iframe');
                                let src = new window.URL(frame.src);
                                let attrs = {volume: document.getElementById('PEC_VOLUME').value};
                                for (const [key, value] of Object.entries(attrs)) {
                                    src.searchParams.set(key, value);
                                }
                                frame.src = src.toString();
                            "> <?=GetMessage('PEC_DELIVERY_FORM_LBL_M3')?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_MAX_DIM')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input id="PEC_MAX_DIMENSION" disabled name="MAX_DIMENSION" value="<?=$shipmentData['MAX_DIMENSION'] ?? max(
                                $totalDims->getLength()/1000,
                                $totalDims->getWidth()/1000,
                                $totalDims->getHeight()/1000
                            )?>" required type="text" onchange="
                                let container = document.getElementById('pec_widget_container');
                                let frame = container.querySelector('iframe');
                                let src = new window.URL(frame.src);
                                let attrs;
                                if (src.searchParams.get('width') >= Math.max(src.searchParams.get('height'), src.searchParams.get('length')))
                                    attrs = {width: document.getElementById('PEC_MAX_DIMENSION').value};
                                if (src.searchParams.get('height') >= Math.max(src.searchParams.get('width'), src.searchParams.get('length')))
                                    attrs = {height: document.getElementById('PEC_MAX_DIMENSION').value};
                                if (src.searchParams.get('length') >= Math.max(src.searchParams.get('height'), src.searchParams.get('width')))
                                    attrs = {length: document.getElementById('PEC_MAX_DIMENSION').value};
                                for (const [key, value] of Object.entries(attrs)) {
                                    src.searchParams.set(key, value);
                                }
                                frame.src = src.toString();
                            "> <?=GetMessage('PEC_DELIVERY_FORM_LBL_M')?>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_POSITION')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input name="POSITION_COUNT" disabled value="<?=$shipmentData['POSITION_COUNT'] ?? Tools::getPecPositionCount($orderId)?>" required type="text">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_ADDRESS')?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-content-cell-r" colspan="2">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td id="pec_widget_container"></td>
                    </tr>
                    </tbody>
                </table>
                <?php
                $widgetData = \Pec\Delivery\PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');
                $_SESSION['pec_post']['arParams'] = \Pecom\Ecomm\Widget\Helper::getConfigByShipment($shipment);
                ?>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
                <script src="/bitrix/js/pecom.ecomm/admin_widget.js"></script>
                <script type="text/javascript">
                    pecomEcommParamsAjax();
                    (function() {
                        let container = document.getElementById('pec_widget_container');
                        let frame = document.createElement('iframe');
                        let src = new URL(widgetGetSrcPEC());
                        let attrs = <?=CUtil::PhpToJSObject($widgetData->toAddressType == 'address' ? [
                            'delivery' => (int)($widgetData->toAddressType == 'address') ?? null,
                            'address-to' => $widgetData->toAddress ?? null,
                        ] : [
                            'delivery' => (int)($widgetData->toAddressType == 'address') ?? null,
                            'address-to' => $widgetData->toAddress ?? null,
                            'department-to-uid' => $widgetData->toDepartmentData->UID ?? null,
                        ])?>;

                        attrs.weight = document.getElementById('PEC_WEIGHT').value;
                        attrs.volume = document.getElementById('PEC_VOLUME').value;
                        attrs.width = document.getElementById('PEC_MAX_DIMENSION').value;
                        for (const [key, value] of Object.entries(attrs)) {
                            src.searchParams.set(key, value);
                        }
                        frame.src = src.toString();
                        frame.setAttribute('width', '100%');
                        frame.setAttribute('height', '552');
                        frame.setAttribute('frameborder', '0');
                        frame.setAttribute('style', 'border: 1px solid #e0e8ea;');
                        container.append(frame);

                        let widgetListener = window.addEventListener('message', (event) => {
                            if (pecomEcomm.widget.isLoadFail || !event.data.hasOwnProperty('pecDelivery')) {
                                return;
                            }
                            if (event.data.pecDelivery.hasOwnProperty('result')) {
                                if (pecomEcomm.widget.lock && event.data.pecDelivery.result.toAddress != pecomEcomm.widget.address) {
                                    pecomEcomm.widget.lock = false;
                                    return;
                                }
                                try {
                                    event.data.pecDelivery.result.toAddress =
                                        event.data.pecDelivery.result.toDepartmentData.Addresses[0].address.RawAddress
                                        ?? event.data.pecDelivery.result.toAddress;
                                } catch (e) {}
                                pecomEcomm.callbackFunction(event.data.pecDelivery.result);
                            }
                            if (event.data.pecDelivery.hasOwnProperty('error')) {
                                pecomEcomm.widget.lock = false;
                                pecomEcomm.callbackError(event.data.pecDelivery.error);
                            }
                        });
                    })();
                </script>
                <input type="hidden" id="pec_address_txt" name="pec_address_txt">
                <input type="hidden" id="pec_price_txt" name="pec_price_txt">
                <input type="hidden" id="pec_days" name="pec_days">
                <input type="hidden" id="pec_days_txt" name="pec_days_txt">
                <input type="hidden" id="pec_to_address" name="pec_to_address">
                <input type="hidden" id="pec_to_uid" name="pec_to_uid">
                <input type="hidden" id="pec_last_select_to_uid" name="pec_last_select_to_uid">
                <input type="hidden" id="pec_to_type" name="pec_to_type">
                <input type="hidden" id="pec_widget_data" name="pec_widget_data">
                <input type="hidden" id="pec_address_selected" name="pec_address_selected">
                <input type="hidden" id="pec_cost-delivery_error" name="pec_cost-delivery_error">
                <input type="hidden" id="pec_cost_out" name="pec_cost_out">
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_ADDRESS')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" name="pec_address" id="pec_address_1">
                            <textarea name="pec_address" id="pec_address" style="width: 300px; height: 47px;" disabled required><?=$order->getPropertyCollection()->getAddress()->getValue()?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr class="heading">
            <td colspan="2">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_SUMMARY')?>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DELIV_ORDER')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input id="old_delivery_price" name="OLD_DELIVERY_PRICE" disabled value="<?=$shipment->getPrice()?>" required type="text">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        <tr>
            <td class="adm-detail-valign-top adm-detail-content-cell-l" width="40%">
                <?=GetMessage('PEC_DELIVERY_FORM_LBL_DELIV_NEW')?>
            </td>
            <td class="adm-detail-content-cell-r" width="60%">
                <table cellpadding="0" cellspacing="0" border="0" class="nopadding" width="100%">
                    <tbody>
                    <tr>
                        <td>
                            <input type="hidden" id="pec_price_1" name="pec_price">
                            <input id="pec_price" name="pec_price" disabled value="<?=$shipment->getPrice()?>" required type="text">
                        </td>
                    </tr>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>
    <input name="action" value="save" type="hidden" required>
</form>
