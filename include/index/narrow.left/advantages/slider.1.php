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
<?php $APPLICATION->IncludeComponent('intec.universe:main.advantages', 'template.40', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '51',
  'SECTIONS_MODE' => 'id',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER' => 'Преимущества',
  'DESCRIPTION_SHOW' => 'Y',
  'DESCRIPTION_POSITION' => 'center',
  'DESCRIPTION' => '',
  'LAZYLOAD_USE' => 'Y',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'SLIDER_NAV' => 'Y',
  'SLIDER_LOOP' => 'N',
  'SLIDER_AUTOPLAY' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'PICTURE_SHOW' => 'Y',
  'PREVIEW_SHOW' => 'Y',
  'COLUMNS' => '2',
  'PICTURE_POSITION' => 'top',
), false) ?>
<?= Html::endTag('div') ?>
