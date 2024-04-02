<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

?>
<?php return function ($arOffer) { ?>
    <div class="catalog-element-offers-list-item-counter" data-role="counter">
        <?= Html::tag('a', '-', [
            'class' => [
                'catalog-element-offers-list-item-counter-button',
                'intec-cl-background-hover'
            ],
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'decrement'
        ]) ?>
        <?= Html::input('text', null, 0, [
            'data-type' => 'input',
            'class' => 'catalog-element-offers-list-item-counter-input'
        ]) ?>
        <?= Html::tag('a', '+', [
            'class' => [
                'catalog-element-offers-list-item-counter-button',
                'intec-cl-background-hover'
            ],
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'increment'
        ]) ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-counter-control-max-message',
            'data' => [
                'role' => 'max-message-offer'
            ]
        ]) ?>
            <div class="catalog-element-counter-control-max-message-close" data-role="max-message-close">
                &times;
            </div>
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_5_MAX_MESSAGE') ?>
        <?= Html::endTag('div') ?>
    </div>
<?php } ?>