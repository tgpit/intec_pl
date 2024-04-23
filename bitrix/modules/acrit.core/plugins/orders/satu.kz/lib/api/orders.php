<?php

namespace Acrit\Core\Orders\Plugins\SatuKzHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {
	const FILTER_CREATED_FROM_FIELD = 'date_start';
	const FILTER_UPDATED_FROM_FIELD = 'update_at_from';
	const DATE_FORMAT = 'Y-m-d\TH:i:s';

	protected static $arOrders = [];


	public function __construct($obPlugin) {
		parent::__construct($obPlugin);
	}

	/**
	 * Check connection
	 */
	public function checkConnection($token, &$message) {
		$result = false;
        $req_filter = [
            'dateFrom' => date(self::DATE_FORMAT, strtotime('2024-01-01 10:00:00')),
        ];
		try {
            $res = $this->execute('orders/list', $req_filter, [
                'METHOD' => 'GET'
            ], 'Bearer '.$token);

        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
        }
		if (isset($res['orders'])) {
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


    public function getList($date_ts) {
        $list = [];
        try {
        $req_filter = [
            'dateFrom' => date(self::DATE_FORMAT, $date_ts),
        ];
        $res = $this->execute('orders/list', $req_filter, [
            'METHOD' => 'GET'
        ]);
            foreach ($res['orders'] as $item) {
            $list[$item['id']] = $item;
        }} catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
        }
		return $list;
	}

    public function getOrder($order_id) {
        $res = [];
        try {
            $res = $this->execute('orders/'.$order_id, false, [
                'METHOD' => 'GET'
            ]);
        } catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
        }
        return $res['order'];
    }

    /**
     * Get order
     */


	/**
	 * Get formatted date from timestamp
	 */

	public static function getDateF($create_from_ts) {
		return gmdate(self::DATE_FORMAT, $create_from_ts);
	}

}
