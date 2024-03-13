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
        'element' => 'component'
    ],
    'v-bind:data-refreshing' => 'isRefreshing ? "true" : "false"'
]) ?>
    <div class="intec-editor-element-content">
        <?= Html::tag('div', null, [
            'class' => 'intec-editor-element-content-wrapper',
            'ref' => 'content',
            'v-html' => 'content'
        ]) ?>
    </div>
    <div class="intec-editor-element-overlay"></div>
<?= Html::endTag('div') ?>
