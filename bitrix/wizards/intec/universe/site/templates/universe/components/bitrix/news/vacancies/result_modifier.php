<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\bitrix\Component;

/* получение параметров news.list */

$sPrefix = 'LIST_';
$sTemplate = $arParams[$sPrefix.'TEMPLATE'];
$arList = [
  'TEMPLATE' => $sTemplate,
  'PARAMETERS' => []
];

$sLength = StringHelper::length($sPrefix); //получаем длинну префикса

foreach ($arParams as $key => $sValue) {
    //если параметр начинается с префикса
    if (StringHelper::startsWith($key, $sPrefix)) {
        //обрезаем этот префикс
        $key = StringHelper::cut($key, $sLength);

        //пропускаем параметр с шаблоном списка, так как он не нужен
        if ($key === 'TEMPLATE')
            continue;

        //формируем массив параметров (уже без префикса)
        $arList['PARAMETERS'][$key] = $sValue;
    }
}
unset($key, $sValue);

//если свойства не выбраны, заносим их в значения параметра
if(!empty($arParams['PROPERTY_CITY']) && !ArrayHelper::isIn($arParams['PROPERTY_CITY'], $arParams['LIST_PROPERTY_CODE'])){
    $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_CITY'];
}
if(!empty($arParams['PROPERTY_SKILL']) && !ArrayHelper::isIn($arParams['PROPERTY_SKILL'], $arParams['LIST_PROPERTY_CODE'])){
    $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SKILL'];
}
if(!empty($arParams['PROPERTY_TYPE_EMPLOYMENT']) && !ArrayHelper::isIn($arParams['PROPERTY_TYPE_EMPLOYMENT'], $arParams['LIST_PROPERTY_CODE'])){
    $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_TYPE_EMPLOYMENT'];
}

$arList['PARAMETERS'] = ArrayHelper::merge($arList['PARAMETERS'], [
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'NEWS_COUNT' => $arParams['NEWS_COUNT'],
    'SORT_BY1' => $arParams['SORT_BY1'],
    'SORT_ORDER1' => $arParams['SORT_ORDER1'],
    'SORT_BY2' => $arParams['SORT_BY2'],
    'SORT_ORDER2' => $arParams['SORT_ORDER2'],
    'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
    'CHECK_DATES' => $arParams['CHECK_DATES'],
    'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
    'SET_TITLE' => $arParams['SET_TITLE'],
    'SET_BROWSER_TITLE' => $arParams['SET_TITLE'],
    'SET_META_KEYWORDS' => 'Y',
    'SET_META_DESCRIPTION' => 'Y',
    'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
    'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
    'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
    'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
    'STRICT_SECTION_CHECK' => $arParams['STRICT_SECTION_CHECK'],
    'SET_STATUS_404' => $arParams['SET_STATUS_404'],
    'SHOW_404' => $arParams['SHOW_404'],
    'MESSAGE_404' => $arParams['MESSAGE_404'],
    'PROPERTY_CITY' => $arParams['PROPERTY_CITY'],
    'PROPERTY_SKILL' => $arParams['PROPERTY_SKILL'],
    'PROPERTY_TYPE_EMPLOYMENT' => $arParams['PROPERTY_TYPE_EMPLOYMENT'],
    'PROPERTY_SALARY' => $arParams['PROPERTY_SALARY']

]);
$arResult['LIST'] = $arList;

unset($sPrefix, $sTemplate, $sLength);

/* получение параметров news.detail */

$sPrefix = 'DETAIL_';
$sTemplate = $arParams[$sPrefix.'TEMPLATE'];
$arDetail = [
    'TEMPLATE' => $sTemplate,
    'PARAMETERS' => []
];

$sLength = StringHelper::length($sPrefix); //получаем длинну префикса

foreach ($arParams as $key => $sValue) {
    //если параметр начинается с префикса
    if (StringHelper::startsWith($key, $sPrefix)) {
        //обрезаем этот префикс
        $key = StringHelper::cut($key, $sLength);

        //пропускаем параметр с шаблоном списка, так как он не нужен
        if ($key === 'TEMPLATE')
            continue;

        //формируем массив параметров (уже без префикса)
        $arDetail['PARAMETERS'][$key] = $sValue;
    }
}
unset($key, $sValue);

//если свойства не выбраны, заносим их в значения параметра
if(!empty($arParams['PROPERTY_CITY']) && !ArrayHelper::isIn($arParams['PROPERTY_CITY'], $arParams['DETAIL_PROPERTY_CODE'])){
    $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_CITY'];
}
if(!empty($arParams['PROPERTY_SKILL']) && !ArrayHelper::isIn($arParams['PROPERTY_SKILL'], $arParams['DETAIL_PROPERTY_CODE'])){
    $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SKILL'];
}
if(!empty($arParams['PROPERTY_TYPE_EMPLOYMENT']) && !ArrayHelper::isIn($arParams['PROPERTY_TYPE_EMPLOYMENT'], $arParams['DETAIL_PROPERTY_CODE'])){
    $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_TYPE_EMPLOYMENT'];
}

$arDetail['PARAMETERS'] = ArrayHelper::merge($arDetail['PARAMETERS'], [
    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
    "FIELD_CODE" => $arParams["DETAIL_FIELD_CODE"],
    "PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
    "DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["detail"],
    "SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
    "META_KEYWORDS" => $arParams["META_KEYWORDS"],
    "META_DESCRIPTION" => $arParams["META_DESCRIPTION"],
    "BROWSER_TITLE" => $arParams["BROWSER_TITLE"],
    "SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
    "DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
    "SET_LAST_MODIFIED" => $arParams["SET_LAST_MODIFIED"],
    "SET_TITLE" => $arParams["SET_TITLE"],
    "MESSAGE_404" => $arParams["MESSAGE_404"],
    "SET_STATUS_404" => $arParams["SET_STATUS_404"],
    "SHOW_404" => $arParams["SHOW_404"],
    "FILE_404" => $arParams["FILE_404"],
    "INCLUDE_IBLOCK_INTO_CHAIN" => $arParams["INCLUDE_IBLOCK_INTO_CHAIN"],
    "ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
    "ACTIVE_DATE_FORMAT" => $arParams["DETAIL_ACTIVE_DATE_FORMAT"],
    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
    "CACHE_TIME" => $arParams["CACHE_TIME"],
    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
    "USE_PERMISSIONS" => $arParams["USE_PERMISSIONS"],
    "GROUP_PERMISSIONS" => $arParams["GROUP_PERMISSIONS"],
    "CHECK_DATES" => $arParams["CHECK_DATES"],
    "ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
    "ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
    "SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
    "SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
    "IBLOCK_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["news"],
    'PROPERTY_CITY' => $arParams['PROPERTY_CITY'],
    'PROPERTY_SKILL' => $arParams['PROPERTY_SKILL'],
    'PROPERTY_TYPE_EMPLOYMENT' => $arParams['PROPERTY_TYPE_EMPLOYMENT'],
    'PROPERTY_SALARY' => $arParams['PROPERTY_SALARY']

]);
$arResult['DETAIL'] = $arDetail;