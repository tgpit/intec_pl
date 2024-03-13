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
<?php $APPLICATION->IncludeComponent('intec.universe:main.instagram', 'template.1', array (
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'Y',
  'ACCESS_TOKEN' => '',
  'COUNT_ITEMS' => '10',
  'CACHE_PATH' => 'upload/intec.universe/instagram/cache/',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'center',
  'HEADER_TEXT' => 'Instagram',
  'DESCRIPTION_SHOW' => 'Y',
  'ITEM_WIDE' => 'N',
  'DESCRIPTION_POSITION' => 'center',
  'DESCRIPTION_TEXT' => 'Статьи, новости и интересные истории в нашем Instagram канале',
  'ITEM_DESCRIPTION_SHOW' => 'Y',
  'ITEM_LINE_COUNT' => '5',
  'ITEM_PADDING_USE' => 'N',
  'FOOTER_SHOW' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
), false) ?>
<?= Html::endTag('div') ?>
