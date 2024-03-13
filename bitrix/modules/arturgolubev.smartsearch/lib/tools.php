<?
namespace Arturgolubev\Smartsearch;

use \Bitrix\Main\Loader;

use \Arturgolubev\Smartsearch\Unitools as UTools;
use \Arturgolubev\Smartsearch\Encoding as Enc;

class Tools {
	const HISTORY_COOKIE_NAME = 'AG_SMSE_H';
	
	// for dresscode templates. Add sku ids for correct view
	static function dwAddSkuId($ids = array()){
		if(!empty($ids) && is_array($ids) && Loader::includeModule("catalog")){			
			$skuList = \CCatalogSKU::getOffersList($ids, 0, array("ACTIVE"=>"Y"), array("ID"), array());
			foreach($skuList as $k=>$v){
				if(is_array($v) && !empty($v))
					$ids = array_merge($ids, array_keys($v));
			}
		}
		
		return array_unique($ids);
	}
	
	// Get ar product ids, by mixed sku+product ids
	static function getProductIdByMixed($ids = array()){
		$result = array();
		
		if(!empty($ids) && is_array($ids) && Loader::includeModule("catalog")){
			foreach($ids as $id){
				$arRs = \CCatalogSku::GetProductInfo($id);				
				if(is_array($arRs) && !empty($arRs)){
					$result[] = $arRs["ID"];
				}else{
					$result[] = $id;
				}
			}
		}
		
		return array_unique($result);
	}
	
	// history
	static function setSearchHistory($query, $maxCount = 0){
		global $APPLICATION;
		if($maxCount < 1) $maxCount = 10;
		
		if($APPLICATION->get_cookie('AG_SMSEARCH')){
			$APPLICATION->set_cookie('AG_SMSEARCH', '', time()+60*60*24*30*12*2, "/");
		}
		
		$history_save = $APPLICATION->get_cookie(self::HISTORY_COOKIE_NAME);
		if(!defined('BX_UTF')){
			$history_save = \Bitrix\Main\Text\Encoding::convertEncoding($history_save, 'utf-8', 'windows-1251');
		}
	
		if(strlen($history_save) < 1){
			$history_save = array();
		}else{
			$history_save = explode('|', $history_save);
			foreach($history_save as $k=>$v){
				if(Enc::toLower($v) == Enc::toLower($query)){
					unset($history_save[$k]);
				}
			}
			$history_save = array_values($history_save);
		}
		
		$history_save[] = $query;
		
		if(count($history_save) > $maxCount){
			$history_save = array_slice($history_save, count($history_save) - $maxCount, $maxCount);
		}
		
		if(!defined('BX_UTF')){
			$history_save = \Bitrix\Main\Text\Encoding::convertEncoding($history_save, 'windows-1251', 'utf-8');
		}
		
		$APPLICATION->set_cookie(self::HISTORY_COOKIE_NAME, implode('|', $history_save), time()+60*60*24*30*12*2, "/");
	}
	
	static function getSearchHistory($maxCount){
		global $APPLICATION;
		if(IntVal($maxCount) < 1) $maxCount = 10;
		
		$return = array();
		
		$history_save = $APPLICATION->get_cookie(self::HISTORY_COOKIE_NAME);
		if(!defined('BX_UTF')){
			$history_save = \Bitrix\Main\Text\Encoding::convertEncoding($history_save, 'utf-8', 'windows-1251');
		}
		
		if(strlen($history_save)>0){
			$history_save = explode('|', $history_save);
		}
		
		if(is_array($history_save) && count($history_save)){
			$ind = 0;
			foreach(array_reverse($history_save) as $k=>$v){
				if($v == trim($_GET["q"]) || $ind >= $maxCount) continue;
				$ind++;
				$return[] = $v;
			}
		}
		
		return $return;
	}
	
	
	// other
	
	static function getMinWordLenght(){
		$min_length = IntVal(UTools::getSetting("min_length"));
		if($min_length <= 0) $min_length = 3;
		
		return $min_length;
	}
	
	static function getReplaceParams(){
		return array("replace_space" => "", "replace_other" => "");
	}
	
	static function dbQuery($q){
		global $DB;
		return $DB->Query($q);
	}
	
	static function array_md5($array) {
		if(is_array($array) && !empty($array)){
			ksort($array);
			
			$s = '';
			foreach($array as $k=>$v){
				$s .= $k.'_'.$v.'-';
			}
			
			return md5($s);
		}
		
		return 0;
	}
	
	
	
	public static function ex_translit($str, $lang, $params = array())
	{
		static $search = array();

		if(!isset($search[$lang]))
		{
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/lang/".$lang."/ex_translit.php")){
				$mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/ex_translit.php", $lang, true);
			}else{
				$mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/js_core_translit.php", $lang, true);
			}
			
			$trans_from = explode(",", $mess["TRANS_FROM"]);
			$trans_to = explode(",", $mess["TRANS_TO"]);
			foreach($trans_from as $i => $from)
				$search[$lang][$from] = $trans_to[$i];
			
			// echo '<pre>'; print_r($search); echo '</pre>';
		}

		$defaultParams = array(
			"max_len" => 100,
			"change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
			"replace_space" => '_',
			"replace_other" => '_',
			"delete_repeat_replace" => true,
			"safe_chars" => '',
		);
		foreach($defaultParams as $key => $value)
			if(!array_key_exists($key, $params))
				$params[$key] = $value;

		$len = Enc::exStrlen($str);
		$str_new = '';
		$last_chr_new = '';

		for($i = 0; $i < $len; $i++)
		{
			$chr = Enc::exSubstr($str, $i, 1);

			if(preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || Enc::exStrpos($params["safe_chars"], $chr)!==false)
			{
				$chr_new = $chr;
			}
			elseif(preg_match("/\\s/".BX_UTF_PCRE_MODIFIER, $chr))
			{
				if (
					!$params["delete_repeat_replace"]
					||
					($i > 0 && $last_chr_new != $params["replace_space"])
				)
					$chr_new = $params["replace_space"];
				else
					$chr_new = '';
			}
			else
			{
				if(array_key_exists($chr, $search[$lang]))
				{
					$chr_new = $search[$lang][$chr];
				}
				else
				{
					if (
						!$params["delete_repeat_replace"]
						||
						($i > 0 && $i != $len-1 && $last_chr_new != $params["replace_other"])
					)
						$chr_new = $params["replace_other"];
					else
						$chr_new = '';
				}
			}

			if(Enc::exStrlen($chr_new))
			{
				if($params["change_case"] == "L" || $params["change_case"] == "l")
					$chr_new = ToLower($chr_new);
				elseif($params["change_case"] == "U" || $params["change_case"] == "u")
					$chr_new = ToUpper($chr_new);

				$str_new .= $chr_new;
				$last_chr_new = $chr_new;
			}

			if (Enc::exStrlen($str_new) >= $params["max_len"])
				break;
		}

		return $str_new;
	}
	
	public static function num_translit($str, $lang, $params = array())
	{
		static $search = array();

		if(!isset($search[$lang]))
		{
			if(file_exists($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/lang/".$lang."/num_translit.php")){
				$mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/arturgolubev.smartsearch/num_translit.php", $lang, true);
			}else{
				$mess = IncludeModuleLangFile($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/js_core_translit.php", $lang, true);
			}
			
			$trans_from = explode(",", $mess["TRANS_FROM"]);
			$trans_to = explode(",", $mess["TRANS_TO"]);
			foreach($trans_from as $i => $from)
				$search[$lang][$from] = $trans_to[$i];
				
			// echo '<pre>'; print_r($search); echo '</pre>';
		}

		$defaultParams = array(
			"max_len" => 100,
			"change_case" => 'L', // 'L' - toLower, 'U' - toUpper, false - do not change
			"replace_space" => '_',
			"replace_other" => '_',
			"delete_repeat_replace" => true,
			"safe_chars" => '',
		);
		foreach($defaultParams as $key => $value)
			if(!array_key_exists($key, $params))
				$params[$key] = $value;

		$len = Enc::exStrlen($str);
		$str_new = '';
		$last_chr_new = '';

		for($i = 0; $i < $len; $i++)
		{
			$chr = Enc::exSubstr($str, $i, 1);

			if(preg_match("/[a-zA-Z0-9]/".BX_UTF_PCRE_MODIFIER, $chr) || Enc::exStrpos($params["safe_chars"], $chr)!==false)
			{
				$chr_new = $chr;
			}
			elseif(preg_match("/\\s/".BX_UTF_PCRE_MODIFIER, $chr))
			{
				if (
					!$params["delete_repeat_replace"]
					||
					($i > 0 && $last_chr_new != $params["replace_space"])
				)
					$chr_new = $params["replace_space"];
				else
					$chr_new = '';
			}
			else
			{
				if(array_key_exists($chr, $search[$lang]))
				{
					$chr_new = $search[$lang][$chr];
				}
				else
				{
					if (
						!$params["delete_repeat_replace"]
						||
						($i > 0 && $i != $len-1 && $last_chr_new != $params["replace_other"])
					)
						$chr_new = $params["replace_other"];
					else
						$chr_new = '';
				}
			}

			if(Enc::exStrlen($chr_new))
			{
				if($params["change_case"] == "L" || $params["change_case"] == "l")
					$chr_new = ToLower($chr_new);
				elseif($params["change_case"] == "U" || $params["change_case"] == "u")
					$chr_new = ToUpper($chr_new);

				$str_new .= $chr_new;
				$last_chr_new = $chr_new;
			}

			if (Enc::exStrlen($str_new) >= $params["max_len"])
				break;
		}

		return $str_new;
	}
}