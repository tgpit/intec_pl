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
	\Acrit\Core\Orders\Plugins\LeroymerlinHelpers\Orders;

Loc::loadMessages(__FILE__);

class Leroymerlin extends Plugin {

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
		return 'LEROYMERLIN';
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
            'id' => 'created',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CREATED')),
        ];
        $list[] = [
            'id' => 'packingStarted',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PACKINGSTARTED')),
        ];
        $list[] = [
            'id' => 'packingCompleted',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PACKINGCOMPLETED')),
        ];
        $list[] = [
            'id' => 'shipped',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_SHIPPED')),
        ];
        $list[] = [
            'id' => 'deliveryStarted',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERYSTARTED')),
        ];
        $list[] = [
            'id' => 'delivered',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERED')),
        ];
        $list[] = [
            'id' => 'canceled',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED')),
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
			'id' => 'deliveryServiceId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYSERVICEID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryServiceName',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYSERVICENAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'warehouseId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_WAREHOUSEID')),
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
			'id' => 'pickupDate',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PICKUPDATE')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'deliveryCost',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYCOST')),
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
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
                    <?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_ID_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_ID'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][client_id]" size="50" maxlength="255" data-role="connect-cred-client_id"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['client_id']);?>" />
                </td>
            </tr>
            <tr>
                <td width="40%" class="adm-detail-content-cell-l">
                    <?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_SECRET_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_SECRET'));?></span>:
                </td>
                <td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][client_secret]" size="50" maxlength="255" data-role="connect-cred-client_secret"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['client_secret']);?>" />
                </td>
            </tr>
			<tr>
				<td width="40%" class="adm-detail-content-cell-l">
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_API_KEY_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_API_KEY'));?></span>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <input type="text" name="PROFILE[CONNECT_CRED][api_key]" size="50" maxlength="255" data-role="connect-cred-api_key"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['api_key']);?>" />
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
			    $connect['api_key'] = $arParams['POST']['api_key'];
                $connect['username'] = $arParams['POST']['username'];
                $connect['password'] = $arParams['POST']['password'];
                $connect['client_id'] = $arParams['POST']['client_id'];
                $connect['client_secret'] = $arParams['POST']['client_secret'];
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
        $jwt  =  'Bearer '.$result['access_token'];
        $orders_list = $api->getList( $date, $jwt );
	    return count($orders_list);
	}


	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
        try {

            $list = [];
            // Get the list

            $date =  $create_from_ts ? $create_from_ts : $change_from_ts;
            $api = $this->getApi();
            $result = $api->getJwt();
            $jwt  =  'Bearer '.$result['access_token'];

            self::$arOrders = $api->getList( $date, $jwt );

            foreach (self::$arOrders as $item) {
                $list[] = $item['id'];
            }
//            foreach ( $list as $item ) {
//                $arStatus = $api->getStatus($item, $jwt);
//                self::$arOrders[$item]['status'] = $arStatus[0]['name'];
//            }
//            file_put_contents(__DIR__.'/orders.txt', var_export( self::$arOrders, true) );
//            file_put_contents(__DIR__.'/list.txt', var_export( $list, true) );
//            sort($list);
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
			$this->addToLog(var_export($errors, true));
        }
	    return $list;
//	    return [];
	}
    public function getStatus($order_id) {
        $api = $this->getApi();
        $result = $api->getJwt();
        $jwt  =  'Bearer '.$result['access_token'];
        $arStatus = $api->getStatus($order_id, $jwt);
        return $arStatus[0]['name'];
    }

	/**
	 * Get order
	 */

	public function getOrder($order_id) {
	    $order = false;
	    $ext_order = self::$arOrders[$order_id];
	    $status = self::getStatus($order_id);
	    if ($ext_order['id']) {
		    // Check encoding
//		    if (!Helper::isUtf()) {
//			    $ext_order['buyer_name'] = Helper::convertEncoding($ext_order['buyer_name']);
//			    $ext_order['delivery_address'] = Helper::convertEncoding($ext_order['delivery_address']);
//		    }
	        // Main fields
		    $order = [
			    'ID'          => $ext_order['id'],
			    'DATE_INSERT' => strtotime($ext_order['creationDate']),
//			    'STATUS_ID'   => $ext_order['status'],
			    'STATUS_ID'   => $status,
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
                    'VALUE' => [$ext_order['id']],
                ],
                'created_at' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['creationDate']],
                ],
                'status' => [
                    'TYPE'  => 'STRING',
//                    'VALUE' => [$ext_order['status']],
                    'VALUE' => [$status],
                ],
                'deliveryServiceId' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['pickup']['deliveryServiceId']],
                ],
                'deliveryServiceName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['pickup']['deliveryServiceName']],
                ],
                'warehouseId' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['pickup']['warehouseId']],
                ],
                'pickupDate' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['pickup']['pickupDate']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    foreach ($ext_order['products'] as $item) {
                $order['PRODUCTS'][] = [
//	                'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['vendorCode'],
					'PRICE'            => $item['price'],
	                'CURRENCY'         => 'RUB',
	                'QUANTITY'         => $item['qty'],
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
