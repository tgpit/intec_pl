<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arProducts = [
    'VIEW' => null,
    'TEMPLATE' => null,
    'PARAMETERS' => []
];

foreach ($arVisual['VIEWS'] as $sKey => $arView) {
    if ($arView['ACTIVE']) {
        $arProducts['VIEW'] = $sKey;
        break;
    }
}

$arProducts['TEMPLATE'] = ArrayHelper::getValue($arParams, 'LIST_'.$arProducts['VIEW'].'_TEMPLATE');

if (empty($arProducts['TEMPLATE']))
    $arProducts['SHOW'] = false;

if ($arProducts['SHOW'] || !empty($arProducts['TEMPLATE'])) {
    $sPrefix = 'LIST_'.$arProducts['VIEW'].'_';
    $arProducts['TEMPLATE'] = 'catalog.'.$arProducts['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (StringHelper::startsWith($sKey, $sPrefix)) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            if (
                StringHelper::startsWith($sKey, 'ORDER_FAST_')
            ) continue;

            $arProducts['PARAMETERS'][$sKey] = $mValue;
        } else if (
            StringHelper::startsWith($sKey, 'QUICK_VIEW_') ||
            StringHelper::startsWith($sKey, 'ORDER_FAST_') ||
            StringHelper::startsWith($sKey, 'SECTIONS_TIMER_')
        ) {
            $arProducts['PARAMETERS'][$sKey] = $mValue;
        } else if (StringHelper::startsWith($sKey, 'LIST_GENERAL_')) {
            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length('LIST_GENERAL_')
            );
            $arProducts['PARAMETERS'][$sKey] = $mValue;
        }
    }

    $arProducts['PARAMETERS'] = ArrayHelper::merge($arProducts['PARAMETERS'], [
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'IBLOCK_TYPE' => $arParams['PRODUCTS_IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['PRODUCTS_IBLOCK_ID'],
        'ELEMENT_SORT_FIELD' => $arSort['FIELD'],
        'ELEMENT_SORT_ORDER' => $arSort['ORDER'],
        'FILTER_NAME' => $arParams['PRODUCTS_FILTER_NAME'],
        'SHOW_ALL_WO_SECTION' => 'Y',
        'PRODUCT_DISPLAY_MODE' => 'Y',
        'USE_COMPARE' => $arParams['PRODUCTS_COMPARE_USE'],
        'DELAY_USE' => $arParams['PRODUCTS_DELAY_USE'],
        'COMPARE_NAME' => $arParams['PRODUCTS_COMPARE_NAME'],
        'WIDE' => $arParams['WIDE'],
        'COMPATIBLE_MODE' => 'Y',
    ]);
}