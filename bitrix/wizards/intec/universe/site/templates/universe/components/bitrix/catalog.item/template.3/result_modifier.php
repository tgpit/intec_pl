<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */


$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'ARTICLE_SHOW' => 'N',
    'PROPERTY_ARTICLE' => null,
    'PROPERTY_PICTURES' => null,
    'VOTE_SHOW' => 'N',
    'VOTE_MODE' => 'rating',
    'IMAGE_SLIDER_SHOW' => 'Y',
    'IMAGE_ASPECT_RATIO' => '1:1',
    'IMAGE_SLIDER_NAV_SHOW' => 'N',
    'IMAGE_SLIDER_OVERLAY_USE' => 'Y',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_FEW' => 10,
    'QUANTITY_BOUNDS_MANY' => 50,
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'ARTICLE' => [
        'SHOW' => $arParams['ARTICLE_SHOW'] === 'Y'
    ],
    'IMAGE' => [
        'ASPECT_RATIO' => 100,
        'SLIDER' => $arParams['IMAGE_SLIDER_SHOW'] === 'Y',
        'NAV' => $arParams['IMAGE_SLIDER_NAV_SHOW'] === 'Y',
        'OVERLAY' => $arParams['IMAGE_SLIDER_OVERLAY_USE'] === 'Y'
    ],
    'VOTE' => [
        'SHOW' => $arParams['VOTE_SHOW'] === 'Y',
        'MODE' => ArrayHelper::fromRange(['average', 'rating'], $arParams['VOTE_MODE'])
    ],
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ],
        'ORDER' => [
            'TEXT' => $arParams['PURCHASE_ORDER_BUTTON_TEXT']
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
];

if (!empty($arResult['ITEM']['OFFERS'])) {
    $arPriceMinimal = [];

    foreach ($arResult['ITEM']['OFFERS'] as $arOffer) {
        $arPriceCurrent = ArrayHelper::getFirstValue($arOffer['ITEM_PRICES']);

        if (empty($arPriceMinimal) || $arPriceMinimal['RATIO_BASE_PRICE'] > $arPriceCurrent['RATIO_BASE_PRICE'])
            $arPriceMinimal = $arPriceCurrent;
    }

    $arResult['ITEM']['ITEM_PRICES'][] = $arPriceMinimal;
}

if (!empty($arResult['ITEM'])) {
    include(__DIR__ . '/modifiers/pictures.php');


    $arResult['ITEM']['ARTICLE'] = [
        'SHOW' => false,
        'VALUE' => null
    ];

    if (!empty($arParams['PROPERTY_ARTICLE'])) {
        $arProperty = ArrayHelper::getValue($arResult['ITEM'], [
            'PROPERTIES',
            $arParams['PROPERTY_ARTICLE'],
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arResult['ITEM']['ARTICLE']['VALUE'] = $arProperty;
        }

        if (!empty($arResult['ITEM']['ARTICLE']['VALUE']))
            $arResult['ITEM']['ARTICLE']['SHOW'] = $arVisual['ARTICLE']['SHOW'];
    }
}
$arResult['VISUAL'] = $arVisual;