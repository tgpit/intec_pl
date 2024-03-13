<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $USER_FIELD_MANAGER;

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

$arTemplateParameters = [];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4',
        5 => '5',
        6 => '6',
    ],
    'DEFAULT' => 5
];

$arTemplateParameters['LINK_BLANK'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_LINK_BLANK'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['PICTURE_SIZE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_PICTURE_SIZE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'medium' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_PICTURE_SIZE_MEDIUM'),
        'small' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_PICTURE_SIZE_SMALL'),
    ],
    'DEFAULT' => 'small'
];

$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SVG_FILE_COLOR_THEME'),
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
            "NAME" => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_PROPERTY_SVG_FILE'),
            "TYPE" => 'LIST',
            "MULTIPLE" => 'N',
            "ADDITIONAL_VALUES" => 'Y',
            "VALUES" => $arProperty_UF,
        ];
    }
}

$arTemplateParameters['SLIDER_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SLIDER_USE'] === 'Y') {
    $arTemplateParameters['SLIDER_NAVIGATION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_NAVIGATION'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_DOTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_DOTS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_LOOP'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_LOOP'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['SLIDER_AUTO_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_AUTO_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SLIDER_AUTO_USE'] === 'Y') {
        $arTemplateParameters['SLIDER_AUTO_PAUSE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_AUTO_PAUSE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        $arTemplateParameters['SLIDER_AUTO_SPEED'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_AUTO_SPEED'),
            'TYPE' => 'STRING',
            'DEFAULT' => 500
        ];

        $arTemplateParameters['SLIDER_AUTO_TIME'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_SLIDER_1_SLIDER_AUTO_TIME'),
            'TYPE' => 'STRING',
            'DEFAULT' => 5000
        ];
    }
}