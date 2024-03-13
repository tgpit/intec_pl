<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'SEND_';
$sPrefixLength = StringHelper::length($sPrefix);
$arParameters = [];
$arExcluded = [
    'USE',
    'TEMPLATE'
];

foreach ($arParams as $key => $parameter) {
    if (!StringHelper::startsWith($key, $sPrefix))
        continue;

    $key = StringHelper::cut($key, $sPrefixLength);

    if (ArrayHelper::isIn($key, $arExcluded))
        continue;

    $arParameters[$key] = $parameter;
}

unset($key, $parameter);

$APPLICATION->IncludeComponent(
    'intec.universe:reviews',
    $arVisual['SEND']['TEMPLATE'], ArrayHelper::merge([
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SEETINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'FORM_USE' => 'Y',
        'MODE' => 'default',
        'ITEMS_HIDE' => 'Y',
        'NAVIGATION_USE' => 'N',
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME']
    ], $arParameters),
    $component,
    ['HIDE_ICONS' => 'Y']
);

unset(
    $sPrefix,
    $sPrefixLength,
    $arParameters,
    $arExcluded
);