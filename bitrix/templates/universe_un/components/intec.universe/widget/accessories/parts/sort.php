<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var string $sLevel
 */

Loc::loadMessages(__FILE__);

$arParams = ArrayHelper::merge([
    'LIST_SORT_PRICE' => null
], $arParams);

$arSort = [
    'PROPERTY' => Core::$app->request->get('sort'),
    'ORDER' => Core::$app->request->get('order'),
    'PROPERTIES' => [
        'POPULAR' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_POPULAR'),
            'FIELD' => 'show_counter',
            'VALUE' => 'popular',
            'ORDER' => 'desc',
            'SUBTITLE' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_POPULAR_SUBTITLE')
        ],
        'RATING' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_RATING'),
            'FIELD' => 'rating',
            'VALUE' => 'rating',
            'ORDER' => 'asc',
            'SUBTITLE' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_RATING_SUBTITLE')
        ],
        'NAME_ASC' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_NAME_ASC'),
            'FIELD' => 'name',
            'VALUE' => 'name_asc',
            'ORDER' => 'asc',
            'SUBTITLE' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_NAME_ASC_SUBTITLE')
        ],
        'NAME_DESC' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_NAME_DESC'),
            'FIELD' => 'name',
            'VALUE' => 'name_desc',
            'ORDER' => 'desc',
            'SUBTITLE' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_NAME_DESC_SUBTITLE')
        ],
        'PRICE_ASC' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_PRICE_ASC'),
            'FIELD' => null,
            'VALUE' => 'price_asc',
            'ORDER' => 'asc',
            'SUBTITLE' => null
        ],
        'PRICE_DESC' => [
            'NAME' => Loc::getMessage('C_WIDGET_ACCESSORIES_1_SORTING_PRICE_DESC'),
            'FIELD' => null,
            'VALUE' => 'price_desc',
            'ORDER' => 'desc',
            'SUBTITLE' => null
        ]
    ]
];

if (!empty($arParams['LIST_SORT_PRICE'])) {
    if ($bIsBase) {
        $arSort['PROPERTIES']['PRICE_ASC']['FIELD'] = 'catalog_PRICE_'.$arParams['LIST_SORT_PRICE'];
        $arSort['PROPERTIES']['PRICE_DESC']['FIELD'] = 'catalog_PRICE_'.$arParams['LIST_SORT_PRICE'];
    } else if ($bIsLite) {
        $arSort['PROPERTIES']['PRICE_ASC']['FIELD'] = 'property_STARTSHOP_PRICE_'.$arParams['LIST_SORT_PRICE'];
        $arSort['PROPERTIES']['PRICE_DESC']['FIELD'] = 'property_STARTSHOP_PRICE_'.$arParams['LIST_SORT_PRICE'];
    }
}

if (empty($arSort['PROPERTIES']['PRICE_ASC']['FIELD']))
    unset($arSort['PROPERTIES']['PRICE_ASC']);
if (empty($arSort['PROPERTIES']['PRICE_DESC']['FIELD']))
    unset($arSort['PROPERTIES']['PRICE_DESC']);

$arSort['ORDER'] = ArrayHelper::fromRange([
    'asc',
    'desc'
], $arSort['ORDER']);

foreach ($arSort['PROPERTIES'] as &$arSortProperty) {
    $arSortProperty['ACTIVE'] = $arSortProperty['VALUE'] === $arSort['PROPERTY'];
}

unset($arSort['PROPERTY']);
unset($arSortProperty);