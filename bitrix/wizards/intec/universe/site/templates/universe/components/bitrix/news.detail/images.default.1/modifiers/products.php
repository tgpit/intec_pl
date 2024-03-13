<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\StringHelper;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

$arProducts = [];
$arProducts['PREFIX'] = 'PRODUCTS_';
$arProducts['PARAMETERS'] = [];
$arProducts['TEMPLATE'] = 'catalog.tile.' . $arParams[$arProducts['PREFIX'] . 'TEMPLATE'];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arProducts['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arProducts['PREFIX']));
    $arProducts['PARAMETERS'][$sKey] = $sValue;
}

$arProducts['PARAMETERS'] = ArrayHelper::merge($arProducts['PARAMETERS'], [
    'SET_TITLE' => 'N',
    'SET_BROWSER_TITLE' => 'N',
    'SET_META_KEYWORDS' => 'N',
    'SET_META_DESCRIPTION' => 'N',
    'SET_LAST_MODIFIED' => 'N',
    'BY_LINK' => 'Y'
]);

$arVisual['PRODUCTS']['SHOW'] = !empty($arProducts['PARAMETERS']['TEMPLATE']) &&
    !empty($arProducts['PARAMETERS']['IBLOCK_ID']) &&
    !empty($arProducts['PARAMETERS']['IBLOCK_TYPE']) &&
    !empty($arProducts['PARAMETERS']['FILTER_NAME']);

if (isset($arProducts['PARAMETERS']['FILTER_NAME'])) {
    $GLOBALS[$arProducts['PARAMETERS']['FILTER_NAME']] = [
        'ID' => ArrayHelper::getValue($arResult['PROPERTIES'], [
            $arParams['PROPERTY_PRODUCTS'],
            'VALUE'
        ])
    ];
} else {
    $arVisual['PRODUCTS']['SHOW'] = false;
}

$arResult['PRODUCTS'] = $arProducts;

unset($arProducts);