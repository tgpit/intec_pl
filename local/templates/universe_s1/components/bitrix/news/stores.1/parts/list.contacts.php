<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var
 */

$arList = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'LIST_CONTACTS_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arList['TEMPLATE']))
    $arList['SHOW'] = false;

if ($arList['SHOW']) {
    $sPrefix = 'LIST_CONTACTS_';
    $arList['TEMPLATE'] = 'stores.'.$arList['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arList['PARAMETERS'][$sKey] = $mValue;
    }

    unset($sKey, $sValue);

    $arList['PARAMETERS'] = ArrayHelper::merge($arList['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'NEWS_COUNT' => $arParams['NEWS_COUNT'],
        'SORT_BY1' => $arParams['SORT_BY1'],
        'SORT_ORDER1' => $arParams['SORT_ORDER1'],
        'SORT_BY2' => $arParams['SORT_BY2'],
        'SORT_ORDER2' => $arParams['SORT_ORDER2'],
        'FIELD_CODE' => $arParams['LIST_FIELD_CODE'],
        'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
        'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
        'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
        'IBLOCK_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
        'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
        'MESSAGE_404' => $arParams['MESSAGE_404'],
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'FILE_404' => $arParams['FILE_404'],
        'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_FILTER' => $arParams['CACHE_FILTER'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
        'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
        'PAGER_TITLE' => $arParams['PAGER_TITLE'],
        'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
        'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
        'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
        'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
        'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
        'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
        'PAGER_BASE_LINK' => $arParams['PAGER_BASE_LINK'],
        'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
        'DISPLAY_DATE' => $arParams['DISPLAY_DATE'],
        'DISPLAY_NAME' => 'Y',
        'DISPLAY_TAB_ALL' => $arParams['DISPLAY_LIST_TAB_ALL'],
        'PREVIEW_TRUNCATE_LEN' => $arParams['PREVIEW_TRUNCATE_LEN'],
        'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
        'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
        'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
        'FILTER_NAME' => $arParams['FILTER_NAME'],
        'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'DESCRIPTION_DISPLAY' => $arParams['LIST_DESCRIPTION_DISPLAY'],
        'PICTURE_DISPLAY' => $arParams['LIST_PICTURE_DISPLAY'],
        'MAP_ID' => $arParams['MAP_ID'],
        "MAP_VENDOR" => $arParams['MAP_VENDOR'],
        "PROPERTY_MAP" => $arParams['PROPERTY_MAP'],
        "API_KEY_MAP" => $arParams['API_KEY_MAP'],
        "PROPERTY_ADDRESS" => $arParams["PROPERTY_ADDRESS"],
        "PROPERTY_PHONE" => $arParams['PROPERTY_PHONE'],
        "PROPERTY_EMAIL" => $arParams['PROPERTY_EMAIL'],
        "PROPERTY_SCHEDULE" => $arParams['PROPERTY_SCHEDULE'],
        "MAP_SHOW" => $arParams['MAP_SHOW'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
    ]);
}
