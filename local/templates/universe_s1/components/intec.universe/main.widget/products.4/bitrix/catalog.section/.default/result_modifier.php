<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
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
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'PROPERTY_STORES_SHOW' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_ARTICLE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'OFFERS_PROPERTY_STORES_SHOW' => null,
    'OFFERS_PROPERTY_ARTICLE' => null,
    'STORES_FIELDS' => [],
    'ARTICLE_SHOW' => 'N',
    'COLUMNS' => 3,
    'COLUMNS_MOBILE' => 1,
    'LINES' => 0,
    'BLOCKS_HEADER_SHOW' => 'N',
    'BLOCKS_HEADER_TEXT' => null,
    'BLOCKS_HEADER_ALIGN' => 'left',
    'BLOCKS_DESCRIPTION_SHOW' => 'N',
    'BLOCKS_DESCRIPTION_TEXT' => null,
    'BLOCKS_DESCRIPTION_ALIGN' => 'left',
    'BLOCKS_FOOTER_SHOW' => 'N',
    'BLOCKS_FOOTER_ALIGN' => 'center',
    'BLOCKS_FOOTER_BUTTON_SHOW' => 'N',
    'BLOCKS_FOOTER_BUTTON_TEXT' => null,
    'DELAY_USE' => 'N',
    'BORDERS' => 'N',
    'BORDERS_STYLE' => 'squared',
    'COUNTER_SHOW' => 'N',
    'MARKS_SHOW' => 'N',
    'MARKS_ORIENTATION' => 'horizontal',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_DETAIL' => 'N',
    'QUICK_VIEW_VIEW' => 'right',
    'QUICK_VIEW_TEMPLATE' => null,
    'IMAGE_ASPECT_RATIO' => '1:1',
    'IMAGE_SLIDER_SHOW' => 'N',
    'IMAGE_SLIDER_NAV_SHOW' => 'N',
    'IMAGE_SLIDER_OVERLAY_USE' => 'Y',
    'ACTION' => 'buy',
    'OFFERS_USE' => 'N',
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => null,
    'QUANTITY_BOUNDS_MANY' => null,
    'BANNER_SHOW' => 'N',
    'PROPERTY_BANNER_SHOW' => null,
    'PROPERTY_BANNER_PICTURE' => null,
    'PROPERTY_BANNER_THEME' => null,
    'RECALCULATION_PRICES_USE' => 'N',
    'VIEW' => 'tabs',
    'SECTIONS_TITLE_SHOW' => 'Y',
    'SECTIONS_TITLE_ALIGN' => 'center',
    'MEASURE_SHOW' => 'N',
    'SECTION_TIMER_SHOW' => 'N',
    'SECTION_TIMER_TIMER_QUANTITY_OVER' => 'Y',
    'LIST_URL' => null,
    'PURCHASE_BASKET_BUTTON_TEXT' => null,
    'PURCHASE_ORDER_BUTTON_TEXT' => null,
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
    'BANNER' => [
        'SHOW' => $arParams['PROPERTY_BANNER_SHOW'],
        'PICTURE' => $arParams['PROPERTY_BANNER_PICTURE'],
        'THEME' => $arParams['PROPERTY_BANNER_THEME']
    ],
    'OFFERS' => [
        'PICTURES' => $arParams['OFFERS_PROPERTY_PICTURES']
    ]
];

$arBlocks = [
    'HEADER' => [
        'SHOW' => $arParams['BLOCKS_HEADER_SHOW'] === 'Y',
        'TEXT' => $arParams['~BLOCKS_HEADER_TEXT'],
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['BLOCKS_HEADER_ALIGN'])
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['BLOCKS_DESCRIPTION_SHOW'] === 'Y',
        'TEXT' => $arParams['~BLOCKS_DESCRIPTION_TEXT'],
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['BLOCKS_DESCRIPTION_ALIGN'])
    ],
    'FOOTER' => [
        'SHOW' => $arParams['BLOCKS_FOOTER_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['BLOCKS_FOOTER_ALIGN']),
        'BUTTON' => [
            'SHOW' => $arParams['BLOCKS_FOOTER_BUTTON_SHOW'] === 'Y',
            'TEXT' => $arParams['~BLOCKS_FOOTER_BUTTON_TEXT'],
            'URL' => null
        ]
    ]
];

if (!empty($arParams['LIST_URL'])) {
    $arBlocks['FOOTER']['BUTTON']['URL'] = StringHelper::replaceMacros($arParams['LIST_URL'], $arMacros);
}

if ($arBlocks['FOOTER']['SHOW']) {
    if ($arBlocks['FOOTER']['BUTTON']['SHOW'] && empty($arBlocks['FOOTER']['BUTTON']['URL']))
        $arBlocks['FOOTER']['BUTTON']['SHOW'] = false;
}

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
        'DESKTOP' => ArrayHelper::fromRange([3, 2, 4, 5], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS_MOBILE'])
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 100,
        'SLIDER' => $arParams['IMAGE_SLIDER_SHOW'] === 'Y',
        'NAV' => $arParams['IMAGE_SLIDER_NAV_SHOW'] === 'Y',
        'OVERLAY' => $arParams['IMAGE_SLIDER_OVERLAY_USE'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y',
        'ORIENTATION' => ArrayHelper::fromRange(['horizontal', 'vertical'], $arParams['MARKS_ORIENTATION'])
    ],
    'COMPARE' => [
        'USE' => $arParams['USE_COMPARE'] === 'Y',
        'CODE' => $arParams['COMPARE_NAME']
    ],
    'DELAY' => [
        'USE' => $arParams['DELAY_USE'] === 'Y'
    ],
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y'
    ],
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
    'TABS' => [
        'ALIGN' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['TABS_ALIGN'])
    ],
    'LINES' => Type::toInteger($arParams['LINES']),
    'BANNER' => [
        'SHOW' => $arParams['BANNER_SHOW'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => true,
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
        'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
    ],
    'VIEW' => ArrayHelper::fromRange(['tabs', 'sections'], $arParams['VIEW']),
    'SECTIONS' => [
        'TITLE' => [
            'SHOW' => $arParams['SECTIONS_TITLE_SHOW'] === 'Y',
            'ALIGN' => ArrayHelper::fromRange([
                'left',
                'center',
                'right'
            ], $arParams['SECTIONS_TITLE_ALIGN'])
        ]
    ],
    'MEASURE' => [
        'SHOW' => $arParams['MEASURE_SHOW'] === 'Y'
    ],
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
        'SHOW' => $arParams['SECTION_TIMER_SHOW'] === 'Y',
        'TIMER_QUANTITY_OVER' => $arParams['SECTION_TIMER_TIMER_QUANTITY_OVER'] === 'Y',
        'MODE' => $arParams['SECTION_TIMER_MODE']
    ]
];

if (!$bBase && $arVisual['TIMER']['MODE'] == 'discount')
    $arVisual['TIMER']['SHOW'] = false;

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_4_BASKET_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_4_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_4_REQUEST');

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

if ($arVisual['LINES'] < 1)
    $arVisual['LINES'] = null;

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
    'USE' => $arParams['DELAY_USE'] === 'Y'
];

include(__DIR__.'/modifiers/order.fast.php');

if ($arResult['ACTION'] !== 'buy' && $arResult['ACTION'] !== 'detail' || $bLite) {
    $arResult['ORDER_FAST']['USE'] = false;
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
                ]))
            ]
        ],
        'QUANTITY' => [
            'SHOW' => $arVisual['QUANTITY']['SHOW']
        ],
        'ARTICLE' => [
            'SHOW' => $arVisual['ARTICLE']['SHOW']
        ],
        'PRICE' => [
            'SHOW' => $arVisual['PRICE']['SHOW']
        ],
        'TIMER' => [
            'SHOW' => $arVisual['TIMER']['SHOW']
        ],
        'DELAY' => [
            'USE' => $arResult['DELAY']['USE']
        ],
        'COMPARE' => [
            'USE' => $arResult['COMPARE']['USE']
        ],
        'ORDER_FAST' => [
            'USE' => $arResult['ORDER_FAST']['USE']
        ],
        'COUNTER' => [
            'SHOW' => $arVisual['COUNTER']['SHOW']
        ]
    ];

    $visual = &$arItem['VISUAL'];

    if ($visual['MARKS']['VALUES']['RECOMMEND'] || $visual['MARKS']['VALUES']['NEW'] || $visual['MARKS']['VALUES']['HIT'] || $visual['MARKS']['VALUES']['SHARE'])
        $visual['MARKS']['SHOW'] = $arVisual['MARKS']['SHOW'];

    if ($visual['ACTION'] !== 'none') {
        if ($visual['ACTION'] !== 'request') {
            $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_REQUEST_USE'],
                'VALUE'
            ]));

            if ($isRequest && $arResult['FORMS']['REQUEST']['SHOW'])
                $visual['ACTION'] = 'request';

            unset($isRequest);
        }

        if ($visual['ACTION'] !== 'order') {
            $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                $arParams['PROPERTY_ORDER_USE'],
                'VALUE'
            ]));

            if ($isOrder && $arResult['FORMS']['ORDER']['SHOW'])
                $visual['ACTION'] = 'order';

            unset($isOrder);
        }
    }

    if ($visual['ACTION'] !== 'buy') {
        $visual['COUNTER']['SHOW'] = false;

        if ($visual['ACTION'] !==' detail') {
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

        $visual['ORDER_FAST']['USE'] = false;
        $visual['QUANTITY']['SHOW'] = false;
        $visual['ARTICLE']['SHOW'] = false;
        $visual['DELAY']['USE'] = false;
        $visual['COMPARE']['USE'] = false;
        $visual['COUNTER']['SHOW'] = false;
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
                    'USE' => $arItem['VISUAL']['ORDER_FAST']['USE']
                ]
            ];
        }

        unset($arOffer);
    }

    $arItem['BANNER'] = [
        'SHOW' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arCodes['BANNER']['SHOW'], 'VALUE']),
        'PICTURE' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arCodes['BANNER']['PICTURE'], 'VALUE']),
        'THEME' => ArrayHelper::getValue($arItem, ['PROPERTIES', $arCodes['BANNER']['THEME'], 'VALUE_XML_ID'])
    ];

    $arItem['BANNER']['SHOW'] = !empty($arItem['BANNER']['SHOW']) && $arParams['BANNER_SHOW'] === 'Y';
    $arItem['BANNER']['THEME'] = ArrayHelper::fromRange(['light', 'dark'], $arItem['BANNER']['THEME']);

    $arItem['VOTE'] = [
        'COUNT' => ArrayHelper::getValue($arItem, ['PROPERTIES', 'vote_count', 'VALUE'])
    ];
}

unset($arItem);

include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/quick.view.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($bBase) {
    include(__DIR__.'/modifiers/base/catalog.php');
    include(__DIR__.'/modifiers/base/stores.php');
}

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['BLOCKS'] = $arBlocks;
$arResult['VISUAL'] = $arVisual;
$arResult['MODE'] = $arParams['MODE'];

$arCategories = [];
$arSections = [];
$arProperty = $arParams['PROPERTY_CATEGORY'];

if (!empty($arProperty)) {
    $arProperty = CIBlockProperty::GetList([], [
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'CODE' => $arProperty
    ])->GetNext();
}

if (!empty($arProperty)) {
    $rsCategories = CIBlockPropertyEnum::GetList(['SORT' => 'ASC'], [
        'PROPERTY_ID' => $arProperty['ID']
    ]);

    while ($arCategory = $rsCategories->GetNext()) {
        if (empty($arCategory['XML_ID']))
            continue;

        $arCategories[$arCategory['XML_ID']] = [
            'CODE' => $arCategory['XML_ID'],
            'NAME' => $arCategory['VALUE'],
            'SORT' => $arCategory['SORT'],
            'BANNER' => null,
            'ITEMS' => []
        ];
    }

    unset($arCategory);
    unset($rsCategories);
} else {
    $arResult['ITEMS'] = [];
}

foreach ($arResult['ITEMS'] as &$arItem) {
    if (!empty($arItem['IBLOCK_SECTION_ID']))
        $arSections[] = $arItem['IBLOCK_SECTION_ID'];

    $arCategory = ArrayHelper::getValue($arItem, [
        'PROPERTIES',
        $arProperty['CODE'],
        'VALUE_XML_ID'
    ]);

    if (!empty($arCategory) && ArrayHelper::keyExists($arCategory, $arCategories)) {
        $arCategory = &$arCategories[$arCategory];

        if ($arItem['BANNER']['SHOW'] && empty($arCategory['BANNER'])) {
            $arCategory['BANNER'] = &$arItem;
        } else {
            $arCategory['ITEMS'][] = &$arItem;
        }

        unset($arCategory);
    }
}

if (!empty($arSections)) {
    $rsSections = CIBlockSection::GetList([
        'SORT' => 'ASC'
    ], [
        'ID' => $arSections,
        'IBLOCK_ID' => $arParams['IBLOCK_ID']
    ]);

    $arSections = Arrays::from([]);
    $rsSections->SetUrlTemplates(
        $arParams['DETAIL_URL'],
        $arParams['SECTION_URL']
    );

    while ($arSection = $rsSections->GetNext())
        $arSections->set($arSection['ID'], $arSection);

    unset($arSection);
    unset($rsSections);
}

$arSections = Arrays::from($arSections);

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['SECTION'] = $arSections->get($arItem['IBLOCK_SECTION_ID']);

    unset($arItem);
}

$arResult['CATEGORIES'] = [];

foreach ($arCategories as $sKey => $arCategory)
    if (!empty($arCategory['ITEMS']) || !empty($arCategory['BANNER']))
        $arResult['CATEGORIES'][$sKey] = $arCategory;

unset($arCategory);
unset($arCategories);
unset($arBlocks);
unset($arVisual);