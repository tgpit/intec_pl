<?php
/**
 * Cache
 *
 * @mail support@s-production.online
 * @link s-production.online
 */

namespace Acrit\Core\Export\Plugins\VkHelpers;

use
	\Acrit\Core\Helper,
	\Bitrix\Main\Entity;

Helper::loadMessages(__FILE__);

class Cache
{
	private static $CACHE = [];

	protected static function getId($key, $params) {
		return md5($key . serialize($params));
	}

	public static function add($key, $params, $value) {
		self::$CACHE[self::getId($key, $params)] = $value;
	}

	public static function hasValue($key, $params) {
		$result = false;
		if (isset(self::$CACHE[self::getId($key, $params)])) {
			$result = true;
		}
		return $result;
	}

	public static function get($key, $params) {
		$result = false;
		if (isset(self::$CACHE[self::getId($key, $params)]) && !is_null(self::$CACHE[self::getId($key, $params)])) {
			$result = self::$CACHE[self::getId($key, $params)];
		}
		return $result;
	}
}
