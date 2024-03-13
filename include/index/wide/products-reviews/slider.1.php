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
<?php $APPLICATION->IncludeComponent('intec.universe:main.widget', 'products.reviews.1', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '60',
  'ELEMENTS_COUNT' => '',
  'PROPERTY_FILTER' => 'SHOW',
  'PROPERTY_PRODUCTS' => 'ELEMENT_ID',
  'PRODUCTS_IBLOCK_TYPE' => 'catalogs',
  'PRODUCTS_IBLOCK_ID' => '58',
  'PRODUCTS_FILTER' => 'productsReviewsFilter',
  'PRODUCTS_PRICE_CODE' => 
  array (
    0 => 'BASE',
  ),
  'PRODUCTS_CONVERT_CURRENCY' => 'Y',
  'PRODUCTS_CURRENCY_ID' => 'RUB',
  'PRODUCTS_PRICE_VAT_INCLUDE' => 'N',
  'PRODUCTS_SHOW_PRICE_COUNT' => '1',
  'PRODUCTS_LIST_URL' => '',
  'PRODUCTS_SECTION_URL' => '',
  'PRODUCTS_DETAIL_URL' => '',
  'SETTINGS_USE' => 'Y',
  'PROPERTY_PREVIEW' => '',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Отзывы к товарам',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'DATE_SHOW' => 'Y',
  'DATE_SOURCE' => 'DATE_ACTIVE_FROM',
  'DATE_FORMAT' => 'd.m.Y',
  'RATING_SHOW' => 'Y',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'PRICE_SHOW' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'LAZYLOAD_USE' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
