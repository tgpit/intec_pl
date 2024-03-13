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
<?= Html::beginTag('div') ?>
<?php $APPLICATION->IncludeComponent('intec.universe:main.form', 'template.1', array (
  'ID' => '13',
  'NAME' => 'Обратная связь',
  'SETTINGS_USE' => 'Y',
  'LAZYLOAD_USE' => 'N',
  'CONSENT' => '/company/consent/',
  'TEMPLATE' => '.default',
  'TITLE' => 'Индивидуальный подход',
  'DESCRIPTION_SHOW' => 'Y',
  'DESCRIPTION_TEXT' => 'Наши специалисты свяжутся с вами и найдут оптимальные для вас условия сотрудничества',
  'BUTTON_TEXT' => 'Обратная связь',
  'THEME' => 'dark',
  'VIEW' => 'left',
  'BACKGROUND_COLOR' => '#f4f4f4',
  'BACKGROUND_IMAGE_USE' => 'Y',
  'BACKGROUND_IMAGE_PATH' => '/images/forms/form.1/background.jpg',
  'BACKGROUND_IMAGE_HORIZONTAL' => 'center',
  'BACKGROUND_IMAGE_VERTICAL' => 'center',
  'BACKGROUND_IMAGE_SIZE' => 'cover',
  'BACKGROUND_IMAGE_PARALLAX_USE' => 'N',
  'CACHE_TYPE' => 'A',
  'CACHE_TIME' => '3600000',
), false) ?>
<?= Html::endTag('div') ?>
