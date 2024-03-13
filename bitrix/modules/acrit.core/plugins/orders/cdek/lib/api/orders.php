<?php
/**
 * Acrit Core: CRM integration plugin for CDEK.Market
 * Documentation: https://api.cdek.market/api/documentation
 */

namespace Acrit\Core\Orders\Plugins\CdekHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	protected static $orders = [];

	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($api_key, &$message) {
		$result = false;
		$res = $this->request('orders', [
			'limit' => 1,
		], $api_key);
		if (!$res['ordersCount']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_CDEK_CHECK_ERROR') . implode('. ', $res['message']);
		}
		else {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_CDEK_CHECK_SUCCESS');
			$result = true;
		}
		return $result;
	}

	/**
	 * Get orders list
	 */
	public function getOrdersList(array $filter, int $limit=1) {
		$list = [];
		if ($limit) {
			$req_filter = [
				'startDate' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
				'limit' => $limit,
			];
			$req_filter = array_merge($req_filter, $filter);
			$res = $this->request('orders', $req_filter);
			if ($res['orders']) {
				$list = $res['orders'];
				// Products data
				$list = $this->getOrdersProducts($list);
				// Remember orders for getOrder()
				foreach ($list as $item) {
					self::$orders[$item['id']] = $item;
				}
			}
		}
		return $list;
	}

	/**
	 * Get orders count
	 */
	public function getOrdersCount(array $filter, int $limit=1) {
		$count = false;
		$req_filter = [
			'startDate' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
			'limit' => $limit,
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->request('orders', $req_filter);
		if ($res['ordersCount']) {
			$count = $res['ordersCount'];
		}
		return $count;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$result = false;
		if (isset(self::$orders[$order_id])) {
			$result = self::$orders[$order_id];
		}
		return $result;
	}

	/**
	 * Products data of orders
	 */
	public function getOrdersProducts($orders) {
		$p_size = 100;
		$p_cnt = 1;
		$p = 0;
		while ($p < $p_cnt) {
			$res = $this->request('products', [
				'page' => $p + 1,
				'pageSize' => $p_size,
			]);
			if ($res['total']) {
				$p_cnt = ceil($res['total'] / $p_size);
				$products = [];
				foreach ($res['result'] as $item) {
					$products[$item['productId'] . '_' . $item['offerId']] = $item;
				}
				foreach ($orders as $i => $order) {
					foreach ($order['products'] as $j => $order_prod) {
						$prod_code = $order_prod['productId'] . $order_prod['offerId'];
						if (isset($products[$prod_code])) {
							$orders[$i]['products'][$j]['info'] = $products[$prod_code];
						}
					}
				}
			}
			$p++;
		}
		return $orders;
	}
}
