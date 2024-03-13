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

    $hPropertyFileSingle = function ($key, $arValue) {
        if ($arValue['PROPERTY_TYPE'] == 'F' && $arValue['LIST_TYPE'] == 'L' && $arValue['MULTIPLE'] === 'N')
            return [
                'key' => $arValue['CODE'],
                'value' => '['.$arValue['CODE'].'] '.$arValue['NAME']
            ];

        return ['skip' => true];
    };
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

    $arPropertyFileSingle = $arProperties->asArray($hPropertyFileSingle);
    $arPropertyTextSingle = $arProperties->asArray($hPropertyTextSingle);
    $arPropertyListSingle = $arProperties->asArray($hPropertyListSingle);

    $arTemplateParameters['SVG_FILE_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_SVG_FILE_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SVG_FILE_USE'] === 'Y') {
        $arTemplateParameters['SVG_FILE_COLOR'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_SVG_FILE_COLOR'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'original' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_SVG_FILE_COLOR_ORIGINAL'),
                'theme' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_SVG_FILE_COLOR_THEME'),
            ],
            'DEFAULT' => 'theme'
        ];

        $arTemplateParameters['PROPERTY_SVG_FILE'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTY_SVG_FILE'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyFileSingle,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }

    $arTemplateParameters['PROPERTY_PRICE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTY_PRICE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyTextSingle,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_PRICE'])) {
        $arTemplateParameters['PROPERTY_PRICE_OLD'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTY_PRICE_OLD'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_CURRENCY'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTY_CURRENCY'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyListSingle,
            'ADDITIONAL_VALUES' => 'Y'
        ];
        $arTemplateParameters['PROPERTY_FORMAT'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTY_FORMAT'),
            'TYPE' => 'LIST',
            'VALUES' => $arPropertyTextSingle,
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }
}

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PICTURE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PICTURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['PROPERTIES_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTIES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['PROPERTIES_SHOW'] === 'Y') {
    $arTemplateParameters['PROPERTIES_COUNT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PROPERTIES_COUNT'),
        'TYPE' => 'STRING'
    ];
}

if (!empty($arCurrentValues['IBLOCK_ID']) && !empty($arCurrentValues['PROPERTY_PRICE'])) {
    $arTemplateParameters['PRICE_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PRICE_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['PRICE_SHOW'] === 'Y') {
        $arTemplateParameters['PRICE_OLD_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_PRICE_OLD_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        if (!empty($arCurrentValues['PROPERTY_FORMAT'])) {
            $arTemplateParameters['FORMAT_USE'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_FORMAT_USE'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }
    }
}

$arTemplateParameters['LAZY_LOAD'] = [
    'PARENT' => 'PAGER_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_LAZY_LOAD'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['LAZY_LOAD'] === 'Y') {
    $arTemplateParameters['LOAD_ON_SCROLL'] = [
        'PARENT' => 'PAGER_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_SERVICES_LIST_6_LOAD_ON_SCROLL'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}