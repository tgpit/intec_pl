<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

global $USER_FIELD_MANAGER;

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
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3'
    ],
    'DEFAULT' => 3
];

$arTemplateParameters['CHILDREN_DISPLAY'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_CHILDREN_DISPLAY'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'line' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_CHILDREN_DISPLAY_LINE'),
        'column' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_CHILDREN_DISPLAY_COLUMN')
    ],
    'DEFAULT' => 'line'
];

$arTemplateParameters['CHILDREN_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_CHILDREN_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'N') {
    $arTemplateParameters['PICTURE_SIZE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_SIZE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'small' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_SIZE_SMALL'),
            'middle' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_SIZE_MIDDLE')
        ],
        'DEFAULT' => 'medium'
    ];
}

$arTemplateParameters['PICTURE_POSITION_VERTICAL'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_POSITION_VERTICAL'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'top' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_POSITION_VERTICAL_TOP'),
        'center' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PICTURE_POSITION_VERTICAL_CENTER')
    ],
    'DEFAULT' => 'medium'
];

$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_SVG_FILE_COLOR_THEME'),
        ],
        'DEFAULT' => 'theme'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arProperty_UF = [];
        $arUserFields = $USER_FIELD_MANAGER->GetUserFields(
            'IBLOCK_'.$arCurrentValues['IBLOCK_ID'].'_SECTION',
            0,
            LANGUAGE_ID
        );

        foreach ($arUserFields as $arUserField) {
            if ($arUserField['USER_TYPE']['USER_TYPE_ID'] !== 'file')
                continue;

            $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
            $arProperty_UF[$arUserField['FIELD_NAME']] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$arUserField['FIELD_NAME'].'] '.$arUserField['LIST_COLUMN_LABEL'] : $arUserField['FIELD_NAME'];
        }

        $arTemplateParameters['PROPERTY_SVG_FILE'] = [
            "PARENT" => 'DATA_SOURCE',
            "NAME" => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_PROPERTY_SVG_FILE'),
            "TYPE" => 'LIST',
            "MULTIPLE" => 'N',
            "ADDITIONAL_VALUES" => 'Y',
            "VALUES" => $arProperty_UF,
        ];
    }
}

$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['HEADER_BUTTON_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_HEADER_BUTTON_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['HEADER_BUTTON_SHOW'] === 'Y') {
    $arTemplateParameters['HEADER_BUTTON_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_HEADER_BUTTON_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_SERVICES_TEMPLATE_21_HEADER_BUTTON_TEXT_DEFAULT')
    ];
}