<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 */

$arParams = ArrayHelper::merge([
    'CURRENCY_CONVERT' => 'N',
    'CURRENCY_ID' => null,
    'PRICE_CODE' => []
], $arParams);

$arCurrency = null;
$arPrices = $arParams['PRICE_CODE'];

if (Type::isArrayable($arPrices)) {
    $rsPrices = CStartShopPrice::GetList([], [
        'CODE' => $arPrices
    ]);

    $arPrices = [];

    while ($arPrice = $rsPrices->Fetch())
        $arPrices[] = $arPrice;

    unset($rsPrices);
} else {
    $arPrices = [];
}

$arCurrency = CStartShopCurrency::GetByCode($arParams['CURRENCY_ID'])->Fetch();

if (!empty($arCurrency))
    $arCurrency['CONVERT'] = $arParams['CURRENCY_CONVERT'] === 'Y';

$arResult['CURRENCY'] = $arCurrency;
$arResult['PRICES'] = $arPrices;

unset($arCurrency, $arPrices);
