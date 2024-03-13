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
  'margin-top' => '60px',
  'margin-bottom' => '60px',
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.stories', 'template.1', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '85',
  'SECTIONS_MODE' => 'id',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '',
  'ELEMENT_ITEMS_COUNT' => '',
  'LIST_VIEW' => 'round',
  'NAVIGATION_BUTTON_SHOW' => 'Y',
  'POPUP_TIME' => '10',
  'PROPERTY_LINK' => 'LINK',
  'PROPERTY_BUTTON_TEXT' => 'BUTTON_TEXT',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER_TEXT' => 'Истории',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
), false) ?>
<?= Html::endTag('div') ?>
