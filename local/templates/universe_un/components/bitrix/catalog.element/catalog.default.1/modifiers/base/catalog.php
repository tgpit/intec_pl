<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Type;

/**
 * @var array $arVisual
 * @var CBitrixComponentTemplate $this
 * @var CatalogSectionComponent $component
 */

$component = $this->getComponent();
$component->applyTemplateModifications();

if (!empty($arResult['OFFERS']))
    uasort($arResult['OFFERS'], function ($arOffer1, $arOffer2) {
        return Type::toInteger($arOffer1['SORT']) - Type::toInteger($arOffer2['SORT']);
    });

$arPricesTypes = [];

foreach ($arResult['CAT_PRICES'] as $arPriceType)
    $arPricesTypes[$arPriceType['ID']] = $arPriceType;

unset($arPriceType);

foreach ($arResult['OFFERS'] as &$arOffer) {
    foreach ($arOffer['PRICES'] as &$arPrice) {
        $arPrice['CODE'] = $arPricesTypes[$arPrice['PRICE_ID']]['CODE'];
        $arPrice['TITLE'] = $arPricesTypes[$arPrice['PRICE_ID']]['TITLE'];
    }

    unset($arPrice);

    $arOffer['MIN_PRICE']['CODE'] = $arPricesTypes[$arOffer['MIN_PRICE']['PRICE_ID']]['CODE'];
    $arOffer['MIN_PRICE']['TITLE'] = $arPricesTypes[$arOffer['MIN_PRICE']['PRICE_ID']]['TITLE'];

    foreach ($arOffer['ITEM_PRICES'] as &$arPrice) {
        $arPrice['CODE'] = $arPricesTypes[$arPrice['PRICE_TYPE_ID']]['CODE'];
        $arPrice['TITLE'] = $arPricesTypes[$arPrice['PRICE_TYPE_ID']]['TITLE'];
    }

    unset($arPrice);
}

unset($arOffer);

foreach ($arResult['PRICES'] as &$arPrice) {
    $arPrice['CODE'] = $arPricesTypes[$arPrice['PRICE_ID']]['CODE'];
    $arPrice['TITLE'] = $arPricesTypes[$arPrice['PRICE_ID']]['TITLE'];
}

unset($arPrice);

$arResult['MIN_PRICE']['CODE'] = $arPricesTypes[$arResult['MIN_PRICE']['PRICE_ID']]['CODE'];
$arResult['MIN_PRICE']['TITLE'] = $arPricesTypes[$arResult['MIN_PRICE']['PRICE_ID']]['TITLE'];

foreach ($arResult['ITEM_PRICES'] as &$arPrice) {
    $arPrice['CODE'] = $arPricesTypes[$arPrice['PRICE_TYPE_ID']]['CODE'];
    $arPrice['TITLE'] = $arPricesTypes[$arPrice['PRICE_TYPE_ID']]['TITLE'];
}

unset($arPrice, $arPricesTypes);

$arSKUProps = [];
$bSKUPropsShow = false;

foreach ($arResult['SKU_PROPS'] as $arSKUProperty) {

    if ($arSKUProperty['SHOW_MODE'] !== 'TEXT' ) {
        foreach ($arSKUProperty['VALUES'] as $arValue) {
            foreach ($arResult['OFFERS_PROPERTIES'] as $key => $arOffer) {
                if ($arValue["XML_ID"] == $arResult['OFFERS_PROPERTIES'][$key][$arSKUProperty['CODE']]["VALUE"]) {
                    $arResult['OFFERS_PROPERTIES'][$key][$arSKUProperty['CODE']]["VALUE"] = $arValue["NAME"];
                }
            }
        }
    }

    $arOffersProperty = [
        'id' => $arSKUProperty['ID'],
        'code' => 'P_'.$arSKUProperty['CODE'],
        'name' => $arSKUProperty['NAME'],
        'type' => $arSKUProperty['SHOW_MODE'] === 'TEXT' ? 'text' : 'picture',
        'values' => []
    ];

    foreach ($arSKUProperty['VALUES'] as $arValue) {
        $arOffersProperty['values'][$arValue['ID']] = [
            'id' => !empty($arValue['XML_ID']) ? $arValue['XML_ID'] : $arValue['ID'],
            'name' => $arValue['NAME'],
            'stub' => $arValue['NA'] == 1,
            'picture' => !empty($arValue['PICT']) ? $arValue['PICT']['SRC'] : null
        ];
    }

    $bSKUPropsShow = !empty($arOffersProperty['values']);
    $arSKUProps[] = $arOffersProperty;
}

$arResult['SKU_PROPS_SHOW'] = $bSKUPropsShow;
$arResult['SKU_PROPS'] = $arSKUProps;

unset($arSKUProps);