<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();


use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */


$sPrefix = 'TIMER_';

$iLength = StringHelper::length($sPrefix);

$arTimerProperties = [];
$arExcluded = [
    'SHOW',
    'NAME'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arTimerProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arTimerProperties = ArrayHelper::merge([
    'ELEMENT_ID' => $arResult['ID'],
    'IBLOCK_ID' => $arResult['IBLOCK_ID'],
    'IBLOCK_TYPE' => $arResult['IBLOCK_TYPE_ID'],
    'QUANTITY' => $arResult['CATALOG_QUANTITY'],
    'ITEM_NAME' => $arResult['NAME'],
    'AJAX_MODE' => 'N'
], $arTimerProperties);

$arTimerProperties = [
    'component' => 'intec.universe:product.timer',
    'template' => 'template.1',
    'parameters' => $arTimerProperties
];

$arResult['TIMER']['PROPERTIES'] = $arTimerProperties;

unset($arTimerProperties);