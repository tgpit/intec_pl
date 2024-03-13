<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Currency;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

Loc::loadMessages(__FILE__);

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arIBlocksTypes = CIBlockParameters::GetIBlockTypes();

$arTemplateParameters = [];

$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['IBLOCK_TYPE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlocksTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['IBLOCK_TYPE'])) {
    $arIBlocksFilter = [
        'ACTIVE' => 'Y'
    ];

    $sIBlockType = $arCurrentValues['IBLOCK_TYPE'];

    if (!empty($sIBlockType))
        $arIBlocksFilter['TYPE'] = $sIBlockType;

    $arIBlocks = Arrays::fromDBResult(CIBlock::GetList(['SORT' => 'ASC'], $arIBlocksFilter))->indexBy('ID');
    $arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

    $bOffersIblockExist = false;

    $arOfferProperties = Arrays::from([]);

    if (!empty($arIBlock)) {
        if ($bBase) {
            $arOfferIBlock = CCatalogSku::GetInfoByProductIBlock($arCurrentValues['IBLOCK_ID']);

            if (!empty($arOfferIBlock['IBLOCK_ID'])) {
                $arOfferProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                    ['SORT' => 'ASC'],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $arOfferIBlock['IBLOCK_ID']
                    ]
                ))->indexBy('ID');
                $bOffersIblockExist = true;
            }
        } else if (!$bBase) {
            $arOfferIBlock = CStartShopCatalog::GetByIBlock($arCurrentValues['IBLOCK_ID'])->Fetch();

            if (!empty($arOfferIBlock['OFFERS_IBLOCK'])) {
                $arOfferProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                    ['SORT' => 'ASC'],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $arOfferIBlock['OFFERS_IBLOCK']
                    ]
                ))->indexBy('ID');
                $bOffersIblockExist = true;
            }
        }
    }

    $arTemplateParameters['IBLOCK_ID'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlocks->asArray(function ($iId, $arIBlock) {
            return [
                'key' => $arIBlock['ID'],
                'value' => '['.$arIBlock['ID'].'] '.$arIBlock['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['IBLOCK_ID'])) {
        $arTemplateParameters['PRODUCT_DAY_TIMER_SHOW'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRODUCT_DAY_TIMER_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];

        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList(['SORT' => 'ASC'], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
        ]));

        $hPropertyDate = function ($key, $value) {
            if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['USER_TYPE'] === 'Date' && $value['MULTIPLE'] === 'N')
                return [
                    'key' => $value['CODE'],
                    'value' => '['.$value['CODE'].'] '.$value['NAME']
                ];

            return ['skip' => true];
        };

        $hPropertyDateTime = function ($key, $value) {
            if ($value['PROPERTY_TYPE'] === 'S' && $value['LIST_TYPE'] === 'L' && $value['USER_TYPE'] === 'DateTime' && $value['MULTIPLE'] === 'N')
                return [
                    'key' => $value['CODE'],
                    'value' => '['.$value['CODE'].'] '.$value['NAME']
                ];

            return ['skip' => true];
        };

        $arTemplateParameters['MODE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MODE'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'period' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MODE_PERIOD'),
                'day' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MODE_DAY')
            ],
            'DEFAULT' => 'period',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['MODE'] === 'period') {
            $arTemplateParameters['PROPERTY_SHOW_START'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_SHOW_START'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertyDateTime)
            ];
            $arTemplateParameters['PROPERTY_SHOW_END'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_SHOW_END'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertyDateTime)
            ];
        } else {
            $arTemplateParameters['PROPERTY_SHOW_END_DAY'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_SHOW_END_DAY'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertyDate)
            ];
            $arTemplateParameters['PROPERTY_SHOW_END_TIME'] = [
                'PARENT' => 'BASE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_SHOW_END_TIME'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertyDateTime)
            ];
        }

        if (!empty($arIBlock)) {
            $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
                'ACTIVE' => 'Y',
                'IBLOCK_ID' => $arIBlock['ID']
            ]))->indexBy('ID');

            $hProperties = function ($sKey, $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                    ];

                if ($arProperty['PROPERTY_TYPE'] === 'L' && $arProperty['LIST_TYPE'] === 'L')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                    ];

                if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['LIST_TYPE'] === 'L')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                    ];

                return ['skip' => true];
            };

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
            $hPropertiesFile = function ($sKey, $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                    ];

                return ['skip' => true];
            };
            $hPropertiesList = function ($sKey, $arProperty) {
                if (empty($arProperty['CODE']))
                    return ['skip' => true];

                if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'L')
                    return ['skip' => true];

                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                ];
            };

            $arTemplateParameters['PROPERTY_ORDER_USE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_ORDER_USE'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_MARKS_HIT'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
            $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_MARKS_NEW'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
            $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_MARKS_RECOMMEND'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
            $arTemplateParameters['PROPERTY_MARKS_SHARE'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_MARKS_SHARE'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesCheckbox),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];
            $arTemplateParameters['PROPERTY_PICTURES'] = [
                'PARENT' => 'DATA_SOURCE',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_PICTURES'),
                'TYPE' => 'LIST',
                'VALUES' => $arProperties->asArray($hPropertiesFile),
                'ADDITIONAL_VALUES' => 'Y',
                'REFRESH' => 'Y'
            ];

            if ($bOffersIblockExist) {
                $arTemplateParameters['OFFERS_PROPERTY_PICTURES'] = [
                    'PARENT' => 'DATA_SOURCE',
                    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_PROPERTY_PICTURES'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arOfferProperties->asArray($hPropertiesFile),
                    'ADDITIONAL_VALUES' => 'Y',
                    'REFRESH' => 'Y'
                ];
                $arTemplateParameters['OFFERS_PROPERTY_CODE'] = [
                    'PARENT' => 'DATA_SOURCE',
                    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_PROPERTY_CODE'),
                    'TYPE' => 'LIST',
                    'MULTIPLE' => 'Y',
                    'VALUES' => $arOfferProperties->asArray($hProperties),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }
        }

        if ($bBase) {
            $arPrices = CCatalogIBlockParameters::getPriceTypesList();
            $arTemplateParameters['PRICE_CODE'] = [
                'PARENT' => 'PRICES',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRICE_CODE'),
                'TYPE' => 'LIST',
                'MULTIPLE' => 'Y',
                'VALUES' => $arPrices,
                'REFRESH' => 'Y'
            ];

            $arTemplateParameters['CONVERT_CURRENCY'] = [
                'PARENT' => 'PRICES',
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_CONVERT_CURRENCY'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['CONVERT_CURRENCY'] === 'Y') {
                $arTemplateParameters['CURRENCY_ID'] = [
                    'PARENT' => 'PRICES',
                    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_CURRENCY_ID'),
                    'TYPE' => 'LIST',
                    'VALUES' => Currency\CurrencyManager::getCurrencyList(),
                    'DEFAULT' => Currency\CurrencyManager::getBaseCurrency(),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }
        } else if ($bLite) {
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
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRICE_CODE'),
                'TYPE' => 'LIST',
                'VALUES' => $arPriceCodes->asArray($hPriceCodes),
                'MULTIPLE' => 'Y',
                'ADDITIONAL_VALUES' => 'Y'
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
                            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PROPERTY_OLD_PRICE', ['#PRICE_CODE#' => $arPrices[$sPrice].' ('.$sPrice.')']),
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
                'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_CONVERT_CURRENCY'),
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
                    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_CURRENCY_ID'),
                    'TYPE' => 'LIST',
                    'VALUES' => $arCurrencies->asArray($hCurrencies),
                    'ADDITIONAL_VALUES' => 'Y'
                ];
            }
        }
    }
}

$arTemplateParameters['MEASURE_SHOW'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MEASURE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['OFFERS_LIMIT'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_LIMIT'),
    'TYPE' => 'STRING'
];
$arTemplateParameters['BASKET_URL'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BASKET_URL'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['SECTION_URL'] = CIBlockParameters::GetPathTemplateParam(
    'SECTION',
    'SECTION_URL',
    Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_SECTION_URL'),
    '',
    'URL_TEMPLATES'
);

$arTemplateParameters['DETAIL_URL'] = CIBlockParameters::GetPathTemplateParam(
    'DETAIL',
    'DETAIL_URL',
    Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_DETAIL_URL'),
    '',
    'URL_TEMPLATES'
);

$arTemplateParameters['USE_PRICE_COUNT'] = [
    'PARENT' => 'PRICES',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_USE_PRICE_COUNT'),
    'TYPE' => 'CHECKBOX'
];
$arTemplateParameters['PRICE_VAT_INCLUDE'] = [
    'PARENT' => 'PRICES',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRICE_VAT_INCLUDE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['BLOCKS_HEADER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BLOCKS_HEADER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BLOCKS_HEADER_SHOW'] === 'Y') {
    $arTemplateParameters['BLOCKS_HEADER_TEXT'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BLOCKS_HEADER_TEXT'),
        'TYPE' => 'TEXT',
        'DEFAULT' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BLOCKS_HEADER_TEXT_DEFAULT')
    ];
    if ($arCurrentValues['PRODUCT_DAY_TIMER_SHOW'] !== 'Y') {
        $arTemplateParameters['BLOCKS_HEADER_ALIGN'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BLOCKS_HEADER_ALIGN'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ALIGN_LEFT'),
                'center' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ALIGN_CENTER'),
                'right' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ALIGN_RIGHT')
            ],
            'DEFAULT' => 'left'
        ];
    }
}

$arTemplateParameters['SLIDER_LOOP_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_SLIDER_LOOP_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($bBase) {
    $arTemplateParameters['DELAY_USE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_DELAY_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['MARKS_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MARKS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['MARKS_SHOW'] === 'Y') {

    $rsTemplates = CComponentUtil::GetTemplatesList('intec.universe:main.markers');

    $arMarksTemplates = [];

    foreach ($rsTemplates as $arKey => $arValue) {
        if ($arValue['NAME'] !== '.default') {
            $arMarksTemplates[$arValue['NAME']] = $arValue['NAME'];
        }
    }

    unset($rsTemplates, $arKey, $arValue);

    $arTemplateParameters['MARKS_TEMPLATE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MARKS_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arMarksTemplates,
        'DEFAULT' => 'template.1'
    ];

    $arTemplateParameters['MARKS_ORIENTATION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MARKS_ORIENTATION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'horizontal' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MARKS_ORIENTATION_HORIZONTAL'),
            'vertical' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MARKS_ORIENTATION_VERTICAL')
        ],
        'DEFAULT' => 'horizontal'
    ];
}

$arTemplateParameters['IMAGE_ASPECT_RATIO'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IMAGE_ASPECT_RATIO'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1:1' => '1:1',
        '4:5' => '4:5',
        '3:4' => '3:4',
        '5:7' => '5:7',
        '4:6' => '4:6',
        '3:5' => '3:5'
    ],
    'ADDITIONAL_VALUES' => 'Y',
    'DEFAULT' => '1:1'
];

if (!empty($arCurrentValues['PROPERTY_PICTURES']) || !empty($arCurrentValues['OFFERS_PROPERTY_PICTURES'])) {
    $arTemplateParameters['IMAGE_SLIDER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IMAGE_SLIDER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];

    if ($arCurrentValues['IMAGE_SLIDER_SHOW'] === 'Y') {
        $arTemplateParameters['IMAGE_SLIDER_NAV_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IMAGE_SLIDER_NAV_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];
        $arTemplateParameters['IMAGE_SLIDER_OVERLAY_USE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_IMAGE_SLIDER_OVERLAY_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];
    }
}

$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ACTION_NONE'),
        'buy' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ACTION_BUY'),
        'detail' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ACTION_DETAIL'),
        'order' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_ACTION_ORDER')
    ],
    'DEFAULT' => 'none',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ACTION'] === 'buy') {
    $arTemplateParameters['COUNTER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COUNTER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['COUNTER_SHOW'] === 'Y') {
        $arTemplateParameters['COUNTER_MESSAGE_MAX_SHOW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COUNTER_MESSAGE_MAX_SHOW'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'Y'
        ];
    }
}

if (Loader::includeModule('form')) {
    include(__DIR__.'/parameters/base/forms.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/lite/forms.php');
}

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_CONSENT_URL'),
    'TYPE' => 'STRING'
];

if ($bOffersIblockExist) {
    $arTemplateParameters['OFFERS_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['OFFERS_USE'] === 'Y') {
        $arTemplateParameters['OFFERS_PROPERTY_PICTURE_DIRECTORY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_OFFERS_PROPERTY_PICTURE_DIRECTORY'),
            'TYPE' => 'LIST',
            'VALUES' => $arOfferProperties->asArray(function ($sKey, $arProperty) {
                if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['USER_TYPE'] === 'directory')
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                    ];

                return ['skip' => true];
            }),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['VOTE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_VOTE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VOTE_SHOW'] === 'Y') {
    $arTemplateParameters['VOTE_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_VOTE_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rating' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_VOTE_MODE_RATING'),
            'average' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_VOTE_MODE_AVERAGE')
        ],
        'DEFAULT' => 'rating'
    ];
}

$arTemplateParameters['QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUANTITY_SHOW'] === 'Y') {
    $arTemplateParameters['QUANTITY_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'number' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_MODE_NUMBER'),
            'text' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_MODE_TEXT'),
            'logic' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_MODE_LOGIC')
        ],
        'DEFAULT' => 'number',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['QUANTITY_MODE'] === 'text') {
        $arTemplateParameters['QUANTITY_BOUNDS_FEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_BOUNDS_FEW'),
            'TYPE' => 'STRING',
        ];
        $arTemplateParameters['QUANTITY_BOUNDS_MANY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_QUANTITY_BOUNDS_MANY'),
            'TYPE' => 'STRING',
        ];
    }
}

$arTemplateParameters['USE_COMPARE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_USE_COMPARE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['USE_COMPARE'] === 'Y') {
    $arTemplateParameters['COMPARE_PATH'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COMPARE_PATH'),
        'TYPE' => 'STRING'
    ];
    $arTemplateParameters['COMPARE_NAME'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_COMPARE_NAME'),
        'TYPE' => 'STRING'
    ];
}

include(__DIR__.'/parameters/quick.view.php');
include(__DIR__.'/parameters/order.fast.php');
include(__DIR__.'/parameters/regionality.php');

$arTemplateParameters['PURCHASE_BASKET_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PURCHASE_BASKET_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PURCHASE_BASKET_BUTTON_TEXT_DEFAULT')
];

$arTemplateParameters['PURCHASE_ORDER_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PURCHASE_ORDER_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PURCHASE_ORDER_BUTTON_TEXT_DEFAULT')
];

$arTemplateParameters['PRODUCT_SLIDER_NAVIGATION_VIEW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRODUCT_SLIDER_NAVIGATION_VIEW'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'shadow' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRODUCT_SLIDER_NAVIGATION_VIEW_SHADOW'),
        'border' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_PRODUCT_SLIDER_NAVIGATION_VIEW_BORDER'),
    ],
    'DEFAULT' => 'shadow'
];