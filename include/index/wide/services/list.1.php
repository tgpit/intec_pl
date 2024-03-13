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
<?php $APPLICATION->IncludeComponent('intec.universe:main.services', 'template.23', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '61',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'Y',
  'PROPERTY_MEASURE' => '',
  'PROPERTY_PRICE' => 'PRICE',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Услуги',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'LINK_USE' => 'Y',
  'PREVIEW_SHOW' => 'Y',
  'PRICE_SHOW' => 'Y',
  'ORDER_USE' => 'Y',
  'ORDER_FORM_ID' => '21',
  'ORDER_FORM_FIELD' => 'form_text_84',
  'ORDER_FORM_TEMPLATE' => '.default',
  'ORDER_FORM_TITLE' => 'Заказать',
  'ORDER_FORM_CONSENT' => '/company/consent/',
  'LIST_PAGE_URL' => '',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
