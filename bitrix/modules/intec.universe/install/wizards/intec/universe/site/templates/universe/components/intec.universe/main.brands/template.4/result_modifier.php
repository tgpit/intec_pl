<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
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
    'LINE_COUNT' => 4,
    'ALIGNMENT' => 'center',
    'TRANSPARENCY' => 0,
    'EFFECT_PRIMARY' => 'none',
    'LINK_USE' => 'Y',
    'LINK_BLANK' => 'N',
    'BORDER_SHOW' => 'Y',
    'SHOW_ALL_BUTTON_SHOW' => 'N',
    'SHOW_ALL_BUTTON_TEXT' => null,
    'SHOW_ALL_BUTTON_POSITION' => 'left',
    'SHOW_ALL_BUTTON_BORDER' => 'rectangular'
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['BUTTONS'] = [
    'SHOW_ALL' => [
        'SHOW' => $arParams['SHOW_ALL_BUTTON_SHOW'] === 'Y',
        'TEXT' => $arParams['SHOW_ALL_BUTTON_TEXT'],
        'POSITION' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['SHOW_ALL_BUTTON_POSITION']),
        'BORDER' => ArrayHelper::fromRange([
            'rectangular',
            'rounded'
        ], $arParams['SHOW_ALL_BUTTON_BORDER']),
        'LINK' => null
    ]
];

if (!empty($arParams['LIST_PAGE_URL']))
    $arResult['BUTTONS']['SHOW_ALL']['LINK'] = StringHelper::replaceMacros(
        $arParams['LIST_PAGE_URL'],
        $arMacros
    );

if (empty($arResult['BUTTONS']['SHOW_ALL']['TEXT']) || empty($arResult['BUTTONS']['SHOW_ALL']['LINK']))
    $arResult['BUTTONS']['SHOW_ALL']['DISPLAY'] = 'none';

if ($arResult['BLOCKS']['HEADER']['POSITION'] === 'right' && $arResult['BUTTONS']['SHOW_ALL']['DISPLAY'] === 'top') {
    $arResult['BUTTONS']['SHOW_ALL']['DISPLAY'] = 'none';
}

$arResult['VISUAL']['LAZYLOAD'] = [
    'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    'STUB' => null
];

if (defined('EDITOR'))
    $arResult['VISUAL']['LAZYLOAD']['USE'] = false;

if ($arResult['VISUAL']['LAZYLOAD']['USE'])
    $arResult['VISUAL']['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL']['COLUMNS'] = ArrayHelper::fromRange([4, 3, 5], $arParams['LINE_COUNT']);
$arResult['VISUAL']['ALIGNMENT'] = ArrayHelper::fromRange([
    'center',
    'left',
    'right'
], $arParams['ALIGNMENT']);

$arResult['VISUAL']['EFFECTS']['PRIMARY'] = ArrayHelper::fromRange([
    'grayscale',
    'shadow',
    'zoom',
    'none'
], $arParams['EFFECT_PRIMARY']);

$arResult['VISUAL']['EFFECTS']['SECONDARY'] = ArrayHelper::fromRange([
    'shadow',
    'grayscale',
    'zoom',
    'none'
], $arParams['EFFECT_SECONDARY']);

$arResult['VISUAL']['LINK'] = [
    'USE' => $arParams['LINK_USE'] === 'Y',
    'BLANK' => $arParams['LINK_BLANK'] === 'Y'
];

$arResult['VISUAL']['BORDER'] = [
    'SHOW' => $arParams['BORDER_SHOW'] === 'Y'
];

$arResult['VISUAL']['TRANSPARENCY'] = Type::toInteger($arParams['TRANSPARENCY']);

if ($arResult['VISUAL']['ALIGNMENT'] === 'left')
    $arResult['VISUAL']['ALIGNMENT'] = 'start';

if ($arResult['VISUAL']['ALIGNMENT'] === 'right')
    $arResult['VISUAL']['ALIGNMENT'] = 'end';

if ($arResult['VISUAL']['TRANSPARENCY'] > 100)
    $arResult['VISUAL']['TRANSPARENCY'] = 100;

if ($arResult['VISUAL']['TRANSPARENCY'] < 0)
    $arResult['VISUAL']['TRANSPARENCY'] = 0;

$arResult['VISUAL']['TRANSPARENCY'] = 1 - ($arResult['VISUAL']['TRANSPARENCY'] / 100);

