<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
	return;

$arIBlocks=array();
$db_iblock = CIBlock::GetList(array("SORT"=>"ASC"), array());
while($arRes = $db_iblock->Fetch())
	$arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];

$arComponentParameters = array(
	"GROUPS" => array(),
	"PARAMETERS" => array(
		"IBLOCK_ID" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_IBLOCK_ID"),
			"TYPE" => "LIST",
			"VALUES" => $arIBlocks,
			"DEFAULT" => '={$_REQUEST["ID"]}',
			"ADDITIONAL_VALUES" => "Y",
			"REFRESH" => "Y",
		),
		"HEIGHT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_HEIGHT"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"HEIGHT_MOBILE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_HEIGHT_MOBILE"),
			"TYPE" => "STRING",
			"DEFAULT" => "",
		),
		"HIDE_TEXT" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_HIDE_TEXT"),
			"TYPE" => "LIST",
			"VALUES" => array('Y' => GetMessage("INNOVA_SLIDER_HIDE_TEXT_Y"), 'N' => GetMessage("INNOVA_SLIDER_HIDE_TEXT_N")),
			"DEFAULT" => "N",
		),
		"TEXT_ALIGN" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_TEXT_ALIGN"),
			"TYPE" => "LIST",
			"VALUES" => array('flex-end' => GetMessage("INNOVA_SLIDER_TEXT_RIGHT"), 'flex-start' => GetMessage("INNOVA_SLIDER_TEXT_LEFT"), 'center' => GetMessage("INNOVA_SLIDER_TEXT_CENTER")),
			"DEFAULT" => "flex-start",
		),
		"STRETCH_TYPE" => array(
			"PARENT" => "BASE",
			"NAME" => GetMessage("INNOVA_SLIDER_STRETCH_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array('1' => GetMessage("INNOVA_SLIDER_STRETCH_TYPE2"), '2' => GetMessage("INNOVA_SLIDER_STRETCH_TYPE1")),
			"DEFAULT" => "1",
		),
		"SLIDER_COLOR" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("INNOVA_SLIDER_SLIDER_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "rgba(0, 0, 0, 0.6)",
		),
		"BTN_COLOR" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("INNOVA_SLIDER_BTN_COLOR"),
			"TYPE" => "COLORPICKER",
			"DEFAULT" => "#FFFFFF",
		),
		"SPEED" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("INNOVA_SLIDER_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "500",
		),
		"AUTOPLAY" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("INNOVA_SLIDER_AUTOPLAY"),
			"TYPE" => "LIST",
			"VALUES" => array('true' => GetMessage("INNOVA_SLIDER_ON"), 'false' => GetMessage("INNOVA_SLIDER_OFF")),
			"DEFAULT" => "true",
		),
		"AUTOPLAY_SPEED" => array(
			"PARENT" => "ADDITIONAL_SETTINGS",
			"NAME" => GetMessage("INNOVA_SLIDER_AUTOPLAY_SPEED"),
			"TYPE" => "STRING",
			"DEFAULT" => "3000",
		),
		"CACHE_TYPE"  =>  array(
			"PARENT" => "CACHE_SETTINGS",
			"NAME" => GetMessage("COMP_PROP_CACHE_TYPE"),
			"TYPE" => "LIST",
			"VALUES" => array("A" => GetMessage("COMP_PROP_CACHE_TYPE_AUTO")." ".GetMessage("COMP_PARAM_CACHE_MAN"), "Y" => GetMessage("COMP_PROP_CACHE_TYPE_YES"), "N" => GetMessage("COMP_PROP_CACHE_TYPE_NO")),
			"DEFAULT" => "N",
			"ADDITIONAL_VALUES" => "N",
			"REFRESH" => "Y" 
		),
	),
);
?>