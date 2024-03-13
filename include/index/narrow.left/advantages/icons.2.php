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
<?= Html::beginTag('div') ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.advantages', 'template.30', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '52',
  'SECTIONS_MODE' => 'id',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'ELEMENTS_COUNT' => 3,
  'HEADER_SHOW' => 'N',
  'DESCRIPTION_SHOW' => 'N',
  'PROPERTY_SVG_FILE' => 'ICON',
  'COLUMNS' => 3,
  'PICTURE_SHOW' => 'Y',
  'PICTURE_POSITION' => 'top',
  'PICTURE_ALIGN' => 'center',
  'SVG_FILE_USE' => 'Y',
  'NAME_SHOW' => 'Y',
  'NAME_ALIGN' => 'center',
  'PREVIEW_SHOW' => 'Y',
  'PREVIEW_ALIGN' => 'center',
), false) ?>
<?= Html::endTag('div') ?>
