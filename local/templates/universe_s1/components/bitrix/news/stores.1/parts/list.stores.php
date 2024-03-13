<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var $sPathToElement
 */

$arList = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'LIST_STORES_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arList['TEMPLATE']))
    $arList['SHOW'] = false;

if ($arList['SHOW']) {
    $sPrefix = 'LIST_STORES_';
    $arList['TEMPLATE'] = 'stores.'.$arList['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arList['PARAMETERS'][$sKey] = $mValue;
    }

    unset($sKey, $sValue);

    $arList['PARAMETERS'] = ArrayHelper::merge($arList['PARAMETERS'], [
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'PHONE' => $arParams['PHONE_SHOW'],
        'SCHEDULE' => $arParams['SCHEDULE_SHOW'],
        'TITLE' => $arParams['TITLE'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'PATH_TO_ELEMENT' => $sPathToElement,
        'MAP_TYPE' => $arParams['MAP_VENDOR'] === 'yandex' ? 0 : 1,
        'MAP_ID' => $arParams['MAP_ID'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
    ]);
}
