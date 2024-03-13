<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];

$arParameters = [];

$prefix = 'TOP_';
$prefixLength = StringHelper::length($prefix);
$excluded = [
    'ELEMENT_COUNT',
    'LINE_ELEMENT_COUNT',
    'ELEMENT_SORT_FIELD',
    'ELEMENT_SORT_ORDER',
    'FIELD_CODE',
    'PROPERTY_CODE',
    'TEMPLATE'
];

foreach ($arParams as $key => $parameter) {
    if (!StringHelper::startsWith($key, $prefix))
        continue;

    $key = StringHelper::cut($key, $prefixLength);

    if (ArrayHelper::isIn($key, $excluded))
        continue;

    $arParameters[$key] = $parameter;
}

$APPLICATION->IncludeComponent(
	'bitrix:photo.sections.top', 
	$arVisual['TOP']['TEMPLATE'],
    ArrayHelper::merge([
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_COUNT' => $arParams['SECTION_COUNT'],
        'ELEMENT_COUNT' => $arParams['TOP_ELEMENT_COUNT'],
        'LINE_ELEMENT_COUNT' => $arParams['TOP_LINE_ELEMENT_COUNT'],
        'SECTION_SORT_FIELD' => $arParams['SECTION_SORT_FIELD'],
        'SECTION_SORT_ORDER' => $arParams['SECTION_SORT_ORDER'],
        'ELEMENT_SORT_FIELD' => $arParams['TOP_ELEMENT_SORT_FIELD'],
        'ELEMENT_SORT_ORDER' => $arParams['TOP_ELEMENT_SORT_ORDER'],
        'FIELD_CODE' => $arParams['TOP_FIELD_CODE'],
        'PROPERTY_CODE' => $arParams['TOP_PROPERTY_CODE'],
        'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
        'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
        'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
        'SETTINGS_USE' => 'N',
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
    ], $arParameters),
	$component
);