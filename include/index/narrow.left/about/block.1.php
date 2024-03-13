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
<?php $APPLICATION->IncludeComponent('intec.universe:main.about', 'template.1', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '49',
  'SECTIONS_MODE' => 'id',
  'SECTION' => 
  array (
  ),
  'ELEMENTS_MODE' => 'code',
  'ELEMENT' => 'about_1',
  'PICTURE_SOURCES' => 
  array (
    0 => 'preview',
  ),
  'SETTINGS_USE' => 'Y',
  'PROPERTY_BACKGROUND' => 'BACKGROUND_IMAGE',
  'PROPERTY_TITLE' => 'HEADER',
  'PROPERTY_LINK' => 'LINK',
  'PROPERTY_VIDEO' => 'VIDEO_LINK',
  'BACKGROUND_SHOW' => 'Y',
  'TITLE_SHOW' => 'Y',
  'PREVIEW_SHOW' => 'Y',
  'BUTTON_SHOW' => 'Y',
  'BUTTON_BLANK' => 'N',
  'BUTTON_TEXT' => 'Узнать подробнее',
  'PICTURE_SHOW' => 'Y',
  'PICTURE_SIZE' => 'contain',
  'POSITION_HORIZONTAL' => 'center',
  'POSITION_VERTICAL' => 'bottom',
  'VIDEO_SHOW' => 'Y',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
