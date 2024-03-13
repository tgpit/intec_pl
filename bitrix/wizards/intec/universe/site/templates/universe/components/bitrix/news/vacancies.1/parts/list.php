<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
* @var array $arParams
* @var array $arResult
*/

$arList = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'LIST_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arList['TEMPLATE']))
    $arList['SHOW'] = false;

if ($arList['SHOW']) {
    $sPrefix = 'LIST_';
    $arList['TEMPLATE'] = 'vacancies.'.$arList['TEMPLATE'];

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

    if(!empty($arParams['PROPERTY_CITY']) && !ArrayHelper::isIn($arParams['PROPERTY_CITY'], $arParams['LIST_PROPERTY_CODE'])){
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_CITY'];
    }
    if(!empty($arParams['PROPERTY_SKILL']) && !ArrayHelper::isIn($arParams['PROPERTY_SKILL'], $arParams['LIST_PROPERTY_CODE'])){
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_SKILL'];
    }
    if(!empty($arParams['PROPERTY_TYPE_EMPLOYMENT']) && !ArrayHelper::isIn($arParams['PROPERTY_TYPE_EMPLOYMENT'], $arParams['LIST_PROPERTY_CODE'])){
        $arParams['LIST_PROPERTY_CODE'][] = $arParams['PROPERTY_TYPE_EMPLOYMENT'];
    }

    $arList['PARAMETERS'] = ArrayHelper::merge($arList['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'NEWS_COUNT' => $arParams['NEWS_COUNT'],
        'SORT_BY1' => $arParams['SORT_BY1'],
        'SORT_ORDER1' => $arParams['SORT_ORDER1'],
        'SORT_BY2' => $arParams['SORT_BY2'],
        'SORT_ORDER2' => $arParams['SORT_ORDER2'],
        'PROPERTY_CODE' => $arParams['LIST_PROPERTY_CODE'],
        'CHECK_DATES' => $arParams['CHECK_DATES'],
        'DETAIL_URL' => $arResult['FOLDER'] . $arResult['URL_TEMPLATES']['detail'],
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
        'SET_STATUS_404' => $arParams['SET_STATUS_404'],
        'SHOW_404' => $arParams['SHOW_404'],
        'MESSAGE_404' => $arParams['MESSAGE_404'],
        'PROPERTY_CITY' => $arParams['PROPERTY_CITY'],
        'PROPERTY_SKILL' => $arParams['PROPERTY_SKILL'],
        'PROPERTY_TYPE_EMPLOYMENT' => $arParams['PROPERTY_TYPE_EMPLOYMENT'],
        'PROPERTY_SALARY' => $arParams['PROPERTY_SALARY'],
        'CONSENT_URL' => $arParams['CONSENT_URL']
    ]);
}
