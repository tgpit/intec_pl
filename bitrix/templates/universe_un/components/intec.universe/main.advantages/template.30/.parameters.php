<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('ID');

    $hPropertyFile = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'F' && $arProperty['LIST_TYPE'] == 'L' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyText = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] == 'S' && $arProperty['MULTIPLE'] === 'N')
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyFile = $arProperties->asArray($hPropertyFile);
    $arPropertyText = $arProperties->asArray($hPropertyText);
}

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['HEADER_SHOW'] === 'Y') {
    $arTemplateParameters['TITLE_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_TITLE_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_LEFT'),
            'top' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_TOP')
        ],
        'DEFAULT' => 'top',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['BACKGROUND_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_BACKGROUND_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BACKGROUND_SHOW'] === 'Y') {
    $arTemplateParameters['BACKGROUND_COLOR'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_BACKGROUND_COLOR'),
        'TYPE' => 'STRING'
    ];

    $arTemplateParameters['THEME'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_THEME'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'light' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_THEME_LIGHT'),
            'dark' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_THEME_DARK')
        ],
        'DEFAULT' => 'light'
    ];
}

if ($arCurrentValues['TITLE_POSITION'] == 'left') {
    $arTemplateParameters['COLUMNS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            3 => '3'
        ],
        'DEFAULT' => 3
    ];
} else {
    $arTemplateParameters['COLUMNS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_COLUMNS'),
        'TYPE' => 'LIST',
        'VALUES' => [
            3 => '3',
            4 => '4',
            5 => '5'
        ],
        'DEFAULT' => 4
    ];
}

$arTemplateParameters['MOBILE_COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_MOBILE_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => '1',
        2 => '2'
    ],
    'DEFAULT' => 2
];

$arTemplateParameters['NAME_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_NAME_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['NAME_SHOW'] === 'Y') {
    $arTemplateParameters['NAME_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_NAME_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}

$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PICTURE_SHOW'] === 'Y') {
    $arTemplateParameters['PICTURE_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PICTURE_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_LEFT'),
            'top' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_TOP')
        ],
        'DEFAULT' => 'top'
    ];

    $arTemplateParameters['PICTURE_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PICTURE_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];

    $arTemplateParameters['SVG_FILE_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_SVG_FILE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
        $arTemplateParameters['PROPERTY_SVG_FILE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PROPERTY_SVG_FILE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyFile,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }
}

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

$arTemplateParameters['LINK_PROPERTY_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_LINK_PROPERTY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_PROPERTY_USE'] === 'Y') {
    $arTemplateParameters['LINK_PROPERTY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_LINK_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyText,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PREVIEW_SHOW'] === 'Y') {
    $arTemplateParameters['PREVIEW_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_PREVIEW_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_MAIN_ADVANTAGES_TEMPLATE_30_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}