<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-counter-control',
        'intec-ui' => [
            '',
            'control-numeric',
            'view-2',
            'scheme-current',
            'size-4'
        ]
    ],
    'data' => [
        'role' => 'counter',
    ]
]) ?>
    <?= Html::tag('a', '-', [
        'class' => 'intec-ui-part-decrement',
        'href' => 'javascript:void(0)',
        'data-type' => 'button',
        'data-action' => 'decrement'
    ]) ?>
    <?= Html::input('text', null, 0, [
        'data-type' => 'input',
        'class' => 'intec-ui-part-input'
    ]) ?>
    <div class="intec-ui-part-increment-wrapper">
        <?= Html::tag('a', '+', [
            'class' => 'intec-ui-part-increment',
            'href' => 'javascript:void(0)',
            'data-type' => 'button',
            'data-action' => 'increment'
        ]) ?>

        <div class="catalog-element-counter-control-max-message" data-role="max-message">
            <div class="catalog-element-counter-control-max-message-close" data-role="max-message-close">
                &times;
            </div>
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_MAX_MESSAGE') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>
