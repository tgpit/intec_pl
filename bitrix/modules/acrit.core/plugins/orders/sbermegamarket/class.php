<?
/**
 * Acrit Core: Orders integration plugin for SberMegaMarket
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\SbermegamarketHelpers\Orders,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Sbermegamarket extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected static $arOrders = [];
	
	//protected static $delault_user_email = '';

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
		return 'SBERMEGAMARKET';
	}

    public function feedBack(){
        $list['ACTION'][] = [
            'id' => 'CONFIRM',
            'name' => Loc::getMessage(self::getLangCode('CONFIRM_NAME')),
        ];
//        $list['ACTION'][] = [
//            'id' => 'CANCEL',
//            'name' => Loc::getMessage(self::getLangCode('CANCEL_NAME')),
//        ];
        return $list;
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
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-sbermegamarket/';
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
            'id' => 'NOT',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_NOT')),
        ];
        $list[] = [
            'id' => 'MERCHANT_CANCELED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_MERCHANT_CANCELED')),
        ];
		$list[] = [
			'id' => 'NEW',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_NEW')),
		];
        $list[] = [
            'id' => 'PENDING ',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING')),
        ];
        $list[] = [
            'id' => 'PENDING_CONFIRMATION ',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING_CONFIRMATION')),
        ];
        $list[] = [
			'id' => 'CONFIRMED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CONFIRMED')),
		];
        $list[] = [
            'id' => 'PENDING_PACKING',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING_PACKING')),
        ];
        $list[] = [
            'id' => 'PACKED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PACKED')),
        ];
		$list[] = [
			'id' => 'PENDING_SHIPPING',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING_SHIPPING')),
		];
        $list[] = [
            'id' => 'SHIPPED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_SHIPPED')),
        ];
        $list[] = [
            'id' => 'PACKING_EXPIRED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PACKING_EXPIRED')),
        ];
        $list[] = [
            'id' => 'SHIPPING_EXPIRED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_SHIPPING_EXPIRED')),
        ];
		$list[] = [
			'id' => 'DELIVERED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERED')),
		];
		$list[] = [
			'id' => 'CUSTOMER_CANCELED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CUSTOMER_CANCELED')),
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
//		$list['user']['items'][] = [
//			'id' => 'fio',
//			'name' => Loc::getMessage(self::getLangCode('CONTACT_CUSTOMER_FIO')),
//			'direction' => self::SYNC_STOC,
//		];
//		$list['user']['items'][] = [
//			'id' => 'address',
//			'name' => Loc::getMessage(self::getLangCode('CONTACT_ADDRESS')),
//			'direction' => self::SYNC_STOC,
//		];
        $list['user']['items'] = self::getFields();
        return $list;
	}

	/**
	 * Store fields for deal fields
	 * @return array
	 */
	public function getFields() {
        $list = parent::getFields();
		$list[] = [
			'id' => 'shipmentId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPMENTID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'orderCode',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERCODE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingPoint',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGPOINT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'customerFullName',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CUSTOMERFULLNAME')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'customerAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CUSTOMERADDRESS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'confirmedTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CONFIRMEDTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'packingTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_PACKINGTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippingTimeLimit',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPINGTIMELIMIT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'creationDate',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CREATIONDATE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryDateFrom',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYDATEFROM')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'deliveryDateTo',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYDATETO')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shippmentDateFrom',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPPMENTDATEFROM')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'shipmentDateTo',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_SHIPMENTDATETO')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'firstName',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_FIRSTNAME')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'lastName',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_LASTNAME')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'middleName',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_MIDDLENAME')),
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
            'id' => 'comment',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_COMMENT')),
            'direction' => self::SYNC_STOC,
        ];

		return $list;
	}

    public function showSpecial()
    {
        ob_start(); ?>
        <tr class="heading">
            <td>
                <span><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_SBER_PRICE_SECTION');?></span>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <input type="hidden" name="PROFILE[OTHER][SBER][PRICE]" value="N" />
                    <label>
                        <input type="checkbox" name="PROFILE[OTHER][SBER][PRICE]" value="Y"
                            <?if($this->arProfile['OTHER']['SBER']['PRICE'] == 'Y'):?> checked="Y"<?endif?>
                        />
                        <span><?=Loc::getMessage('ACRIT_ORDERS_PLUGIN_SBER_PRICE');?></span>
                    </label>
                    <?=Helper::showHint(Loc::getMessage('ACRIT_ORDERS_PLUGIN_SBER_PRICE_NOTICE'));?>
                </div>
            </td>
        </tr>
        <?
        return ob_get_clean();
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
		$count = false;
	    if ($create_from_ts) {
		    $api = $this->getApi();
		    $filter = [
			    'dateFrom' => date(Orders::DATE_FORMAT, $create_from_ts),
		    ];
		    $count = $api->getOrdersCount($filter, 1000);
	    }
		return $count;
	}

    /**
     * Operate orders
     */
	public function operateOrder($conf_arr) {
	    $list = [];
        $api = $this->getApi();
        $arr_confirm = [];
        foreach ($conf_arr as $item) {
            // file_put_contents(__DIR__ . '/item.txt', var_export($item, true));
            $items = [];
            foreach ($item['ITEMS'] as $product) {
                if ( $product['conf'] === 'true' ) {
                    $items[] = [
                        'itemIndex' => $product['itemIndex'],
                        'offerId' => $product['marketId']
                    ];
                }
            }
            if (!empty($items)) {
                $arr_confirm = [
                    'shipments' => [
                        [
                            'shipmentId' => $item['ID_MARKET'],
                            'orderCode' => $item['ID_SALE'],
                            'items' => $items,
                        ]
                    ]
                ];
                $list[$item['ID_MARKET']] = $api->operateOrder($arr_confirm);
            }
        }

        $res = [];
        foreach ($list as $key=>$item) {
            if ( $item['success'] == 1 )  {
                $req[$key] = true;
            } else {
                $req[$key] = false;
            }
        }
//        return $res;
        return $list;
    }
    /**
     * Get orders listNew
     */

    public function getOrdersListNew( $date_from, $date_to ) {
        $list = [];
        // Get the list
        $req_filter = [];
        if ($date_from || $date_to) {
            $req_filter = [
                'dateFrom' => date(Orders::DATE_FORMAT, $date_from),
                'dateTo' => date(Orders::DATE_FORMAT, $date_to),
                'statuses' => ["NEW"],
            ];
        }
        $api = $this->getApi();
        $orders_list = $api->getOrdersList($req_filter, 1000);
        foreach ($orders_list as $id) {
            $list[] = $id;
        }
        $orders = [];
        foreach ($list as $order_id ) {
            $mp_order = $api->getOrder($order_id);
            $sum = 0;
            $offers = [];
            foreach ($mp_order['items'] as $item ) {
                $sum += $item['quantity'] * $item['price'];
                $offers[] = [
                    'itemIndex' => $item['itemIndex'],
                    'price' => $item['price'],
                    'finalPrice' => $item['finalPrice'],
                    'quantity' => $item['quantity'],
                    'marketId' => $item['offerId'],
                    'name' => $item['goodsData']['name'],
                    'conf' => false,
                ];
            }
            $orders[] = [
                'ID_MARKET'   => $mp_order['shipmentId'],
                'DATE_INSERT' => date('d.m.Y', strtotime($mp_order['creationDate'])) ,
                'DATE_CONFIRMED' => date('d.m.Y', strtotime($mp_order['creationDate'])) ,
                'DATE_DELIVERY' => date('d.m.Y', strtotime($mp_order['deliveryDateTo'])) ,
                'STATUS_ID'   => $mp_order['status'],
                'SUMM_MARKET' => $sum,
                'ITEMS' => $offers,
            ];

        }
        return $orders;
    }

	/**
	 * Get orders list
	 */

	public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
		$list = [];
		// Get the list
		$req_filter = [];
		if ($create_from_ts || $change_from_ts) {
			$filter_date = $change_from_ts ? : $create_from_ts;
			$req_filter = [
				'dateFrom' => date(Orders::DATE_FORMAT, $filter_date),
			];
		}
		$api = $this->getApi();
		$orders_list = $api->getOrdersList($req_filter, 1000);
		foreach ($orders_list as $id) {
			$list[] = $id;
		}
		return $list;
	}
	
	/**
	 * Get default user email
	 */
	public function getDefaultUserEmail() {
		$default_buyer_id = $this->arProfile['CONNECT_DATA']['buyer'];
		
		if ( isset($default_buyer_id) && $default_buyer_id != '' )
		{
			//file_put_contents(__DIR__.'/default_buyer_2_pre.txt', var_export($this->arProfile['CONNECT_DATA']['delault_user_email'], true) . PHP_EOL, FILE_APPEND);			
			//if ( !isset($this->delault_user_email) || $this->delault_user_email == '' )
			if ( !isset($this->arProfile['CONNECT_DATA']['delault_user_email']) || $this->arProfile['CONNECT_DATA']['delault_user_email'] == '' )
			{
				$filter = array( "ID" => $default_buyer_id );
				$rsUsers = \CUser::GetList(($by="personal_country"), ($order="desc"), $filter);
				if ( $arUser = $rsUsers->Fetch() )
				{
					//$this->delault_user_email == $arUser['EMAIL'];
					$this->arProfile['CONNECT_DATA']['delault_user_email'] = $arUser['EMAIL'];
					//file_put_contents(__DIR__.'/default_buyer_2.txt', var_export($this->arProfile['CONNECT_DATA']['delault_user_email'], true) . PHP_EOL, FILE_APPEND);
				}
			}
			
		}
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$order = false;
		// Order data
		$api = $this->getApi();
		$mp_order = $api->getOrder($order_id);
		$this->getDefaultUserEmail();
				
		if ($mp_order) {
		    $status = $mp_order['status'] ? $mp_order['status'] : 'NOT';
			// Main fields
			$order = [
				'ID'          => $mp_order['shipmentId'],
				'DATE_INSERT' => strtotime($mp_order['creationDate']),
				'STATUS_ID'   => $status,
				'IS_CANCELED' => false,
			];
			// User data
			$order['USER'] = [
				'fio' => $mp_order['customerFullName'],
				'address'  => $mp_order['customerAddress'],
			];					
			
			//file_put_contents(__DIR__ . '/order_users.txt', var_export($order['USER'], true).PHP_EOL, FILE_APPEND);
			//    [] => 2021-09-22T12:00:00+03:00
			//    [] => 2021-09-23T10:00:00+03:00
			//    [] => 2021-09-23T20:00:00+03:00
			//    [] => 2021-09-23T10:00:00+03:00
			//    [] => 2021-09-23T20:00:00+03:00
			//    [] => 910669351
			//    [] => 1
			//    [] => 1
			// Fields
			$order['FIELDS'] = [
				'orderCode' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['orderCode']],
				],
				'shipmentId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentId']],
				],
				'confirmedTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['confirmedTimeLimit']],
				],
				'packingTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['packingTimeLimit']],
				],
				'shippingTimeLimit' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingTimeLimit']],
				],
				'shipmentDateFrom' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateFrom']],
				],
				'shipmentDateTo' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateTo']],
				],
				'deliveryId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryId']],
				],
				'shipmentDateShift' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentDateShift']],
				],
				'shipmentIsChangeable' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shipmentIsChangeable']],
				],
				'customerFullName' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customerFullName']],
				],
				'customerAddress' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customerAddress']],
				],
				'shippingPoint' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['shippingPoint']],
				],
				'creationDate' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['creationDate']],
				],
				'deliveryDate' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDate']],
				],
				'deliveryDateFrom' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDateFrom']],
				],
				'deliveryDateTo' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryDateTo']],
				],
				'deliveryMethodId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['deliveryMethodId']],
				],
				'serviceScheme' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['serviceScheme']],
				],
				'customer' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['customer']],
				],
				'depositedAmount' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['depositedAmount']],
				],
                'firstName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['firstName']],
                ],
                'lastName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['lastName']],
                ],
                'middleName' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['middleName']],
                ],
                'email' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['email']],
                ],
                'phone' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['phone']],
                ],
                'comment' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['customer']['comment']],
                ],
			];
			
			//$mp_order['customerFullName'] = ''; //test
			
			if ( !isset($mp_order['customerFullName']) || $mp_order['customerFullName'] == '' )
			{				
				$order['FIELDS']['customerFullName'] = [
					'TYPE'  => 'STRING',
					'VALUE' => [$this->arProfile['CONNECT_DATA']['delault_user_email']]
				];
			}		
			
			//file_put_contents(__DIR__.'/orders_data_1.txt', var_export($order, true) . PHP_EOL, FILE_APPEND);
			
			// Products
			$order['PRODUCTS'] = [];
			$products_list = [];
			foreach ($mp_order['items'] as $item) {
			    if (!$item['offerId'] || $item['offerId'] == '') {
//                    file_put_contents(__DIR__ . '/mp_order.txt', date("m.d.y H:i:s") . ' - ' . json_encode($mp_order) . PHP_EOL, FILE_APPEND);
                    return false;
                }
                // Unite same products
                if (isset($products_list[$item['offerId']])) {
	                $products_list[$item['offerId']]['QUANTITY'] += $item['quantity'];
                }
                else {
                    if ( $this->arProfile['OTHER']['SBER']['PRICE'] === 'Y' ) {
                        $products_list[$item['offerId']] = [
                            'PRODUCT_NAME' => $item['goodsData']['name'],
                            'PRODUCT_CODE' => $item['offerId'],
                            'PRICE' => $item['price'],
                            'CURRENCY' => 'RUB',
                            'QUANTITY' => $item['quantity'],
                            'DISCOUNT_TYPE_ID' => 1,
                            'DISCOUNT_SUM' => 0,
                            'MEASURE_CODE' => 0,
                            'TAX_RATE' => 0,
                            'TAX_INCLUDED' => 'Y',
                        ];
                    } else {
                        $products_list[$item['offerId']] = [
                            'PRODUCT_NAME' => $item['goodsData']['name'],
                            'PRODUCT_CODE' => $item['offerId'],
                            'PRICE' => $item['finalPrice'],
                            'CURRENCY' => 'RUB',
                            'QUANTITY' => $item['quantity'],
                            'DISCOUNT_TYPE_ID' => 1,
                            'DISCOUNT_SUM' => ($item['price'] - $item['finalPrice']),
                            'MEASURE_CODE' => 0,
                            'TAX_RATE' => 0,
                            'TAX_INCLUDED' => 'Y',
                        ];
                    }
                }
			}
			foreach ($products_list as $item) {
				$order['PRODUCTS'][] = $item;
			}
			if (!$order['PRODUCTS'][0]['PRODUCT_CODE'] || $order['PRODUCTS'][0]['PRODUCT_CODE'] == '' ) {
				Log::getInstance($this->getModuleId())->add('(syncByPeriod) empty product code for sber order ' . print_r($mp_order, true), false, true);
//                file_put_contents(__DIR__ . '/mp_order2.txt', date("m.d.y H:i:s") . ' - ' . json_encode($mp_order) . PHP_EOL, FILE_APPEND);
			}
            $order = self::formatOrder($order);
		}
		return $order;
	}
}
