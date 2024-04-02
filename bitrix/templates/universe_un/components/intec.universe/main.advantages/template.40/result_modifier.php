<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

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
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'LINK_PROPERTY_USE' => 'N',
    'LINK_PROPERTY' => null,
    'SLIDER_NAV' => 'Y',
    'SLIDER_LOOP' => 'N',
    'SLIDER_AUTOPLAY' => 'N',
    'SLIDER_AUTOPLAY_TIME' => 10000,
    'SLIDER_AUTOPLAY_HOVER' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
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

if (!$arVisual['SLIDER']['AUTO']['TIME'])
    $arVisual['SLIDER']['AUTO']['TIME'] = 10000;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'LINK' => $arItem['DETAIL_PAGE_URL']
    ];

    if ($arParams['LINK_PROPERTY_USE'] === 'Y' && !empty($arParams['LINK_PROPERTY'])) {
        $sLink = ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['LINK_PROPERTY'],
            'VALUE'
        ]);

        if (!empty($sLink))
            $arItem['DATA']['LINK'] = $sLink;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);
