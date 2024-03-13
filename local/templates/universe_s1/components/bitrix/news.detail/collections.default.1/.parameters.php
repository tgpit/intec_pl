<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('iblock'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$arTemplateParameters = [];

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
        'SORT' => 'ASC'
    ], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]))->indexBy('CODE');

    $hPropertiesElements = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'E' && empty($value['USER_TYPE']))
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesString = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'S' && $value['MULTIPLE'] !== 'Y')
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertiesCheckbox = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'C' && $value['MULTIPLE'] !== 'Y')
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertiesString = $arProperties->asArray($hPropertiesString);
    $arPropertyCheckbox = $arProperties->asArray($hPropertiesCheckbox);
    $arPropertiesElements = $arProperties->asArray($hPropertiesElements);

    $arTemplateParameters['LAZYLOAD_USE'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LAZYLOAD_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
    $arTemplateParameters['LINK_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LINK_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['LINK_SHOW'] === 'Y') {
        $arTemplateParameters['LINK_VALUE'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LINK_VALUE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_LINK_VALUE_DEFAULT'),
        ];
    }

    $arTemplateParameters['BANNER_TEXT_PROPERTY'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_BANNER_TEXT_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesString,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['BANNER_THEME_PROPERTY'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_BANNER_THEME_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckbox,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['SHARES_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SHARES_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SHARES_SHOW'] === 'Y') {
        $arTemplateParameters['SHARES_PROPERTY'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SHARES_PROPERTY'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesElements,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];

        if (!empty($arCurrentValues['SHARES_PROPERTY']))
            include(__DIR__.'/parameters/shares.php');
    }

    $arTemplateParameters['DETAIL_TEXT_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_DETAIL_TEXT_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['DETAIL_TEXT_SHOW'] === 'Y') {
        $arTemplateParameters['DETAIL_TEXT_HEADER_PROPERTY'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_DETAIL_TEXT_HEADER_PROPERTY'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertiesString,
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['PRODUCTS_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID']) && $arCurrentValues['PRODUCTS_SHOW'] === 'Y') {
    $arTemplateParameters['PRODUCTS_PROPERTY'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_PROPERTY'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertiesElements,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['PRODUCTS_PROPERTY'])) {
    $arIBlockType = [];
    $arIBlocksList = [];

    $arTemplateParameters['PRODUCTS_FILTER_NAME'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_FILTER_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'arrCollectionProductsFilter'
    ];
    $arTemplateParameters['PRODUCTS_HEADER_SHOW'] = [
        'PARENT' => 'DETAIL_SETTINGS',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_HEADER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRODUCTS_HEADER_SHOW'] === 'Y') {
        $arTemplateParameters['PRODUCTS_HEADER'] = [
            'PARENT' => 'DETAIL_SETTINGS',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_HEADER'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_HEADER_DEFAULT'),
        ];
    }

    $arIBlockType = CIBlockParameters::GetIBlockTypes();

    $arIBlocksList = Arrays::fromDBResult(CIBlock::GetList([], [
        'ACTIVE' => 'Y',
        'SITE_ID' => $sSite
    ]))->indexBy('ID');

    $arTemplateParameters['PRODUCTS_IBLOCK_TYPE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockType,
        'REFRESH' => 'Y',
        'ADDITIONAL_VALUES' => 'Y'
    ];

    if (!empty($arCurrentValues['PRODUCTS_IBLOCK_TYPE']))
        $arIBlock = $arIBlocksList->asArray(function ($key, $value) use (&$arCurrentValues) {
            if ($value['IBLOCK_TYPE_ID'] === $arCurrentValues['PRODUCTS_IBLOCK_TYPE'])
                return [
                    'key' => $key,
                    'value' => '['.$key.'] '.$value['NAME']
                ];

            return ['skip' => true];
        });

    if (!empty($arIBlock)) {
        $arTemplateParameters['PRODUCTS_IBLOCK_ID'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_PRODUCTS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arIBlock,
            'REFRESH' => 'Y',
            'ADDITIONAL_VALUES' => 'Y',
        ];
    }

    unset($arIBlockType, $arIBlocksList, $arIBlock);

    if (!empty($arCurrentValues['PRODUCTS_IBLOCK_ID']))
        include(__DIR__.'/parameters/products.php');
}