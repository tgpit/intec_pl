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
	\Acrit\Core\Orders\Plugins\SatuKzHelpers\Orders;

Loc::loadMessages(__FILE__);

class SatuKz extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
    protected $arOrders = [];

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
		return 'SATUKZ';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'SatuKz(Beta)';
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
		$list = [];
        $list[] = [
            'id' => 'pending',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING')),
        ];
        $list[] = [
            'id' => 'received',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_RECEIVED')),
        ];
        $list[] = [
            'id' => 'delivered',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERED')),
        ];
        $list[] = [
            'id' => 'canceled',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED')),
        ];
        $list[] = [
            'id' => 'paid',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PAID')),
        ];
        $list[] = [
            'id' => 'draft',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DRAFT')),
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
        $list[] = [
            'id' => 'status_name',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS_NAME')),
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
            'id' => 'client_full_name',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_FULL_NAME')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'email',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_EMAIL')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'phone',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_PHONE')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'delivery_address',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_DELIVERY_ADDRESS')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'client_notes',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_CLIENT_NOTES')),
            'direction' => self::SYNC_STOC,
        ];
		return $list;
	}

	public function getTokenLink() {
//		$link = "https://seller.aliexpress.ru/token-management/active";
		return '';
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
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="400" data-role="connect-cred-token"
                           value="<?=htmlspecialcharsbx($arProfile['CONNECT_CRED']['token']);?>" />
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
        $orders_list = $api->getList( $date );
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
            $this->arOrders = array_reverse($api->getList( $date ));

            foreach ($this->arOrders as $key=>$item) {
                $list[] = $key;
            }
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
            $this->addToLog(var_export($errors, true));
        }
//        file_put_contents(__DIR__.'/arorder.txt', var_export($this->arOrders, true) );
        return $list;
    }

	/**
	 * Get order
	 */

	public function getOrder($order_id) {
	    $order = false;
        $ext_order = $this->arOrders[$order_id];
//        $api = $this->getApi();
//        $ext_order = $api->getOrder( $order_id );
        $products = $ext_order['products'];
//        file_put_contents(__DIR__.'/ext_order.txt', var_export($ext_order, true) );
	        // Main fields
		    $order = [
			    'ID'          => $ext_order['id'],
			    'DATE_INSERT' => strtotime($ext_order['date_created']),
			    'STATUS_ID'   => $ext_order['status'],
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
                    'VALUE' => [$ext_order['date_created']],
                ],
                'status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['status']],
                ],
                'status_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['status_name']],
                ],
                'client_first_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['client_first_name']],
                ],
                'client_second_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['client_second_name']],
                ],
                'client_last_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['client_last_name']],
                ],
                'client_full_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['client_last_name'].' '.$ext_order['client_first_name'].' '.$ext_order['client_second_name'] ],
                ],
                'email' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['email']],
                ],
                'phone' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['phone']],
                ],
                'delivery_address' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['delivery_address']],
                ],
                'client_notes' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['client_notes']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    foreach ($products as $item) {
                $order['PRODUCTS'][] = [
	                'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['external_id'],
					'PRICE'            => preg_replace('/[^0-9]/', '', $item['price'] ),
	                'CURRENCY'         => 'KZT',
	                'QUANTITY'         => $item['quantity'],
	                'DISCOUNT_TYPE_ID' => 1,
	                'DISCOUNT_SUM'     => 0,
	                'MEASURE_CODE'     => 0,
	                'TAX_RATE'         => 0,
	                'TAX_INCLUDED'     => 'Y',
                ];
		    }
		    $order = self::formatOrder($order);
//        file_put_contents(__DIR__.'/order.txt', var_export($order, true) );
	    return $order;
	}

}
