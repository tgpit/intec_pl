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
//				$message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . $res;
                $message = Loc::getMessage('ACRIT_CRM_PLUGIN_WB_CHECK_ERROR') . implode(' - ',$res);
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

    public function newSupply($supply_name) {
        $res = $this->execute('/api/v3/supplies',
            null,
            [
                'METHOD' => 'POST',
                'CONTENT' => json_encode( ['name' => $supply_name]),
            ]
        );
        return $res;
    }

    public function getSupplies(){
	    $last_list = [];
	    $next = 0;
	    do {
            $res = $this->execute('/api/v3/supplies',
                [
                    'limit' => 1000,
                    'next' => $next
                ],
                ['METHOD' => 'GET']
            );
            $next =  $res['next'];
            foreach ($res['supplies'] as $item) {
                if (!$item['closedAt']) {
                    $last_list[] = $item;
                }
            }
        } while ($next == 0 );
        return $last_list;
    }

    public function operateOrder( $arr ) {
        try {
        $res = $this->execute('/api/v3/supplies/'.$arr['supply'].'/orders/'.$arr['id'],null , [
            'METHOD' => 'PATCH'
        ]);
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
        }
        return $res;
    }

    public function getOrdersListConfirm() {
        $list = [];
        $res = $this->execute('/api/v3/orders/new', [], ['METHOD' => 'GET']);
        if (is_array($res) && $res['orders']) {
            $list = $res['orders'];
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
            if (is_array($res) && $res['orders']) {
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

    public function getLabel($list, $url, $label_option ) {
//        file_put_contents(__DIR__ . '/list.txt', var_export($list, true));

        $separator = $this->arProfile['OTHER']['sticker']['separator'] ? : '';
        $label_confirm = [];
        $ext = $label_option['type'];
        $width = $label_option['width'] ? $label_option['width'] : 58;
        $height = $label_option['height'] ? $label_option['height'] : 40;
        $dir_name = $_SERVER["DOCUMENT_ROOT"] . '/upload/acrit.exportproplus/label/wb/';

        $confirm_list = [];
        foreach ($list as $key=>$item ) {
            if ($item['supplierStatus'] == 'CONFIRM') {
                $confirm_list[] = $key;
            }
        }
//        file_put_contents(__DIR__ . '/confirm_list.txt', var_export($confirm_list, true));
        $limit = 90;
        $confirm_list_for = ceil(count($confirm_list) / $limit);

        for ($i = 1; $i < $confirm_list_for + 1; $i++ ) {
            $order_ids = [];
            for ($j = (($i - 1) * $limit); $j < ($i * $limit) && $j < count($confirm_list); $j++) {
                $order_ids[] = $confirm_list[$j];
            }

            $body = ['orders' => $order_ids];
            $ar_fields = [
                'type' => $ext,
                'width' => $width,
                'height' => $height,
            ];

            if (!empty($order_ids)) {
//                file_put_contents(__DIR__ . '/order_ids.txt', var_export($order_ids, true));
                $res = $this->execute('/api/v3/orders/stickers', $ar_fields, [
                    'METHOD' => 'POST',
                    'CONTENT' => json_encode($body),
                ]);
//                file_put_contents(__DIR__ . '/result.txt', var_export($res, true));
                if (is_array($res['stickers'])) {
                    foreach ($res['stickers'] as $item) {
                        $label_confirm[$item['orderId']] = [
                            'partA' => $item['partA'],
                            'partB' => $item['partB'],
                            'file' => $item['file'],
                        ];
                    }
                }
            }
        }
//        file_put_contents(__DIR__ . '/arr_label_confirm.txt', var_export($label_confirm, true));
//        file_put_contents(__DIR__ . '/count_label_confirm.txt', var_export(count($label_confirm), true));
        $label = [];
        foreach ( $list as $key=>$item ) {
            $file = false;
            $number_sticker = false;
            $domain = $url;

            if (!$url || $url == '') {
                $domain = $_SERVER['HTTP_HOST'];
            }
            $file_name = $dir_name . $key . '-' . $width . '*' . $height . '.' . $ext;
            $pdf_name = $domain . '/upload/acrit.exportproplus/label/wb/' . $key . '-' . $width . '*' . $height . '.' . $ext;

            if (file_exists($file_name)) {
                $file = $pdf_name;
            } else {
                if (!file_exists($dir_name)) {
                    mkdir($dir_name, 0700, true);
                }
                try {
                    if ( is_array($label_confirm[$key]) && $label_confirm[$key]['file'] ) {
                        file_put_contents($file_name, base64_decode($label_confirm[$key]['file']));
                    }
                    if (file_exists($file_name)) {
                        $file = $pdf_name;
                    }
                } catch (\Throwable  $e) {
                    $errors = [
                        'error_php' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'stek' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                    ];
                    file_put_contents(__DIR__ . '/errors2.txt', var_export($errors, true));
                }
            }
            if (  is_array($label_confirm[$key]) &&  $label_confirm[$key]['partA'] ) {
                $number_sticker = $label_confirm[$key]['partA'] . $separator . $label_confirm[$key]['partB'];
            }

            $label[$key] = [
                'file' => $file,
                'number_sticker' =>  $number_sticker
            ];
        }

//        $file_count = 0;
//        $sticker_count = 0;
//        $alone_file_count = 0;
//        $all = 0;
//        foreach ($label as $item) {
//            if ($item['file']) {
//                $file_count++;
//            }
//            if ($item['number_sticker']) {
//                $sticker_count++;
//            }
//            if ($item['file'] && !$item['number_sticker'] ) {
//                $alone_file_count++;
//            }
//            $all++;
//        }
//        file_put_contents(__DIR__ . '/label.txt', var_export($label, true));
//        file_put_contents(__DIR__ . '/count_full_label.txt', var_export(['all'=> $all, 'file'=> $file_count, 'sticker' => $sticker_count, 'alone' => $alone_file_count], true));
        return $label;
    }
}
