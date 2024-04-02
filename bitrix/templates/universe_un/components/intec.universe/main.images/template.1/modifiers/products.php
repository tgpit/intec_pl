<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams['PRODUCTS_ELEMENTS_COUNT'] = Type::toInteger($arParams['PRODUCTS_ELEMENTS_COUNT']);

if ($arParams['PRODUCTS_ELEMENTS_COUNT'] < 0)
    $arParams['PRODUCTS_ELEMENTS_COUNT'] = 0;

if (empty($arParams['PRODUCTS_FILTER']))
    $arParams['PRODUCTS_FILTER'] = 'collectionsFilter';

if (Type::isArray($arParams['PRODUCTS_PRICE_CODE']))
    $arParams['PRODUCTS_PRICE_CODE'] = array_filter($arParams['PRODUCTS_PRICE_CODE']);
else
    $arParams['PRODUCTS_PRICE_CODE'] = [];

$parameters = [];
$excluded = [
    'SHOW',
    'IBLOCK_TYPE',
    'IBLOCK_ID',
    'ELEMENTS_COUNT',
    'FILTER',
    'PRICE_CODE',
    'CONVERT_CURRENCY',
    'CURRENCY_ID',
    'PRICE_VAT_INCLUDE',
    'SHOW_PRICE_COUNT',
    'SORT_BY',
    'ORDER_BY',
    'LIST_URL',
    'SECTION_URL',
    'DETAIL_URL'
];

foreach ($arParams as $key => $parameter) {
    if (!StringHelper::startsWith($key, 'PRODUCTS_'))
        continue;

    $key = StringHelper::cut($key, 9);

    if (ArrayHelper::isIn($key, $excluded))
        continue;

    $parameters[$key] = $parameter;
}

unset($excluded, $key, $parameter);

$arResult['PRODUCTS'] = ArrayHelper::merge([
    'IBLOCK_TYPE' => $arParams['PRODUCTS_IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['PRODUCTS_IBLOCK_ID'],
    'SECTION_ID' => null,
    'SECTION_CODE' => null,
    'SECTION_USER_FIELDS' => [],
    'FILTER_NAME' => $arParams['PRODUCTS_FILTER'],
    'INCLUDE_SUBSECTIONS' => 'Y',
    'SHOW_ALL_WO_SECTION' => 'Y',
    'HIDE_NOT_AVAILABLE' => 'L',
    'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
    'ELEMENT_SORT_FIELD' => $arParams['PRODUCTS_SORT_BY'],
    'ELEMENT_SORT_ORDER' => ArrayHelper::fromRange(['ASC', 'DESC'], $arParams['PRODUCTS_ORDER_BY']),
    'ELEMENT_SORT_FIELD2' => 'name',
    'ELEMENT_SORT_ORDER2' => 'asc',
    'OFFERS_SORT_FIELD' => 'sort',
    'OFFERS_SORT_ORDER' => 'asc',
    'OFFERS_SORT_FIELD2' => 'name',
    'OFFERS_SORT_ORDER2' => 'asc',
    'PROPERTY_CODE' => [],
    'PROPERTY_CODE_MOBILE' => [],
    'OFFERS_FIELD_CODE' => [],
    'OFFERS_PROPERTY_CODE' => [],
    'OFFERS_LIMIT' => 0,
    'BACKGROUND_IMAGE' => null,
    'PAGE_ELEMENT_COUNT' => Type::toInteger($arParams['PRODUCTS_ELEMENTS_COUNT']),
    'LINE_ELEMENT_COUNT' => null,
    'LIST_URL' => $arParams['PRODUCTS_LIST_URL'],
    'SECTION_URL' => $arParams['PRODUCTS_SECTION_URL'],
    'DETAIL_URL' => $arParams['PRODUCTS_DETAIL_URL'],
    'SECTION_ID_VARIABLE' => 'SECTION_ID',
    'AJAX_MODE' => 'N',
    'AJAX_OPTION_JUMP' => 'N',
    'AJAX_OPTION_STYLE' => 'N',
    'AJAX_OPTION_HISTORY' => 'N',
    'CACHE_TYPE' => 'A',
    'CACHE_TIME' => 0,
    'CACHE_GROUPS' => 'Y',
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'USE_MAIN_ELEMENT_SECTION' => 'N',
    'ADD_SECTIONS_CHAIN' => 'N',
    'CACHE_FILTER' => 'Y',
    'ACTION_VARIABLE' => null,
    'PRODUCT_ID_VARIABLE' => null,
    'PRICE_CODE' => $arParams['PRODUCTS_PRICE_CODE'],
    'USE_PRICE_COUNT' => 'N',
    'CONVERT_CURRENCY' => $arParams['PRODUCTS_CONVERT_CURRENCY'],
    'CURRENCY_ID' => $arParams['PRODUCTS_CURRENCY_ID'],
    'PRICE_VAT_INCLUDE' => $arParams['PRODUCTS_PRICE_VAT_INCLUDE'],
    'SHOW_PRICE_COUNT' => Type::toFloat($arParams['PRODUCTS_SHOW_PRICE_COUNT']),
    'BASKET_URL' => null,
    'USE_PRODUCT_QUANTITY' => 'N',
    'ADD_PROPERTIES_TO_BASKET' => 'N',
    'PAGER_TEMPLATE' => '.default',
    'DISPLAY_TOP_PAGER' => 'N',
    'DISPLAY_BOTTOM_PAGER' => 'N',
    'PAGER_TITLE' => null,
    'PAGER_SHOW_ALWAYS' => 'N',
    'PAGER_DESC_NUMBERING' => 'N',
    'PAGER_DESC_NUMBERING_CACHE_TIME' => 36000,
    'PAGER_SHOW_ALL' => 'N',
    'PAGER_BASE_LINK_ENABLE' => 'N',
    'SET_STATUS_404' => 'N',
    'SHOW_404' => 'N',
    'COMPATIBLE_MODE' => 'Y',
    'DISABLE_INIT_JS_IN_COMPONENT' => 'Y',
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
], $parameters);

unset($parameters);