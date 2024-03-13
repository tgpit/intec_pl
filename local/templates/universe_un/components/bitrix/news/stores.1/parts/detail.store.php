<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
    <?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use Bitrix\Main\Data\Cache;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$arDetail = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'DETAIL_STORE_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arDetail['TEMPLATE']))
    $arDetail['SHOW'] = false;

if ($arDetail['SHOW']) {
    $sPrefix = 'DETAIL_STORE_';
    $arDetail['TEMPLATE'] = 'store.'.$arDetail['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE')
            continue;

        $arDetail['PARAMETERS'][$sKey] = $mValue;
    }

    unset($sKey, $sValue);

    $arDetail['PARAMETERS'] = ArrayHelper::merge($arDetail['PARAMETERS'], [
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'STORE' => $arElement['ID'],
        'PATH_TO_LISTSTORES' => $arResult['PATH_TO_LISTSTORES'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'MAP_TYPE' => $arParams['MAP_VENDOR'] === 'yandex' ? 0 : 1,
        'MAP_ID' => $arParams['MAP_ID'],

        'SOCIAL_SERVICES_VK' => $arParams['SOCIAL_SERVICES_VK'],
        'SOCIAL_SERVICES_FACEBOOK' => $arParams['SOCIAL_SERVICES_FACEBOOK'],
        'SOCIAL_SERVICES_INSTAGRAM' => $arParams['SOCIAL_SERVICES_INSTAGRAM'],
        'SOCIAL_SERVICES_TWITTER' => $arParams['SOCIAL_SERVICES_TWITTER'],
        'SOCIAL_SERVICES_SKYPE' => $arParams['SOCIAL_SERVICES_SKYPE'],
        'SOCIAL_SERVICES_YOUTUBE' => $arParams['SOCIAL_SERVICES_YOUTUBE'],
        'SOCIAL_SERVICES_OK' => $arParams['SOCIAL_SERVICES_OK'],

        'FORM_ID' => $arParams['FORM_ID'],
        'FORM_TEMPLATE' => $arParams['FORM_TEMPLATE'],
        'FORM_TITLE' => $arParams['FORM_TITLE'],
        'CONSENT' => $arParams['CONSENT'],
    ]);
}