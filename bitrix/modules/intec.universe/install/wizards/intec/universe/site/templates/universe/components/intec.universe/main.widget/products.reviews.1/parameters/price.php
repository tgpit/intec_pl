<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Currency\CurrencyManager;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\Type;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 * @var bool $bBase
 * @var bool $bLite
 */

if ($bBase) {
    $arTemplateParameters['PRODUCTS_PRICE_CODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_PRICE_CODE'),
        'TYPE' => 'LIST',
        'VALUES' => CCatalogIBlockParameters::getPriceTypesList(),
        'ADDITIONAL_VALUES' => 'Y',
        'MULTIPLE' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (Type::isArray($arCurrentValues['PRODUCTS_PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRODUCTS_PRICE_CODE']))) {
        $arTemplateParameters['PRODUCTS_CONVERT_CURRENCY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_CONVERT_CURRENCY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PRODUCTS_CONVERT_CURRENCY'] === 'Y') {
            $arTemplateParameters['PRODUCTS_CURRENCY_ID'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_CURRENCY_ID'),
                'TYPE' => 'LIST',
                'VALUES' => CurrencyManager::getCurrencyList(),
                'DEFAULT' => CurrencyManager::getBaseCurrency(),
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arTemplateParameters['PRODUCTS_PRICE_VAT_INCLUDE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_PRICE_VAT_INCLUDE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['PRODUCTS_SHOW_PRICE_COUNT'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_SHOW_PRICE_COUNT'),
            'TYPE' => 'STRING',
            'DEFAULT' => 1
        ];
    }
} else if ($bLite) {
    $arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

    $arTemplateParameters['PRODUCTS_PRICE_CODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_PRICE_CODE'),
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
        'REFRESH' => 'Y'
    ];

    if (Type::isArray($arCurrentValues['PRODUCTS_PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRODUCTS_PRICE_CODE']))) {
        $arPriceProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['PRODUCTS_IBLOCK_ID']
        ]));

        foreach ($arCurrentValues['PRODUCTS_PRICE_CODE'] as $sCode) {
            if (!empty($sCode)) {
                $arPrice = $arPriceCodes->get($sCode);

                $arTemplateParameters['PRODUCTS_PROPERTY_OLD_PRICE_'.$sCode] = [
                    'PARENT' => 'BASE',
                    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_PROPERTY_OLD_PRICE', [
                        '#NAME#' => $arPrice['LANG'][LANGUAGE_ID]['NAME']
                    ]),
                    'TYPE' => 'LIST',
                    'VALUES' => $arPriceProperties->asArray(function ($key, $value) {
                        if (($value['PROPERTY_TYPE'] === 'N' || $value['PROPERTY_TYPE'] === 'S') && $value['LIST_TYPE'] === 'L' && $value['MULTIPLE'] === 'N')
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

        unset($arPriceProperties);

        $arTemplateParameters['PRODUCTS_CONVERT_CURRENCY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_CONVERT_CURRENCY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PRODUCTS_CONVERT_CURRENCY'] === 'Y') {
            $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList(['SORT' => 'ASC']));

            $arTemplateParameters['PRODUCTS_CURRENCY_ID'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_PRODUCTS_CURRENCY_ID'),
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
    }

    unset($arPriceCodes);
}