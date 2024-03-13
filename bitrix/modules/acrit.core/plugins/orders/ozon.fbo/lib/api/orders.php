<?php

namespace Acrit\Core\Orders\Plugins\OzonFboHelpers;

use \Bitrix\Main\Localization\Loc;

require_once __DIR__ . '/request.php';

class Orders extends Request {

	public function __construct($strClientId, $strApiKey, $intProfileId, $strModuleId) {
		parent::__construct($strClientId, $strApiKey, $intProfileId, $strModuleId);
	}

	/**
	 * Check connection
	 */
	public function checkConnection(&$message) {
		$result = false;
		$res = $this->execute('/v2/posting/fbs/list', [
			'dir' => 'desc',
			'filter' => [
				"since" => "2022-11-03T00:00:00.944Z",
			],
			'limit' => 1,
		], [
			'METHOD' => 'POST'
		]);
		if (isset($res['result'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_ERROR') . $res['message'] . ' [' . $res['code'] . ']';
		}
		return $result;
	}
	
	/**
	 * Check connection version 3 api
	 */
	public function checkConnection_v3(&$message) {
		$result = false;
		$to = gmdate("Y-m-d\TH:i:s.000\Z", time());
		$res = $this->execute('/v3/posting/fbs/list', [
			'dir' => 'desc',
			'filter' => [
				"since" => "2022-11-03T00:00:00.944Z",
				"status" => "",
				"to" => $to
			],
			'limit' => 1,
			'translit' => true,
			'with' => [
				'analytics_data' => true,
				'financial_data' => true
			],
		], [
			'METHOD' => 'POST'
		]);
		if (isset($res['result'])) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_SUCCESS');
			$result = true;
		}
		elseif ($res['error']) {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_ERROR') . $res['error']['message'] . ' [' . $res['error']['code'] . ']';
		}
		else {
			$message = Loc::getMessage('ACRIT_CRM_PLUGIN_OZON_CHECK_ERROR') . $res['message'] . ' [' . $res['code'] . ']';
		}
		return $result;
	}

	/**
	 * Get postings list
	 */
	public function getPostingsList(array $filter, int $limit) {
		$list = [];
		$req_filter = [
//			'updated_at' => [
//				'from' => '2020-01-01T00:00:00.944Z',
//			],
		];
		$req_filter = array_merge($req_filter, $filter);
		$res = $this->execute('/v2/posting/fbs/list', [
			'dir' => 'desc',
			'filter' => $req_filter,
			'limit' => $limit,
		], [
			'METHOD' => 'POST'
		]);
		if ($res['result']) {
			$list = $res['result'];
		}
		return $list;
	}
	
	/**
	 * Get postings list version 2 ozon api
	*/
	public function getPostingsList_v2(array $filter, int $limit) {
		$from_timestamp = $filter['order_created_at']['from_timestamp'];
		$filter['order_created_at']['from_timestamp'] = NULL;
		$list = [];
		$is_filter_empty = empty($filter);		
		//$from = $filter['updated_at']['from'];
		$term_h = (time() - $from_timestamp) / 3600;		
		$from = gmdate("Y-m-d\TH:i:s.000\Z", $from_timestamp);
//		$from = gmdate("Y-m-d\TH:i:s.000\Z", time() - 3600 * $term_h);
		if ( $is_filter_empty )
		{	
			$term_h = 24;			
			$from = gmdate("Y-m-d\TH:i:s.000\Z", time() - 3600 * $term_h);
		}
		$to = gmdate("Y-m-d\TH:i:s.000\Z", time());
		$req_filter = [
			"since" => $from,
			"status" => "",
			"to" => $to,
		];
		$req_filter = array_merge($req_filter, $filter);
        $offset = 0;
        $next = true;
		do {
            $res = $this->execute('/v2/posting/fbo/list', [
                'dir' => 'desc',
                'filter' => $req_filter,
                'limit' => $limit,
                'offset' => $offset,
                'translit' => true,
                'with' => [
                    'analytics_data' => true,
                    'financial_data' => true
                ],
            ],
                [
                    'METHOD' => 'POST'
                ]
            );
            if ($res['result']) {
                $list = array_merge($res['result'], $list);
//                file_put_contents(__DIR__.'/result.txt', var_export($res['result'], true));
//                file_put_contents(__DIR__.'/list.txt', var_export($list, true));
            }
            $offset += $limit;
//            file_put_contents(__DIR__.'/offset.txt', var_export($offset, true));
            $next = count($res['result']) < $limit ? false : true;
        } while ( $next && $offset < 100000);
		return $list;
	}

	/**
	 * Get posting
	 */
	public function getPosting($posting_number) {
		$object = false;
		$res = $this->execute('/v2/posting/fbo/get', [
			'posting_number' => $posting_number,
			'with' => [
				'analytics_data' => true,
			],
		], [
			'METHOD' => 'POST'
		]);
		if ($res['result']) {
			$object = $res['result'];
		}
		return $object;
	}
}
