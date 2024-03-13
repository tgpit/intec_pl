<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\regionality\models\Region;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

$sPrefix = 'PRODUCTS_';
$arProducts = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE'),
    'PARAMETERS' => []
];

if (!empty($arProducts['TEMPLATE']))
    $arProducts['TEMPLATE'] = 'images.'.$arProducts['TEMPLATE'];

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
    'AJAX_MODE' => $arParams['AJAX_MODE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'PRODUCT_DISPLAY_MODE' => 'Y',
    'OFFER_TREE_PROPS' => ArrayHelper::getValue($arProducts, ['PARAMETERS', 'OFFERS_PROPERTY_CODE']),
    'USE_COMPARE' => $arParams['PRODUCTS_QUICK_VIEW_COMPARE_USE'],
    'COMPARE_NAME' => $arParams['PRODUCTS_QUICK_VIEW_COMPARE_NAME'],
    'QUICK_VIEW_TIMER_SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'QUICK_VIEW_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'QUICK_VIEW_TIMER_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'CACHE_FILTER' => $arParams['CACHE_FILTER'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'OFFERS_LIMIT' => $arParams['PRODUCTS_OFFERS_LIMIT'],
    'BY_LINK' => 'Y'
]);

if (!empty($arProducts['PARAMETERS']['FILTER_NAME']) && !empty($arResult['IMAGE_ITEMS_ID'])) {
    $GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']] = [
        'ID' => $arResult['IMAGE_ITEMS_ID']
    ];
}

if ($arResult['REGIONALITY']['USE']) {
    $oRegion = Region::getCurrent();

    if (!empty($oRegion)) {
        if ($arResult['REGIONALITY']['FILTER']['USE']) {
            if (!isset($GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']]) || !Type::isArray($GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']]))
                $GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']] = [];

            $arConditions = [
                'LOGIC' => 'OR',
                ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => $oRegion->id]
            ];

            if (!$arResult['REGIONALITY']['FILTER']['STRICT'])
                $arConditions[] = ['PROPERTY_'.$arResult['REGIONALITY']['FILTER']['PROPERTY'] => false];

            $GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']][] = $arConditions;
        }
    }
}