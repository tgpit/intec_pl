<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arParameters = [];
$arParameters['ACCESS_TOKEN'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('IC_VK_ACCESS_TOKEN'),
    'TYPE' => 'STRING'
];
$arParameters['USER_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('IC_VK_USER_ID'),
    'TYPE' => 'STRING'
];
$arParameters['DOMAIN'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('IC_VK_DOMAIN'),
    'TYPE' => 'STRING'
];
$arParameters['ITEMS_OFFSET'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('IC_VK_ITEMS_OFFSET'),
    'TYPE' => 'STRING'
];
$arParameters['ITEMS_COUNT'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('IC_VK_ITEMS_COUNT'),
    'TYPE' => 'STRING'
];
$arParameters['FILTER'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('IC_VK_FILTER'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'all' => Loc::getMessage('IC_VK_FILTER_ALL'),
        'owner' => Loc::getMessage('IC_VK_FILTER_OWNER'),
        'others' => Loc::getMessage('IC_VK_FILTER_OTHERS'),
        'suggests' => Loc::getMessage('IC_VK_FILTER_SUGGESTS'),
        'postponed' => Loc::getMessage('IC_VK_FILTER_POSTPONED')
    ],
    'DEFAULT' => 'all'
];
$arParameters['DATE_FORMAT'] = CIBlockParameters::GetDateFormat(Loc::getMessage('IC_VK_DATE_FORMAT'), 'VISUAL');
$arParameters['CACHE_TIME'] = [];
$arComponentParameters = [
    'GROUPS' => [
        'HEADER' => [
            'NAME' => Loc::getMessage('IC_VK_GROUPS_HEADER'),
            'SORT' => 220
        ]
    ],
    'PARAMETERS' => $arParameters
];