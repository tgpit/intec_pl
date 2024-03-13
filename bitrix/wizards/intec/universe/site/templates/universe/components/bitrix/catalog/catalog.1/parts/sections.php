<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sLevel
 */

$arSections = [
    'SHOW' => true,
    'TEMPLATE' => ArrayHelper::getValue($arParams, 'SECTIONS_'.$sLevel.'_TEMPLATE'),
    'PARAMETERS' => []
];

if (empty($arSections['TEMPLATE']))
    $arSections['SHOW'] = false;

if ($arSections['SHOW']) {
    $sPrefix = 'SECTIONS_'.$sLevel.'_';
    $arSections['TEMPLATE'] = 'catalog.'.$arSections['TEMPLATE'];

    foreach ($arParams as $sKey => $mValue) {
        if (!StringHelper::startsWith($sKey, $sPrefix))
            continue;

        $sKey = StringHelper::cut(
            $sKey,
            StringHelper::length($sPrefix)
        );

        if ($sKey === 'TEMPLATE' || StringHelper::startsWith($sKey, 'EXTENDING_'))
            continue;

        $arSections['PARAMETERS'][$sKey] = $mValue;
    }

    foreach ($arResult['PARAMETERS']['COMMON'] as $sProperty)
        $arSections['PARAMETERS'][$sProperty] = ArrayHelper::getValue($arParams, $sProperty);

    $arSections['PARAMETERS'] = ArrayHelper::merge($arSections['PARAMETERS'], [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_ID' => !empty($arResult['VARIABLES']['SECTION_ID']) ? $arResult['VARIABLES']['SECTION_ID'] : null,
        'SECTION_CODE' => !empty($arResult['VARIABLES']['SECTION_CODE']) ? $arResult['VARIABLES']['SECTION_CODE'] : null,
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
        'COUNT_ELEMENTS' => $arParams['SECTION_COUNT_ELEMENTS'],
        'TOP_DEPTH' => $arParams['SECTION_TOP_DEPTH'],
        'SECTION_URL' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['section'],
        'ADD_SECTIONS_CHAIN' => (isset($arParams['ADD_SECTIONS_CHAIN']) ? $arParams['ADD_SECTIONS_CHAIN'] : ''),
        'SECTION_USER_FIELDS' => ['UF_*'],
        'WIDE' => 'Y'
    ]);
}

if ($sLevel === 'CHILDREN') {
    $arSectionsExtending = [
        'SHOW' => $bSeo,
        'PROPERTY' => ArrayHelper::getValue($arParams, 'SECTIONS_'.$sLevel.'_EXTENDING_PROPERTY'),
        'TEMPLATE' => ArrayHelper::getValue($arParams, 'SECTIONS_'.$sLevel.'_EXTENDING_TEMPLATE'),
        'TITLE' => ArrayHelper::getValue($arParams, 'SECTIONS_'.$sLevel.'_EXTENDING_TITLE'),
        'PARAMETERS' => []
    ];

    if ($arParams['SECTIONS_'.$sLevel.'_EXTENDING_USE'] !== 'Y')
        $arSectionsExtending['SHOW'] = false;

    if (empty($arSectionsExtending['TEMPLATE']))
        $arSectionsExtending['SHOW'] = false;

    if ($arSectionsExtending['SHOW']) {
        $sPrefix = 'SECTIONS_'.$sLevel.'_EXTENDING_';
        $arSectionsExtending['TEMPLATE'] = 'products.small.'.$arSectionsExtending['TEMPLATE'];

        foreach ($arParams as $sKey => $mValue) {
            if (!StringHelper::startsWith($sKey, $sPrefix))
                continue;

            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arSectionsExtending['PARAMETERS'][$sKey] = $mValue;
        }

        $arSectionsExtending['PARAMETERS'] = ArrayHelper::merge($arSectionsExtending['PARAMETERS'], [
            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
            'SET_TITLE' => 'N',
            'SECTION_USER_FIELDS' => array(),
            'SHOW_ALL_WO_SECTION' => 'Y',
            'PRICE_CODE' => $arParams['PRICE_CODE'],
            'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'BASKET_URL' => $arParams['BASKET_URL'],
            'CONSENT_URL' => $arParams['CONSENT_URL'],
            'COMPARE_NAME' => $arParams['COMPARE_NAME'],
            'USE_COMPARE' => $arParams['USE_COMPARE'],
            'WIDE' => 'Y',
            'COMPATIBLE_MODE' => $arParams['COMPATIBLE_MODE']
        ]);
    }

    $arArticlesExtending = [
        'SHOW' => $bSeo,
        'PROPERTY' => ArrayHelper::getValue($arParams, 'SECTIONS_ARTICLES_EXTENDING_PROPERTY'),
        'TEMPLATE' => ArrayHelper::getValue($arParams, 'SECTIONS_ARTICLES_EXTENDING_TEMPLATE'),
        'TITLE' => ArrayHelper::getValue($arParams, 'SECTIONS_ARTICLES_EXTENDING_TITLE'),
        'QUANTITY' => ArrayHelper::getValue($arParams, 'SECTIONS_ARTICLES_EXTENDING_QUANTITY'),
        'PARAMETERS' => []
    ];

    if (empty($arArticlesExtending['TEMPLATE']))
        $arArticlesExtending['SHOW'] = false;

    if ($arArticlesExtending['SHOW']) {
        $sPrefix = 'SECTIONS_ARTICLES_EXTENDING_';
        $arArticlesExtending['TEMPLATE'] = 'news.'.$arArticlesExtending['TEMPLATE'];



        foreach ($arParams as $sKey => $mValue) {
            if (!StringHelper::startsWith($sKey, $sPrefix))
                continue;

            $sKey = StringHelper::cut(
                $sKey,
                StringHelper::length($sPrefix)
            );

            if ($sKey === 'TEMPLATE')
                continue;

            $arArticlesExtending['PARAMETERS'][$sKey] = $mValue;
        }

        $arArticlesExtending['PARAMETERS'] = ArrayHelper::merge($arArticlesExtending['PARAMETERS'], [
            'SECTION_USER_FIELDS' => array(),
            'SET_TITLE' => 'N',
            'ADD_SECTIONS_CHAIN' => 'N',
            'SET_BROWSER_TITLE' => 'N',
            'INCLUDE_IBLOCK_INTO_CHAIN' => 'N',
            'CURRENCY_ID' => $arParams['CURRENCY_ID'],
            'CONSENT_URL' => $arParams['CONSENT_URL'],
            'COMPARE_NAME' => $arParams['COMPARE_NAME'],
            'USE_COMPARE' => $arParams['USE_COMPARE'],
            'WIDE' => 'Y'
        ]);
    }
}