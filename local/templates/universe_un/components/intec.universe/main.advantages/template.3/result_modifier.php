<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'INDENT_USE' => 'N',
    'BACKGROUND_SIZE' => 'cover',
    'LINK_PROPERTY_USE' => 'N',
    'LINK_PROPERTY' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'BACKGROUND' => [
        'SIZE' => ArrayHelper::fromRange([
            'cover',
            'contain'
        ], $arParams['BACKGROUND_SIZE'])
    ],
    'INDENT' => [
        'USE' => $arParams['INDENT_USE'] === 'Y'
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y'
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