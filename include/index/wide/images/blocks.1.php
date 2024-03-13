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
  'margin-top' => '50px',
  'margin-bottom' => '50px',
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.images', 'template.1', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '78',
  'SECTIONS_MODE' => 'id',
  'SECTIONS' => 
  array (
  ),
  'SECTIONS_COUNT' => '',
  'SECTION_ELEMENTS_COUNT' => '',
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'PROPERTY_DISPLAY' => 
  array (
    0 => 'PROPERTY_SEASON',
    1 => 'PROPERTY_STYLE',
    2 => 'PROPERTY_COLOR',
    3 => 'PROPERTY_PRICE_CATEGORY',
  ),
  'PROPERTY_PREVIEW' => '',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER_TEXT' => 'Готовые образы',
  'DESCRIPTION_SHOW' => 'N',
  'TABS_USE' => 'Y',
  'TABS_POSITION' => 'left',
  'PICTURE_SHOW' => 'Y',
  'DISPLAY_SHOW' => 'Y',
  'PREVIEW_SHOW' => 'Y',
  'DETAIL_SHOW' => 'Y',
  'DETAIL_TEXT' => 'Заглянуть в образ',
  'DETAIL_BLANK' => 'Y',
  'MORE_SHOW' => 'Y',
  'MORE_TEXT' => 'Все образы',
  'MORE_BLANK' => 'Y',
  'PRODUCTS_SHOW' => 'Y',
  'PROPERTY_PRODUCTS' => 'ELEMENTS',
  'PRODUCTS_IBLOCK_TYPE' => 'catalogs',
  'PRODUCTS_IBLOCK_ID' => '58',
  'PRODUCTS_ELEMENTS_COUNT' => '',
  'PRODUCTS_FILTER' => 'collectionsFilter',
  'PRODUCTS_PRICE_CODE' => 
  array (
    0 => 'BASE',
  ),
  'PRODUCTS_CONVERT_CURRENCY' => 'N',
  'PRODUCTS_PRICE_VAT_INCLUDE' => 'N',
  'PRODUCTS_SHOW_PRICE_COUNT' => '1',
  'PRODUCTS_SORT_BY' => 'SORT',
  'PRODUCTS_ORDER_BY' => 'ASC',
  'PRODUCTS_LIST_URL' => '',
  'PRODUCTS_SECTION_URL' => '',
  'PRODUCTS_DETAIL_URL' => '',
  'PRODUCTS_PICTURE_SHOW' => 'Y',
  'PRODUCTS_PRICE_SHOW' => 'Y',
  'PRODUCTS_DISCOUNT_SHOW' => 'Y',
  'PRODUCTS_MEASURE_SHOW' => 'N',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'LAZYLOAD_USE' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
