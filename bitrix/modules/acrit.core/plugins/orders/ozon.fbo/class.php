<?
/**
 * Acrit Core: Orders integration plugin for Ozon.ru
 */

namespace Acrit\Core\Orders\Plugins;

require_once __DIR__ . '/lib/api/orders.php';

use Acrit\Core\Orders\PeriodSync;
use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log,
	\Acrit\Core\Orders\Plugins\OzonFboHelpers\Orders;

Loc::loadMessages(__FILE__);

class OzonFbo extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected $isCountable = true;
    protected static $arOrders = [];

	/**
	 * Base constructor.
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return 'OZON_FBO';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'OZON FBO';
	}

	/**
	 * Get type of regular synchronization
	 */
	public static function getAddSyncType() {
		return self::ADD_SYNC_TYPE_DUAL;
	}

	/**
	 * Get plugin help link
	 */
	public static function getHelpLink() {
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-ozon/';
	}

	/**
	 *	Include classes
	 */
	public function includeClasses() {
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 * Get id of products in marketplace
	 */
	public static function getIdField() {
		return [
			'id' => 'order_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_PRODUCTS_ID_FIELD_NAME'),
		];
	}

	/**
	 * Store fields for deal contact
	 * @return array
	 */
	public function getContactFields() {
		$list = [];
		$list[0]['title'] = Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_CONTACT_FIELDS_GROUP_1');
		$list[0]['items'] = self::getFields();
		return $list;
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];

        $list[] = [
            'id' => 'awaiting_deliver',
            'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_AWAITING_DELIVER'),
        ];
        $list[] = [
            'id' => 'awaiting_packaging',
            'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_AWAITING_PACKAGING'),
        ];
        $list[] = [
            'id' => 'cancelled',
            'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_CANCELLED'),
        ];
        $list[] = [
            'id' => 'delivered',
            'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_DELIVERED'),
        ];
		$list[] = [
			'id' => 'delivering',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_STATUSES_DELIVERING'),
		];
		return $list;
	}

	/**
	 * Store fields for deal fields
	 * @return array
	 */
	public function getFields() {
		$list = parent::getFields();
		$list[] = [
			'id' => 'order_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ORDER_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'order_number',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ORDER_NUMBER'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'posting_number',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_POSTING_NUMBER'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_STATUS'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_city',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_CITY'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_delivery_type',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_DELIVERY_TYPE'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_is_premium',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_IS_PREMIUM'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_payment_type_group_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_PAYMENT_TYPE_GROUP_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_region',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_REGION'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_warehouse_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_WAREHOUSE_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'analytics_data_warehouse_name',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_ANALYTICS_DATA_WAREHOUSE_NAME'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cancel_reason_id',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CANCEL_REASON_ID'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'created_at',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CREATED_AT'),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'in_process_at',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_IN_PROCESS_AT'),
			'direction' => self::SYNC_STOC,
		];
//		$list[] = [
//			'id' => 'shipment_date',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_SHIPMENT_DATE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'tracking_number',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_TRACKING_NUMBER'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'cancel_reason',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CANCEL_REASON'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'delivering_date',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_DELIVERING_DATE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'provider_status',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_PROVIDER_STATUS'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'delivery_price',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_DELIVERY_PRICE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'delivery_method_name',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_DELIVERY_METHOD_NAME'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'tpl_provider_id',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_TPL_PROVIDER_ID'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'tpl_provider',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_TPL_PROVIDER'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_name',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_NAME'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_email',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_EMAIL'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_phone',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_PHONE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_address',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_ADDRESS'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_city',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_CITY'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_country',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_COUNTRY'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_district',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_DISTRICT'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_region',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_REGION'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_latitude',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_LATITUDE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_longitude',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_LONGITUDE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_provider_pvz_code',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_PROVIDER_PVZ_CODE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_pvz_code',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_PVZ_CODE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_zip_code',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_ZIP_CODE'),
//			'direction' => self::SYNC_STOC,
//		];
//		$list[] = [
//			'id' => 'customer_comment',
//			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_FIELDS_CUSTOMER_COMMENT'),
//			'direction' => self::SYNC_STOC,
//		];
		return $list;
	}


	/**
	 *	Show plugin default settings
	 */
	public function showSettings($arProfile){
		ob_start();
//		$client_id = $arProfile['CONNECT_CRED']['client_id'];
//		$api_key = $arProfile['CONNECT_CRED']['api_key'];
//		$api = new Orders($client_id, $api_key, $arProfile['ID'], $this->strModuleId);
//		$res = $api->checkConnection($msg);
//		$res = $api->getPostingsList([]);
//		echo '<pre>'; print_r($res); echo '</pre>';
//		$order_data = $this->getOrder('61206578-0004-3');
//      echo '<pre>'; print_r($order_data); echo '</pre>';
//		Settings::setModuleId($this->strModuleId);
//		Controller::setModuleId($this->strModuleId);
//		Controller::setProfile($arProfile['ID']);
//		Controller::syncExtToStore($order_data);
		?>
		<table class="acrit-exp-plugin-settings" style="width:100%;">
			<tbody>
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_HEADING');?></td></tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CLIENT_ID_HINT'));?>
                    <span class="adm-required-field"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CLIENT_ID');?></span>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][client_id]" size="50" maxlength="255" data-role="connect-cred-client_id"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['client_id']);?>" />
				</td>
			</tr>
            <tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_API_KEY_HINT'));?>
                    <span class="adm-required-field"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_API_KEY');?></span>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][api_key]" size="50" maxlength="255" data-role="connect-cred-api_key"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['api_key']);?>" />
                    <p><a class="adm-btn" data-role="connection-check"><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_OZON_SETTINGS_CHECK_CONN');?></a></p>
                    <p id="check_msg"></p>
				</td>
			</tr>
			</tbody>
		</table>
		<?
		return ob_get_clean();
	}

	/**
	 * Ajax actions
	 */

	public function ajaxAction($strAction, $arParams, &$arJsonResult) {
		switch ($strAction) {
			case 'connection_check':
			    $client_id = $arParams['POST']['client_id'];
			    $api_key = $arParams['POST']['api_key'];
				$api = new Orders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
				//$res = $api->checkConnection($message);
				$res = $api->checkConnection_v3($message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}

	/**
	 * Get orders count
	 */

	public function getOrdersCount($create_from_ts) {
        $count = false;
        if ($create_from_ts) {
            $list = $this->getOrdersIDsList($create_from_ts);
            $count = count($list);
        }
        return $count;
	}

	/**
	 * Get list of orders ids
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
	    $list = [];
		// Get the list

		if ( !$create_from_ts && !$change_from_ts ) {
            $date = time() - 86400 * 150;
        } else {
		    $date = $create_from_ts ? $create_from_ts : $change_from_ts;
        }

		$client_id = $this->arProfile['CONNECT_CRED']['client_id'];
		$api_key = $this->arProfile['CONNECT_CRED']['api_key'];
		$api = new Orders($client_id, $api_key, $this->arProfile['ID'], $this->strModuleId);
		$orders_list = [];

            $req_filter = [
                'order_created_at' => [
                    'from' => gmdate("Y-m-d\TH:i:s.000\Z", $date),
                    'from_timestamp' => $date
                ],
            ];
            $orders_list = $api->getPostingsList_v2($req_filter, 1000);
			foreach ($orders_list as $item) {
				$list[] = $item['posting_number'];
                self::$arOrders[$item['posting_number']] = $item;
			}
        unset($orders_list);
        $list = array_reverse(array_unique($list));
	    return $list;
	}

	/**
	 * Get order
	 */
	public function getOrder($posting_id) {
		$order = false;
		// Ozon posting data
        $mp_order = self::$arOrders[$posting_id];
//        file_put_contents(__DIR__.'/order.txt', var_export($mp_order, true));
		if ($mp_order['posting_number']) {
			// Main fields
			$order = [
				'ID'          => $mp_order['posting_number'],
				'DATE_INSERT' => strtotime($mp_order['in_process_at']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
			];
			// Fields
			$order['FIELDS'] = [
				'order_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['order_id']],
				],
				'order_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['order_number']],
				],
				'posting_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['posting_number']],
				],
				'status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status']],
				],
				'created_at' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['in_process_at']],
				],
				'in_process_at' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['in_process_at']],
				],
//				'tracking_number' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['tracking_number']],
//				],
//				'cancel_reason_id' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['cancellation']['cancel_reason_id']],
//				],
//				'cancel_reason' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['cancellation']['cancel_reason']],
//				],
//				'shipment_date' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['shipment_date']],
//				],
//				'delivering_date' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['delivering_date']],
//				],
//				'provider_status' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['provider_status']],
//				],
//				'delivery_price' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['delivery_price']],
//				],
				'analytics_data_city' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['city']],
				],
				'analytics_data_delivery_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['delivery_type']],
				],
				'analytics_data_is_premium' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['is_premium']],
				],
				'analytics_data_payment_type_group_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['payment_type_group_name']],
				],
				'analytics_data_region' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['analytics_data']['region']],
				],
//				'delivery_method_name' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['delivery_method']['name']],
//				],
//				'tpl_provider_id' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['delivery_method']['tpl_provider_id']],
//				],
//				'tpl_provider' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['delivery_method']['tpl_provider']],
//				],
				'analytics_data_warehouse_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery_method']['warehouse_id']],
				],
				'analytics_data_warehouse_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery_method']['warehouse']],
				],
//				'customer_address' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['address_tail']],
//				],
//				'customer_city' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['city']],
//				],
//				'customer_comment' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['comment']],
//				],
//				'customer_country' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['country']],
//				],
//				'customer_district' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['district']],
//				],
//				'customer_latitude' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['latitude']],
//				],
//				'customer_longitude' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['longitude']],
//				],
//				'customer_provider_pvz_code' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['provider_pvz_code']],
//				],
//				'customer_pvz_code' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['pvz_code']],
//				],
//				'customer_region' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['region']],
//				],
//				'customer_zip_code' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['address']['zip_code']],
//				],
//				'customer_email' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['customer_email']],
//				],
//				'customer_name' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['name']],
//				],
//				'customer_phone' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['customer']['phone']],
//				],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = [];
			if (is_array($mp_order['products']) && !empty($mp_order['products'])) {
				$products_list = $mp_order['products'];
			}
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = [
					'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['offer_id'],
					'PRICE'            => $item['price'],
					'CURRENCY'         => 'RUB',
					'QUANTITY'         => $item['quantity'],
					'DISCOUNT_TYPE_ID' => 1,
					'DISCOUNT_SUM'     => 0,
					'MEASURE_CODE'     => 0,
					'TAX_RATE'         => 0,
					'TAX_INCLUDED'     => 'Y',
				];
			}
			$order = self::formatOrder($order);
		}
		return $order;
	}

}
