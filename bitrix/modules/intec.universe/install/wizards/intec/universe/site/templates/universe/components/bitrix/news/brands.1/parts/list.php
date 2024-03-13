<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$arList = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'LIST_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arList['TEMPLATE']))
    $arList['SHOW'] = false;

if ($arList['SHOW']) {
    $sPrefix = 'LIST_';
    $arList['TEMPLATE'] = 'brands.'.$arList['TEMPLATE'];

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
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
        'AJAX_MODE' => $arParams['AJAX_MODE'],
        'AJAX_OPTION_JUMP' => $arParams['AJAX_OPTION_JUMP'],
        'AJAX_OPTION_STYLE' => $arParams['AJAX_OPTION_STYLE'],
        'AJAX_OPTION_HISTORY' => $arParams['AJAX_OPTION_HISTORY'],
        'AJAX_OPTION_ADDITIONAL' => $arParams['AJAX_OPTION_ADDITIONAL'],
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
        'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
        'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
        'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
        'PAGER_TITLE' => $arParams['PAGER_TITLE'],
        'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
        'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
        'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
        'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
        'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'MESSAGE_404' => $arParams['MESSAGE_404'],

        'WIDE' => 'Y',
    ]);
}
