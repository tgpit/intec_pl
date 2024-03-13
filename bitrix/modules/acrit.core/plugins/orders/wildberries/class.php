<?
/**
 * Acrit Core: Orders integration plugin for Wildberries
 * Documentation: https://suppliers-api.wildberries.ru/swagger/index.html#/Marketplace/get_api_v2_orders
 */

namespace Acrit\Core\Crm\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper,
	\Acrit\Core\Orders\Plugin,
	\Acrit\Core\Orders\Settings,
	\Acrit\Core\Orders\Controller,
	\Acrit\Core\Orders\Plugins\WildberriesHelpers\Orders,
	\Acrit\Core\HttpRequest,
	\Acrit\Core\Json,
	\Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class Wildberries extends Plugin {

	// List of available directions
	protected $arDirections = [self::SYNC_STOC];
	protected $arOrders = [];
	protected $arStatus = [];
    const LIMIT_REQ = 1000;
	public $use_v3_api = true;

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
		return 'WILDBERRIES';
	}

	/**
	 * Get plugin short name
	 */
	public static function getName() {
		return 'Wildberries';
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
		return 'https://www.acrit-studio.ru/technical-support/configuring-the-module-export-on-trade-portals/integratsiya-s-zakazami-wildberries/';
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
			'id' => 'BARCODE',
			'name' => Loc::getMessage('ACRIT_ORDERS_PLUGIN_WILDBERRIES_PRODUCTS_ID_FIELD_NAME'),
		];
	}

	/**
	 * Variants for deal statuses
	 * @return array
	 */
	public function getStatuses() {
		$list = [];
		$list[] = [
			'id' => 'WAITING',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_WAITING')),
		];
        $list[] = [
			'id' => 'SORTED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_SORTED')),
		];
		$list[] = [
			'id' => 'SOLD',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_SOLD')),
		];
		$list[] = [
			'id' => 'CANCELED',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED')),
		];
		$list[] = [
			'id' => 'CANCELED_BY_CLIENT',
			'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELED_BY_CLIENT')),
		];
        $list[] = [
            'id' => 'READY_FOR_PICKUP',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_READY_FOR_PICKUP')),
        ];
        $list[] = [
            'id' => 'DEFECT',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DEFECT')),
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
			'id' => 'fio',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_FIO')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'phone',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_PHONE')),
			'direction' => self::SYNC_STOC,
		];
		$list['user']['items'][] = [
			'id' => 'user_id',
			'name' => Loc::getMessage(self::getLangCode('CONTACT_USER_ID')),
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
			'id' => 'orderId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'dateCreated',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DATECREATED')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'office',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_OFFICE')),
            'direction' => self::SYNC_STOC,
        ];
        $list[] = [
            'id' => 'warehouseId',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_WAREHOUSEID')),
            'direction' => self::SYNC_STOC,
        ];
		$list[] = [
			'id' => 'deliveryAddress',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_DELIVERYADDRESS')),
			'direction' => self::SYNC_STOC,
		];

		$list[] = [
			'id' => 'userFio',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_FIO')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'userPhone',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_PHONE')),
            'direction' => self::SYNC_STOC,
        ];
		$list[] = [
			'id' => 'chrtId',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_CHRTID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'barcode',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_BARCODE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'status',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'statusText',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_STATUSTEXT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'userStatus',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_USERSTATUS')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'userStatusText',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_USERSTATUSTEXT')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'rid',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_RID')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'totalPrice',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_TOTALPRICE')),
			'direction' => self::SYNC_STOC,
		];
		$list[] = [
			'id' => 'orderUID',
			'name' => Loc::getMessage(self::getLangCode('FIELDS_ORDERUID')),
			'direction' => self::SYNC_STOC,
		];
        $list[] = [
            'id' => 'label',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_LABEL')),
            'direction' => self::SYNC_STOC,
        ];

		return $list;
	}

    public function getLabelParametrs(){
        return [
            'type' => [
                'png',
                'svg',
                'zplv',
                'zplh',
            ],
            'size' => [
                1 => [
                    'name' => '580х400',
                    'width' => 58,
                    'height' => 40,
                ],
                2 => [
                    'name' => '400х300',
                    'width' => 40,
                    'height' => 30
                ]
            ]
        ];
    }
    public function showSpecial()
    {
        ob_start(); ?>
        <tr class="heading">
            <td>
                <span><?=Loc::getMessage(self::getLangCode('EXPORT_LABEL'));?></span>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <input type="hidden" name="PROFILE[OTHER][LABEL]" value="N" />
                    <label>
                        <input type="checkbox" name="PROFILE[OTHER][LABEL]" value="Y"
                            <?if($this->arProfile['OTHER']['LABEL'] == 'Y'):?> checked="Y"<?endif?>  />
                        <span><?=Loc::getMessage(self::getLangCode('LABEL_CHECKBOX'));?></span>
                    </label>
                    <?=Helper::showHint(Loc::getMessage(self::getLangCode('LABEL_HINT')));?>
                </div>
            </td>
        </tr>
        <tr></tr>
        <tr>
            <td>
                <div>
                    <span class="adm-required-field"><?=Loc::getMessage(self::getLangCode('URL'));?></span>:
                    <input placeholder="https://example.com" type="text" name="PROFILE[OTHER][URL]" size="50" maxlength="255"
                           value="<?=htmlspecialcharsbx($this->arProfile['OTHER']['URL']);?>" />
                    <?=Helper::ShowHint(Loc::getMessage(self::getLangCode('URL_HINT')));?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <div>
                    <label for="field_labeloption_type"><?=Loc::getMessage(self::getLangCode('LABEL_TYPE'))?><label>
                            <select name="PROFILE[OTHER][LABELOPTION][TYPE]">
                                <?foreach ( $this->getLabelParametrs()['type'] as $item):?>
                                    <option value="<?=$item;?>"<?=$this->arProfile['OTHER']['LABELOPTION']['TYPE']==$item?' selected':'';?>><?=$item;?></option>
                                <?endforeach;?>
                            </select>
                            <?=Helper::showHint(Loc::getMessage(self::getLangCode('LABEL_TYPE_HINT')));?>
                </div>
            </td>
        </tr>
        <td>
            <div>
                <label for="field_labeloption_size"><?=Loc::getMessage(self::getLangCode('LABEL_SIZE'))?><label>
                        <select name="PROFILE[OTHER][LABELOPTION][SIZE]">
                            <?foreach ( $this->getLabelParametrs()['size'] as $key=>$item):?>
                                <option value="<?=$key;?>"<?=$this->arProfile['OTHER']['LABELOPTION']['SIZE']==$key?' selected':'';?>><?=$item['name'];?><?=$key?(' [' . $key . ']'):'';?></option>
                            <?endforeach;?>
                        </select>
                        <?=Helper::showHint(Loc::getMessage(self::getLangCode('LABEL_SIZE_HINT')));?>
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
                    <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="400" data-role="connect-cred-token"
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
//		    $api = $this->getApi();
//		    $filter = [
//			    'date_start' => date(Orders::DATE_FORMAT, $create_from_ts),
//		    ];
//		    $count = $api->getOrdersCount($filter);
            $list = $this->getOrdersIDsList($create_from_ts);
            $count = count($list);
        }
        return $count;
    }

    /**
     * Get orders count
     */

    public function getOrdersIDsList($create_from_ts=false, $change_from_ts=false) {
        // Get the list
//        file_put_contents(__DIR__.'/date.txt', $create_from_ts);

        if ( !$create_from_ts && !$change_from_ts ) {
            $filter_date = time() - 86400 * 150;
        } else {
            $filter_date = $create_from_ts ? $create_from_ts : $change_from_ts;
        }
        $req_filter = [
            'dateFrom' => $filter_date,
        ];
        $api = $this->getApi();
        $this->arOrders = [];

        try {
            $i = 0;
            $list = [];
            $orders_list = $api->getOrdersList($req_filter, self::LIMIT_REQ );
            $list = [];
            foreach ($orders_list as $item) {
                $list[] = $item['id'];
                // Remember orders for function getOrder (because The API does not have a function of receiving a separate order)
                // RAM consumption - less than 15 Mb on 1000 orders
                $this->arOrders[$item['id']] = $item;
            }
            unset($orders_list);

            $limit = self::LIMIT_REQ;
            $count_list_for = ceil(count($list) / $limit );

            for ( $i = 1; $i < $count_list_for + 1; $i++ ) {
                $list_req = [];
                for ( $j = ( ( $i - 1 ) * $limit ) ; $j < ( $i * $limit )   && $j < count($list); $j++  ) {
                    $list_req[] = $list[$j];
                }
                $status = $api->getOrdersStatus($list_req);
                foreach ($status as $key=>$item  ) {
                    $this->arStatus[ $key ] = $item;
                }
            }
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
            // file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
        }
        return $list;
    }

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$order = false;
		// Order data
		$mp_order = $this->arOrders[$order_id];
//        file_put_contents(__DIR__ . '/order.txt', var_export($mp_order, true));
		$label = false;
		$label_option = [
		        'type' =>  $this->arProfile['OTHER']['LABELOPTION']['TYPE'],
		        'width' =>  $this->getLabelParametrs()['size'][$this->arProfile['OTHER']['LABELOPTION']['SIZE']]['width'],
		        'height' =>  $this->getLabelParametrs()['size'][$this->arProfile['OTHER']['LABELOPTION']['SIZE']]['height'],
        ];
        if ( $this->arProfile['OTHER']['LABEL'] == 'Y' ) {
            $status = $this->arStatus[$order_id]['supplierStatus'];
            try {
                $api = $this->getApi();
                $label = $api->getLabel($order_id, $this->arProfile['OTHER']['URL'], $status, $label_option);
            } catch (\Throwable  $e) {
                $errors = [
                    'error_php' => $e->getMessage(),
                    'line' => $e->getLine(),
                ];
                file_put_contents(__DIR__ . '/errors.txt', var_export($errors, true));
            }
        }

		if ($mp_order) {
			// Main fields
			$order = [
				'ID'          => $mp_order['id'],
				'DATE_INSERT' => strtotime($mp_order['createdAt']),
				'STATUS_ID'   => $this->arStatus[$order_id]['wbStatus'],
				'IS_CANCELED' => false,
			];
			// User data
//			$order['USER'] = [
//				'fio' => $mp_order['userInfo']['fio'],
//				'phone'  => $mp_order['userInfo']['phone'],
//				'user_id'   => $mp_order['userInfo']['userId'],
//			]; 
			// Fields
			$order['FIELDS'] = [
				'orderId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['id']],
				],
				'dateCreated' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['createdAt']],
				],
				'wbWhId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['wbWhId']],
				],
                'warehouseId' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$mp_order['warehouseId']],
                ],
                'office' => [
                'TYPE'  => 'STRING',
                'VALUE' => [$mp_order['offices'][0]],
            ],
				'deliveryAddress' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['address']['fullAddress']],
				],
//				'userId' => [
//					'TYPE'  => 'STRING',
//					'VALUE' => [$mp_order['userInfo']['userId']],
//				],
				'fio' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user']['fio']],
				],
				'phone' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['user']['phone']],
				],
				'chrtId' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['chrtId']],
				],
				'barcode' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['skus'][0]],
				],
				'status' => [
					'TYPE'  => 'STRING',
					'VALUE' => [ $this->arStatus[$order_id]['wbStatus'] ],
				],
				'statusText' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_STATUS_' .  $this->arStatus[$order_id]['wbStatus']))],
				],
				'userStatus' => [
					'TYPE'  => 'STRING',
					'VALUE' => [ $this->arStatus[$order_id]['supplierStatus']],
				],
				'userStatusText' => [
					'TYPE'  => 'STRING',
					'VALUE' => [Loc::getMessage(self::getLangCode('FIELDS_USERSTATUS_' . $this->arStatus[$order_id]['supplierStatus'] ))],
				],
				'rid' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['rid']],
				],
				'totalPrice' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['price']],
				],
				'orderUID' => [
					'TYPE'  => 'STRING',
					'VALUE' => [$mp_order['orderUID']],
				],
                'label' => [
                    'TYPE'  => 'STRING',
                    'VALUE' => [$label],
                ],
			];
			// Products
			$order['PRODUCTS'] = [];
			$products_list = $mp_order['products'];
			foreach ($products_list as $key => $item) {
				$order['PRODUCTS'][] = [
//					'PRODUCT_NAME'     => $item['name'],
					'PRODUCT_CODE'     => $key,
					'PRICE'            => $item['price'],
					'CURRENCY'         => 'RUB',
					'QUANTITY'         => $item['quantity'],
					'DISCOUNT_TYPE_ID' => 1,
					'DISCOUNT_SUM'     => 0,
					'MEASURE_CODE'     => 0,
					'TAX_RATE'         => 0,
					'TAX_INCLUDED'     => 'Y',
                    'SKUS'             => $mp_order['skus']
				];
			}
			$order = self::formatOrder($order);
		}
//        file_put_contents(__DIR__ . '/order.txt', var_export($order, true));
		return $order;
	}
}
	