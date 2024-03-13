<?
/**
 * Acrit Core: Orders integration plugin for Kaspi
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
	\Acrit\Core\Orders\Plugins\KaspiHelpers\Orders;

Loc::loadMessages(__FILE__);

class Kaspi extends Plugin {

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
		return 'KASPI';
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
		$list = [];
        $list[] = [
            'id' => 'APPROVED_BY_BANK',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_APPROVED_BY_BANK')),
        ];
        $list[] = [
            'id' => 'ACCEPTED_BY_MERCHANT',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_ACCEPTED_BY_MERCHANT')),
        ];
        $list[] = [
            'id' => 'COMPLETED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_COMPLETED')),
        ];
        $list[] = [
            'id' => 'CANCELLED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELLED')),
        ];
        $list[] = [
            'id' => 'CANCELLING',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELLING')),
        ];
        $list[] = [
            'id' => 'KASPI_DELIVERY_RETURN_REQUESTED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_KASPI_DELIVERY_RETURN_REQUESTED')),
        ];
        $list[] = [
            'id' => 'RETURN_ACCEPTED_BY_MERCHANT',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_RETURN_ACCEPTED_BY_MERCHANT')),
        ];
        $list[] = [
            'id' => 'RETURNED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_RETURNED')),
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
			'id' => 'buyer_name',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_NAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'buyer_phone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'order_id',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDER_ID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'creationDate',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATED_AT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'formattedAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_ADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'firstName',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_FIRST_NAME')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'lastName',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_LAST_NAME')),
            'direction' => self::SYNC_STOC,
        ];
		$list[] = [
			'id' => 'cellPhone',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_PHONE')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'status',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'waybill',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_WAYBILL')),
            'direction' => self::SYNC_STOC,
        ];
		return $list;
	}

	public function getTokenLink() {
		$link = "https://kaspi.kz/mc/#/settings?activeTab=5";
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
					<?=Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_HINT')));?>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('SETTINGS_TOKEN'));?></span>:
				</td>
				<td width="60%" class="adm-detail-content-cell-r">
                    <div id="acrit-module-update-notifier">
                        <div class="acrit-exp-note-compact">
                            <div class="adm-info-message-wrap">
                                <div class="adm-info-message"><?=Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_HELP'));?></div>
                            </div>
                        </div>
                    </div>
                    <p><a href="<?=$this->getTokenLink();?>" target="_blank"><?=Loc::getMessage(self::getLangCode('SETTINGS_GET_TOKEN'));?></a></p>
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" data-role="connect-cred-token"
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
//		$api = $this->getApi();
//	    $count = $api->getOr($create_from_ts);
//        self::getOrdersIDsList($create_from_ts);
	    return count($this->getOrdersIDsList($create_from_ts));
	}


	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
        try {
            $list = [];
            // Get the list
            if ( !$create_from_ts && !$change_from_ts ) {
                $filter_date = time() - 86400 * 150;
            } else {
                $filter_date = $create_from_ts ? $create_from_ts : $change_from_ts;
            }
            $filter_date = $filter_date * 1000;
            $api = $this->getApi();
            $this->arOrders = $api->getList($filter_date);
            foreach ($this->arOrders as $key=>$item) {
                $list[] = $key;
            }
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
			$this->addToLog(var_export($errors, true));
        }
	    return $list;
	}


	/**
	 * Get order
	 */

	public function getOrder($order_id) {
	    $order = false;
		$api = $this->getApi();
	    $ext_order = $this->arOrders[$order_id];
	    $products = $api->getProducts($ext_order['id']);
	    if ($ext_order['attributes']['code']) {
		    // Check encoding
//		    if (!Helper::isUtf()) {
//			    $ext_order['buyer_name'] = Helper::convertEncoding($ext_order['buyer_name']);
//			    $ext_order['delivery_address'] = Helper::convertEncoding($ext_order['delivery_address']);
//		    }
	        // Main fields
		    $order = [
			    'ID'          => $ext_order['attributes']['code'],
			    'DATE_INSERT' => (int) floor($ext_order['attributes']['creationDate'] / 1000 ) ,
			    'STATUS_ID'   => $ext_order['attributes']['status'],
			    'IS_CANCELED' => false,
		    ];
		    // User data
		    $order['USER'] = [
			    'first_name' => $ext_order['attributes']['customer']['firstName'],
			    'phone'  => $ext_order['attributes']['customer']['cellPhone'],
            ];
            // Fields
		    $order['FIELDS'] = [
                'order_id' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => $ext_order['attributes']['code'],
                ],
                'creationDate' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [ date('m/d/Y H:i:s', $ext_order['attributes']['creationDate'] / 1000 ) ],
                ],
                'status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['attributes']['status']],
                ],
                'delivery_address' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['delivery_address']],
                ],
                'firstName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['attributes']['customer']['firstName']],
                ],
                'lastName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['attributes']['customer']['lastName']],
                ],
                'cellPhone' => [
	                'TYPE'  => 'STRING',
	                'VALUE' => [$ext_order['attributes']['customer']['cellPhone']],
                ],
                'waybill' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['attributes']['kaspiDelivery']['waybill']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    foreach ($products as $item) {
			    if (!Helper::isUtf()) {
				    $item['name'] = Helper::convertEncoding($item['name']);
			    }
                $order['PRODUCTS'][] = [
	                'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['sku_code'],
					'PRICE'            => $item['item_price'],
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
//        file_put_contents(__DIR__.'/produt.txt', var_export($products, true));
//        file_put_contents(__DIR__.'/order.txt', var_export($order, true));
//	    return [];
	    return $order;
	}

}
