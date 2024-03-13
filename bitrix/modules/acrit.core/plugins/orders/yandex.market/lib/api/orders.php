<?php

namespace Acrit\Core\Orders\Plugins\YandexMarketApi;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request
{

	protected static $stocks = [];
    protected static $extOrders = [];

	public function __construct($obPlugin)
	{
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function changeStatus(&$message)
	{
		$result = false;
		$res = $this->request('campaigns/{campaignId}/orders/{orderId}/status.json', [
			'order' => [
				'status' => 'PROCESSING',
				'substatus' => 'STARTED'
			]
		]);
		if (!$res['success'])
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . implode('. ', $res['error']);
		} else
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	public function checkConnection(&$message)
	{
		$result = false;
		$res = $this->request('campaigns.json', [
//			'dateFrom' => date(self::DATE_FORMAT, strtotime('today')),
			'count' => 1,
		]);
		if ($res['errors'])
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . implode('. ', $res['errors']);
		} else
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	public function getCampaigns(&$message)
	{
		$result = false;
		//$arProfile = $obPlugin->getProfileArray();		
		$res = $this->request('campaigns/' . $this->strCampaignId . '/offer-mapping-entries.json');
//        file_put_contents(__DIR__.'/conn.txt', var_export($res, true));
		if ($res['errors'])
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . $res['errors'][0]['message'];
			$result = true;
		} else
		{
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	/**
	 * Get orders list from market
     * DOC https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/reference/get-campaigns-id-orders.html
     * use in YandexMarket::getOrdersIDsList
	 */
	public function getOrdersList(array $filter, int $limit = 1)
	{
        $page = 1;
        do {
            $req_filter = [
                'fromDate' => date(self::DATE_FORMAT, strtotime('2020-01-01')),
                'pageSize' => 50,
                'page' => $page
            ];
            $req_filter = array_merge($req_filter, $filter);
            $res = $this->request('campaigns/' . $this->strCampaignId . '/orders.json', $req_filter);
            $orders = $res['orders'];
            if (count($orders)) {
                foreach ($orders as $order) {
                    self::$extOrders[$order['id']] = $order;
                }
            }
            $page++;
        } while ( $page <= $res['pager']['pagesCount'] );
//        file_put_contents(__DIR__ . '/orders.txt', var_export(self::$extOrders, true));
        return self::$extOrders;
	}

	/**
	 * Get orders count
	 */
	public function getOrdersCount(array $filter, int $limit = 1)
	{
		$count = false;
		$req_filter = [
			'fromDate' => date(self::DATE_FORMAT, strtotime('2020-01-01')),
            'pageSize' => 50,
            'page' => 1
		];
		$req_filter = array_merge($req_filter, $filter);
		$res        = $this->request('/campaigns/'.$this->strCampaignId.'/orders.json', $req_filter);
		if ($res['pager']) {
            $count = $res['pager']['total'];
        }
		return $count;
	}

	/**
	 * Get order from market
     * https://yandex.ru/dev/market/partner-marketplace-cd/doc/dg/reference/get-campaigns-id-orders-id.html
	 */
	public function getOrder($order_id)
	{
		$result = false;
        if (!self::$extOrders[$order_id]) {
            $resp = $this->request('/campaigns/'.$this->strCampaignId.'/orders/'.$order_id.'.json',
                [
                    'shipments' => [
                        $order_id,
                    ],
            ]);
            if ($resp['result']['order']) {
                $result = $resp['result']['order'];
            }
        } else {
            $result = self::$extOrders[$order_id];
        }
        return $result;
	}

}
