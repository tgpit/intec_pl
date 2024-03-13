<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Type;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'LINK_USE' => 'N',
    'SLIDER_NAV' => 'N',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTOPLAY' => 'N',
    'SLIDER_AUTOPLAY_TIME' => 10000,
    'SLIDER_AUTOPLAY_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'SECTION' => [
        'SHOW' => $arParams['SECTION_SHOW'] === 'Y'
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'SLIDER' => [
        'NAV' => [
            'SHOW' => $arParams['SLIDER_NAV'] === 'Y'
        ],
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTOPLAY'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTOPLAY_TIME']),
            'HOVER' => $arParams['SLIDER_AUTOPLAY_HOVER'] === 'Y'
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    if (isset($arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']])) {
        $arItem['SECTION_NAME'] = $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['NAME'];
        $arItem['SECTION_PAGE_URL'] = $arResult['SECTIONS'][$arItem['IBLOCK_SECTION_ID']]['SECTION_PAGE_URL'];
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);
