<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use \Bitrix\Main\Loader;
use \Bitrix\Main\Localization\Loc;

if(!Loader::includeModule("search"))
	return;

$arComponentParameters = array(
	"GROUPS" => array(
		"VISUAL_PARAMS" => array(
			"NAME" => Loc::getMessage("CP_BST_GROUP_VISUAL_PARAMS"),
		),	
	),
	"PARAMETERS" => array(
		// "CACHE_TIME" => Array("DEFAULT"=>3600),
		
		"PAGE" => array(
			"PARENT" => "URL_TEMPLATES",
			"NAME" => Loc::getMessage("CP_BST_FORM_PAGE"),
			"TYPE" => "STRING",
			"DEFAULT" => "#SITE_DIR#search/index.php",
		),
		"NUM_CATEGORIES" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BST_NUM_CATEGORIES"),
			"TYPE" => "STRING",
			"DEFAULT" => "1",
			"REFRESH" => "Y",
		),
		"TOP_COUNT" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BST_TOP_COUNT"),
			"TYPE" => "STRING",
			"DEFAULT" => "5",
			// "REFRESH" => "Y",
		),
		"ORDER" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BST_ORDER"),
			"TYPE" => "LIST",
			"DEFAULT" => "rank",
			"VALUES" => array(
				"rank" => Loc::getMessage("CP_BST_ORDER_BY_RANK"),
				"date" => Loc::getMessage("CP_BST_ORDER_BY_DATE"),
			),
		),
		"FILTER_NAME" => array(
			// "PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BSP_FILTER_NAME"),
			"TYPE" => "STRING",
		),
		"USE_LANGUAGE_GUESS" => Array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BST_USE_LANGUAGE_GUESS"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "Y",
		),
		"CHECK_DATES" => array(
			// "PARENT" => "BASE",
			"NAME" => Loc::getMessage("CP_BST_CHECK_DATES"),
			"TYPE" => "CHECKBOX",
			"DEFAULT" => "N",
		),
		"ANIMATE_HINTS" => array(
			"NAME" => Loc::getMessage("TP_BST_ANIMATE_HINTS"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
			"MULTIPLE" => "Y",
			"PARENT" => "VISUAL",
		),
		"ANIMATE_HINTS_SPEED" => array(
			"NAME" => Loc::getMessage("TP_BST_ANIMATE_HINTS_SPEED"),
			"TYPE" => "LIST",
			"DEFAULT" => "1",
			"VALUES" => array(
				"1" => 1,
				"2" => 2,
				"3" => 3,
				"4" => 4,
				"5" => 5,
			),
			"PARENT" => "VISUAL",
		),
	),
);

$NUM_CATEGORIES = intval($arCurrentValues["NUM_CATEGORIES"]);
if($NUM_CATEGORIES <= 0)
	$NUM_CATEGORIES = 1;

for($i = 0; $i < $NUM_CATEGORIES; $i++)
{
	$arComponentParameters["GROUPS"]["CATEGORY_".$i] = array(
		"NAME" => Loc::getMessage("CP_BST_NUM_CATEGORY", array("#NUM#" => $i+1))
	);
	$arComponentParameters["PARAMETERS"]["CATEGORY_".$i."_TITLE"] = array(
		"PARENT" => "CATEGORY_".$i,
		"NAME" => Loc::getMessage("CP_BST_CATEGORY_TITLE"),
		"TYPE" => "STRING",
	);

	CSearchParameters::AddFilterParams($arComponentParameters, $arCurrentValues, "CATEGORY_".$i, "CATEGORY_".$i);
}
?>
