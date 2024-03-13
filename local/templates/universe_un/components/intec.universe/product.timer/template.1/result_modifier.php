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
    'SECTION' => $arParams['IS_SECTION'],
    'QUANTITY' => [
        'SHOW' => $arParams['TIMER_QUANTITY_SHOW'] === 'Y',
        'OVER' => $arParams['TIMER_QUANTITY_OVER'] === 'Y'
    ],
    'TITLE' => [
        'SHOW' => $arParams['TIMER_TITLE_SHOW'] === 'Y',
        'ENTER' => $arParams['TIMER_TITLE_ENTER'] === 'Y',
        'VALUE' => null
    ],
    'CASES' => [
        'HOURS' => 'HOURS',
        'MINUTES' => 'MINUTES',
        'SECONDS' => $arResult['VISUAL']['BLOCKS']['SECONDS'] ? 'SECONDS' : null
    ],
    'DAYS' => 'DAYS',
    'RANDOMIZE' => $arParams['RANDOMIZE_ID'] === 'Y'
];

if ($arVisual['QUANTITY']['OVER'] && $arResult['DATA']['TIMER']['PRODUCT']['QUANTITY'] > 999)
    $arResult['DATA']['TIMER']['PRODUCT']['QUANTITY'] = '999+';

if (!empty($arResult['VISUAL']['TITLE']['VALUE']))
     $arVisual['TITLE']['VALUE'] = $arResult['VISUAL']['TITLE']['VALUE'];

if ($arVisual['TITLE']['SHOW'] && $arVisual['TITLE']['ENTER']) {
     if (!empty(trim($arParams['TIMER_TITLE_VALUE']))) {
         $arVisual['TITLE']['VALUE'] = $arParams['TIMER_TITLE_VALUE'];
     }
 }

$iDays = $arResult['DATE']['REMAINING']['DAYS'];

if ($iDays % 10 == 1 && $iDays % 100 != 11) {
    $arVisual['DAYS'] = 'DAY';
} elseif ($iDays % 10 >= 2 && $iDays % 10 <= 4 && ($iDays % 100 < 10 || $iDays % 100 >= 20)) {
    $arVisual['DAYS'] = 'DAYS';
} else {
    $arVisual['DAYS'] = 'DAYS_MANY';
}

unset($iDays);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/',
    'TEMPLATE_PATH' => $this->GetFolder().'/'
];

$arVisual['FOOTER']['LINK'] = StringHelper::replaceMacros($arParams['FOOTER_LINK'], $arMacros);

$arResult['VISUAL'] = ArrayHelper::merge($arResult['VISUAL'],$arVisual);