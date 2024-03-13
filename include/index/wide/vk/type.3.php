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
<?php $APPLICATION->IncludeComponent('intec.universe:main.vk', 'template.3', array (
  'ACCESS_TOKEN' => '',
  'CACHE_TIME' => '0',
  'CACHE_TYPE' => 'A',
  'COMPONENT_TEMPLATE' => 'template.3',
  'DATE_FORMAT' => 'd.m.Y',
  'DOMAIN' => '',
  'FILTER' => 'all',
  'HEADER_DESCRIPTION' => 'Главные новости нашего сообщества ВК',
  'HEADER_SHOW' => 'N',
  'HEADER_TITLE' => 'Мы Вконтакте',
  'ITEMS_COLUMNS' => '2',
  'ITEMS_COUNT' => '',
  'ITEMS_OFFSET' => '',
  'ITEM_DESCRIPTION_TRUNCATE_USE' => 'N',
  'ITEM_PICTURE_RATIO' => '4x3',
  'ITEM_URL_BLANK' => 'Y',
  'ITEM_URL_USE' => 'Y',
  'LAZYLOAD_USE' => 'Y',
  'MAIN_URL_LIST_BLANK' => 'N',
  'MAIN_URL_LIST_SHOW' => 'Y',
  'MAIN_URL_LIST_TEXT' => 'Все записи',
  'MAIN_URL_LIST_URL' => 'https://vk.com/',
  'SETTINGS_USE' => 'N',
  'USER_ID' => '',
), false) ?>
<?= Html::endTag('div') ?>
