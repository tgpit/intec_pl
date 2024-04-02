<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['HEADER_SHOW'] = [
    'PARENT' => 'HEADER',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_SHOW'] === 'Y') {
    $arTemplateParameters['HEADER_TITLE'] = [
        'PARENT' => 'HEADER',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_HEADER_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('IC_VK_TEMPLATE_2_HEADER_TITLE_DEFAULT')
    ];
    $arTemplateParameters['HEADER_DESCRIPTION'] = [
        'PARENT' => 'HEADER',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_HEADER_DESCRIPTION'),
        'TYPE' => 'STRING',
        'DEFAULT' => null
    ];
    $arTemplateParameters['MAIN_URL_LIST_SHOW'] = [
        'PARENT' => 'HEADER',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_MAIN_URL_LIST_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['MAIN_URL_LIST_SHOW'] === 'Y') {
        $arTemplateParameters['MAIN_URL_LIST_URL'] = [
            'PARENT' => 'HEADER',
            'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_MAIN_URL_LIST_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => null
        ];
        $arTemplateParameters['MAIN_URL_LIST_BLANK'] = [
            'PARENT' => 'HEADER',
            'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_MAIN_URL_LIST_BLANK'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['MAIN_URL_LIST_TEXT'] = [
            'PARENT' => 'HEADER',
            'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_MAIN_URL_LIST_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('IC_VK_TEMPLATE_2_MAIN_URL_LIST_TEXT_DEFAULT')
        ];
    }
}

$arTemplateParameters['ITEMS_COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEMS_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => 1,
        2 => 2,
        3 => 3,
        4 => 4,
        5 => 5
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['ITEM_PICTURE_RATIO'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEM_PICTURE_RATIO'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1x1' => '1x1',
        '4x3' => '4x3',
        '16x9' => '16x9',
        '21x9' => '21x9'
    ],
    'DEFAULT' => '4x3'
];
$arTemplateParameters['ITEM_DESCRIPTION_TRUNCATE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEM_DESCRIPTION_TRUNCATE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ITEM_DESCRIPTION_TRUNCATE_USE'] === 'Y') {
    $arTemplateParameters['ITEM_DESCRIPTION_TRUNCATE_LIMIT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEM_DESCRIPTION_TRUNCATE_LIMIT'),
        'TYPE' => 'STRING',
        'DEFAULT' => 20
    ];
}

$arTemplateParameters['ITEM_URL_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEM_URL_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ITEM_URL_USE'] === 'Y') {
    $arTemplateParameters['ITEM_URL_BLANK'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('IC_VK_TEMPLATE_2_ITEM_URL_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}