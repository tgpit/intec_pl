<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var bool $bBase
 */

$sDeliveryPrefix = 'DELIVERY_CALCULATION_';
$sDeliveryPrefixLength = StringHelper::length($sDeliveryPrefix);

$arResult['DELIVERY_CALCULATION'] = [
    'USE' => $arParams['DELIVERY_CALCULATION_USE'] === 'Y' && $bBase,
    'TEMPLATE' => $arParams['DELIVERY_CALCULATION_TEMPLATE'],
    'PARAMETERS' => []
];

if ($arResult['DELIVERY_CALCULATION']['USE'] && empty($arResult['DELIVERY_CALCULATION']['TEMPLATE']))
    $arResult['DELIVERY_CALCULATION']['USE'] = false;

if ($bBase && $arResult['DELIVERY_CALCULATION']['USE']) {
    $arExcluded = [
        'USE',
        'TEMPLATE'
    ];

    foreach ($arParams as $key => $sValue) {
        if (!StringHelper::startsWith($key, $sDeliveryPrefix))
            continue;

        $key = StringHelper::cut($key, $sDeliveryPrefixLength);

        if (ArrayHelper::isIn($key, $arExcluded))
            continue;

        $arResult['DELIVERY_CALCULATION']['PARAMETERS'][$key] = $sValue;
    }

    unset($arExcluded, $key, $sValue);
}

unset($sDeliveryPrefix, $sDeliveryPrefixLength);