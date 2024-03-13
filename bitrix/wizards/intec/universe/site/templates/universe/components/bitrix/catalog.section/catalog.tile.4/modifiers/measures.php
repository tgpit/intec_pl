<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\measures\helpers\ProductsHelper;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['MEASURES'] = ProductsHelper::getMeasures($arItem['ID']);

    if ($arItem['VISUAL']['ACTION'] !== 'request' && count($arItem['MEASURES']) > 1)
        $arItem['VISUAL']['MEASURES']['USE'] = true;

    if (!empty($arItem['OFFERS'])) {
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $arOffer['MEASURES'] = ProductsHelper::getMeasures($arOffer['ID']);

            if ($arItem['VISUAL']['ACTION'] !== 'request' && count($arOffer['MEASURES']) > 1)
                $arItem['VISUAL']['MEASURES']['USE'] = true;
        }
    }
}