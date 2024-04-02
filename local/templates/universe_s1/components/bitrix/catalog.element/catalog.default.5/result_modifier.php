<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponentTemplate $this
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$bMeasures = false;

if ($bBase && Loader::includeModule('intec.measures'))
    $bMeasures = true;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PANEL_MOBILE_SHOW' => 'N',
    'DELAY_USE' => 'N',
    'PROPERTY_ARTICLE' => null,
    'PROPERTY_BRAND' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_DOCUMENTS' => null,
    'PROPERTY_VIDEO' => null,
    'VIDEO_IBLOCK_TYPE' => null,
    'VIDEO_IBLOCK_ID' => null,
    'VIDEO_PROPERTY_URL' => null,
    'PROPERTY_REVIEWS' => null,
    'PROPERTY_ARTICLES' => null,
    'PROPERTY_ACCESSORIES' => null,
    'PROPERTY_ADDITIONAL' => null,
    'PROPERTY_ASSOCIATED' => null,
    'PROPERTY_RECOMMENDED' => null,
    'PROPERTY_SERVICES' => null,
    'OFFERS_PROPERTY_ARTICLE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'OFFERS_PROPERTIES_SHOW' => 'Y',
    'OFFERS_PROPERTIES_COUNT' => 3,
    'OFFERS_PROPERTIES_DELIMITER' => ',',
    'ARTICLE_SHOW' => 'N',
    'VOTE_SHOW' => 'N',
    'BRAND_SHOW' => 'N',
    'MARKS_SHOW' => 'N',
    'GALLERY_ACTION' => 'none',
    'GALLERY_ZOOM' => 'N',
    'GALLERY_PREVIEW_SHOW' => 'N',
    'GALLERY_VIDEO_USE' => 'N',
    'SIZES_SHOW' => 'N',
    'SIZES_PATH' => null,
    'SIZES_MODE' => 'all',
    'PROPERTY_SIZES_SHOW' => null,
    'SKU_VIEW' => 'dynamic',
    'SKU_NAME' => null,
    'PROPERTIES_PREVIEW_SHOW' => 'N',
    'PROEPRTIES_PREVIEW_NAME' => null,
    'PROEPRTIES_PREVIEW_COUNT' => 5,
    'PRICE_DISCOUNT_OLD' => 'N',
    'PRICE_DISCOUNT_PERCENT' => 'N',
    'PRICE_DISCOUNT_ECONOMY' => 'N',
    'PRICE_RANGE' => 'N',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 10,
    'QUANTITY_BOUNDS_MANY' => 50,
    'STORE_POSITION' => 'content',
    'STORE_NAME' => null,
    'STORE_TEMPLATE' => 'template.2',
    'STOREMAP_TEMPLATE' => 'map.1',
    'ACTION' => 'none',
    'COUNTER_SHOW' => 'N',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'PRICE_INFO_SHOW' => 'N',
    'PRICE_INFO_TEXT' => null,
    'PANEL_SHOW' => 'N',
    'DESCRIPTION_PREVIEW_SHOW' => 'N',
    'DESCRIPTION_DETAIL_SHOW' => 'N',
    'DESCRIPTION_DETAIL_NAME' => null,
    'DESCRIPTION_FROM_PREVIEW' => 'N',
    'PROPERTIES_DETAIL_SHOW' => 'N',
    'PROPERTIES_DETAIL_NAME' => null,
    'PRODUCTS_ACCESSORIES_SHOW' => 'N',
    'BRAND_ADDITIONAL_SHOW' => 'N',
    'BRAND_ADDITIONAL_POSITION' => 'content',
    'BRAND_ADDITIONAL_LINK_NAME' => null,
    'DOCUMENTS_SHOW' => 'N',
    'DOCUMENTS_NAME' => null,
    'DOCUMENTS_COLUMNS' => 3,
    'VIDEO_SHOW' => 'N',
    'VIDEO_NAME' => null,
    'ARTICLES_SHOW' => 'N',
    'ARTICLES_NAME' => null,
    'PRODUCTS_RECOMMENDED_SHOW' => 'N',
    'PRODUCTS_RECOMMENDED_NAME' => null,
    'PRODUCTS_RECOMMENDED_POSITION' => 'content',
    'PRODUCTS_ASSOCIATED_SHOW' => 'N',
    'PRODUCTS_ASSOCIATED_NAME' => null,
    'PRODUCTS_ASSOCIATED_POSITION' => 'content',
    'SERVICES_SHOW' => 'N',
    'SERVICES_NAME' => null,
    'INFORMATION_BUY_SHOW' => 'N',
    'INFORMATION_BUY_NAME' => null,
    'INFORMATION_BUY_PATH' => null,
    'INFORMATION_BUY_POSITION' => 'wide',
    'INFORMATION_PAYMENT_SHOW' => 'N',
    'INFORMATION_PAYMENT_NAME' => null,
    'INFORMATION_PAYMENT_PATH' => null,
    'INFORMATION_PAYMENT_POSITION' => 'wide',
    'INFORMATION_SHIPMENT_SHOW' => 'N',
    'INFORMATION_SHIPMENT_NAME' => null,
    'INFORMATION_SHIPMENT_PATH' => null,
    'INFORMATION_SHIPMENT_POSITION' => 'wide',
    'INFORMATION_ADDITIONAL_1_SHOW' => 'N',
    'INFORMATION_ADDITIONAL_1_NAME' => null,
    'INFORMATION_ADDITIONAL_1_PATH' => null,
    'INFORMATION_ADDITIONAL_1_POSITION' => 'wide',
    'INFORMATION_ADDITIONAL_2_SHOW' => 'N',
    'INFORMATION_ADDITIONAL_2_NAME' => null,
    'INFORMATION_ADDITIONAL_2_PATH' => null,
    'INFORMATION_ADDITIONAL_2_POSITION' => 'wide',
    'RECALCULATION_PRICES_USE' => 'N',
    'RECALCULATION_ALWAYS_SHOW' => 'N',
    'RECALCULATION_ADDITIONAL_PRICES_USE' => 'N',
    'MAIN_VIEW' => 1,
    'PRICE_CREDIT_SHOW' => 'N',
    'PRICE_CREDIT_DURATION' => null,
    'RECALCULATION_PRICE_CREDIT_USE' => 'N',
    'PRICE_CREDIT_LINK_USE' => "N",
    'PRICE_CREDIT_LINK' => null,
    'MEASURES_USE' => 'N',
    'MEASURES_POSITION' => 'top',
    'TIMER_SHOW' => 'N',
    'TIMER_TIMER_QUANTITY_OVER' => 'Y',
    'PURCHASE_REQUEST_BUTTON_TEXT' => null,
    'SHARES_SHOW' => 'N',
    'SHARES_IBLOCK_TYPE' => null,
    'SHARES_IBLOCK_ID' => null,
    'SHARES_ACTIVE_DATE' => 'N',
    'SHARES_MODE' => 'default',
    'SHARES_PROPERTY_PRODUCTS' => null,
    'SHARES_IBLOCK_PROPERTY_PRODUCTS' => null,
    'SHARES_IBLOCK_PROPERTY_DISCOUNT' => null,
    'SHARES_IBLOCK_PROPERTY_DATE' => null,
    'SHARES_HEADER' => null,
    'SHOW_SKU_DESCRIPTION' => 'N'
], $arParams);

$arResult['SKU'] = [
    'VIEW' => ArrayHelper::fromRange(['dynamic', 'list'], $arParams['SKU_VIEW']),
    'NAME' => $arParams['SKU_NAME']
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] === 'Y' : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ],
    'ARTICLE' => [
        'SHOW' => $arParams['ARTICLE_SHOW'] === 'Y'
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y' && !empty($arParams['IBLOCK_ID']),
        'TYPE' => ArrayHelper::fromRange(['rating', 'vote_avg'], $arParams['VOTE_TYPE'])
    ],
    'BRAND' => [
        'SHOW' => $arParams['BRAND_SHOW'] === 'Y',
        'ADDITIONAL' => [
            'SHOW' => $arParams['BRAND_ADDITIONAL_SHOW'] === 'Y',
            'POSITION' => ArrayHelper::fromRange(['content', 'column'], $arParams['BRAND_ADDITIONAL_POSITION']),
            'LINK' => [
                'NAME' => $arParams['BRAND_ADDITIONAL_LINK_NAME']
            ]
        ]
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y'
    ],
    'GALLERY' => [
        'ACTION' => ArrayHelper::fromRange(['none', 'source', 'popup'], $arParams['GALLERY_ACTION']),
        'ZOOM' => $arParams['GALLERY_ZOOM'] === 'Y',
        'PREVIEW' => [
            'SHOW' => $arParams['GALLERY_PREVIEW_SHOW'] === 'Y'
        ],
        'VIDEO' => [
            'USE' => !empty($arParams['GALLERY_VIDEO_IBLOCK_ID']) &&
                (!empty($arParams['GALLERY_VIDEO_PROPERTY_LINK']) || !empty($arParams['GALLERY_VIDEO_OFFER_PROPERTY_LINK'])),
            'CONTROLS' => $arParams['GALLERY_VIDEO_CONTROLS_SHOW'] === 'Y'
        ]
    ],
    'PROPERTIES' => [
        'PREVIEW' => [
            'SHOW' => $arParams['PROPERTIES_PREVIEW_SHOW'] === 'Y',
            'PRODUCT_SHOW' => $arParams['PROPERTIES_PREVIEW_PRODUCT_SHOW'] === 'Y',
            'OFFER_SHOW' => $arParams['PROPERTIES_PREVIEW_OFFERS_SHOW'] === 'Y',
            'NAME' => $arParams['PROEPRTIES_PREVIEW_NAME'],
            'COUNT' => Type::toInteger($arParams['PROEPRTIES_PREVIEW_COUNT'])
        ],
        'DETAIL' => [
            'SHOW' => $arParams['PROPERTIES_DETAIL_SHOW'] === 'Y',
            'NAME' => $arParams['PROPERTIES_DETAIL_NAME']
        ]
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y' && $bBase,
        'TIMER_QUANTITY_OVER' => $arParams['TIMER_TIMER_QUANTITY_OVER'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => true,
        'RANGE' => $arParams['PRICE_RANGE'] === 'Y',
        'DISCOUNT' => [
            'OLD' => $arParams['PRICE_DISCOUNT_OLD'] === 'Y',
            'PERCENT' => $arParams['PRICE_DISCOUNT_PERCENT'] === 'Y',
            'ECONOMY' => $arParams['PRICE_DISCOUNT_ECONOMY'] === 'Y'
        ],
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y'
    ],
    'CREDIT' => [
        'SHOW' => $arParams['PRICE_CREDIT_SHOW'] === 'Y',
        'DURATION' => $arParams['PRICE_CREDIT_DURATION'],
        'RECALCULATION'=> $arParams['RECALCULATION_PRICE_CREDIT_USE'] === 'Y' && $arParams['RECALCULATION_PRICES_USE'] === 'Y',
        'LINK' => [
            'USE' => $arParams['PRICE_CREDIT_LINK_USE'] === 'Y',
            'VALUE' => $arParams['PRICE_CREDIT_LINK']
        ]
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'STORES' => [
        'USE' => $arParams['USE_STORE'] === 'Y' && Type::isArray($arParams['STORES']) && !empty(array_filter($arParams['STORES'])) && $bBase,
        'POSITION' => ArrayHelper::fromRange(['content', 'popup'], $arParams['STORE_POSITION']),
        'NAME' => $arParams['STORE_NAME'],
        'TEMPLATE' => ArrayHelper::fromRange(['template.2', 'template.4'], $arParams['STORE_TEMPLATE']),
        'MAP_TEMPLATE' => ArrayHelper::fromRange(['map.1'], $arParams['STOREMAP_TEMPLATE'])
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y',
        'MESSAGE' => [
            'MAX' => [
                'SHOW' => $arParams['COUNTER_MESSAGE_MAX_SHOW'] === 'Y'
            ]
        ]
    ],
    'PRICE_INFO' => [
        'SHOW' => $arParams['PRICE_INFO_SHOW'] === 'Y',
        'TEXT' => Html::decode($arParams['PRICE_INFO_TEXT'])
    ],
    'PANEL' => [
        'DESKTOP' => [
            'SHOW' => $arParams['PANEL_SHOW'] === 'Y'
        ],
        'MOBILE' => [
            'SHOW' => $arParams['PANEL_MOBILE_SHOW'] === 'Y'
        ]
    ],
    'MENU' => [
        'SHOW' => $arResult['ORIGINAL_PARAMETERS']['MENU_SHOW'] === 'Y'
    ],
    'SECTIONS' => $arParams['SECTIONS'] === 'Y',
    'DESCRIPTION' => [
        'DETAIL' => [
            'SHOW' => $arParams['DESCRIPTION_DETAIL_SHOW'] === 'Y',
            'NAME' => $arParams['DESCRIPTION_DETAIL_NAME'],
            'FROM_PREVIEW' => $arParams['DESCRIPTION_FROM_PREVIEW'] === 'Y'
        ],
        'PREVIEW' => [
            'SHOW' => $arParams['DESCRIPTION_PREVIEW_SHOW'] === 'Y'
        ]
    ],
    'DOCUMENTS' => [
        'SHOW' => $arParams['DOCUMENTS_SHOW'] === 'Y' && !empty($arParams['PROPERTY_DOCUMENTS']),
        'NAME' => $arParams['DOCUMENTS_NAME'],
        'POSITION' => ArrayHelper::fromRange(['content', 'column'], $arParams['DOCUMENTS_POSITION']),
        'COLUMNS' => ArrayHelper::fromRange([3, 4], $arParams['DOCUMENTS_COLUMNS'])
    ],
    'VIDEO' => [
        'SHOW' => $arParams['VIDEO_SHOW'] === 'Y' && !empty($arParams['PROPERTY_VIDEO']) && !empty($arParams['VIDEO_IBLOCK_ID']),
        'NAME' => $arParams['VIDEO_NAME']
    ],
    'ARTICLES' => [
        'SHOW' => $arParams['ARTICLES_SHOW'] === 'Y',
        'NAME' => $arParams['ARTICLES_NAME']
    ],
    'ADDITIONAL' => [
        'SHOW' => $arParams['PRODUCTS_ADDITIONAL_SHOW'] === 'Y'
    ],
    'ACCESSORIES' => [
        'SHOW' => $arParams['PRODUCTS_ACCESSORIES_SHOW'] === 'Y',
        'NAME' => $arParams['PRODUCTS_ACCESSORIES_NAME'],
        'VIEW' => ArrayHelper::fromRange(['list', 'tile', 'link'], $arParams['PRODUCTS_ACCESSORIES_VIEW']),
        'LINK' => null,
    ],
    'RECOMMENDED' => [
        'SHOW' => $arParams['PRODUCTS_RECOMMENDED_SHOW'] === 'Y',
        'NAME' => $arParams['PRODUCTS_RECOMMENDED_NAME'],
        'POSITION' => ArrayHelper::fromRange(['content', 'column'], $arParams['PRODUCTS_RECOMMENDED_POSITION'])
    ],
    'ASSOCIATED' => [
        'SHOW' => $arParams['PRODUCTS_ASSOCIATED_SHOW'] === 'Y',
        'NAME' => $arParams['PRODUCTS_ASSOCIATED_NAME'],
        'POSITION' => ArrayHelper::fromRange(['content', 'column'], $arParams['PRODUCTS_ASSOCIATED_POSITION'])
    ],
    'SERVICES' => [
        'SHOW' => $arParams['SERVICES_SHOW'] === 'Y',
        'NAME' => $arParams['SERVICES_NAME']
    ],
    'INFORMATION' => [
        'BUY' => [
            'SHOW' => $arParams['INFORMATION_BUY_SHOW'] === 'Y',
            'NAME' => $arParams['INFORMATION_BUY_NAME'],
            'PATH' => StringHelper::replaceMacros($arParams['INFORMATION_BUY_PATH'], [
                'SITE_DIR' => SITE_DIR
            ]),
            'POSITION' => ArrayHelper::fromRange(['wide', 'column'], $arParams['INFORMATION_BUY_POSITION'])
        ],
        'PAYMENT' => [
            'SHOW' => $arParams['INFORMATION_PAYMENT_SHOW'] === 'Y',
            'NAME' => $arParams['INFORMATION_PAYMENT_NAME'],
            'PATH' => StringHelper::replaceMacros($arParams['INFORMATION_PAYMENT_PATH'], [
                'SITE_DIR' => SITE_DIR
            ]),
            'POSITION' => ArrayHelper::fromRange(['wide', 'column'], $arParams['INFORMATION_PAYMENT_POSITION'])
        ],
        'SHIPMENT' => [
            'SHOW' => $arParams['INFORMATION_SHIPMENT_SHOW'] === 'Y',
            'NAME' => $arParams['INFORMATION_SHIPMENT_NAME'],
            'PATH' => StringHelper::replaceMacros($arParams['INFORMATION_SHIPMENT_PATH'], [
                'SITE_DIR' => SITE_DIR
            ]),
            'POSITION' => ArrayHelper::fromRange(['wide', 'column'], $arParams['INFORMATION_SHIPMENT_POSITION'])
        ],
        'ADDITIONAL_1' => [
            'SHOW' => $arParams['INFORMATION_ADDITIONAL_1_SHOW'] === 'Y',
            'NAME' => $arParams['INFORMATION_ADDITIONAL_1_NAME'],
            'PATH' => StringHelper::replaceMacros($arParams['INFORMATION_ADDITIONAL_1_PATH'], [
                'SITE_DIR' => SITE_DIR
            ]),
            'POSITION' => ArrayHelper::fromRange(['wide', 'column'], $arParams['INFORMATION_ADDITIONAL_1_POSITION'])
        ],
        'ADDITIONAL_2' => [
            'SHOW' => $arParams['INFORMATION_ADDITIONAL_2_SHOW'] === 'Y',
            'NAME' => $arParams['INFORMATION_ADDITIONAL_2_NAME'],
            'PATH' => StringHelper::replaceMacros($arParams['INFORMATION_ADDITIONAL_2_PATH'], [
                'SITE_DIR' => SITE_DIR
            ]),
            'POSITION' => ArrayHelper::fromRange(['wide', 'column'], $arParams['INFORMATION_ADDITIONAL_2_POSITION'])
        ],
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
    'MAIN_VIEW' => ArrayHelper::fromRange(['1', '2', '3'], $arParams['MAIN_VIEW']),
    'MEASURES' => [
        'USE' => $arParams['MEASURES_USE'] === 'Y',
        'POSITION' => ArrayHelper::fromRange(['top', 'bottom'], $arParams['MEASURES_POSITION'])
    ],
    'GIFTS' => [
        'SHOW' => $arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'POSITION' => ArrayHelper::fromRange(['content', 'column'], $arParams['GIFTS_POSITION']),
        'VIEW' => ArrayHelper::fromRange(['1', '2', '3', '4'], $arParams['GIFTS_VIEW']),
        'COLUMNS' => ArrayHelper::fromRange(['auto', '1', '2', '3', '4'], $arParams['GIFTS_COLUMNS']),
        'QUANTITY' => $arParams['GIFTS_QUANTITY'] <= 0 ? 20 : $arParams['GIFTS_QUANTITY']
    ],
    'OFFERS' => [
        'DESCRIPTION' => [
            'SHOW' => $arParams['SHOW_SKU_DESCRIPTION'] === 'Y'
        ],
        'PROPERTIES' => [
            'SHOW' => $arParams['OFFERS_PROPERTIES_SHOW'] === 'Y',
            'COUNT' => !empty($arParams['OFFERS_PROPERTIES_COUNT']) ? $arParams['OFFERS_PROPERTIES_COUNT'] : 0,
            'DELIMITER' => !empty($arParams['OFFERS_PROPERTIES_DELIMITER']) ? $arParams['OFFERS_PROPERTIES_DELIMITER'] : ','
        ]
    ],
    'DEACTIVATED' => [
        'SHOW' => $arParams['SHOW_DEACTIVATED'] === 'Y'
    ],
    'RECALCULATION_ALWAYS_SHOW' => $arParams['RECALCULATION_ALWAYS_SHOW'] === 'Y' && $arParams['RECALCULATION_PRICES_USE'] === 'Y'
];

if ($arVisual['GIFTS']['POSITION'] === 'column')
    $arVisual['GIFTS']['VIEW'] = 4;

if (!$bMeasures)
    $arVisual['MEASURES']['USE'] = false;

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_BASKET_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_REQUEST');

if($arVisual['CREDIT']['SHOW'] && !empty($arVisual['CREDIT']['DURATION']))
    if(is_numeric($arVisual['CREDIT']['DURATION'])){
        if($arVisual['CREDIT']['DURATION'] < 2)
            $arVisual['CREDIT']['DURATION'] = 2;
    }
    else
        $arVisual['CREDIT']['DURATION'] = 2;

if($arVisual['CREDIT']['LINK']['USE'])
    if(empty($arVisual['CREDIT']['LINK']['VALUE']))
        $arVisual['CREDIT']['LINK']['USE'] = false;

if ($arVisual['SIZES']['SHOW'] && empty($arVisual['SIZES']['PATH']))
    $arVisual['SIZES']['SHOW'] = false;

if ($arVisual['STORES']['USE'] && $arVisual['STORES']['POSITION'] === 'popup' && !$arVisual['QUANTITY']['SHOW'] && $arResult['SKU']['VIEW'] !== 'dynamic')
    $arVisual['STORES']['POSITION'] = 'content';

if ($arVisual['PROPERTIES']['PREVIEW']['COUNT'] < 1)
    $arVisual['PROPERTIES']['PREVIEW']['COUNT'] = 5;

if ($arVisual['INFORMATION']['BUY']['SHOW'] && empty($arVisual['INFORMATION']['BUY']['PATH']))
    $arVisual['INFORMATION']['BUY']['SHOW'] = false;

if ($arVisual['ACCESSORIES']['SHOW'] && $arVisual['ACCESSORIES']['VIEW'] === 'link') {
    if (!empty($arParams['PRODUCTS_ACCESSORIES_LINK']))
        $arVisual['ACCESSORIES']['LINK'] = $arParams['PRODUCTS_ACCESSORIES_LINK'];
    else
        $arVisual['ACCESSORIES']['SHOW'] = false;

    if ($arVisual['ACCESSORIES']['SHOW']) {
        if (!empty($arParams['PRODUCTS_ACCESSORIES_LINK_REQUEST_NAME']))
            $sAccessoriesRequest = $arParams['PRODUCTS_ACCESSORIES_LINK_REQUEST_NAME'];
        else
            $sAccessoriesRequest = 'PRODUCT_ID';

        $arVisual['ACCESSORIES']['LINK'] = $arVisual['ACCESSORIES']['LINK'] . '?' . $sAccessoriesRequest . '=' . $arResult['ID'];
    }
}

if ($arVisual['INFORMATION']['PAYMENT']['SHOW'] && empty($arVisual['INFORMATION']['PAYMENT']['PATH']))
    $arVisual['INFORMATION']['PAYMENT']['SHOW'] = false;

if ($arVisual['INFORMATION']['SHIPMENT']['SHOW'] && empty($arVisual['INFORMATION']['SHIPMENT']['PATH']))
    $arVisual['INFORMATION']['SHIPMENT']['SHOW'] = false;

if ($arVisual['INFORMATION']['ADDITIONAL_1']['SHOW'] && empty($arVisual['INFORMATION']['ADDITIONAL_1']['PATH']))
    $arVisual['INFORMATION']['ADDITIONAL_1']['SHOW'] = false;

if ($arVisual['INFORMATION']['ADDITIONAL_2']['SHOW'] && empty($arVisual['INFORMATION']['ADDITIONAL_2']['PATH']))
    $arVisual['INFORMATION']['ADDITIONAL_2']['SHOW'] = false;

if ($arVisual['MAIN_VIEW'] === '2') {
    $arVisual['DOCUMENTS']['POSITION'] = 'content';
    $arVisual['RECOMMENDED']['POSITION'] = 'content';
    $arVisual['ASSOCIATED']['POSITION'] = 'content';
    $arVisual['BRAND']['ADDITIONAL']['POSITION'] = 'content';
}

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'order',
    'request'
], $arParams['ACTION']);

if ($arResult['ACTION'] === 'buy' || $arResult['ACTION'] === 'order') {
    $bRequestUse = !empty(ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_REQUEST_USE'],
        'VALUE'
    ]));

    $bOrderUse = !empty(ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_ORDER_USE'],
        'VALUE'
    ]));

    if ($bOrderUse)
        $arResult['ACTION'] = 'order';

    if ($bRequestUse)
        $arResult['ACTION'] = 'request';

    unset($bRequestUse, $bOrderUse);
}

$arResult['DELAY'] = [
    'USE' => $arParams['DELAY_USE'] === 'Y' && $arResult['ACTION'] === 'buy'
];

$arResult['COMPARE'] = [
    'USE' => $arParams['USE_COMPARE'] === 'Y',
    'CODE' => $arParams['COMPARE_NAME']
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

$arResult['URL'] = [
    'BASKET' => $arParams['BASKET_URL'],
    'CONSENT' => $arParams['CONSENT_URL']
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, [
        'SITE_DIR' => SITE_DIR
    ]);

$arResult['FORM'] = [
    'ORDER' => [
        'SHOW' => $arResult['ACTION'] === 'order',
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => $arParams['FORM_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
        ]
    ],
    'REQUEST' => [
        'SHOW' => $arResult['ACTION'] === 'request',
        'ID' => $arParams['FORM_REQUEST_ID'],
        'TEMPLATE' => $arParams['FORM_REQUEST_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_REQUEST_PROPERTY_PRODUCT']
        ]
    ],
    'CHEAPER' => [
        'SHOW' => $arParams['FORM_CHEAPER_SHOW'] === 'Y',
        'ID' => $arParams['FORM_CHEAPER_ID'],
        'TEMPLATE' => $arParams['FORM_CHEAPER_TEMPLATE'],
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_CHEAPER_PROPERTY_PRODUCT']
        ]
    ]
];

if ($arResult['FORM']['ORDER']['SHOW'] && empty($arResult['FORM']['ORDER']['ID']))
    $arResult['ACTION'] = 'none';

if ($arResult['FORM']['REQUEST']['SHOW'] && empty($arResult['FORM']['REQUEST']['ID']))
    $arResult['ACTION'] = 'none';

if ($arResult['FORM']['CHEAPER']['SHOW'] && empty($arResult['FORM']['CHEAPER']['ID']))
    $arResult['FORM']['CHEAPER']['SHOW'] = false;

$arResult['SIZES'] = [
    'SHOW' => $arParams['SIZES_SHOW'] === 'Y',
    'PATH' => StringHelper::replaceMacros($arParams['SIZES_PATH'], [
        'SITE_DIR' => SITE_DIR
    ]),
    'MODE' => ArrayHelper::fromRange(['all', 'element'], $arParams['SIZES_MODE'])
];

if ($arResult['SIZES']['SHOW'] &&  empty($arResult['SIZES']['PATH']))
    $arResult['SIZES']['SHOW'] = false;

if ($bLite) {
    $arResult['DELAY']['USE'] = false;

    include(__DIR__ . '/modifiers/lite/catalog.php');
}

include(__DIR__.'/modifiers/fields.php');
include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/delivery.calculation.php');
include(__DIR__.'/modifiers/order.fast.php');
include(__DIR__.'/modifiers/reviews.php');
include(__DIR__.'/modifiers/shares.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

if ($arVisual['MEASURES']['USE'])
    include(__DIR__.'/modifiers/measures.php');

$arResult['GALLERY_VIDEO'] = [
    'PRODUCT' => [],
    'OFFERS' => []
];

if ($arVisual['GALLERY']['VIDEO']['USE'])
    include(__DIR__.'/modifiers/gallery.video.php');

if (empty($arResult['GALLERY_VIDEO']['PRODUCT']) && empty($arResult['GALLERY_VIDEO']['OFFERS']))
    $arVisual['GALLERY']['VIDEO']['USE'] = false;

if (empty($arResult['DISPLAY_PROPERTIES'])) {
    $arVisual['PROPERTIES']['PREVIEW']['SHOW'] = false;
    $arVisual['PROPERTIES']['DETAIL']['SHOW'] = false;
} else {
    if ($arVisual['PROPERTIES']['PREVIEW']['SHOW']) {
        $arVisual['PROPERTIES']['PREVIEW']['SHOW'] = false;

        foreach ($arResult['DISPLAY_PROPERTIES'] as &$arProperty)
            if (empty($arProperty['USER_TYPE'])) {
                $arVisual['PROPERTIES']['PREVIEW']['SHOW'] = true;
                break;
            }

        unset($arProperty);
    }
}

if ($arResult['ACTION'] !== 'buy') {
    $arVisual['COUNTER']['SHOW'] = false;
    $arResult['ORDER_FAST']['USE'] = false;

    if ($arResult['ACTION'] === 'request') {
        $arVisual['CREDIT']['SHOW'] = false;
        $arVisual['MEASURES']['USE'] = false;
        $arVisual['PRICE']['SHOW'] = false;
        $arVisual['PRICE']['RANGE'] = false;
        $arVisual['PRICE']['RECALCULATION'] = false;
        $arVisual['PRICE_INFO']['SHOW'] = false;
        $arVisual['TIMER']['SHOW'] = false;
        $arResult['FORM']['CHEAPER']['SHOW'] = false;
        $arResult['DELIVERY_CALCULATION']['USE'] = false;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);

$this->getComponent()->setResultCacheKeys(['PREVIEW_PICTURE', 'DETAIL_PICTURE']);