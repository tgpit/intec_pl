<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['LINE_COUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_LINE_COUNT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => 3,
        4 => 4,
        5 => 5
    ],
    'DEFAULT' => 4
];

$arTemplateParameters['ALIGNMENT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_LEFT'),
        'center' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_CENTER'),
        'right' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_RIGHT')
    ],
    'DEFAULT' => 'center'
];

$arTemplateParameters['EFFECT_PRIMARY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_PRIMARY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_NONE'),
        'grayscale' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_GRAYSCALE'),
        'zoom' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_ZOOM'),
        'shadow' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_SHADOW')
    ],
    'DEFAULT' => 'shadow'
];

$arTemplateParameters['EFFECT_SECONDARY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_SECONDARY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_NONE'),
        'grayscale' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_GRAYSCALE'),
        'zoom' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_ZOOM'),
        'shadow' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_EFFECT_SHADOW')
    ],
    'DEFAULT' => 'grayscale'
];

$arTemplateParameters['TRANSPARENCY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_TRANSPARENCY'),
    'TYPE' => 'STRING',
    'DEFAULT' => 0
];

$arTemplateParameters['BORDER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_BORDER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['SHOW_ALL_BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SHOW_ALL_BUTTON_SHOW'] === 'Y') {
    $arTemplateParameters['SHOW_ALL_BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_TEXT_DEFAULT')
    ];

    $arTemplateParameters['SHOW_ALL_BUTTON_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_LEFT'),
            'center' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_CENTER'),
            'right' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_ALIGNMENT_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];

    $arTemplateParameters['SHOW_ALL_BUTTON_BORDER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_SHOW_ALL_BUTTON_BORDER'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rectangular' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_BORDER_RECTANGULAR'),
            'rounded' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_4_BORDER_ROUNDED')
        ],
        'DEFAULT' => 'rectangular'
    ];
}