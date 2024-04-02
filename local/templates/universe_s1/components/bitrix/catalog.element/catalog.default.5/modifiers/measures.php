<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\measures\helpers\ProductsHelper;

/**
 * @var array $arResult
 */

$arResult['MEASURES'] = ProductsHelper::getMeasures($arResult['ID']);

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $arOffer['MEASURES'] = ProductsHelper::getMeasures($arOffer['ID']);

    unset($arOffer);
}