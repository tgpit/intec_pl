<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['SEND'] = [
    'TITLE' => $arParams['SEND_TITLE'],
    'COMPONENT' => 'intec.universe:reviews',
    'TEMPLATE' => $arParams['SEND_TEMPLATE'],
    'PARAMETERS' => [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'FORM_USE' => 'Y',
        'MODE' => 'default',
        'ITEMS_HIDE' => 'Y',
        'NAVIGATION_USE' => 'N',
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME']
    ]
];

$sendPrefix = 'SEND_';
$sendPrefixLength = StringHelper::length($sendPrefix);
$sendParameters = [];
$sendExcluded = [
    'USE',
    'TEMPLATE'
];

foreach ($arParams as $key => $parameter) {
    if (!StringHelper::startsWith($key, $sendPrefix))
        continue;

    $key = StringHelper::cut($key, $sendPrefixLength);

    if (ArrayHelper::isIn($key, $sendExcluded))
        continue;

    $sendParameters[$key] = $parameter;
}

$arResult['SEND']['PARAMETERS'] = ArrayHelper::merge($arResult['SEND']['PARAMETERS'], $sendParameters);