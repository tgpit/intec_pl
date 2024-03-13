<?php

namespace Acrit\Core\Orders\Plugins\WildberriesHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	protected static $stocks = [];
	
	public $use_v3_api = true;

	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($token, &$message) {
		$result = false;
		
		if (!$this->use_v3_api)
		{
			$res = $this->execute('/api/v2/orders', [
				'date_start' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
				'take' => 1,
				'skip' => 0,
			], [
				'METHOD' => 'GET'
			], $token);
		}
		else {
			// https://openapi.wildberries.ru/#tag/Marketplace-Sborochnye-zadaniya/paths/~1api~1v3~1orders~1status/post
			// /api/v3/orders
			$res = $this->execute('/api/v3/orders', [				
				'limit' => 1000,
				'next' => 0,
				//'dateFrom' => mktime(0, 0, 0, 2, 1, 2023)
			], [
				'METHOD' => 'GET'
			], $token);
		}
		if (isset($res['orders'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_SUCCESS');
			$result = true;
		}
		else {
			if (isset($res['error'])) {
				$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . $res['errorText'] . ' [' . $res['error'] . ']';
			}
			else {
				$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . $res;
			}
		}
		return $result;
	}

	/**
	 * Product info
	 */
	public function getProduct($barcode) {
		$result = [];
		// Fill static array $stocks
		if (empty(self::$stocks)) {
			$res = $this->execute('/api/v2/stocks', [
				'take' => 1000,
				'skip' => 0,
			], [
				'METHOD' => 'GET'
			]);
			foreach ($res['stocks'] as $stock) {
				self::$stocks[$stock['barcode']] = $stock;
			}
		}
		if (isset(self::$stocks[$barcode])) {
			$result = self::$stocks[$barcode];
		}
		else {
			$res = $this->execute('/api/v2/stocks', [
				'search' => $barcode,
				'take'   => 1,
				'skip'   => 0,
			], [
				'METHOD' => 'GET'
			]);
			if ($res['stocks'][0]) {
				$result = $res['stocks'][0];
			}
		}
		return $result;
	}

	public function getOrdersStatus(array $order_array) {
	    $list = [];
        $res = $this->execute('/api/v3/orders/status', null, [
            'METHOD' => 'POST',
            'CONTENT' => json_encode( [ 'orders' => $order_array ] ),
        ]);
        foreach ($res['orders'] as $item ) {
            $list[$item['id']] = [
                'wbStatus' =>  strtoupper($item['wbStatus']),
                'supplierStatus' =>  strtoupper($item['supplierStatus'])
            ];
        }
        return $list;
    }

    /**
     * Get orders list
     * @param array $filter
     * @param int $limit
     * @return array
     */
    public function getOrdersList(array $filter, int $limit ) {
        $list = [];
        $next = 0;
//        $limit = 1000;
        do {
            $req_filter = [
                'limit' => $limit,
                'next' => $next,
            ];
            $req_filter = array_merge($req_filter, $filter);
            $res = $this->execute('/api/v3/orders', $req_filter, [
                'METHOD' => 'GET'
            ]);
            if ($res['orders']) {
                foreach ($res['orders'] as $wb_order) {
                    $list[$wb_order['id']] = $wb_order;
                    $list[$wb_order['id']]['products'][$wb_order['skus'][0]]['quantity'] = 1;
                    $list[$wb_order['id']]['products'][$wb_order['skus'][0]]['price'] = $wb_order['convertedPrice'] / 100;
                }
            }
            $next = $res['next'];
            $count = count($res['orders']);
        } while ( $count == $limit );
        return $list;
    }

    /**
     * Get orders count
     * @param array $filter
     * @param int $limit
     * @return bool
     */
	public function getOrdersCount(array $filter, int $limit) {
		$count = false;
		$req_filter = [
			'date_start' => date(self::DATE_FORMAT, strtotime('2020-01-01 10:00:00')),
			'take' => $limit,
			'skip' => 0,
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->execute('/api/v2/orders', $req_filter);
		if ($res['total']) {
			$count = $res['total'];
		}
		return $count;
	}

    public function getLabel($order_id, $url, $status, $label_option ) {
	    $ext = $label_option['type'];
        $width = $label_option['width'] ? $label_option['width'] : 58;
        $height = $label_option['height'] ? $label_option['height'] : 40;

        $dir_name = $_SERVER["DOCUMENT_ROOT"] . '/upload/acrit.exportproplus/label/wb/';
        $file_name = $dir_name . $order_id .'-'.$width.'*'.$height.'.'.$ext;
        $domain = $url;
        if ( !$url || $url == '' ) {
            $domain = $_SERVER['HTTP_HOST'];
        }
        $pdf_name = $domain . '/upload/acrit.exportproplus/label/wb/' . $order_id . '-'.$width.'*'.$height.'.'.$ext;

        if (file_exists($file_name)) {
            return $pdf_name;
        }

        if ($status == 'CONFIRM') {
            $body = ['orders' => [$order_id]];

            $ar_fields = [
                'type' => $ext,
                'width' => $width,
                'height' => $height,
            ];
            $res = $this->execute('/api/v3/orders/stickers', $ar_fields, [
                'METHOD' => 'POST',
                'CONTENT' => json_encode($body),
            ]);
            if (!file_exists($dir_name)) {
                mkdir($dir_name, 0700, true);
            }
            file_put_contents($file_name,  base64_decode( $res['stickers'][0]['file']) );
            return $pdf_name;
        } else {
            return false;
        }

    }
}
