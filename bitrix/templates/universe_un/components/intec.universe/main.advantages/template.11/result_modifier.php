<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PREVIEW_SHOW' => 'N',
    'VIEW' => 'number',
    'COLUMNS' => 2,
    'BACKGROUND_PATH' => null,
    'LINK_PROPERTY_USE' => 'N',
    'LINK_PROPERTY' => null
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
    ],
    'VIEW' => ArrayHelper::fromRange([
        'number',
        'icon',
        'empty'
    ], $arParams['VIEW']),
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4, 5], $arParams['COLUMNS']),
    'BACKGROUND' => [
        'PATH' => StringHelper::replaceMacros(
            $arParams['BACKGROUND_PATH'],
            $arMacros
        )
    ]
];

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