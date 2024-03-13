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
<?php $APPLICATION->IncludeComponent('intec.universe:main.services', 'template.21', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '61',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'Y',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Услуги',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'LINK_USE' => 'Y',
  'COLUMNS' => '2',
  'CHILDREN_DISPLAY' => 'line',
  'CHILDREN_SHOW' => 'Y',
  'PICTURE_SIZE' => 'middle',
  'PICTURE_POSITION_VERTICAL' => 'top',
  'SVG_FILE_USE' => 'N',
  'DESCRIPTION_SHOW' => 'Y',
  'HEADER_BUTTON_SHOW' => 'Y',
  'HEADER_BUTTON_TEXT' => 'Все услуги',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
