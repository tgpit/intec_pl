<?php

namespace Acrit\Core\Crm\Plugins\TiuRuHelpers;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	protected static $stocks = [];

	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($token, &$message) {
		$result = false;
		$res = $this->request('/orders/list', 'GET', [
			'limit' => 1,
		], $token);
		if (!isset($res['orders'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_TIURU_CHECK_ERROR') . $res;
		}
		else {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_TIURU_CHECK_SUCCESS');
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
				'date_from' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
				'limit' => $limit,
			];
			$req_filter = array_merge($req_filter, $filter);
			$res = $this->request('/orders/list', 'GET', $req_filter);
			if (isset($res['orders'])) {
				$list = $res['orders'];
			}
		}
		return $list;
	}

	/**
	 * Get orders count
	 */
	public function getOrdersCount(array $filter) {
		$count = false;
		$req_filter = [
			'date_from' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->request('/orders/list', 'GET', $req_filter);
		if (isset($res['orders'])) {
			$count = count($res['orders']);
		}
		return $count;
	}

	/**
	 * Get order
	 */
	public function getOrder($order_id) {
		$result = false;
		$resp = $this->request('/orders/' . $order_id);
		if (isset($resp['order'])) {
			$result = $resp['order'];
		}
		return $result;
	}

	/**
	 * Get order
	 */
	public function getStatuses() {
		$list = [];
		$resp = $this->request('/order_status_options/list');
		if (isset($resp['order_status_options'])) {
			foreach ($resp['order_status_options'] as $item) {
				$id = $item['name'];
				$name = $item['title'];
				if (!Helper::isUtf()) {
					$name = Helper::convertEncoding($name);
				}
				$list[$id] = $name;
			}
		}
		return $list;
	}
}
