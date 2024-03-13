<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<?= Html::beginTag('div', [
    'id' => 'basket-warning',
    'class' => [
        'basket-alert',
        'basket-alert-sticky'
    ],
    'style' => [
        'display' => 'none'
    ]
]) ?>
<?= Html::tag('div', null, [
    'class' => [
        'basket-alert-close',
        'far fa-times',
        'intec-cl-text'
    ],
    'data-entity' => 'basket-items-warning-notification-close'
]) ?>
<?= Html::tag('div', null, [
    'data-entity' => 'basket-general-warnings',
    'style' => [
        'display' => 'none'
    ]
]) ?>
<?= Html::beginTag('div', [
    'data-entity' => 'basket-item-warnings',
    'style' => [
        'display' => 'none'
    ]
]) ?>
<?= Html::tag('span', Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ALERT_GLOBAL_PART_1')) ?>
<?= Html::tag('span', null, [
    'class' => 'basket-alert-interactive intec-cl-text',
    'data-entity' => 'basket-items-warning-count'
]) ?>
<?= Html::tag('span', Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ALERT_GLOBAL_PART_2')) ?>
<?= Html::endTag('div') ?>
<?= Html::endTag('div') ?>
