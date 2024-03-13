<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

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

$arParams = ArrayHelper::merge([
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_ORDER_USE' => null,
    'OFFERS_PROPERTY_PICTURES' => null,
    'PROPERTY_TEXT' => null,
    'PROPERTY_REQUEST_USE' => null,
    'LAZYLOAD_USE' => 'N',
    'MEASURE_SHOW' => 'N',
    'MARKS_SHOW' => 'N',
    'WEIGHT_SHOW' => 'N',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 3,
    'QUANTITY_BOUNDS_MANY' => 10,
    'ACTION' => 'none',
    'COUNTER_SHOW' => 'N',
    'COUNTER_MESSAGE_MAX_SHOW' => 'Y',
    'DESCRIPTION_SHOW' => 'Y',
    'DESCRIPTION_MODE' => 'preview',
    'TEXT_SHOW' => 'N',
    'GALLERY_PANEL' => 'N',
    'GALLERY_PREVIEW' => 'N',
    'INFORMATION_PAYMENT' => 'N',
    'PAYMENT_URL' => null,
    'INFORMATION_SHIPMENT' => 'N',
    'SHIPMENT_URL' => null,
    'BUTTON_REQUEST_TEXT' => null,
    'BASKET_URL' => null,
    'SLIDE_USE' => 'N'
], $arParams);

$arCodes = [
    'MARKS' => [
        'HIT' => $arParams['PROPERTY_MARKS_HIT'],
        'NEW' => $arParams['PROPERTY_MARKS_NEW'],
        'RECOMMEND' => $arParams['PROPERTY_MARKS_RECOMMEND'],
        'SHARE' => $arParams['PROPERTY_MARKS_SHARE']
    ],
    'PICTURES' => $arParams['PROPERTY_PICTURES'],
    'TEXT' => $arParams['PROPERTY_TEXT'],
    'OFFERS' => [
        'PICTURES' => $arParams['OFFERS_PROPERTY_PICTURES']
    ]
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y'
    ],
    'PRICE' => [
        'SHOW' => true
    ],
    'ADDITIONAL_PRODUCTS' => [
        'SHOW' => $arParams['ADDITIONAL_PRODUCTS'] === 'Y' && !empty($arParams['PROPERTY_ADDITIONAL']),
        'VALUES' => []
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y'
    ],
    'WEIGHT' => [
        'SHOW' => $arParams['WEIGHT_SHOW'] === 'Y'
    ],
    'QUANTITY' => [
        'SHOW' => $arParams['QUANTITY_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], $arParams['QUANTITY_MODE']),
        'BOUNDS' => [
            'FEW' => $arParams['QUANTITY_BOUNDS_FEW'],
            'MANY' => $arParams['QUANTITY_BOUNDS_MANY']
        ]
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y',
        'MESSAGE' => [
            'MAX' => [
                'SHOW' => $arParams['COUNTER_MESSAGE_MAX_SHOW'] === 'Y'
            ]
        ]
    ],
    'MEASURE' => [
        'SHOW' => $arParams["MEASURE_SHOW"] === 'Y'
    ],
    'DESCRIPTION' => [
        'SHOW' => $arParams['DESCRIPTION_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'preview',
            'detail'
        ], $arParams['DESCRIPTION_MODE'])
    ],
    'TEXT' => [
        'SHOW' => $arParams['TEXT_SHOW'] === 'Y'
    ],
    'GALLERY' => [
        'PANEL' => $arParams['GALLERY_PANEL'] === 'Y',
        'PREVIEW' => $arParams['GALLERY_PREVIEW'] === 'Y'
    ],
    'INFORMATION' => [
        'PAYMENT' => $arParams['INFORMATION_PAYMENT'] === 'Y',
        'SHIPMENT' => $arParams['INFORMATION_SHIPMENT'] === 'Y'
    ],
    'SLIDE' => [
        'USE' => $arParams['SLIDE_USE'] === 'Y'
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y'
    ],
    'BUTTONS' => [
        'REQUEST' => [
            'TEXT' => $arParams['BUTTON_REQUEST_TEXT']
        ]
    ]
];

if (empty($arResult[$arVisual['DESCRIPTION']['MODE'] === 'preview' ? 'PREVIEW_TEXT' : 'DETAIL_TEXT']))
    $arVisual['DESCRIPTION']['SHOW'] = false;

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
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
        $arResult['ACTION'] = 'detail';

    if ($bRequestUse)
        $arResult['ACTION'] = 'request';
}

$arResult['URL'] = [
    'BASKET' => $arParams['BASKET_URL'],
    'PAYMENT' => $arParams['PAYMENT_URL'],
    'SHIPMENT' => $arParams['SHIPMENT_URL']
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

if (empty($arResult['URL']['PAYMENT']))
    $arVisual['INFORMATION']['PAYMENT'] = false;

if (empty($arResult['URL']['SHIPMENT']))
    $arVisual['INFORMATION']['SHIPMENT'] = false;

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

include(__DIR__.'/modifiers/fields.php');
include(__DIR__.'/modifiers/marks.php');
include(__DIR__.'/modifiers/pictures.php');
include(__DIR__.'/modifiers/properties.php');

if (empty($arResult['TEXT']))
    $arVisual['TEXT']['SHOW'] = false;

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arVisual['ADDITIONAL_PRODUCTS']['VALUES'] = ArrayHelper::getValue($arResult['PROPERTIES'], [
    $arParams['PROPERTY_ADDITIONAL'],
    'VALUE'
]);

if (empty($arVisual['ADDITIONAL_PRODUCTS']['VALUES']))
    $arVisual['ADDITIONAL_PRODUCTS']['SHOW'] = false;

if ($arResult['ACTION'] !== 'buy') {
    $arVisual['COUNTER']['SHOW'] = false;

    if ($arResult['ACTION'] === 'request') {
        $arVisual['ADDITIONAL_PRODUCTS']['SHOW'] = false;
        $arVisual['PRICE']['SHOW'] = false;
        $arVisual['TIMER']['SHOW'] = false;
    }
}

$arResult['VISUAL'] = $arVisual;

unset($arVisual);