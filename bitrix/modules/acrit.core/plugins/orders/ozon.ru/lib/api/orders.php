<?php

namespace Acrit\Core\Orders\Plugins\OzonRuHelpers;

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
				"since" => gmdate("Y-m-d\TH:i:s.000\Z", time() - 24*7*60*60),
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
	 * Get postings list version 3 ozon api
	*/
	public function getPostingsList_v3(array $filter, int $limit) {
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
            $res = $this->execute('/v3/posting/fbs/list', [
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
                $list = array_merge($res['result']['postings'], $list);
            }
            $offset += $limit;
            $next = $res['result']['has_next'];
        } while ( $next && $offset < 100000);
		return $list;
	}

	/**
	 * Get posting
	 */
	public function getPosting($posting_number) {
		$object = false;
		$res = $this->execute('/v3/posting/fbs/get', [
			'posting_number' => $posting_number,
			'with' => [
				'analytics_data' => true,
                'customer' => true,
			],
		], [
			'METHOD' => 'POST'
		]);
		if ($res['result']) {
			$object = $res['result'];
		}
		return $object;
	}

    public function getLabel($posting_number, $url, $status) {
//        file_put_contents(__DIR__.'/number.txt', var_export($posting_number, true));

        $dir_name = $_SERVER["DOCUMENT_ROOT"] . '/upload/acrit.exportproplus/label/ozon/';
        $file_name = $dir_name . $posting_number . '.pdf';
        $domain = $url;
        if ( !$url || $url == '' ) {
            $domain = $_SERVER['HTTP_HOST'];
        }
        $pdf_name = $domain . '/upload/acrit.exportproplus/label/ozon/' . $posting_number . '.pdf';

        if (file_exists($file_name)) {
            return $pdf_name;
        }

        try {
            if ($status == 'awaiting_deliver') {
                $res = $this->executeHttpClient(
                    '/v2/posting/fbs/package-label',
                    $posting_number
                );

                if (is_array(json_decode($res, true))) {
                    file_put_contents(__DIR__ . '/arr.txt', var_export(json_decode($res, true), true));
                    return false;
                } else {
                    if (!file_exists($dir_name)) {
                        mkdir($dir_name, 0700, true);
                    }
                    file_put_contents($file_name, $res);
                }
            } else {
                return false;
            }
        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
            ];
//            return false;
            file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
        }
        return $pdf_name;
    }
}
