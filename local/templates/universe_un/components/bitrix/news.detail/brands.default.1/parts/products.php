<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!empty($arParams['PRODUCTS_FILTER_NAME']))
    $GLOBALS[$arParams['PRODUCTS_FILTER_NAME']] = [
        'PROPERTY_'.$arParams['PRODUCTS_PROPERTY_BRAND'] => $arResult['ID']
    ];

$sPrefix = 'PRODUCTS_';
$arProducts = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE'),
    'PARAMETERS' => []
];

if (!empty($arProducts['TEMPLATE']))
    $arProducts['TEMPLATE'] = 'catalog.' . $arProducts['TEMPLATE'];

foreach ($arParams as $sKey => $mValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arProducts['PARAMETERS'][$sKey] = $mValue;
    }
}

$arProducts['PARAMETERS'] = ArrayHelper::merge($arProducts['PARAMETERS'], [
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'SHOW_ALL_WO_SECTION' => 'N',
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'OFFER_TREE_PROPS' => ArrayHelper::getValue($arProducts, ['PARAMETERS', 'OFFERS_PROPERTY_CODE']),
    'USE_COMPARE' => $arParams['PRODUCTS_COMPARE_USE'],
    'DELAY_USE' => $arParams['PRODUCTS_DELAY_USE'],
    'COMPARE_NAME' => $arParams['PRODUCTS_COMPARE_NAME'],
    'WIDE' => $arParams['WIDE'],
    'SHOW_ALL_WO_SECTION' => 'N',
    'INCLUDE_SUBSECTIONS' => 'Y',
    'COMPATIBLE_MODE' => 'Y',
    'BY_LINK' => 'Y'
]);