<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_PRICE' => null,
    'PROPERTY_CURRENCY' => null,
    'PROPERTY_DISCOUNT' => null,
    'PROPERTY_DISCOUNT_TYPE' => null,
    'PRICE_SHOW' => 'N',
    'DISCOUNT_SHOW' => 'N',
    'PROPERTY_LIST' => null,
    'ORDER_USE' => 'N',
    'ORDER_FORM_ID' => null,
    'ORDER_FORM_TEMPLATE' => null,
    'ORDER_FORM_FIELD' => null,
    'ORDER_FORM_TITLE' => null,
    'ORDER_FORM_CONSENT' => null,
    'ORDER_BUTTON' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arMacros = [
    'SITE_DIR' => SITE_DIR
];

$arVisual = [
    'LAZYLOAD' => [
        'USE' => $arParams['LAZYLOAD_USE'] === 'Y',
        'STUB' => null
    ],
    'PRICE' => [
        'SHOW' => !empty($arParams['PROPERTY_PRICE']) && $arParams['PRICE_SHOW'] === 'Y'
    ],
    'DISCOUNT' => [
        'SHOW' => !empty($arParams['PROPERTY_DISCOUNT']) && $arParams['DISCOUNT_SHOW'] === 'Y'
    ],
];

if (defined('EDITOR'))
    $arVisual['LAZYLOAD']['USE'] = false;

if ($arVisual['LAZYLOAD']['USE'])
    $arVisual['LAZYLOAD']['STUB'] = Properties::get('template-images-lazyload-stub');

$arResult['VISUAL'] = ArrayHelper::merge($arVisual, $arResult['VISUAL']);

unset($arVisual);

$arResult['PROPERTIES'] = [];

if (!empty($arParams['PROPERTY_LIST'])) {
    $arProperties = ArrayHelper::getFirstValue($arResult['ITEMS']);

    foreach ($arProperties['DISPLAY_PROPERTIES'] as $arProp)
        if (!empty($arProp['CODE']))
            if (ArrayHelper::isIn($arProp['CODE'], $arParams['PROPERTY_LIST'])) {
                $bPropEmpty = true;
                foreach ($arResult['ITEMS'] as $arItem) {
                    $arPropertyValue = ArrayHelper::getValue($arItem, ['DISPLAY_PROPERTIES', $arProp['CODE'], 'DISPLAY_VALUE']);
                    if (!empty($arPropertyValue)) {
                        $bPropEmpty = false;
                        continue;
                    }
                }

                if (!$bPropEmpty)
                    $arResult['PROPERTIES'][] = $arProp;
            }
}

unset($arProperties);

$arForm = [
    'USE' => $arParams['ORDER_USE'] === 'Y',
    'ID' => $arParams['ORDER_FORM_ID'],
    'TEMPLATE' => $arParams['ORDER_FORM_TEMPLATE'],
    'FIELD' => $arParams['ORDER_FORM_FIELD'],
    'TITLE' => $arParams['ORDER_FORM_TITLE'],
    'CONSENT' => $arParams['ORDER_FORM_CONSENT'],
    'BUTTON' => $arParams['ORDER_BUTTON']
];

if ($arForm['USE'])
    if (empty($arForm['ID']) || empty($arForm['TEMPLATE']))
        $arForm['USE'] = false;

$arResult['FORM'] = $arForm;

unset($arForm);

$hSetPropertyText = function (&$item, $property = '') {
    $result = null;

    if (!empty($item) && !empty($property)) {
        $arProperty = ArrayHelper::getValue($item, [
            'PROPERTIES',
            $property,
            'VALUE'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty)) {
                if (ArrayHelper::keyExists('TEXT', $arProperty))
                    $arProperty = Html::stripTags($arProperty['TEXT']);
                else
                    $arProperty = implode(', ', $arProperty);

                if (empty($arProperty))
                    $arProperty = null;
            }

            $result = $arProperty;
        }
    }

    return $result;
};

$hGetDiscountPrice = function ($price = '', $discount = []) {
    $result = null;

    if (!empty($price)) {
        $value = 0;

        if (!empty($discount['VALUE'])) {
            if ($discount['TYPE'] !== 'value')
                if ($discount['VALUE'] > 0 && $discount['VALUE'] <= 100)
                    $value = $price / 100 * $discount['VALUE'];
                else
                    $value = 0;
            else {
                $value = $discount['VALUE'];

                if ($value < 0)
                    $value = 0;
            }
        }

        $result = round($price - $value, 2);
    }

    return $result;
};

$bPriceEmpty = true;
$bOldPriceEmpty = true;

foreach ($arResult['ITEMS'] as &$arItem) {
    $arItem['DATA'] = [
        'PRICE' => [
            'NEW' => null,
            'OLD' => $hSetPropertyText($arItem, $arParams['PROPERTY_PRICE']),
            'CURRENCY' => $hSetPropertyText($arItem, $arParams['PROPERTY_CURRENCY']),
        ],
        'DISCOUNT' => [
            'VALUE' => $hSetPropertyText($arItem, $arParams['PROPERTY_DISCOUNT']),
            'TYPE' => 'percent'
        ],
    ];

    if (!empty($arParams['PROPERTY_DISCOUNT_TYPE'])) {
        $arProperty = ArrayHelper::getValue($arItem, [
            'PROPERTIES',
            $arParams['PROPERTY_DISCOUNT_TYPE'],
            'VALUE_XML_ID'
        ]);

        if (!empty($arProperty)) {
            if (Type::isArray($arProperty))
                $arProperty = ArrayHelper::getFirstValue($arProperty);

            $arItem['DATA']['DISCOUNT']['TYPE'] = $arProperty;
        }

        unset($arProperty);
    }

    if (!empty($arItem['DATA']['PRICE']['NEW']))
        $arItem['DATA']['PRICE']['VALUE'] = Type::toFloat($arItem['DATA']['PRICE']['VALUE']);

    if (!empty($arItem['DATA']['DISCOUNT']['VALUE']))
        $arItem['DATA']['DISCOUNT']['VALUE'] = Type::toFloat($arItem['DATA']['DISCOUNT']['VALUE']);

    if (!empty($arItem['DATA']['DETAIL']))
        $arItem['DATA']['DETAIL'] = StringHelper::replaceMacros($arItem['DATA']['DETAIL'], $arMacros);

    $arItem['DATA']['PRICE']['NEW'] = $hGetDiscountPrice(
        $arItem['DATA']['PRICE']['OLD'],
        $arItem['DATA']['DISCOUNT']
    );

    if ($arItem['DATA']['PRICE']['NEW'] === $arItem['DATA']['PRICE']['OLD'])
        $arItem['DATA']['PRICE']['OLD'] = null;

    if (!empty($arItem['DATA']['PRICE']['NEW']))
        $bPriceEmpty = false;

    if (!empty($arItem['DATA']['PRICE']['OLD']))
        $bOldPriceEmpty = false;
}

unset($arItem);

if ($arResult['VISUAL']['PRICE']['SHOW'] && !$bPriceEmpty) {
    $arResult['VISUAL']['PRICE']['SHOW'] = true;
} else {
    $arResult['VISUAL']['PRICE']['SHOW'] = false;
}

if ($arResult['VISUAL']['DISCOUNT']['SHOW'] && !$bOldPriceEmpty) {
    $arResult['VISUAL']['DISCOUNT']['SHOW'] = true;
} else {
    $arResult['VISUAL']['DISCOUNT']['SHOW'] = false;
}