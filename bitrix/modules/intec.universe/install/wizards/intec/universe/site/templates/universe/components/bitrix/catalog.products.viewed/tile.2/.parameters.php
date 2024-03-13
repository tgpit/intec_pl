<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 * @var string $componentName
 * @var string $componentTemplate
 * @var string $siteTemplate
 */

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core') || !Loader::includeModule('catalog') || !Loader::includeModule('sale'))
    return;

$arIBlocks = Arrays::fromDBResult(CIBlock::GetList([], ['ACTIVE' => 'Y']))->indexBy('ID');
$arIBlock = $arIBlocks->get($arCurrentValues['IBLOCK_ID']);
$bOffersIblockExist = false;
$arOfferProperties = [];

if (!empty($arIBlock)) {
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
}

$arTemplateParameters = [];

$arTemplateParameters['COMPARE_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_COMPARE_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => 'compare'
];

/** DATA_SOURCE */
if (!empty($arIBlock)) {
    $arProperties = Arrays::fromDBResult(CIBlockProperty::GetList([], [
        'ACTIVE' => 'Y',
        'IBLOCK_ID' => $arIBlock['ID']
    ]))->indexBy('ID');

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
    $hPropertiesString = function ($sKey, $arProperty) {
        if ($arProperty['PROPERTY_TYPE'] === 'S')
            return [
                'key' => $arProperty['CODE'],
                'value' => '['.$arProperty['CODE'].'] '.$arProperty['NAME']
            ];

        return ['skip' => true];
    };

    $hPropertiesCheckbox = $arProperties->asArray($hPropertiesCheckbox);

    $arTemplateParameters['PROPERTY_ORDER_USE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_ORDER_USE'),
        'TYPE' => 'LIST',
        'VALUES' => $hPropertiesCheckbox,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_HIT'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_MARKS_HIT'),
        'TYPE' => 'LIST',
        'VALUES' => $hPropertiesCheckbox,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_NEW'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_MARKS_NEW'),
        'TYPE' => 'LIST',
        'VALUES' => $hPropertiesCheckbox,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_MARKS_RECOMMEND'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_MARKS_RECOMMEND'),
        'TYPE' => 'LIST',
        'VALUES' => $hPropertiesCheckbox,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_ARTICLE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_ARTICLE'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesString),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PROPERTY_PICTURES'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PROPERTY_PICTURES'),
        'TYPE' => 'LIST',
        'VALUES' => $arProperties->asArray($hPropertiesFile),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    if ($bOffersIblockExist) {
        $arTemplateParameters['OFFERS_PROPERTY_PICTURES'] = [
            'PARENT' => 'DATA_SOURCE',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_PROPERTY_PICTURES'),
            'TYPE' => 'LIST',
            'VALUES' => $arOfferProperties->asArray($hPropertiesFile),
            'ADDITIONAL_VALUES' => 'Y',
            'REFRESH' => 'Y'
        ];
    }
}

/** VISUAL */
$arTemplateParameters['LAZYLOAD_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_LAZYLOAD_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['COLUMNS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_COLUMNS'),
    'TYPE' => 'LIST',
    'VALUES' => [
        2 => '2',
        3 => '3',
        4 => '4'
    ],
    'DEFAULT' => 3
];
$arTemplateParameters['COLUMNS_MOBILE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_COLUMNS_MOBILE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        1 => 1,
        2 => 2
    ],
    'DEFAULT' => 1
];
$arTemplateParameters['DELAY_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_DELAY_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['ARTICLE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ARTICLE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['BORDERS'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_BORDERS'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['BORDERS_STYLE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_BORDERS_STYLE'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'squared' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_BORDERS_STYLE_SQUARED'),
        'rounded' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_BORDERS_STYLE_ROUNDED')
    ],
    'DEFAULT' => 'squared'
];
$arTemplateParameters['MARKS_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_MARKS_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['MARKS_SHOW'] === 'Y') {
    $arTemplateParameters['MARKS_ORIENTATION'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_MARKS_ORIENTATION'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'horizontal' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_MARKS_ORIENTATION_HORIZONTAL'),
            'vertical' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_MARKS_ORIENTATION_VERTICAL')
        ],
        'DEFAULT' => 'horizontal'
    ];
}

$arTemplateParameters['NAME_POSITION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_NAME_POSITION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'top' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_POSITION_TOP'),
        'middle' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_POSITION_MIDDLE')
    ],
    'DEFAULT' => 'middle'
];
$arTemplateParameters['NAME_ALIGN'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_NAME_ALIGN'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'left' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_LEFT'),
        'center' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_CENTER'),
        'right' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_RIGHT')
    ],
    'DEFAULT' => 'left'
];
$arTemplateParameters['PRICE_ALIGN'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PRICE_ALIGN'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'start' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_LEFT'),
        'center' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_CENTER'),
        'end' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_RIGHT')
    ],
    'DEFAULT' => 'start'
];

if (!empty($arCurrentValues['PROPERTY_PICTURES']) || !empty($arCurrentValues['OFFERS_PROPERTY_PICTURES'])) {
    $arTemplateParameters['IMAGE_SLIDER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_IMAGE_SLIDER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

$arTemplateParameters['ACTION'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ACTION'),
    'TYPE' => 'LIST',
    'VALUES' => [
        'none' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ACTION_NONE'),
        'buy' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ACTION_BUY'),
        'detail' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ACTION_DETAIL'),
        'order' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ACTION_ORDER')
    ],
    'DEFAULT' => 'none',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['ACTION'] === 'buy') {
    $arTemplateParameters['COUNTER_SHOW'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_COUNTER_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ];
}

if (Loader::includeModule('form'))
    include(__DIR__.'/parameters/base/forms.php');

$arTemplateParameters['CONSENT_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_CONSENT_URL'),
    'TYPE' => 'STRING'
];

if ($bOffersIblockExist) {
    $arTemplateParameters['OFFERS_USE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_USE'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['OFFERS_USE'] === 'Y') {
        $arTemplateParameters['OFFERS_ALIGN'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_ALIGN'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'left' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_LEFT'),
                'center' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_CENTER'),
                'right' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_RIGHT')
            ],
            'DEFAULT' => 'left'
        ];
        $arTemplateParameters['OFFERS_VIEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_VIEW'),
            'TYPE' => 'LIST',
            'VALUES' => [
                'default' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_VIEW_DEFAULT'),
                'extended' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_VIEW_EXTENDED')
            ],
            'DEFAULT' => 'default',
            'REFRESH' => 'Y'
        ];

        if ($arCurrentValues['OFFERS_VIEW'] === 'extended' && !empty($arCurrentValues['IBLOCK_ID'])) {
            $hOfferProperties = function ($sKey, $arProperty) {
                if (!empty($arProperty['CODE']))
                    return [
                        'key' => $arProperty['CODE'],
                        'value' => '[' . $arProperty['CODE'] . '] ' . $arProperty['NAME']
                    ];

                return ['skip' => true];
            };

            $arOfferProperties = $arOfferProperties->asArray($hOfferProperties);

            $arTemplateParameters['OFFERS_VIEW_EXTENDED_LEFT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_VIEW_EXTENDED_LEFT'),
                'TYPE' => 'LIST',
                'VALUES' => $arOfferProperties,
                'ADDITIONAL_VALUES' => 'Y'
            ];
            $arTemplateParameters['OFFERS_VIEW_EXTENDED_RIGHT'] = [
                'PARENT' => 'VISUAL',
                'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_OFFERS_VIEW_EXTENDED_RIGHT'),
                'TYPE' => 'LIST',
                'VALUES' => $arOfferProperties,
                'ADDITIONAL_VALUES' => 'Y'
            ];

            if ($arCurrentValues['IMAGE_SLIDER_SHOW'] === 'Y') {
                $arTemplateParameters['IMAGE_SLIDER_NAV_HIDE'] = [
                    'PARENT' => 'VISUAL',
                    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_IMAGE_SLIDER_NAV_HIDE'),
                    'TYPE' => 'CHECKBOX',
                    'DEFAULT' => 'N'
                ];
            }
        }
    }
}

$arTemplateParameters['IMAGE_ASPECT_RATIO'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_IMAGE_ASPECT_RATIO'),
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
$arTemplateParameters['VOTE_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_VOTE_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['VOTE_SHOW'] === 'Y') {
    $arTemplateParameters['VOTE_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_VOTE_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
    $arTemplateParameters['VOTE_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_VOTE_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'rating' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_VOTE_MODE_RATING'),
            'average' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_VOTE_MODE_AVERAGE')
        ],
        'DEFAULT' => 'rating'
    ];
}

$arTemplateParameters['RECALCULATION_PRICES_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_RECALCULATION_PRICES_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];
$arTemplateParameters['QUANTITY_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['QUANTITY_SHOW'] === 'Y') {
    $arTemplateParameters['QUANTITY_MODE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_MODE'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'number' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_MODE_NUMBER'),
            'text' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_MODE_TEXT'),
            'logic' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_MODE_LOGIC')
        ],
        'DEFAULT' => 'number',
        'REFRESH' => 'Y'
    ];

    if ($arCurrentValues['QUANTITY_MODE'] === 'text') {
        $arTemplateParameters['QUANTITY_BOUNDS_FEW'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_BOUNDS_FEW'),
            'TYPE' => 'STRING',
        ];
        $arTemplateParameters['QUANTITY_BOUNDS_MANY'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_BOUNDS_MANY'),
            'TYPE' => 'STRING',
        ];
    }

    $arTemplateParameters['QUANTITY_ALIGN'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_QUANTITY_ALIGN'),
        'TYPE' => 'LIST',
        'VALUES' => [
            'left' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_LEFT'),
            'center' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_CENTER'),
            'right' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_ALIGN_RIGHT')
        ],
        'DEFAULT' => 'left'
    ];
}

/** PAGER_SETTINGS */
include(__DIR__.'/parameters/quick.view.php');

$arTemplateParameters['PURCHASE_BASKET_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PURCHASE_BASKET_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PURCHASE_BASKET_BUTTON_TEXT_DEFAULT')
];
$arTemplateParameters['PURCHASE_ORDER_BUTTON_TEXT'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PURCHASE_ORDER_BUTTON_TEXT'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_CATALOG_PRODUCTS_VIEWED_TILE_2_PURCHASE_ORDER_BUTTON_TEXT_DEFAULT')
];