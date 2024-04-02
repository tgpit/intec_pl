<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

$bBase = false;
$bLite = false;

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

if ($bLite) {
    $arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

    $hPriceCodes = function ($sKey, $arProperty) {
        if (!empty($arProperty['CODE']))
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
            ];

        return ['skip' => true];
    };

    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PRICE_CODE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPriceCodes->asArray($hPriceCodes),
        'MULTIPLE' => 'Y',
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PRICE_CODE'])) {
        $arPrices = $arPriceCodes->asArray(function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                return [
                    'key' => $arProperty['CODE'],
                    'value' => $arProperty['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        });

        $arPropertiesPrice = Arrays::fromDBResult(CIBlockProperty::GetList([], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]))->indexBy('ID');

        foreach ($arCurrentValues['PRICE_CODE'] as $sPrice) {
            if (!empty($sPrice))
                $arTemplateParameters['PROPERTY_OLD_PRICE_' . $sPrice] = [
                    'PARENT' => 'PRICES',
                    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PROPERTY_OLD_PRICE', ['#PRICE_CODE#' => $arPrices[$sPrice].' ('.$sPrice.')']),
                    'TYPE' => 'LIST',
                    'VALUES' => $arPropertiesPrice->asArray(function ($sKey, $arProperty) {
                        if ($arProperty['PROPERTY_TYPE'] === 'N' && $arProperty['LIST_TYPE'] === 'L' && $arProperty['MULTIPLE'] === 'N') {
                            return [
                                'key' => $arProperty['CODE'],
                                'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                            ];
                        }

                        return ['skip' => true];
                    }),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
        }

        unset($arPrices);
        unset($arPropertiesPrice);
    }

    $arTemplateParameters['CONVERT_CURRENCY'] = [
        'PARENT' => 'PRICES',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_CONVERT_CURRENCY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
        $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList())->indexBy('CODE');

        $hCurrencies = function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        };

        $arTemplateParameters['CURRENCY_ID'] = [
            'PARENT' => 'PRICES',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_CURRENCY_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $arCurrencies->asArray($hCurrencies),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

if (!empty($arCurrentValues['IBLOCK_ID'])) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID'],
        'ACTIVE' => 'Y'
    ]))->indexBy('CODE');

    $hPropertyCheckbox = function ($key, $value) {
        if ($value['PROPERTY_TYPE'] === 'L' && $value['LIST_TYPE'] === 'C' && $value['MULTIPLE'] === 'N')
            return [
                'key' => $key,
                'value' => '['.$key.'] '.$value['NAME']
            ];

        return ['skip' => true];
    };

    $arPropertyCheckbox = $arProperties->asArray($hPropertyCheckbox);

    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckbox,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_REQUEST_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PROPERTY_REQUEST_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arPropertyCheckbox,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 4
];
$arTemplateParameters['BORDERS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_BORDERS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['NAME_ALIGN'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_NAME_ALIGN'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_LEFT'),
        'center' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_CENTER'),
        'right' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_RIGHT')
    ],
    'DEFAULT' => 'left'
];
$arTemplateParameters['PRICE_ALIGN'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PRICE_ALIGN'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_LEFT'),
        'center' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_CENTER'),
        'right' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ALIGN_RIGHT'),
    ],
    'DEFAULT' => 'left'
];
$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION_NONE'),
        'buy' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION_BUY'),
        'detail' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION_DETAIL'),
        'order' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION_ORDER'),
        'request' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_ACTION_REQUEST')
    ],
    'DEFAULT' => 'buy',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ACTION'] === 'buy') {
    $arTemplateParameters['COUNTER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_COUNTER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];
    if ($arCurrentValues['COUNTER_SHOW'] === 'Y') {
        $arTemplateParameters['RECALCULATION_PRICES_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_RECALCULATION_PRICES_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
}

if (Loader::includeModule('form')) {
    include(__DIR__.'/parameters/base/forms.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/lite/forms.php');
}