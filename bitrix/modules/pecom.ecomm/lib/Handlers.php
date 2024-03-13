<?php

namespace Pec\Delivery;

use Bitrix\Sale\Delivery;
use Bitrix\Main\Config\Option;
use Bitrix\Main\Loader;
use Bitrix\Sale\Order;
use Bitrix\Sale\Shipment;
use CSaleOrderProps;
use CUtil;
use Pecom\Ecomm\Widget\Helper;
use Throwable;

class Handlers
{
	private static $ORDER_ID;

    public static function prepare($order, &$arUserResult, $request, &$arParams, &$arResult)
	{
        try {
            require_once('Request.php');
            $req = new Request();
            $result = $req->checkAuth();
        } catch (Throwable $throwable) {}
		if (!isset($result['error'])) {
			$pathToWidjet = '/bitrix/js/' . Tools::$MODULE_ID . '/pec_widget.js';
            $GLOBALS['APPLICATION']->AddHeadScript($pathToWidjet);
            foreach ($arResult['JS_DATA']['DELIVERY'] as $deliveryId => $arDelivery) {
                $service = Delivery\Services\Manager::getById($deliveryId);

                if ($service['CLASS_NAME'] === '\Sale\Handlers\Delivery\PecomEcommHandler') {
                    /** @var Order $order */
                    /** @var Shipment $shipment */
                    foreach ($order->getShipmentCollection()->getNotSystemItems() as $shipment) {
                        $_SESSION['pec_post']['arParams'] = Helper::getConfigByShipment($shipment);
                        break;
                    }
                }
                $price = 0;
                $_SESSION['pec_post']['price'] = $price;
                if ($arDelivery['CHECKED'] == 'Y') {
                    if ($service['CLASS_NAME'] === '\Sale\Handlers\Delivery\PecomEcommHandler') {

                        $pecomEcommId = $deliveryId;
                        $arDelivery = $arResult['JS_DATA']['DELIVERY'][$pecomEcommId];
                        $arDelivery['IS_PEC_DELIVERY'] = 1;

                        if ($_SESSION['pec_post']['price'] && $_SESSION['pec_post']['cost_error'] !== true) {
                            $_SESSION['pec_post']['price'] += ($_SESSION['MAIN']['marginType'] == '%') ? $_SESSION['pec_post']['price'] * $_SESSION['MAIN']['marginValue'] / 100 : $_SESSION['MAIN']['marginValue'];
                            $arDelivery['PRICE_FORMATED'] = SaleFormatCurrency($_SESSION['pec_post']['price'], $arDelivery["CURRENCY"]);
                        }
                        if ($_SESSION['pec_post']['days'] && $_SESSION['pec_post']['cost_error'] !== true) {
                            $arDelivery['PERIOD_TEXT'] = GetMessage("PEC_DELIVERY_DAYS_PREFIX") . $_SESSION['pec_post']['days'];
                        }

                        $arResult['JS_DATA']['DELIVERY'][$pecomEcommId] = $arDelivery;

                        if ($arDelivery['CHECKED'] == 'Y') {
                            foreach ($arResult['JS_DATA']['ORDER_PROP']['properties'] as $key => $arProp) {
                                if ($arProp['CODE'] == 'ADDRESS' && $_REQUEST['order']['pec_to_address']) {
                                    $arResult['JS_DATA']['ORDER_PROP']['properties'][$key]['VALUE'][0] = $_REQUEST['order']['pec_to_address'];
                                }
                                if ($arProp['IS_PHONE'] == 'Y') {
                                    $arResult['JS_DATA']['ORDER_PROP']['properties'][$key]['REQUIRED'] = 'Y';
                                }
                            }

                            echo '<script>pecomEcomm.isChecked = true;pecomEcomm.productPrice = '.$_SESSION['pec_post']['arParams']['PRICE'].';</script>';
                        } else {
                            echo '<script>pecomEcomm.isChecked = false;</script>';
                        }
                        break;
                    }
                }
                if ($service['CLASS_NAME'] === '\Sale\Handlers\Delivery\PecomEcommHandler') {
                    echo '<script>pecomEcomm.isChecked = true;pecomEcomm.productPrice = '.$_SESSION['pec_post']['arParams']['PRICE'].';</script>';
                }
            }
        }
	}

    public static function ajax(&$result)
	{
		$pecomEcommId = 8;
		$arDelivery = $result['JS_DATA']['DELIVERY'][$pecomEcommId];
		$arDelivery['DESCRIPTION'] = '<a href="javascript:void(0);" onclick="showPekPickup();">asdf</a> ' . $arDelivery['DESCRIPTION'];
		$arResult['JS_DATA']['DELIVERY'][$pecomEcommId] = $arDelivery;
	}

    public static function BeforeSaved(\Bitrix\Main\Event $event)//$order)
	{
		return;
	}

    public static function SaleSaved(\Bitrix\Main\Event  $event)
	{
		$order = $event->getParameter("ENTITY");
		$isNew = $event->getParameter("IS_NEW");

		if (!$isNew) return;

		$orderId = $order->getId();

		if ($_REQUEST['pec_widget_data']) {
			PecomEcommDb::AddNewOrder($orderId);
			$widgetData = serialize(json_decode($_REQUEST['pec_widget_data']));
			PecomEcommDb::AddOrderData($orderId, 'WIDGET', $widgetData);
			PecomEcommDb::AddOrderData($orderId, 'TRANSPORTATION_TYPE', Tools::getTransportTypeWidget());
			if (Tools::isOrderDeliveryPec($orderId)) {

				if (Option::get(Tools::$MODULE_ID, "PEC_ORDER_SEND") == 'C') {
					self::createOrders($orderId);
				}

				$orderData = json_decode($_REQUEST['pec_widget_data']);

				$price = $orderData->price;
				$marginConfig = $_SESSION['MAIN'];
				$price += ($marginConfig['marginType'] == '%') ? $price * $marginConfig['marginValue']/100 : $marginConfig['marginValue'];

				$address = $orderData->toAddressType == 'address' ? $orderData->toAddress : $orderData->toDepartmentData->Addresses[0]->address->RawAddress;
				if ($_SESSION['pec_post']['cost_error']) {
					$price = GetMessage("PEC_DELIVERY_H_ERROR_CODE");
				}
//				$toType = $_SESSION['pec_post']['to_type'] == 'address' ? GetMessage("PEC_DELIVERY_ADDRESS_TO_TYPE_1") : GetMessage("PEC_DELIVERY_ADDRESS_TO_TYPE_2") . $_SESSION['pec_post']['to_uid'] . ')';
				$value = GetMessage("PEC_DELIVERY_PRICE_PREFIX") . round($price, 0) . GetMessage("PEC_DELIVERY_CURRENCY")
					. GetMessage("PEC_DELIVERY_TERM_PREFIX") . $_SESSION['pec_post']['days']
					. GetMessage("PEC_DELIVERY_ADDRESS_PREFIX") . $address;

				self::savePECOrderProperty($orderId, $value, $address, $order->getPersonTypeId());
			}
			unset ($_SESSION['pec_post']);
		}
	}

	public static function savePECOrderProperty($orderId, $value, $address, $person) {
		if (!Loader::includeModule('sale')) {
            return false;
        }

        $optionNames = [
            0 => 'PEC_DELIVERY_ADDRESS',
            1 => 'PEC_DELIVERY_APARTMENT'
        ];

        foreach ($optionNames as $index => $optionName) {
            $delivery_option = Option::get(Tools::$MODULE_ID, $optionName);

            if ($delivery_option) {
                $options_prop = CSaleOrderProps::GetList(array(), array("CODE" => $delivery_option, "PERSON_TYPE_ID" => $person))->Fetch();
                $arFields = [
                    'ORDER_PROPS_ID' => $options_prop['ID'],
                    'ORDER_ID' => $orderId,
                    'VALUE' => trim(preg_split('# \| |, '.GetMessage('PEC_DELIVERY_FLAT_OFFICE').'#', $address)[$index]),
                    'NAME' => $options_prop['NAME'],
                    'CODE' => $delivery_option,
                ];
                if (empty($arFields['VALUE'])) {
                    $arFields['VALUE'] = '';
                }
                if (!\CSaleOrderPropsValue::Add($arFields)) {
                    $props = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $orderId, "ORDER_PROPS_ID" => $options_prop['ID']))->Fetch();
                    if ($props || !$props['VALUE']) {
                        \CSaleOrderPropsValue::Update($props['ID'], $arFields);
                    }
                }
            }

            $op = CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DELIVERY", "PERSON_TYPE_ID" => $person))->Fetch();
            $arPropFields = [
                'ORDER_PROPS_ID' => $op['ID'],
                'ORDER_ID' => $orderId,
                'VALUE' => $value
            ];

            $arPropFields['NAME'] = GetMessage("PEC_DELIVERY_H_MODULE_NAME");
            $arPropFields['CODE'] = 'PEC_DELIVERY';

            $add = \CSaleOrderPropsValue::Add($arPropFields);
            if (!$add) {
                $prop = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $orderId, "ORDER_PROPS_ID" => $op['ID']))->Fetch();
                if ($prop || !$prop['VALUE']) {
                    \CSaleOrderPropsValue::Update($prop['ID'], $arPropFields);
                }
            }
        }

        return true;
	}

    public static function OnSaleStatusOrder($id, $val) {
		if (Option::get(Tools::$MODULE_ID, "PEC_ORDER_SEND") == 'U') {
			if (Option::get(Tools::$MODULE_ID, "PEC_ORDER_CREATE") == $val) {
				if (Tools::isOrderDeliveryPec($id)) {
					Loader::includeModule('sale');
					$result = \CSaleOrder::GetByID($id);
					if (!$result['TRACKING_NUMBER']) {
						self::createOrders($id);
					}
				}
			}
		}
	}

	public static function onOrderAdmin($args)
	{
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $urlData);
		if ($url['path'] !== '/bitrix/admin/sale_order_archive_view.php') {
			$id = !empty($args['ORDER']) ? $args['ORDER']->getId() : 0;

			if (!$id || !Tools::isOrderDeliveryPec($id)) {
				return [];
			}

			self::$ORDER_ID = $id;

			return [
				"BLOCKSET" => "Pec\Delivery\Handlers",
				"getScripts" => ["Pec\Delivery\Handlers", "mygetScripts"],
				"getBlocksBrief" => ["Pec\Delivery\Handlers", "mygetBlocksBrief"],
				"getBlockContent" => ["Pec\Delivery\Handlers", "mygetBlockContent"],
			];
		}
	}

	public static function mygetBlocksBrief($args)
	{
		return ['pecom_ecomm_admin' => ["TITLE" => GetMessage("PEC_DELIVERY_ORDER_TITLE")]];
	}

	public static function mygetScripts($args)
	{
		$pathToWidjet = '/bitrix/js/' . Tools::$MODULE_ID . '/admin-order.js';
		$GLOBALS['APPLICATION']->AddHeadScript($pathToWidjet);
	}

	public static function mygetBlockContent($blockCode, $selectedTab, $args)
	{
		$result = '';
		$orderId = self::$ORDER_ID;

		if ($selectedTab == 'tab_order') {
			if ($blockCode == 'pecom_ecomm_admin') {
				$pecId = Tools::getPecIndexByOrderId($orderId);
				$pickUpDate = Tools::getPickUpDate($orderId);
				$countPositions = Tools::getPecPositionCount($orderId);
				$status = [];
				if ($pecId) {
					$status = Tools::getPecStatusSaved($orderId);
				}
				if (0 && $pecId && !$status) {
					$status = Tools::getAndSavePecStatus($orderId, $pecId);
				}
				$transportType = Tools::getOrderPecTransportType($orderId);
				$result = '
                    <p id="pec-delivery__api-error" style="display: none;color: red;"></p>
                    <label>'.GetMessage("PEC_DELIVERY_PEC_INDEX").'<input id="pec-delivery__pec-id" type="text" value="' . $pecId . '" disabled> </label>
                    <label>'.GetMessage("PEC_DELIVERY_PICKUP_DATE").'
                        <input id="pec-delivery__pec_pickup_date" type="date" min="' . date('Y-m-d', strtotime("+1 day")) . '" value="' . $pickUpDate . '" ' . ($pecId ? 'disabled' : '') . ' style="font-size: 13px;height: 25px;padding: 0 5px;margin: 0;background: #fff;border: 1px solid;border-color: #87919c #959ea9 #9ea7b1 #959ea9;border-radius: 4px;color: #000;box-shadow: 0 1px 0 0 rgba(255,255,255,0.3), inset 0 2px 2px -1px rgba(180,188,191,0.7);display: inline-block;outline: none;vertical-align: middle;-webkit-font-smoothing: antialiased;width: 125px;">
                    </label>
                    <label>'.GetMessage("PEC_DELIVERY_SEATS_NUMBER").'
                        <input id="pec-delivery__pec-count-positions" type="number" value="' . $countPositions . '" ' . ($pecId ? 'disabled' : '') . ' style="font-size: 13px;height: 25px;padding: 0 5px;margin: 0;background: #fff;border: 1px solid;border-color: #87919c #959ea9 #9ea7b1 #959ea9;border-radius: 4px;color: #000;box-shadow: 0 1px 0 0 rgba(255,255,255,0.3), inset 0 2px 2px -1px rgba(180,188,191,0.7);display: inline-block;outline: none;vertical-align: middle;-webkit-font-smoothing: antialiased;width: 50px;">
                    </label>
                    <label>'.GetMessage("PEC_DELIVERY_TYPE").' 
                        <select id="pec-delivery__transport-type"' . ($pecId ? 'disabled' : '') . '>
                            <option value="auto" ' . ($transportType == 'auto' ? 'selected' : '') . '>'.GetMessage("PEC_DELIVERY_TYPE_1").' </option>
                            <option value="avia" ' . ($transportType == 'avia' ? 'selected' : '') . '>'.GetMessage("PEC_DELIVERY_TYPE_2").' </option>
                            <option value="easyway" ' . ($transportType == 'easyway' ? 'selected' : '') . '>'.GetMessage("PEC_DELIVERY_TYPE_3").' </option>
                        </select>
                    </label>
                    <label>'.GetMessage("PEC_DELIVERY_STATUS").' <input id="pec-delivery__pec-status" type="text" title="' . $status['name'] . '" value="' . $status['name'] . '" disabled style="width:'.strlen($status['name'])*7 .'px; min-width: 150px"> </label>
                    <input class="pec-delivery__change-pec-id" type="button" value="'.GetMessage("PEC_DELIVERY_CHANGE_CODE").'">
                    <br><br>
                    <input id="pec-delivery__get-status" type="button" value="'.GetMessage("PEC_DELIVERY_GET_STATUS").'">
                    <input id="pec-delivery__print-tag" type="button" value="'.GetMessage("PEC_DELIVERY_PRINT_LABEL").'">
                    <input id="pec-delivery__cancel-order" type="button" value="'.GetMessage('PEC_DELIVERY_CANCEL_ORDER').'">
                    <input id="pec-delivery__send-order" type="button" value="'.GetMessage("PEC_DELIVERY_APPLY_ORDER").'">
                    <input id="pec-delivery__pre-registration" type="button" value="'.GetMessage("PEC_DELIVERY_PRE_REGISTRATION").'">
                    ';
			}
		}

		return $result;
	}

	public static function createOrders($orderId) {
		$addressType = Option::get(Tools::$MODULE_ID, "PEC_STORE_PZZ");
		$pickupDate = date('Y-m-d', strtotime("+1 day"));
		$transportType = Tools::getOrderPecTransportType($orderId);
		$positionCount = 1;

		if ($addressType == 'store') {
			return Tools::pickupSubmit($orderId, $positionCount, $pickupDate, $transportType);
		} else if ($addressType == 'pzz') {
			return Tools::preRegistration($orderId, $positionCount, $transportType);
		}
	}

	public static function OrderUpdate($id, $arFields) {
		if (isset($arFields['DELIVERY_ID']) && $arFields['DELIVERY_ID'] > 0) {
			Loader::includeModule('sale');
			$res = \Bitrix\Sale\Delivery\Services\Manager::getById($arFields['DELIVERY_ID']);
			if ($res['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomEcommHandler') {
				$result = unserialize(PecomEcommDb::GetOrderData($id, 'WIDGET'));
				$price = $result->price;
				$price += ($_SESSION['MAIN']['marginType'] == '%') ? $price * $_SESSION['MAIN']['marginValue'] / 100 : $_SESSION['MAIN']['marginValue'];
				$address = $result->toAddressType == 'address' ? $result->toAddress : $result->toDepartmentData->Addresses[0]->address->RawAddress;
				$toType = $result->toAddressType == 'address' ? GetMessage("PEC_DELIVERY_ADDRESS_TO_TYPE_1") : GetMessage("PEC_DELIVERY_ADDRESS_TO_TYPE_2") . $result->toDepartmentData->UID . ')';
				$value = GetMessage("PEC_DELIVERY_PRICE_PREFIX") . round($price, 0) . GetMessage("PEC_DELIVERY_CURRENCY")
					. GetMessage("PEC_DELIVERY_TERM_PREFIX") . $result->term->days . GetMessage("PEC_DELIVERY_TERM")
					. GetMessage("PEC_DELIVERY_ADDRESS_PREFIX") . $address . ' (' . $toType . ')';

				$order = \Bitrix\Sale\Order::load($id);
				self::savePECOrderProperty($id, $value, $address, $order->getPersonTypeId());
			} else {
				$props = \CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DELIVERY"))->Fetch();
				$prop = \CSaleOrderPropsValue::GetList(array(), array("ORDER_ID" => $id, "ORDER_PROPS_ID" => $props['ID']))->Fetch();
				$arPropFields = [
					'VALUE_ORIG' => '',
					'VALUE' => ''
				];
				if ($prop) {
					\CSaleOrderPropsValue::Update($prop['ID'], $arPropFields);
				}
			}
		}
	}

	public static function onChangeDeliveryService() {
		$url = parse_url($_SERVER['REQUEST_URI']);
		parse_str($url['query'], $urlData);
		$orderId = $urlData['order_id'];

		if ($url['path'] === '/bitrix/admin/sale_order_shipment_edit.php' && $orderId > 0) {
			$deliveryID = Tools::getDeliveryID();
			$orders = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');
			$order = \Bitrix\Sale\Order::load($orderId);
			$price = $order->getPrice() - $order->getDeliveryPrice();
			$transportationType = Tools::getOrderTransportType($orderId);
			$deliveryAddress = $orders->toAddress;
			$params['SELF_PACK'] = Option::get(Tools::$MODULE_ID, "PEC_SELF_PACK", '0');

            $cargoesData = \Pec\Delivery\Tools::getTotalCargoesData($orderId);
            $weight = $cargoesData['weight'];
            $dimensions = array(
                    'LENGTH' => $cargoesData['length']*1000,
                    'WIDTH'  => $cargoesData['width']*1000,
                    'HEIGHT' => $cargoesData['height']*1000,
                    'VOLUME' => $cargoesData['volume']
                );

			$props = $order->getPropertyCollection();

			if (!$deliveryAddress) {
				$location = $props->getDeliveryLocation();
				$addressProp = $props->getAddress();
				$deliveryCity = \CSaleLocation::GetByID($location->getValue(), LANGUAGE_ID)['CITY_NAME'];
				$deliveryAddress = $deliveryCity;
				if ($addressProp->getValue())
					$deliveryAddress = $deliveryAddress .', '. $addressProp->getValue();
			}

			// If additional warehouses exists
			$locProp = $props->getDeliveryLocation();
			if($locProp) {
				$locationCode = $locProp->getValue();
				if ($locationCode != '') {
					$pec_locations = \CSaleLocation::GetList([], ["CODE" => $locationCode, "LID" => LANGUAGE_ID]);
					if ($pec_loc = $pec_locations->Fetch()) {
						$option_id = "PEC_STORE_DOP";
						$sklads = is_array(unserialize(Option::get(Tools::$MODULE_ID, $option_id))) ?
							unserialize(Option::get(Tools::$MODULE_ID, $option_id)) : [];
						if(!empty($sklads)) {
							foreach($sklads as $sklad) {
								$locs [$sklad['parent_id']]= [
									'address' => $sklad['address'],
									'intake' => $sklad['intake'],
								];
							}
						}
						if (in_array($pec_loc['CITY_ID'], array_keys($locs))) {
							$dop_address = $locs[$pec_loc['CITY_ID']]['address'];
							$dop_intake = $locs[$pec_loc['CITY_ID']]['intake'];
						} elseif (in_array($pec_loc['REGION_ID'], array_keys($locs))) {
							$dop_address = $locs[$pec_loc['REGION_ID']]['address'];
							$dop_intake = $locs[$pec_loc['REGION_ID']]['intake'];
						}
					}
				}
			}
			if($dop_address) {
				$params['FROM_ADDRESS'] = $dop_address;
				$params['FROM_TYPE'] = $dop_intake ? 'store' : 'pzz';
			} else {
				$params['FROM_ADDRESS'] = Option::get(Tools::$MODULE_ID, "PEC_STORE_ADDRESS", '');
				$params['FROM_TYPE'] = Option::get(Tools::$MODULE_ID, "PEC_STORE_PZZ", '');
			} ?>

            <script>
                let div = document.getElementById('DELIVERY_1');
                let params = <?=CUtil::PhpToJSObject(Tools::getDeliveryConfig())?>;
                let widgetUrl = <?=CUtil::PhpToJSObject(Tools::getWidgetUrl())?>;
                let deliveryID = <?=CUtil::PhpToJSObject($deliveryID)?>;
                let orderId = <?=CUtil::PhpToJSObject($orderId)?>;
                let order = <?=CUtil::PhpToJSObject($params)?>;
                let transportationType = <?=CUtil::PhpToJSObject($transportationType)?>;
                let deliveryAddress = <?=CUtil::PhpToJSObject($deliveryAddress)?>;
                let dimensions = <?=CUtil::PhpToJSObject($dimensions)?>;
                if (div.value === deliveryID) {
                    let id = document.getElementById('BLOCK_DELIVERY_SERVICE_1');
                    let price = <?=CUtil::PhpToJSObject($price)?>;
                    let weight = <?=CUtil::PhpToJSObject($weight)?>;
                    let fromType = order.FROM_TYPE === 'store' ? 1 : 0;

                    let addressType = 'department';
                    try {
                        addressType = $('#pec_to_type').val();
                    } catch (e) {}
                    if (!addressType) {
                        addressType = 'department';
                    }

                    let departmentUid = ''
                    try {
                        departmentUid = $('#pec_last_select_to_uid').val();
                        if (!departmentUid || departmentUid === '') {
                            departmentUid = $('#pec_to_uid').val();
                        }
                    } catch (e) {}
                    if (!departmentUid) {
                        departmentUid = '';
                    }

                    let deliveryParams;

                    if (addressType === 'address') {
                        deliveryParams = '&delivery=1&address-to=' + toAddress;
                        if (!!departmentUid) {
                            deliveryParams += '&department-to-uid=' + departmentUid;
                        }
                    } else if (!departmentUid) {
                        deliveryParams = '&delivery=0&address-to=' + toAddress;
                    } else {
                        deliveryParams = '&delivery=0&address-to=' + toAddress + '&department-to-uid=' + departmentUid;
                    }

                    let src = widgetUrl + '?' +
                        'address-from=' + order.FROM_ADDRESS +
                        '&intake=' + fromType +
                        deliveryParams +
                        '&weight=' + weight +
                        '&volume=' + dimensions.VOLUME +
                        '&declared-amount=' + price +
                        '&packing-rigid=' + order.SELF_PACK +
                        '&width=' + dimensions.WIDTH +
                        '&height=' + dimensions.HEIGHT +
                        '&length=' + dimensions.LENGTH +
                        '&transportation-type=' + transportationType +
                        '&auto-run=1' +
                        '&hide-price=1' +
                        '&hide-terms=1' +
                        '&margin_value=' + pecomEcomm.params.MAIN.marginValue +
                        '&margin_type=' + pecomEcomm.params.MAIN.marginType;
                    let iframe = `<tr><td colspan="2"><iframe id="pecWidjetOrig" src='${(src)}' width="100%" height="552" frameborder="0"></iframe></td></tr>`;
                    id.insertAdjacentHTML('afterend', iframe);
                }

                div.onchange = function() {
                    let id = document.getElementById('BLOCK_DELIVERY_SERVICE_1');
                    let price = <?=CUtil::PhpToJSObject($price)?>;
                    let weight = <?=CUtil::PhpToJSObject($weight)?>;
                    let fromType = order.FROM_TYPE === 'store' ? 1 : 0;

                    let addressType = 'department';
                    try {
                        addressType = $('#pec_to_type').val();
                    } catch (e) {}
                    if (!addressType) {
                        addressType = 'department';
                    }

                    let departmentUid = ''
                    try {
                        departmentUid = $('#pec_last_select_to_uid').val();
                        if (!departmentUid || departmentUid === '') {
                            departmentUid = $('#pec_to_uid').val();
                        }
                    } catch (e) {}
                    if (!departmentUid) {
                        departmentUid = '';
                    }

                    let deliveryParams;

                    if (addressType === 'address') {
                        deliveryParams = '&delivery=1&address-to=' + toAddress;
                        if (!!departmentUid) {
                            deliveryParams += '&department-to-uid=' + departmentUid;
                        }
                    } else if (!departmentUid) {
                        deliveryParams = '&delivery=0&address-to=' + toAddress;
                    } else {
                        deliveryParams = '&delivery=0&address-to=' + toAddress + '&department-to-uid=' + departmentUid;
                    }

                    if (this.value === deliveryID) {
                        let src = 'https://calc.pecom.ru/iframe/e-store-calculator?' +
                            'address-from=' + order.FROM_ADDRESS +
                            '&intake=' + fromType +
                            deliveryParams +
                            '&weight=' + weight +
                            '&volume=' + dimensions.VOLUME +
                            '&declared-amount=' + price +
                            '&packing-rigid=' + order.SELF_PACK +
                            '&width=' + dimensions.WIDTH/1000 +
                            '&height=' + dimensions.HEIGHT/1000 +
                            '&length=' + dimensions.LENGTH/1000 +
                            '&transportation-type=' + transportationType +
                            '&auto-run=1' +
                            '&hide-price=1' +
                            '&hide-terms=1' +
                            '&margin_value=' + pecomEcomm.params.MAIN.marginValue +
                            '&margin_type=' + pecomEcomm.params.MAIN.marginType;
                        console.log(src);
                        let iframe = `<tr><td colspan="2"><iframe id="pecWidjetOrig" src='${(src)}' width="100%" height="552" frameborder="0"></iframe></td></tr>`;
                        id.insertAdjacentHTML('afterend', iframe);
                    } else {
                        let elem = document.getElementById('pecWidjetOrig');
                        if (elem)
                            elem.remove();
                    }
                };

                let widgetListener = window.addEventListener('message', (event) => {
                    if (event.data.pecDelivery.hasOwnProperty('result')) {
                        let result = event.data.pecDelivery.result;
                        let price = Math.round(result.price);
                        price += params.MARGIN_TYPE === '%' ? price * params.MARGIN_VALUE/100 : parseInt(params.MARGIN_VALUE, 10)
                        let input = document.getElementById('PRICE_DELIVERY_1');
                        document.getElementById('BASE_PRICE_DELIVERY_1').value = price;
                        input.value = price;
                        let fields = {
                            "method": 'saveProperty',
                            "pec_widget_data": JSON.stringify(result),
                            "orderId": orderId,
                            sessid: BX.bitrix_sessid()
                        };

                        BX.ajax({
                            url: '/bitrix/js/pecom.ecomm/ajax.php',
                            data: fields,
                            method: 'POST',
                            dataType: 'json',
                            timeout: 30,
                            async: false,
                            onsuccess: function(data){
                                console.log(data)
                            }
                        });
                    }
                });
            </script>
			<?php
		}
	}
}
