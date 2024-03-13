<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */


$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N',
    'WIDE' => 'N',
    'COLUMNS' => null
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'BUTTONS' => [
        'BASKET' => [
            'TEXT' => $arParams['PURCHASE_BASKET_BUTTON_TEXT']
        ],
        'ORDER' => [
            'TEXT' => $arParams['PURCHASE_ORDER_BUTTON_TEXT']
        ]
    ],
    'WIDE' => $arParams['WIDE'] === 'Y',
    'COLUMNS' => !empty($arParams['COLUMNS']) ? $arParams['COLUMNS'] : 0
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

$arResult['VISUAL'] = $arVisual;