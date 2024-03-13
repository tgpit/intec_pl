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
<?php $APPLICATION->IncludeComponent('intec.universe:main.reviews', 'template.18', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '72',
  'SECTIONS_MODE' => 'id',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'ELEMENTS_COUNT' => 4,
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'left',
  'HEADER_TEXT' => 'Истории успеха клиентов',
  'DESCRIPTION_SHOW' => 'N',
  'RATING_SHOW' => 'Y',
  'PROPERTY_RATING' => 'RATING',
  'RATING_MAX' => '5',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'BUTTON_ALL_SHOW' => 'Y',
  'BUTTON_ALL_TEXT' => 'Все отзывы',
  'LIST_PAGE_URL' => '/company/reviews/',
  'VIDEO_SHOW' => 'Y',
  'PROPERTY_VIDEO' => 'VIDEOS_ELEMENTS',
  'VIDEO_IBLOCK_TYPE' => 'content',
  'VIDEO_IBLOCK_ID' => '81',
  'VIDEO_IBLOCK_PROPERTY_LINK' => 'LINK',
  'VIDEO_IMAGE_QUALITY' => 'hqdefault',
  'SLIDER_USE' => 'Y',
  'SLIDER_DOTS' => 'Y',
  'SLIDER_LOOP' => 'Y',
  'SLIDER_AUTO_USE' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
