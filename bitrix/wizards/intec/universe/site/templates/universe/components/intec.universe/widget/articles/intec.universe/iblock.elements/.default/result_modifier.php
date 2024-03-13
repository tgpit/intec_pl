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
    'SEE_ALL_SHOW' => 'N',
    'SEE_ALL_POSITION' => 'N',
    'HIDDE_NON_ACTIVE' => 'N',
    'SEE_ALL_TEXT' => null,
    'SEE_ALL_URL' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = ArrayHelper::merge($arResult['VISUAL'], [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] ==='Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'HEADER' => [
        'SHOW' => $arParams['HEADER_SHOW'] === 'Y' && !empty($arParams['HEADER']),
        'VALUE' => $arParams['HEADER'],
        'POSITION' => $arParams['HEADER_CENTER'] === 'Y' ? 'center' : 'left'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y' && !empty($arParams['DESCRIPTION']),
        'VALUE' => $arParams['DESCRIPTION'],
        'POSITION' => $arParams['DESCRIPTION_CENTER'] === 'Y' ? 'center' : 'left'
    ],
    'ELEMENT' => [
        'FIRST_BIG' => $arParams['BIG_FIRST_BLOCK'] === 'Y',
        'HEADER' => $arParams['HEADER_ELEMENT_SHOW'] === 'Y',
        'DESCRIPTION' => $arParams['DESCRIPTION_ELEMENT_SHOW'] === 'Y',
        'HIDDE_NON_ACTIVE' => $arParams['HIDDE_NON_ACTIVE'] === 'Y'
    ],
    'SEE_ALL' => [
        'SHOW' => $arParams['SEE_ALL_SHOW'] === 'Y',
        'POSITION' => $arParams['SEE_ALL_POSITION'] === 'Y' ? 'center' : 'right',
        'TEXT' => $arParams['SEE_ALL_TEXT'],
        'URL' => null
    ]
]);

$arVisual['SEE_ALL']['URL'] = StringHelper::replaceMacros($arParams['SEE_ALL_URL'], [
    'SITE_DIR' => SITE_DIR
]);

if ($arVisual['SEE_ALL']['SHOW'] && empty($arVisual['SEE_ALL']['URL']))
    $arVisual['SEE_ALL']['SHOW'] = false;

$arResult['VISUAL'] = $arVisual;

unset($arVisual);