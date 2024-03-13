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
<?php $APPLICATION->IncludeComponent('intec.universe:main.services', 'template.2', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '61',
  'ELEMENTS_COUNT' => '5',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'PROPERTY_PRICE' => 'PRICE',
  'HEADER_BLOCK_SHOW' => 'Y',
  'HEADER_BLOCK_POSITION' => 'center',
  'HEADER_BLOCK_TEXT' => 'Услуги',
  'DESCRIPTION_BLOCK_SHOW' => 'N',
  'TEMPLATE_VIEW' => 'mosaic',
  'PRICE_SHOW' => 'N',
  'BUTTON_SHOW' => 'Y',
  'BUTTON_TYPE' => 'order',
  'BUTTON_FORM_ID' => '21',
  'FORM_FIELD' => 'form_text_84',
  'FORM_TEMPLATE' => '.default',
  'FORM_TITLE' => 'Заказать услугу',
  'CONSENT_URL' => '/company/consent/',
  'BUTTON_TEXT' => 'ЗАКАЗАТЬ',
  'FOOTER_SHOW' => 'N',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
