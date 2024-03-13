<?php
/**
 * Periodical synchronization by agents
 */

namespace Acrit\Core\Orders;

use Bitrix\Main,
    Bitrix\Main\DB\Exception,
    Bitrix\Main\Config\Option,
	\Acrit\Core\Helper;

class PeriodSync
{
	static $MODULE_ID = '';

	public static function setModuleId($value) {
		self::$MODULE_ID = $value;
		Settings::setModuleId($value);
	}

	// Create agent
	public static function set($profile_id) {
		$result = true;
		$profile = Helper::call(self::$MODULE_ID, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		$next_exec = (\Bitrix\Main\Type\DateTime::createFromTimestamp(time() - 60))->toString();
		// If use 1 agent
		if ($profile['SYNC']['add']['period']) {
			$sync_schedule = $profile['SYNC']['add']['period'];
			$agent_period = $sync_schedule * 60;
			if ($profile['SYNC']['add_active'] == 'Y' && $agent_period) {
				static::remove($profile_id);
				\CAgent::AddAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id);", self::$MODULE_ID, "N", $agent_period, '', 'Y', $next_exec);
			}
			else {
				self::remove($profile_id);
			}
		}
		// If use more agents
		elseif (is_array($profile['SYNC']['add']) && !empty($profile['SYNC']['add'])) {
			foreach ($profile['SYNC']['add'] as $variant => $item) {
				$agent_period = $profile['SYNC']['add'][$variant]['period'];
				if ($profile['SYNC']['add_active'] == 'Y' && $agent_period) {
					if ($profile['SYNC']['add'][$variant]['measure'] == 'h') {
						$agent_period *= 3600;
					} else {
						$agent_period *= 60;
					}
					static::remove($profile_id, $variant);
					\CAgent::AddAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id, $variant);", self::$MODULE_ID, "N", $agent_period, '', 'Y', $next_exec);
				}
				else {
					self::remove($profile_id, $variant);
				}
			}
		}
		return $result;
	}

	// Remove agent
	public static function remove($profile_id, $variant=0) {
		$result = true;
			\CAgent::RemoveAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id);", self::$MODULE_ID);
			\CAgent::RemoveAgent("\\Acrit\\Core\\Orders\\PeriodSync::run('" . self::$MODULE_ID . "', $profile_id, $variant);", self::$MODULE_ID);
		return $result;
	}

	// Run sync
	public static function run($module_id, $profile_id, $variant=0) {
		if(!is_object($GLOBALS['USER'])){
			$GLOBALS['USER'] = new \CUser;
		}
		// Profile data
		$profile = Helper::call($module_id, 'OrdersProfiles', 'getProfiles', [$profile_id]);
		$sync_active = ($profile['ACTIVE'] == 'Y');
		// Run sync
		if ($sync_active) {
			Controller::setModuleId($module_id);
			Controller::setProfile($profile_id);
			$sync_interval = self::getSyncInterval($profile, $variant);
			Settings::set('last_update_ts', time());
			Controller::syncByPeriod($sync_interval);
		}
		if (!$variant) {
			$agent_name = "\\Acrit\\Core\\Orders\\PeriodSync::run('$module_id', $profile_id);";
		}
		else {
			$agent_name = "\\Acrit\\Core\\Orders\\PeriodSync::run('$module_id', $profile_id, $variant);";
		}
		return $agent_name;
	}

	public static function getSyncInterval($profile, $variant=0) {
		if ($variant) {
			$profile_range = $profile['SYNC']['add'][$variant]['range'];
			if ($profile['SYNC']['add'][$variant]['measure'] == 'h') {
				$profile_range *= 3600;
			}
			else {
				$profile_range *= 60;
			}
		}
		else {
			if ($profile['SYNC']['add']['range']) {
				$profile_range = (int) $profile['SYNC']['add']['range'];
			}
			else {
				$profile_range = (int) $profile['SYNC']['add']['period'] * 60 * 3;
			}
		}
		//TODO add link with profile
//		$last_update_period = 0;
//		$last_update_ts = Settings::get('last_update_ts');
//		if ($last_update_ts) {
//			$last_update_period = time() - $last_update_ts;
//		}
//		$sync_range = $last_update_period > $profile_range ? $last_update_period : $profile_range;
		$sync_range = $profile_range;
		return $sync_range;
	}
}
