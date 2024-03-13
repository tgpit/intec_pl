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
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'FOOTER_BLOCK_SHOW' => 'N',
    'FOOTER_BUTTON_SHOW' => 'N',
    'FOOTER_BUTTON_TEXT' => null,
], $arParams);

if (!defined('EDITOR')) {
    if ($arResult['NAVIGATION']['USE'] && $arResult['NAVIGATION']['MODE'] === 'ajax')
        Core::setAlias('@intec/template', $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes');
}

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ]
];

$arParams['LIST_PAGE_URL'] = StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
    'SITE_DIR' => SITE_DIR
]);

$arResult['BLOCKS']['LINK_ALL'] = [
    'SHOW' => $arParams['LINK_ALL_SHOW'] === 'Y' && !empty($arParams['LIST_PAGE_URL']),
    'BUTTON' => [
        'TEXT' => !empty($arParams['LINK_ALL_BUTTON_TEXT']) ? trim($arParams['LINK_ALL_BUTTON_TEXT']) : null,
        'URL' => $arParams['LIST_PAGE_URL']
    ]
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);