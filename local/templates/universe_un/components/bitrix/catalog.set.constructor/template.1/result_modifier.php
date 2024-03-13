<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;
use intec\template\Properties;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'LAZYLOAD_USE' => 'N'
], $arParams);

$arVisual = [
    'LAZYLOAD' => [
        'USE' => !defined('EDITOR') ? $arParams['LAZYLOAD_USE'] : false,
        'STUB' => !defined('EDITOR') ? Properties::get('template-images-lazyload-stub') : null
    ]
];

$arDefaultSetIDs = [$arResult['ELEMENT']['ID']];

foreach (['DEFAULT', 'OTHER'] as $type) {
    foreach ($arResult['SET_ITEMS'][$type] as $key => $arItem) {
        $arElement = [
            'ID'=>$arItem['ID'],
            'NAME' =>$arItem['NAME'],
            'DETAIL_PAGE_URL'=>$arItem['DETAIL_PAGE_URL'],
            'DETAIL_PICTURE'=>$arItem['DETAIL_PICTURE'],
            'PREVIEW_PICTURE'=> $arItem['PREVIEW_PICTURE'],
            'PRICE_CURRENCY' => $arItem['PRICE_CURRENCY'],
            'PRICE_DISCOUNT_VALUE' => $arItem['PRICE_DISCOUNT_VALUE'],
            'PRICE_PRINT_DISCOUNT_VALUE' => $arItem['PRICE_PRINT_DISCOUNT_VALUE'],
            'PRICE_VALUE' => $arItem['PRICE_VALUE'],
            'PRICE_PRINT_VALUE' => $arItem['PRICE_PRINT_VALUE'],
            'PRICE_DISCOUNT_DIFFERENCE_VALUE' => $arItem['PRICE_DISCOUNT_DIFFERENCE_VALUE'],
            'PRICE_DISCOUNT_DIFFERENCE' => $arItem['PRICE_DISCOUNT_DIFFERENCE'],
            'CAN_BUY' => $arItem['CAN_BUY'],
            'CATALOG_QUANTITY' => $arItem['CATALOG_QUANTITY'],
            'SET_QUANTITY' => $arItem['SET_QUANTITY'],
            'MEASURE_RATIO' => $arItem['MEASURE_RATIO'],
            'BASKET_QUANTITY' => $arItem['BASKET_QUANTITY'],
            'MEASURE' => $arItem['MEASURE']
        ];

        if ($arItem['PRICE_CONVERT_DISCOUNT_VALUE'])
            $arElement['PRICE_CONVERT_DISCOUNT_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_VALUE'];

        if ($arItem['PRICE_CONVERT_VALUE'])
            $arElement['PRICE_CONVERT_VALUE'] = $arItem['PRICE_CONVERT_VALUE'];

        if ($arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'])
            $arElement['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'] = $arItem['PRICE_CONVERT_DISCOUNT_DIFFERENCE_VALUE'];

        if ($type == 'DEFAULT')
            $arDefaultSetIDs[] = $arItem['ID'];

        $arResult['SET_ITEMS'][$type][$key] = $arElement;
    }

    unset($key, $arItem);
}

unset($type);

$arResult['DEFAULT_SET_IDS'] = $arDefaultSetIDs;

$arResult['VISUAL'] = $arVisual;

unset($arVisual);