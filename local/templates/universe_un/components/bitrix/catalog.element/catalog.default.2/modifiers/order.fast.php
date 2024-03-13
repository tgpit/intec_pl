<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arOrderFast = [];
$arOrderFast['PREFIX'] = 'ORDER_FAST_';
$arOrderFast['USE'] = $arParams['ORDER_FAST_USE'] === 'Y';
$arOrderFast['TEMPLATE'] = $arParams['ORDER_FAST_TEMPLATE'];
$arOrderFast['PARAMETER'] = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arOrderFast['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arOrderFast['PREFIX']));
    $arOrderFast['PARAMETERS'][$sKey] = $sValue;
}

$arOrderFast['PARAMETERS']['PROPERTY_ARTICLE'] = $arParams['PROPERTY_ARTICLE'];
$arOrderFast['PARAMETERS']['OFFERS_PROPERTY_ARTICLE'] = $arParams['OFFERS_PROPERTY_ARTICLE'];

if (empty($arOrderFast['TEMPLATE']))
    $arOrderFast['USE'] = false;

$arResult['ORDER_FAST'] = $arOrderFast;

unset($arOrderFast);