<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

$this->setFrameMode(true);

if (!CModule::IncludeModule('iblock'))
    return;

if (!CModule::IncludeModule('intec.core'))
    return;

if (empty($arParams['IBLOCK_ID']))
    return;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'IBLOCK_TYPE' => null,
    'IBLOCK_ID' => null,
    'CACHE_TIME' => null,
    'CACHE_TYPE' => null,
    'CACHE_GROUPS' => null,
    'PRICE_CODE' => null,
    'BASKET_URL' => null
], $arParams);

$arParameters = ArrayHelper::merge([
    'FILTER_NAME' => 'arFilter'
], $arParams, [
    'USE_FILTER' => 'Y',
    'SECTION_ID' => null,
    'SECTION_CODE' => null,
    'INCLUDE_SUBSECTIONS' => 'Y',
    'SHOW_ALL_WO_SECTION' => 'Y',
    'HIDE_NOT_AVAILABLE' => 'N',
    'HIDE_NOT_AVAILABLE_OFFERS' => 'N',
    'ELEMENT_SORT_FIELD' => 'SORT',
    'ELEMENT_SORT_ORDER' => 'ASC',
    'ELEMENT_SORT_FIELD2' => 'ID',
    'ELEMENT_SORT_ORDER2' => 'DESC',
    'OFFERS_SORT_FIELD' => 'SORT',
    'OFFERS_SORT_ORDER' => 'ASC',
    'OFFERS_SORT_FIELD2' => 'ID',
    'OFFERS_SORT_ORDER2' => 'DESC',
    'OFFERS_FIELD_CODE' => [],
    'OFFERS_PROPERTY_PICTURE_DIRECTORY' => $arParams['OFFERS_PROPERTY_PICTURE_DIRECTORY'],
    'OFFER_TREE_PROPS' => $arParams['OFFERS_PROPERTY_CODE'],
    'SECTION_ID_VARIABLE' => null,
    'SEF_MODE' => 'N',
    'AJAX_MODE' => 'N',
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
    'PRODUCT_PROPERTIES' => [],
    'SET_STATUS_404' => 'N',
    'SHOW_404' => 'N',
    'COMPATIBLE_MODE' => 'Y',
    'DISABLE_INIT_JS_IN_COMPONENT' => 'Y',
    'PRODUCT_DISPLAY_MODE' => 'Y'
]);

if (empty($arParameters['FILTER_NAME']))
    $arParameters['FILTER_NAME'] = 'arFilter';

$arFilter = ArrayHelper::getValue($GLOBALS, $arParameters['FILTER_NAME']);

if (!Type::isArray($arFilter))
    $arFilter = [];

$arParams['MODE'] = ArrayHelper::fromRange(['period', 'day'], $arParams['MODE']);
$sDate = date('Y-m-d');

if ($arParams['MODE'] === 'period') {
    if (!empty($arParams['PROPERTY_SHOW_START']) && !empty($arParams['PROPERTY_SHOW_END'])) {
        $arFilter['<=PROPERTY_'.$arParams['PROPERTY_SHOW_START']] = $sDate;
        $arFilter['>=PROPERTY_'.$arParams['PROPERTY_SHOW_END']] = $sDate;
    }
} else {
    if (!empty($arParams['PROPERTY_SHOW_END'])) {
        $arFilter['=PROPERTY_'.$arParams['PROPERTY_SHOW_END_DAY']] = $sDate;
    }
}

$GLOBALS[$arParameters['FILTER_NAME']] = $arFilter;

$APPLICATION->IncludeComponent(
    'bitrix:catalog.section',
    '.default',
    $arParameters,
    $component
);
