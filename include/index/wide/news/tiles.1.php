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
<?php $APPLICATION->IncludeComponent('intec.universe:main.news', 'template.3', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '70',
  'ELEMENTS_COUNT' => '4',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Новости',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'DATE_SHOW' => 'Y',
  'DATE_FORMAT' => 'd.m.Y',
  'COLUMNS' => 4,
  'LINK_USE' => 'Y',
  'FOOTER_SHOW' => 'N',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
