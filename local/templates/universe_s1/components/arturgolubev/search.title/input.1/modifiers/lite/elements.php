<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\iblock\Elements;
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

    $arPrices = [];

    foreach ($arResult['PRICES'] as $arPrice)
        $arPrices[] = $arPrice['CODE'];

    $arElements = new Elements();
    $rsElements = CStartShopCatalogProduct::GetList(
        [],
        $arFilter,
        [],
        [],
        !empty($arResult['CURRENCY']) && $arResult['CURRENCY']['CONVERT'] ? $arResult['CURRENCY']['CODE'] : null,
        $arPrices
    );

    while ($arElement = $rsElements->Fetch())
        $arElements->set($arElement['ID'], $arElement);

    unset($arElement);

    foreach ($arResult['CATEGORIES'] as &$arCategory)
        foreach ($arCategory['ITEMS'] as &$arItem) {
            if (empty($arItem['ELEMENT']))
                continue;

            $arElement = $arElements->get($arItem['ELEMENT']['ID']);

            if (empty($arElement))
                continue;

            $arItem['ELEMENT']['PRICES'] = $arElement['STARTSHOP']['PRICES'];
            $arItem['ELEMENT']['IS_PRODUCT'] = 'Y';
        }

    unset($arSelect, $arFilter, $arPrices);
}

unset($arElements);
