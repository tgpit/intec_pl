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
<?php $APPLICATION->IncludeComponent('intec.universe:main.collections', 'template.3', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '77',
  'SECTIONS_MODE' => 'id',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '4',
  'SETTINGS_USE' => 'Y',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Коллекции',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'COLUMNS' => '4',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'FOOTER_BLOCK_SHOW' => 'Y',
  'FOOTER_BUTTON_SHOW' => 'Y',
  'FOOTER_BUTTON_TEXT' => 'Показать все',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'NAVIGATION_USE' => 'Y',
  'NAVIGATION_ID' => 'collections',
  'NAVIGATION_MODE' => 'ajax',
  'NAVIGATION_ALL' => 'Y',
  'NAVIGATION_TEMPLATE' => 'lazy.2',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'LAZYLOAD_USE' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
