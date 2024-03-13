<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

?>
<div class="catalog-element-counter-container">
    <!--noindex-->
        <div class="catalog-element-counter" data-role="counter">
            <?= Html::tag('a', '-', [
                'class' => [
                    'catalog-element-counter-button',
                    'intec-cl-text-hover'
                ],
                'href' => 'javascript:void(0)',
                'data-type' => 'button',
                'data-action' => 'decrement'
            ]) ?>
            <?= Html::input('text', null, 0, [
                'data-type' => 'input',
                'class' => 'catalog-element-counter-input'
            ]) ?>
            <div class="catalog-element-counter-button-wrapper">
                <?= Html::tag('a', '+', [
                    'class' => [
                        'catalog-element-counter-button',
                        'intec-cl-text-hover'
                    ],
                    'href' => 'javascript:void(0)',
                    'data-type' => 'button',
                    'data-action' => 'increment'
                ]) ?>
                <div class="catalog-element-counter-control-max-message" data-role="max-message">
                    <div class="catalog-element-counter-control-max-message-close" data-role="max-message-close">
                        &times;
                    </div>
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_5_MAX_MESSAGE') ?>
                </div>
            </div>
        </div>
    <!--/noindex-->
</div>