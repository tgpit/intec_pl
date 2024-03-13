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
  'background-color' => '#f8f9fb',
  'margin-top' => '50px',
  'margin-bottom' => '50px',
)]) ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.faq', 'template.4', array (
  'IBLOCK_TYPE' => 'content',
  'IBLOCK_ID' => '83',
  'SECTIONS_MODE' => 'id',
  'SECTIONS' => 
  array (
  ),
  'ELEMENTS_COUNT' => '5',
  'PROPERTY_EXPANDED' => '',
  'HEADER_SHOW' => 'Y',
  'HEADER_POSITION' => 'left',
  'HEADER_TEXT' => 'Вопрос - ответ',
  'DESCRIPTION_SHOW' => 'Y',
  'DESCRIPTION_POSITION' => 'left',
  'DESCRIPTION_TEXT' => 'Соберите самые популярные вопросы пользователей, дайте на них экспертные ответы и разместите на сайте в блоке «Вопрос-ответ». Также пользователям может быть интересна информация об особенностях сотрудничества с вашей компанией.',
  'LIMITED_ITEMS_USE' => 'N',
  'SEE_ALL_SHOW' => 'Y',
  'SEE_ALL_POSITION' => 'left',
  'SEE_ALL_TEXT' => 'Показать все',
  'SEE_ALL_URL' => NULL,
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
  'CACHE_NOTES' => '',
  'SORT_BY' => 'SORT',
  'ORDER_BY' => 'ASC',
  'COMPOSITE_FRAME_MODE' => 'A',
  'COMPOSITE_FRAME_TYPE' => 'AUTO',
  'BY_SECTION' => 'Y',
  'TABS_POSITION' => 'center',
  'ELEMENT_TEXT_ALIGN' => 'center',
  'FOOTER_SHOW' => 'N',
), false) ?>
<?= Html::endTag('div') ?>
