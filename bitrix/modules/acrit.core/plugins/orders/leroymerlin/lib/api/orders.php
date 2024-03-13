<?php

namespace Acrit\Core\Orders\Plugins\LeroymerlinHelpers;

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
	public function checkConnection($connect, &$message) {
//        file_put_contents(__DIR__.'/connect.txt', var_export($connect, true));
		$result = false;
		$data = [
            'grant_type' => 'password',
            'username' => $connect['username'],
            'password' => $connect['password'],
            'client_id' => $connect['client_id'],
            'client_secret' => $connect['client_secret'],
        ];
		try {
            $res = $this->request('/oauth/token', $data, $connect['api_key'], 'POST', false  );
        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
//            file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
        }
		if (isset($res['access_token'])) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_LEROYMERLIN_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_LEROYMERLIN_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_LEROYMERLIN_CHECK_ERROR');
		}
		return $result;
	}

	/**
	 * Get orders list
	 */

	public function getJwt() {
	    $data = [
            'grant_type' => $this->grantType,
            'username' => $this->userName,
            'password' => $this->password,
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
        ];
        $res = $this->request('/oauth/token', $data, $this->strApiKey, 'POST' , false);
        return $res;
//        file_put_contents(__DIR__.'/result2.txt', var_export($res, true) );
    }

    public function getList($date_ts, $jwt) {
        $date_compare =  strtotime(date('d.m.Y 00:00:00', $date_ts));
        $list = [];
        $url = '/merchants/v1/parcels';
        $limit = 100;
        $offset = 0;
        while ( $offset < 10000 ) {
            $method = $url.'?limit='.$limit.'&offset='.$offset;
            $result = $this->request($method, [], $this->strApiKey, 'GET', $jwt);
            if ( !$result || empty($result) ) {
                break;
            }
            foreach ( $result as $key => $item ) {
                $date_str = $item['creationDate'];
                $date_order =  strtotime(date('d.m.Y 00:00:00', strtotime($date_str)));
                if ( $date_order >= $date_compare ) {
                    $list[$item['id']] = $item['id'];
                    $list[$item['id']] = $item;
                } else {
                    break 2;
                }
            }
            $offset += $limit;
        }
		return $list;
	}

	public function getStatus( $date, $jwt ) {
        $method = '/merchants/v1/parcels/'.$date.'/statuses';
        $result = $this->request($method, [], $this->strApiKey, 'GET', $jwt );
        return $result;
    }
	/**
	 * Get orders count
	 */

//	public function getCount($create_from_ts) {
//		$count = 0;
//		$params = [
//			'page' => 1,
//			'page_size' => 1,
//		];
//		if ($create_from_ts) {
//			$params[self::FILTER_CREATED_FROM_FIELD] = self::getDateF($create_from_ts);
////			$params[self::FILTER_UPDATED_FROM_FIELD] = self::getDateF($create_from_ts);
//		}
//		$res = $this->request('order/get-order-list', $params);
//		if ($res['data']) {
//			$count = (int) $res['data']['total_count'];
//		}
//		return $count;
//	}

	/**
	 * Get order
	 */

//	public function getById($order_id) {
//		$order = false;
//		if (isset(self::$arOrders[$order_id])) {
//			$order = self::$arOrders[$order_id];
//		}
//		else {
//			$res = $this->request('order/get-order-list', [
//				'order_ids' => [$order_id],
//				'page'      => 1,
//				'page_size' => 1,
//			]);
//			if ($res['data']) {
//				$order = $res['data']['orders'][0];
//				self::$arOrders[$order_id] = $order;
//			}
//		}
//		return $order;
//	}

	/**
	 * Get formatted date from timestamp
	 */

	public static function getDateF($create_from_ts) {
		return gmdate(self::DATE_FORMAT, $create_from_ts);
	}

}
