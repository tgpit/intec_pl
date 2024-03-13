<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<div class="intec-editor-container-grid">
    <?= Html::tag('div', null, [
        'class' => 'intec-editor-container-grid-line',
        'data-orientation' => 'vertical',
        'v-for' => '(value, index) in widthLines',
        'v-bind:style' => '{
            "left": widthStep * (index + 1) + measure
        }'
    ]) ?>
    <?= Html::tag('div', null, [
        'class' => 'intec-editor-container-grid-line',
        'data-orientation' => 'horizontal',
        'v-for' => '(value, index) in heightLines',
        'v-bind:style' => '{
            "top": heightStep * (index + 1) + measure
        }'
    ]) ?>
</div>
