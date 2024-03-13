<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;
else
    return;

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_BLANK'] === 'Y'
    ],
    'GALLERY' => [
        'USE' => $arParams['GALLERY_USE'] === 'Y'
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y'
    ],
    'QUICK_VIEW' => [
        'USE' => $arParams['QUICK_VIEW_USE'] === 'Y'
    ],
    'ARTICLE' => [
        'SHOW' => $arParams['ARTICLE_SHOW'] === 'Y'
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y' && empty($arResult['OFFERS']),
        'MODE' => ArrayHelper::fromRange(['number', 'logic', 'text'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'MANY' => $arParams['QUANTITY_BOUNDS_MANY'],
            'FEW' => $arParams['QUANTITY_BOUNDS_FEW']
        ]
    ],
    'VOTE' => [
        'USE' => $arParams['VOTE_USE'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['rating', 'vote_avg'], $arParams['VOTE_MODE'])
    ],
    'PRICE' => [
        'RANGE' => [
            'SHOW' => $bBase && $arParams['PRICE_RANGE_SHOW'] === 'Y' && count($arResult['ITEM_PRICES']) > 1
        ],
        'DISCOUNT' => [
            'SHOW' => $arParams['PRICE_DISCOUNT_SHOW'] === 'Y',
            'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
            'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
        ]
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'TIMER' => [
        'SHOW' => $bBase && $arParams['TIMER_SHOW'] === 'Y' && ($arResult['CAN_BUY'] || !empty($arResult['OFFERS']))
    ],
    'ORDER_FAST' => [
        'USE' => $arParams['ORDER_FAST_USE'] === 'Y'
    ]
];

if ($bLite)
    include(__DIR__ . '/modifiers/lite/catalog.php');

include(__DIR__.'/modifiers/actions.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/gallery.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

unset($arVisual);