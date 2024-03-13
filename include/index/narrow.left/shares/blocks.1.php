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
  'margin-bottom' => '50px',
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.shares', 'template.2', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '67',
  'ELEMENTS_COUNT' => '2',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'TIMER_USE' => 'Y',
  'ELEMENT_HEADER_PROPERTY_TEXT' => 'DURATION',
  'TIMER_PROPERTY_UNTIL_DATE' => 'ACTION_END',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Акции',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'LINE_COUNT' => 1,
  'LINK_USE' => 'Y',
  'DESCRIPTION_USE' => 'Y',
  'ELEMENT_HEADER_SHOW' => 'Y',
  'LINK_ALL_SHOW' => 'Y',
  'PREVIEW_SHOW' => 'Y',
  'PREVIEW_TRUNCATE_USE' => 'Y',
  'PREVIEW_TRUNCATE_WORDS' => 30,
  'TIMER_SHOW' => 'Y',
  'TIMER_TIMER_SECONDS_SHOW' => 'N',
  'TIMER_TIMER_HEADER_SHOW' => 'Y',
  'TIMER_TIMER_HEADER' => 'До конца акции',
  'TIMER_SALE_SHOW' => 'Y',
  'TIMER_PROPERTY_DISCOUNT' => 'SALE',
  'TIMER_SALE_HEADER_SHOW' => 'N',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'NAVIGATION_USE' => 'Y',
  'NAVIGATION_ID' => 'news',
  'NAVIGATION_MODE' => 'ajax',
  'NAVIGATION_TEMPLATE' => 'lazy.2',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'DATE_SHOW' => 'Y',
  'DATE_FORMAT' => 'd.m.Y',
  'SEE_ALL_SHOW' => 'Y',
  'SEE_ALL_PLACE' => 'top',
  'SEE_ALL_POSITION' => 'right',
  'SEE_ALL_TEXT' => 'Все акции',
  'NAVIGATION_ALL' => 'N',
  'PAGINATION_USE' => 'Y',
  'PAGINATION_AJAX' => 'Y',
  'PAGINATION_TEMPLATE' => 'lazy.1',
  'PAGINATION_NAME' => 'Акции',
  'PAGINATION_REVERSE' => 'Y',
  'PAGINATION_ALWAYS' => 'Y',
  'PAGINATION_ALL' => 'Y',
  'PAGINATION_WINDOW' => '5',
  'PAGINATION_SAVE' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
), false) ?>
<?= Html::endTag('div') ?>
