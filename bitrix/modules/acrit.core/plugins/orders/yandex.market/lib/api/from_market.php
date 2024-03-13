<?
/**
 * Acrit Core: Orders integration plugin for yandexMarket
 * Documentation: https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/reference/post-cart.html
 */

namespace Acrit\Core\Orders\Plugins\YandexMarketApi;

use \Acrit\Core\Helper,
    \Acrit\Core\Log,
    \Acrit\Core\Json,
    \Acrit\Core\HttpRequest,
    \Acrit\Core\Orders\Controller,
    \Acrit\Core\Orders\Plugins\YandexMarket,
    \Acrit\Core\Export\Plugins\YandexMarketplaceHelpers\StockTable as Stock,
    \Acrit\Core\Export\Plugins\YandexMarketplaceHelpers\StockHistoryTable as StockHistory;
use PhpOffice\PhpSpreadsheet\Exception;

Helper::loadMessages(__FILE__);

class FromMarket
{
    const URL         = 'https://api.partner.market.yandex.ru/v1/';
    const DATE_FORMAT = 'Y-m-d\TH:i:sO';

    protected $obPlugin;
    protected $strAccessToken;
    protected $intProfileId;
    protected $strModuleId;

    /**
     * 	Constructor
     */
    public function __construct($obPlugin)
    {
        $arProfile            = $obPlugin->getProfileArray();
        $this->obPlugin       = $obPlugin;
        $this->intProfileId   = $arProfile['ID'];
        $this->strModuleId    = $obPlugin->getModuleId();
        $this->strClientId    = $arProfile['CONNECT_CRED']['oauth_client_id'];
        $this->strAccessToken = $arProfile['CONNECT_CRED']['token'];
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

    function orderAccept()
    {

        Json::prepare();
        //$strJson = file_get_contents('php://input');
        $arJson   = $this->getRequestJson();
        $mp_order = $arJson['order'];
        $arResult = [];
        Log::getInstance($this->strModuleId, 'orders')->add('getRequestJson ', $this->arProfile['ID'], true);
        Log::getInstance($this->strModuleId, 'orders')->add($this->getRequestJson(), $this->arProfile['ID'], true);
        if ($arJson   = $this->getRequestJson()) {
            // Main fields
            $order = [
                'ID' => $arJson['order']['id'],
                'DATE_INSERT' => time(),
                'IS_CANCELED' => false,
            ];

            // Fields
            $order['FIELDS']   = [
                'id' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['id']],
                ],
                'status' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['status']],
                ],
                'substatus' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['substatus']],
                ],
                'paymentMethod' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['paymentMethod']],
                ],
                'paymentType' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['paymentType']],
                ],
                'shipmentDate' => [
                    'TYPE' => 'STRING',
                    'VALUE' => [$mp_order['delivery']['shipments'][0]['shipmentDate']],
                ],
            ];
            // Products
            $order['PRODUCTS'] = [];
            $products_list     = $mp_order['items'];
            foreach ($products_list as $item) {
                $order['PRODUCTS'][] = [
                    'PRODUCT_NAME' => $item['offerName'],
                    'PRODUCT_CODE' => $item['offerId'],
//                    'PRICE' => $item['buyerPriceBeforeDiscount'],
                    'PRICE' => $item['price'] +  $item['subsidy'],
//                    'PRICE' => $item['price'],
                    'CURRENCY' => 'RUB',
                    'QUANTITY' => $item['count'],
                    'DISCOUNT_TYPE_ID' => 1,
//                    'DISCOUNT_SUM' => ($item['price'] - $item['buyer-price']),
                    'DISCOUNT_SUM' => 0,
                    'MEASURE_CODE' => 0,
                    'TAX_RATE' => 0,
                    'TAX_INCLUDED' => 'Y',
                ];
            }
            $order = $this->obPlugin->formatOrder($order);

            Controller::setModuleId($this->strModuleId);
            Controller::setProfile($this->intProfileId);
            $result = Controller::syncExtToStore($order);
            if (!$result['errors']) {
                return [
                    'order' => [
                        'accepted' => true,
                        'id' => $result['id'],
                    ]
                ];
            }
        }
    }

    function orderStatus()
    {
        Json::prepare();
        //$strJson = file_get_contents('php://input');
        $arJson                 = $this->getRequestJson();
        Controller::setModuleId($this->strModuleId);
        Controller::setProfile($this->intProfileId);
        $status                 = $arJson['order']['status'];
        $mp_order               = $arJson['order'];
        $order                  = [
            'ID' => $arJson['order']['id'],
            'STATUS_ID' => $arJson['order']['status'],
            'DATE_INSERT' => strtotime($mp_order['creationDate']),
            'SUB_STATUS_ID' => $arJson['order']['substatus'],
        ];
        $order['FIELDS']        = [
            'id' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['id']],
            ],
            'status' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['status']],
            ],
            'substatus' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['substatus']],
            ],
            'paymentMethod' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['paymentMethod']],
            ],
            'paymentType' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['paymentType']],
            ],
            'shipmentDate' => [
                'TYPE' => 'STRING',
                'VALUE' => [$mp_order['delivery']['shipments'][0]['shipmentDate']],
            ],
        ];

//        $order['PRODUCTS'] = [];
//        $products_list     = $mp_order['items'];
//        foreach ($products_list as $item) {
//            $order['PRODUCTS'][] = [
//                'PRODUCT_NAME' => $item['offerName'],
//                'PRODUCT_CODE' => $item['offerId'],
//                'PRICE' => $item['price'] +  $item['subsidy'],
//                'CURRENCY' => 'RUB',
//                'QUANTITY' => $item['count'],
//                'DISCOUNT_TYPE_ID' => 1,
//                'DISCOUNT_SUM' => 0,
//                'MEASURE_CODE' => 0,
//                'TAX_RATE' => 0,
//                'TAX_INCLUDED' => 'Y',
//            ];
//        }

        $order['MODULE_ACTION'] = 'change_status';
        $order = $this->obPlugin->formatOrder($order);
        $result                 = (array) Controller::syncExtToStore($order);
        Log::getInstance($this->strModuleId, 'orders')->add('$order '.$order, $this->arProfile['ID'], true);
        Log::getInstance($this->strModuleId, 'orders')->add($order, $this->arProfile['ID'], true);

        if (!$result['errors']) {
            return [
                'order' => [
                    'accepted' => true,
                    'id' => $result['id'],
                ]
            ];
        }
        //updateOrderStatus
    }

    function orderCancel() {
        http_response_code(404);
        return http_response_code();
    }

    /**
     * 	/cart
     */
    function cart()
    {
        Json::prepare();
        //$strJson = file_get_contents('php://input');
        $arJson   = $this->getRequestJson();
        Controller::setModuleId($this->strModuleId);
        Controller::setProfile($this->intProfileId);
        $arResult = [];
        Log::getInstance($this->strModuleId, 'orders')->add('$arJson', $this->arProfile['ID'], true);
        Log::getInstance($this->strModuleId, 'orders')->add($arJson, $this->arProfile['ID'], true);
        if ($arJson   = $this->getRequestJson()) {

            $arWarehousesSkus = [];
            $arSkusFeeds      = [];
            if (is_array($arJson['cart']) && is_array($arJson['cart']['items'])) {
                foreach ($arJson['cart']['items'] as $arItem) {
                    $arWarehousesSkus[$arItem['warehouseId']][] = $arItem['offerId'];
                    $arSkusFeeds[$arItem['offerId']]            = $arItem['feedId'];
                }
            }
            Log::getInstance($this->strModuleId, 'orders')->add('$arWarehousesSkus', $this->arProfile['ID'], true);
            Log::getInstance($this->strModuleId, 'orders')->add($arWarehousesSkus, $this->arProfile['ID'], true);

            foreach ($arWarehousesSkus as $warehouseId => $skus) {
                if ($warehouseId && is_array($skus)) {
                    $obDate = new \Bitrix\Main\Type\Datetime;
                    $arData = $this->getWarehouseSkuData($warehouseId, $skus, true);
                    Log::getInstance($this->strModuleId, 'orders')->add('$arData', $this->arProfile['ID'], true);
                    Log::getInstance($this->strModuleId, 'orders')->add($arData, $this->arProfile['ID'], true);
                    foreach ($skus as $strSku) {
                        $count      = ($arData[$strSku]['count']) ? $arData[$strSku]['count'] : 0;
                        $arResult[] = [
                            'feedId' => $arSkusFeeds[$strSku],
                            'offerId' => $strSku,
                            'count' => $count,
                            'delivery' => true
                        ];
                    }
                }
            }
        }
        Log::getInstance($this->strModuleId, 'orders')->add('$arResult', $this->arProfile['ID'], true);
        Log::getInstance($this->strModuleId, 'orders')->add($arResult, $this->arProfile['ID'], true);
        return ['cart' => ['items' => $arResult]];
    }

    /**
     * Get SKU data for selected warehouse
     * Example: $arJson = $this->getWarehouseSkuData(1, '48286');
     */
    public function getWarehouseSkuData($intWarehouseId, $arSku, $bForAllProfiles = false)
    {
        $arQuery = [
            'order' => ['TIMESTAMP_X' => 'DESC'],
            'filter' => [
                '=MODULE_ID' => $this->strModuleId,
                'PROFILE_ID' => $this->intProfileId,
                'WAREHOUSE_ID' => $intWarehouseId,
                '=SKU' => $arSku,
            ],
            'select' => ['ID', 'SKU', 'WAREHOUSE_ID', 'TYPE', 'COUNT', 'UPDATED_AT'],
        ];
        if ($bForAllProfiles) {
            unset($arQuery['filter']['PROFILE_ID']);
        }
        $arStocks  = [];
        $resStocks = Stock::getList($arQuery);
        while ($arStock   = $resStocks->fetch()) {
            $arStocks[$arStock['SKU']] = [
                'sku' => $arStock['SKU'],
                'warehouseId' => intVal($intWarehouseId),
                'count' => intVal($arStock['COUNT']),
            ];
        }
        return $arStocks;
    }
}