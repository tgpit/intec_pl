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
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'DETAIL_CONTACT_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arDetail['TEMPLATE']))
    $arDetail['SHOW'] = false;

if ($arDetail['SHOW']) {
    $sPrefix = 'DETAIL_CONTACT_';
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
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_TYPE_SERVICES' => $arParams['IBLOCK_TYPE_SERVICES'],
        'IBLOCK_ID_SERVICES' => $arParams['IBLOCK_ID_SERVICES'],
        'IBLOCK_TYPE_REVIEWS' => $arParams['IBLOCK_TYPE_REVIEWS'],
        'IBLOCK_ID_REVIEWS' => $arParams['IBLOCK_ID_REVIEWS'],
        'FIELD_CODE' => $arParams['DETAIL_FIELD_CODE'],
        'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
        'DETAIL_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['detail'],
        'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
        'IBLOCK_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['news'],
        'META_KEYWORDS' => $arParams['META_KEYWORDS'],
        'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
        'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
        'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
        'DISPLAY_PANEL' => $arParams['DISPLAY_PANEL'],
        'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
        'SET_TITLE' => $arParams['SET_TITLE'],
        'MESSAGE_404' => $arParams['MESSAGE_404'],
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'FILE_404' => $arParams['FILE_404'],
        'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
        'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
        'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'CACHE_FILTER' => $arParams['CACHE_FILTER'],
        'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
        'GROUP_PERMISSIONS' => $arParams['GROUP_PERMISSIONS'],
        'DISPLAY_TOP_PAGER' => $arParams['DETAIL_DISPLAY_TOP_PAGER'],
        'DISPLAY_BOTTOM_PAGER' => $arParams['DETAIL_DISPLAY_BOTTOM_PAGER'],
        'PAGER_TITLE' => $arParams['DETAIL_PAGER_TITLE'],
        'PAGER_SHOW_ALWAYS' => 'N',
        'PAGER_TEMPLATE' => $arParams['DETAIL_PAGER_TEMPLATE'],
        'PAGER_SHOW_ALL' => $arParams['DETAIL_PAGER_SHOW_ALL'],
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
        'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
        'ADD_ELEMENT_CHAIN' => (isset($arParams['ADD_ELEMENT_CHAIN']) ? $arParams['ADD_ELEMENT_CHAIN'] : ''),

        'MAP_VENDOR' => $arParams['MAP_VENDOR'],
        'PROPERTY_MAP' => $arParams['PROPERTY_MAP'],
        'API_KEY_MAP' => $arParams['API_KEY_MAP'],
        'MAP_SHOW' => $arParams['MAP_SHOW'],

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