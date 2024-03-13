<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$arComponentDescription = array(
	"NAME" => GetMessage("ARTUR_GOLUBEV_SMARTSEARCH_SEARCH_PAGE_COMPONENT_NAME"),
	"DESCRIPTION" => GetMessage("ARTUR_GOLUBEV_SMARTSEARCH_SEARCH_PAGE_DESCRIPTION"),
	"ICON" => "/images/icon.gif",
	"SORT" => 20,
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "AG_DOP_SERVICES",
		"NAME" => GetMessage("ARTUR_GOLUBEV_SMARTSEARCH_SEARCH_PAGE_MAIN_FOLDER"),
		"SORT" => 1930,
		"CHILD" => array(
			"ID" => "AG_DOP_SERVICES_SMART_SEARCH",
			"NAME" => GetMessage("ARTUR_GOLUBEV_SMARTSEARCH_SEARCH_PAGE_FOLDER"),
			"SORT" => 150
		)
	),
	"COMPLEX" => "N",
);
?>