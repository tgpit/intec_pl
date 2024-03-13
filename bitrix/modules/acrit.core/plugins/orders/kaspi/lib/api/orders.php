<?php

namespace Acrit\Core\Orders\Plugins\KaspiHelpers;

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
		$res = $this->request('cities', [], $strToken);
		if (isset($res['data'])) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_KASPI_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_KASPI_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_ORDERS_PLUGIN_KASPI_CHECK_ERROR');
		}
		return $result;
	}

	/**
	 * Get orders list
	 */

	public function getList( $filter_date ) {
//        file_put_contents(__DIR__.'/filterdate.txt', $filter_date );
//        $filter_date = 1677625200000;
        $list = [];
		$states = ['NEW', 'SIGN_REQUIRED', 'PICKUP', 'DELIVERY', 'KASPI_DELIVERY', 'ARCHIVE'];
        $date_fin = strtotime( date('d.m.Y H:i:s') ) * 1000;
        foreach ( $states as $state ) {
            $date_beg = $filter_date;
            do {
                $date_end = $date_beg + (14 * 24 * 60 * 60 * 1000);
                $page = 0;
                do {
                    $method = 'orders?page[number]='.$page.'&page[size]=100&filter[orders][state]=' . $state . '&filter[orders][creationDate][$ge]=' . $date_beg . '&filter[orders][creationDate][$le]=' . $date_end;
                    $data = $this->request($method, [] );
                    foreach ($data['data'] as $item) {
                        $list[$item['attributes']['code']] = $item;
                    }
                    $page++;
                }
                while ( $page < $data['meta']['pageCount'] );
                $date_beg = $date_end;
            } while ($date_end <= $date_fin);
        }
//        file_put_contents(__DIR__.'/list.txt', var_export($list, true));
		return $list;
	}

	public function getProducts($id) {
        $list = [];
        $method = 'orders/'.$id.'=/entries';
        $data = $this->request($method, [] );
        $products = [];
        $i = 0;
        foreach ( $data['data'] as $item) {
            $products[$i]['quantity'] = $item['attributes']['quantity'];
            $products[$i]['item_price'] = $item['attributes']['basePrice'];
            $product_id = $item['relationships']['product']['data']['id'];
            $sku =  $this->getSku($product_id)['data']['attributes'];
            $products[$i]['sku_code'] = $sku['code'];
            $products[$i]['name'] = $sku['name'];
            $i++;
        }
//        file_put_contents(__DIR__.'/skus1.txt', var_export($data, true));
//        file_put_contents(__DIR__.'/skus2.txt', var_export($products, true));
        return $products;
    }

    public function getSku($id) {
        $method = $ext_url='masterproducts/'.$id.'==/merchantProduct';
        return $sku =  $this->request($method, [] );
    }



	/**
	 * Get formatted date from timestamp
	 */

	public static function getDateF($create_from_ts) {
		return gmdate(self::DATE_FORMAT, $create_from_ts);
	}

}
