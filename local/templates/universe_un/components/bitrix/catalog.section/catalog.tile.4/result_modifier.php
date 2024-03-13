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

$bMeasures = false;

if ($bBase && Loader::includeModule('intec.measures'))
    $bMeasures = true;

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'PROPERTY_STORES_SHOW' => null,
    'PROPERTY_PICTURES' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'OFFERS_PROPERTY_STORES_SHOW' => null,
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'COLUMNS_MOBILE' => 1,
    'DELAY_USE' => 'N',
    'BORDERS' => 'N',
    'BORDERS_STYLE' => 'squared',
    'COUNTER_SHOW' => 'N',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'MARKS_SHOW' => 'N',
    'MARKS_ORIENTATION' => 'horizontal',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_DETAIL' => 'N',
    'QUICK_VIEW_VIEW' => 'right',
    'QUICK_VIEW_TEMPLATE' => null,
    'IMAGE_SLIDER_SHOW' => 'N',
    'ACTION' => 'buy',
    'OFFERS_USE' => 'N',
    'IMAGE_ASPECT_RATIO' => '1:1',
    'IMAGE_SLIDER_NAV_SHOW' => 'N',
    'IMAGE_SLIDER_OVERLAY_USE' => 'Y',
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => null,
    'QUANTITY_BOUNDS_MANY' => null,
    'ARTICLE_SHOW' => 'N',
    'MEASURE_SHOW' => 'N',
    'RECALCULATION_PRICES_USE' => 'N',
    'SECTION_TIMER_SHOW' => 'N',
    'MEASURES_USE' => 'Y',
    'PURCHASE_REQUEST_BUTTON_TEXT' => null
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arPosition = [
    'left',
    'center',
    'right'
];

$arCodes = [
    'MARKS' => [
        'HIT' => $arParams['PROPERTY_MARKS_HIT'],
        'NEW' => $arParams['PROPERTY_MARKS_NEW'],
        'RECOMMEND' => $arParams['PROPERTY_MARKS_RECOMMEND'],
        'SHARE' => $arParams['PROPERTY_MARKS_SHARE']
    ],
    'PICTURES' => $arParams['PROPERTY_PICTURES'],
    'OFFERS' => [
        'PICTURES' => $arParams['OFFERS_PROPERTY_PICTURES']
    ]
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'BORDERS' => [
        'USE' => $arParams['BORDERS'] === 'Y',
        'STYLE' => ArrayHelper::fromRange(['squared', 'rounded'], $arParams['BORDERS_STYLE'])
    ],
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([3, 2, 4], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS_MOBILE'])
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 100,
        'SLIDER' => $arParams['IMAGE_SLIDER_SHOW'] === 'Y',
        'NAV' => $arParams['IMAGE_SLIDER_NAV_SHOW'] === 'Y',
        'OVERLAY' => $arParams['IMAGE_SLIDER_OVERLAY_USE'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y',
        'MESSAGE' => [
            'MAX' => [
                'SHOW' => $arParams['COUNTER_MESSAGE_MAX_SHOW'] === 'Y'
            ]
        ]
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y' && (!empty($arCodes['MARKS']['HIT']) || !empty($arCodes['MARKS']['NEW']) || !empty($arCodes['MARKS']['RECOMMEND'])|| !empty($arCodes['MARKS']['SHARE'])),
        'ORIENTATION' => ArrayHelper::fromRange(['horizontal', 'vertical'], $arParams['MARKS_ORIENTATION'])
    ],
    'COMPARE' => [
        'USE' => $arParams['USE_COMPARE'] === 'Y',
        'CODE' => $arParams['COMPARE_NAME']
    ],
    'DELAY' => [
        'USE' => $arParams['DELAY_USE'] === 'Y' && $bBase
    ],
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y'
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
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['average', 'rating'], $arParams['VOTE_MODE'])
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'ARTICLE' => [
        'SHOW' => $arParams['ARTICLE_SHOW'] === 'Y'
    ],
    'MEASURE' => [
        'SHOW' => $arParams['MEASURE_SHOW'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => true,
        'RANGE' => $arParams['USE_PRICE_COUNT'],
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
        'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
    ],
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ],
        'ORDER' => [
            'TEXT' => $arParams['PURCHASE_ORDER_BUTTON_TEXT']
        ],
        'MOBILE' => [
            'USE' => $arParams['PURCHASE_ORDER_BUTTON_MOBILE_USE'] === 'Y' && $arParams['COUNTER_SHOW'] === 'N'
        ],
        'REQUEST' => [
            'TEXT' => $arParams['PURCHASE_REQUEST_BUTTON_TEXT']
        ]
    ],
    'TIMER' => [
        'SHOW' => $arParams['SECTION_TIMER_SHOW'] === 'Y' && $bBase
    ],
    'MEASURES' => [
        'USE' => $arParams['MEASURES_USE'] === 'Y'
    ],
    'GIFTS' => [
        'SHOW' => $arResult['CATALOG'] && $arParams['USE_GIFTS_SECTION'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'POSITION' => ArrayHelper::fromRange(['top', 'middle', 'bottom'], $arParams['GIFTS_SECTION_LIST_POSITION']),
        'VIEW' => ArrayHelper::fromRange(['1', '2', '3', '4', '5'], $arParams['GIFTS_SECTION_LIST_VIEW']),
        'COLUMNS' => ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['GIFTS_SECTION_LIST_COLUMNS']),
        'QUANTITY' => $arParams['GIFTS_SECTION_LIST_QUANTITY'] <= 0 ? 20 : $arParams['GIFTS_SECTION_LIST_COLUMNS']
    ]
];

if (!$bMeasures)
    $arVisual['MEASURES']['USE'] = false;

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_BASKET_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_REQUEST');

if (defined('EDITOR') || !class_exists('\\intec\\template\\Properties'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['QUANTITY']['BOUNDS']['FEW'] < 0)
    $arVisual['QUANTITY']['BOUNDS']['FEW'] = 5;

if ($arVisual['QUANTITY']['BOUNDS']['MANY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'])
    $arVisual['QUANTITY']['BOUNDS']['MANY'] = $arVisual['QUANTITY']['BOUNDS']['FEW'] + 10;

if (empty($arResult['NAV_STRING'])) {
    $arVisual['NAVIGATION']['TOP']['SHOW'] = false;
    $arVisual['NAVIGATION']['BOTTOM']['SHOW'] = false;
}

$arRatio = explode(':', $arParams['IMAGE_ASPECT_RATIO']);

if (count($arRatio) >= 2) {
    $arRatio[0] = Type::toFloat($arRatio[0]);
    $arRatio[1] = Type::toFloat($arRatio[1]);

    if ($arRatio[0] <= 0)
        $arRatio[0] = 1;

    if ($arRatio[1] <= 0)
        $arRatio[1] = 1;

    $arVisual['IMAGE']['ASPECT_RATIO'] = floor(100 * $arRatio[1] / $arRatio[0]);
}

unset($arRatio);

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
    'order',
    'request'
], $arParams['ACTION']);

$arResult['DELAY'] = [
    'USE' => $arParams['DELAY_USE'] === 'Y' && $bBase
];

include(__DIR__.'/modifiers/order.fast.php');

if ($arResult['ACTION'] !== 'buy' && $arResult['ACTION'] !== 'detail') {
    $arResult['ORDER_FAST']['USE'] = false;

    if ($bLite)
        $arResult['DELAY']['USE'] = false;
}

$arResult['COMPARE'] = [
    'USE' => $arParams['USE_COMPARE'] === 'Y',
    'CODE' => $arParams['COMPARE_NAME']
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

$arUrl['URL'] = [
    'BASKET' => $arParams['BASKET_URL'],
    'CONSENT' => $arParams['CONSENT_URL']
];

foreach ($arUrl['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);


if ($bSeo) {
    $arSeo = $APPLICATION->IncludeComponent('intec.seo:iblocks.elements.modifier', '', [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_ID' => $arParams['SECTION_ID'],
        'SECTION_CODE' => $arParams['SECTION_CODE'],
        'ITEMS' => $arResult['ITEMS'],
        'PAGE_USE' => 'Y',
        'PAGE_COUNT' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageCount : null,
        'PAGE_SIZE' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageSize : null,
        'PAGE_NUMBER' => !empty($arResult['NAV_RESULT']) ? $arResult['NAV_RESULT']->NavPageNomer : null,
        'CACHE_TYPE' => 'N'
    ], $component);

    if (!empty($arSeo))
        $arResult['ITEMS'] = $arSeo['ITEMS'];

    foreach ($arResult['ITEMS'] as $arItem) {
        $arItems[] = $arItem['ID'];
    }

    $GLOBALS['arCatalogSectionsExtendingFilterMainItems'] = $arItems;
}

$arResult['FORMS'] = [
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

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['VISUAL'] = [
        'OFFER' => !empty($arItem['OFFERS']),
        'ACTION' => $arResult['ACTION'],
        'MARKS' => [
            'SHOW' => false,
            'VALUES' => [
                'RECOMMEND' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arCodes['MARKS']['RECOMMEND'],
                    'VALUE'
                ])),
                'NEW' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arCodes['MARKS']['NEW'],
                    'VALUE'
                ])),
                'HIT' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arCodes['MARKS']['HIT'],
                    'VALUE'
                ])),
                'SHARE' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arCodes['MARKS']['SHARE'],
                    'VALUE'
                ])),
            ]
        ],
        'DELAY' => [
            'USE' => $arResult['DELAY']['USE']
        ],
        'COMPARE' => [
            'USE' => $arResult['COMPARE']['USE']
        ],
        'QUANTITY' => [
            'SHOW' => $arVisual['QUANTITY']['SHOW']
        ],
        'PRICE' => [
            'SHOW' => $arVisual['PRICE']['SHOW']
        ],
        'TIMER' => [
            'SHOW' => $arVisual['TIMER']['SHOW']
        ],
        'MEASURES' => [
            'USE' => false
        ],
        'COUNTER' => [
            'SHOW' => $arVisual['COUNTER']['SHOW']
        ],
        'ORDER_FAST' => [
            'USE' => $arResult['ORDER_FAST']['USE'] && $arItem['CAN_BUY']
        ]
    ];

    $visual = &$arItem['VISUAL'];

    if ($visual['MARKS']['VALUES']['RECOMMEND'] || $visual['MARKS']['VALUES']['NEW'] || $visual['MARKS']['VALUES']['HIT'] || $visual['MARKS']['VALUES']['SHARE'])
        $visual['MARKS']['SHOW'] = true;

    if ($visual['ACTION'] !== 'none') {
        if ($visual['ACTION'] !== 'order') {
            $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_ORDER_USE'],
                'VALUE'
            ]));

            if ($isOrder && $arResult['FORMS']['ORDER']['SHOW'])
                $visual['ACTION'] = 'order';

            unset($isOrder);
        }

        if ($visual['ACTION'] !== 'request') {
            $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_REQUEST_USE'],
                'VALUE'
            ]));

            if ($isRequest && $arResult['FORMS']['REQUEST']['SHOW'])
                $visual['ACTION'] = 'request';

            unset($isRequest);
        }
    }

    if ($visual['ACTION'] !== 'buy') {
        $visual['COUNTER']['SHOW'] = false;
        $visual['MEASURES']['USE'] = false;

        if ($visual['ACTION'] !== 'detail') {
            $visual['DELAY']['USE'] = false;
            $visual['ORDER_FAST']['USE'] = false;
        }

        if ($visual['ACTION'] === 'request') {
            $visual['PRICE']['SHOW'] = false;
            $visual['TIMER']['SHOW'] = false;
        }
    }

    if (!$arVisual['OFFERS']['USE'] && $visual['OFFER']) {
        if ($visual['ACTION'] === 'buy')
            $visual['ACTION'] = 'detail';

        $visual['DELAY']['USE'] = false;
        $visual['COMPARE']['USE'] = false;
        $visual['QUANTITY']['SHOW'] = false;
        $visual['WEIGHT']['SHOW'] = false;
        $visual['COUNTER']['SHOW'] = false;
        $visual['MEASURES']['USE'] = false;
        $visual['ORDER_FAST']['USE'] = false;
    }

    unset($visual);

    if ($arItem['VISUAL']['OFFER']) {
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['VISUAL'] = [
                'OFFER' => false,
                'ACTION' => $arItem['VISUAL']['ACTION'],
                'DELAY' => [
                    'USE' => $arItem['VISUAL']['DELAY']['USE']
                ],
                'COMPARE' => [
                    'USE' => $arItem['VISUAL']['COMPARE']['USE']
                ],
                'ORDER_FAST' => [
                    'USE' => $arResult['ORDER_FAST']['USE'] && $arOffer['CAN_BUY']
                ]
            ];
        }

        unset($arOffer);
    }
}

include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/quick.view.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($bBase) {
    include(__DIR__ . '/modifiers/base/catalog.php');

    $arSkuFilteredId = [];

    if (isset($arResult['FILTERED_OFFERS_ID'])) {
        foreach ($arResult['FILTERED_OFFERS_ID'] as $arIds) {
            foreach ($arIds as $sId)
                $arSkuFilteredId[] = $sId;
        }
    } else {
        $arSkuFilteredId = null;
    }

    $arResult['OFFERS_FILTERED_APPLY'] = $arSkuFilteredId;

    unset($arSkuFilteredId, $arIds, $sId);
}

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

if ($arVisual['MEASURES']['USE'])
    include(__DIR__.'/modifiers/measures.php');

$arResult['VISUAL'] = $arVisual;