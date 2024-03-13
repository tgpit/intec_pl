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
    'content-class' => 'intec-editor-dialog intec-editor-dialog-auto intec-editor-dialog-block-convert intec-editor-dialog-theme-default',
    'persistent' => true,
    'max-width' => '400px',
    'v-bind:retain-focus' => 'false',
    'v-model' => 'display'
]) ?>
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-controls">
            <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                <div class="intec-editor-grid-item"></div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="intec-editor-dialog-title" data-align="left">
            {{ $root.$localization.getMessage('dialogs.blockConvert.title') }}
        </div>
        <div class="intec-editor-dialog-content">
            <div class="intec-editor-dialog-content-wrapper intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-o-vertical">
                <div class="intec-editor-grid-item-auto" v-if="hasError">
                    <div class="intec-editor-dialog-error intec-editor-dialog-content-part">
                        {{ error }}
                    </div>
                </div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-field intec-editor-dialog-content-part" data-first="true">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-dialog-default',
                            'is' => 'v-text-field',
                            'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.blockConvert.fields.name.name")',
                            'v-bind:disabled' => 'isApplying',
                            'v-model' => 'name',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-field intec-editor-dialog-content-part">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-dialog-default',
                            'is' => 'v-text-field',
                            'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.blockConvert.fields.code.name")',
                            'v-bind:disabled' => 'isApplying',
                            'v-model' => 'code',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-buttons intec-editor-dialog-content-part">
                        <button class="intec-editor-dialog-button intec-editor-button" v-ripple data-fullsized="true" v-bind:disabled="isApplying" v-on:click="apply">
                            {{ $root.$localization.getMessage('dialogs.blockConvert.buttons.convert') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('component') ?>
