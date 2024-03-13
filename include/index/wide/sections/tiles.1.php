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
<?php $APPLICATION->IncludeComponent('intec.universe:main.sections', 'template.1', array (
  'IBLOCK_TYPE' => 'catalogs',
  'IBLOCK_ID' => '58',
  'QUANTITY' => 'N',
  'SECTIONS_MODE' => 'id',
  'DEPTH' => 1,
  'ELEMENTS_COUNT' => '10',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER_TEXT' => 'Популярные категории',
  'DESCRIPTION_SHOW' => 'N',
  'BUTTON_ALL_SHOW' => 'Y',
  'BUTTON_ALL_TEXT' => 'Весь каталог',
  'LINE_COUNT' => 5,
  'SECTION_URL' => '',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
