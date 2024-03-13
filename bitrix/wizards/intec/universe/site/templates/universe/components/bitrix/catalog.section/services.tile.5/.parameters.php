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
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]));
    
    $hPropertyTextSingle = function ($key, $arValue) {
        if (($arValue['PROPERTY_TYPE'] === 'S' || $arValue['PROPERTY_TYPE'] === 'N') && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N' && empty($arValue['USER_TYPE']))
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    $hPropertyListSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] === 'L' && $arValue['LIST_TYPE'] === 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
    
    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyListSingle = $arProperties->asArray($hPropertyListSingle);
    
    $arTemplateParameters['PROPERTY_PRICE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTY_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
        $arTemplateParameters['PROPERTY_PRICE_OLD'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTY_PRICE_OLD'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_CURRENCY'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTY_CURRENCY'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyListSingle,
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_FORMAT'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTY_FORMAT'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }
}

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 3
];

$arTemplateParameters['PROPERTIES_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTIES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PROPERTIES_SHOW'] === 'Y') {
    $arTemplateParameters['PROPERTIES_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PROPERTIES_COUNT'),
        'TYPE' => 'STRING'
    ];
}

$arTemplateParameters['PREVIEW_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PREVIEW_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arCurrentValues['IBLOCK_ID']) && !empty($arCurrentValues['PROPERTY_PRICE'])) {
    $arTemplateParameters['PRICE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PRICE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRICE_SHOW'] === 'Y') {
        $arTemplateParameters['PRICE_OLD_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_PRICE_OLD_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        if (!empty($arCurrentValues['PROPERTY_FORMAT'])) {
            $arTemplateParameters['FORMAT_USE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_FORMAT_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }
    }
}

$arTemplateParameters['LAZY_LOAD'] = [
    'PARENT' => 'PAGER_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_LAZY_LOAD'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LAZY_LOAD'] === 'Y') {
    $arTemplateParameters['LOAD_ON_SCROLL'] = [
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_TILE_5_LOAD_ON_SCROLL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}