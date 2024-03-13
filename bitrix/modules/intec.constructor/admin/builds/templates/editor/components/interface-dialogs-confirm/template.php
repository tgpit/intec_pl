<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<?= Html::beginTag('component', [
    'is' => 'v-dialog',
    'content-class' => 'intec-editor-dialog intec-editor-dialog-auto intec-editor-dialog-confirm intec-editor-dialog-theme-default',
    'persistent' => true,
    'v-bind:max-width' => 'maxWidth',
    'v-bind:retain-focus' => 'false',
    'v-model' => 'display'
]) ?>
    <template v-slot:activator="scope">
        <slot name="activator" v-bind="scope"></slot>
    </template>
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-content">
            <div class="intec-editor-dialog-content-wrapper">
                <div class="intec-editor-dialog-content-part">
                    <div class="intec-editor-dialog-message">
                        <div class="intec-editor-dialog-message-title">
                            <slot name="title" v-bind:confirm="confirm" v-bind:data="data" v-bind:reject="reject">
                                {{ $root.$localization.getMessage('dialogs.confirm.message') }}
                            </slot>
                        </div>
                        <div class="intec-editor-dialog-message-description" v-if="$slots.description || $scopedSlots.description">
                            <slot name="description" v-bind:confirm="confirm" v-bind:data="data" v-bind:reject="reject"></slot>
                        </div>
                    </div>
                    <div class="intec-editor-dialog-buttons">
                        <slot name="buttons" v-bind:confirm="confirm" v-bind:data="data" v-bind:reject="reject">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-editor-grid' => [
                                        '',
                                        'i-h-6',
                                        'i-v-4',
                                        'a-h-end',
                                        'a-v-center'
                                    ]
                                ]
                            ]) ?>
                            <div class="intec-editor-grid-item-auto">
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-dialog-button',
                                    'data-action' => 'reject',
                                    'v-ripple' => true,
                                    'v-on:click' => 'reject'
                                ]) ?>
                                {{ $root.$localization.getMessage('dialogs.confirm.buttons.reject') }}
                                <?= Html::endTag('button') ?>
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-dialog-button',
                                    'data-action' => 'confirm',
                                    'v-ripple' => true,
                                    'v-on:click' => 'confirm'
                                ]) ?>
                                {{ $root.$localization.getMessage('dialogs.confirm.buttons.confirm') }}
                                <?= Html::endTag('button') ?>
                            </div>
                            <?= Html::endTag('div') ?>
                        </slot>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('component') ?>
