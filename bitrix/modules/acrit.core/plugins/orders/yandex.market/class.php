<?
/**
 * Acrit Core: Orders integration plugin for YandexMarket
 */

namespace Acrit\Core\Orders\Plugins;

require_once __DIR__.'/lib/api/request.php';
require_once __DIR__.'/lib/api/orders.php';
require_once __DIR__.'/lib/api/from_market.php';
require_once __DIR__.'/lib/api/stock.php';

use \Bitrix\Main\Localization\Loc,
    \Acrit\Core\Helper,
    \Acrit\Core\Orders\Plugin,
    \Acrit\Core\Orders\Settings,
    \Acrit\Core\Orders\Controller,
    \Acrit\Core\Orders\ProfilesTable,
    \Acrit\Core\Orders\Plugins\YandexMarketApi\Orders,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Json,
    \Acrit\Core\Export\Field\Field,
    \Acrit\Core\Log;

Loc::loadMessages(__FILE__);

class YandexMarket extends Plugin
{
    // List of available directions
    protected $arDirections    = [self::SYNC_STOC];
    protected $isCountable = true;
    protected static $arOrders = [];
    const DATE_FORMAT = 'd-m-Y';
    /**
     * Base constructor.
     */
    public function __construct($strModuleId)
    {
        parent::__construct($strModuleId);
    }

    /**
     * Base constructor.
     */
    public function log($var)
    {
        Log::getInstance($this->strModuleId, 'orders')->add($var, $this->arProfile['ID'], true);
    }
    /* START OF BASE STATIC METHODS */

    /**
     * Get plugin unique code ([A-Z_]+)
     */
    public static function getCode()
    {
        return 'YANDEXMARKET';
    }

    /**
     * Get plugin short name
     */
    public static function getName()
    {
        return Loc::getMessage(self::getLangCode('PLUGIN_NAME'));
    }

    /**
     * Get type of regular synchronization
     */
    public static function getAddSyncType() {
        return self::ADD_SYNC_TYPE_DUAL;
    }

    /**
     * 	Include classes
     */
    public function includeClasses()
    {
        #require_once(__DIR__.'/lib/json.php');
    }

    /**
     * Get comment for the tab
     */
    public function getTabComment($tab)
    {
        $comment = '';
        switch ($tab) {
//            case 'products': $comment = Loc::getMessage(self::getLangCode('PRODUCTS_MESSAGE'));
//                break;
        }
        return $comment;
    }

    protected function getRequestJson()
    {
        $arJson  = null;
        $strJson = file_get_contents('php://input');
        try {
            $arJson = \Bitrix\Main\Web\Json::decode($strJson);
        } catch (\Exception $obError) {

        }
        return $arJson;
    }

    private static function checkAuth($arProfile)
    {
        $headers = \getallheaders();
//        file_put_contents(__DIR__.'/token.txt', var_export($headers, true));
        if (!$arProfile['CONNECT_CRED']['inToken'] || $arProfile['CONNECT_CRED']['inToken'] != $headers['Authorization']) {
            \CHTTP::setStatus('403 Forbidden');
//            print Json::output($arResult);
            print Json::output(json_encode('Autorization error'));
            die();
        }
    }

    /**
     * Direct execute plugin/profile from ProfilesTable
     */
    public function execPlugin($arParams = [])
    {
        try {
            $api = new YandexMarketApi\FromMarket($this);
            self::checkAuth($this->arProfile);
            $requestsMap = ['cart', 'orderAccept', 'orderStatus'];
            $url = explode('/', $arParams['URL']);
            $action = '';
            if (strpos($arParams['URL'], '/cart')) {
                $action = 'cart';
            } elseif (strpos($arParams['URL'], '/order/accept')) {
                $action = 'orderAccept';
            } elseif (strpos($arParams['URL'], '/order/status')) {
                $action = 'orderStatus';
            } elseif (strpos($arParams['URL'], '/order/cancellation/notify')) {
                $action = 'orderCancel';
            }
        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
             file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
        }

        if (!in_array($action, $requestsMap)) {
            // error
        }
        //Helper::call($this->strModuleId, '\Acrit\Core\Orders\Plugins\YandexMarketApi\FromMarket', $url['2']);

        Log::getInstance($this->strModuleId, 'orders')->add('$action '.$action, $this->arProfile['ID'], true);

        $arResult = call_user_func([$api, $action]);
        //$arResult = YandexMarketApi\FromMarket::$url['2'];
        //$strJson = \Bitrix\Main\HttpRequest::getInput();

        print Json::output($arResult);
        die();
    }

    /**
     * Get id of products in marketplace
     */
    public static function getIdField()
    {
        return [
            'id' => 'CODE',
            'name' => Loc::getMessage(self::getLangCode('PRODUCTS_ID_FIELD_NAME')),
        ];
    }

    /**
     * Variants for deal statuses
     * @return array
     */
    public function getStatuses()
    {
        $list   = [];
        $list[] = [
            'id' => 'UNPAID',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_UNPAID')),
        ];
//        $list[] = [
//            'id' => 'PENDING',
//            'name' => Loc::getMessage(self::getLangCode('STATUSES_PENDING')),
//        ];
        $list[] = [
            'id' => 'PROCESSING',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PROCESSING')),
        ];
        $list[] = [
            'id' => 'PICKUP',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_PICKUP')),
        ];
        $list[] = [
            'id' => 'DELIVERY',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERY')),
        ];
        $list[] = [
            'id' => 'DELIVERED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_DELIVERED')),
        ];
        $list[] = [
            'id' => 'CANCELLED',
            'name' => Loc::getMessage(self::getLangCode('STATUSES_CANCELLED')),
        ];
        return $list;
    }

    /**
     * Store fields for deal contact
     * @return array
     */
    public function getContactFields()
    {
        /*
          $list = [];
          $list['user'] = [
          'title' => Loc::getMessage(self::getLangCode('CONTACT_TITLE')),
          ];
          $list['user']['items'][] = [
          'id' => 'fio',
          'name' => Loc::getMessage(self::getLangCode('CONTACT_CUSTOMER_FIO')),
          'direction' => self::SYNC_STOC,
          ];
          $list['user']['items'][] = [
          'id' => 'address',
          'name' => Loc::getMessage(self::getLangCode('CONTACT_ADDRESS')),
          'direction' => self::SYNC_STOC,
          ];
          return $list; */
    }

    /**
     * Store fields for deal fields
     * @return array
     */
    public function getFields()
    {
        $list   = parent::getFields();
        $list[] = [
            'id' => 'id',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_id')),
            'direction' => self::SYNC_ALL,
        ];
        $list[] = [
            'id' => 'paymentType',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_paymentType')),
            'direction' => self::SYNC_ALL,
        ];
        $list[] = [
            'id' => 'paymentMethod',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_paymentMethod')),
            'direction' => self::SYNC_ALL,
        ];
        $list[] = [
            'id' => 'status',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_status')),
            'direction' => self::SYNC_ALL,
        ];
        $list[] = [
            'id' => 'substatus',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_substatus')),
            'direction' => self::SYNC_ALL,
        ];
        $list[] = [
            'id' => 'shipmentDate',
            'name' => Loc::getMessage(self::getLangCode('FIELDS_shipmentDate')),
            'direction' => self::SYNC_ALL,
        ];

        return $list;
    }

    public function getProductFields($intProfileID, $intIBlockID)
    {
        $arResult       = [];
        $arResult['id'] = ['FIELD' => 'ID', 'REQUIRED' => true];
        return $arResult;
    }

    /**
     * 	Show plugin default settings
     */
    public function showSettings($arProfile)
    {
        ob_start();
        ?>
        <table class="acrit-exp-plugin-settings" style="width:100%;">
            <tbody>
                <tr class="heading" id="tr_HEADING_CONNECT"><td colspan="2"><?= Loc::getMessage(self::getLangCode('SETTINGS_HEADING')); ?></td></tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
        <?= Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_ID_HINT'))); ?>
        <?= Loc::getMessage(self::getLangCode('SETTINGS_CLIENT_ID')); ?>:
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[CONNECT_CRED][oauth_client_id]" size="50" maxlength="255"
                               value="<?= htmlspecialcharsbx($arProfile['CONNECT_CRED']['oauth_client_id']); ?>" />
        <? if ($arProfile['CONNECT_CRED']['oauth_client_id']): ?>
                            <a href="https://oauth.yandex.ru/authorize?response_type=token&client_id=<?= $arProfile['CONNECT_CRED']['oauth_client_id'] ?>"><?= Loc::getMessage(self::getLangCode('GET_OAUTH_TOKEN')); ?></a>
        <? endif; ?>
                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
        <?= Helper::ShowHint(Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_HINT'))); ?>
                        <?= Loc::getMessage(self::getLangCode('SETTINGS_TOKEN')); ?>:
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[CONNECT_CRED][token]" size="50" maxlength="255"
                               value="<?= htmlspecialcharsbx($arProfile['CONNECT_CRED']['token']); ?>" />

                    </td>
                </tr>

                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage(self::getLangCode('SETTINGS_CAMPAIGN_ID')); ?>:
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[CONNECT_CRED][campaignId]" size="50" maxlength="255" data-role="connect-cred-campaignId"
                               value="<?= htmlspecialcharsbx($arProfile['CONNECT_CRED']['campaignId']); ?>" />

                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">

                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <p><a class="adm-btn plugin_ajax_action" data-role="get_campaign"><?= Loc::getMessage(self::getLangCode('SETTINGS_CHECK_CONN')); ?></a></p>
                        <p id="check_msg"></p>

                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">
        <?= Loc::getMessage(self::getLangCode('SETTINGS_TOKEN_IN')); ?>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[CONNECT_CRED][inToken]" size="50" maxlength="255"
                               value="<?= htmlspecialcharsbx($arProfile['CONNECT_CRED']['inToken']); ?>" />

                    </td>
                </tr>
                <tr>
                    <td width="40%" class="adm-detail-content-cell-l">

        <?= Loc::getMessage(self::getLangCode('SETTINGS_EXTERNAL_REQUEST_URL')); ?>
                    </td>
                    <td width="60%" class="adm-detail-content-cell-r">
                        <input type="text" name="PROFILE[EXTERNAL_REQUEST_URL]" size="50" maxlength="255"
                               value="<?= htmlspecialcharsbx($arProfile['EXTERNAL_REQUEST_URL']); ?>" />
                        <input type="hidden" name="PROFILE[EXTERNAL_REQUEST]" value="Y" />
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
    public function ajaxAction($strAction, $arParams, &$arJsonResult)
    {
        switch ($strAction) {
            case 'connection_check':
                $message                 = '';
                $api                     = $this->getApi();
                $res                     = $api->checkConnection($message);
                $arJsonResult['check']   = $res ? 'success' : 'fail';
                $arJsonResult['message'] = $message;
                $arJsonResult['result']  = 'ok';
                break;
            case 'get_campaign':
                $message                 = '';
                $api                     = $this->getApi();
                $res                     = $api->getCampaigns($message);
                $arJsonResult['check']   = $res ? 'success' : 'fail';
                $arJsonResult['message'] = $message;
                $arJsonResult['result']  = 'ok';
                break;
        }
    }

    /**
     * Get object for api requests
     */
    public function getApi()
    {
        $api = new Orders($this);
        return $api;
    }

    /**
     * Get orders count
     */
    public function getOrdersCount($create_from_ts)
    {
        $count = false;
        if ($create_from_ts) {
            $api    = $this->getApi();
            $filter = [
                'fromDate' => date(self::DATE_FORMAT, $create_from_ts),
            ];
            $count  = $api->getOrdersCount($filter);
        }
        return $count;
    }

    /**
     * Get orders count
     * Used in \Acrit\Core\Orders\Controller\syncByPeriod
     * return extOrderIds
     */
    public function getOrdersIDsList($create_from_ts = false, $change_from_ts = false)
    {
//        return [];
        $result   = [];
        // Get the list
        $filter = [];
        if ($create_from_ts || $change_from_ts) {
            $date = $change_from_ts ?: $create_from_ts;
            if ($date) {
                $filter = [
                    'fromDate' => date(self::DATE_FORMAT, $date),
                ];
            }
        }
        $api    = $this->getApi();
        $orders = $api->getOrdersList($filter, 1000);
        foreach ($orders as $id => $order) {
            $result[] = $order['id'];
        }
        return $result;
    }

    /**
     * Get order
     */
    public function getOrder($extOrderId)
    {
        $order    = false;
        // Order data
        $api      = $this->getApi();
        $extOrder = $api->getOrder($extOrderId);
        if ($extOrder) {
            // Main fields
            $order         = [
                'ID' => $extOrder['id'],
                'DATE_INSERT' => strtotime($extOrder['creationDate']),
                'STATUS_ID' => $extOrder['status'],
                'IS_CANCELED' => false,
            ];
            // User data
            $order['USER'] = [
                'fio' => $extOrder['customerFullName'],
                'address' => $extOrder['customerAddress'],
            ];

            // Fields
            $order['FIELDS']   = [
                'id' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['id']],
                ],
                'status' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['status']],
                ],
                'substatus' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['substatus']],
                ],
                'paymentMethod' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['paymentMethod']],
                ],
                'paymentType' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['paymentType']],
                ],
                'shipmentDate' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$extOrder['delivery']['shipments'][0]['shipmentDate']],
                ],
            ];
            // Products
            $order['PRODUCTS'] = [];
            $products          = $extOrder['items'];
            foreach ($products as $item) {
                $order['PRODUCTS'][] = [
                    'PRODUCT_NAME' => $item['offerName'],
                    'PRODUCT_CODE' => $item['offerId'],
//                    'PRICE' => $item['buyerPriceBeforeDiscount'],
                    'PRICE' => $item['price'] +  $item['subsidy'],
                    'CURRENCY' => 'RUB',
                    'QUANTITY' => $item['count'],
                    'DISCOUNT_TYPE_ID' => 1,
                    'DISCOUNT_SUM' => 0,
                    'MEASURE_CODE' => 0,
                    'TAX_RATE' => 0,
                    'TAX_INCLUDED' => 'Y',
                ];
            }
            $order = self::formatOrder($order);
        }
        return $order;
    }
    static function getDescription()
    {
        return Loc::getMessage(self::getLangCode('DESCRIPTION'));;
    }
}