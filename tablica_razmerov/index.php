<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

/** @var object $APPLICATION */

$APPLICATION->SetPageProperty("description", "Продажа питбайков в Москве с доставкой по всей России ✅ Есть кредит и рассрочка ⭐ Тест-драйв перед покупкой! Огромный ассортимент мототехники и запчастей на PiteBikeLand ☎ +7 (495) 363-52-99");
$APPLICATION->SetPageProperty("title", "Таблица размеров");
$APPLICATION->SetTitle("Таблица размеров");

?><?$APPLICATION->IncludeComponent(
	"bitrix:catalog", 
	"catalog.tr", 
	array(
		"CACHE_TIME" => "0",
		"CACHE_TYPE" => "N",
		"DATE_FORMAT" => "d.m.Y",
		"DATE_SHOW" => "N",
		"DESCRIPTION_BLOCK_SHOW" => "Y",
		"DETAIL_URL" => "",
		"ELEMENTS_COUNT" => "",
		"HEADER_BLOCK_SHOW" => "Y",
		"IBLOCK_ID" => "100",
		"IBLOCK_TYPE" => "content",
		"LIST_PAGE_URL" => "",
		"NAVIGATION_USE" => "N",
		"ORDER_BY" => "ASC",
		"SECTION_URL" => "",
		"SORT_BY" => "SORT",
		"COMPONENT_TEMPLATE" => "catalog.tr",
		"HEADER_BLOCK_POSITION" => "center",
		"HEADER_BLOCK_TEXT" => "Новости",
		"DESCRIPTION_BLOCK_POSITION" => "center",
		"DESCRIPTION_BLOCK_TEXT" => "",
		"SETTINGS_USE" => "Y",
		"DELAY_USE" => "N",
		"MENU_TEMPLATE" => "",
		"MENU_ROOT" => "",
		"MENU_CHILD" => "",
		"MENU_LEVEL" => "",
		"ORDER_FAST_USE" => "N",
		"TAGS_USE" => "N",
		"REGIONALITY_USE" => "N",
		"SEARCH_SECTIONS_USE" => "N",
		"FORM_ID" => "",
		"FORM_REQUEST_ID" => "",
		"HIDE_NOT_AVAILABLE" => "N",
		"HIDE_NOT_AVAILABLE_OFFERS" => "N",
		"PROPERTY_MARKS_RECOMMEND" => "",
		"PROPERTY_MARKS_NEW" => "",
		"PROPERTY_MARKS_HIT" => "",
		"PROPERTY_MARKS_SHARE" => "",
		"PROPERTY_ORDER_USE" => "",
		"PROPERTY_REQUEST_USE" => "",
		"PROPERTY_ARTICLE" => "",
		"PROPERTY_BRAND" => "BRAND_TR",
		"PROPERTY_PICTURES" => "",
		"PROPERTY_SERVICES" => "",
		"PROPERTY_DOCUMENTS" => "",
		"PROPERTY_ADDITIONAL" => "",
		"PROPERTY_ASSOCIATED" => "",
		"PROPERTY_RECOMMENDED" => "",
		"PROPERTY_VIDEO" => "",
		"OFFERS_PROPERTY_ARTICLE" => "",
		"OFFERS_PROPERTY_PICTURES" => "",
		"VIDEO_IBLOCK_TYPE" => "",
		"GALLERY_VIDEO_IBLOCK_TYPE" => "",
		"SERVICES_IBLOCK_TYPE" => "",
		"REVIEWS_IBLOCK_TYPE" => "",
		"ROOT_LAYOUT" => "1",
		"SECTIONS_LAYOUT" => "1",
		"VOTE_MODE" => "rating",
		"QUANTITY_MODE" => "number",
		"USER_CONSENT" => "N",
		"USER_CONSENT_ID" => "0",
		"USER_CONSENT_IS_CHECKED" => "Y",
		"USER_CONSENT_IS_LOADED" => "N",
		"CONSENT_URL" => "",
		"SEF_MODE" => "Y",
		"SEF_TABS_USE" => "N",
		"AJAX_MODE" => "N",
		"AJAX_OPTION_JUMP" => "N",
		"AJAX_OPTION_STYLE" => "Y",
		"AJAX_OPTION_HISTORY" => "N",
		"AJAX_OPTION_ADDITIONAL" => "",
		"CACHE_FILTER" => "N",
		"CACHE_GROUPS" => "Y",
		"USE_MAIN_ELEMENT_SECTION" => "N",
		"DETAIL_STRICT_SECTION_CHECK" => "N",
		"SET_LAST_MODIFIED" => "N",
		"SET_TITLE" => "Y",
		"ADD_SECTIONS_CHAIN" => "Y",
		"ADD_ELEMENT_CHAIN" => "Y",
		"GIFTS_SECTION_LIST_PAGE_ELEMENT_COUNT" => "",
		"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => "",
		"OFFERS_PROPERTY_PICTURE_DIRECTORY" => "",
		"COMPARE_ACTION" => "none",
		"COMPARE_LAZYLOAD_USE" => "N",
		"BLOCK_ON_EMPTY_SEARCH_RESULTS_USE" => "N",
		"USE_FILTER" => "Y",
		"ACTION_VARIABLE" => "action",
		"PRODUCT_ID_VARIABLE" => "id",
		"USE_COMPARE" => "N",
		"PRICE_CODE" => array(
		),
		"USE_PRICE_COUNT" => "N",
		"SHOW_PRICE_COUNT" => "1",
		"PRICE_VAT_INCLUDE" => "Y",
		"PRICE_VAT_SHOW_VALUE" => "N",
		"CONVERT_CURRENCY" => "N",
		"BASKET_URL" => "/personal/basket.php",
		"USE_PRODUCT_QUANTITY" => "N",
		"PRODUCT_QUANTITY_VARIABLE" => "quantity",
		"ADD_PROPERTIES_TO_BASKET" => "Y",
		"PRODUCT_PROPS_VARIABLE" => "prop",
		"PARTIAL_PRODUCT_PROPERTIES" => "N",
		"SHOW_TOP_ELEMENTS" => "N",
		"TOP_ELEMENT_COUNT" => "9",
		"TOP_LINE_ELEMENT_COUNT" => "3",
		"TOP_ELEMENT_SORT_FIELD" => "sort",
		"TOP_ELEMENT_SORT_ORDER" => "asc",
		"TOP_ELEMENT_SORT_FIELD2" => "id",
		"TOP_ELEMENT_SORT_ORDER2" => "desc",
		"SECTION_COUNT_ELEMENTS" => "Y",
		"SECTION_TOP_DEPTH" => "2",
		"SECTIONS_ROOT_SECTION_DESCRIPTION_SHOW" => "Y",
		"SECTIONS_ROOT_CANONICAL_URL_USE" => "N",
		"SECTIONS_ROOT_TEMPLATE" => "tile.8",
		"SECTIONS_ROOT_MENU_SHOW" => "N",
		"SECTIONS_CHILDREN_SECTION_DESCRIPTION_SHOW" => "N",
		"SECTIONS_CHILDREN_CANONICAL_URL_USE" => "N",
		"SECTIONS_CHILDREN_TEMPLATE" => "tile.tr",
		"SECTIONS_CHILDREN_MENU_SHOW" => "N",
		"SECTIONS_CHILDREN_EXTENDING_USE" => "N",
		"SECTIONS_CHILDREN_EXTENDING_PROPERTY" => "",
		"SECTIONS_CHILDREN_EXTENDING_TITLE" => "Смотрите также",
		"SECTIONS_CHILDREN_EXTENDING_TEMPLATE" => "",
		"SECTIONS_ARTICLES_EXTENDING_TITLE" => "Полезные статьи",
		"SECTIONS_ARTICLES_EXTENDING_QUANTITY" => "5",
		"SECTIONS_ARTICLES_EXTENDING_TEMPLATE" => "",
		"PAGE_ELEMENT_COUNT" => "30",
		"LINE_ELEMENT_COUNT" => "3",
		"ELEMENT_SORT_FIELD" => "sort",
		"ELEMENT_SORT_ORDER" => "asc",
		"ELEMENT_SORT_FIELD2" => "id",
		"ELEMENT_SORT_ORDER2" => "desc",
		"INCLUDE_SUBSECTIONS" => "Y",
		"LIST_META_KEYWORDS" => "-",
		"LIST_META_DESCRIPTION" => "-",
		"LIST_BROWSER_TITLE" => "-",
		"SECTION_BACKGROUND_IMAGE" => "-",
		"LIST_SORT_PRICE" => "1",
		"LIST_ROOT_SHOW" => "N",
		"LIST_VIEW" => "tile",
		"LIST_TEXT_TEMPLATE" => "list.2",
		"LIST_LIST_TEMPLATE" => "list.1",
		"LIST_TILE_TEMPLATE" => "tile.4",
		"QUICK_VIEW_USE" => "N",
		"QUICK_VIEW_DETAIL" => "N",
		"INTEREST_PRODUCTS_SHOW" => "N",
		"ADDITIONAL_ARTICLES_SHOW" => "N",
		"DETAIL_META_KEYWORDS" => "-",
		"DETAIL_META_DESCRIPTION" => "-",
		"DETAIL_BROWSER_TITLE" => "-",
		"DETAIL_SET_CANONICAL_URL" => "N",
		"SECTION_ID_VARIABLE" => "SECTION_CODE",
		"DETAIL_CHECK_SECTION_ID_VARIABLE" => "N",
		"DETAIL_BACKGROUND_IMAGE" => "-",
		"SHOW_DEACTIVATED" => "N",
		"SHOW_SKU_DESCRIPTION" => "N",
		"OFFERS_VARIABLE_SELECT" => "",
		"DETAIL_TEMPLATE" => "",
		"DETAIL_MENU_SHOW" => "N",
		"LINK_IBLOCK_TYPE" => "",
		"LINK_IBLOCK_ID" => "",
		"LINK_PROPERTY_SID" => "",
		"LINK_ELEMENTS_URL" => "link.php?PARENT_ELEMENT_ID=#ELEMENT_ID#",
		"USE_ALSO_BUY" => "N",
		"USE_GIFTS_DETAIL" => "N",
		"USE_GIFTS_SECTION" => "N",
		"USE_GIFTS_MAIN_PR_SECTION_LIST" => "N",
		"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => "4",
		"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_DETAIL_BLOCK_TITLE" => "Выберите один из подарков",
		"GIFTS_DETAIL_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SECTION_LIST_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_SECTION_LIST_BLOCK_TITLE" => "Подарки к товарам этого раздела",
		"GIFTS_SECTION_LIST_TEXT_LABEL_GIFT" => "Подарок",
		"GIFTS_SHOW_DISCOUNT_PERCENT" => "Y",
		"GIFTS_SHOW_OLD_PRICE" => "Y",
		"GIFTS_SHOW_NAME" => "Y",
		"GIFTS_SHOW_IMAGE" => "Y",
		"GIFTS_MESS_BTN_BUY" => "Выбрать",
		"GIFTS_MAIN_PRODUCT_DETAIL_HIDE_BLOCK_TITLE" => "N",
		"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => "Выберите один из товаров, чтобы получить подарок",
		"GIFTS_SECTION_LIST_POSITION" => "bottom",
		"GIFTS_SECTION_LIST_NAVIGATION_BUTTON_POSITION" => "top",
		"GIFTS_SECTION_LIST_COLUMNS" => "3",
		"GIFTS_SECTION_LIST_QUANTITY" => "20",
		"USE_STORE" => "N",
		"STORES" => array(
			0 => "",
			1 => "",
		),
		"STORE_BLOCK_DESCRIPTION_USE" => "N",
		"PAGER_TEMPLATE" => ".default",
		"DISPLAY_TOP_PAGER" => "N",
		"DISPLAY_BOTTOM_PAGER" => "N",
		"PAGER_TITLE" => "Товары",
		"PAGER_SHOW_ALWAYS" => "N",
		"PAGER_DESC_NUMBERING" => "N",
		"PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
		"PAGER_SHOW_ALL" => "N",
		"PAGER_BASE_LINK_ENABLE" => "N",
		"LAZY_LOAD" => "N",
		"LOAD_ON_SCROLL" => "N",
		"SET_STATUS_404" => "N",
		"SHOW_404" => "N",
		"MESSAGE_404" => "",
		"COMPATIBLE_MODE" => "N",
		"USE_ELEMENT_COUNTER" => "N",
		"DISABLE_INIT_JS_IN_COMPONENT" => "N",
		"DETAIL_SET_VIEWED_IN_COMPONENT" => "N",
		"SEF_FOLDER" => "/info/tablica_razmerov/",
		"SECTIONS_ROOT_LAZYLOAD_USE" => "Y",
		"SECTIONS_ROOT_COLUMNS" => "3",
		"SECTIONS_ROOT_LINK_BLANK" => "N",
		"SECTIONS_ROOT_PICTURE_SHOW" => "Y",
		"SECTIONS_ROOT_CHILDREN_SHOW" => "Y",
		"SECTIONS_ROOT_SVG_FILE_USE" => "N",
		"SECTIONS_ROOT_CHILDREN_COUNT" => "3",
		"SECTIONS_CHILDREN_SECTION_DESCRIPTION_POSITION" => "top",
		"SECTIONS_CHILDREN_LAZYLOAD_USE" => "Y",
		"SECTIONS_CHILDREN_COLUMNS" => "3",
		"SECTIONS_CHILDREN_CHILDREN_SHOW" => "Y",
		"SECTIONS_CHILDREN_SVG_FILE_USE" => "N",
		"SECTIONS_ROOT_SECTION_DESCRIPTION_POSITION" => "bottom",
		"SECTIONS_ROOT_CHILDREN_ELEMENTS" => "N",
		"SECTIONS_CHILDREN_CHILDREN_COUNT" => "3",
		"FILTER_NAME" => "",
		"FILTER_AJAX" => "N",
		"FILTER_TYPE" => "vertical",
		"FILTER_TEMPLATE" => "1",
		"FILTER_COLLAPSED" => "N",
		"FILTER_PRICES_EXPANDED" => array(
		),
		"FILTER_TYPE_F_VIEW" => "default",
		"FILTER_TYPE_G_VIEW" => "default",
		"FILTER_TYPE_A_PRECISION" => "2",
		"FILTER_TYPE_B_PRECISION" => "2",
		"FILTER_SEARCH_SHOW" => "Y",
		"FILTER_TYPE_G_SIZE" => "default",
		"FILTER_SEARCH_SHOW_MODE" => "quantity",
		"FILTER_SEARCH_SHOW_QUANTITY" => "8",
		"SECTIONS_CHILDREN_BORDERS" => "Y",
		"SECTIONS_CHILDREN_PICTURE_SHOW" => "Y",
		"SECTIONS_CHILDREN_DESCRIPTION_SHOW" => "Y",
		"SECTIONS_CHILDREN_LINK_BLANK" => "N",
		"SECTIONS_CHILDREN_PICTURE_SIZE" => "cover",
		"SECTIONS_CHILDREN_CHILDREN_VIEW" => "1",
		"SECTIONS_CHILDREN_CHILDREN_ELEMENTS" => "N",
		"LIST_TEXT_ACTION" => "buy",
		"LIST_TEXT_BORDERS" => "Y",
		"LIST_TEXT_IMAGE_SHOW" => "Y",
		"LIST_TEXT_PROPERTIES_SHOW" => "Y",
		"LIST_TEXT_COUNTER_SHOW" => "Y",
		"LIST_TEXT_OFFERS_USE" => "Y",
		"LIST_TEXT_LAZYLOAD_USE" => "N",
		"LIST_TEXT_VOTE_SHOW" => "N",
		"LIST_TEXT_QUANTITY_SHOW" => "N",
		"LIST_TEXT_SECTION_TIMER_SHOW" => "Y",
		"LIST_TEXT_MEASURE_SHOW" => "N",
		"LIST_TEXT_PURCHASE_BASKET_BUTTON_TEXT" => "В корзину",
		"LIST_TEXT_PURCHASE_ORDER_BUTTON_TEXT" => "Заказать",
		"LIST_TEXT_PURCHASE_REQUEST_BUTTON_TEXT" => "Уточнить цену",
		"LIST_LIST_LAZYLOAD_USE" => "N",
		"LIST_LIST_ACTION" => "buy",
		"LIST_LIST_BORDERS" => "Y",
		"LIST_LIST_PROPERTIES_SHOW" => "Y",
		"LIST_LIST_COUNTER_SHOW" => "Y",
		"LIST_LIST_COUNTER_MESSAGE_MAX_SHOW" => "Y",
		"LIST_LIST_OFFERS_USE" => "Y",
		"LIST_LIST_VOTE_SHOW" => "N",
		"LIST_LIST_RECALCULATION_PRICES_USE" => "N",
		"LIST_LIST_QUANTITY_SHOW" => "N",
		"LIST_LIST_MEASURE_SHOW" => "N",
		"LIST_LIST_SECTION_TIMER_SHOW" => "N",
		"LIST_LIST_PURCHASE_BASKET_BUTTON_TEXT" => "В корзину",
		"LIST_LIST_PURCHASE_ORDER_BUTTON_TEXT" => "Заказать",
		"LIST_LIST_PURCHASE_REQUEST_BUTTON_TEXT" => "Уточнить цену",
		"LIST_TILE_LAZYLOAD_USE" => "N",
		"LIST_TILE_COLUMNS" => "3",
		"LIST_TILE_COLUMNS_MOBILE" => "1",
		"LIST_TILE_MARKS_SHOW" => "N",
		"LIST_TILE_IMAGE_ASPECT_RATIO" => "1:1",
		"LIST_TILE_MEASURE_SHOW" => "N",
		"LIST_TILE_NAME_ALIGN" => "left",
		"LIST_TILE_VOTE_SHOW" => "N",
		"LIST_TILE_RECALCULATION_PRICES_USE" => "N",
		"LIST_TILE_PRICE_DISCOUNT_PERCENT" => "N",
		"LIST_TILE_QUANTITY_SHOW" => "N",
		"LIST_TILE_WEIGHT_SHOW" => "N",
		"LIST_TILE_DESCRIPTION_SHOW" => "N",
		"LIST_TILE_PRICE_ALIGN" => "left",
		"LIST_TILE_ACTION" => "buy",
		"LIST_TILE_COUNTER_SHOW" => "Y",
		"LIST_TILE_COUNTER_MESSAGE_MAX_SHOW" => "Y",
		"LIST_TILE_SECTION_TIMER_SHOW" => "N",
		"LIST_TILE_PURCHASE_BASKET_BUTTON_TEXT" => "В корзину",
		"LIST_TILE_PURCHASE_ORDER_BUTTON_TEXT" => "Заказать",
		"LIST_TILE_PURCHASE_REQUEST_BUTTON_TEXT" => "Уточнить цену",
		"CATALOG_TIMER_TIME_ZERO_HIDE" => "N",
		"CATALOG_TIMER_MODE" => "discount",
		"CATALOG_TIMER_ELEMENT_ID_INTRODUCE" => "N",
		"CATALOG_TIMER_TIMER_SECONDS_SHOW" => "N",
		"CATALOG_TIMER_TIMER_QUANTITY_SHOW" => "Y",
		"CATALOG_TIMER_TIMER_QUANTITY_ENTER_VALUE" => "N",
		"CATALOG_TIMER_TIMER_PRODUCT_UNITS_USE" => "Y",
		"CATALOG_TIMER_TIMER_QUANTITY_HEADER_SHOW" => "Y",
		"CATALOG_TIMER_TIMER_HEADER_SHOW" => "Y",
		"CATALOG_TIMER_TIMER_HEADER" => "До конца акции",
		"CATALOG_TIMER_SETTINGS_USE" => "N",
		"CATALOG_TIMER_LAZYLOAD_USE" => "N",
		"CATALOG_TIMER_TIMER_QUANTITY_OVER" => "Y",
		"CATALOG_TIMER_TIMER_TITLE_SHOW" => "N",
		"LIST_TEXT_RECALCULATION_PRICES_USE" => "N",
		"LIST_TEXT_COUNTER_MESSAGE_MAX_SHOW" => "Y",
		"LIST_TEXT_TIMER_POSITION" => "left",
		"LIST_TILE_PROPERTY_STORES_SHOW" => "",
		"LIST_TILE_BORDERS" => "N",
		"LIST_TILE_BORDERS_STYLE" => "squared",
		"LIST_TILE_MEASURES_USE" => "N",
		"LIST_TILE_ARTICLE_SHOW" => "N",
		"SEF_URL_TEMPLATES" => array(
			"sections" => "",
			"section" => "#SECTION_CODE#/",
			"element" => "#SECTION_CODE#/#ELEMENT_CODE#/",
			"compare" => "compare.php?action=#ACTION_CODE#",
			"smart_filter" => "#SECTION_CODE#/filter/#SMART_FILTER_PATH#/apply/",
		),
		"VARIABLE_ALIASES" => array(
			"compare" => array(
				"ACTION_CODE" => "action",
			),
		)
	),
	false
);?><?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>