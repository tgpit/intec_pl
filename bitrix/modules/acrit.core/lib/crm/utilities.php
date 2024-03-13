<?
/**
 * Utilities for data synchronization
 */

namespace Acrit\Core\Crm;

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Main\Context,
	Bitrix\Currency\CurrencyManager,
	Bitrix\Sale\Order,
	Bitrix\Sale\Basket,
	Bitrix\Sale\Delivery,
	Bitrix\Sale\PaySystem,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;
use PhpOffice\PhpSpreadsheet\Exception;

Loc::loadMessages(__FILE__);

class Utilities {

	// Values equal check
	public static function isEqual($order_value, $deal_value) {
		$res = false;
		if ($order_value == [false]) {
			$order_value = [];
		}
		if ($deal_value == [false]) {
			$deal_value = [];
		}
		if ( !is_array($order_value) && !is_array($deal_value)) {
			if ($order_value == $deal_value) {
				$res = true;
			}
		}
		elseif (is_array($order_value) && is_array($deal_value)) {
			if (count($order_value) == count($deal_value)) {
				$res = true;
				foreach ($order_value as $k => $value) {
					if ($value != $deal_value[$k]) {
						$res = false;
					}
				}
				foreach ($deal_value as $k => $value) {
					if ($value != $order_value[$k]) {
						$res = false;
					}
				}
			}
		}
		return $res;
	}

	public static function getPhonesFormats($phone){
		$phones = [];
		if(strlen($phone)){
			$phoneUnformatted = preg_replace('/[^+\d]/', '', $phone);
			$phoneFormatted = preg_replace(
				[
					'/^\+?7([\d]{3})([\d]{3})([\d]{2})([\d]{2})$/m',
					'/^\+?380([\d]{2})([\d]{3})([\d]{2})([\d]{2})$/m',
					'/^\+?996([\d]{3})([\d]{3})([\d]{3})$/m',
					'/^\+?998([\d]{2})([\d]{3})([\d]{4})$/m',
				],
				[
					'+7 (${1}) ${2}-${3}-${4}', // +7 (___) ___-__-__
					'+380 (${1}) ${2}-${3}-${4}', // +380 (__) ___-__-__
					'+996 (${1}) ${2}-${3}', // +996 (___) ___-___
					'+998-${1}-${2}-${3}', // +998-__- ___-____
				],
				$phoneUnformatted
			);
			$phones = array_unique([$phone, $phoneFormatted, $phoneUnformatted]);
		}
		return $phones;
	}

	// Convert encoding
	public static function convEncForDeal($value) {
		if (!Helper::isUtf()) {
			$value = \Bitrix\Main\Text\Encoding::convertEncoding($value, "Windows-1251", "UTF-8");
		}
		return $value;
	}

	/**
	 * Check system parameters
	 */

	public static function checkConnection() {
		$res = false;
		$status = self::checkModuleStatus();
		if ($status['connect']) {
			$res = true;
		}
		return $res;
	}

	public static function checkModuleStatus() {
		$res = [
			'auth_file' => false,
			'store_handler_file' => false,
			'crm_handler_file' => false,
			'app_info' => false,
			'auth_info' => false,
			'connect' => false,
			'store_events' => false,
			'crm_events' => false,
			'crm_events_uncheck' => false,
		];
//		// Site base directory
//		$site_default = \Helper::getSiteDef();
//		$abs_root_path = $_SERVER['DOCUMENT_ROOT'] . $site_default['DIR'];
//		// Check auth file
//		if (file_exists($abs_root_path . 'bitrix/acrit_export_auth.php')) {
//			$res['auth_file'] = true;
//		}
//		// Check handler files
//		if (file_exists($abs_root_path . 'bitrix/acrit_export_bgr_run.php')) {
//			$res['store_handler_file'] = true;
//		}
//		if (file_exists($abs_root_path . 'bitrix/acrit_export_handler.php')) {
//			$res['crm_handler_file'] = true;
//		}
		// Availability of B24 application data
		if (Rest::getAppInfo()) {
			$res['app_info'] = true;
			// Availability of connection data
			if (Rest::getAuthInfo()) {
				$res['auth_info'] = true;
			}
		}
		if ($res['app_info'] && $res['auth_info']) {
			// Availability of an order change handler
//			if (self::checkStoreHandlers()) {
//				$res['store_events'] = true;
//			}
			// Relevance of data for connecting to B24
			$resp = Rest::execute('app.info', [], false, true, false);
			if ($resp && !$resp['error']) {
				$res['connect'] = true;
//				// Availability of a deal change handler
//				if (self::checkCrmHandlers()) {
//					$res['crm_events'] = true;
//				}
//				if (self::$profile['CONNECT_CRED']['direction'] == 'ctos') {
//					$res['crm_events_uncheck'] = true;
//				}
			}
		}

		return $res;
	}

}
