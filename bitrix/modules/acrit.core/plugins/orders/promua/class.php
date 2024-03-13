<?
/**
 * Acrit Core: Orders integration plugin for prom.ua
 */

namespace Acrit\Core\Orders\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\PromUaHelpers\Orders,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class PromUa extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
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
		return 'PROMUA';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return Loc::getMessage(self::getLangCode('PLUGIN_NAME'));
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
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-tiu-ru-prom-ua-i-deal-by/';
	}

	/**
	 *	Include classes
	 */
	public function includeClasses() {
		#require_once(__DIR__.'/lib/json.php');
	}

	/**
	 * Get comment for the tab
	 */
	public function getTabComment($tab){
	    $comment = '';
	    switch ($tab) {
//            case 'products': $comment = Loc::getMessage(self::getLangCode('PRODUCTS_MESSAGE'));
//                break;
        }
		return $comment;
	}

	/**
	 * Get id of products in marketplace
	 */
	public static function getIdField() {
		return [
			'id' => 'CODE',
			'name' => Loc::getMessage(self::getLangCode('PRODUCTS_ID_FIELD_NAME')),
		];
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$api = $this->getApi();
        $statuses = $api->getStatuses();
        foreach ($statuses as $id => $name) {
	        $list[] = [
		        'id' => $id,
		        'name' => $name,
	        ];
        }
		return $list;
	}

	/**
	 * Store fields for deal contact
	 * @return array
	 */
	public function getContactFields() {
		$list = [];
		$list['user'] = [
			'title' => Loc::getMessage(self::getLangCode('CONTACT_TITLE')),
		];
		$list['user']['items'][] = [
			'id' => 'client_first_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_CLIENT_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'client_second_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_CLIENT_SECOND_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'client_last_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_CLIENT_LAST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'client_avg_rating',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_CLIENT_AVG_RATING')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'email',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_EMAIL')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'phone',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_PHONE')),
			'direction' => self::SYNC_STOC,
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
			'id' => 'id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'date_created',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DATE_CREATED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'client_first_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'client_second_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_SECOND_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'client_last_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_LAST_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'client_avg_rating',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_AVG_RATING')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'client_notes',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_NOTES')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'email',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_EMAIL')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'phone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'price',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PRICE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'full_price',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FULL_PRICE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_option_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_OPTION_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_provider_data_provider',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PROVIDER_DATA_PROVIDER')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_provider_data_type',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PROVIDER_DATA_TYPE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_provider_data_sender_warehouse_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PROVIDER_DATA_SENDER_WAREHOUSE_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_provider_data_recipient_warehouse_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PROVIDER_DATA_RECIPIENT_WAREHOUSE_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_provider_data_declaration_number',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_PROVIDER_DATA_DECLARATION_NUMBER')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_address',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_ADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_cost',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_COST')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_option_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_OPTION_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_data_type',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_DATA_TYPE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_data_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_DATA_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_data_status_modified',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_DATA_STATUS_MODIFIED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'source',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SOURCE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'has_order_promo_free_delivery',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_HAS_ORDER_PROMO_FREE_DELIVERY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cpa_commission_amount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CPA_COMMISSION_AMOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cpa_commission_is_refunded',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CPA_COMMISSION_IS_REFUNDED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'utm_medium',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_UTM_MEDIUM')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'utm_source',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_UTM_SOURCE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'utm_campaign',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_UTM_CAMPAIGN')),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	/**
	 *	Show plugin default settings
	 */
	public function showSettings($arProfile){
		ob_start();
		?>
        <table class="acrit-exp-plugin-settings" style="width:100%;">
            <tbody>
            <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?=Loc::getMessage(self::getLangCode('SETTINGS_HEADING'));?></td></tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_TOKEN'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="255" data-role="connect-cred-token"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['token']);?>" />
                    <p><a class="adm-btn" data-role="connection-check"><?=Loc::getMessage(self::getLangCode('SETTINGS_CHECK_CONN'));?></a></p>
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
				$token = $arParams['POST']['token'];
				$message = '';
				$api = $this->getApi();
				$res = $api->checkConnection($token, $message);
				$arJsonResult['check'] = $res ? 'success' : 'fail';
				$arJsonResult['message'] = $message;
				$arJsonResult['result'] = 'ok';
				break;
		}
	}

	/**
	 * Get object for api requests
	 */

	public function getApi() {
		$api = new Orders($this);
		return $api;
	}

	/**
	 * Get orders count
	 */

	public function getOrdersCount($create_from_ts) {
		$api = $this->getApi();
		$filter = [];
	    if ($create_from_ts) {
		    $filter['date_from'] = date(Orders::DATE_FORMAT, $create_from_ts);
	    }
		$count = $api->getOrdersCount($filter);
		return $count;
	}

	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
		$list = [];
		// Get the list
		$req_filter = [];
		if ($create_from_ts) {
			$req_filter = [
				'date_from' => date(Orders::DATE_FORMAT, $create_from_ts),
			];
		}
        if ($change_from_ts) {
			$req_filter = [
				'last_modified_from' => date(Orders::DATE_FORMAT, $change_from_ts),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 1000);
		foreach ($orders_list as $order) {
			$list[] = $order['id'];
		}
		return $list;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$order = false;
		// Order data
		$api = $this->getApi();
		$mp_order = $api->getOrder($order_id);
		if ($mp_order) {
			// Main fields
			$order = [
				'ID'          => $mp_order['id'],
				'DATE_INSERT' => strtotime($mp_order['date_created']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
				'client_first_name' => $mp_order['client_first_name'],
				'client_second_name'  => $mp_order['client_second_name'],
				'client_last_name'  => $mp_order['client_last_name'],
				'client_avg_rating'  => $mp_order['client_avg_rating'],
				'email'  => $mp_order['email'],
				'phone'  => $mp_order['phone'],
			];
			// Fields
			$order['FIELDS'] = [
				'id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['id']],
				],
				'date_created' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['date_created']],
				],
				'client_first_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['client_first_name']],
				],
				'client_second_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['client_second_name']],
				],
				'client_last_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['client_last_name']],
				],
				'client_avg_rating' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['client_avg_rating']],
				],
				'client_notes' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['client_notes']],
				],
				'email' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['email']],
				],
				'phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['phone']],
				],
				'price' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['price']],
				],
				'delivery_option_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_option'][0]['name']) ? : ''],
				],
				'delivery_provider_data_provider' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_provider_data'][0]['provider']) ? : ''],
				],
				'delivery_provider_data_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_provider_data'][0]['type']) ? : ''],
				],
				'delivery_provider_data_sender_warehouse_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_provider_data'][0]['sender_warehouse_id']) ? : ''],
				],
				'delivery_provider_data_recipient_warehouse_id' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_provider_data'][0]['recipient_warehouse_id']) ? : ''],
				],
				'delivery_provider_data_declaration_number' => [
					'TYPE'  => 'STRING',
					'VALUE' => [isset($mp_order['delivery_provider_data'][0]['declaration_number']) ? : ''],
				],
				'delivery_address' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery_address']],
				],
				'delivery_cost' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['delivery_cost']],
				],
				'payment_option_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_option'][0]['name']],
				],
				'payment_data_type' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_data'][0]['type']],
				],
				'payment_data_status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_data'][0]['status']],
				],
				'payment_data_status_modified' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['payment_data'][0]['status_modified']],
				],
				'status_name' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status_name']],
				],
				'source' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['source']],
				],
				'has_order_promo_free_delivery' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['has_order_promo_free_delivery']],
				],
				'cpa_commission_amount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['cpa_commission'][0]['amount']],
				],
				'cpa_commission_is_refunded' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['cpa_commission'][0]['is_refunded']],
				],
				'utm_medium' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['utm'][0]['medium']],
				],
				'utm_source' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['utm'][0]['source']],
				],
				'utm_campaign' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['utm'][0]['campaign']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			foreach ($mp_order['products'] as $item) {
                $price = 0;
                $price_str = $item['price'];
				$price_str = str_replace(' руб.', '', $price_str);
				preg_match_all("/([\d]+)/i", $price_str, $matches);
                if (isset($matches[1])) {
	                $price = intval(implode('', $matches[1]));
                }
				$order['PRODUCTS'][] = [
                    'PRODUCT_NAME'     => $item['name'],
                    'PRODUCT_CODE'     => $item['external_id'],
                    'PRICE'            => $price,
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
