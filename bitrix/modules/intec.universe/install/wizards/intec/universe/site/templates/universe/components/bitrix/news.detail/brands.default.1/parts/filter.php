<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arSection
 * @var string $sLevel
 */

$arParams = ArrayHelper::merge([
    'FILTER_TYPE' => null,
    'FILTER_TEMPLATE' => null
], $arParams);

$arFilter = [
    'SHOW' => $arParams['FILTER_USE'] === 'Y',
    'AJAX' => $arParams['FILTER_AJAX'] === 'Y',
    'TYPE' => ArrayHelper::fromRange([
        'horizontal',
        'vertical'
    ], $arParams['FILTER_TYPE']),
    'TEMPLATE' => $arParams['FILTER_TEMPLATE'],
    'PARAMETERS' => []
];

if (empty($arFilter['TEMPLATE']))
    $arFilter['SHOW'] = false;

if ($arFilter['SHOW']) {
    $sPrefix = 'FILTER_';
    $arFilter['TEMPLATE'] = $arFilter['TYPE'].'.'.$arFilter['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TYPE' || $sKey === 'TEMPLATE')
            continue;

        $arFilter['PARAMETERS'][$sKey] = $mValue;
    }

    $arFilter['PARAMETERS'] = ArrayHelper::merge($arFilter['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['PRODUCTS_IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['PRODUCTS_IBLOCK_ID'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'PREFILTER_NAME' => $arParams['PRODUCTS_FILTER_NAME'],
        'FILTER_NAME' => $arParams['PRODUCTS_FILTER_NAME'],
        'PRICE_CODE' => $arParams['PRODUCTS_PRICE_CODE'],
        'CONVERT_CURRENCY' => $arParams['PRODUCTS_CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['PRODUCTS_CURRENCY_ID'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'SAVE_IN_SESSION' => 'N',
        'XML_EXPORT' => 'Y',
        'SECTION_TITLE' => 'NAME',
        'SECTION_DESCRIPTION' => 'DESCRIPTION',
        'SHOW_ALL_WO_SECTION' => 'Y',
        'SEF_MODE' => $arParams['SEF_MODE'],
        'SEF_RULE' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['smart_filter'],
        'SMART_FILTER_PATH' => $arResult['VARIABLES']['SMART_FILTER_PATH'],
        'PAGER_PARAMS_NAME' => $arParams['PAGER_PARAMS_NAME'],
        'POPUP_USE' => $arFilter['AJAX'] ? 'N' : 'Y'
    ]);
}