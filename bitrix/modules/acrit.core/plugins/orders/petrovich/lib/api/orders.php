<?php

namespace Acrit\Core\Orders\Plugins\PetrovichHelpers;

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
            'username' => $connect['username'],
            'password' => $connect['password'],
        ];
		try {
            $res = $this->request('seller.petrovich.ru/api/login', $data, 'POST', false  );
        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
//            file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
        }
		if (isset($res['token'])) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_PETROVICH_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_PETROVICH_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_PETROVICH_CHECK_ERROR');
		}
		return $result;
	}

	/**
	 * Get orders list
	 */

	public function getJwt() {
	    $data = [
            'username' => $this->userName,
            'password' => $this->password,
        ];
        $res = $this->request('seller.petrovich.ru/api/login', $data,  'POST' , false);
        return $res;
    }

    public function getList($date_ts, $jwt) {
        $date_end = date('Y-m-d');
        $date_beg =  date('Y-m-d', $date_ts );
        $list = [];
        $page = 1;
        $lastPage = 1;
        do {
            $url = 'lkmarketplace.petrovich.ru/oms/v1/orders?sort=orderCreatedAt&filter[orderCreatedAtBetween]='.$date_beg.'&page[number]='.$page.'&page[size]=100';
            $result = $this->request($url, [], 'GET', $jwt);
            //file_put_contents(__DIR__ . '/list.txt', var_export($result['data'], true));
            foreach ($result['data'] as $item ) {
                $list[$item['attributes']['number']] = $item['attributes'];
                $list[$item['attributes']['number']]['id_mp'] = $item['id'];
            }
            $lastPage= $result['meta']['page']['lastPage'];
            $page++;
        } while( $page <= $lastPage );

		return $list;
	}

    /**
     * Get order
     */

	public function getProducts($order_id,  $jwt) {
	    $list = [];
        $url = 'lkmarketplace.petrovich.ru/oms/v1/orders/'.$order_id.'/products';
        $result = $this->request($url, [], 'GET', $jwt);
        foreach ( $result['data'] as $item ) {
            $list[] = [
                'sku' => $item['attributes']['sku'],
                'quant' => $item['attributes']['qty'],
                'price' => $item['attributes']['amount'] / 100,
            ];
        }
//        file_put_contents(__DIR__.'/product.txt', var_export($list, true) );
        return $list;
    }

    public function getCustomer($order_id, $jwt) {
        $list = [];
        $url = 'lkmarketplace.petrovich.ru/oms/v1/orders/'.$order_id.'/customer';
        $result = $this->request($url, [], 'GET', $jwt);
//        file_put_contents(__DIR__.'/customer.txt', var_export($result, true) );
        return true;
    }

	/**
	 * Get formatted date from timestamp
	 */

	public static function getDateF($create_from_ts) {
		return gmdate(self::DATE_FORMAT, $create_from_ts);
	}

}
