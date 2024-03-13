<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/** @var array $arCurrentValues */

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Type;
use intec\core\collections\Arrays;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$arIBlockType = [];
$arIBlocksList = [];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arIBlockType = CIBlockParameters::GetIBlockTypes();

$arIBlocksList = Arrays::fromDBResult(CIBlock::GetList([], [
    'ACTIVE' => 'Y',
    'SITE_ID' => $sSite
]))->indexBy('ID');

if (!empty($arCurrentValues['PRODUCTS_IBLOCK_TYPE']))
    $arIBlock = $arIBlocksList->asArray(function ($sKey, $arProperty) use (&$arCurrentValues) {
        if ($arProperty['IBLOCK_TYPE_ID'] === $arCurrentValues['PRODUCTS_IBLOCK_TYPE'])
            return [
                'key' => $arProperty['ID'],
                'value' => '[' . $arProperty['ID'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    });

$arTemplateParameters["PRODUCTS_IBLOCK_TYPE"] = [
    "PARENT" => "BASE",
    "NAME" => Loc::getMessage("C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_IBLOCK_TYPE"),
    "TYPE" => "LIST",
    "VALUES" => $arIBlockType,
    "REFRESH" => "Y",
    "ADDITIONAL_VALUES" => "Y"
];

if (!empty($arCurrentValues['PRODUCTS_IBLOCK_TYPE'])) {
    $arTemplateParameters["PRODUCTS_IBLOCK_ID"] = [
        "PARENT" => "BASE",
        "NAME" => Loc::getMessage("C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_IBLOCK_ID"),
        "TYPE" => "LIST",
        "VALUES" => $arIBlock,
        "REFRESH" => "Y",
        "ADDITIONAL_VALUES" => "Y",
    ];
}

unset($arIBlockType, $arIBlocksList, $arIBlock);

if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID'])) {
    $arProductsProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['PRODUCTS_IBLOCK_ID']
    ]));

    $hPropertiesElements = function ($iIndex, $arProperty) {
        if (empty($arProperty['CODE']))
            return ['skip' => true];

        if ($arProperty['PROPERTY_TYPE'] === 'E' && empty($arProperty['USER_TYPE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PRODUCTS_PROPERTY_BRAND'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_PROPERTY_BRAND'),
        'TYPE' => 'LIST',
        'VALUES' => $arProductsProperties->asArray($hPropertiesElements),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['LINK_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_LINK_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LINK_SHOW'] === 'Y') {
    $arTemplateParameters['LINK_VALUE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_LINK_VALUE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_LINK_VALUE_DEFAULT'),
    ];
}

$arTemplateParameters['SECTIONS_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SECTIONS_SHOW'] === 'Y') {
    $arTemplateParameters['SECTIONS_HEADER_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SECTIONS_HEADER_SHOW'] === 'Y') {
        $arTemplateParameters['SECTIONS_HEADER'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_HEADER_DEFAULT'),
        ];
    }

    $arTemplateParameters['SECTIONS_QUANTITY'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_QUANTITY'),
        'TYPE' => 'NUMBER',
        'DEFAULT' => 0,
    ];

    $arTemplateParameters['SECTIONS_COUNT_ELEMENTS'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_COUNT_ELEMENTS'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
    ];

    $arTemplateParameters['SECTIONS_FILTER_NAME'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_SECTIONS_FILTER_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'BrandsDetailSectionsFilter',
    ];

    include(__DIR__.'/parameters/sections.php');
}

$arTemplateParameters['PRODUCTS_SHOW'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
    $arTemplateParameters['PRODUCTS_HEADER_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRODUCTS_HEADER_SHOW'] === 'Y') {
        $arTemplateParameters['PRODUCTS_HEADER'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_HEADER_DEFAULT'),
        ];
    }

    include(__DIR__.'/parameters/products.php');

    $arTemplateParameters['PRODUCTS_COMPARE_USE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_COMPARE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    $arTemplateParameters['PRODUCTS_COMPARE_NAME'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_COMPARE_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'compare'
    ];

    $arTemplateParameters['PRODUCTS_FILTER_NAME'] = [
        'PARENT' => 'FILTER_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FILTER_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'BrandsDetailProductsFilter',
    ];

    $arTemplateParameters['FILTER_USE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FILTER_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    include(__DIR__.'/parameters/filter.php');
}