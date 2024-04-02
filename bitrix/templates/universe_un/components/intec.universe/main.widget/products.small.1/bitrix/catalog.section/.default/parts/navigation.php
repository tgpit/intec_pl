<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>

<?= Html::tag('div', '', [
    'class' => 'widget-banner-products-nav',
    'data-role' => 'container-nav',
    'data-view' => $arVisual['BUTTONS']['NAVIGATION']['VIEW']
]) ?>
<?= Html::tag('div', '', [
    'class' => 'widget-banner-products-dots',
    'data-role' => 'container-dots'
]) ?>