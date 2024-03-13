<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$editorVariatorVariant = $this->getComponent('editor-variator-variant');

?>
<?= Html::beginTag('div', [
    'class' => [
        'intec-editor-element'
    ],
    'data' => [
        'element' => 'variator'
    ]
]) ?>
    <?= $editorVariatorVariant->begin([
        'v-if' => 'hasVariant',
        'v-bind:model' => 'variant'
    ]) ?>
    <?= $editorVariatorVariant->end() ?>
<?= Html::endTag('div') ?>
