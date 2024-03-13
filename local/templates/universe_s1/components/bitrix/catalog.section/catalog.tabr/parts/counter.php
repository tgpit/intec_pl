<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

?>
<?php $vCounter = function ($arItem) { ?>
    <div class="catalog-section-item-counter intec-grid-item">
        <div class="intec-ui intec-ui-control-numeric intec-ui-view-1" data-role="item.counter">
            <?= Html::beginTag('a', [
                'class' => [
                    'intec-ui-part-decrement',
                    'intec-cl-background-hover',
                    'intec-cl-border-hover'
                ],
                'href' => 'javascript:void(0)',
                'data-type' => 'button',
                'data-action' => 'decrement'
            ]) ?>
                <i class="catalog-section-item-counter-text far fa-minus"></i>
            <?= Html::endTag('a') ?>
            <?= Html::input('text', null, 0, [
                'data-type' => 'input',
                'class' => 'intec-ui-part-input'
            ]) ?>
            <div class="intec-ui-part-increment-wrapper">
                <?= Html::beginTag('a', [
                    'class' => [
                        'intec-ui-part-increment',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover'
                    ],
                    'href' => 'javascript:void(0)',
                    'data-type' => 'button',
                    'data-action' => 'increment'
                ]) ?>
                    <i class="catalog-section-item-counter-text far fa-plus"></i>
                <?= Html::endTag('a') ?>

                <div class="catalog-section-item-counter-max-message" data-role="max-message">
                    <div class="catalog-section-item-counter-max-message-close" data-role="max-message-close">
                        &times;
                    </div>
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_MAX_MESSAGE') ?>
                </div>
            </div>
        </div>
    </div>
<?php } ?>