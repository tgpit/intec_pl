<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

$arParams = ArrayHelper::merge([
    'IBLOCK_TYPE' => null,
    'IBLOCK_ID' => null,
    'ELEMENT_ID' => null,
    'SECTION_ID' => null,
    'MODE' => 'period',
    'PROPERTY_DAY' => null,
    'PROPERTY_PERIOD_START' => null,
    'PROPERTY_PERIOD_END' => null,
    'SORT_BY' => 'SORT',
    'ORDER_BY' => 'ASC',
    'SETTINGS_USE' => 'N',
    'LAZYLOAD_USE' => 'N',
    'PROPERTY_MARKS_HIT' => null,
    'PROPERTY_MARKS_NEW' => null,
    'PROPERTY_MARKS_RECOMMEND' => null,
    'PROPERTY_MARKS_SHARE' => null,
    'PROPERTY_PICTURES' => null,
    'PROPERTY_ARTICLE' => null,
    'PROPERTY_ORDER_USE' => null,
    'PRICE_CODE' => [],
    'CONVERT_CURRENCY' => 'N',
    'CURRENCY_ID' => null,
    'PRICE_VAT_INCLUDE' => 'N',
    'PRICE_RANGE_SHOW' => 'N',
    'PRICE_DISCOUNT_SHOW' => 'N',
    'PRICE_DISCOUNT_PERCENT' => 'N',
    'PRICE_DISCOUNT_ECONOMY' => 'N',
    'FORM_ID' => null,
    'FORM_TEMPLATE' => null,
    'FORM_PROPERTY_PRODUCT' => null,
    'FORM_TITLE' => null,
    'ORDER_FAST_USE' => 'N',
    'ORDER_FAST_TEMPLATE' => null,
    'CONSENT_URL' => '#SITE_DIR#company/consent/',
    'LINK_USE' => 'N',
    'LINK_BLANK' => 'N',
    'GALLERY_USE' => 'N',
    'QUANTITY_SHOW' => 'N',
    'QUANTITY_MODE' => 'number',
    'QUANTITY_BOUNDS_MANY' => 50,
    'QUANTITY_BOUNDS_FEW' => 10,
    'MARKS_SHOW' => 'N',
    'ARTICLE_SHOW' => 'N',
    'VOTE_USE' => 'N',
    'VOTE_MODE' => 'rating',
    'COMPARE_USE' => 'N',
    'COMPARE_CODE' => 'compare',
    'ACTION' => 'none',
    'BASKET_URL' => '#SITE_DIR#personal/basket/',
    'DELAY_USE' => 'N',
    'SUBSCRIBE_USE' => 'N',
    'COUNTER_SHOW' => 'N',
    'TIMER_SHOW' => 'N',
    'TIMER_TEMPLATE' => null,
    'QUICK_VIEW_USE' => 'N',
    'QUICK_VIEW_TEMPLATE' => null,
    'QUICK_VIEW_PROPERTY_CODE' => [],
    'LIST_PAGE_URL' => null,
    'SECTION_URL' => null,
    'DETAIL_URL' => null
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult = [
    'URL' => [
        'LIST' => null,
        'SECTION' => null,
        'DETAIL' => null
    ]
];

if (!empty($arParams['LIST_PAGE_URL']) && !empty($arParams['SECTION_URL']) && !empty($arParams['DETAIL_URL'])) {
    $arResult['URL']['LIST'] = StringHelper::replaceMacros($arParams['LIST_PAGE_URL'], [
        'SITE_DIR' => SITE_DIR
    ]);

    if (!StringHelper::endsWith($arResult['URL']['LIST'], '/'))
        $arResult['URL']['LIST'] = $arResult['URL']['LIST'] . '/';

    $arResult['URL']['SECTION'] = $arParams['SECTION_URL'];
    $arResult['URL']['DETAIL'] = $arParams['DETAIL_URL'];
}