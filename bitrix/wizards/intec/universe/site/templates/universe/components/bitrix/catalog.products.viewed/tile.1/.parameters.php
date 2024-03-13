<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core') || !Loader::includeModule('sale'))
    return;

if (Loader::includeModule('iblock')) {
    $arIBlocksTypes = CIBlockParameters::GetIBlockTypes();

    $arIBlocks = [];
    $rsIBlocks = CIBlock::GetList([], [
        'ACTIVE' => 'Y',
        'TYPE' => $arCurrentValues['REQUESTED_IBLOCK_ID_TYPE']
    ]);

    while ($arIBlock = $rsIBlocks->GetNext())
        $arIBlocks[$arIBlock['ID']] = '['.$arIBlock['ID'].'] '.$arIBlock['NAME'];
}

if ($arCurrentValues['REQUESTED_IBLOCK_ID']) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['REQUESTED_IBLOCK_ID'],
    ]))->indexBy('ID');

    $hPropertiesCheckbox = function ($sKey, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
            return ['skip' => true];

        return [
            'key' => $arProperty['CODE'],
            'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
        ];
    };
}

$arTemplateParameters = [];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['TITLE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_TITLE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y',
    'DEFAULT' => 'N'
];

if ($arCurrentValues['TITLE_SHOW'] === 'Y') {
    $arTemplateParameters['TITLE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_TITLE'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['PAGE_ELEMENT_COUNT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_ELEMENT_COUNT'),
    'TYPE' => 'STRING',
    'DEFAULT' => '10',
    'HIDDEN' => 'Y'
];

$arTemplateParameters['HIDE_REQUESTED'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_HIDE_REQUESTED'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

$arTemplateParameters['REQUESTED_IBLOCK_ID_TYPE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_REQUESTED_IBLOCK_ID_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arTemplateParameters['REQUESTED_IBLOCK_ID'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_REQUESTED_IBLOCK_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocks,
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['REQUESTED_IBLOCK_ID'])) {
    if ($arCurrentValues['HIDE_REQUESTED'] === 'Y') {
        $arTemplateParameters['PROPERTY_REQUEST'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_PROPERTY_REQUESTED'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        4 => '4',
        5 => '5',
        8 => '8',
        9 => '9'
    ],
    'DEFAULT' => 5
];

$arTemplateParameters['SHOW_NAVIGATION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_1_SHOW_NAVIGATION'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];