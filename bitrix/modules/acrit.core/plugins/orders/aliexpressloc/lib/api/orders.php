<?php

namespace Acrit\Core\Orders\Plugins\AliexpressLocHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	const FILTER_CREATED_FROM_FIELD = 'date_start';
	const FILTER_UPDATED_FROM_FIELD = 'update_at_from';
	const DATE_FORMAT = 'Y-m-d\TH:i:s\Z';

	protected static $arOrders = [];


	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($strToken, &$message) {
		$result = false;
		$res = $this->request('order/get-order-list', [
			'page' => 1,
			'page_size' => 1,
		], $strToken);
		if (isset($res['data'])) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALIEXPRESSLOC_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALIEXPRESSLOC_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_ALIEXPRESSLOC_CHECK_ERROR');
		}
		return $result;
	}

	/**
	 * Get orders list
	 */

	public function getList($filter=[]) {
		$list = [];
		$page_size = 20;
        $i = 0;
		$page = 1;
		// Filter
		if ($filter[self::FILTER_CREATED_FROM_FIELD]) {
			$filter[self::FILTER_CREATED_FROM_FIELD] = self::getDateF($filter[self::FILTER_CREATED_FROM_FIELD]);
			unset($filter[self::FILTER_UPDATED_FROM_FIELD]);
		}
		if ($filter[self::FILTER_UPDATED_FROM_FIELD]) {
			$filter[self::FILTER_UPDATED_FROM_FIELD] = self::getDateF($filter[self::FILTER_UPDATED_FROM_FIELD]);
			unset($filter[self::FILTER_CREATED_FROM_FIELD]);
		}
		if ( !$filter[self::FILTER_UPDATED_FROM_FIELD] && !$filter[self::FILTER_CREATED_FROM_FIELD] )  {
			$filter[self::FILTER_UPDATED_FROM_FIELD] = self::getDateF(strtotime('2010-01-01 00:00:00'));
		}
		$filter['page_size'] = $page_size;
		do {
            $filter['page'] = $page;
//		$filter['order_statuses'] = ['Created','InProgress','Finished','Cancelled'];
//		$filter['delivery_statuses'] = ['Init','PartialShipped','Shipped','Delivered','Cancelled'];
            $res = $this->request('order/get-order-list', $filter);
            if ($res['data']) {
                foreach ($res['data']['orders'] as $item) {
                    $list[] = $item;
	                self::$arOrders[$item['id']] = $item;
                }
            }
            $page++;
            $i++;
        }  while( $page <= ceil($res['data']['total_count'] / $page_size ) );
		return $list;
	}

	/**
	 * Get orders count
	 */

	public function getCount($create_from_ts) {
		$count = 0;
		$params = [
			'page' => 1,
			'page_size' => 1,
		];
		if ($create_from_ts) {
			$params[self::FILTER_CREATED_FROM_FIELD] = self::getDateF($create_from_ts);
//			$params[self::FILTER_UPDATED_FROM_FIELD] = self::getDateF($create_from_ts);
		}
		$res = $this->request('order/get-order-list', $params);
		if ($res['data']) {
			$count = (int) $res['data']['total_count'];
		}
		return $count;
	}

	/**
	 * Get order
	 */

	public function getById($order_id) {
		$order = false;
		if (isset(self::$arOrders[$order_id])) {
			$order = self::$arOrders[$order_id];
		}
		else {
			$res = $this->request('order/get-order-list', [
				'order_ids' => [$order_id],
				'page'      => 1,
				'page_size' => 1,
			]);
			if ($res['data']) {
				$order = $res['data']['orders'][0];
				self::$arOrders[$order_id] = $order;
			}
		}
		return $order;
	}

	/**
	 * Get formatted date from timestamp
	 */

	public static function getDateF($create_from_ts) {
		return gmdate(self::DATE_FORMAT, $create_from_ts);
	}

}
