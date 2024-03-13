<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 */

Core::setAlias(
    '@intec/template',
    $_SERVER['DOCUMENT_ROOT'].SITE_TEMPLATE_PATH.'/classes'
);

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'TIMER_TITLE_SHOW' => 'N',
    'TIMER_TITLE_ENTER' => 'N',
    'TIMER_TITLE_VALUE' => null,
    'TIMER_QUANTITY_OVER' => 'Y',
    'RANDOMIZE_ID' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'SALE' => [
        'SHOW' => $arParams['SALE_SHOW'] === 'Y' && !empty($arParams['SALE_VALUE']),
        'VALUE' => $arParams['SALE_VALUE']
    ],
    'CASES' => [
        'DAYS' => 'DAYS',
        'HOURS' => 'HOURS',
        'MINUTES' => 'MINUTES',
        'SECONDS' => $arResult['VISUAL']['BLOCKS']['SECONDS'] ? 'SECONDS' : null
    ],
    'RANDOMIZE' => $arParams['RANDOMIZE_ID'] === 'Y'
];

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

$arVisual['FOOTER']['LINK'] = StringHelper::replaceMacros($arParams['FOOTER_LINK'], $arMacros);

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'],$arVisual);