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
<?php $APPLICATION->IncludeComponent('intec.universe:main.shares', 'template.3', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '67',
  'ELEMENTS_COUNT' => '4',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'TIMER_USE' => 'Y',
  'TIMER_SHOW' => 'Y',
  'ELEMENT_HEADER_PROPERTY_TEXT' => 'DURATION',
  'TIMER_PROPERTY_UNTIL_DATE' => 'ACTION_END',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Акции',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'LINE_COUNT' => 2,
  'LINK_USE' => 'Y',
  'LINK_ALL_SHOW' => 'Y',
  'LINK_ALL_POSITION' => 'right',
  'LINK_ALL_TEXT' => 'Показать все',
  'ELEMENT_HEADER_SHOW' => 'Y',
  'TIMER_TIMER_SECONDS_SHOW' => 'N',
  'TIMER_SALE_SHOW' => 'Y',
  'TIMER_PROPERTY_DISCOUNT' => 'SALE',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'NAVIGATION_USE' => 'Y',
  'NAVIGATION_ID' => 'news',
  'NAVIGATION_MODE' => 'ajax',
  'NAVIGATION_TEMPLATE' => 'lazy.2',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'DESCRIPTION_USE' => 'Y',
  'SEE_ALL_SHOW' => 'N',
  'NAVIGATION_ALL' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
), false) ?>
<?= Html::endTag('div') ?>
