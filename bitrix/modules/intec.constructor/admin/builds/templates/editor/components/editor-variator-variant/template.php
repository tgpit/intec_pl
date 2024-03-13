<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$editorContainer = $this->getComponent('editor-container');

?>
<?= Html::beginTag('div', [
    'class' => [
        'intec-editor-element'
    ],
    'data' => [
        'element' => 'variator.variant'
    ]
]) ?>
    <?= $editorContainer->begin([
        'v-if' => 'hasContainer',
        'v-bind:model' => 'container'
    ]) ?>
    <?= $editorContainer->end() ?>
<?= Html::endTag('div') ?>