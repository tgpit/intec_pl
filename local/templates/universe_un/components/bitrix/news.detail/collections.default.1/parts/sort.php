<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use Bitrix\Main\Loader;
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

$bIsBase = Loader::includeModule('catalog') && Loader::includeModule('sale');
$bIsLite = !$bIsBase && Loader::includeModule('intec.startshop');

$arSort = [
    'PROPERTY' => Core::$app->session->get('BITRIX_COLLECTIONS_SORT_PROPERTY'),
    'FIELD' => null,
    'ORDER' => Core::$app->session->get('BITRIX_COLLECTIONS_SORT_ORDER'),
    'PROPERTIES' => [
        'POPULAR' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_POPULAR'),
            'DESCRIPTION' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_POPULAR_DESCRIPTION'),
            'FIELD' => 'show_counter',
            'VALUE' => 'popular',
            'ORDER' => 'desc'
        ],
        'RATING' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_RATING'),
            'DESCRIPTION' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_RATING_DESCRIPTION'),
            'FIELD' => 'rating',
            'VALUE' => 'rating',
            'ORDER' => 'asc'
        ],
        'NAME_ASC' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_NAME_ASC'),
            'DESCRIPTION' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_NAME_ASC_DESCRIPTION'),
            'FIELD' => 'name',
            'VALUE' => 'name_asc',
            'ORDER' => 'asc'
        ],
        'NAME_DESC' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_NAME_DESC'),
            'DESCRIPTION' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_NAME_DESC_DESCRIPTION'),
            'FIELD' => 'name',
            'VALUE' => 'name_desc',
            'ORDER' => 'desc'
        ],
        'PRICE_ASC' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_PRICE_ASC'),
            'DESCRIPTION' => null,
            'FIELD' => null,
            'VALUE' => 'price_asc',
            'ORDER' => 'asc'
        ],
        'PRICE_DESC' => [
            'NAME' => Loc::getMessage('C_NEWS_DETAIL_COLLECTIONS_DEFAULT_1_SORT_PRICE_DESC'),
            'DESCRIPTION' => null,
            'FIELD' => null,
            'VALUE' => 'price_desc',
            'ORDER' => 'desc'
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

$sSortProperty = Core::$app->request->get('sort');
$sSortOrder = Core::$app->request->get('order');

if (!empty($sSortProperty)) {
    if (!empty($sSortOrder)) {
        $arSort['PROPERTY'] = $sSortProperty;
        $arSort['ORDER'] = $sSortOrder;
    } else {
        $arSort['PROPERTY'] = null;
        $arSort['ORDER'] = null;
    }
}

unset($sSortOrder, $sSortProperty);

$arSort['ORDER'] = ArrayHelper::fromRange([
    'asc',
    'desc'
], $arSort['ORDER']);

foreach ($arSort['PROPERTIES'] as &$arSortProperty) {
    $arSortProperty['ACTIVE'] = $arSortProperty['VALUE'] === $arSort['PROPERTY'];

    if ($arSortProperty['ACTIVE']) {
        $arSort['ORDER'] = $arSortProperty['ORDER'];
        $arSort['FIELD'] = $arSortProperty['FIELD'];
    }
}

Core::$app->session->set('BITRIX_COLLECTIONS_SORT_PROPERTY', $arSort['PROPERTY']);
Core::$app->session->set('BITRIX_COLLECTIONS_SORT_ORDER', $arSort['ORDER']);

unset($arSortProperty);