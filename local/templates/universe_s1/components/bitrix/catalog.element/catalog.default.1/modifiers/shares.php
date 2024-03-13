<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arShares = [];
$arShares['PREFIX'] = 'SHARES_';
$arShares['SHOW'] = $arParams['SHARES_SHOW'] === 'Y';
$arShares['PARAMETER'] = [];

$arShowCheck = [
    'FB_USE',
    'TW_USE',
    'VK_USE',
    'PINTEREST_USE',
    'OK_USE'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $arShares['PREFIX']))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($arShares['PREFIX']));
    $arShares['PARAMETERS'][$sKey] = $sValue;
}

if ($arShares['SHOW']) {
    $bSharesShow = false;

    foreach ($arShowCheck as $sItem) {
        if ($arShares['PARAMETERS'][$sItem] === 'Y') {
            $bSharesShow = true;
            break;
        }
    }

    $arShares['SHOW'] = $bSharesShow;

    unset($arShowCheck, $bSharesShow);
}


$arResult['SHARES'] = $arShares;

unset($arShares);