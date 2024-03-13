<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

use \Arturgolubev\Smartsearch\SearchComponent;
use \Arturgolubev\Smartsearch\Encoding as Enc;

$ag_module = 'arturgolubev.smartsearch';

if(!Loader::includeModule($ag_module))
{
	global $USER; if($USER->IsAdmin()){
		echo '<div style="color:red;">'.Loc::getMessage("ARTURGOLUBEV_SMARTSEARCH_MODULE_UNAVAILABLE").'</div>';
	}
	return;
}

$isSearchInstalled = Loader::includeModule("search");

if(!isset($arParams["PAGE"]) || strlen($arParams["PAGE"])<=0)
	$arParams["PAGE"] = "#SITE_DIR#search/index.php";

if(strlen($arParams["FILTER_NAME"])<=0 || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
	$arFILTERCustom = array();
else
{
	$arFILTERCustom = $GLOBALS[$arParams["FILTER_NAME"]];
	if(!is_array($arFILTERCustom))
		$arFILTERCustom = array();
}
$arResult["CATEGORIES"] = array();

$q = ltrim($_POST["q"]);
$q = \Bitrix\Main\Text\Encoding::convertEncodingToCurrent($q);
$arResult["query"] = $q;

$smartcomponent = new SearchComponent($q, 'title');
$smartcomponent->setItemIdFilterMode('');

// echo '<pre>'; print_r($smartcomponent); echo '</pre>';
if($smartcomponent->getOption('engine') == 'sphinx' && !is_array($arParams['ORDER'])){
	if($arParams['ORDER'] == 'date'){
		$arParams['ORDER'] = 'date_change';
	}

	$arParams['ORDER'] = ['CUSTOM_RANK' => "DESC", $arParams['ORDER'] => "DESC"];
}

$arResult["VISUAL_PARAMS"] = array();
$arResult["VISUAL_PARAMS"]["THEME_CLASS"] = 'theme-'.$smartcomponent->getOption('theme_class');
$arResult["VISUAL_PARAMS"]["THEME_COLOR"] = $smartcomponent->getOption('theme_color');
$arResult["VISUAL_PARAMS"]["PLACEHOLDER"] = $smartcomponent->getOption('theme_placeholder');

$arResult["DEBUG"] = array();
$arResult["DEBUG"]["QUERY"] = $smartcomponent->baseQuery;
$arResult["DEBUG"]["TYPE"] = Loc::getMessage("SEARCH_DEBUG_TYPE_BASE"); 

global $USER; if($USER->IsAdmin()){
	$arResult["DEBUG"]["SHOW"] = $smartcomponent->getOption('debug');
}

if(
	!empty($smartcomponent->baseQuery)
	&& $_REQUEST["ajax_call"] === "y"
	&& (
		!isset($_REQUEST["INPUT_ID"])
		|| $_REQUEST["INPUT_ID"] == $arParams["INPUT_ID"]
	)
)
{
	CUtil::decodeURIComponent($smartcomponent->baseQuery);
	if (!$isSearchInstalled)
	{
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/tools/language.php");
		require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/search/tools/stemming.php");
	}

	$arParams["NUM_CATEGORIES"] = intval($arParams["NUM_CATEGORIES"]);
	if($arParams["NUM_CATEGORIES"] <= 0)
		$arParams["NUM_CATEGORIES"] = 1;

	$arParams["TOP_COUNT"] = intval($arParams["TOP_COUNT"]);
	if($arParams["TOP_COUNT"] <= 0)
		$arParams["TOP_COUNT"] = 5;

	$arResult["DEBUG"]["TOP_COUNT"] = $arParams["TOP_COUNT"]; 
	$arResult["DEBUG"]["RES_COUNT"] = 0; 

	$arOthersFilter = array("LOGIC"=>"OR");
	
	$alreadyFinded = array();
	for($i = 0; $i < $arParams["NUM_CATEGORIES"]; $i++)
	{
		$bCustom = true;
		if(is_array($arParams["CATEGORY_".$i]))
		{
			foreach($arParams["CATEGORY_".$i] as $categoryCode)
			{
				if ((strpos($categoryCode, 'custom_') !== 0))
				{
					$bCustom = false;
					break;
				}
			}
		}
		else
		{
			$bCustom = (strpos($arParams["CATEGORY_".$i], 'custom_') === 0);
		}

		if ($bCustom)
			continue;

		$category_title = trim($arParams["CATEGORY_".$i."_TITLE"]);
		if(empty($category_title))
		{
			if(is_array($arParams["CATEGORY_".$i]))
				$category_title = implode(", ", $arParams["CATEGORY_".$i]);
			else
				$category_title = trim($arParams["CATEGORY_".$i]);
		}
		if(empty($category_title))
			continue;

		$arResult["CATEGORIES"][$i] = array(
			"TITLE" => htmlspecialcharsbx($category_title),
			"ITEMS" => array()
		);

		if ($isSearchInstalled)
		{
			$time_start = microtime(true); 
			
			$exFILTER = array(
				0 => CSearchParameters::ConvertParamsToFilter($arParams, "CATEGORY_".$i),
			);
			$exFILTER[0]["LOGIC"] = "OR";

			if($arParams["CHECK_DATES"] === "Y")
				$exFILTER["CHECK_DATES"] = "Y";

			// echo '<pre>'; print_r($exFILTER); echo '</pre>';
						
			$correctionParams = [
				'filter' => []
			];
			
			foreach($exFILTER[0] as $flt){
				if(is_array($flt)){
					$tmp = [];
					if($flt['=MODULE_ID']){
						$tmp['MODULE_ID'] = $flt['=MODULE_ID'];
					}
					if($flt['PARAM1']){
						$tmp['PARAM1'] = $flt['PARAM1'];
					}
					if(is_array($flt['PARAM2']) && count($flt['PARAM2'])){
						$tmp['PARAM2'] = $flt['PARAM2'];
					}
					if(count($tmp)){
						$correctionParams['filter'][] = $tmp;
					}
				}
			}
			
			$arOthersFilter[] = $exFILTER;
			
			if(!empty($arFILTERCustom))
				$exFILTER = array_merge($exFILTER, $arFILTERCustom);
			
			$j = 0;
			$obTitle = new CSearchTitleExt;
			$obTitle->setMinWordLength($_REQUEST["l"]);
			
			$arResult["work_query"] = $smartcomponent->query;
			$arResult["DEBUG"]["QUERY_R"] = $smartcomponent->query;
			
			/* if(COption::GetOptionString("search", "use_stemming") == "Y"){
				$arResult["phrase"] = stemming_split($arResult["work_query"], LANGUAGE_ID);
				if(is_array($arResult["phrase"]) && count($arResult["phrase"]) > 0){
					$arResult["phrase_stemming"] = array();
					foreach($arResult["phrase"] as $k=>$v){
						$tmp = stemming($k, LANGUAGE_ID);
						// $arResult['DEBUG2'][] = $tmp;
						
						if(is_array($tmp)){
							$arResult["phrase_stemming_base"][] = $tmp;
							foreach($tmp as $kk=>$vv){
								$arResult["phrase_stemming"][] = $kk;
								// $arResult['DEBUG2'][] = $kk;
								// $arResult['DEBUG2'][] = $vv;
							}
						}
					}
				}
				
				// $arResult['DEBUG2'][] = $arResult["phrase_stemming"];
				
				if(is_array($arResult["phrase_stemming"]) && count($arResult["phrase_stemming"])){
					$arResult["work_query"] = implode(' ', $arResult["phrase_stemming"]);
				}
			} */
			
			$cnt = 0;
			$time_start2 = microtime(true); 
			if($obTitle->Search(
				$arResult["work_query"]
				,$arParams["TOP_COUNT"]
				,$exFILTER
				,false
				,$arParams["ORDER"]
			))
			{
				while($ar = $obTitle->Fetch())
				{
					$j++;
					if($j > $arParams["TOP_COUNT"])
					{
						break;
					}
					else
					{
						$arResult["CATEGORIES"][$i]["ITEMS"][] = array(
							"NAME" => $ar["NAME"],
							"URL" => htmlspecialcharsbx($ar["URL"]),
							"MODULE_ID" => $ar["MODULE_ID"],
							"PARAM1" => $ar["PARAM1"],
							"PARAM2" => $ar["PARAM2"],
							"ITEM_ID" => $ar["ITEM_ID"],
							"C_RANK" => $ar["CUSTOM_RANK"],
							"T_RANK" => $ar["TITLE_RANK"],
						);
						
						$alreadyFinded[] = $ar["ITEM_ID"];
						$cnt++;
					}
				}
			}
			$arResult["DEBUG"]["Q"][] = $arResult["work_query"] . ' (time: '.round((microtime(true) - $time_start2), 4).', cnt: '.$cnt.')';
	
			if(empty($alreadyFinded) && $arParams["USE_LANGUAGE_GUESS"] !== "N")
			{
				$guessQuery = '';
				if($smartcomponent->getOption('use_guessplus')){
					$guessQuery = CArturgolubevSmartsearch::guessLanguage($smartcomponent->baseQuery, $correctionParams);
				}
				
				if(!$guessQuery){
					$arLang = CSearchLanguage::GuessLanguage($smartcomponent->baseQuery);
					if(is_array($arLang) && $arLang["from"] != $arLang["to"])
						$guessQuery = CSearchLanguage::ConvertKeyboardLayout($smartcomponent->baseQuery, $arLang["from"], $arLang["to"]);
				}
				
				if($guessQuery)
				{
					$cnt = 0;
					$time_start2 = microtime(true); 
					if($obTitle->Search(
						$guessQuery
						,$arParams["TOP_COUNT"]
						,$exFILTER
						,false
						,$arParams["ORDER"]
					))
					{
						while($ar = $obTitle->Fetch())
						{
							$j++;
							if($j > $arParams["TOP_COUNT"])
							{
								break;
							}
							else
							{
								$arResult["CATEGORIES"][$i]["ITEMS"][] = array(
									"NAME" => $ar["NAME"],
									"URL" => htmlspecialcharsbx($ar["URL"]),
									"MODULE_ID" => $ar["MODULE_ID"],
									"PARAM1" => $ar["PARAM1"],
									"PARAM2" => $ar["PARAM2"],
									"ITEM_ID" => $ar["ITEM_ID"],
									"C_RANK" => $ar["CUSTOM_RANK"],
									"T_RANK" => $ar["TITLE_RANK"],
								);
								
								$alreadyFinded[] = $ar["ITEM_ID"];
								$cnt++;
							}
						}
					}
					
					if(!empty($alreadyFinded)){
						$arResult["DEBUG"]["TYPE"] = Loc::getMessage("SEARCH_DEBUG_TYPE_KEY");
					}
					
					$arResult["DEBUG"]["Q"][] = $guessQuery . ' (time: '.round((microtime(true) - $time_start2), 4).', cnt: '.$cnt.')';
				}
			}
			
			if ($smartcomponent->getOption('use_fixes') && empty($alreadyFinded) || ($arParams["ALWAYS_USE_SMART"] == 'Y' && $j < $arParams["TOP_COUNT"])){					
				$time_start2 = microtime(true); 
				$arLavelsWords = CArturgolubevSmartsearch::getSimilarWordsList($arResult["work_query"], 'title', $correctionParams);
				$arResult["DEBUG"]["TYPE"] = Loc::getMessage("SEARCH_DEBUG_TYPE_SMART") . ' ('.$smartcomponent->getOption('mode').')'; 
				
				if(empty($arLavelsWords) && $smartcomponent->getOption('use_guessplus') && $guessQuery){
					$arLavelsWords = CArturgolubevSmartsearch::getSimilarWordsList($guessQuery, 'title', $correctionParams);
					$arResult["DEBUG"]["TYPE"] = Loc::getMessage("SEARCH_DEBUG_TYPE_SMART") . ' ('.$smartcomponent->getOption('mode').' + guess)'; 
				}
				
				$arResult["DEBUG"]["RESULT_WORDS"] = $arLavelsWords;
				$arResult["DEBUG"]["TIMES"]["SIMILARITY"] = round((microtime(true) - $time_start2), 4);
				
				// $arResult["DEBUG"]["OTHER"]["WORDS"] = $arLavelsWords;
				
				if(!empty($arLavelsWords))
				{
					foreach($arLavelsWords as $level=>$searchArray)
					{
						foreach($searchArray as $sWord)
						{
							if(Enc::toLower($sWord) == Enc::toLower($arResult["work_query"]))
								continue;
							
							if(CArturgolubevSmartsearch::checkMatrixLineEmpty($sWord)){
								$arResult["DEBUG"]["Q"][] = $sWord . ' (skip)';
								continue;
							}
							
							if(!$smartcomponent->getOption('disable_item_id_filter')){
								$exFILTER["!=ITEM_ID"] = $alreadyFinded;
							}
							
							$cnt = 0;
							$time_start2 = microtime(true); 
							if ($obTitle->Search(
								  $sWord
								  , $arParams["TOP_COUNT"]
								  , $exFILTER
								  , false
								  , $arParams["ORDER"]
							   )) {
								while ($ar = $obTitle->Fetch()) {
									$j++;
									if ($j > $arParams["TOP_COUNT"]){
										break;
									}
									else
									{
										$arResult["CATEGORIES"][$i]["ITEMS"][] = array(
											"NAME" => $ar["NAME"],
											"URL" => htmlspecialcharsbx($ar["URL"]),
											"MODULE_ID" => $ar["MODULE_ID"],
											"PARAM1" => $ar["PARAM1"],
											"PARAM2" => $ar["PARAM2"],
											"ITEM_ID" => $ar["ITEM_ID"],
											"C_RANK" => $ar["CUSTOM_RANK"],
											"T_RANK" => $ar["TITLE_RANK"],
										);
										
										$alreadyFinded[] = $ar["ITEM_ID"];
										$cnt++;
									}
								}
							}
							
							CArturgolubevSmartsearch::saveMatrixLineEmpty($sWord, $cnt);
							
							$arResult["DEBUG"]["Q"][] = $sWord . ' (time: '.round((microtime(true) - $time_start2), 4).', cnt: '.$cnt.')';
							
							if ($j > $arParams["TOP_COUNT"]) {
								break(2);
							}
						}
						
						if(!empty($alreadyFinded)) break;
					}
				}
			}
			
			if(!$j)
			{
				unset($arResult["CATEGORIES"][$i]);
			}
				
			$arResult["DEBUG"]["TIMES"]["FULL"] = round((microtime(true) - $time_start), 4);
		}
	}
	
	/* get dop information */
	if(count($alreadyFinded)>0)
	{
		$arResult["DEBUG"]["RES_COUNT"] = count($alreadyFinded);
		
		$arResult["INFORMATION"] = CArturgolubevSmartsearch::getRealElementsName($alreadyFinded);
		if(!empty($arResult["INFORMATION"])){
			foreach($arResult["CATEGORIES"] as $category_id => $arCategory)
			{
				foreach($arCategory["ITEMS"] as $key => $arItem){
					if($arItem['MODULE_ID'] != 'iblock') continue;
					
					if(isset($arItem["ITEM_ID"]))
					{
						$newTitle = $arResult["INFORMATION"][$arItem["ITEM_ID"]]["NAME"];
						if($newTitle)
						{
							$arResult["CATEGORIES"][$category_id]["ITEMS"][$key]["NAME_S"] = $arResult["CATEGORIES"][$category_id]["ITEMS"][$key]["NAME"];
							$arResult["CATEGORIES"][$category_id]["ITEMS"][$key]["NAME"] = CArturgolubevSmartsearch::formatElementName($arResult["CATEGORIES"][$category_id]["ITEMS"][$key]["NAME"], $newTitle);
						}
					}
				}
			}
		}
	}
	/* end get dop information */

	if(!empty($arResult["CATEGORIES"]) && $isSearchInstalled)
	{
		$arResult["CATEGORIES"]["all"] = array(
			"TITLE" => "",
			"ITEMS" => array()
		);

		$url = CHTTP::urlAddParams(
			str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]),
			array("q" => $q),
			array("encode"=>true)
		);
		$arResult["CATEGORIES"]["all"]["ITEMS"][] = array(
			"NAME" => Loc::getMessage("CC_BST_ALL_RESULTS"),
			"URL" => $url,
		);
	}
	
	$arResult['CATEGORIES_ITEMS_EXISTS'] = false;
	foreach ($arResult["CATEGORIES"] as $category)
	{
		if (!empty($category['ITEMS']) && is_array($category['ITEMS']))
		{
			$arResult['CATEGORIES_ITEMS_EXISTS'] = true;
			break;
		}
	}
}

$arResult["FORM_ACTION"] = htmlspecialcharsbx(str_replace("#SITE_DIR#", SITE_DIR, $arParams["PAGE"]));

if (
	$_REQUEST["ajax_call"] === "y"
	&& (
		!isset($_REQUEST["INPUT_ID"])
		|| $_REQUEST["INPUT_ID"] == $arParams["INPUT_ID"]
	)
)
{
	$APPLICATION->RestartBuffer();

	if(!empty($smartcomponent->baseQuery))
		$this->IncludeComponentTemplate('ajax');
	CMain::FinalActions();
	die();
}
else
{
	$APPLICATION->AddHeadScript($this->GetPath().'/script.js');
	CUtil::InitJSCore(array('ajax'));
	$this->IncludeComponentTemplate();
}
?>
