<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

?>
<?php $vCounter = function ($arItem) { ?>
    <div class="catalog-section-item-counter intec-grid-item">
        <?= Html::beginTag('div', [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-numeric',
                    'view-1',
                    'scheme-current'
                ]
            ],
            'data-role' => 'item.counter'
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

                <div class="catalog-section-item-counter-max-message" data-role="max-message">
                    <div class="catalog-section-item-counter-max-message-close" data-role="max-message-close">
                        &times;
                    </div>
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_2_MAX_MESSAGE') ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    </div>
<?php };