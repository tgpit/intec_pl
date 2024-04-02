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

$sPrefix = 'LIST_';
$sLength = StringHelper::length($sPrefix);

$arParameters = [
    'TEMPLATE' => ArrayHelper::getValue($arParams, $sPrefix.'TEMPLATE'),
    'PARAMETERS' => []
];

if (!empty($arParameters['TEMPLATE'])) {
    if (!Type::isArray($arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'] = [];

    $arParams['LIST_PROPERTY_CODE'] = array_filter($arParams['LIST_PROPERTY_CODE']);

    if (!empty($arParams['PROPERTY_POSITION']) && !ArrayHelper::isIn($arParams['PROPERTY_POSITION'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_POSITION'];

    if (!empty($arParams['PROPERTY_PHONE']) && !ArrayHelper::isIn($arParams['PROPERTY_PHONE'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_PHONE'];

    if (!empty($arParams['PROPERTY_EMAIL']) && !ArrayHelper::isIn($arParams['PROPERTY_EMAIL'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_EMAIL'];

    if (!empty($arParams['PROPERTY_SOCIAL_VK']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_VK'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_VK'];

    if (!empty($arParams['PROPERTY_SOCIAL_FB']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_FB'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_FB'];

    if (!empty($arParams['PROPERTY_SOCIAL_INST']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_INST'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_INST'];

    if (!empty($arParams['PROPERTY_SOCIAL_TW']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_TW'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_TW'];

    if (!empty($arParams['PROPERTY_SOCIAL_SKYPE']) && !ArrayHelper::isIn($arParams['PROPERTY_SOCIAL_SKYPE'], $arParams['LIST_PROPERTY_CODE']))
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SOCIAL_SKYPE'];

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

    $APPLICATION->IncludeComponent(
        'bitrix:news.list',
        $arParameters['TEMPLATE'],
        ArrayHelper::merge($arParameters['PARAMETERS'], [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'NEWS_COUNT' => $arParams['NEWS_COUNT'],
            'SORT_BY1' => $arParams['SORT_BY1'],
            'SORT_ORDER1' => $arParams['SORT_ORDER1'],
            'SORT_BY2' => $arParams['SORT_BY2'],
            'SORT_ORDER2' => $arParams['SORT_ORDER2'],
            'FIELD_CODE' => $arParams['LIST_FIELD_CODE'],
            'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
            'CHECK_DATES' => $arParams['CHECK_DATES'],
            'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
            'AJAX_MODE' => $arParams['AJAX_MODE'],
            'AJAX_OPTION_JUMP' => $arParams['AJAX_OPTION_JUMP'],
            'AJAX_OPTION_STYLE' => $arParams['AJAX_OPTION_STYLE'],
            'AJAX_OPTION_HISTORY' => $arParams['AJAX_OPTION_HISTORY'],
            'AJAX_OPTION_ADDITIONAL' => $arParams['AJAX_OPTION_ADDITIONAL'],
            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
            'CACHE_TIME' => $arParams['CACHE_TIME'],
            'CACHE_FILTER' => $arParams['CACHE_FILTER'],
            'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
            'ACTIVE_DATE_FORMAT' => $arParams['LIST_ACTIVE_DATE_FORMAT'],
            'SET_TITLE' => $arParams['SET_TITLE'],
            'SET_BROWSER_TITLE' => $arParams['SET_TITLE'],
            'SET_META_KEYWORDS' => 'Y',
            'SET_META_DESCRIPTION' => 'Y',
            'SET_LAST_MODIFIED' => $arParams['SET_LAST_MODIFIED'],
            'INCLUDE_IBLOCK_INTO_CHAIN' => $arParams['INCLUDE_IBLOCK_INTO_CHAIN'],
            'ADD_SECTIONS_CHAIN' => $arParams['ADD_SECTIONS_CHAIN'],
            'HIDE_LINK_WHEN_NO_DETAIL' => $arParams['HIDE_LINK_WHEN_NO_DETAIL'],
            'STRICT_SECTION_CHECK' => $arParams['STRICT_SECTION_CHECK'],
            'PAGER_TEMPLATE' => $arParams['PAGER_TEMPLATE'],
            'DISPLAY_TOP_PAGER' => $arParams['DISPLAY_TOP_PAGER'],
            'DISPLAY_BOTTOM_PAGER' => $arParams['DISPLAY_BOTTOM_PAGER'],
            'PAGER_TITLE' => $arParams['PAGER_TITLE'],
            'PAGER_SHOW_ALWAYS' => $arParams['PAGER_SHOW_ALWAYS'],
            'PAGER_DESC_NUMBERING' => $arParams['PAGER_DESC_NUMBERING'],
            'PAGER_DESC_NUMBERING_CACHE_TIME' => $arParams['PAGER_DESC_NUMBERING_CACHE_TIME'],
            'PAGER_SHOW_ALL' => $arParams['PAGER_SHOW_ALL'],
            'PAGER_BASE_LINK_ENABLE' => $arParams['PAGER_BASE_LINK_ENABLE'],
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