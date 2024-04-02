<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\PropertyTable;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 */

$bBase = false;
$bLite = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
} else if (Loader::includeModule('intec.startshop')) {
    $bLite = true;
}

/**
 * @param $arItem
 * @param bool $bOffer
 * @return array
 */

$hData = function (&$arItem, $bOffer = false) use (&$arResult, &$bBase, &$bLite) {
    $arData = [
        'id' => Type::toInteger($arItem['ID']),
        'article' => !empty($arItem['ARTICLE']) ? $arItem['ARTICLE'] : null,
        'prices' => [],
        'available' => Type::toBoolean($arItem['CAN_BUY']),
        'quantity' => [
            'value' => Type::toFloat($arItem['CATALOG_QUANTITY']),
            'ratio' => Type::toFloat($arItem['CATALOG_MEASURE_RATIO']),
            'measure' => $arItem['CATALOG_MEASURE_NAME'],
            'trace' => $arItem['CATALOG_QUANTITY_TRACE'] === 'Y',
            'zero' => $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y'
        ],
        'sort' => $arItem['SORT'],
        'measures' => [
            'base' => null,
            'selected' => null,
            'items' => $arItem['MEASURES'],
            'use' => !empty($arItem['MEASURES']) && count($arItem['MEASURES']) > 1 ? 'true' : 'false'
        ]
    ];

    if (!empty($arItem['MEASURES']))
        foreach ($arItem['MEASURES'] as $arMeasure) {
            if ($arMeasure['base']) {
                $arData['measures']['base'] = $arMeasure;
                $arData['measures']['selected'] = $arMeasure;
            }
        }

    if (!$bOffer)
        $arData['name'] = $arItem['~NAME'];

    foreach ($arItem['ITEM_PRICES'] as &$arPrice) {
        $arData['prices'][] = [
            'title' => !empty($arPrice['TITLE']) ? $arPrice['TITLE'] : $arPrice['CODE'],
            'id' => $arPrice['PRICE_TYPE_ID'],
            'quantity' => [
                'from' => $arPrice['QUANTITY_FROM'] !== null ? Type::toFloat($arPrice['QUANTITY_FROM']) : null,
                'to' => $arPrice['QUANTITY_TO'] !== null ? Type::toFloat($arPrice['QUANTITY_TO']) : null
            ],
            'base' => [
                'value' => $arPrice['BASE_PRICE'],
                'display' => $arPrice['PRINT_BASE_PRICE']
            ],
            'discount' => [
                'use' => $arPrice['DISCOUNT'] > 0,
                'percent' => $arPrice['PERCENT'],
                'value' => $arPrice['PRICE'],
                'display' => $arPrice['PRINT_PRICE'],
                'difference' => $arPrice['PRINT_DISCOUNT']
            ],
            'currency' => Loader::includeModule('currency') ? CCurrencyLang::GetFormatDescription($arPrice['CURRENCY']) : null
        ];

        unset($arPrice);
    }

    if ($bOffer) {
        $arData['values'] = [];
        $arData['img'] = '';

        if (!empty($arItem['PREVIEW_PICTURE'])) {
            $arData['img'] = $arItem['PREVIEW_PICTURE']['SRC'];
        } else if (!empty($arItem['DETAIL_PICTURE'])) {
            $arData['img'] = $arItem['DETAIL_PICTURE']['SRC'];
        }

        if ($bBase) {
            foreach ($arResult['SKU_PROPS'] as $arSKUProperty) {
                foreach ($arItem['PROPERTIES'] as $arProperty) {
                    $sCode = null;
                    $sValue = null;

                    if (!empty($arProperty['CODE'])) {
                        $sCode = $arProperty['CODE'];
                    } else {
                        $sCode = $arProperty['ID'];
                    }

                    $sCode = 'P_' . $sCode;

                    if ($sCode !== $arSKUProperty['code'])
                        $sCode = null;

                    if (empty($sCode))
                        continue;

                    if ($arProperty['PROPERTY_TYPE'] === PropertyTable::TYPE_LIST) {
                        $sValue = $arProperty['VALUE_ENUM_ID'];
                    } else if (
                        $arProperty['PROPERTY_TYPE'] === PropertyTable::TYPE_STRING &&
                        $arProperty['USER_TYPE'] === 'directory'
                    ) {
                        $sValue = $arProperty['VALUE'];
                    }

                    if (empty($sValue))
                        $sValue = '0';

                    $arData['values'][$sCode] = $sValue;
                }
            }
        } else if ($bLite) {
            if (!empty($arItem['SKU_VALUES']))
                foreach ($arItem['SKU_VALUES'] as $arValue) {
                    $arData['values']['P_'.$arValue['ID']] = $arValue['VALUE']['ID'];
                }
        }
    }

    if ($arItem['MEASURES']) {
        $arData['default']['prices'] = $arData['prices'];
        $arData['default']['quantity'] = $arData['quantity'];
    }

    return $arData;
};

$arData = $hData($arResult);
$arData['offers'] = [];

if (!empty($arResult['OFFERS']))
    foreach ($arResult['OFFERS'] as $arOffer)
        $arData['offers'][$arOffer['ID']] = $hData($arOffer, true);

unset($hData);