<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 */

foreach ($arResult['ITEMS'] as &$arItem) {
    if (empty($arItem['OFFERS']) && !empty($arItem['ITEM_PRICES'])) {
        $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

        if ($arPrice['DISCOUNT'] > 0 && !$arVisual['DISCOUNT']['GLOBAL'])
            $arVisual['DISCOUNT']['GLOBAL'] = true;
    }

    if (!empty($arItem['OFFERS'])) {
        $arItem['CAN_BUY'] = false;

        $arPrices = [];
        $arPrice = [];

        foreach ($arItem['OFFERS'] as &$arOffer) {
            if (!empty($arOffer['ITEM_PRICES'])) {
                if (empty($arPrices) || $arPrices[0]['PRICE'] > $arOffer['ITEM_PRICES'][0]['PRICE'])
                    $arPrices = $arOffer['ITEM_PRICES'];

                if ($arPrices[0]['DISCOUNT'] > 0 && !$arVisual['DISCOUNT']['GLOBAL'])
                    $arVisual['DISCOUNT']['GLOBAL'] = true;
            }

            if (!empty($arOffer['MIN_PRICE'])) {
                if (empty($arPrice) || $arPrice['DISCOUNT_VALUE'] > $arOffer['MIN_PRICE']['DISCOUNT_VALUE'])
                    $arPrice = $arOffer['MIN_PRICE'];

                if ($arPrice['DISCOUNT'] > 0 && !$arVisual['DISCOUNT']['GLOBAL'])
                    $arVisual['DISCOUNT']['GLOBAL'] = true;
            }

            unset($arOffer);
        }

        $arItem['MIN_PRICE'] = $arPrice;
        $arItem['ITEM_PRICES'] = $arPrices;

        unset($arPrices, $arPrice);
    }

    unset($arItem);
}