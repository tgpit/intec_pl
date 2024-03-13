<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'DETAIL_';
$sLength = StringHelper::length($sPrefix);

$arParameters = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'DETAIL_TEMPLATE'),
    'PARAMETERS' => []
];

if (!empty($arParameters['TEMPLATE'])) {
    if (!Type::isArray($arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'] = [];

    $arParams['DETAIL_PROPERTY_CODE'] = array_filter($arParams['DETAIL_PROPERTY_CODE']);

    if (!empty($arParams['PROPERTY_POSITION']) && !ArrayHelper::isIn($arParams['PROPERTY_POSITION'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_POSITION'];

    if (!empty($arParams['PROPERTY_PHONE']) && !ArrayHelper::isIn($arParams['PROPERTY_PHONE'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_PHONE'];

    if (!empty($arParams['PROPERTY_EMAIL']) && !ArrayHelper::isIn($arParams['PROPERTY_EMAIL'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_EMAIL'];

    if (!empty($arParams['PROPERTY_SOCIAL_VK']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_VK'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_VK'];

    if (!empty($arParams['PROPERTY_SOCIAL_FB']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_FB'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_FB'];

    if (!empty($arParams['PROPERTY_SOCIAL_INST']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_INST'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_INST'];

    if (!empty($arParams['PROPERTY_SOCIAL_TW']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_TW'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_TW'];

    if (!empty($arParams['PROPERTY_SOCIAL_SKYPE']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_SKYPE'], $arParams['DETAIL_PROPERTY_CODE']))
        $arParams['DETAIL_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_SKYPE'];
    
    $arParameters['TEMPLATE'] = 'staff.'.$arParameters['TEMPLATE'];

    foreach ($arParams as $key => $sValue) {
        if (StringHelper::startsWith($key, $sPrefix)) {
            $key = StringHelper::cut($key, $sLength);

            if ($key === 'TEMPLATE')
                continue;

            $arParameters['PARAMETERS'][$key] = $sValue;
        }
    }

    unset($key, $sValue);

    $arFields = [
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE'
    ];

    foreach ($arFields as $sField) {
        if (!ArrayHelper::isIn($sField, $arParams['DETAIL_FIELD_CODE']))
            $arParams['DETAIL_FIELD_CODE'][] = $sField;
    }

    unset($arFields, $sField);

    $APPLICATION->IncludeComponent(
        'bitrix:news.detail',
        $arParameters['TEMPLATE'],
        ArrayHelper::merge($arParameters['PARAMETERS'], [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'ELEMENT_ID' => $arResult['VARIABLES']['ELEMENT_ID'],
            'ELEMENT_CODE' => $arResult['VARIABLES']['ELEMENT_CODE'],
            'CHECK_DATES' => $arParams['CHECK_DATES'],
            'FIELD_CODE' => $arParams['DETAIL_FIELD_CODE'],
            'PROPERTY_CODE' => $arParams['DETAIL_PROPERTY_CODE'],
            'IBLOCK_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['news'],
            'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'SET_TITLE' => $arParams['SET_TITLE'],
            'SET_CANONICAL_URL' => $arParams['DETAIL_SET_CANONICAL_URL'],
            'SET_BROWSER_TITLE' => $arParams['SET_BROWSER_TITLE'],
            'BROWSER_TITLE' => $arParams['BROWSER_TITLE'],
            'SET_META_KEYWORDS' => $arParams['SET_META_KEYWORDS'],
            'META_KEYWORDS' => $arParams['META_KEYWORDS'],
            'SET_META_DESCRIPTION' => $arParams['SET_META_DESCRIPTION'],
            'META_DESCRIPTION' => $arParams['META_DESCRIPTION'],
            'SET_LAST_MODIFIED' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
            'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
            'ADD_ELEMENT_CHAIN' => $arParams['ADD_ELEMENT_CHAIN'],
            'ACTIVE_DATE_FORMAT' => $arParams['DETAIL_ACTIVE_DATE_FORMAT'],
            'USE_PERMISSIONS' => $arParams['USE_PERMISSIONS'],
            'STRICT_SECTION_CHECK' => 'N',
            'DISPLAY_DATE' => 'N',
            'DISPLAY_NAME' => 'N',
            'DISPLAY_PICTURE' => 'N',
            'DISPLAY_PREVIEW_TEXT' => 'N',
            'USE_SHARE' => 'N',
            'PAGER_TEMPLATE' => '.default',
            'DISPLAY_TOP_PAGER' => 'N',
            'DISPLAY_BOTTOM_PAGER' => 'N',
            'PAGER_TITLE' => '',
            'PAGER_SHOW_ALL' => 'N',
            'PAGER_BASE_LINK_ENABLE' => 'N',
            'SET_STATUS_404' => $arParams['SET_STATUS_404'],
            'SHOW_404' => $arParams['SHOW_404'],
            'MESSAGE_404' => $arParams['MESSAGE_404'],

            'SETTINGS_USE' => 'N',
            'FORM_ASK_TEMPLATE' => $arParams['FORM_ASK_TEMPLATE'],
            'FORM_ASK_ID' => $arParams['FORM_ASK_USE'] === 'Y' && !empty($arParams['FORM_ASK_TEMPLATE']) ? $arParams['FORM_ASK_ID'] : null,
            'FORM_ASK_FIELD' => $arParams['FORM_ASK_FIELD'],
            'FORM_ASK_CONSENT_URL' => $arParams['FORM_ASK_CONSENT_URL'],
            'PROPERTY_POSITION' => $arParams['PROPERTY_POSITION'],
            'PROPERTY_PHONE' => $arParams['PROPERTY_PHONE'],
            'PROPERTY_EMAIL' => $arParams['PROPERTY_EMAIL'],
            'PROPERTY_SOCIAL_VK' => $arParams['PROPERTY_SOCIAL_VK'],
            'PROPERTY_SOCIAL_FB' => $arParams['PROPERTY_SOCIAL_FB'],
            'PROPERTY_SOCIAL_INST' => $arParams['PROPERTY_SOCIAL_INST'],
            'PROPERTY_SOCIAL_TW' => $arParams['PROPERTY_SOCIAL_TW'],
            'PROPERTY_SOCIAL_SKYPE' => $arParams['PROPERTY_SOCIAL_SKYPE']
        ]),
        $component
    );
}

unset($sPrefix, $sLength, $arParameters);