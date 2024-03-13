<?
/**
 * Acrit Core: AliExpress crm integration plugin
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__ . '/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log,
	\Acrit\Core\Orders\Plugins\AliexpressLocHelpers\Orders;

Loc::loadMessages(__FILE__);

class AliExpressLoc extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];

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
		return 'ALIEXPRESSLOC';
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
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-aliexpress/';
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
            'id' => 'Created',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CREATED')),
        ];
        $list[] = [
            'id' => 'InProgress',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_INPROGRESS')),
        ];
        $list[] = [
            'id' => 'Finished',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_FINISHED')),
        ];
        $list[] = [
            'id' => 'Cancelled',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED')),
        ];
//		$list[] = [
//			'id' => 'Unknown',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_UNKNOWN')),
//		];
//		$list[] = [
//			'id' => 'PlaceOrderSuccess',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_PLACEORDERSUCCESS')),
//		];
//		$list[] = [
//			'id' => 'PaymentPending',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_PAYMENTPENDING')),
//		];
//		$list[] = [
//			'id' => 'WaitExamineMoney',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_WAITEXAMINEMONEY')),
//		];
//		$list[] = [
//			'id' => 'WaitGroup',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_WAITGROUP')),
//		];
//		$list[] = [
//			'id' => 'WaitSendGood',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_WAITSENDGOOD')),
//		];
//		$list[] = [
//			'id' => 'PartialSendGoods',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_PARTIALSENDGOODS')),
//		];
//		$list[] = [
//			'id' => 'WaitAcceptGoods',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_WAITACCEPTGOODS')),
//		];
//		$list[] = [
//			'id' => 'InCancel',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_INCANCEL')),
//		];
//		$list[] = [
//			'id' => 'Complete',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_COMPLETE')),
//		];
//		$list[] = [
//			'id' => 'Close',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_CLOSE')),
//		];
//		$list[] = [
//			'id' => 'Finish',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_FINISH')),
//		];
//		$list[] = [
//			'id' => 'InFrozen',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_INFROZEN')),
//		];
//		$list[] = [
//			'id' => 'InIssue',
//			'name' => Loc::getMessage(self::getLangCode('STATUSES_INISSUE')),
//		];
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
			'id' => 'buyer_country_code',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_COUNTRY_CODE')),
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
			'id' => 'paid_at',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAID_AT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'payment_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PAYMENT_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'delivery_address',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERY_ADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'antifraud_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ANTIFRAUD_STATUS')),
			'direction' => self::SYNC_STOC,
		];
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
			'id' => 'buyer_country_code',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BUYER_COUNTRY_CODE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'order_display_status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDER_DISPLAY_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'total_amount',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TOTAL_AMOUNT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'seller_comment',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SELLER_COMMENT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'fully_prepared',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FULLY_PREPARED')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'finish_reason',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FINISH_REASON')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'cut_off_date',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CUT_OFF_DATE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shipping_deadline',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPING_DEADLINE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'next_cut_off_date',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_NEXT_CUT_OFF_DATE')),
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
//		$order = $this->getOrder(8018196774972936);
//		echo '<pre>'; print_r($order); echo '</pre>';
//		Settings::setModuleId($this->strModuleId);
//		Controller::setModuleId($this->strModuleId);
//		Controller::setProfile($arProfile['ID']);
//		Controller::syncOrderToDeal($order);
//		Controller::syncStoreToCRM(60);
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
		$api = $this->getApi();
	    $count = $api->getCount($create_from_ts);
	    return $count;
	}


	/**
	 * Get orders count
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
        try {
            $list = [];
            // Get the list
            $filter = [];
            if ($create_from_ts) {
                $filter[Orders::FILTER_CREATED_FROM_FIELD] = $create_from_ts;
            }
            if ($change_from_ts) {
                $filter[Orders::FILTER_UPDATED_FROM_FIELD] = $change_from_ts;
            }
            $api = $this->getApi();
            $orders_list = $api->getList($filter);
            foreach ($orders_list as $item) {
                $list[] = $item['id'];
            }
            sort($list);
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
	    $ext_order = $api->getById($order_id);
	    if ($ext_order['id']) {
		    // Check encoding
		    if (!Helper::isUtf()) {
			    $ext_order['buyer_name'] = Helper::convertEncoding($ext_order['buyer_name']);
			    $ext_order['delivery_address'] = Helper::convertEncoding($ext_order['delivery_address']);
		    }
	        // Main fields
		    $order = [
			    'ID'          => $ext_order['id'],
			    'DATE_INSERT' => strtotime($ext_order['created_at']),
			    'STATUS_ID'   => $ext_order['status'],
			    'IS_CANCELED' => false,
		    ];
		    // User data
		    $order['USER'] = [
			    'first_name' => $ext_order['buyer_name'],
			    'phone'  => $ext_order['buyer_phone'],
			    'country'    => $ext_order['buyer_country_code'],
            ];
            // Fields
		    $order['FIELDS'] = [
                'order_id' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['id']],
                ],
                'created_at' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['created_at']],
                ],
                'paid_at' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['paid_at']],
                ],
                'status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['status']],
                ],
                'payment_status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['payment_status']],
                ],
                'delivery_status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['delivery_status']],
                ],
                'delivery_address' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['delivery_address']],
                ],
                'antifraud_status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['antifraud_status']],
                ],
                'buyer_name' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['buyer_name']],
                ],
                'buyer_phone' => [
	                'TYPE'  => 'STRING',
	                'VALUE' => [$ext_order['buyer_phone']],
                ],
                'buyer_country_code' => [
	                'TYPE'  => 'STRING',
	                'VALUE' => [$ext_order['buyer_country_code']],
                ],
                'order_display_status' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['order_display_status']],
                ],
                'total_amount' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['total_amount']],
                ],
                'seller_comment' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['seller_comment']],
                ],
                'fully_prepared' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['fully_prepared']],
                ],
                'finish_reason' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['finish_reason']],
                ],
                'cut_off_date' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['cut_off_date']],
                ],
                'shipping_deadline' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['shipping_deadline']],
                ],
                'next_cut_off_date' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$ext_order['next_cut_off_date']],
                ],
		    ];
            // Products
		    $order['PRODUCTS'] = [];
		    foreach ($ext_order['order_lines'] as $item) {
			    if (!Helper::isUtf()) {
				    $item['name'] = Helper::convertEncoding($item['name']);
			    }
                $order['PRODUCTS'][] = [
	                'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $item['sku_code'],
					'PRICE'            => $item['item_price'] / 100,
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
