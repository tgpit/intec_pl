<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var bool $bLite
 */

$arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

$arTemplateParameters['PRICE_CODE'] = [
    'PARENT' => 'PRICE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_IMAGES_LIST_1_PRICE_CODE'),
    'TYPE' => 'LIST',
    'VALUES' => $arPriceCodes->asArray(function ($key, $value) {
        if (!empty($value['CODE']))
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
            ];

        return ['skip' => true];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'MULTIPLE' => 'Y',
    'SIZE' => 5,
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_ID']) && !empty(array_filter($arCurrentValues['PRICE_CODE']))) {
    $arPriceProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
    ]));

    foreach ($arCurrentValues['PRICE_CODE'] as $sCode) {
        if (!empty($sCode)) {
            $arPrice = $arPriceCodes->get($sCode);

            $arTemplateParameters['PROPERTY_OLD_PRICE_'.$sCode] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_CATALOG_SECTION_IMAGES_LIST_1_PROPERTY_OLD_PRICE', [
                    '#NAME#' => $arPrice['LANG'][LANGUAGE_ID]['NAME']
                ]),
                'TYPE' => 'LIST',
                'VALUES' => $arPriceProperties->asArray(function ($key, $value) {
                    if ($value['PROPERTY_TYPE'] === 'N' && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
                        return [
                            'key' => $value['CODE'],
                            'value' => '['.$value['CODE'].'] '.$value['NAME']
                        ];

                    return ['skip' => true];
                }),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            unset($arPrice);
        }
    }
}

unset($arPriceCodes);

$arTemplateParameters['CONVERT_CURRENCY'] = [
    'PARENT' => 'PRICE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_IMAGES_LIST_1_CONVERT_CURRENCY'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
    $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList(['SORT' => 'ASC']));

    $arTemplateParameters['CURRENCY_ID'] = [
        'PARENT' => 'PRICE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_IMAGES_LIST_1_CURRENCY_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arCurrencies->asArray(function ($key, $value) {
            if (!empty($value['CODE']))
                return [
                    'key' => $value['CODE'],
                    'value' => '['.$value['CODE'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    unset($arCurrencies);
}