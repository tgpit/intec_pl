<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;
else if (Loader::includeModule('intec.startshop'))
    $bLite = true;

$arMacros = [
    'SITE_DIR' => SITE_DIR,
    'SITE_TEMPLATE_PATH' => SITE_TEMPLATE_PATH.'/'
];

$bMeasures = false;

if ($bBase && Loader::includeModule('intec.measures'))
    $bMeasures = true;

$arParams = ArrayHelper::merge([
    'PROPERTY_ARTICLE' => null,
    'PROPERTY_ARTICLES' => null,
    'ARTICLES_SHOW' => 'N',
    'ARTICLES_NAME' => null,
    'PROPERTY_BRAND' => null,
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ACCESSORIES' => null,
    'PROPERTY_PICTURES' => null,
    'OFFERS_PROPERTY_ARTICLE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'PROPERTY_ADDITIONAL' => null,
    'PROPERTY_ASSOCIATED' => null,
    'PROPERTY_RECOMMENDED' => null,
    'PROPERTY_SERVICES' => null,
    'PROPERTY_ORDER_USE' => null,
    'MARKS_SHOW' => 'N',
    'MARKS_ORIENTATION' => 'horizontal',
    'GALLERY_PANEL' => 'N',
    'GALLERY_POPUP' => 'N',
    'GALLERY_ZOOM' => 'N',
    'GALLERY_PREVIEW' => 'N',
    'GALLERY_VIDEO_USE' => 'N',
    'PRICE_RANGE' => 'N',
    'PRICE_DIFFERENCE' => 'N',
    'ARTICLE_SHOW' => 'N',
    'ACTION' => 'none',
    'COUNTER_SHOW' => 'N',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'ADDITIONAL_SHOW' => 'N',
    'DESCRIPTION_SHOW' => 'N',
    'DESCRIPTION_NAME' => null,
    'DESCRIPTION_MODE' => 'detail',
    'DESCRIPTION_EXPANDED' => 'N',
    'OFFERS_NAME' => null,
    'OFFERS_EXPANDED' => 'N',
    'PROPERTIES_SHOW' => 'N',
    'PROPERTIES_NAME' => null,
    'PROPERTIES_EXPANDED' => 'N',
    'OFFERS_PROPERTIES_SHOW' => 'Y',
    'OFFERS_PROPERTIES_COUNT' => 3,
    'OFFERS_PROPERTIES_DELIMITER' => ',',
    'ASSOCIATED_SHOW' => 'N',
    'ASSOCIATED_NAME' => null,
    'ASSOCIATED_EXPANDED' => 'N',
    'RECOMMENDED_SHOW' => 'N',
    'RECOMMENDED_NAME' => null,
    'RECOMMENDED_EXPANDED' => 'N',
    'PRODUCTS_ACCESSORIES_EXPANDED' => 'N',
    'PRODUCTS_ACCESSORIES_SHOW' => 'N',
    'SERVICES_SHOW' => 'N',
    'SERVICES_NAME' => null,
    'SERVICES_EXPANDED' => 'N',
    'INFORMATION_PAYMENT_SHOW' => 'N',
    'INFORMATION_PAYMENT_PATH' => null,
    'INFORMATION_SHIPMENT_SHOW' => 'N',
    'INFORMATION_SHIPMENT_PATH' => null,
    'LAZYLOAD_USE' => 'N',
    'PANEL_SHOW' => 'N',
    'PANEL_MOBILE_SHOW' => 'N',
    'QUANTITY_SHOW' => 'N',
    'PANEL_QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 10,
    'QUANTITY_BOUNDS_MANY' => 50,
    'BASKET_URL' => null,
    'CONSENT_URL' => null,
    'PRINT_SHOW' => 'N',
    'DELIVERY_CALCULATION_USE' => 'N',
    'DELIVERY_CALCULATION_TEMPLATE' => null,
    'WIDE' => 'Y',
    'PROPERTY_ADVANTAGES' => null,
    'ADVANTAGES_SHOW' => 'N',
    'FORM_CHEAPER_SHOW' => 'N',
    'FORM_CHEAPER_ID' => null,
    'FORM_CHEAPER_TEMPLATE' => null,
    'FORM_CHEAPER_PROPERTY_PRODUCT' => null,
    'PRICE_CREDIT_SHOW' => 'N',
    'PRICE_CREDIT_DURATION' => null,
    'RECALCULATION_PRICE_CREDIT_USE' => 'N',
    'PRICE_CREDIT_LINK_USE' => "N",
    'PRICE_CREDIT_LINK' => null,
    'MEASURES_USE' => 'N',
    'RECALCULATION_PRICES_USE' => 'N',
    'TIMER_SHOW' => 'N',
    'TIMER_TIMER_QUANTITY_OVER' => 'Y',
    'PURCHASE_REQUEST_BUTTON_TEXT' => null,
    'SHOW_SKU_DESCRIPTION' => 'N'
], $arParams);

$arCodes = [
    'ARTICLE' => $arParams['PROPERTY_ARTICLE'],
    'MARKS' => [
        'HIT' => $arParams['PROPERTY_MARKS_HIT'],
        'NEW' => $arParams['PROPERTY_MARKS_NEW'],
        'RECOMMEND' => $arParams['PROPERTY_MARKS_RECOMMEND'],
        'SHARE' => $arParams['PROPERTY_MARKS_SHARE']
    ],
    'PICTURES' => $arParams['PROPERTY_PICTURES'],
    'OFFERS' => [
        'ARTICLE' => $arParams['OFFERS_PROPERTY_ARTICLE'],
        'PICTURES' => $arParams['OFFERS_PROPERTY_PICTURES']
    ],
    'ADDITIONAL' => $arParams['PROPERTY_ADDITIONAL'],
    'ASSOCIATED' => $arParams['PROPERTY_ASSOCIATED'],
    'RECOMMENDED' => $arParams['PROPERTY_RECOMMENDED'],
    'ARTICLES' => $arParams['PROPERTY_ARTICLES']
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'PANEL' => [
        'DESKTOP' => [
            'SHOW' => $arParams['PANEL_SHOW'] === 'Y'
        ],
        'MOBILE' => [
            'SHOW' => $arParams['PANEL_MOBILE_SHOW'] === 'Y'
        ]
    ],
    'BRAND' => [
        'SHOW' => $arParams['BRAND_SHOW'] === 'Y'
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y',
        'ORIENTATION' => ArrayHelper::fromRange(['horizontal', 'vertical'], $arParams['MARKS_ORIENTATION'])
    ],
    'GALLERY' => [
        'PANEL' => $arParams['GALLERY_PANEL'] === 'Y',
        'POPUP' => $arParams['GALLERY_POPUP'] === 'Y',
        'ZOOM' => $arParams['GALLERY_ZOOM'] === 'Y',
        'PREVIEW' => $arParams['GALLERY_PREVIEW'] === 'Y',
        'VIDEO' => [
            'USE' => $arParams['GALLERY_VIDEO_USE'] === 'Y',
            'CONTROLS' => $arParams['GALLERY_VIDEO_CONTROLS_SHOW'] === 'Y',
            'ELEMENTS' => []
        ]
    ],
    'PRICE' => [
        'SHOW' => true,
        'RANGE' => $arParams['PRICE_RANGE'] === 'Y',
        'DIFFERENCE' =>$arParams['PRICE_DIFFERENCE'] === 'Y',
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y'
    ],
    'ARTICLE' => [
        'SHOW' => $arParams['ARTICLE_SHOW'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y',
        'MESSAGE' => [
            'MAX' => [
                'SHOW' => $arParams['COUNTER_MESSAGE_MAX_SHOW'] === 'Y'
            ]
        ]
    ],
    'ADDITIONAL' => [
        'SHOW' => $arParams['ADDITIONAL_SHOW'] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
        'NAME' => !empty($arParams['DESCRIPTION_NAME']) ? Html::encode(trim($arParams['DESCRIPTION_NAME'])) : null,
        'MODE' => ArrayHelper::fromRange(['detail', 'preview'], $arParams['DESCRIPTION_MODE']),
        'EXPANDED' => $arParams['DESCRIPTION_EXPANDED'] === 'Y'
    ],
    'OFFERS' => [
        'NAME' => !empty($arParams['OFFERS_NAME']) ? Html::encode(trim($arParams['OFFERS_NAME'])) : null,
        'DESCRIPTION' => [
            'SHOW' => $arParams['SHOW_SKU_DESCRIPTION'] === 'Y'
        ],
        'EXPANDED' => $arParams['OFFERS_EXPANDED'] === 'Y',
        'PROPERTIES' => [
            'SHOW' => $arParams['OFFERS_PROPERTIES_SHOW'] === 'Y',
            'COUNT' => !empty($arParams['OFFERS_PROPERTIES_COUNT']) ? $arParams['OFFERS_PROPERTIES_COUNT'] : 0,
            'DELIMITER' => !empty($arParams['OFFERS_PROPERTIES_DELIMITER']) ? $arParams['OFFERS_PROPERTIES_DELIMITER'] : ','
        ]
    ],
    'PROPERTIES' => [
        'SHOW' => $arParams['PROPERTIES_SHOW'] === 'Y',
        'NAME' => !empty($arParams['PROPERTIES_NAME']) ? Html::encode(trim($arParams['PROPERTIES_NAME'])) : null,
        'EXPANDED' => $arParams['PROPERTIES_EXPANDED'] === 'Y'
    ],
    'INFORMATION' => [
        'PAYMENT' => [
            'SHOW' => $arParams['INFORMATION_PAYMENT_SHOW'] === 'Y',
            'PATH' => $arParams['INFORMATION_PAYMENT_PATH']
        ],
        'SHIPMENT' => [
            'SHOW' => $arParams['INFORMATION_SHIPMENT_SHOW'] === 'Y',
            'PATH' => $arParams['INFORMATION_SHIPMENT_PATH']
        ]
    ],
    'ASSOCIATED' => [
        'SHOW' => $arParams['ASSOCIATED_SHOW'] === 'Y',
        'NAME' => !empty($arParams['ASSOCIATED_NAME']) ? Html::encode(trim($arParams['ASSOCIATED_NAME'])) : null,
        'EXPANDED' => $arParams['ASSOCIATED_EXPANDED'] === 'Y'
    ],
    'RECOMMENDED' => [
        'SHOW' => $arParams['RECOMMENDED_SHOW'] === 'Y',
        'NAME' => !empty($arParams['RECOMMENDED_NAME']) ? Html::encode(trim($arParams['RECOMMENDED_NAME'])) : null,
        'EXPANDED' => $arParams['RECOMMENDED_EXPANDED'] === 'Y'
    ],
    'ACCESSORIES' => [
        'SHOW' => $arParams['PRODUCTS_ACCESSORIES_SHOW'] === 'Y' && !empty($arParams['PROPERTY_ACCESSORIES']),
        'EXPANDED' => $arParams['PRODUCTS_ACCESSORIES_EXPANDED'] === 'Y',
        'NAME' => $arParams['PRODUCTS_ACCESSORIES_NAME'],
        'VIEW' => ArrayHelper::fromRange(['list', 'tile', 'link'], $arParams['PRODUCTS_ACCESSORIES_VIEW']),
        'LINK' => null,
    ],
    'SERVICES' => [
        'SHOW' => $arParams['SERVICES_SHOW'] === 'Y',
        'NAME' => $arParams['SERVICES_NAME'],
        'EXPANDED' => $arParams['SERVICES_EXPANDED'] === 'Y'
    ],
    'QUANTITY' => [
        'PANEL' => [
            'SHOW' => $arParams['PANEL_QUANTITY_SHOW'] === 'Y',
        ],
        'MAIN' => [
            'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        ],
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => Type::toFloat($arParams['QUANTITY_BOUNDS_FEW']),
            'MANY' => Type::toFloat($arParams['QUANTITY_BOUNDS_MANY'])
        ]
    ],
    'PRINT' => [
        'SHOW' => $arParams['PRINT_SHOW'] === 'Y'
    ],
    'ARTICLES' => [
        'SHOW' => $arParams['ARTICLES_SHOW'] === 'Y',
        'NAME' => $arParams['ARTICLES_NAME'],
        'EXPANDED' => $arParams['ARTICLES_EXPANDED'] === 'Y'
    ],
    'ADVANTAGES' => [
        'SHOW' => $arParams['ADVANTAGES_SHOW'] === 'Y'
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
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y',
        'TIMER_QUANTITY_OVER' => $arParams['TIMER_TIMER_QUANTITY_OVER'] === 'Y'
    ],
    'MEASURES' => [
        'USE' => $arParams['MEASURES_USE'] === 'Y'
    ],
    'MENU' => [
        'SHOW' => $arResult['ORIGINAL_PARAMETERS']['MENU_SHOW'] === 'Y'
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
    'GIFTS' => [
        'SHOW' => $arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled('sale'),
        'VIEW' => ArrayHelper::fromRange(['1', '2', '3'], $arParams['GIFTS_VIEW'])
    ],
    'DEACTIVATED' => [
        'SHOW' => $arParams['SHOW_DEACTIVATED'] === 'Y'
    ],
];

if (empty($arVisual['BUTTONS']['BASKET']['TEXT']))
    $arVisual['BUTTONS']['BASKET']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_ADD');

if (empty($arVisual['BUTTONS']['ORDER']['TEXT']))
    $arVisual['BUTTONS']['ORDER']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_ORDER');

if (empty($arVisual['BUTTONS']['REQUEST']['TEXT']))
    $arVisual['BUTTONS']['REQUEST']['TEXT'] = Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_REQUEST');

if($arVisual['CREDIT']['SHOW'] && !empty($arVisual['CREDIT']['DURATION'])) {
    if(is_numeric($arVisual['CREDIT']['DURATION'])){
        if($arVisual['CREDIT']['DURATION'] < 2)
            $arVisual['CREDIT']['DURATION'] = 2;
    }
    else {
        $arVisual['CREDIT']['DURATION'] = 2;
    }
}

if($arVisual['CREDIT']['LINK']['USE'])
    if(empty($arVisual['CREDIT']['LINK']['VALUE']))
        $arVisual['CREDIT']['LINK']['USE'] = false;

$sDescriptionSource = strtoupper($arVisual['DESCRIPTION']['MODE']).'_TEXT';

if (empty($arResult[$sDescriptionSource]))
    $arVisual['DESCRIPTION']['SHOW'] = false;

if (empty($arVisual['INFORMATION']['PAYMENT']['PATH']))
    $arVisual['INFORMATION']['PAYMENT']['SHOW'] = false;

if (empty($arVisual['INFORMATION']['SHIPMENT']['PATH']))
    $arVisual['INFORMATION']['SHIPMENT']['SHOW'] = false;

if (!$arVisual['WIDE'])
    $arVisual['GALLERY']['PREVIEW'] = false;

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'order',
    'request'
], $arParams['ACTION']);

if ($arResult['ACTION'] === 'buy' || $arResult['ACTION'] === 'order') {
    $arRequestUse = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_REQUEST_USE'],
        'VALUE'
    ]);

    $arOrderUse = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arParams['PROPERTY_ORDER_USE'],
        'VALUE'
    ]);

    if (!empty($arOrderUse))
        $arResult['ACTION'] = 'order';

    if (!empty($arRequestUse))
        $arResult['ACTION'] = 'request';

    unset($arRequestUse, $arOrderUse);
}

$arResult['URL'] = [
    'BASKET' => $arParams['BASKET_URL'],
    'CONSENT' => $arParams['CONSENT_URL']
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

$arResult['FORM']['ORDER'] = [
    'SHOW' => $arResult['ACTION'] === 'order',
    'ID' => $arParams['FORM_ID'],
    'TEMPLATE' => $arParams['FORM_TEMPLATE'],
    'PROPERTIES' => [
        'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
    ]
];

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

include(__DIR__.'/modifiers/fields.php');
include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');
include(__DIR__.'/modifiers/marks.php');
include(__DIR__.'/modifiers/tab.php');
include(__DIR__.'/modifiers/shares.php');
include(__DIR__.'/modifiers/services.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($bBase)
    include(__DIR__.'/modifiers/delivery.calculation.php');

if (empty($arResult['DISPLAY_PROPERTIES']))
    $arVisual['PROPERTIES']['SHOW'] = false;

if (empty($arResult['ADDITIONAL']))
    $arVisual['ADDITIONAL']['SHOW'] = false;

if (empty($arResult['ASSOCIATED']))
    $arVisual['ASSOCIATED']['SHOW'] = false;

if (empty($arResult['ARTICLES']) || empty($arParams['ARTICLES_IBLOCK_ID']))
    $arVisual['ARTICLES']['SHOW'] = false;

if (empty($arResult['RECOMMENDED']))
    $arVisual['RECOMMENDED']['SHOW'] = false;

if (empty($arResult['ADVANTAGES']))
    $arVisual['ADVANTAGES']['SHOW'] = false;

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
        $arVisual['ACCESSORIES']['EXPANDED'] = false;
    }
}

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['SKU_VIEW'] = ArrayHelper::fromRange([
    'dynamic',
    'list'
], $arParams['SKU_VIEW']);

if (!empty($arResult['OFFERS']) && $arResult['SKU_VIEW'] == 'list') {
    $arVisual['PANEL']['DESKTOP']['SHOW'] = false;
    $arVisual['PANEL']['MOBILE']['SHOW'] = false;
}

$arResult['FORM']['CHEAPER'] = [
    'SHOW' => $arParams['FORM_CHEAPER_SHOW'] === 'Y',
    'ID' => $arParams['FORM_CHEAPER_ID'],
    'TEMPLATE' => $arParams['FORM_CHEAPER_TEMPLATE'],
    'PROPERTIES' => [
        'PRODUCT' => $arParams['FORM_CHEAPER_PROPERTY_PRODUCT']
    ]
];

$arResult['FORM']['REQUEST'] = [
    'SHOW' => $arResult['ACTION'] === 'request',
    'ID' => $arParams['FORM_REQUEST_ID'],
    'TEMPLATE' => $arParams['FORM_REQUEST_TEMPLATE'],
    'PROPERTIES' => [
        'PRODUCT' => $arParams['FORM_REQUEST_PROPERTY_PRODUCT']
    ]
];

if (empty($arResult['FORM']['CHEAPER']['ID']))
    $arResult['FORM']['CHEAPER']['SHOW'] = false;

if (empty($arResult['FORM']['REQUEST']['ID']))
    $arResult['FORM']['REQUEST']['SHOW'] = false;

if (!$bMeasures)
    $arVisual['MEASURES']['USE'] = false;

if ($arVisual['MEASURES']['USE'])
    include(__DIR__.'/modifiers/measures.php');

if ($arVisual['GALLERY']['VIDEO']['USE'])
    include(__DIR__.'/modifiers/video.php');

$arResult['ACCESSORIES'] = [
    'VALUES' => []
];

if ($arVisual['ACCESSORIES']['SHOW']) {
    if (ArrayHelper::keyExists($arParams['PROPERTY_ACCESSORIES'], $arResult['PROPERTIES'])) {
        if (!empty($arResult['PROPERTIES'][$arParams['PROPERTY_ACCESSORIES']]['VALUE'])) {
            $arResult['ACCESSORIES']['VALUES'] = $arResult['PROPERTIES'][$arParams['PROPERTY_ACCESSORIES']]['VALUE'];
        }
    }

    if (empty($arResult['ACCESSORIES']['VALUES']))
        $arVisual['ACCESSORIES']['SHOW'] = false;
}

if ($arResult['ACTION'] === 'request' && !$arResult['FORM']['REQUEST']['SHOW'])
    $arResult['ACTION'] = 'none';

if ($arResult['ACTION'] !== 'buy') {
    $arVisual['COUNTER']['SHOW'] = false;
    $arVisual['ADDITIONAL']['SHOW'] = false;

    if ($arResult['ACTION'] === 'request') {
        $arVisual['CREDIT']['SHOW'] = false;
        $arVisual['MEASURES']['USE'] = false;
        $arVisual['PRICE']['SHOW'] = false;
        $arVisual['PRICE']['RANGE'] = false;
        $arVisual['PRICE']['RECALCULATION'] = false;
        $arVisual['TIMER']['SHOW'] = false;
        $arResult['DELIVERY_CALCULATION']['USE'] = false;
        $arResult['FORM']['CHEAPER']['SHOW'] = false;
    }
}

$arResult['VISUAL'] = $arVisual;

$this->getComponent()->setResultCacheKeys(['PREVIEW_PICTURE', 'DETAIL_PICTURE']);

unset($arVisual);