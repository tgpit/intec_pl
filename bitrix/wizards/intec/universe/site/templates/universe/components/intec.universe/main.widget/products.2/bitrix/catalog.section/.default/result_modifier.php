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
    'PROPERTY_PICTURES' => null,
    'PROPERTY_ORDER_USE' => null,
    'PROPERTY_REQUEST_USE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
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
    'TABS_ALIGN' => 'left',
    'BORDERS' => 'N',
    'INDENTS' => 'Y',
    'BORDERS_STYLE' => 'squared',
    'COUNTER_SHOW' => 'N',
    'MARKS_SHOW' => 'N',
    'MARKS_ORIENTATION' => 'horizontal',
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_DETAIL' => 'N',
    'QUICK_VIEW_VIEW' => 'right',
    'QUICK_VIEW_TEMPLATE' => null,
    'NAME_POSITION' => 'middle',
    'NAME_ALIGN' => 'left',
    'SECTION_SHOW' => 'N',
    'SECTION_ALIGN' => 'left',
    'PRICE_ALIGN' => 'left',
    'IMAGE_SLIDER_SHOW' => 'N',
    'IMAGE_ASPECT_RATIO' => '1:1',
    'ACTION' => 'buy',
    'OFFERS_USE' => 'N',
    'OFFERS_ALIGN' => 'left',
    'OFFERS_VIEW' => 'default',
    'OFFERS_VIEW_EXTENDED_LEFT' => null,
    'OFFERS_VIEW_EXTENDED_RIGHT' => null,
    'IMAGE_SLIDER_NAV_HIDE' => 'N',
    'VOTE_SHOW' => 'N',
    'VOTE_ALIGN' => 'left',
    'VOTE_MODE' => 'rating',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => null,
    'QUANTITY_BOUNDS_MANY' => null,
    'QUANTITY_ALIGN' => 'left',
    'SECTION_URL' => null,
    'DETAIL_URL' => null,
    'VIEW' => 'tabs',
    'SECTIONS_TITLE_SHOW' => 'Y',
    'SECTIONS_TITLE_ALIGN' => 'center',
    'RECALCULATION_PRICES_USE' => 'N',
    'SECTION_TIMER_SHOW' => 'N',
    'SECTION_TIMER_TIMER_QUANTITY_OVER' => 'Y',
    'LIST_URL' => null,
    'PURCHASE_BASKET_BUTTON_TEXT' => null,
    'PURCHASE_ORDER_BUTTON_TEXT' => null,
    'PURCHASE_REQUEST_BUTTON_TEXT' => null,
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

if (empty($arBlocks['HEADER']['TEXT']))
    $arBlocks['HEADER']['SHOW'] = false;

if (empty($arBlocks['DESCRIPTION']['TEXT']))
    $arBlocks['DESCRIPTION']['SHOW'] = false;

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
    'INDENTS' => [
        'USE' => $arParams['INDENTS'] === 'Y',
    ],
    'COLUMNS' => [
        'DESKTOP' => ArrayHelper::fromRange([3, 2, 4, 5], $arParams['COLUMNS']),
        'MOBILE' => ArrayHelper::fromRange([1, 2], $arParams['COLUMNS_MOBILE']),
    ],
    'LINES' => Type::toInteger($arParams['LINES']),
    'IMAGE' => [
        'ASPECT_RATIO' => 100,
        'SLIDER' => $arParams['IMAGE_SLIDER_SHOW'] === 'Y',
        'NAV' => $arParams['IMAGE_SLIDER_NAV_HIDE'] === 'Y'
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
    'NAME' => [
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['NAME_ALIGN']),
        'POSITION' => ArrayHelper::fromRange(['middle', 'top'], $arParams['NAME_POSITION'])
    ],
    'PRICE' => [
        'SHOW' => true,
        'ALIGN' => ArrayHelper::fromRange(['start', 'center', 'end'], $arParams['PRICE_ALIGN']),
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
        'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
    ],
    'OFFERS' => [
        'USE' => $arParams['OFFERS_USE'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['OFFERS_ALIGN']),
        'VIEW' => ArrayHelper::fromRange(['default', 'extended'], $arParams['OFFERS_VIEW']),
        'EXTENDED' => [
            'LEFT' => $arParams['OFFERS_VIEW_EXTENDED_LEFT'],
            'RIGHT' => $arParams['OFFERS_VIEW_EXTENDED_RIGHT']
        ]
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['average', 'rating'], $arParams['VOTE_MODE']),
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['VOTE_ALIGN'])
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'ALIGN' => ArrayHelper::fromRange($arPosition, $arParams['QUANTITY_ALIGN']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'SECTION' => [
        'SHOW' => $arParams['SECTION_SHOW'] === 'Y',
        'ALIGN' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['SECTION_ALIGN'])
    ],
    'VIEW' => ArrayHelper::fromRange([
        'tabs',
        'sections'
    ], $arParams['VIEW']),
    'SLIDER' => [
        'USE' => $arParams['SLIDER_USE'] === 'Y',
        'NAVIGATION' => $arParams['SLIDER_NAVIGATION'] === 'Y',
        'DOTS' => $arParams['SLIDER_DOTS'] === 'Y'
    ],
    'TABS' => [
        'ALIGN' => ArrayHelper::fromRange([
            'left',
            'center',
            'right'
        ], $arParams['TABS_ALIGN'])
    ],
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
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_2_BASKET_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_2_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_WIDGET_PRODUCTS_2_REQUEST');

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

if ($arVisual['OFFERS']['VIEW'] === 'extended' && $arVisual['IMAGE']['NAV'])
    $arVisual['IMAGE']['NAV'] = true;

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

$arResult['MODE'] = $arParams['MODE'];
$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
    'order',
    'request'
], $arParams['ACTION']);

if ($arResult['MODE'] != 'section') {
    if ($arVisual['VIEW'] === 'sections') {
        $arBlocks['HEADER']['SHOW'] = false;
        $arBlocks['DESCRIPTION']['SHOW'] = false;
    }
}

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

        if ($visual['ACTION'] !== 'detail')
            $visual['DELAY']['USE'] = false;

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
                ]
            ];
        }

        unset($arOffer);
    }
}

include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/quick.view.php');

if ($arVisual['TIMER']['SHOW'])
    include(__DIR__.'/modifiers/timer.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['BLOCKS'] = $arBlocks;
$arResult['VISUAL'] = $arVisual;

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
        $arCategory['ITEMS'][] = &$arItem;

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
    if (!empty($arCategory['ITEMS']))
        $arResult['CATEGORIES'][$sKey] = $arCategory;

unset($arCategory);
unset($arCategories);
unset($arBlocks);
unset($arVisual);