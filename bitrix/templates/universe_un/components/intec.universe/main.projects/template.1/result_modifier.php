<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 4,
    'ALIGNMENT' => 'center',
    'LINK_USE' => 'Y',
    'LINK_BLANK' => 'N',
    'TABS_USE' => 'N',
    'TABS_POSITION' => 'center',
    'TABS_ELEMENTS' => null,
    'SLIDER_USE' => 'Y',
    'SLIDER_LOOP' => 'N',
    'SLIDER_DOTS' => 'Y',
    'SLIDER_NAVIGATION' => 'Y',
    'SLIDER_AUTO_USE' => 'N',
    'SLIDER_AUTO_PAUSE' => 'N',
    'SLIDER_AUTO_SPEED' => 500,
    'SLIDER_AUTO_TIME' => 5000,
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'ALIGNMENT' => ArrayHelper::fromRange([
        'center',
        'left',
        'right'
    ], $arParams['ALIGNMENT']),
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS']),
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'DOTS' => $arParams['SLIDER_DOTS'] === 'Y',
        'NAVIGATION' => $arParams['SLIDER_NAVIGATION'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_USE'] === 'Y',
            'SPEED' => Type::toInteger($arParams['SLIDER_AUTO_SPEED']),
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_TIME']),
            'PAUSE' => $arParams['SLIDER_AUTO_PAUSE'] === 'Y'
        ]
    ],
    'TABS' => [
        'USE' => $arParams['TABS_USE'] === 'Y',
        'POSITION' => ArrayHelper::fromRange([
            'center',
            'left',
            'right'
        ], $arParams['TABS_POSITION']),
        'ELEMENTS' => Type::toInteger($arParams['TABS_ELEMENTS'])
    ],
    'BUTTON_ALL' => [
        'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y',
        'TEXT' => $arParams['BUTTON_ALL_TEXT'],
        'LINK' => StringHelper::replaceMacros(ArrayHelper::getValue($arParams, 'LIST_PAGE_URL'), [
            'SITE_DIR' => SITE_DIR
        ])
    ]
]);

$arVisual = &$arResult['VISUAL'];

if ($arVisual['BUTTON_ALL']['SHOW'] && empty($arVisual['BUTTON_ALL']['LINK'])) {
    $arIblock = Arrays::fromDBResult(CIBlock::GetByID($arParams['IBLOCK_ID']))->getFirst();
    $arMacros = [
        'SITE_DIR' => SITE_DIR,
        'SERVER_NAME' => $_SERVER['SERVER_NAME'],
        'IBLOCK_TYPE_ID' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'IBLOCK_CODE' => $arIblock['CODE'],
        'IBLOCK_EXTERNAL_ID' => !empty($arIblock['EXTERNAL_ID']) ? $arIblock['EXTERNAL_ID'] : $arIblock['XML_ID']
    ];
    $arItem = ArrayHelper::getFirstValue($arResult['ITEMS']);
    $arVisual['BUTTON_ALL']['LINK'] = StringHelper::replaceMacros($arItem['LIST_PAGE_URL'], $arMacros);

    unset($arIblock, $arMacros, $arItem);
}

if ($arVisual['TABS']['ELEMENTS'] <= 0)
    $arVisual['TABS']['ELEMENTS'] = null;

if ($arVisual['SLIDER']['AUTO']['SPEED'] < 100)
    $arVisual['SLIDER']['AUTO']['SPEED'] = 100;

if ($arVisual['SLIDER']['AUTO']['TIME'] < 100)
    $arVisual['SLIDER']['AUTO']['TIME'] = 100;