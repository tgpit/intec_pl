<?
/**
 * Class to work with JSON
 */

namespace Acrit\Core;

use
	\Acrit\Core\Helper;

class Json extends \Bitrix\Main\Web\Json {
	
	static protected $intTabSize = 4; // space count for one tab in formatted output
	
	/**
	 *	Helper for add value
	 */
	public static function addValue($mValue){
		$arResult = array();
		if(is_array($mValue)){
			foreach($mValue as $strValueItem){
				$arResult[] = $strValueItem;
			}
		}
		else {
			$arResult = $mValue;
		}
		return $arResult;
	}
	
	/**
	 *	Set http header for JSON file
	 */
	public static function setHttpHeader(){
		header('Content-Type: application/json; charset='.(Helper::isUtf()?'utf-8':'windows-1251'));
	}
	
	/**
	 *	Set no display errors
	 *	Against 'Warning:  A non-numeric value encountered in /home/bitrix/www/bitrix/modules/perfmon/classes/general/keeper.php on line 321'
	 */
	public static function disableErrors(){
		ini_set('display_errors', 0);
		error_reporting(~E_ALL);
	}
	
	/**
	 *	Print JSON to page
	 */
	public static function printEncoded($arJson, $intOptions=0){
		if($intOptions === 0 && checkVersion(PHP_VERSION, '7.2.0')){
			$intOptions = JSON_INVALID_UTF8_IGNORE;
		}
		print static::encode($arJson, $intOptions);
	}
	
	/**
	 *	
	 */
	public static function prepare($arJson=[]){
		static::setHttpHeader();
		static::disableErrors();
		Helper::obRestart();
		return $arJson;
	}
	
	/**
	 *	
	 */
	public static function output($arJsonResult, $arOptions=0){
		Helper::obRestart();
		static::printEncoded($arJsonResult, $arOptions);
		static::disableErrors();
	}
	
	/**
	 *	
	 */
	public static function getTabSize(){
		return static::$intTabSize;
	}
	
	/**
	 *	
	 */
	public static function replaceSpaces(&$strJson){
		$strJson = preg_replace_callback("#^[\t]?([ ]*)(.*?)$#m", function($arMatch){
			$intTabCount = floor(strlen($arMatch[1]) / static::$intTabSize);
			return str_repeat("\t",  $intTabCount).$arMatch[2];
		}, $strJson);
	}
	
	/**
	 *	
	 */
	public static function addIndent(&$strJson, $intAmount=1){
		$strOffset = str_repeat("\t", intVal($intAmount));
		$strJson = preg_replace('#^(.*?)$#m', $strOffset.'$1', $strJson);
	}

	/**
	 * Wrapper for encode with auto set serialize_precision = -1
	 */
	public static function encode($data, $options = null){
		$serialize_precision = ini_get('serialize_precision');
		@ini_set('serialize_precision', '-1');
		$result = parent::encode($data, $options);
		@ini_set('serialize_precision', $serialize_precision);
		return $result;
	}

	/**
	 * Pretty print JSON
	 * $arJson is JSON array || JSON string
	 */
	public static function prettyPrint($arJson){
		$intMode = JSON_PRETTY_PRINT;
		if(defined('JSON_UNESCAPED_UNICODE')){
			$intMode = $intMode | JSON_UNESCAPED_UNICODE;
		}
		if(defined('JSON_UNESCAPED_SLASHES')){
			$intMode = $intMode | JSON_UNESCAPED_SLASHES;
		}
		if(is_string($arJson) && Helper::strlen($arJson)){
			$arJson = static::tryDecode($arJson);
		}
		return static::encode($arJson, $intMode);
	}

	public static function tryDecode($arJson){
		try{
			$arResult = static::decode($arJson);
		}
		catch(\Throwable $obError){
			$arResult = [];
		}
		return $arResult;
	}

}
?>