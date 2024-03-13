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
<?php $APPLICATION->IncludeComponent('intec.universe:main.video', 'template.1', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '81',
  'SECTIONS_MODE' => 'id',
  'ELEMENTS_MODE' => 'code',
  'ELEMENT' => 'video_1',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'PROPERTY_LINK' => 'LINK',
  'HEADER_SHOW' => 'N',
  'DESCRIPTION_SHOW' => 'N',
  'WIDE' => 'Y',
  'HEIGHT' => 500,
  'FADE' => 'N',
  'SHADOW_USE' => 'N',
  'THEME' => 'light',
  'PARALLAX_USE' => 'N',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
