<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'ACTION' => 'buy',
    'BUTTON_TOGGLE_ACTION' => 'buy',
    'PROPERTIES_SHOW' => 'Y',
    'COUNTER_SHOW' => 'Y',
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 3,
    'QUANTITY_BOUNDS_MANY' => 10,
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'OFFERS_USE' => 'Y',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_TEMPLATE' => null,
    'QUICK_VIEW_DETAIL' => 'N',
    'WIDE' => 'Y',
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'LAZY_LOAD' => 'N',
    'COMPARE_SHOW_INACTIVE' => 'N',
    'DELAY_SHOW_INACTIVE' => 'N',
    'RECALCULATION_PRICES_USE' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'])
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PROPERTIES' => [
        'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y',
        'AMOUNT' => ArrayHelper::fromRange([5, 0, 1, 2, 3, 4, 6], $arParams['PROPERTIES_AMOUNT'])
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'NAVIGATION' => [
        'TOP' => [
            'SHOW' => $arParams['DISPLAY_TOP_PAGER']
        ],
        'BOTTOM' => [
            'SHOW' => $arParams['DISPLAY_BOTTOM_PAGER']
        ],
        'LAZY' => [
            'BUTTON' => $arParams['LAZY_LOAD'] === 'Y',
            'SCROLL' => $arParams['LOAD_ON_SCROLL'] === 'Y'
        ]
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y'
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'average',
            'rating'
        ], $arParams['VOTE_MODE'])
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'number',
            'text',
            'logic'
        ], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'BUTTON_TOGGLE' => [
        'ACTION' => ArrayHelper::fromRange([
            'none',
            'buy'
        ], $arParams['BUTTON_TOGGLE_ACTION'])
    ],
    'PRICE' => [
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y'
    ],
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ],
        'ORDER' => [
            'TEXT' => $arParams['PURCHASE_ORDER_BUTTON_TEXT']
        ]
    ]
];

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_BUTTON_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_BUTTON_ORDER');

if (defined('EDITOR') || !class_exists('\\intec\\template\\Properties'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['QUANTITY']['BOUNDS']['FEW'] < 0)
    $arVisual['QUANTITY']['BOUNDS']['FEW'] = 3;

if ($arVisual['QUANTITY']['BOUNDS']['MANY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'])
    $arVisual['QUANTITY']['BOUNDS']['MANY'] = $arVisual['QUANTITY']['BOUNDS']['FEW'] + 1;

if (empty($arResult['NAV_STRING'])) {
    $arVisual['NAVIGATION']['TOP']['SHOW'] = false;
    $arVisual['NAVIGATION']['BOTTOM']['SHOW'] = false;
}

$arVisual['PROPERTIES']['COLUMNS'] = $arVisual['PROPERTIES']['AMOUNT'] - 2;
$arVisual['PROPERTIES']['COLUMNS'] = $arVisual['PROPERTIES']['COLUMNS'] > 0 ? $arVisual['PROPERTIES']['COLUMNS'] : 1;

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
    'order',
    'request'
], $arParams['ACTION']);

$arResult['DELAY'] = [
    'USE' => $arParams['DELAY_USE'] === 'Y',
    'SHOW_INACTIVE' => $arParams['DELAY_SHOW_INACTIVE'] === 'Y'
];

if ($arResult['ACTION'] !== 'buy' && $arResult['ACTION'] !== 'detail' || $bLite)
    $arResult['DELAY']['USE'] = false;

$arResult['COMPARE'] = [
    'USE' => $arParams['USE_COMPARE'] === 'Y' || $arParams['DISPLAY_COMPARE'],
    'CODE' => $arParams['COMPARE_NAME'],
    'SHOW_INACTIVE' => $arParams['COMPARE_SHOW_INACTIVE'] === 'Y'
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

$arResult['URL'] = [
    'BASKET' => ArrayHelper::getValue($arParams, 'BASKET_URL'),
    'CONSENT' => ArrayHelper::getValue($arParams, 'CONSENT_URL')
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

if ($bLite)
    include(__DIR__ . '/modifiers/lite/catalog.php');

$arResult['FORM'] = [
    'ORDER' => [
        'SHOW' => !empty($arParams['FORM_ID']),
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => $arParams['FORM_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
        ]
    ],
    'REQUEST' => [
        'SHOW' => !empty($arParams['FORM_REQUEST_ID']),
        'ID' => $arParams['FORM_REQUEST_ID'],
        'TEMPLATE' => $arParams['FORM_REQUEST_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_REQUEST_PROPERTY_PRODUCT']
        ]
    ]
];

foreach ($arResult['ITEMS'] as $iKey => &$arItem) {
    $arItem['DATA'] = [
        'OFFER' => !empty($arItem['OFFERS']),
        'ACTION' => $arResult['ACTION'],
        'MARKS' => [
            'NEW' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_MARKS_NEW'],
                'VALUE'
            ])),
            'HIT' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_MARKS_HIT'],
                'VALUE'
            ])),
            'RECOMMEND' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_MARKS_RECOMMEND'],
                'VALUE'
            ])),
            'SHARE' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_MARKS_SHARE'],
                'VALUE'
            ]))
        ],
        'QUANTITY' => [
            'SHOW' => $arVisual['QUANTITY']['SHOW']
        ],
        'PRICE' => [
            'SHOW' => true
        ],
        'COUNTER' => [
            'SHOW' => $arVisual['COUNTER']['SHOW']
        ],
        'DELAY' => [
            'USE' => $arResult['DELAY']['USE']
        ],
        'COMPARE' => [
            'USE' => $arResult['COMPARE']['USE']
        ]
    ];

    $arData = &$arItem['DATA'];

    if ($arData['ACTION'] === 'buy') {
        if (!empty($arParams['PROPERTY_ORDER_USE'])) {
            $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_ORDER_USE'],
                'VALUE'
            ]));

            if ($isOrder && $arResult['FORM']['ORDER']['SHOW'])
                $arData['ACTION'] = 'order';
        }

        if (!empty($arParams['PROPERTY_REQUEST_USE'])) {
            $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_REQUEST_USE'],
                'VALUE'
            ]));

            if ($isRequest && $arResult['FORM']['REQUEST']['SHOW'])
                $arData['ACTION'] = 'request';
        }
    }

    if ($arData['ACTION'] !== 'buy') {
        $arData['COUNTER']['SHOW'] = false;

        if ($arData['ACTION'] !== 'detail')
            $arData['DELAY']['USE'] = false;

        if ($arData['ACTION'] === 'request') {
            $arData['QUANTITY']['SHOW'] = false;
            $arData['PRICE']['SHOW'] = false;
        }
    }

    if ($arData['OFFER']) {
        if ($arVisual['OFFERS']['USE']) {
            foreach ($arItem['OFFERS'] as &$arOffer) {
                $arOffer['DATA'] = [
                    'OFFER' => false,
                    'ACTION' => $arData['ACTION'],
                    'DELAY' => [
                        'USE' => $arData['DELAY']['USE']
                    ],
                    'COMPARE' => [
                        'USE' => $arData['COMPARE']['USE']
                    ]
                ];
            }
        } else {
            $arData['ACTION'] = 'detail';
        }
    }

    $arItem['PICTURE'] = null;

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arItem['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arItem['PICTURE'] = $arItem['DETAIL_PICTURE'];

    unset($arData);
}

unset($arItem);

include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/quick.view.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;