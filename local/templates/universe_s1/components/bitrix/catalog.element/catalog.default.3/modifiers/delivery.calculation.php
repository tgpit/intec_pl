<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arDeliveryCalculation = [];
$arDeliveryCalculation['PREFIX'] = 'DELIVERY_CALCULATION_';
$arDeliveryCalculation['USE'] = $arParams['DELIVERY_CALCULATION_USE'] === 'Y';
$arDeliveryCalculation['TEMPLATE'] = $arParams['DELIVERY_CALCULATION_TEMPLATE'];
$arDeliveryCalculation['PARAMETER'] = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arDeliveryCalculation['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arDeliveryCalculation['PREFIX']));
    $arDeliveryCalculation['PARAMETERS'][$sKey] = $sValue;
}

if (empty($arDeliveryCalculation['TEMPLATE']))
    $arDeliveryCalculation['USE'] = false;

$arResult['DELIVERY_CALCULATION'] = $arDeliveryCalculation;

unset($arDeliveryCalculation);