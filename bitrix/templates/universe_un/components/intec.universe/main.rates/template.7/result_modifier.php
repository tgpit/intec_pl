<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_ADVANTAGES' => null,
    'PROPERTY_MARK_NEW' => null,
    'PROPERTY_MARK_HIT' => null,
    'PROPERTY_MARK_RECOMMEND' => null,
    'PROPERTY_MARK_SHARE' => null,
    'MARKS_SHOW' => 'N',
    'PROPERTIES_SHOW' => 'N',
    'ADVANTAGES_SHOW' => 'N',
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
    ],
    'TABS' => [
        'USE' => $arParams['TABS_USE'] === 'Y',
        'POSITION' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['TABS_POSITION'])
    ],
    'COLUMNS' => ArrayHelper::fromRange([4, 3], $arParams['COLUMNS']),
    'LINK' => [
        'USE' => $arParams['LINK_USE'] === 'Y',
        'BLANK' => $arParams['LINK_USE'] === 'Y' && $arParams['LINK_BLANK'] === 'Y'
    ],
    'HEADER' => [
        'SHOW' => $arParams['ITEM_HEADER_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ITEM_HEADER'])
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y'
    ],
    'PROPERTIES' => [
        'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y'
    ],
    'ADVANTAGES' => [
        'SHOW' => $arParams['ADVANTAGES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ADVANTAGES'])
    ],
    'PRICE' => [
        'SHOW' => $arParams['PRICE_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE']),
        'BASE' => [
            'SHOW' => $arParams['PRICE_BASE_SHOW'] === 'Y'
        ],
        'DISCOUNT' => [
            'SHOW' => $arParams['PRICE_DISCOUNT_SHOW'] === 'Y' && !empty($arParams['PROPERTY_PRICE_DISCOUNT'])
        ],
        'DIFFERENCE' => [
            'SHOW' => $arParams['PRICE_DIFFERENCE_SHOW'] === 'Y'
        ]
    ],
    'SLIDER' => [
        'LOOP' => $arParams['SLIDER_LOOP'] === 'Y',
        'DOTS' => count($arResult['ITEMS']) > 1
    ]
];

$arResult['FORM'] = [
    'ORDER' => [
        'USE' => $arParams['FORM_ORDER_USE'] === 'Y' && !empty($arParams['FORM_ORDER_ID']),
        'ID' => $arParams['FORM_ORDER_ID'],
        'TITLE' => $arParams['FORM_ORDER_TITLE'],
        'TEMPLATE' => !empty($arParams['FORM_ORDER_TEMPLATE']) ? $arParams['FORM_ORDER_TEMPLATE'] : '.default',
        'FIELDS' => [
            'INSERT' => $arParams['FORM_ORDER_PROPERTY_INSERT']
        ],
    ]
];

$arResult['CONSENT'] = [
    'USE' => $arParams['CONSENT_USE'] === 'Y' && !empty($arParams['CONSENT_URL']),
    'VALUE' => StringHelper::replaceMacros($arParams['CONSENT_URL'], [
        'SITE_DIR' => SITE_DIR
    ])
];

$priceFormat = function ($price) {
    $difference = $price - Type::toInteger($price);
    $decimal = 0;

    if ($difference > 0)
        $decimal = 2;

    return number_format($price, $decimal, '.', '&nbsp');
};

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PICTURE' => null,
        'MARKS' => [
            'SHOW' => false,
            'VALUES' => [
                'NEW' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARK_NEW'],
                    'VALUE'
                ])),
                'HIT' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARK_HIT'],
                    'VALUE'
                ])),
                'RECOMMEND' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARK_RECOMMEND'],
                    'VALUE'
                ])),
                'SHARE' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARK_SHARE'],
                    'VALUE'
                ]))
            ]
        ],
        'HEADER' => [
            'SHOW' => false,
            'VALUE' => null
        ],
        'PROPERTIES' => [
            'SHOW' => false,
            'VALUES' => []
        ],
        'ADVANTAGES' => [
            'SHOW' => false,
            'EXPAND' => false,
            'VALUES' => []
        ],
        'PRICE' => [
            'SHOW' => false,
            'CURRENCY' => null,
            'BASE' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'DISCOUNT' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'DIFFERENCE' => [
                'SHOW' => false,
                'VALUE' => null,
                'PRINT' => null
            ],
            'TOTAL' => [
                'VALUE' => null,
                'PRINT' => null
            ]
        ],
        'FORM' => [
            'ORDER' => [
                'USE' => &$arResult['FORM']['ORDER']['USE']
            ]
        ]
    ];

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arItem['DATA']['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arItem['DATA']['PICTURE'] = $arItem['DETAIL_PICTURE'];

    if (
        $arItem['DATA']['MARKS']['VALUES']['NEW'] ||
        $arItem['DATA']['MARKS']['VALUES']['HIT'] ||
        $arItem['DATA']['MARKS']['VALUES']['RECOMMEND'] ||
        $arItem['DATA']['MARKS']['VALUES']['SHARE']
    )
        $arItem['DATA']['MARKS']['SHOW'] = $arVisual['MARKS']['SHOW'];

    if (!empty($arItem['DISPLAY_PROPERTIES'])) {
        foreach ($arItem['DISPLAY_PROPERTIES'] as &$arProperty) {
            if (!empty($arProperty['DISPLAY_VALUE']))
                $arItem['DATA']['PROPERTIES']['VALUES'][] = &$arProperty;
        }

        unset($arProperty);

        if (!empty($arItem['DATA']['PROPERTIES']['VALUES']))
            $arItem['DATA']['PROPERTIES']['SHOW'] = $arVisual['PROPERTIES']['SHOW'];
    }

    if (!empty($arParams['PROPERTY_ITEM_HEADER'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_ITEM_HEADER']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['HEADER']['SHOW'] = $arVisual['HEADER']['SHOW'];
                $arItem['DATA']['HEADER']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_ADVANTAGES'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_ADVANTAGES']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (!Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = [$arProperty['DISPLAY_VALUE']];


                $arItem['DATA']['ADVANTAGES']['EXPAND'] = count($arProperty['DISPLAY_VALUE']) > 1;
                $arItem['DATA']['ADVANTAGES']['VALUES'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);

        if (!empty($arItem['DATA']['ADVANTAGES']['VALUES']))
            $arItem['DATA']['ADVANTAGES']['SHOW'] = $arVisual['ADVANTAGES']['SHOW'];
    }

    if (!empty($arParams['PROPERTY_PRICE'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PRICE']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = round(Type::toFloat($arProperty['DISPLAY_VALUE']), 2);

                if ($arProperty['DISPLAY_VALUE'] <= 0)
                    $arProperty['DISPLAY_VALUE'] = null;

                $arItem['DATA']['PRICE']['BASE']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_PRICE_DISCOUNT'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PRICE_DISCOUNT']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arProperty['DISPLAY_VALUE'] = round(Type::toFloat($arProperty['DISPLAY_VALUE']), 2);

                if ($arProperty['DISPLAY_VALUE'] < 0)
                    $arProperty['DISPLAY_VALUE'] = null;
                else if ($arProperty['DISPLAY_VALUE'] > 100)
                    $arProperty['DISPLAY_VALUE'] = Type::toFloat(100);

                $arItem['DATA']['PRICE']['DISCOUNT']['VALUE'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arParams['PROPERTY_PRICE_CURRENCY'])) {
        $arProperty = ArrayHelper::getValue($arItem['PROPERTIES'], $arParams['PROPERTY_PRICE_CURRENCY']);

        if (!empty($arProperty)) {
            $arProperty = CIBlockFormatProperties::GetDisplayValue(
                $arItem,
                $arProperty,
                false
            );

            if (!empty($arProperty['DISPLAY_VALUE'])) {
                if (Type::isArray($arProperty['DISPLAY_VALUE']))
                    $arProperty['DISPLAY_VALUE'] = ArrayHelper::getFirstValue($arProperty['DISPLAY_VALUE']);

                $arItem['DATA']['PRICE']['CURRENCY'] = $arProperty['DISPLAY_VALUE'];
            }
        }

        unset($arProperty);
    }

    if (!empty($arItem['DATA']['PRICE']['BASE']['VALUE'])) {
        $price = &$arItem['DATA']['PRICE'];

        $price['SHOW'] = $arVisual['PRICE']['SHOW'];

        if (empty($price['DISCOUNT']['VALUE'])) {
            $price['TOTAL']['VALUE'] = $price['BASE']['VALUE'];
        } else {
            $price['TOTAL']['VALUE'] = $price['BASE']['VALUE'] - ($price['DISCOUNT']['VALUE'] * ($price['BASE']['VALUE'] / 100));
            $price['DIFFERENCE']['VALUE'] = $price['BASE']['VALUE'] - $price['TOTAL']['VALUE'];

            $price['DISCOUNT']['PRINT'] = $price['DISCOUNT']['VALUE'].'%';
            $price['DIFFERENCE']['PRINT'] = $priceFormat($price['DIFFERENCE']['VALUE']);

            $price['DISCOUNT']['SHOW'] = $arVisual['PRICE']['DISCOUNT']['SHOW'];
            $price['DIFFERENCE']['SHOW'] = $arVisual['PRICE']['DIFFERENCE']['SHOW'];
        }

        $price['BASE']['PRINT'] = $priceFormat($price['BASE']['VALUE']);
        $price['TOTAL']['PRINT'] = $priceFormat($price['TOTAL']['VALUE']);

        if ($price['BASE']['VALUE'] > $price['TOTAL']['VALUE'])
            $price['BASE']['SHOW'] = $arVisual['PRICE']['BASE']['SHOW'];

        unset($price);
    }
}

unset($priceFormat, $arItem);

$arResult['VISUAL'] = $arVisual;

unset($arVisual);