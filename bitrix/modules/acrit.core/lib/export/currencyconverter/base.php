<?
/**
 * Base currency converter class
 */

namespace Acrit\Core\Export\CurrencyConverter;

use
	\Bitrix\Main\Localization\Loc,
	\Acrit\Core\Helper;

/**
 * Base interface for settings
 */
abstract class Base {
	
	static $arCacheList;
	
	/**
	 *	Get name of converter
	 */
	abstract public static function getName();
	
	/**
	 *	Get code of converter
	 */
	abstract public static function getCode();
	
	/**
	 *	Get sort index
	 */
	abstract public static function getSort();
	
	/**
	 *	Convert currency
	 */
	abstract public static function convert($fPrice, $strFrom, $strTo);
	
	/**
	 *	Get currency convert factor
	 */
	abstract public static function getFactor($strFrom, $strTo);
	
	/**
	 *	Get list of converters
	 */
	final public static function getConverterList(){
		$arResult = &static::$arCacheList;
		#
		if(!is_array($arResult) || empty($arResult)) {
			$resHandle = opendir(__DIR__);
			while ($strFile = readdir($resHandle)) {
				if($strFile != '.' && $strFile != '..') {
					$strFullFilename = __DIR__.DIRECTORY_SEPARATOR.$strFile;
					if(toUpper(pathinfo($strFile, PATHINFO_EXTENSION))=='PHP') {
						require_once($strFullFilename);
					}
				}
			}
			closedir($resHandle);
			$arResult = array();
			foreach(get_declared_classes() as $strClass){
				if(is_subclass_of($strClass, __CLASS__)) {
					$strCode = $strClass::getCode();
					$arItem = array(
						'NAME' => $strClass::getName(),
						'SORT' => $strClass::getSort(),
						'CLASS' => $strClass,
					);
					$arResult[$strCode] = $arItem;
				}
			}
			uasort($arResult, '\Acrit\Core\Helper::sortBySort');
		}
		#
		return $arResult;
	}

	public static function getCurrencyDecimalsCount($strCurrency){
		$intResult = 2;
		if(count($arCurrencies = Helper::getCurrencyList()) > 0){
			if(isset($arCurrencies[$strCurrency]) && is_array($arCurrency = $arCurrencies[$strCurrency])){
				if(isset($arCurrency['DECIMALS']) && is_numeric($arCurrency['DECIMALS'])){
					if($arCurrency['DECIMALS'] >= 0){
						$intResult = intVal($arCurrency['DECIMALS']);
					}
				}
			}
		}
		return $intResult;
	}

	protected static function roundDecimalsCount($fValue, $strCurrency){
		return round($fValue, static::getCurrencyDecimalsCount($strCurrency));
	}
	
}
