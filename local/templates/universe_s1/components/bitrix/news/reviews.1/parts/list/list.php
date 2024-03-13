<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'LIST_';
$sPrefixLength = StringHelper::length($sPrefix);

$arCommonParameters = [
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'NEWS_COUNT' => $arParams['NEWS_COUNT'],
    'SORT_BY1' => $arParams['SORT_BY1'],
    'SORT_ORDER1' => $arParams['SORT_ORDER1'],
    'SORT_BY2' => $arParams['SORT_BY2'],
    'SORT_ORDER2' => $arParams['SORT_ORDER2'],
    'FILTER_NAME' => $arParams['FILTER_NAME'],
    'FIELD_CODE' => $arParams['LIST_FIELD_CODE'],
    'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
    'CHECK_DATES' => $arParams['CHECK_DATES'],
    'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
    'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
    'IBLOCK_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
    'AJAX_MODE' => $arParams['AJAX_MODE'],
    'AJAX_OPTION_JUMP' => $arParams['AJAX_OPTION_JUMP'],
    'AJAX_OPTION_STYLE' => $arParams['AJAX_OPTION_STYLE'],
    'AJAX_OPTION_HISTORY' => $arParams['AJAX_OPTION_HISTORY'],
    'PREVIEW_TRUNCATE_LEN' => $arParams['PREVIEW_TRUNCATE_LEN'],
    'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
    'SET_TITLE' => $arParams['SET_TITLE'],
    'SET_BROWSER_TITLE' => $arParams['SET_BROWSER_TITLE'],
    'SET_META_KEYWORDS' => $arParams['SET_META_KEYWORDS'],
    'SET_META_DESCRIPTION' => $arParams['SET_META_DESCRIPTION'],
    'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
    'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
    'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
    'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
    'PARENT_SECTION' => $arParams['PARENT_SECTION'],
    'PARENT_SECTION_CODE' => $arParams['PARENT_SECTION_CODE'],
    'INCLUDE_SUBSECTIONS' => $arParams['INCLUDE_SUBSECTIONS'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
    'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
    'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
    'PAGER_TITLE' => $arParams['PAGER_TITLE'],
    'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
    'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
    'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
    'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
    'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
    'PAGER_BASE_LINK' => $arParams['PAGER_BASE_LINK'],
    'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
    'SET_STATUS_404' => $arParams['SET_STATUS_404'],
    'SHOW_404' => $arParams['SHOW_404'],
    'FILE_404' => $arParams['FILE_404'],
    'MESSAGE_404' => $arParams['MESSAGE_404'],
    'DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
    'LIST_TEMPLATE' => $arParams['LIST_TEMPLATE'],
    'VIDEO_IBLOCK_TYPE' => $arParams['VIDEO_IBLOCK_TYPE'],
    'VIDEO_IBLOCK_ID' => $arParams['VIDEO_IBLOCK_ID'],
    'STAFF_IBLOCK_TYPE' => $arParams['STAFF_IBLOCK_TYPE'],
    'STAFF_IBLOCK_ID' => $arParams['STAFF_IBLOCK_ID'],
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'PROPERTY_INFORMATION' => $arParams['PROPERTY_INFORMATION'],
    'PROPERTY_RATING' => $arParams['PROPERTY_RATING'],
    'PROPERTY_VIDEO' => $arParams['PROPERTY_VIDEO'],
    'VIDEO_PROPERTY_URL' => $arParams['VIDEO_PROPERTY_URL'],
    'PROPERTY_PICTURES' => $arParams['PROPERTY_PICTURES'],
    'PROPERTY_FILES' => $arParams['PROPERTY_FILES'],
    'PROPERTY_STAFF' => $arParams['PROPERTY_STAFF'],
    'STAFF_PROPERTY_POSITION' => $arParams['STAFF_PROPERTY_POSITION']
];

$arFilteredParameters = [];
$arExcludedParameters = [
    'TEMPLATE'
];

foreach ($arParams as $key => $parameter) {
    if (!StringHelper::startsWith($key, 'LIST_'))
        continue;

    $key = StringHelper::cut($key, $sPrefixLength);

    if (ArrayHelper::isIn($key, $arExcludedParameters))
        continue;

    $arFilteredParameters[$key] = $parameter;
}

unset($key, $parameter);

$arParameters = ArrayHelper::merge($arFilteredParameters, $arCommonParameters);

$APPLICATION->IncludeComponent(
    'bitrix:news.list',
    $arVisual['LIST']['TEMPLATE'],
    $arParameters,
    $component
);

unset(
    $sPrefix,
    $sPrefixLength,
    $arCommonParameters,
    $arFilteredParameters,
    $arExcludedParameters,
    $arParameters
);