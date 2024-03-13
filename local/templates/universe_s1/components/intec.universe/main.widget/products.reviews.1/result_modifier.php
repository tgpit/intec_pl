<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Data\Cache;
use Bitrix\Main\Loader;
use intec\core\bitrix\iblock\ElementsQuery;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arParams = ArrayHelper::merge([
    'IBLOCK_TYPE' => null,
    'IBLOCK_ID' => null,
    'ELEMENTS_COUNT' => 0,
    'PROPERTY_FILTER' => null,
    'PROPERTY_PRODUCTS' => null,
    'PRODUCTS_IBLOCK_TYPE' => null,
    'PRODUCTS_IBLOCK_ID' => null,
    'PRODUCTS_FILTER' => 'productsReviewsFilter',
    'PRODUCTS_PRICE_CODE' => [],
    'PRODUCTS_CONVERT_CURRENCY' => 'N',
    'PRODUCTS_CURRENCY_ID' => null,
    'PRODUCTS_PRICE_VAT_INCLUDE' => 'N',
    'PRODUCTS_SHOW_PRICE_COUNT' => 1,
    'PRODUCTS_LIST_URL' => null,
    'PRODUCTS_SECTION_URL' => null,
    'PRODUCTS_DETAIL_URL' => null,
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_PREVIEW' => null,
    'HEADER_BLOCK_SHOW' => 'N',
    'HEADER_BLOCK_POSITION' => 'center',
    'HEADER_BLOCK_TEXT' => null,
    'DESCRIPTION_BLOCK_SHOW' => 'N',
    'DESCRIPTION_BLOCK_POSITION' => 'center',
    'DESCRIPTION_BLOCK_TEXT' => null,
    'DATE_SHOW' => 'N',
    'DATE_SOURCE' => 'DATE_ACTIVE_FROM',
    'DATE_FORMAT' => 'd.m.Y',
    'RATING_SHOW' => 'N',
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'PRICE_SHOW' => 'N',
    'PRICE_DISCOUNT_SHOW' => 'N',
    'SORT_BY' => 'SORT',
    'ORDER_BY' => 'ASC'
], $arParams);

if (
    empty($arParams['IBLOCK_ID']) ||
    empty($arParams['PROPERTY_FILTER']) ||
    empty($arParams['PROPERTY_PRODUCTS']) ||
    empty($arParams['PRODUCTS_IBLOCK_ID'])
)
    return;

$arParams['ELEMENTS_COUNT'] = Type::toInteger($arParams['ELEMENTS_COUNT']);

if ($arParams['ELEMENTS_COUNT'] < 0)
    $arParams['ELEMENTS_COUNT'] = 0;

if (empty($arParams['PRODUCTS_FILTER']))
    $arParams['PRODUCTS_FILTER'] = 'productsReviewsFilter';

if (Type::isArray($arParams['PRODUCTS_PRICE_CODE']))
    $arParams['PRODUCTS_PRICE_CODE'] = array_filter($arParams['PRODUCTS_PRICE_CODE']);
else
    $arParams['PRODUCTS_PRICE_CODE'] = [];

if (empty($arParams['SORT_BY']))
    $arParams['SORT_BY'] = 'SORT';

$arParams['ORDER_BY'] = ArrayHelper::fromRange(['ASC', 'DESC'], $arParams['ORDER_BY']);

if (!defined('EDITOR') && $arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$obCache = Cache::createInstance();
$isCaching = false;

if ($arParams['CACHE_TYPE'] === 'A' || $arParams['CACHE_TYPE'] === 'Y') {
    if ($obCache->initCache(
            $arParams['CACHE_TIME'],
            'PRODUCTS_REVIEWS'.serialize($arParams),
            '/'.SITE_ID.'/templates/'.SITE_TEMPLATE_ID.'/main.widget/'
    ))
        $arResult = $obCache->getVars();
    else if ($obCache->startDataCache())
        $isCaching = true;
}

if ($isCaching || $arParams['CACHE_TYPE'] === 'N') {
    $arResult = [
        'ITEMS' => [],
        'PRODUCTS' => [],
        'PARAMETERS' => []
    ];

    $reviewsQuery = new ElementsQuery();

    $reviewsQuery->setIBlockType($arParams['IBLOCK_TYPE'])
        ->setIBlockId($arParams['IBLOCK_ID'])
        ->setSort([$arParams['SORT_BY'] => $arParams['ORDER_BY']])
        ->setLimit($arParams['ELEMENTS_COUNT'])
        ->setFilter([
            'ACTIVE' => 'Y',
            'ACTIVE_DATE' => 'Y',
            'CHECK_PERMISSIONS' => 'Y',
            'MIN_PERMISSION' => 'R',
            '!PROPERTY_'.$arParams['PROPERTY_FILTER'] => false
        ]);

    $reviews = $reviewsQuery->execute();

    if (!$reviews->isEmpty()) {
        $reviews->each(function ($key, $item) use (&$arResult, &$arParams) {
            if (!empty($arParams['PROPERTY_PRODUCTS'])) {
                $arProperty = ArrayHelper::getValue($item['PROPERTIES'], [
                    $arParams['PROPERTY_PRODUCTS'],
                    'VALUE'
                ]);

                if (!empty($arProperty)) {
                    if (Type::isArray($arProperty))
                        $arProperty = ArrayHelper::getFirstValue($arProperty);

                    $arResult['PRODUCTS'][] = $arProperty;
                }
            }
        })->handleFiles();

        $arResult['ITEMS'] = $reviews->asArray();

        include(__DIR__.'/modifiers/products.php');
    }

    unset($reviewsQuery, $reviews);
}

if ($isCaching)
    $obCache->endDataCache($arResult);

unset($obCache, $isCaching);