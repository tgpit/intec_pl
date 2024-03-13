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
<?php $APPLICATION->IncludeComponent('intec.universe:main.reviews', 'template.11', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '72',
  'SECTIONS_MODE' => 'id',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'ELEMENTS_COUNT' => '',
  'ACTIVE_DATE_SHOW' => 'Y',
  'ACTIVE_DATE_FORMAT' => 'd.m.Y',
  'LIST_PAGE_URL' => '/company/reviews/',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'RATING_SHOW' => 'Y',
  'PROPERTY_RATING' => 'RATING',
  'RATING_MAX' => '5',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'left',
  'HEADER_TEXT' => 'Отзывы о нашей работе',
  'DESCRIPTION_SHOW' => 'N',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'LINK_TEXT' => 'Полный отзыв',
  'BUTTON_ALL_SHOW' => 'Y',
  'BUTTON_ALL_TEXT' => 'Все отзывы',
  'SLIDER_LOOP' => 'Y',
  'SLIDER_AUTO_USE' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
