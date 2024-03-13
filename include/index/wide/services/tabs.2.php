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
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.services', 'template.24', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '61',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'PROPERTY_PRICE' => 'PRICE',
  'PROPERTY_PRICE_OLD' => '',
  'PRICE_SHOW' => 'Y',
  'PROPERTY_CURRENCY' => '',
  'CURRENCY' => 'руб.',
  'PRICE_FORMAT_USE' => 'N',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Услуги',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'SECTION_ELEMENTS_COUNT' => '3',
  'MOBILE_MENU_COLUMN_USE' => 'N',
  'COLUMNS' => '3',
  'LINK_USE' => 'Y',
  'WHOLE_ELEMENT_LINK_USE' => 'N',
  'LINK_PICTURE_EFFECT_ZOOM' => 'N',
  'LINK_COLORING_NAME' => 'N',
  'FOOTER_SHOW' => 'N',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'COMPOSITE_FRAME_MODE' => 'A',
  'COMPOSITE_FRAME_TYPE' => 'AUTO',
  'CHILDREN_DISPLAY' => 'column',
  'CHILDREN_SHOW' => 'Y',
  'PICTURE_SIZE' => 'middle',
  'PICTURE_POSITION_VERTICAL' => 'top',
  'SVG_FILE_USE' => 'N',
  'DESCRIPTION_SHOW' => 'N',
  'HEADER_BUTTON_SHOW' => 'N',
  'PREVIEW_SHOW' => 'N',
  'ORDER_USE' => 'Y',
  'ORDER_FORM_ID' => '21',
  'ORDER_FORM_FIELD' => 'form_text_84',
  'ORDER_FORM_TEMPLATE' => '.default',
  'ORDER_FORM_TITLE' => 'Заказать',
  'ORDER_FORM_CONSENT' => '/company/consent/',
  'ORDER_BUTTON' => 'Заказать',
), false) ?>
<?= Html::endTag('div') ?>
