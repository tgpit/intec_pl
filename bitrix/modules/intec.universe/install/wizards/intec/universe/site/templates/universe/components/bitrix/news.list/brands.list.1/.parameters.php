<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['VIEW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'tiles.1' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_VIEW_TILES_1'),
        'tiles.2' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_VIEW_TILES_2'),
        'list.1' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_VIEW_LIST_1')
    ],
    'DEFAULT' => 'tiles.1',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VIEW'] === 'tiles.1' || $arCurrentValues['VIEW'] === 'tiles.2') {
    $arTemplateParameters['COLUMNS'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            2 => '2',
            3 => '3',
            4 => '4',
        ],
        'DEFAULT' => 3
    ];
}

$arTemplateParameters['NAME_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_NAME_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['VIEW'] !== 'tiles.1') {
    $arTemplateParameters['DESCRIPTION_SHOW'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_DESCRIPTION_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['BORDERS_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_LIST_BRANDS_LIST_1_BORDERS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];