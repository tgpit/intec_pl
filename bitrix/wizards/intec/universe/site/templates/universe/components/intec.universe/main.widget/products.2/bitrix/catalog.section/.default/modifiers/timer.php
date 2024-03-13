<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */


$sPrefix = 'SECTION_TIMER_';

$iTimerQuantity = 0;

if ($arParams[$sPrefix . 'TIMER_QUANTITY_ENTER_VALUE'] === 'Y') {
    $iTimerQuantity = $arParams[$sPrefix . 'QUANTITY'];
} else {
    $iTimerQuantity = $arItem['CATALOG_QUANTITY'];
}

$arTimerParams = [
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
];

$excluded = [
    'SETTINGS_USE',
    'LAZYLOAD_USE'
];

foreach ($arParams as $sKey => $mValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if (ArrayHelper::isIn($sKey, $excluded))
            continue;

        if ($sKey == 'QUANTITY')
            $arTimerParams[$sKey] = $iTimerQuantity;

        $arTimerParams[$sKey] = $mValue;
    }
}

$arTimerParams['IBLOCK_ID'] = $arParams['IBLOCK_ID'];
$arTimerParams['IBLOCK_TYPE'] = $arParams['IBLOCK_TYPE'];
$arTimerParams['ELEMENT_ID'] = $arItem['ID'];


$arTimerParams = [
    'component' => 'intec.universe:product.timer',
    'template' => 'template.1',
    'parameters' => $arTimerParams
];

$arResult['TIMER']['PROPERTIES'] = $arTimerParams;

unset($arTimerParams);