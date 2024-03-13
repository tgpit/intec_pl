<?
/**
 * Acrit Core: Orders integration plugin for Leroy Merlin
 */

namespace Acrit\Core\Orders\Plugins;

require_once __DIR__ . '/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log,
	\Acrit\Core\Orders\Plugins\PetrovichHelpers\Orders;

Loc::loadMessages(__FILE__);

class Petrovich extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
    protected static $arOrders = [];
    protected static $token = false;

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
		return 'PETROVICH';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'Petrovich';
	}

	/**
	 * Get type of regular synchronization
	 */
	public static function getAddSyncType() {
//		return self::ADD_SYNC_TYPE_SINGLE;
		return self::ADD_SYNC_TYPE_DUAL;
	}

	/**
	 * Get plugin help link
	 */
	public static function getHelpLink() {
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/nastroyka-profiley-integratsii-s-zakazami-obshchaya-instruktsiya/';
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
			'id' => 'NAME',
			'name' => Loc::getMessage(self::getLangCode('PRODUCTS_ID_FIELD_NAME')),
		];
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
			'id' => 'buyer_name',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_BUYER_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'buyer_phone',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_BUYER_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		return $list;
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
//        delivered
//        deliveryStarted
//        shipped
//        packingCompleted
//        packingStarted
//        created
//        canceled

		$list = [];
        $list[] = [
            'id' => 'pending',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING')),
        ];
        $list[] = [
            'id' => 'shipping_date_confirmation',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CONFIRMATION')),
        ];
        $list[] = [
            'id' => 'shipping_date_confirmed',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DATE_CONFIRMED')),
        ];
        $list[] = [
            'id' => 'shipping_date_not_confirmed',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_NOT_CONFIRMED')),
        ];
        $list[] = [
            'id' => 'confirmed',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CONFIRMED')),
        ];
        $list[] = [
            'id' => 'confirmation_expired',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_EXPIRED')),
        ];
        $list[] = [
            'id' => 'start_picked',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PICKED')),
        ];
        $list[] = [
            'id' => 'canceled_supplier',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED_SUPPLIER')),
        ];
        $list[] = [
            'id' => 'ready_picked',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_READY')),
        ];
        $list[] = [
            'id' => 'transf_on_dock',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_TRANSF')),
        ];
        $list[] = [
            'id' => 'accepted_on_dock',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_ACCEPTED')),
        ];
        $list[] = [
            'id' => 'shipment_overdue',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_OVERDUE')),
        ];
        $list[] = [
            'id' => 'complete',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_COMPLETE')),
        ];
        $list[] = [
            'id' => 'canceled',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED')),
        ];
        $list[] = [
            'id' => 'refusal',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_REFUSAL')),
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
			'id' => 'warehouseId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_WAREHOUSEID')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'warehouseName',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_WAREHOUSENAME')),
            'direction' => self::SYNC_STOC,
        ];
		$list[] = [
			'id' => 'order_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDER_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'created_at',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATED_AT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];


		return $list;
	}

	public function getTokenLink() {
		$link = "https://seller.aliexpress.ru/token-management/active";
		return $link;
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
                    <?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_USERNAME_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_USERNAME'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][username]" size="50" maxlength="255" data-role="connect-cred-username"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['username']);?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
                    <?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_PASSWORD_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_PASSWORD'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][password]" size="50" maxlength="255" data-role="connect-cred-password"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['password']);?>" />
                    <a class="adm-btn" data-role="connection-check"><?=Loc::getMessage(self::getLangCode('SETTINGS_CHECK_TOKEN'));?></a>
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
//			    $connect['api_key'] = $arParams['POST']['api_key'];
                $connect['username'] = $arParams['POST']['username'];
                $connect['password'] = $arParams['POST']['password'];
//                $connect['client_id'] = $arParams['POST']['client_id'];
//                $connect['client_secret'] = $arParams['POST']['client_secret'];
				$message = '';
                $api = $this->getApi();
				$res = $api->checkConnection($connect, $message);
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
	 *	Regular synchronization interval modifications
	 */
	public function modifSyncInterval($sync_interval) {
	    // Min interval for search orders
		return $sync_interval + 12*3600;
	}


	/**
	 * Get orders count
	 */

	public function getOrdersCount($create_from_ts) {
	    $date = $create_from_ts;
        $api = $this->getApi();
        $result = $api->getJwt();
        $jwt  =  'Bearer '.$result['token'];
        $orders_list = $api->getList( $date, $jwt );
	    return count($orders_list);
	}


	/**
	 * Get orders count
	 */

    public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
        try {
//            $list = [];
            // Get the list
            $date =  $create_from_ts ? $create_from_ts : $change_from_ts;
            $api = $this->getApi();
            $result = $api->getJwt();
            $jwt  =  'Bearer '.$result['token'];
            self::$token = $jwt;
            self::$arOrders = $api->getList( $date, $jwt );

            foreach (self::$arOrders as $key=>$item) {
                $list[] = $key;
            }
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
            $this->addToLog(var_export($errors, true));
        }

        file_put_contents(__DIR__ . '/arorders.txt', var_export(self::$arOrders, true));
        return $list;
//	    return ['ТВЭ00020506'];
    }


	/**
	 * Get order
	 */

	public function getOrder($order_id) {
	    $order = false;
        $api = $this->getApi();
        $ext_order = self::$arOrders[$order_id];
        $products = $api->getProducts(self::$arOrders[$order_id]['id_mp'], self::$token);
//        $customer = $api->getCustomer($order_id, self::$token);
//        file_put_contents(__DIR__.'/ext_order.txt', var_export($ext_order, true) );
//        return false;
	    if ($ext_order['number']) {
	        // Main fields
		    $order = [
			    'ID'          => $ext_order['number'],
			    'DATE_INSERT' => strtotime($ext_order['createdAt']),
			    'STATUS_ID'   => $ext_order['statusCode'],
			    'IS_CANCELED' => false,
		    ];
		    // User data
//		    $order['USER'] = [
//			    'first_name' => $ext_order['buyer_name'],
//			    'phone'  => $ext_order['buyer_phone'],
//			    'country'    => $ext_order['buyer_country_code'],
//            ];
            // Fields
		    $order['FIELDS'] = [
                'order_id' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['number']],
                ],
                'created_at' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['orderCreatedAt']],
                ],
                'status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['statusTitle']],
                ],
                'warehouseId' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['warehouseGuid']],
                ],
                'warehouseName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['warehouseTitle']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    foreach ($products as $item) {
                $order['PRODUCTS'][] = [
//	                'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['sku'],
					'PRICE'            => $item['price'],
	                'CURRENCY'         => 'RUB',
	                'QUANTITY'         => $item['quant'],
	                'DISCOUNT_TYPE_ID' => 1,
	                'DISCOUNT_SUM'     => 0,
	                'MEASURE_CODE'     => 0,
	                'TAX_RATE'         => 0,
	                'TAX_INCLUDED'     => 'Y',
                ];
		    }
		    $order = self::formatOrder($order);
	    }
//        file_put_contents(__DIR__.'/order.txt', var_export($order, true) );
	    return $order;
	}

}
