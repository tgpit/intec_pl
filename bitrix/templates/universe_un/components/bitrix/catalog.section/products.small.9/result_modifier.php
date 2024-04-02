<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 2,
    'TITLE_SHOW' => 'Y',
    'NAVIGATION_SHOW' => 'Y',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 10,
    'QUANTITY_BOUNDS_MANY' => 50
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'COLUMNS' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS']),
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'TITLE' => [
        'SHOW' => $arParams['TITLE_SHOW'] === 'Y',
        'TEXT' => $arParams['TITLE_TEXT']
    ],
    'NAVIGATION' => [
        'SHOW' => $arParams['NAVIGATION_SHOW'] === 'Y'
    ],
    'QUANTITY' => [
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toInteger($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toInteger($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ]
];

if (empty($arVisual['TITLE']['TEXT']))
    $arVisual['TITLE']['SHOW'] = false;

include(__DIR__.'/modifiers/properties.php');

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

include(__DIR__.'/modifiers/pictures.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

unset($bBase, $bLite, $arVisual);