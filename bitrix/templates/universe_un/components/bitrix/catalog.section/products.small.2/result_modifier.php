<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
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

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'COLUMNS' => 4,
    'BORDERS' => 'N',
    'NAME_ALIGN' => 'left',
    'PRICE_ALIGN' => 'left',
    'ACTION' => 'buy',
    'COUNTER_SHOW' => 'N',
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'WIDE' => 'Y',
    'RECALCULATION_PRICES_USE' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include (__DIR__ . '/modifiers/settings.php');

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => !defined('EDITOR') && $arParams['LAZYLOAD_USE'] === 'Y' ? Properties::get('template-images-lazyload-stub') : null
    ],
    'COLUMNS' => ArrayHelper::fromRange([4, 3, 2], $arParams['COLUMNS']),
    'BORDERS' => $arParams['BORDERS'] === 'Y',
    'NAME' => [
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['NAME_ALIGN'])
    ],
    'PRICE' => [
        'ALIGN' => ArrayHelper::fromRange(['left', 'center', 'right'], $arParams['PRICE_ALIGN']),
        'RECALCULATION' => $arParams['RECALCULATION_PRICES_USE'] === 'Y'
    ],
    'COUNTER' => [
        'SHOW' => $arParams['COUNTER_SHOW'] === 'Y'
    ],
    'WIDE' => $arParams['WIDE'] === 'Y'
];

if (!$arVisual['WIDE'] && $arVisual['COLUMNS'] > 3)
    $arVisual['COLUMNS'] = 3;

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'detail',
    'order',
    'request'
], $arParams['ACTION']);

if ($arResult['ACTION'] !== 'buy')
    $arVisual['COUNTER']['SHOW'] = false;

$arResult['URL'] = [];
$arUrl = [
    'BASKET' => $arParams['BASKET_URL'],
    'CONSENT' => $arParams['CONSENT_URL']
];

foreach ($arUrl as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

unset($arUrl, $sKey, $sUrl);

if ($bLite)
    include(__DIR__.'/modifiers/lite/catalog.php');

$arResult['FORM'] = [
    'ORDER' => [
        'SHOW' => !empty($arParams['FORM_ID']),
        'ID' => $arParams['FORM_ID'],
        'TEMPLATE' => !empty($arParams['FORM_TEMPLATE']) ? $arParams['FORM_TEMPLATE'] : '.default',
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
        ]
    ],
    'REQUEST' => [
        'SHOW' => !empty($arParams['FORM_REQUEST_ID']),
        'ID' => $arParams['FORM_REQUEST_ID'],
        'TEMPLATE' => !empty($arParams['FORM_REQUEST_TEMPLATE']) ? $arParams['FORM_REQUEST_TEMPLATE'] : '.default',
        'PROPERTIES' => [
            'PRODUCT' => $arParams['FORM_REQUEST_PROPERTY_PRODUCT']
        ]
    ]
];

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'OFFER' => !empty($arItem['OFFERS']),
        'ACTION' => $arResult['ACTION'],
        'COUNTER' => [
            'SHOW' => $arVisual['COUNTER']['SHOW'] && $arItem['CAN_BUY']
        ],
        'PRICE' => [
            'SHOW' => true,
            'RECALCULATION' => $arVisual['PRICE']['RECALCULATION'] && $arItem['CAN_BUY']
        ]
    ];

    $arData = &$arItem['DATA'];

    if ($arData['ACTION'] === 'buy' || $arData['ACTION'] === 'order') {
        $isOrder = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_ORDER_USE'],
            'VALUE'
        ]));

        if ($isOrder && $arResult['FORM']['ORDER']['SHOW'])
            $arData['ACTION'] = 'order';

        $isRequest = !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
            $arParams['PROPERTY_REQUEST_USE'],
            'VALUE'
        ]));

        if ($isRequest && $arResult['FORM']['REQUEST']['SHOW'])
            $arData['ACTION'] = 'request';
    }

    if ($arData['ACTION'] !== 'buy') {
        $arData['COUNTER']['SHOW'] = false;
        $arData['PRICE']['RECALCULATION'] = false;

        if ($arData['ACTION'] === 'request')
            $arData['PRICE']['SHOW'] = false;
    }

    if ($arItem['DATA']['OFFER']) {
        $arItem['DATA']['COUNTER']['SHOW'] = false;
        $arData['PRICE']['RECALCULATION'] = false;
    }

    unset($arData);
}

unset($arItem);

include(__DIR__.'/modifiers/pictures.php');

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

unset($bBase, $bLite, $arMacros, $arVisual);