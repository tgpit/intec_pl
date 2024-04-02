<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale')) {
    $bBase = true;
}

$bMeasures = false;

if ($bBase && Loader::includeModule('intec.measures'))
    $bMeasures = true;

$arParams = ArrayHelper::merge([
    'SETTINGS_USE' => 'N',
    'MARKS_ORIENTATION' => '',
    'PRICE_CODE' => [],
    'COUNTER_SHOW' => 'N',
    'ORDER_FAST_USE' => 'N',
    'ORDER_FAST_TEMPLATE' => null,
    'DEFERRED_REFRESH' => 'N',
    'SHOW_RESTORE' => 'N',
    'COLUMNS_LIST' => [],
    'COLUMNS_LIST_MOBILE' => [],
    'TOTAL_BLOCK_FIXED_MODE' => 'N',
    'TOTAL_BLOCK_DISPLAY' => [],
    'HIDE_COUPON' => 'N',
    'PRICE_VAT_SHOW_VALUE' => 'N',
    'SHOW_FILTER' => 'N',
    'USE_DYNAMIC_SCROLL' => 'N',
    'SHOW_DISCOUNT_PERCENT' => 'N',
    'DISCOUNT_PERCENT_POSITION' => 'bottom-right',
    'LABEL_PROP' => [],
    'LABEL_PROP_MOBILE' => [],
    'LABEL_PROP_POSITION' => [],
    'PRODUCT_BLOCKS_ORDER' => 'props,sku,columns',
    'PRICE_DISPLAY_MODE' => 'N',
    'USE_PRICE_ANIMATION' => 'N',
    'OFFERS_PROPS' => [],
    'EMPTY_BASKET_HINT_PATH' => '#SITE_DIR#',
    'USE_ENHANCED_ECOMMERCE' => 'N',
    'DATA_LAYER_NAME' => 'dataLayer',
    'BRAND_PROPERTY' => null,
    'OFFERS_VARIABLE_SELECT' => null,
    'SHOW_ALERT_FORM' => 'N'
], $arParams);

if ($arParams['SETTINGS_USE'] === 'Y')
    include(__DIR__.'/modifiers/settings.php');

$arResult['QUICK_VIEW'] = [
    'USE' => $arParams['QUICK_VIEW_USE'] === 'Y',
    'TEMPLATE' => $arParams['QUICK_VIEW_TEMPLATE'],
    'SLIDE' => $arParams['QUICK_VIEW_SLIDE_USE'] === 'Y'
];

if (!empty($arResult['QUICK_VIEW']['TEMPLATE'])) {
    $arResult['QUICK_VIEW']['TEMPLATE'] = 'quick.view.'. $arResult['QUICK_VIEW']['TEMPLATE'];
} else {
    $arResult['QUICK_VIEW']['USE'] = false;
}

if (empty($arParams['TOTAL_BLOCK_DISPLAY']))
    $arParams['TOTAL_BLOCK_DISPLAY'] = ['top'];

if (!empty($arParams['OFFERS_VARIABLE_SELECT']))
    $arResult['OFFERS_VARIABLE_SELECT'] = $arParams['OFFERS_VARIABLE_SELECT'];
else
    $arResult['OFFERS_VARIABLE_SELECT'] = 'offer';