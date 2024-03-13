<?php
/**
 * Controller
 */

namespace Acrit\Core\Crm;

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
		self::$profile = Helper::call(self::$MODULE_ID, 'CrmProfiles', 'getProfiles', [$profile_id]);
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

	public static function setBaseParams() {
		if (!self::$SERVER_ADDR) {
			self::$SERVER_ADDR = Settings::get("crm_conn_site");
		}
        return true;
    }

	public static function getServerAddr() {
		return self::$SERVER_ADDR;
	}

    public static function getOrderIDField() {
		$field = 'ORIGIN_ID';
		if (Settings::get('crm_orderid_field')) {
			$field = Settings::get('crm_orderid_field');
		}
		return $field;
    }

	/**
	 * Sync store with deal
	 */

	public static function syncOrderToDeal($order_data) {
		$incl_res = \Bitrix\Main\Loader::includeSharewareModule(self::$MODULE_ID);
		if ($incl_res == \Bitrix\Main\Loader::MODULE_NOT_FOUND || $incl_res == \Bitrix\Main\Loader::MODULE_DEMO_EXPIRED) {
			return;
		}
		if (!Utilities::checkConnection()) {
			return;
		}
		// Check order data
		if (!$order_data) {
			return false;
		}
		// Check start date
		$start_date_ts = self::getStartDateTs();
		if ($start_date_ts && $order_data['DATE_INSERT'] < $start_date_ts) {
			return;
		}
		// Run sync
		return DealsSync::runSync($order_data, self::$profile);
	}

    /**
     * Sync all orders by period
     */

    function syncByPeriod($sync_interval=0) {
        global $DB;
	    if (Utilities::checkConnection()) {
		    Log::getInstance(self::$MODULE_ID, 'crm')->add('(syncStoreToCRM) run period ' . $sync_interval, self::$profile['ID']);
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
					Log::getInstance(self::$MODULE_ID, 'crm')->add('(syncStoreToCRM) orders ' . print_r($orders_ids, true), self::$profile['ID'], true);
			    } catch (\Exception $e) {
					Log::getInstance(self::$MODULE_ID, 'crm')->add('(syncStoreToCRM) get orders error: "' . $e->getMessage() . '" [' . $e->getCode() . ']', self::$profile['ID']);
				}
				foreach ($orders_ids as $order_id) {
				    $order_data = $plugin->getOrder($order_id);
				    try {
					    self::syncOrderToDeal($order_data);
				    } catch (\Exception $e) {
					    Log::getInstance(self::$MODULE_ID, 'crm')->add('(syncStoreToCRM) can\'t sync of order ' . $order_data['ID'], self::$profile['ID']);
				    }
			    }
		    }
		    Log::getInstance(self::$MODULE_ID, 'crm')->add('(syncStoreToCRM) success', self::$profile['ID']);
	    }
    }



    /**
     * Other data
     */

    // Get prefix option
    public static function getPrefix() {
        $prefix = self::$profile['CONNECT_DATA']['prefix'];
        return $prefix;
    }

    // Get CRM order title
    function getOrdTitleWithPrefix(array $order_data) {
        $prefix = self::getPrefix();
        $order_num = $order_data['ID'];
	    $title = $prefix . $order_num;
        return $title;
    }

	public static function getStartDateTs() {
		$start_date_ts = false;
		$start_date = self::$profile['CONNECT_CRED']['start_date'];
		if ($start_date) {
			$start_date_ts = strtotime(date('d.m.Y 00:00:00', strtotime($start_date)));
		}
		return $start_date_ts;
	}

}