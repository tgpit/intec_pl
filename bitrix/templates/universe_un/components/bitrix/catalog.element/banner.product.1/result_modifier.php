<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
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
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_ORDER_USE' => null,
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'ACTION' => 'none',
    'DELAY_USE' => 'N',
    'QUANTITY_SHOW' => 'Y',
    'QUANTITY_MODE' => 'number',
    'MARKS_SHOW' => 'Y',
    'MARKS_TEMPLATE' => null,
    'PRICE_DIFFERENCE' => 'N',
    'BASKET_URL' => null,
    'COMPARE_URL' => null,
    'COMPARE_NAME' => null,
    'USE_STORE' => 'N',
    'TIMER_SHOW' => 'N',
    'TIMER_TIMER_QUANTITY_OVER' => 'Y'
], $arParams);

$arVisual = [
    'WIDE' => $arParams['WIDE'] === 'Y',
    'QUANTITY' => [
        'SHOW' => ArrayHelper::getValue($arParams, 'QUANTITY_SHOW') === 'Y' && empty($arResult['OFFERS']),
        'MODE' => ArrayHelper::fromRange(['number', 'text', 'logic'], ArrayHelper::getValue($arParams, 'QUANTITY_MODE')),
        'BOUNDS' => [
            'FEW' => ArrayHelper::getValue($arParams, 'QUANTITY_BOUNDS_FEW'),
            'MANY' => ArrayHelper::getValue($arParams, 'QUANTITY_BOUNDS_MANY')
        ]
    ],
    'MARKS' => [
        'SHOW' => $arParams['MARKS_SHOW'] === 'Y' && !empty($arParams['MARKS_TEMPLATE']),
        'TEMPLATE' => $arParams['MARKS_TEMPLATE']
    ],
    'PRICE' => [
        'DIFFERENCE' => $arParams['PRICE_DIFFERENCE'] === 'Y'
    ],
    'TIMER' => [
        'SHOW' => $arParams['TIMER_SHOW'] === 'Y',
        'TIMER_QUANTITY_OVER' => $arParams['TIMER_TIMER_QUANTITY_OVER'] === 'Y'
    ],
    'VOTE' => [
        'SHOW' => ArrayHelper::getValue($arParams, 'VOTE_SHOW') === 'Y',
        'MODE' => ArrayHelper::fromRange([
            'average',
            'rating'
        ], $arParams['VOTE_MODE'])
    ],
];

$arResult['ACTION'] = ArrayHelper::fromRange([
    'none',
    'buy',
    'order'
], $arParams['ACTION']);

$arResult['COMPARE'] = [
    'USE' => $arParams['DISPLAY_COMPARE'],
    'CODE' => $arParams['COMPARE_NAME']
];

if (empty($arResult['COMPARE']['CODE']))
    $arResult['COMPARE']['USE'] = false;

$arResult['DELAY'] = [
    'USE' => $arParams['DELAY_USE'] === 'Y'
];

if ($arResult['ACTION'] !== 'buy') {
    $arResult['DELAY']['USE'] = false;
}

$arOrderUse = ArrayHelper::getValue($arResult, [
    'PROPERTIES',
    $arParams['PROPERTY_ORDER_USE'],
    'VALUE'
]);

if (!empty($arOrderUse) && $arResult['ACTION'] === 'buy') {
    $arResult['ACTION'] = 'order';
}

$arResult['URL'] = [
    'BASKET' => $arParams['BASKET_URL'],
    'CONSENT' => $arParams['CONSENT_URL']
];

$arResult['FORM']['ORDER'] = [
    'SHOW' => $arResult['ACTION'] === 'order',
    'ID' => $arParams['FORM_ID'],
    'TEMPLATE' => $arParams['FORM_TEMPLATE'],
    'PROPERTIES' => [
        'PRODUCT' => $arParams['FORM_PROPERTY_PRODUCT']
    ]
];

foreach ($arResult['URL'] as $sKey => $sUrl)
    $arResult['URL'][$sKey] = StringHelper::replaceMacros($sUrl, $arMacros);

if ($bLite) {
    $arResult['DELAY']['USE'] = false;

    include(__DIR__.'/modifiers/lite/catalog.php');

    if (!empty($arResult['OFFERS']))
        $arVisual['QUANTITY']['SHOW'] = false;
}

include(__DIR__.'/modifiers/marks.php');

if ($arVisual['TIMER']['SHOW']) {
    include(__DIR__.'/modifiers/timer.php');
}

if ($bBase)
    include(__DIR__.'/modifiers/base/catalog.php');

if ($bBase || $bLite)
    include(__DIR__.'/modifiers/catalog.php');

$arResult['VISUAL'] = $arVisual;

$this->getComponent()->setResultCacheKeys(['VISUAL']);