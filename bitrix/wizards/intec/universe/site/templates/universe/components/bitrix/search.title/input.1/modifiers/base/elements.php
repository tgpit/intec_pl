<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Catalog\ProductTable;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\collections\Scalars;

if (empty($arResult['CATEGORIES']))
    return;

$arElements = new Scalars();

foreach ($arResult['CATEGORIES'] as &$arCategory)
    foreach ($arCategory['ITEMS'] as &$arItem)
        if (!empty($arItem['ELEMENT']))
            $arElements->addUnique($arItem['ELEMENT']['ID']);

unset($arCategory, $arItem);

if (!$arElements->isEmpty()) {
    $arSelect = [
        'ID',
        'IBLOCK_ID',
        'IBLOCK_SECTION_ID'
    ];

    $arFilter = [
        'ID' => $arElements->asArray()
    ];

    foreach ($arResult['PRICES'] as $arPrice) {
        if (!$arPrice['CAN_VIEW'] && !$arPrice['CAN_BUY'])
            continue;

        $arSelect[] = $arPrice['SELECT'];
        $arFilter['CATALOG_SHOP_QUANTITY_' . $arPrice['ID']] = 1;
    }

    $arElements = new ElementsQuery([
        'filter' => $arFilter,
        'select' => $arSelect
    ]);

    $arElements = $arElements
        ->setWithProperties(false)
        ->execute()
        ->indexBy('ID');

    if (!$arElements->isEmpty()) {
        $arElements->each(function ($iIndex, &$arElement) use (&$arResult, &$arParams) {
            $arElement['PRICES'] = [];

            if ($arElement['CATALOG_TYPE'] != ProductTable::TYPE_SKU)
                $arElement['PRICES'] = CIBlockPriceTools::GetItemPrices(
                    $arElement['IBLOCK_ID'],
                    $arResult['PRICES'],
                    $arElement,
                    $arParams['PRICE_VAT_INCLUDE'] === 'Y',
                    !empty($arResult['CURRENCY']) && $arResult['CURRENCY']['CONVERT'] ? [
                        'CURRENCY_ID' => $arResult['CURRENCY']['CURRENCY']
                    ] : []
                );
        });

        foreach ($arResult['CATEGORIES'] as &$arCategory)
            foreach ($arCategory['ITEMS'] as &$arItem) {
                if (empty($arItem['ELEMENT']))
                    continue;

                $arElement = $arElements->get($arItem['ELEMENT']['ID']);

                if (empty($arElement))
                    continue;

                $arItem['ELEMENT']['PRICES'] = $arElement['PRICES'];
                $arItem['ELEMENT']['IS_PRODUCT'] = 'Y';
            }

        unset($arCategory, $arItem);
    }

    unset($arSelect, $arFilter);
}

unset($arElements);
