<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'VIEW' => 'tiles.1',
    'COLUMNS' => 4,
    'WIDE' => 'N',
    'NAME_SHOW' => 'N',
    'DESCRIPTION_SHOW' => 'N',
    'BORDERS_SHOW' => 'N'
], $arParams);

$arVisual = [
    'VIEW' => ArrayHelper::fromRange(['tiles.1', 'tiles.2', 'list.1'], $arParams['VIEW']),
    'COLUMNS' => ArrayHelper::fromRange([3, 2, 4], $arParams['COLUMNS']),
    'WIDE' => $arParams['WIDE'] === 'Y',
    'LINK' => [
        'HIDE' => $arParams['HIDE_LINK_WHEN_NO_DETAIL']
    ],
    'NAME' => [
        'SHOW' => $arParams['NAME_SHOW'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y'
    ],
    'BORDERS' => [
        'SHOW' => $arParams['BORDERS_SHOW'] === 'Y'
    ],
    'NAVIGATION' => [
        'SHOW' => [
            'TOP' => false,
            'BOTTOM' => false,
            'ALWAYS' => $arParams['PAGER_SHOW_ALWAYS']
        ],
        'COUNT' => Type::toInteger($arParams['NEWS_COUNT'])
    ]
];

$arResult['LAZYLOAD'] = [
    'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    'STUB' => null
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arData = [
        'HIDE_LINK' => false
    ];

    /** Hide link item */
    $arData['HIDE_LINK'] = $arVisual['LINK']['HIDE'] && empty($arItem['DETAIL_TEXT']);

    $arItem['DATA'] = $arData;

    unset($arData);
}

unset($arItem);

if ($arVisual['VIEW'] === 'list.1')
    $arVisual['COLUMNS'] = 'false';

if (defined('EDITOR'))
    $arResult['LAZYLOAD']['USE'] = false;

if ($arResult['LAZYLOAD']['USE'])
    $arResult['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arNavigation = [];

if (!empty($arResult['NAV_RESULT'])) {
    $arNavigation = [
        'PAGE' => [
            'COUNT' => $arResult['NAV_RESULT']->NavPageCount,
            'NUMBER' => $arResult['NAV_RESULT']->NavPageNomer,
        ],
        'NUMBER' => $arResult['NAV_RESULT']->NavNum
    ];

    if ($arVisual['NAVIGATION']['SHOW']['ALWAYS']) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    } else if ($arVisual['NAVIGATION']['COUNT'] > 0 && $arNavigation['PAGE']['COUNT'] > 1) {
        $arVisual['NAVIGATION']['SHOW']['TOP'] = $arParams['DISPLAY_TOP_PAGER'];
        $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = $arParams['DISPLAY_BOTTOM_PAGER'];
    }
} else {
    $arVisual['NAVIGATION']['SHOW']['TOP'] = false;
    $arVisual['NAVIGATION']['SHOW']['BOTTOM'] = false;
}

$arResult['VISUAL'] = $arVisual;
$arResult['NAVIGATION'] = $arNavigation;