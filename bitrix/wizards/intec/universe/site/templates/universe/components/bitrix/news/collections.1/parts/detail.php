<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arDetail = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'DETAIL_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arDetail['TEMPLATE']))
    $arDetail['SHOW'] = false;

if ($arDetail['SHOW']) {
    $sPrefix = 'DETAIL_';
    $arDetail['TEMPLATE'] = 'collections.'.$arDetail['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arDetail['PARAMETERS'][$sKey] = $mValue;
    }

    unset($sKey, $sValue);

    $arDetail['PARAMETERS'] = ArrayHelper::merge($arDetail['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
        'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
        'LAZYLOAD_USE' => $arParams['DETAIL_LAZYLOAD_USE'],
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'FIELD_CODE' => $arParams['DETAIL_FIELD_CODE'],
        'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
        'IBLOCK_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'],
        'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
        'CACHE_TYPE' => 'N',
        'CACHE_TYPE_ORIGINAL' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
        'SET_BROWSER_TITLE' => $arParams['SET_BROWSER_TITLE'],
        'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
        'SET_META_KEYWORDS' => $arParams['SET_META_KEYWORDS'],
        'META_KEYWORDS' => $arParams['META_KEYWORDS'],
        'SET_META_DESCRIPTION' => $arParams['SET_META_DESCRIPTION'],
        'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
        'SET_LAST_MODIFIED' => 'N',
        'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
        'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
        'ADD_ELEMENT_CHAIN' => $arParams['ADD_ELEMENT_CHAIN'],
        'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
        'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
        'STRICT_SECTION_CHECK' => 'N',
        'PAGER_TEMPLATE' => '.default',
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'MESSAGE_404' => $arParams['MESSAGE_404'],

        'WIDE' => 'Y',
    ]);
}