<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$sPrefix = 'SHARES_';
$arShares = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE'),
    'PARAMETERS' => []
];

$arSharesFilter = [
    'ID' => []
];

if ($arVisual['SHARES']['SHOW'] && !empty($arParams['SHARES_PROPERTY'])) {
    $arSharesFilter['ID'] = ArrayHelper::getValue($arResult, ['PROPERTIES', $arParams['SHARES_PROPERTY'], 'VALUE']);

    if (empty($arSharesFilter['ID'])) {
        $arVisual['SHARES']['SHOW'] = false;
    }
} else {
    $arVisual['SHARES']['SHOW'] = false;
}

if (!empty($arShares['TEMPLATE']))
    $arShares['TEMPLATE'] = 'template.' . $arShares['TEMPLATE'];

foreach ($arParams as $sKey => $mValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        $arShares['PARAMETERS'][$sKey] = $mValue;
    }
}

$arShares['PARAMETERS'] = ArrayHelper::merge($arShares['PARAMETERS'], [
    'IBLOCK_ID' => $arParams['SHARES_IBLOCK_ID'],
    'FILTER' => $arSharesFilter,
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'WIDE' => $arParams['WIDE']
]);