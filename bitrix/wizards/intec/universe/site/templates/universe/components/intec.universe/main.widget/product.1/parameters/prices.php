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
    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_CODE'),
        'TYPE' => 'LIST',
        'VALUES' => CCatalogIBlockParameters::getPriceTypesList(),
        'ADDITIONAL_VALUES' => 'Y',
        'MULTIPLE' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (Type::isArray($arCurrentValues['PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRICE_CODE']))) {
        $arTemplateParameters['CONVERT_CURRENCY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_CONVERT_CURRENCY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
            $arTemplateParameters['CURRENCY_ID'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_CURRENCY_ID'),
                'TYPE' => 'LIST',
                'VALUES' => CurrencyManager::getCurrencyList(),
                'DEFAULT' => CurrencyManager::getBaseCurrency(),
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arTemplateParameters['PRICE_VAT_INCLUDE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_VAT_INCLUDE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['PRICE_RANGE_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_RANGE_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
    }
} else if ($bLite) {
    $arPriceCodes = Arrays::fromDBResult(CStartShopPrice::GetList())->indexBy('CODE');

    $arTemplateParameters['PRICE_CODE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_CODE'),
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

    if (Type::isArray($arCurrentValues['PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRICE_CODE']))) {
        $arPriceProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]));

        foreach ($arCurrentValues['PRICE_CODE'] as $sCode) {
            if (!empty($sCode)) {
                $arPrice = $arPriceCodes->get($sCode);

                $arTemplateParameters['PROPERTY_OLD_PRICE_'.$sCode] = [
                    'PARENT' => 'BASE',
                    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PROPERTY_OLD_PRICE', [
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

        unset($arPriceProperties);

        $arTemplateParameters['CONVERT_CURRENCY'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_CONVERT_CURRENCY'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
            $arCurrencies = Arrays::fromDBResult(CStartShopCurrency::GetList(['SORT' => 'ASC']));

            $arTemplateParameters['CURRENCY_ID'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_CURRENCY_ID'),
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

if (Type::isArray($arCurrentValues['PRICE_CODE']) && !empty(array_filter($arCurrentValues['PRICE_CODE']))) {
    $bOldPrice = false;

    if ($bLite) {
        foreach ($arCurrentValues['PRICE_CODE'] as $sPriceCode) {
            if (!empty($arCurrentValues['PROPERTY_OLD_PRICE_' . $sPriceCode]) && !$bOldPrice) {
                $bOldPrice = true;

                break;
            }
        }
    }

    if ($bBase || ($bLite && $bOldPrice)) {
        $arTemplateParameters['PRICE_DISCOUNT_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_DISCOUNT_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['PRICE_DISCOUNT_SHOW'] === 'Y') {
            $arTemplateParameters['PRICE_DISCOUNT_PERCENT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_DISCOUNT_PERCENT'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
            $arTemplateParameters['PRICE_DISCOUNT_ECONOMY'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_PRICE_DISCOUNT_ECONOMY'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N'
            ];
        }
    }
}