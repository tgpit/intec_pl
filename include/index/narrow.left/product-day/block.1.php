<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\collections\Arrays;
use intec\core\helpers\Html;
use intec\core\io\Path;

/**
 * @var Arrays $blocks
 * @var array $block
 * @var array $data
 * @var string $page
 * @var Path $path
 * @global CMain $APPLICATION
 */

?>
<?= Html::beginTag('div', ['style' => array (
  'padding-top' => '50px',
  'padding-bottom' => '50px',
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.widget', 'product.1', array (
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '58',
  'MODE' => 'period',
  'PROPERTY_PERIOD_START' => 'PRODUCT_DAY_DATE_START',
  'PROPERTY_PERIOD_END' => 'PRODUCT_DAY_DATE_END',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'PRICE_CODE' => 
  array (
    0 => 'BASE',
  ),
  'CONVERT_CURRENCY' => 'N',
  'PRICE_VAT_INCLUDE' => 'N',
  'FORM_ID' => '17',
  'FORM_TEMPLATE' => '.default',
  'FORM_PROPERTY_PRODUCT' => 'form_text_67',
  'FORM_TITLE' => 'Оформить заказ',
  'ORDER_FAST_USE' => '',
  'CONSENT_URL' => '/company/consent/',
  'PROPERTY_MARKS_HIT' => 'HIT',
  'PROPERTY_MARKS_NEW' => 'NEW',
  'PROPERTY_MARKS_RECOMMEND' => 'RECOMMEND',
  'PROPERTY_PICTURES' => 'PICTURES',
  'PROPERTY_ARTICLE' => 'ARTICLE',
  'PROPERTY_ORDER_USE' => 'ORDER_USE',
  'PRICE_RANGE_SHOW' => 'N',
  'PRICE_DISCOUNT_SHOW' => 'Y',
  'PRICE_DISCOUNT_PERCENT' => 'N',
  'PRICE_DISCOUNT_ECONOMY' => 'N',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER_TEXT' => 'Товар дня',
  'DESCRIPTION_SHOW' => 'N',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'N',
  'GALLERY_USE' => 'Y',
  'QUANTITY_SHOW' => 'Y',
  'QUANTITY_MODE' => 'number',
  'MARKS_SHOW' => 'Y',
  'ARTICLE_SHOW' => 'Y',
  'VOTE_USE' => 'Y',
  'VOTE_MODE' => 'rating',
  'COMPARE_USE' => 'Y',
  'COMPARE_CODE' => 'compare',
  'ACTION' => 'buy',
  'BASKET_URL' => '/personal/basket/',
  'DELAY_USE' => 'Y',
  'SUBSCRIBE_USE' => 'Y',
  'COUNTER_SHOW' => 'Y',
  'TIMER_SHOW' => 'Y',
  'TIMER_TEMPLATE' => '.default',
  'SECTION_TIMER_TIME_ZERO_HIDE' => 'N',
  'SECTION_TIMER_MODE' => 'discount',
  'SECTION_TIMER_TIMER_SECONDS_SHOW' => 'N',
  'SECTION_TIMER_TIMER_QUANTITY_SHOW' => 'Y',
  'SECTION_TIMER_TIMER_QUANTITY_ENTER_VALUE' => 'N',
  'SECTION_TIMER_TIMER_PRODUCT_UNITS_USE' => 'Y',
  'SECTION_TIMER_TIMER_QUANTITY_HEADER_SHOW' => 'Y',
  'SECTION_TIMER_TIMER_QUANTITY_HEADER' => 'Остаток',
  'SECTION_TIMER_TIMER_HEADER_SHOW' => 'Y',
  'SECTION_TIMER_TIMER_HEADER' => 'До конца акции',
  'SECTION_TIMER_COMPOSITE_FRAME_MODE' => 'A',
  'SECTION_TIMER_COMPOSITE_FRAME_TYPE' => 'AUTO',
  'QUICK_VIEW_USE' => 'N',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '0',
  'CACHE_NOTES' => '',
  'COMPOSITE_FRAME_MODE' => 'A',
  'COMPOSITE_FRAME_TYPE' => 'AUTO',
), false) ?>
<?= Html::endTag('div') ?>
