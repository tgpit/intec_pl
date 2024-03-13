<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
    'COLUMNS' => 3,
    'NAME_SHOW' => 'N',
    'SLIDER_USE' => 'N',
    'SLIDER_AUTO_PLAY_USE' => 'N',
    'SLIDER_AUTO_PLAY_TIME' => 10000,
    'SLIDER_AUTO_PLAY_SPEED' => 500,
    'SLIDER_AUTO_PLAY_HOVER_PAUSE' => 'N',
    'CONTENT_POSITION' => 'left',
    'FOOTER_SHOW' => 'N',
    'FOOTER_POSITION' => 'top',
    'FOOTER_ALIGN' => 'center',
    'FOOTER_BUTTON_LINK' => null,
    'FOOTER_BUTTON_TEXT' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 1, 2, 4, 5], $arParams['COLUMNS']),
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y'
    ],
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'AUTO' => [
            'USE' => $arParams['SLIDER_AUTO_PLAY_USE'] === 'Y',
            'TIME' => Type::toInteger($arParams['SLIDER_AUTO_PLAY_TIME']),
            'SPEED' => Type::toInteger($arParams['SLIDER_AUTO_PLAY_SPEED']),
            'PAUSE' => $arParams['SLIDER_AUTO_PLAY_HOVER_PAUSE'] === 'Y'
        ]
    ]
];

$arResult['BLOCKS']['CONTENT'] = [
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['CONTENT_POSITION'])
];

$arParams['FOOTER_BUTTON_LINK'] = StringHelper::replaceMacros($arParams['FOOTER_BUTTON_LINK'], [
    'SITE_DIR' => SITE_DIR
]);

$arResult['BLOCKS']['FOOTER'] = [
    'SHOW' => $arParams['FOOTER_SHOW'] === 'Y' && !empty($arParams['FOOTER_BUTTON_LINK']),
    'POSITION' => ArrayHelper::fromRange(['top', 'bottom'], $arParams['FOOTER_POSITION']),
    'ALIGN' => ArrayHelper::fromRange(['center', 'left', 'right'], $arParams['FOOTER_ALIGN']),
    'BUTTON' => [
        'TEXT' => !empty($arParams['FOOTER_BUTTON_TEXT']) ? $arParams['FOOTER_BUTTON_TEXT'] : null,
        'LINK' => !empty($arParams['FOOTER_BUTTON_LINK']) ? $arParams['FOOTER_BUTTON_LINK'] : null
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);