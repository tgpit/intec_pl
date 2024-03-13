<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetPageProperty("title", "Производители и партнеры магазина PitbikeLand");
$APPLICATION->SetTitle("Партнеры");
?><?$APPLICATION->IncludeComponent(
	"intec.universe:main.news",
	"",
	Array(
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "A",
		"DATE_FORMAT" => "d.m.Y",
		"DATE_SHOW" => "N",
		"DESCRIPTION_BLOCK_POSITION" => "center",
		"DESCRIPTION_BLOCK_SHOW" => "Y",
		"DESCRIPTION_BLOCK_TEXT" => "",
		"DETAIL_URL" => "",
		"ELEMENTS_COUNT" => "",
		"HEADER_BLOCK_POSITION" => "center",
		"HEADER_BLOCK_SHOW" => "Y",
		"HEADER_BLOCK_TEXT" => "Новости",
		"IBLOCK_ID" => "95",
		"IBLOCK_TYPE" => "content",
		"LIST_PAGE_URL" => "",
		"NAVIGATION_USE" => "N",
		"ORDER_BY" => "ASC",
		"SECTION_URL" => "",
		"SORT_BY" => "SORT"
	)
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>