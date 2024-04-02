<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

$arResult['SHARES'] = [
    'SHOW' => false,
    'MODE' => ArrayHelper::fromRange(['default', 'auto'], $arParams['SHARES_MODE']),
    'PROPERTY' => $arParams['SHARES_PROPERTY_PRODUCTS'],
    'PRODUCTS' => $arParams['SHARES_IBLOCK_PROPERTY_PRODUCTS'],
    'FILTER' => [
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => $arParams['SHARES_ACTIVE_DATE'] === 'Y' ? 'Y' : null
    ],
    'HEADER' => $arParams['SHARES_HEADER'],
    'ITEMS' => [],
    'PARAMETERS' => [
        'IBLOCK_TYPE' => null,
        'IBLOCK_ID' => null,
        'ELEMENT_ID_ENTER' => 'N',
        'ELEMENT_ID' => null,
        'DISCOUNT_SHOW' => 'N',
        'PROPERTY_DISCOUNT' => null,
        'DISCOUNT_MINUS_USE' => 'N',
        'DATE_SHOW_FROM' => 'property',
        'PROPERTY_DATE' => null,
        'DATE_FORMAT' => 'd.m.Y',
        'DATE_ONLY_ONE_SHOW' => 'N',
        'TIMER_SHOW' => 'N',
        'TIMER_SECONDS_SHOW' => 'N',
        'TIMER_END_HIDE' => 'N',
        'TEXT_USE' => 'preview',
        'ALL_TEXT_SHOW' => 'N',
        'BUTTON_SHOW' => 'N',
        'BUTTON_TEXT' => null
    ]
];

if ($arParams['SHARES_SHOW'] !== 'Y' || empty($arParams['SHARES_IBLOCK_ID']))
    return;

if ($arResult['SHARES']['MODE'] === 'default') {
    if (empty($arResult['SHARES']['PROPERTY']))
        return;

    $arProperty = ArrayHelper::getValue($arResult['PROPERTIES'], [
        $arResult['SHARES']['PROPERTY'],
        'VALUE'
    ]);

    if (!empty($arProperty)) {
        if (!Type::isArray($arProperty))
            $arProperty = [$arProperty];

        $arResult['SHARES']['FILTER']['ID'] = $arProperty;
    }

    unset($arProperty);

    if (empty($arResult['SHARES']['FILTER']['ID']))
        return;
    else
        $arResult['SHARES']['SHOW'] = true;
} else if ($arResult['SHARES']['MODE'] === 'auto') {
    if (empty($arResult['SHARES']['PRODUCTS']))
        return;

    $arResult['SHARES']['FILTER']['PROPERTY_'.$arResult['SHARES']['PRODUCTS']] = [$arResult['ID']];
    $arResult['SHARES']['SHOW'] = true;
}

$prefix = 'SHARES_';
$prefixLength = StringHelper::length($prefix);
$arExcluded = [
    'ELEMENT_ID_ENTER',
    'ELEMENT_ID',
    'PROPERTY_DISCOUNT',
    'PROPERTY_DATE'
];

foreach ($arParams as $key => $value) {
    if (!StringHelper::startsWith($key, $prefix))
        continue;

    $key = StringHelper::cut($key, $prefixLength);

    if (ArrayHelper::isIn($key, $arExcluded))
        continue;

    if (!ArrayHelper::keyExists($key, $arResult['SHARES']['PARAMETERS']))
        continue;

    $arResult['SHARES']['PARAMETERS'][$key] = $value;
}

$arResult['SHARES']['PARAMETERS']['PROPERTY_DISCOUNT'] = $arParams['SHARES_IBLOCK_PROPERTY_DISCOUNT'];
$arResult['SHARES']['PARAMETERS']['PROPERTY_DATE'] = $arParams['SHARES_IBLOCK_PROPERTY_DATE'];

unset($prefix, $prefixLength, $arExcluded, $key, $value);

$query = new ElementsQuery();

$arResult['SHARES']['ITEMS'] = $query->setUseTilda(false)
    ->setWithProperties(false)
    ->setSort([
        'DATE_ACTIVE_TO' => 'ASC',
        'NAME' => 'ASC'
    ])
    ->setIBlockType($arResult['SHARES']['PARAMETERS']['IBLOCK_TYPE'])
    ->setIBlockId($arResult['SHARES']['PARAMETERS']['IBLOCK_ID'])
    ->setSelect(['ID', 'NAME'])
    ->setFilter($arResult['SHARES']['FILTER'])
    ->execute()
    ->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => [
                'ID' => $value['ID'],
                'NAME' => $value['NAME']
            ]
        ];
    });

if (empty($arResult['SHARES']['ITEMS']))
    $arResult['SHARES']['SHOW'] = false;

unset($query);