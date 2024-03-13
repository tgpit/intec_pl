<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-button-counter',
        'intec-grid',
        'intec-grid-a-v-stretch',
        'intec-ui' => [
            '',
            'control-numeric',
            'scheme-current'
        ]
    ],
    'data-role' => 'product.counter'
]) ?>
    <?= Html::tag('a', '-', [
        'class' => [
            'catalog-element-button-counter-item',
            'intec-ui-part-decrement'
        ],
        'href' => 'javascript:void(0)',
        'data' => [
            'type' => 'button',
            'action' => 'decrement'
        ]
    ]) ?>
    <?= Html::input('text', null, 0, [
        'class' => [
            'catalog-element-button-counter-item',
            'intec-ui-part-input'
        ],
        'data-type' => 'input'
    ]) ?>
    <?= Html::tag('a', '+', [
        'class' => [
            'catalog-element-button-counter-item',
            'intec-ui-part-increment'
        ],
        'href' => 'javascript:void(0)',
        'data' => [
            'type' => 'button',
            'action' => 'increment'
        ]
    ]) ?>
<?= Html::endTag('div') ?>