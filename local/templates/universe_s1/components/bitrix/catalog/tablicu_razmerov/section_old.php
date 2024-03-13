<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die();
}

use Bitrix\Main\Loader;
use Ninja\Helper\Iblock\Section;
use Ninja\Project\Regionality\Cities;
use Ninja\Project\Regionality\Seo;

/** @var array $arResult */
/** @var array $arParams */
/** @var object $APPLICATION */

$this->setFrameMode(true);

Loader::includeModule("iblock");

global $arTheme, $NextSectionID, $arRegion;
$arPageParams = $arSection = $section = array();

$checkCity = Cities::checkCity();

// get current section ID

if ($arResult["VARIABLES"]["SECTION_ID"] > 0) {
    $section = CNextCache::CIBlockSection_GetList(
        array(
            'CACHE' => array(
                "MULTI" =>"N",
                "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"])
            )
        ),
        array
        (
            'GLOBAL_ACTIVE' => 'Y',
            "ID" => $arResult["VARIABLES"]["SECTION_ID"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"]
        ),
        false,
        array("ID", "IBLOCK_ID", "NAME", "DESCRIPTION", "UF_SECTION_DESCR", "UF_OFFERS_TYPE", $arParams["SECTION_DISPLAY_PROPERTY"], "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN")
    );
}
elseif (strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0) {
    $section = CNextCache::CIBlockSection_GetList(
        array(
            'CACHE' => array(
                "MULTI" =>"N",
                "TAG" => CNextCache::GetIBlockCacheTag($arParams["IBLOCK_ID"])
            )
        ),
        array(
            'GLOBAL_ACTIVE' => 'Y',
            "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
            "IBLOCK_ID" => $arParams["IBLOCK_ID"]
        ),
        false,
        array("ID", "IBLOCK_ID", "NAME", "DESCRIPTION", "UF_SECTION_DESCR", "UF_OFFERS_TYPE", $arParams["SECTION_DISPLAY_PROPERTY"], "IBLOCK_SECTION_ID", "DEPTH_LEVEL", "LEFT_MARGIN", "RIGHT_MARGIN")
    );
}



$APPLICATION->IncludeComponent("bitrix:catalog.section", "catalog_block_razmeru", Array(
    "ACTION_VARIABLE" => "action",	// Название переменной, в которой передается действие
    "ADD_PICT_PROP" => "MORE_PHOTO",
    "ADD_PROPERTIES_TO_BASKET" => "Y",	// Добавлять в корзину свойства товаров и предложений
    "ADD_SECTIONS_CHAIN" => "N",	// Включать раздел в цепочку навигации
    "ADD_TO_BASKET_ACTION" => "ADD",
    "AJAX_MODE" => "N",	// Включить режим AJAX
    "AJAX_OPTION_ADDITIONAL" => "",	// Дополнительный идентификатор
    "AJAX_OPTION_HISTORY" => "N",	// Включить эмуляцию навигации браузера
    "AJAX_OPTION_JUMP" => "N",	// Включить прокрутку к началу компонента
    "AJAX_OPTION_STYLE" => "Y",	// Включить подгрузку стилей
    "BACKGROUND_IMAGE" => "UF_BACKGROUND_IMAGE",	// Установить фоновую картинку для шаблона из свойства
    "BASKET_URL" => "/personal/basket.php",	// URL, ведущий на страницу с корзиной покупателя
    "BRAND_PROPERTY" => "BRAND_REF",
    "BROWSER_TITLE" => "-",	// Установить заголовок окна браузера из свойства
    "CACHE_FILTER" => "N",	// Кешировать при установленном фильтре
    "CACHE_GROUPS" => "Y",	// Учитывать права доступа
    "CACHE_TIME" => "36000000",	// Время кеширования (сек.)
    "CACHE_TYPE" => "A",	// Тип кеширования
    "COMPATIBLE_MODE" => "Y",	// Включить режим совместимости
    "CONVERT_CURRENCY" => "Y",	// Показывать цены в одной валюте
    "CURRENCY_ID" => "RUB",	// Валюта, в которую будут сконвертированы цены
    "CUSTOM_FILTER" => "",
    "DATA_LAYER_NAME" => "dataLayer",
    "DETAIL_URL" => "",	// URL, ведущий на страницу с содержимым элемента раздела
    "DISABLE_INIT_JS_IN_COMPONENT" => "N",	// Не подключать js-библиотеки в компоненте
    "DISCOUNT_PERCENT_POSITION" => "bottom-right",
    "DISPLAY_BOTTOM_PAGER" => "Y",	// Выводить под списком
    "DISPLAY_TOP_PAGER" => "N",	// Выводить над списком
    "ELEMENT_SORT_FIELD" => "sort",	// По какому полю сортируем элементы
    "ELEMENT_SORT_FIELD2" => "id",	// Поле для второй сортировки элементов
    "ELEMENT_SORT_ORDER" => "asc",	// Порядок сортировки элементов
    "ELEMENT_SORT_ORDER2" => "desc",	// Порядок второй сортировки элементов
    "ENLARGE_PRODUCT" => "PROP",
    "ENLARGE_PROP" => "NEWPRODUCT",
    "FILTER_NAME" => "arrFilter",	// Имя массива со значениями фильтра для фильтрации элементов
    "HIDE_NOT_AVAILABLE" => "N",	// Недоступные товары
    "HIDE_NOT_AVAILABLE_OFFERS" => "N",	// Недоступные торговые предложения
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],	// Инфоблок
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],	// Тип инфоблока
    "INCLUDE_SUBSECTIONS" => "Y",	// Показывать элементы подразделов раздела
    "LABEL_PROP" => "",
    "LABEL_PROP_MOBILE" => "",
    "LABEL_PROP_POSITION" => "top-left",
    "LAZY_LOAD" => "Y",
    "LINE_ELEMENT_COUNT" => "4",	// Количество элементов выводимых в одной строке таблицы
    "LOAD_ON_SCROLL" => "N",
    "MESSAGE_404" => "",	// Сообщение для показа (по умолчанию из компонента)
    "MESS_BTN_ADD_TO_BASKET" => "В корзину",
    "MESS_BTN_BUY" => "Купить",
    "MESS_BTN_DETAIL" => "Подробнее",
    "MESS_BTN_LAZY_LOAD" => "Показать ещё",
    "MESS_BTN_SUBSCRIBE" => "Подписаться",
    "MESS_NOT_AVAILABLE" => "Нет в наличии",
    "META_DESCRIPTION" => "-",	// Установить описание страницы из свойства
    "META_KEYWORDS" => "-",	// Установить ключевые слова страницы из свойства
    "OFFERS_CART_PROPERTIES" => array(
        0 => "ARTNUMBER",
        1 => "COLOR_REF",
        2 => "SIZES_SHOES",
        3 => "SIZES_CLOTHES",
    ),
    "OFFERS_FIELD_CODE" => array(
        0 => "",
        1 => "",
    ),
    "OFFERS_LIMIT" => "5",	// Максимальное количество предложений для показа (0 - все)
    "OFFERS_PROPERTY_CODE" => array(
        0 => "COLOR_REF",
        1 => "SIZES_SHOES",
        2 => "SIZES_CLOTHES",
        3 => "",
    ),
    "OFFERS_SORT_FIELD" => "sort",
    "OFFERS_SORT_FIELD2" => "id",
    "OFFERS_SORT_ORDER" => "asc",
    "OFFERS_SORT_ORDER2" => "desc",
    "OFFER_ADD_PICT_PROP" => "MORE_PHOTO",
    "OFFER_TREE_PROPS" => array(
        0 => "COLOR_REF",
        1 => "SIZES_SHOES",
        2 => "SIZES_CLOTHES",
    ),
    "PAGER_BASE_LINK_ENABLE" => "N",	// Включить обработку ссылок
    "PAGER_DESC_NUMBERING" => "N",	// Использовать обратную навигацию
    "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",	// Время кеширования страниц для обратной навигации
    "PAGER_SHOW_ALL" => "N",	// Показывать ссылку "Все"
    "PAGER_SHOW_ALWAYS" => "N",	// Выводить всегда
    "PAGER_TEMPLATE" => ".default",	// Шаблон постраничной навигации
    "PAGER_TITLE" => "Товары",	// Название категорий
    "PAGE_ELEMENT_COUNT" => "24",	// Количество элементов на странице
    "PARTIAL_PRODUCT_PROPERTIES" => "N",	// Разрешить добавлять в корзину товары, у которых заполнены не все характеристики
    "PRICE_CODE" => array(	// Тип цены
        0 => "BASE",
    ),
    "PRICE_VAT_INCLUDE" => "Y",	// Включать НДС в цену
    "PRODUCT_BLOCKS_ORDER" => "price,props,sku,quantityLimit,quantity,buttons,compare",
    "PRODUCT_DISPLAY_MODE" => "Y",
    "PRODUCT_ID_VARIABLE" => "id",	// Название переменной, в которой передается код товара для покупки
    "PRODUCT_PROPERTIES" => array(	// Характеристики товара
        0 => "NEWPRODUCT",
        1 => "MATERIAL",
    ),
    "PRODUCT_PROPS_VARIABLE" => "prop",	// Название переменной, в которой передаются характеристики товара
    "PRODUCT_QUANTITY_VARIABLE" => "",	// Название переменной, в которой передается количество товара
    "PRODUCT_ROW_VARIANTS" => "[{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':false},{'VARIANT':'2','BIG_DATA':true}]",
    "PRODUCT_SUBSCRIPTION" => "Y",
    "PROPERTY_CODE" => array(	// Свойства
        0 => "NEWPRODUCT",
        1 => "",
    ),
    "PROPERTY_CODE_MOBILE" => "",
    "RCM_PROD_ID" => $_REQUEST["PRODUCT_ID"],
    "RCM_TYPE" => "personal",
    "SECTION_CODE" => "",	// Код раздела
    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],	// ID раздела
    "SECTION_ID_VARIABLE" => "SECTION_ID",	// Название переменной, в которой передается код группы
    "SECTION_URL" => "",	// URL, ведущий на страницу с содержимым раздела
    "SECTION_USER_FIELDS" => array(	// Свойства раздела
        0 => "",
        1 => "",
    ),
    "SEF_MODE" => "N",	// Включить поддержку ЧПУ
    "SET_BROWSER_TITLE" => "Y",	// Устанавливать заголовок окна браузера
    "SET_LAST_MODIFIED" => "N",	// Устанавливать в заголовках ответа время модификации страницы
    "SET_META_DESCRIPTION" => "Y",	// Устанавливать описание страницы
    "SET_META_KEYWORDS" => "Y",	// Устанавливать ключевые слова страницы
    "SET_STATUS_404" => "N",	// Устанавливать статус 404
    "SET_TITLE" => "Y",	// Устанавливать заголовок страницы
    "SHOW_404" => "N",	// Показ специальной страницы
    "SHOW_ALL_WO_SECTION" => "N",	// Показывать все элементы, если не указан раздел
    "SHOW_CLOSE_POPUP" => "N",
    "SHOW_DISCOUNT_PERCENT" => "Y",
    "SHOW_FROM_SECTION" => "N",
    "SHOW_MAX_QUANTITY" => "N",
    "SHOW_OLD_PRICE" => "N",
    "SHOW_PRICE_COUNT" => "1",	// Выводить цены для количества
    "SHOW_SLIDER" => "Y",
    "SLIDER_INTERVAL" => "3000",
    "SLIDER_PROGRESS" => "N",
    "TEMPLATE_THEME" => "blue",
    "USE_ENHANCED_ECOMMERCE" => "Y",
    "USE_MAIN_ELEMENT_SECTION" => "N",	// Использовать основной раздел для показа элемента
    "USE_PRICE_COUNT" => "N",	// Использовать вывод цен с диапазонами
    "USE_PRODUCT_QUANTITY" => "N",	// Разрешить указание количества товара
),
    false
);


$res = CIBlockSection::GetByID($arResult['VARIABLES']['SECTION_ID']);
if($ar_res = $res->GetNext())  {

    //echo "<pre>"; print_r($ar_res['NAME'] ); echo "</pre>";

    $APPLICATION->AddChainItem($ar_res['NAME'], "");

    if ($ar_res['DESCRIPTION']<>null)
        echo $ar_res['DESCRIPTION'];

}?>




