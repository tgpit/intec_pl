<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */


$sPrefix = 'SECTIONS_TIMER_';

$sTemplate = $arParams[$sPrefix . 'TEMPLATE'];

$iTimerQuantity = 0;

if ($arParams[$sPrefix . 'TIMER_QUANTITY_ENTER_VALUE'] === 'Y') {
    $iTimerQuantity = $arParams[$sPrefix . 'QUANTITY'];
} else {
    $iTimerQuantity = $arItem['CATALOG_QUANTITY'];
}

$arTimerParams = [];

foreach ($arParams as $sKey => $mValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

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
    'template' => $sTemplate,
    'parameters' => $arTimerParams
];

$arResult['TIMER']['PROPERTIES'] = $arTimerParams;

unset($arTimerParams);