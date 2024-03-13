<?php
namespace Pec\Delivery;

use Bitrix\Main\Loader;
use Bitrix\Main\Config\Option;
use Bitrix\Main\ModuleManager;
use Bitrix\Sale\Delivery;
use Bitrix\Sale\Location\Admin\LocationHelper as Helper;
use Bitrix\Sale\Order;
use Pec\Delivery\PDF\TCPDF;
use CAgent;
use CUtil;
use CIBlockElement;
use Pecom\Ecomm\ORM\ShipmentPropsValueTable;

use Pecom\Delivery\Bitrix\Adapter\Cargoes;

IncludeModuleLangFile(__FILE__);

class Tools
{
	public static $MODULE_ID = 'pecom.ecomm';
	public static $SUBMIT_TRANSPORT_TYPE = ['auto' => 3, 'avia' => 1, 'easyway' => 12]; // ��� ������ (1 - �������������, 3 - ����� �����, 12 - EASYWAY) [Number]
	public static $PREREGISTRATION_TRANSPORT_TYPE = ['auto' => 3, 'avia' => 1, 'easyway' => 12]; // ��� ��������� (1 - �������������, 3 - �������������, 12 - EASYWAY) [Number]
    public static $ORDER_ID;

	public static function getStoreAddress()
	{
		Loader::includeModule('catalog');

		$location = Option::get('sale', 'location', '');
		$addressStreet = '';
		if ($location) {
			$resDb = \CSaleLocation::GetByID($location);
			$addressStreet = $resDb['CITY_NAME_LANG'] . ' ' . $resDb['COUNTRY_NAME_LANG'] . ' ';
		}

		$resStores = \CCatalogStore::GetList(['ID' => 'ASC'], [], false, false, ['ID', 'TITLE', 'ADDRESS']);
		while ($arStore = $resStores->Fetch()) {
			$addressStreet .= $arStore['ADDRESS'];
			break;
		}

		return $addressStreet;
	}

	public static function savePecId(int $orderId, string $pecId)
	{
		$pecId = trim($pecId);
		$labelSettings = Option::get(self::$MODULE_ID, 'PEC_SAVE_PDF', '');
		if ($labelSettings) {
			$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_RESPONSE');
			$barcodeOrder = $db->cargos[0]->barcode;

			if (!$barcodeOrder) return ['error' => GetMessage('PEC_DELIVERY_ERROR_NO_CARGO')];

			Loader::includeModule('sale');
			$order = \Bitrix\Sale\Order::load($orderId);
			$propertyCollection = $order->getPropertyCollection();

			$bayerName = '';
			if ($propItem = $propertyCollection->getProfileName()) {
				$bayerName = $propItem->getValue();
			}

			$widgetData = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');

			$style = array(
				'position' => '',
				'align' => 'L',
				'stretch' => false,
				'fitwidth' => true,
				'cellfitalign' => '',
				'border' => false,
				'hpadding' => 'auto',
				'vpadding' => 'auto',
				'fgcolor' => array(0,0,0),
				'bgcolor' => false,
				'text' => true,
				'font' => 'helvetica',
				'fontsize' => 10,
				'stretchtext' => 4
			);

			if ($widgetData->toAddressType == 'department')
				$address = $widgetData->toDepartmentData->Addresses[0]->address->RawAddress;
			else
				$address = $widgetData->toAddress;

			require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/pecom.ecomm/lib/tcpdf/tcpdf.php");

			$pdf = new \TCPDFPEC(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A6', true, 'UTF-8', false);

			$pdf->SetFont('DejaVuSans', '', 10);
			$pdf->setHeaderFont(Array('DejaVuSans', '', PDF_FONT_SIZE_MAIN));
			$pdf->SetHeaderData('', '', GetMessage('PEC_DELIVERY_MODULE_NAME'));
			$pdf->AddPage();

			$html = '<html>';
			$i = 0;
			foreach ($db->cargos[0]->positions as $item) {
				$i++;
				$html .= '<div>
                    <p style="margin-bottom: 0;">' . $i . GetMessage('PEC_DELIVERY_PDF_FROM') . count($db->cargos[0]->positions) . '</p>
                    <p style="text-align:center;font-size:16px;margin-top:0">' . $pecId . '</p>';
				$pdf->writeHTML($html, true, false, true, false, '');
				$pdf->write1DBarcode((string)$item->barcode, 'C128C', '10', '', '', 30, 0.7, $style, 'C');
				$html = '<p style="text-align: center; font-size: small;margin-top: 2px"></p>';
				$html .= '<table align="center" border="0"><tr><td>'.GetMessage('PEC_DELIVERY_PDF_SENDER').'</td><td style="text-align: right;">'.GetMessage('PEC_DELIVERY_PDF_RECEIVER').'</td></tr>';
				$html .= '<tr style="font-size:10px;"><td>' . self::getStoreTitle() . '<br>' . $widgetData->fromAddress . '</td>';
				$html .= '<td style="padding-left: 20px;text-align:right;">' . $bayerName . '<br>' . $address . '<br></td></tr>';
				$html .= '<tr></tr>';
				$html .= '<tr style="font-size:10px; text-align: center"><td colspan="2">'.GetMessage('PEC_DELIVERY_PDF_NUMBER'). $orderId . '</td></tr>';
				$html .= '</table>';
				$html .= '</div>';
			}
			$html .= '</html>';
			$pdf->writeHTML($html, true, false, true, false, '');

			$labelPath = Option::get(self::$MODULE_ID, 'PEC_SAVE_PDF_URL', '');
			$fileName = $_SERVER['DOCUMENT_ROOT'].$labelPath."/".$orderId.'.pdf';
			$pdf->Output($fileName, 'F');
		}

		PecomEcommDb::AddOrderData($orderId, 'PEC_ID', $pecId);
		\CSaleOrder::Update($orderId, array('TRACKING_NUMBER' => $pecId));

		self::getAndSavePecStatus($orderId, $pecId);
		return ['status' => 'success' ? 'success' : 'failed'];
	}

	public static function savePecStatus(int $orderId, array $pecStatus)
	{
		// \CModule::IncludeModule('sale');
		// $order = \Bitrix\Sale\Order::load($orderId);
		//
		// if (!$order) return ['status' => 'error' ? 'success' : 'error'];
		//
		// $propertyCollection = $order->getPropertyCollection();
		//
		// $result = 'error';
		//
		// foreach ($propertyCollection as $obProp) {
		//     $arProp = $obProp->getProperty();
		//     if ($arProp["CODE"] == 'PEC_DELIVERY_SERVICE') {
		//         $pecProps = $obProp->getValue();
		//         $pecProps['status'] = $pecStatus;
		//         $obProp->setValue($pecProps);
		//         $result = $obProp->save();
		//         break;
		//     }
		// }

		PecomEcommDb::AddOrderData($orderId, 'STATUS', serialize($pecStatus));

		return ['status' => 'success' ? 'success' : 'success'];
	}

	private static function changeShipStatusBySettingTable(int $orderId, array $pecStatus) {
		$pecStatusId = $pecStatus['code'];
		$optionPecApiAllowDelivery = array_filter(unserialize(Option::get(self::$MODULE_ID, 'PEC_API_ALLOW_DELIVERY', '')));
		$optionPecApiShipped = array_filter(unserialize(Option::get(self::$MODULE_ID, 'PEC_API_SHIPPED', '')));
		$optionPecApiStatusTable = array_filter(unserialize(Option::get(self::$MODULE_ID, 'PEC_API_STATUS_TABLE', '')));

		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);

		if (!$order) return;

		$shipmentmentCollection = $order->getShipmentCollection();
		foreach ($shipmentmentCollection as $shipment)
		{
			if (isset($optionPecApiAllowDelivery[$pecStatusId])) {
				if ($optionPecApiAllowDelivery[$pecStatusId] == 1) {
					$r = $shipment->allowDelivery();
				} else {
					$r = $shipment->disallowDelivery();
				}
				if (!$r->isSuccess())
				{
					var_dump($r->getErrorMessages());
				}
			}

			if (isset($optionPecApiShipped[$pecStatusId])) {
				if ($optionPecApiShipped[$pecStatusId] == 1) {
					$r = $shipment->setField('DEDUCTED', 'Y');
				} else {
					$r = $shipment->setField('DEDUCTED', 'N');
				}
				if (!$r->isSuccess())
				{
					var_dump($r->getErrorMessages());
				}
			}

			if (isset($optionPecApiStatusTable[$pecStatusId])) {
				$shipment->setField("STATUS_ID", $optionPecApiStatusTable[$pecStatusId]);
				if (!$r->isSuccess())
				{
					var_dump($r->getErrorMessages());
				}
			}
			break;
		}

		$r = $order->save();
		if (!$r->isSuccess())
		{
			var_dump($r->getErrorMessages());
		}
	}

	public static function getAndSavePecStatus($orderId, string $pecId)
	{
		require_once('Request.php');
		$request = new Request();
		$response = $request->getPecStatus([$pecId]);
		$name = '';
		$code = '';
		if ($response->cargos) {
			$name = $response->cargos[0]->info->cargoStatus;
			$code = $response->cargos[0]->info->cargoStatusId;
			$pecStatus = ['code' => $code, 'name' => $name];
			self::savePecStatus($orderId, $pecStatus);
			self::changeShipStatusBySettingTable($orderId, $pecStatus);
		} elseif ($response->error) {
			$name = $response->error->title;
			$code = 'error';
		}

		return ['code' => $code, 'name' => $name];
	}

	public static function getTag(int $orderId, string $pecId)
	{
		require_once 'vendor/autoload.php';
		$generator = new \Picqer\Barcode\BarcodeGeneratorPNG();

		require_once('Request.php');

		$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_RESPONSE');
		$barcodeOrder = $db->cargos[0]->barcode;
		$positions = [];
		foreach ($db->cargos[0]->positions as $item) {
			$positions[] = [
				'barcode' => $item->barcode,
				'src' => 'data:image/png;base64,' . base64_encode($generator->getBarcode($item->barcode,
						$generator::TYPE_CODE_128, 2, 50))
			];
		}

		if (!$barcodeOrder) return ['error' => GetMessage('PEC_DELIVERY_ERROR_NO_CARGO')];

		$pecId = self::getPecIndexByOrderId($orderId);

		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);
		$propertyCollection = $order->getPropertyCollection();
		$phone = '';
		if ($propItem = $propertyCollection->getPhone()) {
			$phone = $propItem->getValue();
		}
		$bayerName = '';
		if ($propItem = $propertyCollection->getProfileName()) {
			$bayerName = $propItem->getValue();
		}
		$bayerPerson = '';
		if ($propItem = $propertyCollection->getPayerName()) {
			$bayerPerson = $propItem->getValue();
		}
		$count = 0;
		foreach ($order->getBasket() as $item) {
			$itemQuantity = $item->getQuantity();
			$count += $itemQuantity;
		}
		$basket = $order->getBasket();
		$amount = $basket->getPrice();

		$widgetData = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');

		$insurance = Option::get('pecom.ecomm', "PEC_SAFE_PRICE", '1') ? GetMessage('PEC_DELIVERY_Y') : GetMessage('PEC_DELIVERY_N');
		$selfPac = Option::get('pecom.ecomm', "PEC_SELF_PACK", '1') ? GetMessage('PEC_DELIVERY_Y') : GetMessage('PEC_DELIVERY_N');
		$printLabel = Option::get('pecom.ecomm', "PEC_PRINT_LABEL", '1');
		if ($widgetData->toAddressType == 'department')
			$address = $widgetData->toDepartmentData->Addresses[0]->address->RawAddress;
		else
			$address = $widgetData->toAddress;

		$html = '
            <html>
                <head>
                    <style>
                        #table-cargo td {
                            width: 3%;
                            padding-bottom: 10px;
                        }
                    </style>
                </head>
                <body onload="window.print()" onafterprint="window.close()">';
		$html1 = '
                    <div style="position:absolute;top:1rem;right:2rem;">
                       <img src="data:image/png;base64,' . base64_encode($generator->getBarcode($barcodeOrder,
				$generator::TYPE_CODE_128, 2, 50)) . '"/>
                       <p style="text-align: center; font-size: small;margin-top: 2px">' . $barcodeOrder . ' </p>
                   </div>
                   <h3>'.GetMessage('PEC_DELIVERY_PRINT_NUMBER'). $orderId . '</h3>
                   <h3>'.GetMessage('PEC_DELIVERY_PRINT_CARGO').'</h3>
                   <table id="table-cargo" style="border: none">
                       <tr>
                        <td><b>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_CODE').'</b> ' . $pecId . '</td>
                        <td><b>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_POS').'</b> ' . $count . '</td>
                        <td><b>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_EST_COST').'</b> ' . $amount . '</td>
                       </tr>
                       <tr>
                        <td><b>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_INS').'</b> ' . $insurance . '</td>
                        <td><b>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_SELFPACK').'</b> ' . $selfPac . '</td>
                       </tr>
                   </table>
                   
                   <table id="table-address">
                   <tr>
                    <td style="padding-right: 50px;"><h3>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_SENDER').'</h3></td>
                    <td>
                        <span>' . self::getStoreTitle() . '</span><br>
                        <span>' . $widgetData->fromAddress . '</span>
                    </td>
                   </tr>
                   <tr>
                    <td><h3>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_RECEIVER').'</h3></td>
                    <td>
                        <span>' . $bayerName . '</span><br>
                        <span>' . $bayerPerson . '</span><br>
                        <span>' . $address . '</span><br>
                        <span>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_PHONE'). $phone . '</span>
                    </td>
                   </tr>
                   </table>
                   <br><br>
                   <p style="text-align: center">'.GetMessage('PEC_DELIVERY_PRINT_CARGO_LABELS').'</p>';
		$html2 = '<hr><div>';
		$i = 0;

		foreach ($positions as $item) {
			$i++;
			$html2 .= '<div style="width:252px;padding-right: 50px;float:left;page-break-inside:avoid"><p style="margin-bottom: 0;">' . $i . GetMessage('PEC_DELIVERY_PRINT_CARGO_FROM') . count($positions) . '</p><p style="text-align:center;font-size:1.7rem;margin-top:0">' . $pecId . '</p>';
			$html2 .= '<img src="data:image/png;base64,' . base64_encode($generator->getBarcode($item['barcode'],
					$generator::TYPE_CODE_128, 2, 50)) . '"/>
                <p style="text-align: center; font-size: small;margin-top: 2px"> ' . $item['barcode'] . ' </p>';
			$html2 .= '<table style="border:none;"><tr><td>'.GetMessage('PEC_DELIVERY_PRINT_CARGO_SENDER').'</td><td style="text-align: right;">'.GetMessage('PEC_DELIVERY_PRINT_CARGO_RECEIVER').'</td></tr>';
			$html2 .= '<tr style="font-size:0.8rem;"><td>' . self::getStoreTitle() . '<br>' . $widgetData->fromAddress . '</td>';
			$html2 .= '<td style="padding-left: 20px;text-align:right;">' . $bayerName . '<br>' . $address . '<br></td></tr>';
			$html2 .= '<tr></tr>';
			$html2 .= '<tr style="font-size:0.8rem; text-align: center">
                            <td colspan="2">'.GetMessage('PEC_DELIVERY_PRINT_CARGO_ORDER_NUM'). $orderId . '</td>
                      </tr>';
			$html2 .= '</table>';
			$html2 .= '</div>';
		}
		$html2 .= '</div>';
		if ($printLabel == '1')
			$html .= $html1.$html2;
		elseif ($printLabel == '2')
			$html .= $html1;
		elseif ($printLabel == '3')
			$html .= $html2;
		$html .= '
               </body>
            </html>';

		return [
			'src' => 'data:image/png;base64,' . base64_encode($generator->getBarcode($barcodeOrder,
					$generator::TYPE_CODE_128, 2, 50)),
			'barCode' => $barcodeOrder,
			'positionsBarcode' => $positions,
			'html' => $html
		];
	}

	public static function pickupNetworkSubmit(int $orderId, int $positionCount)
	{
		self::$ORDER_ID = $orderId;

		require_once('Request.php');
		$request = new Request();

		$orderItems = self::getOrderProducts();
		$items = [];
		foreach ($orderItems as $item) {
			$items[] = [
				'receiver'   => self::getReceiverData(),
				'cargo'      => [
					'transporting' => 1,
					// ��� ��������� (1 - ����, 2 - ����) [Number]
					'description'  => $item['name'],
					'orderNumber'  => $orderId,
					// "weight" => (float)$item['weight'], // ���, �� [Number], ���� �������������, ���� ������� ����� ������
					// "volume" => (float)$item['volume'], // �����, �3 [Number], ���� �������������, ���� ������� ����� ������
					// "width" => (float)$item['width'], // ������, � [Number], ���� �������������, ���� ������� ����� ������
					// "length" => (float)$item['length'], // �����, � [Number], ���� �������������, ���� ������� ����� ������
					// "height" => (float)$item['height'], // ������, � [Number], ���� �������������, ���� ������� ����� ������
					// "positionsCount" => $positionCount, //$item['quantity'] // ���������� ����, �� [Number],
					// ���� �������������, ���� ������� ����� ������
				],
				"conditions" => [
					"isOpenCar"          => false, // ���������� �������� ������ [Boolean]
					"isSideLoad"         => false, // ���������� ������� �������� [Boolean]
					"isDayByDay"         => false, // ��������� ����� ���� � ���� [Boolean]
					"isSpecialEquipment" => false, // ���������� ����������� ������������ [Boolean],
					// ���� �������������, ���� �� ������� ��������� ������ false
					"isUncovered"        => false, // ���������� ����������� [Boolean],
					// ���� �������������, ���� �� ������� ��������� ������ false
					"isFast"             => false, // ���������� ���������� ��������� [Boolean]
					"isLoading"          => false // ���������� �������� ������ ���ʻ [Boolean]
				],
				"services"   => [ // ������ [Object]
					"delivery" => [ // �������� [Object]
						"enabled" => true, // �������� �� ������ [Boolean]
						// "avisationDateTime" => "2013-04-02", // ���� �������� [DateTime], ���� �������������
						"address" => self::getDeliveryAddress(), // ����� �������� [String],
						// ���� �����������, ���� "enabled"=>true
					],

					"insurance" => [ // ��������� [Object]
						"enabled" => self::isInsurance(), // �������� �� ������ [Boolean]
						"cost"    => self::getInsurancePrice(), // ��������� ���������, ��� [Number],
						// ���� �����������, ���� "enabled"=>true
						"payer"   => ['type' => self::getDeliveryPayerType()],
					],

					"documentsReturning" => [ // ������� ���������� [Object]
						"enabled" => false, // �������� �� ������ [Boolean]
						"payer"   => ['type' => self::getDeliveryPayerType()],
					],

					"strapping" => [ // �������� ���������-������ [Object]
						"enabled" => false // �������� �� ������ [Boolean]
					],

					"sealing" => [ // ����������� [Object]
						"enabled"        => false, // �������� �� ������ [Boolean]
						"positionsCount" => $positionCount, // ���������� ���� ��� ����������� [Number]
						"payer"   => ['type' => self::getDeliveryPayerType()],
					],

					"hardPacking" => [ // �������� ������������������ �������� [Object]
						"enabled"        => self::isSafePac(), // �������� �� ������ [Boolean]
						"positionsCount" => $positionCount, //$item['quantity'], // ���������� ���� � ��� [Number]
						"payer"   => ['type' => self::getDeliveryPayerType()],
					],
				],

				"cashOnDelivery" => [
					"enabled"           => false,
					// �������� ������ ����������� �������
					"cashOnDeliverySum" => 456.26,
					// ����� ��������� ������ (����� ��) �����������, ���� �������� ������ �� [Number]
					"actualCost"        => 789.36,
					// ����������� ��������� ������, ����������� ��� �� [Number]
					"includeTES"        => false
					// �� ������ ������ ����������� �� ����� �� [Boolean]
				],
			];
		}

        $cargoesData = self::getTotalCargoesData($orderId);

		$data = [
			'common' => [
				'applicationDate'   => date('Y-m-d'),
				'responsiblePerson' => self::getStoreResponsiblePerson(),
				'description'       => self::getDescriptionProducts(),
                'cargoSourceSystemGUID' => '5ff31c58-2c7f-11eb-80ce-00155d4a0436',
                'refferalId' => Tools::getRefferalId(),
			],
			'sender' => [
				'city'          => self::getOrderSenderCity(),//'������ ������', //self::getStoreAddressOffice(),
				'title'         => self::getStoreTitle(),
				'person'        => self::getStoreResponsiblePerson(),
				'phone'         => self::getStorePhone(),
				'addressOffice' => self::getStoreAddressOffice(),
				'addressStock'  => self::getStoreAddressStock(),
			],
			'cargos' => [
				'common' => [
					'cargoTotals'       => [
						'volume'         => $cargoesData['volume'],
						'weight'         => $cargoesData['weight'],
						'maxDimension'   => $cargoesData['maxDimension'],
						'positionsCount' => $positionCount, //self::getOrderPositionsCount(),
					],
					"conditions"        => [
						"isOpenCar"          => false,
						"isSideLoad"         => false, // ���������� ������� �������� [Boolean]
						"isDayByDay"         => false, // ��������� ����� ���� � ���� [Boolean]
						"isSpecialEquipment" => false, // ���������� ����������� ������������ [Boolean],
						// ���� �������������, ���� �� ������� ��������� ������ false
						"isUncovered"        => false, // ���������� ����������� [Boolean],
						// ���� �������������, ���� �� ������� ��������� ������ false
						"isFast"             => false, // ���������� ���������� ��������� [Boolean]
						"isLoading"          => false // ���������� �������� ������ ���ʻ [Boolean]
					],
					"services"          => [ // ������ [Object]
						"pickUp"       => [ // ������ ������ ����� [Object],
							// ���� �������������, ���� ������ ���������� �� ��� ������
							// ��� ���� ������
							"payer"   => ['type' => self::getDeliveryPayerType()],
						],
						"transporting" => [ // ��������� [Object]
							// ���� �������������, ���� ������ ���������� �� ��� ������
							// ��� ���� ������
							"payer"   => ['type' => self::getDeliveryPayerType()],
						],
						"delivery"     => [ // ���������� �� ������ �������� [Object], ���� �������������
							"payer"   => ['type' => self::getDeliveryPayerType()],
						],
					],
					'typeClientBarcode' => 'EAN13',
                    'cargoSourceSystemGUID' => '5ff31c58-2c7f-11eb-80ce-00155d4a0436',
                    'refferalId' => Tools::getRefferalId(),
				],
				'items'  => $items,
			],
		];
		$response = $request->pickupNetworkSubmit($data);

		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_REQUEST', serialize($data));
		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_RESPONSE', serialize($response));

		if ($pecId = $response->cargos[0]->cargoCode) {
			self::savePecId($orderId, $pecId);
		}

		return $response;
	}

    /**
     * Returns order sender info
     * @return array
     */
    public static function getSender(): array
    {
        $result = [
            'inn'   => self::getStoreInn(),
            'fs'    => self::getStoreType(),
            'title' => self::getStoreTitle(),
            'phone' => self::getStorePhone(),
            'person' => self::getStoreResponsiblePerson(),
        ];
        if (self::isDeliveryFromAddress()) {
            $result['cityFullAddress'] = Option::get('pecom.ecomm', "PEC_STORE_ADDRESS");
        } else {
            $result['warehouseId'] = self::getIntakeWarehouseId();
        }
        return $result;
    }

	/**
	 *
	 * @param     $orderId
	 * @param int $positionCount
	 * @param string $pickupDate
	 * @param string $transportType
	 * @return mixed
	 */
	public static function pickupSubmit($orderId, int $positionCount, string $pickupDate, string $transportType = '')
	{
		self::$ORDER_ID = $orderId;

		require_once('Request.php');
		$request = new Request();

		$transportType = $transportType ? self::$SUBMIT_TRANSPORT_TYPE[$transportType] : self::getSubmitTransportType();

        $cargoesData = self::getTotalCargoesData($orderId);

		$data = [
			"common" => [ // ����� ������ [Object]
				"type"                   => $transportType,
				// ��� ������ (1 - �������������, 3 - ����� �����, 12 - EASYWAY) [Number]
				"applicationDate"        => $pickupDate, //"2012-02-25", ���� ���������� ������ [Date]
				"description"            => self::getDescriptionProducts(),
				// �������� ����� [String]
				"weight"                 => $cargoesData['weight'],
				// ��� �����, �� [Number]
				"volume"                 => $cargoesData['volume'],
				// ����� �����, �3 [Number]
				"positionsCount"         => $positionCount, //self::getOrderPositionsCount(),
				// ���������� ����, �� [Number]
				"width"                  => $cargoesData['width'],
				// ������, � [Number]
				"length"                 => $cargoesData['length'],
				// �����, � [Number]
				"height"                 => $cargoesData['height'],
				// ������, � [Number]
				"isFragile"              => false,
				// ������� ���� [Boolean]
				"isGlass"                => false,
				// ������ [Boolean]
				"isLiquid"               => false,
				// �������� [Boolean]
				"isOtherType"            => false,
				// ���� ������� ���� [Boolean]
				"isOtherTypeDescription" => null,
				// �������� ����� ������� ���� [String],
				// ���� �����������, ���� "isOtherType" => true
				"isOpenCar"              => false,
				// ���������� �������� ������ [Boolean]
				"isSideLoad"             => false,
				// ���������� ������� �������� [Boolean]
				"isSpecialEquipment"     => false,
				// ���������� ����������� ������������ [Boolean],
				// ���� �������������, ���� �� ������� ��������� ������ false
				"isUncovered"            => false,
				// ���������� ����������� [Boolean],
				// ���� �������������, ���� �� ������� ��������� ������ false
				"isDayByDay"             => false,
				// ��������� ����� ���� � ���� [Boolean]

				"whoRegisterApplication" => 1,
				// ������������� ����� ������� ��������� ������
				// (1 - �����������, 2 - ����������, 3 - ������ ����) [Number]
				"responsiblePerson"      => self::getStoreResponsiblePerson(),
				// ��� �������������� �� ���������� ������ [String]
				// "typeClientBarcode"      => "CODE128",
				// ��� �����-�����, ��������� ��� ���� ������ ������ [String]
				// ��� �����-���� ����� �������� ��������� ������ ��������
				// "clientPositionsBarcode" => [     // �����-���� ���� ����� [Array]
				//     "123654789", // �����-��� ������� [String]
				// ],
				"customerCorrelation"    => $orderId,
				// ������������ �������� ��� ������������� �� ������� ������� [String], ���� �������������
				'cargoSourceSystemGUID' => '5ff31c58-2c7f-11eb-80ce-00155d4a0436',
                'refferalId' => Tools::getRefferalId(),
			],

			"services" => [ // ������ [Object]
				"isHP"                    => self::isSafePac(),
				// ������������ �������� ������������������ �������� [Boolean]
				"isInsurance"             => self::isInsurance(),
				// �������������� ����������� ����� [Boolean]
				"isInsurancePrice"        => self::getInsurancePrice(),
				// ��������� ����� ��� �����������, ��� [Number]
				// ���� �����������, ���� "isInsurance" => true
				"isSealing"               => false,
				// ����������� ����� (������ �� 3 ��) [Boolean]
				"isSealingPositionsCount" => null,
				// ���������� ���� ��� ����������� [Number]
				// ���� �����������, ���� "isSealing" => true
				"isStrapping"             => false,
				// �������� ����� ���������?������ [Boolean]
				"isDocumentsReturn"       => false,
				// ������� ���������� [Boolean]
				"isLoading"               => false,
				// ���������� �������� ������ ���ʻ [Boolean]
				// "floor"                   => 8,
				// ���� � �������� ���������� ������� ����, ���� ������������� [Number]
				// "isElevator"              => true,
				// ���� ����, ���� ������������� [Boolean]
				// "carryingDistance"        => 150,
				// ������ ��������� �����, ���� ������������� [Number]
				// "email"                   => "example@example.com",
				// Email ��� ������������� ����������� [String], ���� �������������
				"cashOnDelivery"          => [ // ���������� ������ [Object]
					"enabled"                      => false,
					// �������� ������ ����������� ������� [Boolean], ���� �����������, ���� �������� ������ �� [Number]
					// "cashOnDeliverySum"            => 456.26,
					// // ����� ��������� ������ (����� ��) [Number]
					// "actualCost"                   => 789.36,
					// // ����������� ��������� ������ [Number]
					// "includeTES"                   => false,
					// // �� ������ ������ ���������� ����� ����� �� [Boolean]
					// "isPartialDistributionAllowed" => true,
					// // �������� ��������� ������ [Boolean], ���� �������������
					// "isOpenAndInspectAllowed"      => true,
					// // �������� �������� � ������������ ������ [Boolean], ���� �������������
					// "orderNumber"                  => "�23434-��",
					// // ����� ������ ������� [String], ���� �������������
					// "sellerINN"                    => "7716542310",
					// // ��� ����������� (��������) [String], ���� �������������
					// "sellerTitle"                  => "������������ �����������",
					// // ������������ ����������� (��������) [String], ���� �������������
					// "sellerPhone"                  => "88-99-00",
					// // ������� [String], ���� �������������
					// "sellerServices"               => [
					//     [ // �������������� ������ [Object]
					//         "type"            => 1,
					//         // ������ �������������� �����, ��������������� ���������������� [Number]
					//         // 1 - ��������,
					//         // 2 - ���������� ��������,
					//         // 3 - �������� � ������ �� ���������,
					//         // 4 - �������� � ������ �� ���,
					//         // 5 - ������ �� ����,
					//         // 6 - �������� ��������-��������,
					//         // 7 - ����������-������������ ������ �������� ��������
					//         "rateVAT"         => "���20",
					//         // ������ ��� [String]
					//         "sumIncludingVAT" => 68403.17,
					//         // ��������� �������������� �����, � �.�. ���, ���. [Number]
					//     ],
					// ],
					// "specification"                => [ // ��������� ������ ����� [Object]
					//     "takeDeliveryZeroSum"     => false,
					// // ����� ����� ��������� ��� ������ ������ ���������� [Boolean]
					// "amountDeliveryMandatory" => 300,
					// // ������������ ����� ��������. ����������� ���� takeDeliveryZeroSum = true [Number]
					// "specifications"          => [
					//     [ // ������ ������������
					//         "vendorCode"               => "32711600Y",
					//         // ������� [String]
					//         "title"                    => "����� AlcaPlast A75",
					//         // ������������ ������� [String]
					//         "amount"                   => 1,
					//         // ���������� [Number]
					//         "kit"                      => true,
					//         // �������� [Boolean]
					//         "rateVAT"                  => "20%",
					//         // ������ ��� [String]
					//         "actualCostPerUnit"        => 123.36,
					//         // ����������� �������� �� ��., � �.�. ���, ���. [Number]
					//         "sumPerUnit"               => 123.36,
					//         // � ������ � ��������������� �� ��., � �.�. ���, ���. [Number]
					//         "actualCostTotal"          => 123.36,
					//         // ����������� �������� �����, ���., � �.�. ��� [Number]
					//         "sumTotal"                 => 123.36,
					//         // � ������ � ��������������� �����, ���., � �.�. ���, ���. [Number]
					//         "fitting"                  => true,
					//         // �������� [Boolean]
					//         "openingIndividualPacking" => true
					//         // �������� ���. �������� [Boolean]
					//     ],
					// ],
					// ],
				],
			],

			"sender" => [ // ����������� [Object]
				"inn"                  => self::getStoreInn(), // ��� [String], ���� �������������
				// "city"                 => self::getOrderSenderCity(), // ����� [City]
				"title"                => self::getStoreTitle(), // ������������ [String]
				"person"               => self::getStoreResponsiblePerson(), // ���������� ���� [String]
				"phone"                => self::getStorePhone(), // ������� [String]
				// "phoneAdditional"      => "1234", // ���������� ����� (�������� 10 ��������) [String]
				// "email"                => "example@example.com", // E-mail [String], ���� �������������
				"addressOffice"        => self::getStoreAddress(), // ����� ����� [String]
				// "addressOfficeComment" => "����� �������", // ����������� � ������ ����� [String]
				"addressStock"         => self::getStoreAddressStock(), // ����� ������ [String]
				// "addressStockComment"  => "���� �� ������� �����", // ����������� � ������ ������ [String]
				// "latitudeForCar"       => 55.432025, // ���������� ��� ������ ������ [String]
				// "longitudeForCar"      => 37.545734, // ���������� ��� ������ ������ [String]
				// "workTimeFrom"         => "09 => 00", // ����� ������ �������� ��� [Time], ���� �������������
				// "workTimeTo"           => "18 => 00", // ����� ��������� �������� ��� [Time], ���� �������������
				// "lunchBreakFrom"       => "14 => 00", // ����� ������ ���������� �������� [Time], ���� �������������
				// "lunchBreakTo"         => "15 => 00", // ����� ��������� ���������� �������� [Time], ���� �������������
				// "cargoDocumentNumber"  => "��-419987234�", // ����� ����� �� ������ ����� ���������
				// ��� ������� ��������� �� ���� [String]
				"isAuthorityNeeded"    => false, // ��� ��������� ����� ���������� ������������ ���ʻ
				// (�����, ������������ �������) [Boolean]
				// "identityCard"         => [ // �������� �������������� ��������  [Object]
				//     "type"   => 10, // ��� ��������� [Number] (1 - ������� ������������ ����������,
				//     // 2 - ����������� �� ��������� ����������, 3 - ������������ �������������,
				//     // 4 - ��� �� ����������, 5 - ����������� �������, 6 - ������������� �������,
				//     // 7 - ��������� ������������� �������� ���������� ��,
				//     // 8 - ������������� � �������������� ���������� ������� �� ���������� ��,
				//     // 9 - ������� ������, 10 - ������� ���������� ��,
				//     // 11 - ������������� � ������������ ����������� � ��������� ��������,
				//     // 12 - �������  �����)
				//     "series" => "1234", // ����� [String]
				//     "number" => "56789", // ����� [String]
				//     "date"   => "1985-01-01", // ���� [DateTime]
				//     "note"   => "" // ��������� ���� ��� ������ ���������� [String]
				// ],
			],

			"receiver" => [ // ���������� [Object]
				// "inn"                                => "7716542310",
				// ��� [String], ���� �������������
				// "city"                               => self::getReceiverData()['city'],
				// ����� [City]
				"title"                              => self::getReceiverData()['title'] ? : self::getReceiverData()['person'],
				// ������������ [String]
				"person"                             => self::getReceiverData()['person'],
				// ���������� ���� [String]
				"phone"                              => self::getReceiverData()['phone'],
				// ������� [String]
				// "phoneAdditional"                    => "1234",
				// ���������� ����� (�������� 10 ��������) [String]
				// "email"                              => "test@test.com",
				// E-mail [String], ���� �������������
				"isCityDeliveryNeeded"               => self::isDeliveryToAddress(),
				// ���������� �������� �� ������ ���������� [Boolean]
				// "isLoading"                          => true,
				// ���������� ��������� ������ ���ʻ, ���� ������������� [Boolean]
				// "floor"                              => 15,
				// ���� �� ������� ���������� ������� ����, ���� ������������� [Number]
				// "isElevator"                         => false,
				// ���� ����, ���� ������������� [Boolean]
				// "carryingDistance"                   => 30,
				// ������ ��������� �����, ���� ������������� [Number]
				// "isCityDeliveryNeededAddress"        => self::getDeliveryAddress(),
				// ����� �������� ����� [String]
				// ���� �����������,
				// ���� "isCityDeliveryNeeded" => true
				// "isCityDeliveryNeededAddressComment" => "���� �� �����",
				// ����������� � ������ ��������
				// [String], �������������� ����
				// "avisationDateTime"                  => "2013-04-02",
				// ���� �������� [DateTime], ���� �������������
				// "dateOfDelivery"                     => "2013-04-02",
				// �������� ���� �������� [DateTime], ���� �������������
				"declaredCost"                       => self::getInsurancePrice(),
				// ����������� ��������� ������

				// "warehouseId"  => self::getDeliveryWarehouseId(),
				// ������������� ������ [String]
//                 "identityCard" => [ // �������� �������������� ��������  [Object]
//                     "type"   => 10, // ��� ��������� [Number] (
				//     // 0 - ��� �������������� ��������� (�����\����� �������� �������),
				//     // 1 - ������� ������������ ����������,
				//     // 2 - ����������� �� ��������� ����������, 3 - ������������ �������������,
				//     // 4 - ��� �� ����������, 5 - ����������� �������, 6 - ������������� �������,
				//     // 7 - ��������� ������������� �������� ���������� ��,
				//     // 8 - ������������� � �������������� ���������� ������� �� ���������� ��,
				//     // 9 - ������� ������, 10 - ������� ���������� ��,
				//     // 11 - ������������� � ������������ ����������� � ��������� ��������,
				//     // 12 - �������  �����)
//                     "series" => "1234", // ����� [String]
//                     "number" => "56789", // ����� [String]
//                     "date"   => "1985-01-01", // ���� [DateTime]
				//     "note"   => "" // ��������� ���� ��� ������ ���������� [String]
				// ],

			],

			"payments" => [ // ������ [Object]
                "isHP" => [
                    "type"        => self::getDeliveryPayerType(),
                ],
				"pickUp"    => [ // ������ ������ ����� [Object]
					"type"        => self::getDeliveryPayerType(),
					// ���������� (1 - �����������, 2 - ����������, 3 - ������ ����) [Number]
				//	"paymentCity" => self::getDeliveryPayerCity(),
					// ����� ������ �� ������ [City], ����������� ������ ��� type = 3 - ������ ����.
					// ��������� ���� �� �����������, �.�. ���������� �����������
				],
				"moving"    => [ // ������ ��������� [Object]
					"type"        => self::getDeliveryPayerType(),
				//	"paymentCity" => self::getDeliveryPayerCity(),
				],
				"insurance" => [ // ������ ����������� [Object],
					"type"        => self::getDeliveryPayerType(),
				//	"paymentCity" => self::getDeliveryPayerCity(),
				],
				"delivery"  => [ // ������ �������� �� ������ ���������� [Object],
					"type"        => self::getDeliveryPayerType(),
				//	"paymentCity" => self::getDeliveryPayerCity(),
					// "inn"          => "7716542310",
					// // ��� �������� ����,
					// // ���� ������������� [String]
					// "title"        => "��� \"�������� ����\"",
					// // ������������ �������� ���� [String],
					// // ���� �����������, ���� "type" => 3
					// "phone"        => "12-12-12",
					// // ������� �������� ���� [String],
					// // ���� �����������, ���� "type" => 3
					// "identityCard" => [ // �������� �������������� ��������  [Object]
					//     // ���� �����������, ���� "type" => 3
					//     "type"   => 10, // ��� ��������� [Number] (1 - ������� ������������ ����������,
					//     // 2 - ����������� �� ��������� ����������, 3 - ������������ �������������,
					//     // 4 - ��� �� ����������, 5 - ����������� �������, 6 - ������������� �������,
					//     // 7 - ��������� ������������� �������� ���������� ��,
					//     // 8 - ������������� � �������������� ���������� ������� �� ���������� ��,
					//     // 9 - ������� ������, 10 - ������� ���������� ��,
					//     // 11 - ������������� � ������������ ����������� � ��������� ��������,
					//     // 12 - �������  �����)
					//     "series" => "1234", // ����� [String]
					//     "number" => "56789", // ����� [String]
					//     "date"   => "1985-01-01", // ���� [DateTime]
					//     "note"   => "" // ��������� ���� ��� ������ ���������� [String]
					// ],
				],
			],
		];

		if (self::isDeliveryToAddress()) {
			$data['receiver']["isCityDeliveryNeededAddress"] = self::getDeliveryAddress();
		} else {
			$data['receiver']["warehouseId"] = self::getDeliveryWarehouseId();
		}

		if (self::getDocumentData($orderId)) {
			foreach (self::getDocumentData(self::$ORDER_ID) as $key => $item) {
				$data['receiver'][$key] = $item;
			}
		}

        if (self::isDeliveryToAddress()) {
            $data['receiver']['isCityDeliveryNeededAddress'] = trim(preg_split(
                '# \| |, '.GetMessage('PEC_DELIVERY_FLAT_OFFICE').'#',
                self::getDeliveryAddress()
            )[0]);

            $apartment = \CSaleOrderPropsValue::GetList([], [
                'ORDER_ID' => $orderId,
                'CODE' => Option::get(Tools::$MODULE_ID, 'PEC_DELIVERY_APARTMENT'),
            ])->Fetch()['VALUE'];

            if (!empty($apartment)) {
                if (!empty($data['receiver']['isCityDeliveryNeededAddressComment'])) {
                    $data['receiver']['isCityDeliveryNeededAddressComment'] = sprintf(
                        GetMessage('PEC_DELIVERY_FLAT_OFFICE_2').' %s; %s',
                        $apartment,
                        $data['receiver']['isCityDeliveryNeededAddressComment']
                    );
                } else {
                    $data['receiver']['isCityDeliveryNeededAddressComment'] = GetMessage('PEC_DELIVERY_FLAT_OFFICE_2').' ' . $apartment;
                }
            }
        }

		$response = $request->pickupSubmit($data);

		$deltaDays = 0;
		while ($response->error) {
			if ($deltaDays == 5)
				break;
			$deltaDays++;
			if ($response->error->fields[0]->Key == 'common.applicationDate') {
				$data['common']['applicationDate'] = date('Y-m-d', strtotime($data['common']['applicationDate'] . "+1 days"));
				$response = $request->pickupSubmit($data);
			}
		}
		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_REQUEST', serialize($data));
		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_RESPONSE', serialize($response));

		if ($pecId = $response->cargos[0]->cargoCode) {
			self::savePecId($orderId, $pecId);
		}

		return $response;
	}

	/**
	 *
	 * @param     $orderId
	 * @param int $positionCount
	 * @param string $transportType
	 * @return mixed
	 */
	public static function preRegistration($orderId, int $positionCount, string $transportType = '')
	{
		self::$ORDER_ID = $orderId;

		require_once('Request.php');
		$request = new Request();

		$transportType = $transportType ? self::$PREREGISTRATION_TRANSPORT_TYPE[$transportType] : self::getPreregistrationTransportType();

        $cargoesData = self::getTotalCargoesData($orderId);

		$data = [
			'sender' => self::getSender(),
			'cargos' => [
				[
					'common'   => [
                        "weight"                 => $cargoesData['weight'],
                        "volume"                 => $cargoesData['volume'],
                        "width"                  => $cargoesData['width'],
                        "length"                 => $cargoesData['length'],
                        "height"                 => $cargoesData['height'],
						'type'                  => $transportType, // ��� ��������� (1 - �������������, 3 - �������������, 12 - EASYWAY) [Number]
						'declaredCost'          => self::getInsurancePrice(), // ����������� ��������� ������ [Number]
						'description'           => self::getDescriptionProducts(), // �������� ����� [String]
						'orderNumber'           => $orderId, // ����� ������ ������� [String], ���� �������������
						'accompanyingDocuments' => false, // ���� �������� ���������������� ���������� [Boolean]
						'positionsCount'        => $positionCount, //self::getOrderPositionsCount(), // ���������� ���� [Number]
						'cargoSourceSystemGUID' => '5ff31c58-2c7f-11eb-80ce-00155d4a0436',
                        'refferalId' => Tools::getRefferalId(),
					],
					'receiver' => self::getReceiverData(),
					'services' => [
						'hardPacking'        => [
						        'enabled' => self::isSafePac(),
                                "payer"   => ['type' => self::getDeliveryPayerType()],
                        ],
						'delivery'           => [
							'enabled' => self::isDeliveryToAddress(),
							"payer"   => [
								'type' => self::getDeliveryPayerType(),
							//	"paymentCity" => self::getDeliveryPayerCity(),
							],
						],
						'transporting'       => [
							"payer"   => ['type' => self::getDeliveryPayerType()],
						],
						"insurance"          => [ // ��������� [Object]
							"enabled" => self::isInsurance(), // �������� �� ������ [Boolean]
							"cost"    => self::getInsurancePrice(), // ��������� ���������, ��� [Number],
							// ���� �����������, ���� "enabled"=>true
							"payer"   => ['type' => self::getDeliveryPayerType()],
						],
						'sealing'            => ['enabled' => false],
						'strapping'          => ['enabled' => false],
						'documentsReturning' => ['enabled' => false],
					],
				],
			],
            'common' => [
                'cargoSourceSystemGUID' => '5ff31c58-2c7f-11eb-80ce-00155d4a0436',
                'refferalId' => Tools::getRefferalId(),
            ]
		];

        if (self::isDeliveryToAddress()) {
            $data['receiver']['addressStock'] = \CSaleOrderPropsValue::GetList([], [
                "ORDER_ID" => self::$ORDER_ID,
                "CODE" => 'ADDRESS',
            ])->Fetch()['VALUE'];
            $data['receiver']['addressStock'] = trim(preg_split('# \| |, '.GetMessage('PEC_DELIVERY_FLAT_OFFICE').'#', $data['receiver']['addressStock'])[0]);

            $apartment = \CSaleOrderPropsValue::GetList([], [
                "ORDER_ID" => $orderId,
                "CODE" => Option::get(Tools::$MODULE_ID, 'PEC_DELIVERY_APARTMENT'),
            ])->Fetch()['VALUE'];

            if (!empty($apartment)) {
                if (!empty($data['receiver']['comment'])) {
                    $data['receiver']['comment'] = sprintf(
                        GetMessage('PEC_DELIVERY_FLAT_OFFICE_2').' %s; %s',
                        $apartment,
                        $data['receiver']['comment']
                    );
                } else {
                    $data['receiver']['comment'] = GetMessage('PEC_DELIVERY_FLAT_OFFICE_2').' ' . $apartment;
                }
            }
        }

		$response = $request->preRegistration($data);

		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_REQUEST', serialize($data));
		PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_RESPONSE', serialize($response));

		if ($pecId = $response->cargos[0]->cargoCode) {
			self::savePecId($orderId, $pecId);
		}

		return $response;
	}

	public static function getPecIndexByOrderId($orderId)
	{
		$pecId = PecomEcommDb::GetOrderData($orderId, 'PEC_ID');

		return $pecId;
	}

	public static function getPickUpDate($orderId) {
		$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_REQUEST');
		$pickUpDate = $db['common']['applicationDate'];

		if (!$pickUpDate) {
			$pickUpDate = date('Y-m-d', strtotime("+1 day"));
		}
		return $pickUpDate;
	}

	public static function getPecPositionCount($orderId) {
		$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_REQUEST');
		$positionsCount = $db['common']['positionsCount'];
		if (!$positionsCount) {
			$positionsCount = $db['common']['positionsCount'];
		}
		if (!$positionsCount) {
			$positionsCount = 1;
		}
		return $positionsCount;
	}

	public static function isPecIdSetByApi($orderId) {
		$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_RESPONSE');
		return isset($db->cargos[0]->cargoCode);
	}

	public static function getPecStatusSaved($orderId)
	{
		$pecStatus = PecomEcommDb::GetOrderDataArray($orderId, 'STATUS');

		return $pecStatus;
	}

	public static function isOrderDeliveryPec($orderId)
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);

		$bCheckProps = false;
		if (!empty($order)) {
			$deliveryIds = $order->getDeliveryIdList();
			foreach ($deliveryIds as $deliveryId) {
				if ($deliveryId > 0) {
					$service = Delivery\Services\Manager::getById($deliveryId);
					if ($service['CLASS_NAME'] === '\Sale\Handlers\Delivery\PecomEcommHandler') {
						$bCheckProps = true;
						break;
					}
				}
			}
		}
		return $bCheckProps;
	}

    public static function cancelOrder($orderId, $pecId)
    {
        require_once('Request.php');
        $request = new Request();

        $data = [
            (string)$pecId,
        ];

        $response = $request->cancelOrder($data);

        PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_REQUEST', serialize($data));
        PecomEcommDb::AddOrderData($orderId, 'PEC_API_SUBMIT_RESPONSE', serialize($response));
        PecomEcommDb::AddOrderData($orderId, 'PEC_ID', '');

        $order = \Bitrix\Sale\Order::load($orderId);
        $ShipmentCollection = $order->getShipmentCollection();
        foreach ($ShipmentCollection as $ship) {
            if (!$ship->isSystem()){
                $ship->setFields(array(
                    'TRACKING_NUMBER' => ''
                ));
            }
        }
        $order->save();

        if ($pecId = $response[0]->success) {
            self::savePecId($orderId, '');
        }

        return [
            'error' => !$response[0]->success,
            'success' => $response[0]->success,
            'result' => [
                'description' => $response[0]->description,
            ]
        ];
    }

    private static function validateOrderPecProps($pecProps)
	{
		if (!isset($pecProps[0])) {
			$pecProps[0] = '-';
		}
		if (!isset($pecProps[1])) {
			$pecProps[1] = '-';
		}

		return $pecProps;
	}

	private static function getStoreSettings()
	{
		$storeSetting = unserialize(Option::get('sale', 'reports', ''));
		$result = [
			'inn'   => $storeSetting['INN']['VALUE'],
			'title' => $storeSetting['COMPANY_NAME']['VALUE'],
		];

		return $result;
	}

	public static function getDeliveryPayerType()
	{
		$type = Option::get('pecom.ecomm', "PEC_COST_OUT", '1') ? 2 : 1;

		return $type;
	}

	public static function getDeliveryPayerCity()
	{
		if (Option::get('pecom.ecomm', "PEC_COST_OUT", '1')) {
			return self::getDeliveryCity();
		} else {
			return self::getStoreCity();
		}
	}

	private static function getStoreTitle()
	{
		return Option::get(self::$MODULE_ID, "PEC_STORE_TITLE", '');
	}

	private static function getStoreInn()
	{
		return Option::get(self::$MODULE_ID, "PEC_STORE_INN", '0');
	}

	private static function getStoreType()
	{
		return Option::get(self::$MODULE_ID, "PEC_STORE_TYPE", '');
	}

	public static function getStoreResponsiblePerson()
	{
		return Option::get(self::$MODULE_ID, "PEC_STORE_PERSON", GetMessage('PEC_DELIVERY_TOOLS_PEC_STORE_PERSON'));
	}

	public static function getStorePhone()
	{
		return Option::get(self::$MODULE_ID, "PEC_STORE_PHONE", '+7 490');
	}

	public static function getStoreCity()
	{
		return self::getOrderSenderCity();
	}

	public static function getStoreAddressOffice()
	{
		return Option::get('pecom.ecomm', "PEC_STORE_ADDRESS", '');
	}

	public static function getStoreAddressStock()
	{
		return Option::get('pecom.ecomm', "PEC_STORE_ADDRESS", '');
	}

	public static function getDescriptionProducts()
	{
		return GetMessage('PEC_DELIVERY_GOODS_DESCR');
	}

	public static function isSafePac()
	{
        $order = Order::load(static::$ORDER_ID);

        if (Option::get('pecom.ecomm', "PEC_SELF_PACK", 0)) {
            return true;
        }

        $self_pack_prop = Option::get('pecom.ecomm', "PEC_SELF_PACK_INPUT", '');
        if (empty($self_pack_prop)) {
            return false;
        }

        foreach ($order->getBasket() as $item) {
            $product_id = $item->getField('PRODUCT_ID');
            $fragile = CIBlockElement::GetByID($product_id)->GetNextElement()->GetProperties()[$self_pack_prop]['VALUE'];
            if ($fragile === 'Y') {
                return true;
            }
        }

        return false;
	}

	public static function isInsurance()
	{
		return Option::get('pecom.ecomm', "PEC_SAFE_PRICE", '') ? true : false;
	}

	public static function getInsurancePrice()
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load(self::$ORDER_ID);
		$basket = $order->getBasket();

		return $basket->getPrice();
	}

	public static function getReceiverData()
	{
		$result = [
		//	'city'        => self::getDeliveryCity(),
			'title'       => self::getOrderReceiverTitle(),
			'person'      => self::getOrderReceiverName(),
			'phone'       => self::getOrderReceiverPhone(),
		];

		if (self::isDeliveryToAddress()) {
			$result['addressStock'] = self::getDeliveryAddress();
		} else {
			$result['warehouseId'] = self::getDeliveryWarehouseId();
		}

		if (self::getDocumentData(self::$ORDER_ID)) {
			foreach (self::getDocumentData(self::$ORDER_ID) as $key => $item) {
				$result[$key] = $item;
			}
		}


		return $result;
	}

    public static function getTotalCargoesData($orderId)
    {
        $shipmentData = self::getOrderShipmentData($orderId);

        $cargoesAdapter = new Cargoes();
        $cargoesAdapter->fromOrder($orderId);

        $totalWeight = (float)($cargoesAdapter->getCoreCargoes()->getTotalWeight()/1000); // To Kg

        $totalDims   = $cargoesAdapter->getCoreCargoes()->getTotalDimensions();
        $totalLength = $totalDims->getLength()/1000;  // To m
        $totalWidth  = $totalDims->getWidth()/1000;
        $totalHeight = $totalDims->getHeight()/1000;

        $totalMaxDim = max($totalLength, $totalWidth, $totalHeight);

        $totalVolume = round($totalLength * $totalWidth * $totalHeight, 2, PHP_ROUND_HALF_UP);

        // Override calculated params
        $totalWeight = (!empty($shipmentData['WEIGHT'])) ? $shipmentData['WEIGHT'] : $totalWeight;
        $totalVolume = (!empty($shipmentData['VOLUME'])) ? $shipmentData['VOLUME'] : $totalVolume;
        $totalMaxDim = (!empty($shipmentData['MAX_DIMENSION'])) ? $shipmentData['MAX_DIMENSION'] : $totalMaxDim;

        return array(
                'weight'       => $totalWeight,
                'volume'       => $totalVolume,
                'maxDimension' => $totalMaxDim,
                'length'       => $totalLength,
                'width'        => $totalWidth,
                'height'       => $totalHeight,
            );
    }

    /**
     * @deprecated
     */
	public static function getOrderVolume()
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load(self::$ORDER_ID);

		$result = 0;
		foreach ($order->getBasket() as $item) {
			$itemQuantity = $item->getQuantity();
			$itemVolume = self::calculateVolumeM3(unserialize($item->getField('DIMENSIONS')));
			if (!$itemVolume || $itemVolume < 0.001) {
				$itemVolume = Option::get(self::$MODULE_ID, "PEC_VOLUME", '0.001');
			}
			$result += $itemVolume * $itemQuantity;
		}

		return $result;
	}

    public static function getOrderShipmentData($orderId)
    {
        $shipmentData = ShipmentPropsValueTable::query()
            ->setSelect(['PROPS_CODE', 'VALUE'])
            ->setFilter(['=ORDER_ID' => $orderId])
            ->exec()
            ->fetchAll();
        return array_combine(
            array_column($shipmentData, 'PROPS_CODE'),
            array_column($shipmentData, 'VALUE')
        );
    }

    /**
     * @deprecated
     */
	public static function getOrderWeight()
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load(self::$ORDER_ID);

        $shipmentData = self::getOrderShipmentData(self::$ORDER_ID);
        if (!empty($shipmentData['WEIGHT'])) {
            return $shipmentData['WEIGHT'];
        }

        $weight = 0;
		foreach ($order->getBasket() as $item) {
            $itemQuantity = $item->getQuantity();
            $itemWeight = (float)$item->getWeight() / 1000;
            $minWeight = (float)Option::get(static::$MODULE_ID, "PEC_WEIGHT", 0.05);
            $itemWeight = max($itemWeight, $minWeight);
            $weight += $itemWeight * $itemQuantity;
		}

		return $weight;
	}

    /**
     * @deprecated
     */
	public static function getOrderMaxDimension($orderId = '')
	{
		$orderId = $orderId ? : self::$ORDER_ID;
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);

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
		$result['VOLUME'] = $result['VOLUME'] ? : Option::get(Tools::$MODULE_ID, "PEC_VOLUME", 0.001);

        $shipmentData = self::getOrderShipmentData($orderId);
        if (!empty($shipmentData['MAX_DIMENSION'])) {
            if ($result['WIDTH'] > $result['HEIGHT']) {
                if ($result['WIDTH'] > $result['LENGTH']) {
                    $result['WIDTH'] = $shipmentData['MAX_DIMENSION'] * 1000;
                } else {
                    $result['LENGTH'] = $shipmentData['MAX_DIMENSION'] * 1000;
                }
            } else {
                if ($result['HEIGHT'] > $result['LENGTH']) {
                    $result['HEIGHT'] = $shipmentData['MAX_DIMENSION'] * 1000;
                } else {
                    $result['LENGTH'] = $shipmentData['MAX_DIMENSION'] * 1000;
                }
            }
        }
        if (!empty($shipmentData['VOLUME'])) {
            $result['VOLUME'] = $shipmentData['VOLUME'];
        }

		return $result;
	}

	public static function getOrderPositionsCount()
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load(self::$ORDER_ID);

        $shipmentData = self::getOrderShipmentData(self::$ORDER_ID);
        if (!empty($shipmentData['POSITION_COUNT'])) {
            return $shipmentData['POSITION_COUNT'];
        }

		$result = 0;
		foreach ($order->getBasket() as $item) {
			$itemQuantity = $item->getQuantity();
			$result += $itemQuantity;
		}

		return $result;
	}

	public static function getOrderProducts()
	{
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load(self::$ORDER_ID);

		$result = [];
		foreach ($order->getBasket() as $item) {
			$dimensions = unserialize($item->getField('DIMENSIONS'));
			if ($dimensions['LENGTH'] < 10) {
				$dimensions['LENGTH'] = 10;
			}
			if ($dimensions['WIDTH'] < 10) {
				$dimensions['WIDTH'] = 10;
			}
			if ($dimensions['HEIGHT'] < 10) {
				$dimensions['HEIGHT'] = 10;
			}
			$volume = self::calculateVolumeM3($dimensions);
			if (!$volume) {
				$volume = Option::get(self::$MODULE_ID, "PEC_VOLUME", '0.001');
			}
			if ($volume < 0.001) {
				$volume = 0.001;
			}

			$weight = $item->getWeight();
			if (!(float)$weight) {
				$weight = Option::get(self::$MODULE_ID, "PEC_WEIGHT", '0.05');
			}

			$result[] = [
				'name'     => $item->getField('NAME'),
				'volume'   => $volume,
				'weight'   => $weight,
				'quantity' => $item->getQuantity(),
				'length'   => $dimensions['LENGTH'] / 1000,
				'width'    => $dimensions['WIDTH'] / 1000,
				'height'   => $dimensions['HEIGHT'] / 1000,
			];
		}

		return $result;
	}

	public static function isDeliveryToAddress($orderId = ''): bool
	{
		$orderId = $orderId ? : self::$ORDER_ID;

		$db = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');
		return $db->toAddressType == 'address';
	}

    public static function isDeliveryFromAddress($orderId = ''): bool
    {
        $orderId = $orderId ? : self::$ORDER_ID;

        $db = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');
        return $db->fromDepartmentData == null;
    }

	public static function getDeliveryAddress()
	{
		return self::getDeliveryAddressParams()['addressOrUid'];
	}

	public static function getDeliveryCity()
	{
		$db = PecomEcommDb::GetOrderDataArray(self::$ORDER_ID, 'WIDGET');
        if ($db->toDepartmentData->Town->Town) {
            return $db->toDepartmentData->Town->Town;
        }
        $order = Order::load(self::$ORDER_ID);
        $locProp = $order->getPropertyCollection()->getDeliveryLocation();
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
        return null;
	}

	public static function getDeliveryWarehouseId()
	{
		$db = PecomEcommDb::GetOrderDataArray(self::$ORDER_ID, 'WIDGET');
		return $db->toDepartmentData->Warehouses[0]->UID;
	}

    public static function getIntakeWarehouseId()
    {
        $db = PecomEcommDb::GetOrderDataArray(self::$ORDER_ID, 'WIDGET');
        return $db->fromDepartmentData->Warehouses[0]->UID;
    }

	public static function getDeliveryAddressParams($orderId = ''): array
	{
		$orderId = $orderId ? : self::$ORDER_ID;
		$db = PecomEcommDb::GetOrderDataArray($orderId, 'WIDGET');
		return [
			'type'         => $db->toAddressType,
			'addressOrUid' => $db->toAddress,
		];
	}

	public static function getOrderReceiverTitle()
	{
		$orderId = self::$ORDER_ID;

		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);
		$propertyCollection = $order->getPropertyCollection();
		$result = '';
		if ($propItem = $propertyCollection->getProfileName()) {
			$result = $propItem->getValue();
		}

		return $result;
	}

	public static function getOrderReceiverName()
	{
		$orderId = self::$ORDER_ID;

		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);
		$propertyCollection = $order->getPropertyCollection();
		$result = '';
		if ($propItem = $propertyCollection->getPayerName()) {
			$result = $propItem->getValue();
		}

		return $result;
	}

	public static function getOrderReceiverPhone()
	{
		$orderId = self::$ORDER_ID;

		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);
		$propertyCollection = $order->getPropertyCollection();
		$result = '';
		if ($propItem = $propertyCollection->getPhone()) {
			$result = $propItem->getValue();
		}

		return $result;
	}

	public static function getOrderSenderCity()
	{
		$db = PecomEcommDb::GetOrderDataArray(self::$ORDER_ID, 'WIDGET');
		return $db->fromDepartmentData->Town->Town;
	}

	public static function getOrderTransportType($orderId)
	{
		return PecomEcommDb::GetOrderData($orderId, 'TRANSPORTATION_TYPE');
	}

	public static function calculateVolumeM3(array $size)
	{
		return round($size['LENGTH'] * $size['WIDTH'] * $size['HEIGHT'] / (1000 ** 3), 2, PHP_ROUND_HALF_UP);
	}

	public static function getTransportTypeWidget() {
		return Option::get(self::$MODULE_ID, 'PEC_API_TYPE_DELIVERY', 'auto');
	}

	public static function getOrderPecTransportType($orderId) {
		$db = PecomEcommDb::GetOrderDataArray($orderId, 'PEC_API_SUBMIT_REQUEST');
		$typeId = $db['cargos'][0]['common']['type'];
		if ($typeId) {
			$typeCode = array_keys(self::$SUBMIT_TRANSPORT_TYPE, $typeId)[0];
		} else {
			$typeId = $db['common']['type'];
			if ($typeId) {
				$typeCode = array_keys(self::$PREREGISTRATION_TRANSPORT_TYPE, $typeId)[0];;
			}
		}
		if (!$typeId) {
			$typeCode = Option::get(self::$MODULE_ID, 'PEC_API_TYPE_DELIVERY', 'auto');
		}
		return $typeCode;
	}

	public static function getSubmitTransportType() {
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		$typeCode = $request->getPost("transpotType");
		return self::$SUBMIT_TRANSPORT_TYPE[$typeCode];
	}

	public static function getPreregistrationTransportType() {
		$request = \Bitrix\Main\Context::getCurrent()->getRequest();
		$typeCode = $request->getPost("transpotType");
		return self::$PREREGISTRATION_TRANSPORT_TYPE[$typeCode];
	}

	public static function agentUpdateOrdersPecStatus($limitIterations = 100) {
		$returnFuncName = '\Pec\Delivery\Tools::agentUpdateOrdersPecStatus();';
		$debug = false;

		if (!\CModule::IncludeModule('sale')) {
			return $returnFuncName;
		}

		$nStepTimeLimit = 10;
		$timerStart = time();

		$optionPecApiStartAgent = array_filter(unserialize(Option::get(self::$MODULE_ID, 'PEC_API_START_AGENT', '')));
		$optionPecApiStartAgent = array_keys($optionPecApiStartAgent);
		if (!$optionPecApiStartAgent) {
			return $returnFuncName;
		}

		$startOrderId = self::getStartOrderIdByModuleOptions();
		$actualPecOrders = self::getOrderWithPecStatusByOption($startOrderId);
		$actualPecOrders = array_slice($actualPecOrders, 0, $limitIterations, true);
		$actualOrderIds = array_keys($actualPecOrders);


		if (!$actualOrderIds) {
			Option::set(self::$MODULE_ID, 'PEC_DELIVERY_AGENT_LAST_ORDER_ID', 0);
			return $returnFuncName;
		}

		$request = new Request();

		$response = [];
		foreach ($actualOrderIds as $orderId) {
			$pecId = $actualPecOrders[$orderId]['pecId'];
			if (!$pecId) continue;
			$response[] = $request->getPecStatus([$pecId]);
		}

		$lastOrderId = end($actualOrderIds);
		$requestArray = $changesArray = [];
		foreach ($response as $item) {
			if ($item->error) {
				if ($debug)
					file_put_contents(__DIR__.'/log.txt',"Error ".print_r($item->error,1)."\n",FILE_APPEND);
				continue;
			}

			$item = $item->cargos[0];
			$cargoStatusId = $item->info->cargoStatusId; // ����� ID ������� �����
			if (!$cargoStatusId) {
				if ($debug)
					file_put_contents(__DIR__.'/log.txt',"No ID ".print_r($item,1)."\n",FILE_APPEND);
				continue;
			}
			$cargoStatusName = $item->info->cargoStatus; // ����� �������� ������� �����
			$pecId = $item->cargo->code;                        // ��� �����
			$preliminaryCode = $item->cargo->preliminaryCode;   // ��� ����� 2
			$pecStatus = ['code' => $cargoStatusId, 'name' => $cargoStatusName];

			$requestArray['requestArray'][] = ['orderId' => $pecId, 'statusId' => $cargoStatusId];
			foreach ($actualPecOrders as $orderId => $value) {
				if ($value['pecId'] == $pecId || $value['pecId'] == $preliminaryCode) {
					$oldPecStatus = $actualPecOrders[$orderId]['status'];
					if ($oldPecStatus != $cargoStatusId) {
						self::savePecStatus($orderId, $pecStatus);
						self::changeShipStatusBySettingTable($orderId, $pecStatus);
						$lastOrderId = $orderId;
						$changesArray['changesArray'][] = ['orderId' => $pecId, 'statusId' => $cargoStatusId, 'oldPecStatus' => $oldPecStatus];
					}
				}
			}

			if (time() - $timerStart > $nStepTimeLimit) {
				break;
			}
		}

		if ($debug) {
			$log = date('Y-m-d H:i:s') . ' ';
			file_put_contents(__DIR__ . '/log.txt', $log ." actualPecOrders". PHP_EOL, FILE_APPEND);
			file_put_contents(__DIR__.'/log.txt',print_r($actualPecOrders,1)."\n",FILE_APPEND);

			file_put_contents(__DIR__ . '/log.txt', $log ." requestArray". PHP_EOL, FILE_APPEND);
			file_put_contents(__DIR__.'/log.txt',print_r($requestArray,1)."\n",FILE_APPEND);

			file_put_contents(__DIR__ . '/log.txt', $log ." changesArray". PHP_EOL, FILE_APPEND);
			file_put_contents(__DIR__.'/log.txt',print_r($changesArray,1)."\n",FILE_APPEND);
		}

		Option::set(self::$MODULE_ID, 'PEC_DELIVERY_AGENT_LAST_ORDER_ID', $lastOrderId);

		$arOldAgent = CAgent::GetList([], ["NAME" => $returnFuncName])->Fetch();
		$delta_start = 20 - $arOldAgent['AGENT_INTERVAL'];
		$objDateTime = new \DateTime($delta_start." seconds");
		$date = $objDateTime->format("d.m.Y H:i:s");
		$arField['NEXT_EXEC'] = $date;
		CAgent::Update($arOldAgent['ID'], $arField);

		return $returnFuncName;
	}

	private static function getOrderWithPecStatusByOption($startOrderId = 0) {
		$orders = PecomEcommDb::GetOrderIds($startOrderId);
		foreach ($orders as $orderId => $pec) {
			if($pec['status'] == 0)
				self::getAndSavePecStatus($orderId, $pec['pecId']);
		}

		$optionPecApiStartAgent = array_filter(unserialize(Option::get(self::$MODULE_ID, 'PEC_API_START_AGENT', '')));
		$optionPecApiStartAgent = array_keys($optionPecApiStartAgent);
		$orders = array_filter($orders, function($item) use ($optionPecApiStartAgent) {
			return in_array($item['status'], $optionPecApiStartAgent);
		});
		return $orders;
	}

	private static function getStartOrderIdByModuleOptions() {
		$savedStartOrderId = (int)Option::get(self::$MODULE_ID, 'PEC_DELIVERY_AGENT_LAST_ORDER_ID', 0);

		Loader::includeModule('sale');
		$date = \Bitrix\Main\Type\DateTime::createFromPhp(new \DateTime(date('Y-m-d')));
		$dateDiff = Option::get(self::$MODULE_ID, "PEC_API_AGENT_ORDER_EXPIRED", '30');
		$date->add("-$dateDiff day");

		$parameters = [
			'order' => ['id' => 'asc'],
			'select' => ['*'],
			'filter' => [
				'>=DATE_INSERT' => $date,
			],
			'limit' => 1
		];
		$res = \Bitrix\Sale\Order::getList($parameters);
		$resultId = 0;
		if ($el = $res->fetch()) {
			$resultId = $el['ID'];
		}

		return max($resultId, $savedStartOrderId + 1);
	}

	private static function getDocumentData($orderId) {
		Loader::includeModule('sale');
		$order = \Bitrix\Sale\Order::load($orderId);
		$personTypeId = $order->getPersonTypeId();

		$result = [];
		if ($personTypeId == 1) {
			$documentType = Option::get(self::$MODULE_ID, 'PEC_DOCUMENT_TYPE');
			$result['identityCard']['type'] = (int)\CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $documentType))->Fetch()['VALUE'];
            if (!$result['identityCard']['type']) $result['identityCard']['type'] = 0;
			$documentSeries = Option::get(self::$MODULE_ID, "PEC_DOCUMENT_SERIES", '');
			$documentNumber = Option::get(self::$MODULE_ID, "PEC_DOCUMENT_NUMBER", '');
			$documentDate = Option::get(self::$MODULE_ID, "PEC_DOCUMENT_DATE", '');
			if ($documentSeries) {
				$result['identityCard']['series'] = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $documentSeries))->Fetch()['VALUE'];
			} else return false;
			if ($documentNumber) {
				$result['identityCard']['number'] = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $documentNumber))->Fetch()['VALUE'];
			} else return false;
			if ($documentDate) {
				$date = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $documentDate))->Fetch()['VALUE'];
				$result['identityCard']['date'] = date('Y-m-d', strtotime($date));
			} else return false;
		} else {
			$documentType = Option::get(self::$MODULE_ID, 'PEC_DELIVERY_INN');
			$result['inn'] = \CSaleOrderPropsValue::GetList(array(), array('ORDER_ID' => $orderId, 'CODE' => $documentType))->Fetch()['VALUE'];

		}
		return $result;
	}

	private static function filterActualOrderIds($orderIds, $limit = null) {
		Loader::includeModule('sale');

		$parameters = [
			'order' => ['id' => 'asc'],
			'select' => ['*'],
			'filter' => [
				'ID' => $orderIds,
				'CANCELED' => 'N',
				// 'DEDUCTED' => 'N'
			],
			'limit' => $limit
		];
		$res = \Bitrix\Sale\Order::getList($parameters);
		$result = [];
		while ($el = $res->fetch()) {
			$result[] = $el['ID'];
		}
		return $result;
	}

	public static function agentUpdateModuleFiles() {
		self::addSaleProperty();
		global $DB;
		if (!$DB->TableExists('pecom_ecomm')) {
			$sql = '
                create table if not exists pecom_ecomm
                (
                    ID int(11) NOT NULL auto_increment,
                    ORDER_ID int(11),
                    PEC_ID varchar(50),
                    WIDGET text,
                    STATUS text,
                    TRANSPORTATION_TYPE varchar(50),
                    PEC_API_SUBMIT_REQUEST text,
                    PEC_API_SUBMIT_RESPONSE text,
                    PEC_API_SUBMIT_OK varchar(1),
                    UPTIME varchar(10),
                    PRIMARY KEY(ID),
                    INDEX ix_pecom_ecomm (ORDER_ID)
                );
            ';
			global $DB;
			$DB->Query($sql);
		} else {
			$sql = "show columns FROM `pecom_ecomm` where `Field` = 'TRANSPORTATION_TYPE'";
			$query = $DB->Query($sql);
			if (!$query->Fetch())
				$DB->Query('ALTER TABLE pecom_ecomm ADD COLUMN TRANSPORTATION_TYPE VARCHAR(255) NOT NULL AFTER STATUS;');
		}
		DeleteDirFilesEx("/bitrix/js/pecom.ecomm/");
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_delivery/pecomecomm/");
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::$MODULE_ID."/install/js/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/js/", true, true);
		CopyDirFiles($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/".self::$MODULE_ID."/install/sale_delivery/", $_SERVER["DOCUMENT_ROOT"]."/bitrix/php_interface/include/sale_delivery/", true, true);
		exec("chmod -R 744 ".$_SERVER['DOCUMENT_ROOT']."/bitrix/modules/pecom.ecomm/lib/tcpdf");

		if(file_exists($_SERVER['DOCUMENT_ROOT'].'/bitrix/tools/'.self::$MODULE_ID))
			DeleteDirFilesEx("/bitrix/tools/".self::$MODULE_ID);

		DeleteDirFilesEx("/bitrix/images/".self::$MODULE_ID);
		DeleteDirFilesEx("/bitrix/php_interface/include/sale_delivery/delivery_ipolh_pecom.php");
		DeleteDirFilesEx("/bitrix/components/ipol/ipol.pecomPickup");
		DeleteDirFilesEx("/upload/".self::$MODULE_ID);

		\COption::SetOptionString(self::$MODULE_ID,'login',false);
		\COption::SetOptionString(self::$MODULE_ID,'pass',false);

		self::RegisterAgent();

		$eventManager = \Bitrix\Main\EventManager::getInstance();
		$eventManager->registerEventHandler("sale", "OnSaleStatusOrder", self::$MODULE_ID, "\\Pec\\Delivery\\Handlers", "OnSaleStatusOrder");
		$eventManager->registerEventHandler("main", "OnEpilog", self::$MODULE_ID, "\\Pec\\Delivery\\Handlers", "onChangeDeliveryService");
		$eventManager->unRegisterEventHandler("main", "OnEpilog", self::$MODULE_ID, '\Ipolh\Pecom\subscribeHandler', "onEpilog");
		$eventManager->unRegisterEventHandler("sale", "OnSaleComponentOrderOneStepProcess",  self::$MODULE_ID, '\Ipolh\Pecom\subscribeHandler', "loadComponent");
		$eventManager->unRegisterEventHandler("sale", "OnSaleComponentOrderOneStepDelivery", self::$MODULE_ID, '\Ipolh\Pecom\subscribeHandler', "prepareData");
		$eventManager->unRegisterEventHandler("main", "OnEndBufferContent", self::$MODULE_ID, '\Ipolh\Pecom\subscribeHandler', "OnEndBufferContent");
		$eventManager->unRegisterEventHandler("sale", "OnSaleComponentOrderOneStepComplete", self::$MODULE_ID, '\Ipolh\Pecom\subscribeHandler', "onOrderCreate");
		$eventManager->unRegisterEventHandler("sale", "OnSaleOrderBeforeSaved", self::$MODULE_ID, 'Ipolh\Pecom\subscribeHandler', "onBeforeOrderCreate");

		self::InstallDeliveryService();
	}

	public static function InstallDeliveryService() {
		if (!self::UninstallDeliveryService()) {
			Loader::includeModule('sale');
			$arFile = \CFile::MakeFileArray('/bitrix/modules/' . self::$MODULE_ID . '/install/sale_delivery/pecomecomm/logo_sq2.png');
			$arLogo = \CFile::SaveFile($arFile, "sale/delivery/logotip");

			$arFields = array(
				'NAME' => GetMessage('PEC_DELIVERY_TOOLS_MODULE_NAME'),
				'ACTIVE' => 'Y',
				'DESCRIPTION' => GetMessage('PEC_DELIVERY_TOOLS_SERVICE_NAME_COURIER'),
				'LOGOTIP' => $arLogo,
				'CLASS_NAME' => '\Sale\Handlers\Delivery\PecomEcommHandler',
				'CURRENCY' => 'RUB',
				'ALLOW_EDIT_SHIPMENT' => 'Y'
			);

			Delivery\Services\Manager::add($arFields);
		}
	}

	public static function UninstallDeliveryService() {
		Loader::includeModule('sale');

		$deliveries = [];
		$result = false;
		if (method_exists ( '\Bitrix\Sale\Delivery\Services\Manager', 'getList' )) {
			$res = Delivery\Services\Manager::getList(
				array(
					'select' => array('ID', 'NAME', 'DESCRIPTION', 'CLASS_NAME', 'ALLOW_EDIT_SHIPMENT', 'CURRENCY'),
					'order' => array('ACTIVE' => 'DESC')
				)
			);
			while ($item = $res->fetch()) {
				if ($item['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomEcommHandler'
					|| $item['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomIntegrationHandler'
				) {
					$deliveries[] = [
						'id' => $item['ID'],
						'ALLOW_EDIT_SHIPMENT' => $item['ALLOW_EDIT_SHIPMENT'],
						'NAME' => $item['NAME'],
						'DESCRIPTION' => $item['DESCRIPTION'],
						'CURRENCY' => $item['CURRENCY']
					];
				}
			}
			rsort($deliveries);
		}
		foreach ($deliveries as $key =>$delivery) {
			if ($key == 0) {
				$name = $delivery['NAME'] ? : GetMessage('PEC_DELIVERY_TOOLS_MODULE_NAME');
				$description = $delivery['DESCRIPTION'] ? : GetMessage('PEC_DELIVERY_TOOLS_SERVICE_NAME_COURIER');
				$arFields = array(
					'NAME' => $name,
					'ACTIVE' => 'Y',
					'DESCRIPTION' => $description,
					'CLASS_NAME' => '\Sale\Handlers\Delivery\PecomEcommHandler',
					'CURRENCY' => $delivery['CURRENCY'],
					'ALLOW_EDIT_SHIPMENT' => $delivery['ALLOW_EDIT_SHIPMENT']
				);

				if (Delivery\Services\Manager::update($delivery['id'], $arFields))
					$result = true;
			} else {
				Delivery\Services\Manager::delete($delivery['id']);
			}
		}

		if (count($deliveries) == 0)
			$result = false;

		return $result;
	}

	public static function getPecPrice($data) {
		$httpClient = new \Bitrix\Main\Web\HttpClient();
		$httpClient->setHeader('Content-Type', 'application/json;charset=UTF-8', true);
		$httpClient->setHeader('X-Requested-With', 'XMLHttpRequest', true);
		$url = self::getWidgetApiUrl();

//        $httpClient->setTimeout(4);
//        $httpClient->setStreamTimeout(4);

		$resultData = $httpClient->post($url, json_encode($data), false);
		return json_decode($resultData);
	}

	public static function getDeliveryID()
	{
		Loader::includeModule('sale');

		$res = Delivery\Services\Manager::getActiveList();

		$deliveryId = '';
		foreach ($res as $item) {
			if ($item['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomEcommHandler') {
				$deliveryId = $item['ID'];
			}
		}
		return $deliveryId;
	}

	public static function addSaleProperty() {
		Loader::includeModule('sale');

		$personResult = \CSalePersonType::GetList(Array("SORT" => "ASC", Array("LID" => SITE_ID)), Array());
		while($person = $personResult->Fetch()){
			if ($person['ACTIVE'] == 'Y') {
				$prop = \CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DELIVERY", "PERSON_TYPE_ID" => $person['ID']))->Fetch();
				$tmpGet = \CSaleOrderPropsGroup::GetList(array("SORT" => "ASC"),array("PERSON_TYPE_ID" => $person['ID']),false,array('nTopCount' => '1'));
				$tmpVal = $tmpGet->Fetch();
				if (!$prop) {
					$arField = array(
						"PERSON_TYPE_ID" => $person['ID'],
						"NAME" => GetMessage("PEC_DELIVERY_MODULE_NAME"),
						"DESCRIPTION" => '',
						"TYPE" => "TEXT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 200,
						"CODE" => 'PEC_DELIVERY',
						"PROPS_GROUP_ID" => $tmpVal['ID'],
						"USER_PROPS" => "Y",
						"IS_LOCATION" => "N",
						"IS_LOCATION4TAX" => "N",
						"SIZE1" => 140,
						"SIZE2" => 2,
						"IS_EMAIL" => "N",
						"IS_PROFILE_NAME" => "N",
						"IS_PAYER" => "N",
						"IS_FILTERED" => "Y",
						"IS_ZIP" => "N",
						"UTIL" => "Y"
					);
					$propId = \CSaleOrderProps::Add($arField);
					if ($propId) {
						$deliveryId = self::getDeliveryID();
						\CSaleOrderProps::UpdateOrderPropsRelations($propId, [$deliveryId], "D");
					}
				}

				$op = \CSaleOrderProps::GetList(array(), array("CODE" => "PEC_DOCUMENT_TYPE"))->Fetch();
				if (!$op) {
					$arFields = array(
						"PERSON_TYPE_ID" => $person['ID'],
						"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE"),
						"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_DESCRIPTION"),
						"ACTIVE" => "N",
						"TYPE" => "SELECT",
						"REQUIED" => "N",
						"DEFAULT_VALUE" => "",
						"SORT" => 200,
						"CODE" => 'PEC_DOCUMENT_TYPE',
						"PROPS_GROUP_ID" => $tmpVal['ID'],
					);
					$ID = \CSaleOrderProps::Add($arFields);
					if ($ID) {
						$arFieldsV = array(
							[
								"ORDER_PROPS_ID" => $ID,
								"VALUE" => 10,
								"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_P"),
								"SORT" => 100,
								"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_P")
							],
							[
								"ORDER_PROPS_ID" => $ID,
								"VALUE" => 3,
								"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_V"),
								"SORT" => 200,
								"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_V")
							],
							[
								"ORDER_PROPS_ID" => $ID,
								"VALUE" => 5,
								"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_Z"),
								"SORT" => 300,
								"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_Z")
							],
							[
								"ORDER_PROPS_ID" => $ID,
								"VALUE" => 12,
								"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_B"),
								"SORT" => 400,
								"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_B")
							],
							[
								"ORDER_PROPS_ID" => $ID,
								"VALUE" => 1,
								"NAME" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_I"),
								"SORT" => 500,
								"DESCRIPTION" => GetMessage("PEC_DELIVERY_DOCUMENT_TYPE_I")
							]
						);
						foreach ($arFieldsV as $item) {
							\CSaleOrderPropsVariant::Add($item);
						}
						$deliveryId = self::getDeliveryID();
						return \CSaleOrderProps::UpdateOrderPropsRelations($ID, [$deliveryId], "D");
					}
				}
			}
			$allPayers[] = $person['ID'];
		}
	}

	public static function RegisterAgent($interval = 7200)
	{
		$arAgent = CAgent::GetList([], ["NAME" => '\Pec\Delivery\Tools::agentUpdateOrdersPecStatus();'])->Fetch();
		if (!$arAgent) {
			$active = Option::get(self::$MODULE_ID, 'PEC_API_AGENT_ACTIVE', '') ? 'Y' : 'N';
			$objDateTime = new \DateTime("+10 seconds");
			$date = $objDateTime->format("d.m.Y H:i:s");
			CAgent::AddAgent(
				"\Pec\Delivery\Tools::agentUpdateOrdersPecStatus();",
				self::$MODULE_ID,
				'Y',
				$interval,
				'Y',
				$active,
				$date
			);
		}
	}

	public static function getDeliveryConfig() {
		$result = [];

		if (method_exists ( '\Bitrix\Sale\Delivery\Services\Manager', 'getList' )) {
			$res = Delivery\Services\Manager::getList(
				array(
					'select' => array('CONFIG', 'CLASS_NAME', 'CURRENCY'),
					'order' => array('ACTIVE' => 'DESC')
				)
			);
			while ($item = $res->fetch()) {
				if ($item['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomEcommHandler'
					|| $item['CLASS_NAME'] == '\Sale\Handlers\Delivery\PecomIntegrationHandler'
				) {
					$result = $item['CONFIG']['MAIN'];
				}
			}
		}
		return $result;
	}

    public static function getWidgetApiUrl(){
        return Option::get(self::$MODULE_ID, 'PEC_WIDGET_API_URL', 'https://calc.pecom.ru/api/e-store-calculate');
    }

    public static function getWidgetUrl(){
        return Option::get(self::$MODULE_ID, 'PEC_WIDGET_URL', 'https://calc.pecom.ru/iframe/e-store-calculator');
    }

	public static function otherWarehouse(): string
	{
		global $APPLICATION;
		$key = round(microtime(true)*1000);
		ob_start();?>

	<div style="display: flex; border-top: 1px solid grey; margin-top: 15px; padding-top: 15px" class="dop_sklad"
		 id="div-dop-sklad-<?=$key?>">
		<style>
            .dop_sklad .bx-slst .bx-ui-combobox-toggle {
                background-position: 6px -2623px;
                height: 22px;
            }
            .dop_sklad .bx-slst .dropdown-fade2white {
                right: 1px;
                height: 22px;
            }
            .dop_sklad .bx-slst .dropdown-icon {
                top: 7px;
            }
		</style>
		<div style="width: 45%;"><?=GetMessage("PEC_DELIVERY_WAREHOUSE_1")?><br><br>
			<div style="padding: 0 20px 20px 0" class="bx-admin-mode"><?php
				$APPLICATION->IncludeComponent("bitrix:sale.location.selector.".Helper::getWidgetAppearance(), "", array(
					"ID" => "",
					"CODE" => "",
					"INPUT_NAME" => "PEC_STORE_DOP[$key][parent_id]",
					"PROVIDE_LINK_BY" => "id",
					"SHOW_ADMIN_CONTROLS" => 'Y',
					"SELECT_WHEN_SINGLE" => 'N',
					"FILTER_BY_SITE" => 'N',
					"SHOW_DEFAULT_LOCATIONS" => 'N',
					"SEARCH_BY_PRIMARY" => 'Y',
					"ADMIN_MODE"=>'Y'
				),
					false
				)?>
			</div>
		</div>
		<div>
			<br><br>
			<textarea  rows="2" cols="45" name="PEC_STORE_DOP[<?=$key?>][address]"></textarea>
			<div><?=GetMessage("PEC_DELIVERY_WAREHOUSE_2")?></div>
			<br>
            <input type="radio" value="1" name="PEC_STORE_DOP[<?=$key?>][intake]" checked><?=GetMessage("PEC_DELIVERY_BY_ADDRESS")?>
            <input type="radio" value="0" name="PEC_STORE_DOP[<?=$key?>][intake]"><?=GetMessage("PEC_DELIVERY_IN_DEPARTMENT")?>
		</div>
		<div style="padding: 30px">
			<button type="button" class="adm-btn" id="but-dop-sklad-<?=$key?>"><?=GetMessage("PEC_DELIVERY_WAREHOUSE_3")?></button>
		</div>
		<script>
            BX.ready(function (){
                BX("but-dop-sklad-<?=$key?>").addEventListener("click",function (){
                    BX.remove(BX("div-dop-sklad-<?=$key?>"));
                });
            });
		</script>
		</div><?php

		return ob_get_clean();
	}

    public static function getRefferalId()
    {
        return sprintf(
            GetMessage('PEC_DELIVERY_REF_NAME_1').' #%s# v.%s - %s (%s)',
            Option::get('pecom.ecomm', 'PEC_API_LOGIN', ''),
            ModuleManager::getVersion('pecom.ecomm'),
            GetMessage('PEC_DELIVERY_REF_NAME_2'),
            SM_VERSION
        );
    }

    /**
     * Checks login and pass for API access
     * @param string $login
     * @param string $pass
     * @param $apiUrl
     * @return mixed
     * @throws \PecomKabinetException
     */
    public static function checkApi($login, $pass, $apiUrl)
    {
        include_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/pecom.ecomm/lib/pec-api/pecom_kabinet.php');

        $sdk = new \PecomKabinet($login, $pass, [], $apiUrl);

        return $sdk->call('BRANCHES', 'FINDZONEBYADDRESS', array('address' => GetMessage('PEC_DELIVERY_MOSCOW')));
    }

    private static $LABEL_ID = 0;

    public static function ShowLabel($text)
    {
        echo sprintf(
                '<span id="%s"></span>
                <script>BX.hint_replace(BX(\'%s\'), \'%s\');</script>',
            'pec_option_' . self::$LABEL_ID,
            'pec_option_' . self::$LABEL_ID,
            CUtil::JSEscape(str_replace(PHP_EOL, ' ', trim($text)))
        );
        self::$LABEL_ID = self::$LABEL_ID + 1;
    }
}
