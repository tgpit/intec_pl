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
    'content-class' => 'intec-editor-dialog intec-editor-dialog-auto intec-editor-dialog-container-structure intec-editor-dialog-theme-default',
    'persistent' => true,
    'max-width' => '960',
    'v-model' => 'display',
    'v-bind:retain-focus' => 'false'
]) ?>
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-controls">
            <div class="intec-editor-grid intec-editor-grid-i-h-12">
                <div class="intec-editor-grid-item"></div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="intec-editor-dialog-title" data-align="left">
            {{ $root.$localization.getMessage('dialogs.containerStructure.title') }}
        </div>
        <div class="intec-editor-dialog-content">
            <div class="intec-editor-dialog-content-wrapper">
                <div class="intec-editor-dialog-content-part">
                    <?= Html::tag('component', null, [
                        'is' => 'v-textarea',
                        'class' => 'v-input-theme-dialog-default',
                        'hide-details' => 'auto',
                        'solo' => true,
                        'flat' => true,
                        'readonly' => true,
                        'no-resize' => true,
                        'height' => '450px',
                        'v-model' => 'code'
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('component') ?>