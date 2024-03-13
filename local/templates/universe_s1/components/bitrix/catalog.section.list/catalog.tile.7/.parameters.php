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
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3'
    ],
    'DEFAULT' => 2
];
$arTemplateParameters['LINK_BLANK'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_LINK_BLANK'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
$arTemplateParameters['PICTURE_SIZE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PICTURE_SIZE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'big' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PICTURE_SIZE_BIG'),
        'medium' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PICTURE_SIZE_MEDIUM'),
        'small' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PICTURE_SIZE_SMALL'),
    ],
    'DEFAULT' => 'small'
];
$arTemplateParameters['DESCRIPTION_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_DESCRIPTION_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['CHILDREN_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
if ($arCurrentValues['CHILDREN_SHOW'] === 'Y') {
    $arTemplateParameters['CHILDREN_VIEW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_VIEW'),
        'TYPE' => 'LIST',
        'VALUES' => [
            1 => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_VIEW_1'),
            2 => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_VIEW_2')
        ],
        'DEFAULT' => 1
    ];
    $arTemplateParameters['CHILDREN_ELEMENTS'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_ELEMENTS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['CHILDREN_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_CHILDREN_COUNT'),
        'TYPE' => 'STRING',
        'DEFAULT' => '5'
    ];
}
$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];
if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_SVG_FILE_COLOR_THEME'),
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
            "NAME" => Loc::getMessage('C_CATALOG_SECTION_LIST_CATALOG_TILE_7_PROPERTY_SVG_FILE'),
            "TYPE" => 'LIST',
            "MULTIPLE" => 'N',
            "ADDITIONAL_VALUES" => 'Y',
            "VALUES" => $arProperty_UF,
        ];
    }
}