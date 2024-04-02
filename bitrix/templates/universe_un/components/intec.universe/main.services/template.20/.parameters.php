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

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 3
];

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['HEADER_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_HEADER_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'top' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_TOP'),
        'left' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_LEFT')
    ],
    'DEFAULT' => 'left'
];

$arTemplateParameters['HEADER_BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_HEADER_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_BUTTON_SHOW'] === 'Y') {
    $arTemplateParameters['HEADER_BUTTON_POSITION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_HEADER_BUTTON_POSITION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_LEFT'),
            'center' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_CENTER'),
            'right' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_RIGHT')
        ],
        'DEFAULT' => 'center'
    ];

    $arTemplateParameters['HEADER_BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_HEADER_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_HEADER_BUTTON_TEXT_DEFAULT')
    ];
}

$arTemplateParameters['BORDERS_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_BORDERS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['NAME_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_NAME_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_LEFT'),
        'center' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_CENTER'),
        'right' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_POSITION_RIGHT')
    ],
    'DEFAULT' => 'left'
];

$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_SVG_FILE_COLOR_THEME'),
        ],
        'DEFAULT' => 'theme'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]))->indexBy('ID');

        $hPropertiesFile = function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        };

        $arTemplateParameters['PROPERTY_SVG_FILE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_20_PROPERTY_SVG_FILE'),
            'TYPE' => 'LIST',
            'MULTIPLE' => 'N',
            'ADDITIONAL_VALUES' => 'Y',
            'VALUES' => $arProperties->asArray($hPropertiesFile),
        ];
    }
}
