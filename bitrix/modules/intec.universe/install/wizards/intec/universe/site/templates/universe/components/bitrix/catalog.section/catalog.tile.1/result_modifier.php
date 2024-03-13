<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
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

$arResult['BASE'] = false;
$arResult['LITE'] = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $arResult['BASE'] = true;
} else if (Loader::includeModule('intec.startshop')) {
    $arResult['LITE'] = true;
}

$bSeo = Loader::includeModule('intec.seo');

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'ACTION' => 'buy',
    'BORDERS' => 'Y',
    'COLUMNS' => 3,
    'COLUMNS_MOBILE' => 1,
    'IMAGE_ASPECT_RATIO' => '1:1',
    'COUNTER_SHOW' => 'Y',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'DELAY_USE' => 'N',
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 3,
    'QUANTITY_BOUNDS_MANY' => 10,
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_DETAIL' => 'N',
    'QUICK_VIEW_TEMPLATE' => null,
    'QUICK_VIEW_SLIDE_USE' => 'N',
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'OFFERS_USE' => 'Y',
    'WIDE' => 'Y',
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'LAZY_LOAD' => 'N',
    'RECALCULATION_PRICES_USE' => 'N',
    'SECTION_TIMER_SHOW' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'BORDERS' => $arParams['BORDERS'] === 'Y',
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([2, 3, 4], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS_MOBILE'])
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 100
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y',
        'MESSAGE' => [
            'MAX' => [
                'SHOW' => $arParams['COUNTER_MESSAGE_MAX_SHOW'] === 'Y'
            ]
        ]
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
    'WIDE' => $arParams['WIDE'] === 'Y',
    'PRICE' => [
        'SHOW' => true,
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
        'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
    ],
    'MEASURE' => [
        'SHOW' => $arResult['BASE'] && $arParams['MEASURE_SHOW'] === 'Y'
    ],
    'TIMER' => [
        'SHOW' => $arParams['SECTION_TIMER_SHOW'] === 'Y' && $arResult['BASE']
    ],
    'GIFTS' => [
        'SHOW' => $arResult['CATALOG'] && $arParams['USE_GIFTS_SECTION'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'POSITION' => ArrayHelper::fromRange(['top', 'middle', 'bottom'], $arParams['GIFTS_SECTION_LIST_POSITION']),
        'VIEW' => ArrayHelper::fromRange(['1', '2', '3', '4', '5'], $arParams['GIFTS_SECTION_LIST_VIEW']),
        'COLUMNS' => ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['GIFTS_SECTION_LIST_COLUMNS']),
        'QUANTITY' => $arParams['GIFTS_SECTION_LIST_QUANTITY'] <= 0 ? 20 : $arParams['GIFTS_SECTION_LIST_COLUMNS']
    ]
];

if (defined('EDITOR') || !class_exists('\\intec\\template\\Properties'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['QUANTITY']['BOUNDS']['FEW'] < 0)
    $arVisual['QUANTITY']['BOUNDS']['FEW'] = 3;

if ($arVisual['QUANTITY']['BOUNDS']['MANY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'])
    $arVisual['QUANTITY']['BOUNDS']['MANY'] = $arVisual['QUANTITY']['BOUNDS']['FEW'] + 1;

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
    'USE' => $arParams['DELAY_USE'] === 'Y'
];

if ($arResult['ACTION'] !== 'buy' && $arResult['ACTION'] !== 'detail' || $arResult['LITE'])
    $arResult['DELAY']['USE'] = false;

$arResult['COMPARE'] = [
    'USE' => $arParams['USE_COMPARE'] === 'Y',
    'CODE' => $arParams['COMPARE_NAME']
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

if (empty($arResult['FORM']['ID']))
    $arResult['FORM']['SHOW'] = false;

$arResult['URL'] = [
    'BASKET' => ArrayHelper::getValue($arParams, 'BASKET_URL'),
    'CONSENT' => ArrayHelper::getValue($arParams, 'CONSENT_URL')
];

foreach ($arResult['URL'] as $sKey => $sUrl)
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
        'SHOW' => true,
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => $arParams['FORM_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
        ]
    ],
    'REQUEST' => [
        'SHOW' => true,
        'ID' => $arParams['FORM_REQUEST_ID'],
        'TEMPLATE' => $arParams['FORM_REQUEST_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_REQUEST_PROPERTY_PRODUCT']
        ]
    ]
];

if ($arResult['LITE'])
    include(__DIR__ . '/modifiers/lite/catalog.php');

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'OFFER' => !empty($arItem['OFFERS']),
        'ACTION' => $arResult['ACTION'],
        'PICTURE' => null,
        'MARKS' => [
            'SHOW' => false,
            'VALUES' => [
                'RECOMMEND' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARKS_RECOMMEND'],
                    'VALUE'
                ])),
                'NEW' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARKS_NEW'],
                    'VALUE'
                ])),
                'HIT' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARKS_HIT'],
                    'VALUE'
                ])),
                'SHARE' => !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                    $arParams['PROPERTY_MARKS_SHARE'],
                    'VALUE'
                ]))
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
        'TIMER' => [
            'SHOW' => $arVisual['TIMER']['SHOW']
        ],
        'PRICE' => [
            'SHOW' => $arVisual['PRICE']['SHOW']
        ],
        'COUNTER' => [
            'SHOW' => $arVisual['COUNTER']['SHOW']
        ]
    ];

    $arData = &$arItem['DATA'];

    if (!empty($arItem['PREVIEW_PICTURE']))
        $arData['PICTURE'] = $arItem['PREVIEW_PICTURE'];
    else if (!empty($arItem['DETAIL_PICTURE']))
        $arData['PICTURE'] = $arItem['DETAIL_PICTURE'];

    if ($arData['MARKS']['VALUES']['RECOMMEND'] || $arData['MARKS']['VALUES']['NEW'] || $arData['MARKS']['VALUES']['NEW'] || $arData['MARKS']['VALUES']['SHARE'])
        $arData['MARKS']['SHOW'] = true;

    if ($arData['ACTION'] !== 'none') {
        if ($arData['ACTION'] !== 'order') {
            $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_ORDER_USE'],
                'VALUE'
            ]));

            if ($isOrder && $arResult['FORMS']['ORDER']['SHOW'])
                $arData['ACTION'] = 'order';

            unset($isOrder);
        }

        if ($arData['ACTION'] !== 'request') {
            $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_REQUEST_USE'],
                'VALUE'
            ]));

            if ($isRequest && $arResult['FORMS']['REQUEST']['SHOW'])
                $arData['ACTION'] = 'request';

            unset($isRequest);
        }
    }

    if ($arData['ACTION'] !== 'buy') {
        $arData['COUNTER']['SHOW'] = false;

        if ($arData['ACTION'] !== 'detail')
            $arData['DELAY']['USE'] = false;

        if ($arData['ACTION'] === 'request') {
            $arData['PRICE']['SHOW'] = false;
            $arData['TIMER']['SHOW'] = false;
        }
    }

    if (!$arVisual['OFFERS']['USE'] && $arData['OFFER']) {
        if ($arData['ACTION'] === 'buy')
            $arData['ACTION'] = 'detail';

        $arData['COUNTER']['SHOW'] = false;
        $arData['DELAY']['USE'] = false;
        $arData['COMPARE']['USE'] = false;
        $arData['QUANTITY']['SHOW'] = false;
    }

    unset($arData);

    if ($arItem['DATA']['OFFER']) {
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['DATA'] = [
                'OFFER' => false,
                'ACTION' => $arItem['DATA']['ACTION'],
                'PICTURE' => null,
                'DELAY' => [
                    'USE' => $arItem['DATA']['DELAY']['USE']
                ],
                'COMPARE' => [
                    'USE' => $arItem['DATA']['COMPARE']['USE']
                ]
            ];

            $arOfferData = &$arOffer['DATA'];

            if (!empty($arOffer['PREVIEW_PICTURE']))
                $arOfferData['PICTURE'] = $arOffer['PREVIEW_PICTURE'];
            else if (!empty($arItem['DETAIL_PICTURE']))
                $arOfferData['PICTURE'] = $arOffer['DETAIL_PICTURE'];

            unset($arOfferData);
        }
    }
}

unset($arItem);

include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/quick.view.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($arResult['BASE']) {
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

if ($arResult['BASE'] || $arResult['LITE'])
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;