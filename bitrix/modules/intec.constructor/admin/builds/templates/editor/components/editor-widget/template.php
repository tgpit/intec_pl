<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<?= Html::beginTag('div', [
    'class' => [
        'intec-editor-element'
    ],
    'data' => [
        'element' => 'widget'
    ]
]) ?>
    <div class="intec-editor-element-content">
        <div class="intec-editor-element-content-wrapper">
            <component v-bind:is="component" v-if="isLoaded" v-bind:model="model" v-on:save-properties="saveProperties"></component>
        </div>
    </div>
    <div class="intec-editor-element-overlay"></div>
<?= Html::endTag('div') ?>
