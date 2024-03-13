<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
	return;

Loc::loadMessages(__FILE__);

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LIST_MENU_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_LIST_MENU_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

$arTemplateParameters['DETAIL_MENU_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_DETAIL_MENU_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LIST_MENU_SHOW'] === 'Y' || $arCurrentValues['DETAIL_MENU_SHOW'] === 'Y')
    include(__DIR__.'/parameters/menu.php');

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['DESCRIPTION_SHOW'] === 'Y' && $arCurrentValues['LIST_MENU_SHOW'] === 'Y') {
    $arTemplateParameters['DESCRIPTION_POSITION'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_BRANDS_1_DESCRIPTION_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'inside' => Loc::getMessage('C_NEWS_BRANDS_1_DESCRIPTION_POSITION_INSIDE'),
            'outside' => Loc::getMessage('C_NEWS_BRANDS_1_DESCRIPTION_POSITION_OUTSIDE')
        ],
        'DEFAULT' => 'inside'
    ];
}

include(__DIR__.'/parameters/regionality.php');
include(__DIR__.'/parameters/list.php');
include(__DIR__.'/parameters/detail.php');

if (!Loader::includeModule('catalog') && Loader::includeModule('intec.startshop'))
    include(__DIR__.'/parameters/lite.php');