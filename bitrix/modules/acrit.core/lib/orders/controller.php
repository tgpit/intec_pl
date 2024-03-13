<?php
/**
 * Controller class
 * Class for linking all parts of the synchronization system
 */

namespace Acrit\Core\Orders;

\Bitrix\Main\Loader::includeModule("sale");

use Bitrix\Main,
	Bitrix\Main\Type,
	Bitrix\Main\Entity,
	Bitrix\Main\Localization\Loc,
	Bitrix\Main\SiteTable,
	Bitrix\Sale,
	\Acrit\Core\Log,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class Controller
{
	const APP_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_auth.php';
	const EVENTS_HANDLER = '/bitrix/acrit_#MODULE_ID#_crm_handler.php';
    public static $SERVER_ADDR;
    protected static $MANUAL_RUN = false;

	static $MODULE_ID = '';
	static $profile = false;

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	public static function setProfile(int $profile_id) {
		self::$profile = Helper::call(self::$MODULE_ID, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		//\Helper::Log('(getOrderProfile) selected profile "' . self::$profile['id'] . '"');
	}

	/**
	 * Get a link for process of external requests
	 */
	public static function getAppHandler() {
		$module_code = str_replace('acrit.', '', self::$MODULE_ID);
		$link = str_replace('#MODULE_ID#', $module_code, self::APP_HANDLER);
		return $link;
	}

	/**
	 * Get plugin class for the profile
	 */
	public static function getPlugin($profile) {
		$plugin = false;
		if (strlen($profile['PLUGIN'])) {
			$arProfilePlugin = Exporter::getInstance(self::$MODULE_ID)->getPluginInfo($profile['PLUGIN']);
			if (is_array($arProfilePlugin)) {
				$strPluginClass = $arProfilePlugin['CLASS'];
				if (strlen($strPluginClass) && class_exists($strPluginClass)) {
					$plugin = new $strPluginClass(self::$MODULE_ID);
					$plugin->setProfileArray($profile);
				}
			}
		}
		return $plugin;
	}

	/**
	 * Flag of the bulk manual synchronization process
	 */
	public static function setBulkRun() {
		self::$MANUAL_RUN = true;
		Rest::setBulkRun();
    }

	public static function isBulkRun() {
		return self::$MANUAL_RUN;
    }

	/**
	 * Sync store with order
	 */

	public static function syncExtToStore($ext_order) {
//        file_put_contents(__DIR__.'/syncorders.txt', date("m.d.y H:i:s").' - '.$ext_order['ID'].PHP_EOL, FILE_APPEND );
		Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncExtToStore) external order ' . print_r($ext_order, true), self::$profile['ID'], true);
		$incl_res = \Bitrix\Main\Loader::includeSharewareModule(self::$MODULE_ID);
		if ($incl_res == \Bitrix\Main\Loader::MODULE_NOT_FOUND || $incl_res == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
			return;
		}
		// Has synchronization active
		$sync_active = (self::$profile['ACTIVE'] == 'Y');
		if (!$sync_active) {
			return;
		}
        // Has unuse stage
        if ( is_array(self::$profile['STAGES']['table_unuse']) && in_array(trim($ext_order['STATUS_ID']), self::$profile['STAGES']['table_unuse'])) {
//            file_put_contents(__DIR__.'/stages_unuse.txt', $ext_order['ID'].' - '.$ext_order['STATUS_ID'].PHP_EOL, FILE_APPEND);
            return;
        }
		// Check order data
		$order_id = OrderSync::findOrder($ext_order, self::$profile);
		// Ignore old external orders
		$start_date_ts = self::getStartDateTs();
//        file_put_contents(__DIR__.'/startdate.txt', $start_date_ts.' - '.$ext_order['ID'].'-'.$ext_order['DATE_INSERT'].PHP_EOL, FILE_APPEND);
		if ($start_date_ts && $ext_order['DATE_INSERT'] < $start_date_ts) {
			return;
		}
		// Get profile
		if (!self::$profile) {
			Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncExtToStore) error: profile empty', self::$profile['ID'], true);
			return;
		}
        if ($ext_order['MODULE_ACTION'] == 'change_status'){
			return OrderSync::runSyncOrderStatus($order_id, $ext_order, self::$profile);
		}
		// Run sync
        try {
            return OrderSync::runSync($order_id, $ext_order, self::$profile);
        }  catch ( \Throwable  $e ) {
            $errors = [
                'error_php' => $e->getMessage(),
                'line' => $e->getLine(),
                'stek' => $e->getTraceAsString(),
                'file' => $e->getFile(),
            ];
            file_put_contents(__DIR__.'/errors2.txt', var_export($errors, true));
        }
	}

	/**
	 * Sync all orders by period
	 */

	 public static function syncByPeriod($sync_interval=0) {
		Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncByPeriod) run period ' . $sync_interval, self::$profile['ID']);
		// Get plugin object
		$plugin = self::getPlugin(self::$profile);
		// List of orders, changed by last period (if period is not set than get all orders)
		if ($plugin) {
			$change_date_from = false;
			if ($sync_interval > 0) {
				$change_date_from = time() - $plugin->modifSyncInterval($sync_interval);
			}
			try {
				$orders_ids = $plugin->getOrdersIDsList(false, $change_date_from);
//                file_put_contents(__DIR__.'/orders.txt', json_encode($orders_ids), true);
//                file_put_contents(__DIR__.'/orders.txt', var_export($orders_ids, true));
				Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncByPeriod) orders ' . print_r($orders_ids, true), self::$profile['ID'], true);
			} catch (\Exception $e) {
				Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncByPeriod) get orders error: "' . $e->getMessage() . '" [' . $e->getCode() . ']', self::$profile['ID']);
			}
			$i = 0;
			foreach ($orders_ids as $order_id) {
				$order_data = $plugin->getOrder($order_id);

				try {
//                    file_put_contents(__DIR__.'/syncorders.txt', date("m.d.y H:i:s").' - '.$order_data['ID'].PHP_EOL, FILE_APPEND );
					self::syncExtToStore($order_data);
				} catch ( \Throwable  $e ) {
                    $errors = [
                        'error_php' => $e->getMessage(),
                        'line' => $e->getLine(),
                        'stek' => $e->getTraceAsString(),
                        'file' => $e->getFile(),
                    ];
                    file_put_contents(__DIR__.'/errors.txt', var_export($errors, true));
                }
//				catch (\Exception $e) {
//					Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncByPeriod) can\'t sync of order ' . $order_data['ID'] . ' [error: ' . $e->getMessage() . ' (' . $e->getCode() . ')]', self::$profile['ID']);
//				}
                $i++;
//                if ( $i%500 === 0  ) {
//                    sleep(60 );
//                    file_put_contents(__DIR__.'/sleep.txt', 'sleep'.PHP_EOL, FILE_APPEND );
//                }
			}

		}
		Log::getInstance(self::$MODULE_ID, 'orders')->add('(syncByPeriod) success', self::$profile['ID']);
	}

	/**
	 * Get start date of synchronization
	 */
	public static function getStartDateTs() {
		$start_date_ts = false;
		$start_date = self::$profile['CONNECT_CRED']['start_date'];
		if ($start_date) {
			$start_date_ts = strtotime(date('d.m.Y 00:00:00', strtotime($start_date)));
		}
		return $start_date_ts;
	}

	/**
	 * Get default site
	 */
	public static function getSiteDef() {
		$site_id = false;
		$site_default = false;
		$result = \Bitrix\Main\SiteTable::getList([]);
		while ($site = $result->fetch()) {
			if (!$site_default) {
				$site_default = $site;
			}
			if ($site['DEF'] == 'Y') {
				$site_default = $site;
			}
		}
		if ($site_default) {
			$site_id = $site_default['LID'];
		}
		return $site_id;
	}

}