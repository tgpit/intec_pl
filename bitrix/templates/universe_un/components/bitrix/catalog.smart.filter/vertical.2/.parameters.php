<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

$bBase = false;
$bLite = false;
$arPrices = [];

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

if ($bBase) {
    $arPrices = CCatalogIBlockParameters::getPriceTypesList();
} else if ($bLite) {
    $arPrices = Arrays::fromDBResult(CStartShopPrice::GetList([], ['ACTIVE' => 'Y']))
        ->indexBy('ID')
        ->asArray(function ($sKey, $arPrice) {
            if (!empty($arPrice['CODE']))
                return [
                    'key' => $arPrice['CODE'],
                    'value' => '['.$arPrice['CODE'].'] '.$arPrice['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        });
}

$arTemplateParameters = [];

/** VISUAL */
$arTemplateParameters['COLLAPSED'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_COLLAPSED'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if (!empty($arPrices))
    $arTemplateParameters['PRICES_EXPANDED'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_PRICES_EXPANDED'),
        "TYPE" => "LIST",
        "MULTIPLE" => "Y",
        "VALUES" => $arPrices
    ];

$arTemplateParameters['TYPE_A_PRECISION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_TYPE_A_PRECISION'),
    'TYPE' => 'STRING',
    'DEFAULT' => 2
];

$arTemplateParameters['TYPE_B_PRECISION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_TYPE_B_PRECISION'),
    'TYPE' => 'STRING',
    'DEFAULT' => 2
];


$arTemplateParameters['SEARCH_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['SEARCH_SHOW'] === 'Y') {
    $arTemplateParameters['SEARCH_SHOW_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'all' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_MODE_ALL'),
            'quantity' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_MODE_QUANTITY'),
            'properties' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_MODE_PROPERTIES')
        ],
        'DEFAULT' => 'quantity',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['SEARCH_SHOW_MODE'] === 'quantity') {
        $arTemplateParameters['SEARCH_SHOW_QUANTITY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_QUANTITY'),
            'TYPE' => 'STRING',
            'DEFAULT' => 8
        ];
    } elseif ($arCurrentValues['SEARCH_SHOW_MODE'] === 'properties') {

        if (!empty($arCurrentValues['IBLOCK_ID'])) {
            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['sort' => 'asc'], [
                'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
                'ACTIVE' => 'Y'
            ]));

            $arTemplateParameters['SEARCH_SHOW_PROPERTIES'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_SMART_FILTER_VERTICAL_2_SEARCH_SHOW_PROPERTIES'),
                'TYPE' => 'LIST',
                'MULTIPLE' => 'Y',
                'VALUES' => $arProperties->asArray(function ($iIndex, $arProperty) {
                    $sCode = $arProperty['CODE'];

                    if (empty($sCode))
                        $sCode = $arProperty['ID'];

                    return [
                        'key' => $sCode,
                        'value' => '['.$sCode.'] '.$arProperty['NAME']
                    ];
                })
            ];
        }
    }
}