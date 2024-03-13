<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

if (!empty($arResult['OFFERS'])) {
    $arResult['CAN_BUY'] = false;

    $arPrices = null;
    $arPrice = null;
    $arQuantity = [];

    foreach ($arResult['OFFERS'] as &$arOffer) {
        if (!empty($arOffer['ITEM_PRICES'])) {
            if ($arPrices === null || $arPrices[0]['PRICE'] > $arOffer['ITEM_PRICES'][0]['PRICE']) {
                $arPrices = $arOffer['ITEM_PRICES'];
                $arQuantity = [
                    'value' => $arOffer['CATALOG_QUANTITY'],
                    'ratio' => $arOffer['CATALOG_MEASURE_RATIO'],
                    'measure' => $arOffer['CATALOG_MEASURE_NAME'],
                    'trace' => $arOffer['CATALOG_QUANTITY_TRACE'],
                    'zero' => $arOffer['CATALOG_CAN_BUY_ZERO']
                ];
            }
        }

        if (!empty($arOffer['MIN_PRICE'])) {
            if ($arPrice === null || $arPrice['DISCOUNT_VALUE'] > $arOffer['MIN_PRICE']['DISCOUNT_VALUE']) {
                $arPrice = $arOffer['MIN_PRICE'];
            }
        }

        unset($arOffer);
    }

    $arResult['MIN_PRICE'] = $arPrice;
    $arResult['ITEM_PRICES'] = $arPrices;
    $arResult['CATALOG_QUANTITY'] = $arQuantity['value'];
    $arResult['CATALOG_MEASURE_RATIO'] = $arQuantity['ratio'];
    $arResult['CATALOG_MEASURE_NAME'] = $arQuantity['measure'];
    $arResult['CATALOG_QUANTITY_TRACE'] = $arQuantity['trace'];
    $arResult['CATALOG_CAN_BUY_ZERO'] = $arQuantity['zero'];

    unset($arPrices, $arPrice, $arQuantity);
}