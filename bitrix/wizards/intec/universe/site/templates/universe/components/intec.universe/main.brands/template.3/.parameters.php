<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_USE'] === 'Y') {
    $arTemplateParameters['LINK_BLANK'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_LINK_BLANK'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['LINE_COUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_LINE_COUNT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        4 => 4,
        5 => 5,
        6 => 6
    ],
    'DEFAULT' => 4
];

if ($arCurrentValues['SLIDER_USE'] !== 'Y') {
    $arTemplateParameters['ALIGNMENT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_LEFT'),
            'center' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_CENTER'),
            'right' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];
}

$arTemplateParameters['EFFECT_PRIMARY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_PRIMARY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_NONE'),
        'grayscale' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_GRAYSCALE'),
        'zoom' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_ZOOM'),
        'shadow' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_SHADOW')
    ],
    'DEFAULT' => 'shadow'
];

$arTemplateParameters['EFFECT_SECONDARY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_SECONDARY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_NONE'),
        'grayscale' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_GRAYSCALE'),
        'zoom' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_ZOOM'),
        'shadow' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_EFFECT_SHADOW')
    ],
    'DEFAULT' => 'grayscale'
];

$arTemplateParameters['TRANSPARENCY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_TRANSPARENCY'),
    'TYPE' => 'STRING',
    'DEFAULT' => 0
];

$arTemplateParameters['BORDER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_BORDER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['SLIDER_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SLIDER_USE'] === 'Y') {
    $arTemplateParameters['SLIDER_NAVIGATION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_NAVIGATION'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_DOTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_DOTS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_LOOP'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_LOOP'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_AUTO_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_AUTO_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SLIDER_AUTO_USE'] === 'Y') {
        $arTemplateParameters['SLIDER_AUTO_PAUSE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_AUTO_PAUSE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        $arTemplateParameters['SLIDER_AUTO_SPEED'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_AUTO_SPEED'),
            'TYPE' => 'STRING',
            'DEFAULT' => 500
        ];

        $arTemplateParameters['SLIDER_AUTO_TIME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SLIDER_AUTO_TIME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 5000
        ];
    }
}

$arTemplateParameters['SHOW_ALL_BUTTON_DISPLAY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_DISPLAY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_DISPLAY_NONE'),
        'top' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_DISPLAY_TOP'),
        'bottom' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_DISPLAY_BOTTOM')
    ],
    'DEFAULT' => 'none',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SHOW_ALL_BUTTON_DISPLAY'] !== 'none') {
    $arTemplateParameters['SHOW_ALL_BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_TEXT_DEFAULT')
    ];
}

if ($arCurrentValues['SHOW_ALL_BUTTON_DISPLAY'] === 'bottom') {
    $arTemplateParameters['SHOW_ALL_BUTTON_ALIGNMENT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_ALIGNMENT'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_LEFT'),
            'center' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_CENTER'),
            'right' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_ALIGNMENT_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];


    $arTemplateParameters['SHOW_ALL_BUTTON_BORDER'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_SHOW_ALL_BUTTON_BORDER'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rectangular' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_BORDER_RECTANGULAR'),
            'rounded' => Loc::getMessage('C_MAIN_BRANDS_TEMPLATE_3_BORDER_ROUNDED')
        ],
        'DEFAULT' => 'rectangular'
    ];
}