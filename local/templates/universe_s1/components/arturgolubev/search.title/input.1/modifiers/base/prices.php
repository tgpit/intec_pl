<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'CURRENCY_CONVERT' => 'N',
    'CURRENCY_ID' => null,
    'PRICE_CODE' => [],
    'PROPERTY_ARTICLE' => null
], $arParams);

$arCurrency = null;
$arPrices = $arParams['PRICE_CODE'];

if (Type::isArrayable($arPrices)) {
    $arPrices = CIBlockPriceTools::GetCatalogPrices(0, $arPrices);
} else {
    $arPrices = [];
}

if (!empty($arParams['CURRENCY_ID']) && Loader::includeModule('currency')) {
    $arCurrency = CCurrency::GetByID($arParams['CURRENCY_ID']);

    if (!empty($arCurrency))
        $arCurrency['CONVERT'] = $arParams['CURRENCY_CONVERT'] === 'Y';
}

$arResult['CURRENCY'] = $arCurrency;
$arResult['PRICES'] = $arPrices;

unset($arCurrency, $arPrices);
