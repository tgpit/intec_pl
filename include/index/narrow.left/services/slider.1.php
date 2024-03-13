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
<?php $APPLICATION->IncludeComponent('intec.universe:main.services', 'template.25', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '61',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Услуги',
  'DESCRIPTION_BLOCK_SHOW' => 'Y',
  'DESCRIPTION_BLOCK_POSITION' => 'center',
  'DESCRIPTION_BLOCK_TEXT' => '',
  'LAZYLOAD_USE' => 'Y',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'SLIDER_NAV' => 'Y',
  'SLIDER_LOOP' => 'N',
  'SLIDER_AUTOPLAY' => 'N',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
