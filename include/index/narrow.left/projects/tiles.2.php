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
<?php $APPLICATION->IncludeComponent('intec.universe:main.projects', 'template.5', array (
  'ALIGNMENT' => 'center',
  'BUTTON_ALL_SHOW' => 'Y',
  'CACHE_TIME' => '0',
  'CACHE_TYPE' => 'A',
  'COLUMNS' => '3',
  'DESCRIPTION_POSITION' => 'center',
  'DESCRIPTION_SHOW' => 'Y',
  'DESCRIPTION_TEXT' => '',
  'DETAIL_URL' => '',
  'ELEMENTS_COUNT' => '',
  'HEADER_POSITION' => 'left',
  'HEADER_SHOW' => 'Y',
  'HEADER_TEXT' => 'Реализованные проекты',
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '80',
  'LAZYLOAD_USE' => 'N',
  'LINK_USE' => 'Y',
  'LIST_PAGE_URL' => '',
  'ORDER_BY' => 'ASC',
  'SECTIONS_MODE' => 'id',
  'SECTION_URL' => '',
  'SETTINGS_USE' => 'N',
  'SLIDER_USE' => 'N',
  'SORT_BY' => 'SORT',
  'TABS_ELEMENTS' => '',
  'TABS_POSITION' => 'left',
  'TABS_USE' => 'Y',
  'COMPONENT_TEMPLATE' => 'template.5',
  'BUTTON_ALL_TEXT' => 'Показать все',
), false) ?>
<?= Html::endTag('div') ?>
