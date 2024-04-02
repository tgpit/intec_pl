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
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y'
];
$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => 2,
        3 => 3,
        4 => 4
    ],
    'DEFAULT' => 3
];
$arTemplateParameters['CHILDREN_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_CHILDREN_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['CHILDREN_SHOW'] === 'Y') {
    $arTemplateParameters['CHILDREN_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_CHILDREN_COUNT'),
        'TYPE' => 'LIST',
        'VALUES' => [
            0 => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_CHILDREN_COUNT_UNLIMITED'),
            1 => 1,
            2 => 2,
            3 => 3,
            4 => 4
        ],
        'DEFAULT' => 3,
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['SVG_FILE_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_SVG_FILE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
    $arTemplateParameters['SVG_FILE_COLOR'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_SVG_FILE_COLOR'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'original' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_SVG_FILE_COLOR_ORIGINAL'),
            'theme' => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_SVG_FILE_COLOR_THEME'),
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
            "NAME" => Loc::getMessage('C_BITRIX_CATALOG_SECTION_LIST_CATALOG_TILE_3_PROPERTY_SVG_FILE'),
            "TYPE" => 'LIST',
            "MULTIPLE" => 'N',
            "ADDITIONAL_VALUES" => 'Y',
            "VALUES" => $arProperty_UF,
        ];
    }
}