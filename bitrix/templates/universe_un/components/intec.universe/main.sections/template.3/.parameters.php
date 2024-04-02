<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $USER_FIELD_MANAGER;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['LINK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_LINK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['WIDE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_WIDE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
$arTemplateParameters['LINE_COUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_LINE_COUNT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3',
        4 => '4',
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['TEXT_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['BUTTON_ALL_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_BUTTON_ALL_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BUTTON_ALL_SHOW'] == 'Y') {
    $arTemplateParameters['BUTTON_ALL_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_BUTTON_ALL_TEXT'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_3_BUTTON_ALL_TEXT_DEFAULT')
    ];
}

$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_1_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_1_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_1_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_1_SVG_FILE_COLOR_THEME'),
        ],
        'DEFAULT' => 'theme'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arProperty_UF = array();
        $arUserFields = $USER_FIELD_MANAGER->GetUserFields("IBLOCK_".$arCurrentValues["IBLOCK_ID"]."_SECTION", 0, LANGUAGE_ID);

        foreach ($arUserFields as $arUserField) {
            if ($arUserField['USER_TYPE']['USER_TYPE_ID'] !== 'file')
                continue;

            $arUserField['LIST_COLUMN_LABEL'] = (string)$arUserField['LIST_COLUMN_LABEL'];
            $arProperty_UF[$arUserField['FIELD_NAME']] = $arUserField['LIST_COLUMN_LABEL'] ? '['.$arUserField['FIELD_NAME'].'] '.$arUserField['LIST_COLUMN_LABEL'] : $arUserField['FIELD_NAME'];
        }

        $arTemplateParameters['PROPERTY_SVG_FILE'] = [
            "PARENT" => 'DATA_SOURCE',
            "NAME" => Loc::getMessage('C_MAIN_SECTIONS_TEMPLATE_1_PROPERTY_SVG_FILE'),
            "TYPE" => 'LIST',
            "MULTIPLE" => 'N',
            "ADDITIONAL_VALUES" => 'Y',
            "VALUES" => $arProperty_UF,
        ];
    }
}