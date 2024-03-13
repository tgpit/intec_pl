<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$editorContainer = $this->getComponent('editor-container');

?>
<div class="intec-editor-layout-zone">
    <?= $editorContainer->begin([
        'ref' => 'container',
        'v-if' => 'container',
        'v-bind:model' => 'container'
    ]) ?>
    <?= $editorContainer->end() ?>
</div>
