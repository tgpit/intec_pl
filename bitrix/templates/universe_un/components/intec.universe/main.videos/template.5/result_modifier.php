<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
    'BUTTON_ALL_POSITION' => 'center',
    'BUTTON_ALL_SHOW' => 'N',
    'BUTTON_ALL_TEXT' => null
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
]);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$arButtonAll = [
    'SHOW' => $arParams['BUTTON_ALL_SHOW'] === 'Y',
    'POSITION' => ArrayHelper::fromRange([
        'left',
        'center',
        'right'
    ], $arParams['BUTTON_ALL_POSITION']),
    'TEXT' => $arParams['BUTTON_ALL_TEXT'],
    'LINK' => null
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arButtonAll['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

if (empty($arButtonAll['TEXT']) || empty($arButtonAll['LINK']))
    $arButtonAll['SHOW'] = false;

if (!$arButtonAll['SHOW'])
    $arButtonAll['SHOW'] = false;

$arResult['BLOCKS']['BUTTON_ALL'] = $arButtonAll;

unset($arFooter, $arMacros);