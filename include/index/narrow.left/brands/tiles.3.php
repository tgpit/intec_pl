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
<?php $APPLICATION->IncludeComponent('intec.universe:main.brands', 'template.4', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '84',
  'SECTIONS_MODE' => 'id',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => 3600000,
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'ELEMENTS_COUNT' => 8,
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'left',
  'HEADER_TEXT' => 'Бренды',
  'LINK_USE' => 'Y',
  'LINK_BLANK' => 'Y',
  'FOOTER_SHOW' => 'Y',
  'FOOTER_POSITION' => 'center',
  'FOOTER_BUTTON_SHOW' => 'N',
  'SECTION_URL' => '',
  'DETAIL_URL' => '',
  'DESCRIPTION_SHOW' => 'Y',
  'DESCRIPTION_POSITION' => 'left',
  'DESCRIPTION_TEXT' => 'Предлагаем вам широкий ассортимент продукции, который в совокупности с нашими услугами поможет повысить эффективность вашего бизнеса, автоматизировать бизнес- процессы и защитит вас от киберпреступников.',
  'LINE_COUNT' => 4,
  'ALIGNMENT' => 'center',
  'EFFECT_PRIMARY' => 'shadow',
  'EFFECT_SECONDARY' => 'grayscale',
  'TRANSPARENCY' => 0,
  'BORDER_SHOW' => 'Y',
  'SHOW_ALL_BUTTON_SHOW' => 'Y',
  'SHOW_ALL_BUTTON_TEXT' => 'Все бренды',
  'SHOW_ALL_BUTTON_POSITION' => 'left',
  'SHOW_ALL_BUTTON_BORDER' => 'rectangular',
  'LIST_PAGE_URL' => '/help/brands/',
), false) ?>
<?= Html::endTag('div') ?>
