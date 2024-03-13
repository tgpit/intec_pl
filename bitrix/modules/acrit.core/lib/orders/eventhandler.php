<?
/**
 * Event handler for orders
 */

namespace Acrit\Core\Orders;

use \Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

Loc::loadMessages(__FILE__);

class EventHandler {

	/**
	 * 
	 */
	public static function onSaleOrderBeforeSaved($order) {
		global $USER, $APPLICATION;
		//
		return true;
	}

	/**
	 * 
	 */
	public static function onSaleOrderSaved($order) {
		  global $USER, $APPLICATION;
		  //
		  return true;
	}
		
}
