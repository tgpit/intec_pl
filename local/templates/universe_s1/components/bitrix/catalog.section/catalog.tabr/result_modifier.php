<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$bBase = false;
$bLite = false;

if (!Loader::includeModule('intec.core'))
    return;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

$bSeo = Loader::includeModule('intec.seo');

$arParams = ArrayHelper::merge([
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 3,
    'COLUMNS_MOBILE' => 1,
    'MARKS_SHOW' => 'N',
    'MARKS_ORIENTATION' => 'horizontal',
    'NAME_ALIGN' => 'left',
    'VOTE_SHOW' => 'N',
    'VOTE_ALIGN' => 'left',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => null,
    'QUANTITY_BOUNDS_MANY' => null,
    'QUANTITY_ALIGN' => 'left',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_DETAIL' => 'N',
    'QUICK_VIEW_TEMPLATE' => null,
    'WEIGHT_SHOW' => 'N',
    'WEIGHT_ALIGN' => 'left',
    'DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_ALIGN' => 'left',
    'PRICE_ALIGN' => 'left',
    'ACTION' => 'buy',
    'COUNTER_SHOW' => 'N',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'FORM_ID' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'FORM_TEMPLATE' => null,
    'CONSENT_URL' => null,
    'OFFERS_USE' => 'N',
    'OFFERS_ALIGN' => 'start',
    'IMAGE_ASPECT_RATIO' => '1:1',
    'IMAGE_SLIDER_SHOW' => 'N',
    'IMAGE_SLIDER_ANIMATION' => 'standard',
    'WIDE' => 'Y',
    'LAZY_LOAD' => 'N',
    'RECALCULATION_PRICES_USE' => 'N',
    'SECTION_TIMER_SHOW' => 'Y',
    'PURCHASE_REQUEST_BUTTON_TEXT' => null
], $arParams);

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$arCodes = [
    'MARKS' => [
        'HIT' => $arParams['PROPERTY_MARKS_HIT'],
        'NEW' => $arParams['PROPERTY_MARKS_NEW'],
        'RECOMMEND' => $arParams['PROPERTY_MARKS_RECOMMEND'],
        'SHARE' => $arParams['PROPERTY_MARKS_SHARED'],
    ],
    'PICTURES' => $arParams['PROPERTY_PICTURES'],
    'OFFERS' => [
        'PICTURES' => $arParams['OFFERS_PROPERTY_PICTURES']
    ]
];

$arPosition = [
    'left',
    'center',
    'right'
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([3, 2, 4], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS_MOBILE']),
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y' && (!empty($arCodes['MARKS']['HIT']) || !empty($arCodes['MARKS']['NEW']) || !empty($arCodes['MARKS']['RECOMMEND']) || !empty($arCodes['MARKS']['SHARE'])),
        'ORIENTATION' => ArrayHelper::fromRange(['horizontal', 'vertical'], $arParams['MARKS_ORIENTATION'])
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 100,
        'SLIDER' => $arParams['IMAGE_SLIDER_SHOW'] === 'Y',
        'NAV' => $arParams['IMAGE_SLIDER_NAV'] === 'Y',
        'ANIMATION' => ArrayHelper::fromRange(['standard', 'fade'], $arParams['IMAGE_SLIDER_ANIMATION'])
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['VOTE_ALIGN']),
        'MODE' => ArrayHelper::fromRange(['average', 'rating'], $arParams['VOTE_MODE'])
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['QUANTITY_ALIGN']),
        'BOUNDS' => [
            'FEW' => Type::toInteger($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toInteger($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'NAME' => [
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['NAME_ALIGN'])
    ],
    'WEIGHT' => [
        'SHOW' => $arParams['WEIGHT_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['WEIGHT_ALIGN'])
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['DESCRIPTION_ALIGN'])
    ],
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange(['start', 'center', 'end'], $arParams['OFFERS_ALIGN'])
    ],
    'PRICE' => [
        'SHOW' => true,
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['PRICE_ALIGN']),
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
        'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
    ],
    'MEASURE' => [
        'SHOW' => $bBase && $arParams['MEASURE_SHOW'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y' && $arParams['ACTION'] === 'buy',
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
    'WIDE' => $arParams['WIDE'] === 'Y',
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ],
        'ORDER' => [
            'TEXT' => $arParams['PURCHASE_ORDER_BUTTON_TEXT']
        ],
        'REQUEST' => [
            'TEXT' => $arParams['PURCHASE_REQUEST_BUTTON_TEXT']
        ]
    ],
    'TIMER' => [
        'SHOW' => $arParams['SECTION_TIMER_SHOW'] === 'Y' && $bBase
    ],
    'GIFTS' => [
        'SHOW' => $arResult['CATALOG'] && $arParams['USE_GIFTS_SECTION'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'POSITION' => ArrayHelper::fromRange(['top', 'middle', 'bottom'], $arParams['GIFTS_SECTION_LIST_POSITION']),
        'VIEW' => ArrayHelper::fromRange(['1', '2', '3', '4', '5'], $arParams['GIFTS_SECTION_LIST_VIEW']),
        'COLUMNS' => ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['GIFTS_SECTION_LIST_COLUMNS']),
        'QUANTITY' => $arParams['GIFTS_SECTION_LIST_QUANTITY'] <= 0 ? 20 : $arParams['GIFTS_SECTION_LIST_COLUMNS']
    ]
];

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_BASKET_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_REQUEST');

if (defined('EDITOR') || !class_exists('\\intec\\template\\Properties'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['QUANTITY']['BOUNDS']['FEW'] < 0)
    $arVisual['QUANTITY']['BOUNDS']['FEW'] = 5;

if ($arVisual['QUANTITY']['BOUNDS']['MANY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'])
    $arVisual['QUANTITY']['BOUNDS']['MANY'] = $arVisual['QUANTITY']['BOUNDS']['FEW'] + 10;

if (!$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3)
    $arVisual['COLUMNS']['DESKTOP'] = 3;

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

if ($arResult['ACTION'] !== 'buy' && $arResult['ACTION'] !== 'detail' || $bLite)
    $arResult['DELAY']['USE'] = false;

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

unset($arUrl);

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
    include(__DIR__ . '/modifiers/lite/catalog.php');

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
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
        'WEIGHT' => [
            'SHOW' => $arVisual['WEIGHT']['SHOW']
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

    if ($arData['MARKS']['VALUES']['RECOMMEND'] || $arData['MARKS']['VALUES']['NEW'] || $arData['MARKS']['VALUES']['HIT'] || $arData['MARKS']['VALUES']['SHARE'])
        $arData['MARKS']['SHOW'] = true;

    if ($arData['ACTION'] !== 'none') {
        if ($arData['ACTION'] !== 'request') {
            $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_REQUEST_USE'],
                'VALUE'
            ]));

            if ($isRequest && $arResult['FORMS']['REQUEST']['SHOW'])
                $arData['ACTION'] = 'request';

            unset($isRequest);
        }

        if ($arData['ACTION'] !== 'order') {
            $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_ORDER_USE'],
                'VALUE'
            ]));

            if ($isOrder && $arResult['FORMS']['ORDER']['SHOW'])
                $arData['ACTION'] = 'order';

            unset($isOrder);
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

        $arData['DELAY']['USE'] = false;
        $arData['COMPARE']['USE'] = false;
        $arData['QUANTITY']['SHOW'] = false;
        $arData['WEIGHT']['SHOW'] = false;
        $arData['COUNTER']['SHOW'] = false;
    }

    unset($arData);

    if ($arItem['DATA']['OFFER']) {
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['DATA'] = [
                'OFFER' => false,
                'ACTION' => $arItem['DATA']['ACTION'],
                'DELAY' => [
                    'USE' => $arItem['DATA']['DELAY']['USE']
                ],
                'COMPARE' => [
                    'USE' => $arItem['DATA']['COMPARE']['USE']
                ]
            ];
        }

        unset($arOffer);
    }
}

unset($arItem);

include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/quick.view.php');
include(__DIR__.'/modifiers/properties.php');

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

$arResult['VISUAL'] = $arVisual;