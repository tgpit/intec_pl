<?
/**
 * Acrit Core: Orders integration plugin for Cdek
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
	\Acrit\Core\Orders\Plugins\CdekHelpers\Orders,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Cdek extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected static $arOrders = [];

	/**
	 * Base constructor
	 */
	public function __construct($strModuleId) {
		parent::__construct($strModuleId);
	}

	/* START OF BASE STATIC METHODS */

	/**
	 * Get plugin unique code ([A-Z_]+)
	 */
	public static function getCode() {
		return 'CDEK';
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
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-sdek/';
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
		$list[] = [
			'id' => 'A',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_A')),
		];
		$list[] = [
			'id' => 'C',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_C')),
		];
		$list[] = [
			'id' => 'D',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_D')),
		];
		$list[] = [
			'id' => 'E',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_E')),
		];
		$list[] = [
			'id' => 'H',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_H')),
		];
		$list[] = [
			'id' => 'K',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_K')),
		];
		$list[] = [
			'id' => 'O',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_O')),
		];
		$list[] = [
			'id' => 'P',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_P')),
		];
		$list[] = [
			'id' => 'Y',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_Y')),
		];
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
			'id' => 'firstname',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_FIRSTNAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'lastname',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_LASTNAME')),
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
			'id' => 'total',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TOTAL')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'subtotal',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SUBTOTAL')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'discount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DISCOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'subtotalDiscount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SUBTOTALDISCOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'createdAt',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATEDAT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'firstname',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FIRSTNAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'lastname',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_LASTNAME')),
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
			'id' => 'city',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CITY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'state',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'country',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_COUNTRY')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingCost',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGCOST')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingDiscount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGDISCOUNT')),
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
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_APIKEY_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_APIKEY'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][apikey]" size="50" maxlength="255" data-role="connect-cred-apikey"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['apikey']);?>" />
                    <p><a class="adm-btn" data-role="connection-check"><?=Loc::getMessage(self::getLangCode('SETTINGS_CHECK_CONN'));?></a></p>
                    <p id="check_msg"></p>
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_TESTMODE_HINT')));?>
                    <?=Loc::getMessage(self::getLangCode('SETTINGS_TESTMODE'));?>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="checkbox" name="PROFILE[CONNECT_CRED][testmode]" data-role="connect-cred-testmode"
                          value="Y" <?=$arProfile['CONNECT_CRED']['testmode']=='Y'?' checked':'';?> />
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
				$apikey = $arParams['POST']['apikey'];
				$message = '';
				$api = $this->getApi();
				$res = $api->checkConnection($apikey, $message);
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
			$filter['startDate'] = date(Orders::DATE_FORMAT, $create_from_ts);
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
		if ($create_from_ts || $change_from_ts) {
			$filter_date = $change_from_ts ? : $create_from_ts;
			$req_filter = [
				'startDate' => date(Orders::DATE_FORMAT, $filter_date),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 100);
		foreach ($orders_list as $item) {
			$list[] = $item['id'];
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
				'DATE_INSERT' => strtotime($mp_order['createdAt']),
				'STATUS_ID'   => $mp_order['status'],
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
				'firstname' => $mp_order['firstname'],
				'lastname'  => $mp_order['lastname'],
				'phone'  => $mp_order['phone'],
				'email'  => $mp_order['email'],
			];
			// Fields
			$order['FIELDS'] = [
				'total' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['total']],
				],
				'subtotal' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['subtotal']],
				],
				'discount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['discount']],
				],
				'subtotalDiscount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['subtotalDiscount']],
				],
				'createdAt' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['createdAt']],
				],
				'status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['status']],
				],
				'firstname' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['firstname']],
				],
				'lastname' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['lastname']],
				],
				'phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['phone']],
				],
				'email' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['email']],
				],
				'city' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['city']],
				],
				'state' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['state']],
				],
				'country' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['country']],
				],
				'shippingCost' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingCost']],
				],
				'shippingDiscount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingDiscount']],
				],
			];
			// Products
			$order['PRODUCTS'] = [];
			foreach ($mp_order['products'] as $item) {
                $order['PRODUCTS'][] = [
                    'PRODUCT_NAME'     => $item['info']['descriptions'][0]['name'],
                    'PRODUCT_CODE'     => $item['info']['descriptions'][0]['code'],
                    'PRICE'            => $item['price'],
                    'CURRENCY'         => 'RUB',
                    'QUANTITY'         => $item['amount'],
                    'DISCOUNT_TYPE_ID' => 1,
                    'DISCOUNT_SUM'     => $item['price'],
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
