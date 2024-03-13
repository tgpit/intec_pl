<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
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
    'COLUMNS' => 3,
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PREVIEW_SHOW' => 'N',
    'LINK_ALL_SHOW' => 'N',
    'LINK_ALL_TEXT' => null
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([3, 2, 4], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'PREVIEW' => [
        'SHOW' => $arParams['PREVIEW_SHOW'] === 'Y'
    ]
]);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PREVIEW' => [
            'SHOW' => false,
            'VALUE' => null
        ]
    ];

    if ($arVisual['PREVIEW']['SHOW']) {
        if (!empty($arItem['PREVIEW_TEXT']))
            $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['PREVIEW_TEXT'];
        else if (!empty($arItem['DETAIL_TEXT']))
            $arItem['DATA']['PREVIEW']['VALUE'] = $arItem['DETAIL_TEXT'];

        if (!empty($arItem['DATA']['PREVIEW']['VALUE']))
            $arItem['DATA']['PREVIEW']['SHOW'] = $arVisual['PREVIEW']['SHOW'];
    }
}

unset($arItem);

if (empty($arParams['LIST_PAGE_URL'])) {
    $arParams['LIST_PAGE_URL'] = ArrayHelper::getValue(
        ArrayHelper::getFirstValue($arResult['ITEMS']),
        'LIST_PAGE_URL'
    );
}

$arResult['FOOTER_BLOCK'] = [
    'SHOW' => $arParams['LINK_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
    'URL' => StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR
    ]),
    'TEXT' => trim($arParams['LINK_ALL_TEXT'])
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);