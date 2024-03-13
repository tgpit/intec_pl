<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

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

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

Loc::loadMessages(__FILE__);

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
$arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);

$arParametersCommon = [
    'FORM_ID',
    'FORM_TEMPLATE',
    'FORM_PROPERTY_PRODUCT',
    'FORM_REQUEST_ID',
    'FORM_REQUEST_TEMPLATE',
    'FORM_REQUEST_PROPERTY_PRODUCT',
    'PROPERTY_MARKS_RECOMMEND',
    'PROPERTY_MARKS_NEW',
    'PROPERTY_MARKS_HIT',
    'PROPERTY_MARKS_SHARE',
    'PROPERTY_ORDER_USE',
    'PROPERTY_REQUEST_USE',
    'CONSENT_URL',
    'LAZY_LOAD',
    'LOAD_ON_SCROLL',
    'VOTE_MODE',
    'DELAY_USE',
    'QUANTITY_MODE',
    'QUANTITY_BOUNDS_FEW',
    'QUANTITY_BOUNDS_MANY',

    'VIDEO_IBLOCK_TYPE',
    'VIDEO_IBLOCK_ID',
    'VIDEO_PROPERTY_URL',
    'SERVICES_IBLOCK_TYPE',
    'SERVICES_IBLOCK_ID',
    'REVIEWS_IBLOCK_TYPE',
    'REVIEWS_IBLOCK_ID',
    'REVIEWS_PROPERTY_ELEMENT_ID',
    'REVIEWS_USE_CAPTCHA',
    'PROPERTY_ARTICLE',
    'PROPERTY_BRAND',
    'PROPERTY_PICTURES',
    'PROPERTY_SERVICES',
    'PROPERTY_DOCUMENTS',
    'PROPERTY_ADDITIONAL',
    'PROPERTY_ASSOCIATED',
    'PROPERTY_RECOMMENDED',
    'PROPERTY_VIDEO',
    'OFFERS_PROPERTY_ARTICLE',
    'OFFERS_PROPERTY_PICTURES',
    'OFFERS_PROPERTY_PICTURE_DIRECTORY',
    'OFFERS_VARIABLE_SELECT',

    'CONVERT_CURRENCY',
    'CURRENCY_ID',
    'PRICE_CODE'
];

$arTemplateParameters = [];
$arTemplateParameters['SETTINGS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SETTINGS_USE'),
    'TYPE' => 'CHECKBOX'
];

$arTemplateParameters['OFFERS_VARIABLE_SELECT'] = [
    'PARENT' => 'DETAIL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_OFFERS_VARIABLE_SELECT'),
    'TYPE' => 'STRING'
];

$arProperties = Arrays::from([]);
$arOffersProperties = Arrays::from([]);

if (!empty($arIBlock)) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arIBlock['ID']
    ]))->indexBy('ID');

    $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_MARKS_RECOMMEND'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_MARKS_NEW'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_MARKS_HIT'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_MARKS_SHARE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_MARKS_SHARE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_REQUEST_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_REQUEST_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'L' || $arProperty['LIST_TYPE'] !== 'C')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_ARTICLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'S')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_BRAND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_BRAND'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_SERVICES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_SERVICES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_DOCUMENTS'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_DOCUMENTS'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_ADDITIONAL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_ADDITIONAL'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_ASSOCIATED'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_ASSOCIATED'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_RECOMMENDED'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_RECOMMENDED'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PROPERTY_VIDEO'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_VIDEO'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($bBase) {
        $arTemplateParameters['DELAY_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_DELAY_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N'
        ];

        $arOffersIBlock = CCatalogSku::GetInfoByProductIBlock($arIBlock['ID']);

        if (!empty($arOffersIBlock['IBLOCK_ID'])) {
            $arOffersProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                ['SORT' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $arOffersIBlock['IBLOCK_ID']
                ]
            ))->indexBy('ID');
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
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PRICE_CODE'),
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

            foreach ($arCurrentValues['PRICE_CODE'] as $sPrice) {
                if (!empty($sPrice))
                    $arTemplateParameters['PROPERTY_OLD_PRICE_' . $sPrice] = [
                        'PARENT' => 'PRICES',
                        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_OLD_PRICE', ['#PRICE_CODE#' => $arPrices[$sPrice]." (".$sPrice.")"]),
                        'TYPE' => 'LIST',
                        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
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
        }

        $arTemplateParameters['CONVERT_CURRENCY'] = [
            'PARENT' => 'PRICES',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_CONVERT_CURRENCY'),
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
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_CURRENCY_ID'),
                'TYPE' => 'LIST',
                'VALUES' => $arCurrencies->asArray($hCurrencies),
                'ADDITIONAL_VALUES' => 'Y'
            ];
        }

        $arOffersIBlock = CStartShopCatalog::GetByIBlock($arIBlock['ID'])->Fetch();

        if (!empty($arOffersIBlock['OFFERS_IBLOCK'])) {
            $arOffersProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                ['SORT' => 'ASC'],
                [
                    'ACTIVE' => 'Y',
                    'IBLOCK_ID' => $arOffersIBlock['OFFERS_IBLOCK']
                ]
            ))->indexBy('ID');
        }
        /**/
        $arTemplateParameters['COMPARE_OFFERS_PROPERTY_CODE'] = [
            'PARENT' => 'COMPARE_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_OFFERS_PROPERTY_CODE'),
            'TYPE' => 'LIST',
            'VALUES' => $arOffersProperties->asArray(function ($sKey, $arProperty) {
                if (empty($arProperty['CODE']))
                    return ['skip' => true];

                return [
                    'key' => $arProperty['CODE'],
                    'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
                ];
            }),
            'MULTIPLE' => 'Y',
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }

    $arTemplateParameters['OFFERS_PROPERTY_ARTICLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_OFFERS_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arOffersProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'S')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['OFFERS_PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_OFFERS_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arOffersProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    if (!empty($arOffersProperties)) {
        $arTemplateParameters['OFFERS_PROPERTY_PICTURE_DIRECTORY'] = [
            'PARENT' => 'OFFERS_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_OFFERS_PROPERTY_PICTURE_DIRECTORY'),
            'TYPE' => 'LIST',
            'VALUES' => $arOffersProperties->asArray(function ($sKey, $arProperty) use ($bLite) {
                if ($arProperty['PROPERTY_TYPE'] === 'S' && $arProperty['USER_TYPE'] === 'directory')
                    return [
                        'key' => $bLite ? $arProperty['ID'] : $arProperty['CODE'],
                        'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                    ];

                return ['skip' => true];
            }),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arIBlockTypes = CIBlockParameters::GetIBlockTypes();

$arTemplateParameters['VIDEO_IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_VIDEO_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
$arIBlockType = $arIBlocks->asArray();

$hGetIblockByType = function ($sType) use ($arIBlockType) {
    $arIblockList = [];

    foreach ($arIBlockType as $sKey => $arIblock) {
        if ($arIblock['IBLOCK_TYPE_ID'] !== $sType) continue;

        $arIblockList[$arIblock['ID']] = $arIblock['NAME'];
    }

    return $arIblockList;
};

if (!empty($arCurrentValues['VIDEO_IBLOCK_TYPE'])) {
    $arTemplateParameters['VIDEO_IBLOCK_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_VIDEO_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $hGetIblockByType($arCurrentValues['VIDEO_IBLOCK_TYPE'] ),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['VIDEO_IBLOCK_ID'])) {
    $arProperties = null;
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['VIDEO_IBLOCK_ID']
    ]))->indexBy('ID');

    $arTemplateParameters['VIDEO_PROPERTY_URL'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_VIDEO_PROPERTY_URL'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if (empty($arProperty['CODE']))
                return ['skip' => true];

            if ($arProperty['PROPERTY_TYPE'] !== 'S')
                return ['skip' => true];

            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];
}

$arTemplateParameters['GALLERY_VIDEO_IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['GALLERY_VIDEO_IBLOCK_TYPE'])) {
    $arTemplateParameters['GALLERY_VIDEO_IBLOCK_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $hGetIblockByType($arCurrentValues['GALLERY_VIDEO_IBLOCK_TYPE']),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['GALLERY_VIDEO_CONTROLS_SHOW'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_CONTROLS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['GALLERY_VIDEO_IBLOCK_ID'])) {
        $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['IBLOCK_ID']
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

        if ($bBase) {
            $arOffersIBlock = CCatalogSku::GetInfoByProductIBlock($arIBlock['ID']);

            if (!empty($arOffersIBlock['IBLOCK_ID'])) {
                $arOffersProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                    ['SORT' => 'ASC'],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $arOffersIBlock['IBLOCK_ID']
                    ]
                ))->indexBy('ID');
            }
        } else if ($bLite) {
            $arOffersIBlock = CStartShopCatalog::GetByIBlock($arIBlock['ID'])->Fetch();

            if (!empty($arOffersIBlock['OFFERS_IBLOCK'])) {
                $arOffersProperties = Arrays::fromDBResult(CIBlockProperty::GetList(
                    ['SORT' => 'ASC'],
                    [
                        'ACTIVE' => 'Y',
                        'IBLOCK_ID' => $arOffersIBlock['OFFERS_IBLOCK']
                    ]
                ))->indexBy('ID');
            }
        }

        $arVideoProperties = Arrays::fromDBResult(CIBlockProperty::GetList([
            'SORT' => 'ASC'
        ], [
            'ACTIVE' => 'Y',
            'IBLOCK_ID' => $arCurrentValues['GALLERY_VIDEO_IBLOCK_ID']
        ]));

        $hPropertiesFile = function ($iIndex, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'F' && $arProperty['LIST_TYPE'] === 'L' && $arProperty['MULTIPLE'] === 'N')
                return [
                    'key' => $arProperty['ID'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        };

        $hPropertyText = function ($sKey, $arProperty) {
            if (!empty($arProperty['CODE']))
                if ($arProperty['PROPERTY_TYPE'] == 'S')
                    return [
                        'key' => $arProperty['ID'],
                        'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                    ];

            return ['skip' => true];
        };

        $arTemplateParameters['GALLERY_VIDEO_PROPERTY_LINK'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_PROPERTY_LINK'),
            'TYPE' => 'LIST',
            'VALUES' => $arProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['GALLERY_VIDEO_OFFER_PROPERTY_LINK'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_OFFER_PROPERTY_LINK'),
            'TYPE' => 'LIST',
            'VALUES' => $arOffersProperties->asArray($hPropertiesElements),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['GALLERY_VIDEO_PROPERTY_URL'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_PROPERTY_URL'),
            'TYPE' => 'LIST',
            'VALUES' => $arVideoProperties->asArray($hPropertyText),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['GALLERY_VIDEO_PROPERTY_FILE_MP4'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_PROPERTY_FILE_MP4'),
            'TYPE' => 'LIST',
            'VALUES' => $arVideoProperties->asArray($hPropertiesFile),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['GALLERY_VIDEO_PROPERTY_FILE_WEBM'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_PROPERTY_FILE_WEBM'),
            'TYPE' => 'LIST',
            'VALUES' => $arVideoProperties->asArray($hPropertiesFile),
            'ADDITIONAL_VALUES' => 'Y'
        ];

        $arTemplateParameters['GALLERY_VIDEO_PROPERTY_FILE_OGV'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_GALLERY_VIDEO_PROPERTY_FILE_OGV'),
            'TYPE' => 'LIST',
            'VALUES' => $arVideoProperties->asArray($hPropertiesFile),
            'ADDITIONAL_VALUES' => 'Y'
        ];
    }
}

$arTemplateParameters['SERVICES_IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SERVICES_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['SERVICES_IBLOCK_TYPE'])) {
    $arTemplateParameters['SERVICES_IBLOCK_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SERVICES_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $hGetIblockByType($arCurrentValues['SERVICES_IBLOCK_TYPE']),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

$arTemplateParameters['REVIEWS_IBLOCK_TYPE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_REVIEWS_IBLOCK_TYPE'),
    'TYPE' => 'LIST',
    'VALUES' => $arIBlockTypes,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['REVIEWS_IBLOCK_TYPE'])) {
    $arTemplateParameters['REVIEWS_IBLOCK_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_REVIEWS_IBLOCK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $hGetIblockByType($arCurrentValues['REVIEWS_IBLOCK_TYPE']),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

if (!empty($arCurrentValues['REVIEWS_IBLOCK_ID'])) {
    $arProperties = null;
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arCurrentValues['REVIEWS_IBLOCK_ID']
    ]))->indexBy('ID');

    $arTemplateParameters['REVIEWS_PROPERTY_ELEMENT_ID'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_REVIEWS_PROPERTY_ELEMENT_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray(function ($sKey, $arProperty) {
            if ($arProperty['PROPERTY_TYPE'] === 'E' && $arProperty['LIST_TYPE'] === 'L')
                return [
                    'key' => $arProperty['CODE'],
                    'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                ];

            return ['skip' => true];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    $arTemplateParameters['REVIEWS_USE_CAPTCHA'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_REVIEWS_USE_CAPTCHA'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['COMPARE_ACTION'] = [
    'PARENT' => 'COMPARE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_ACTION_NONE'),
        'buy' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_ACTION_BUY'),
        'detail' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_ACTION_DETAIL')
    ]
];

$arTemplateParameters['COMPARE_LAZYLOAD_USE'] = [
    'PARENT' => 'COMPARE',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_COMPARE_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arTemplateParameters['ROOT_LAYOUT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_ROOT_LAYOUT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1' => 1,
        '2' => 2,
    ],
    'DEFAULT' => 1
];

$arTemplateParameters['SECTIONS_LAYOUT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_SECTIONS_LAYOUT'),
    'TYPE' => 'LIST',
    'VALUES' => [
        '1' => 1,
        '2' => 2,
    ],
    'DEFAULT' => 1
];

include(__DIR__.'/parameters/menu.php');
include(__DIR__.'/parameters/sections.php');
include(__DIR__.'/parameters/filter.php');
include(__DIR__.'/parameters/elements.php');
include(__DIR__.'/parameters/navigation.php');
include(__DIR__.'/parameters/element.php');
include(__DIR__.'/parameters/vote.php');
include(__DIR__.'/parameters/quantity.php');
include(__DIR__.'/parameters/order.fast.php');
include(__DIR__.'/parameters/quick.view.php');
include(__DIR__.'/parameters/tags.php');
include(__DIR__.'/parameters/regionality.php');
include(__DIR__.'/parameters/sef.php');
include(__DIR__.'/parameters/search.sections.php');
include(__DIR__.'/parameters/interest.products.php');
include(__DIR__.'/parameters/timer.php');

$arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_USE'] = [
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_USE'] === 'Y') {
    $arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_TYPE'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_TYPE'),
        'TYPE' => 'LIST',
        'VALUES' => $arIBlockTypes,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_TYPE'])) {
        $arTemplateParameters['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID'] = [
            'PARENT' => 'ADDITIONAL_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID'),
            'TYPE' => 'LIST',
            'VALUES' => $hGetIblockByType($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_TYPE']),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }

    if (!empty($arCurrentValues['BLOCK_ON_EMPTY_SEARCH_RESULTS_IBLOCK_ID'])) {
        include(__DIR__.'/parameters/search.elements.php');
    }
}

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_CONSENT_URL'),
    'TYPE' => 'STRING'
];


//articles display options
$arTemplateParameters['ADDITIONAL_ARTICLES_SHOW'] = [
    'PARENT' => 'LIST_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ADDITIONAL_ARTICLES_SHOW'] == 'Y') {

    $arFields = array();
    $rsFields = CUserTypeEntity::GetList(['SORT' => 'ASC'], array(
        'ENTITY_ID' => 'IBLOCK_'.$arCurrentValues['IBLOCK_ID'].'_SECTION',
        'USER_TYPE_ID' => 'iblock_element'
    ));

    while ($arField = $rsFields->Fetch())
        $arFields[$arField['FIELD_NAME']] = $arField['FIELD_NAME'];

    $arTemplateParameters['PROPERTY_ADDITIONAL_ARTICLES'] = [
        'PARENT' => 'LIST_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_PROPERTY_ADDITIONAL_ARTICLES'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if (!empty($arCurrentValues['PROPERTY_ADDITIONAL_ARTICLES'])) {

        if ($arCurrentValues['ADDITIONAL_ARTICLES_SHOW'] === 'Y') {
            $arTemplateParameters['ADDITIONAL_ARTICLES_HEADER_SHOW'] = [
                'PARENT' => 'LIST_SETTINGS',
                'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES_HEADER_SHOW'),
                'TYPE' => 'CHECKBOX',
                'DEFAULT' => 'N',
                'REFRESH' => 'Y'
            ];

            if ($arCurrentValues['ADDITIONAL_ARTICLES_HEADER_SHOW'] === 'Y') {
                $arTemplateParameters['ADDITIONAL_ARTICLES_HEADER_TEXT'] = [
                    'PARENT' => 'LIST_SETTINGS',
                    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES_HEADER_TEXT'),
                    'TYPE' => 'STRING',
                    'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_ADDITIONAL_ARTICLES_HEADER_TEXT_DEFAULT')
                ];
            }

            include(__DIR__ . '/parameters/articles.php');
        }
    }
}

if ($bBase) {
    $arStores = Arrays::fromDBResult(CCatalogStore::GetList(['ID' => 'ASC'], ['ACTIVE' => 'Y'], false, false, []))
        ->indexBy('ID');

    $arTemplateParameters['STORES'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_STORES'),
        'TYPE' => 'LIST',
        'VALUES' => $arStores->asArray(function ($sKey, $arStore) {
            return [
                'key' => $arStore['ID'],
                'value' => '[' . $arStore['ID'] . '] ' . $arStore['TITLE']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'MULTIPLE' => 'Y'
    ];

    $arTemplateParameters['STORE_BLOCK_DESCRIPTION_USE'] = [
        'PARENT' => 'STORE_SETTINGS',
        'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_STORE_BLOCK_DESCRIPTION_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['STORE_BLOCK_DESCRIPTION_USE'] === 'Y') {
        $arTemplateParameters['STORE_BLOCK_DESCRIPTION_TEXT'] = [
            'PARENT' => 'STORE_SETTINGS',
            'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_STORE_BLOCK_DESCRIPTION_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_CATALOG_CATALOG_1_STORE_BLOCK_DESCRIPTION_TEXT_DEFAULT')
        ];
    }
}

include(__DIR__.'/parameters/hidden.php');

if (Loader::includeModule('form')) {
    include(__DIR__.'/parameters/base/forms.php');
} else if (Loader::includeModule('intec.startshop')) {
    include(__DIR__.'/parameters/lite/forms.php');
}


$arTemplateParameters['LOAD_ON_SCROLL'] = [
    'PARENT' => 'PAGER_SETTINGS',
    'NAME' => Loc::getMessage('C_CATALOG_CATALOG_1_LOAD_ON_SCROLL'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];