<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<component is="v-tooltip" right>
    <template v-slot:activator="{ on }">
        <?= Html::beginTag('a', [
            'class' => 'intec-editor-menu-item',
            'v-on' => 'on',
            'v-on:click' => '$emit("click")',
            'v-bind:data-active' => 'isInteractive ? (isActive ? "true" : "false") : null',
            'v-bind:data-interactive' => 'isInteractive ? "true" : "false"',
            'v-bind:href' => 'isInteractive && link ? link : null'
        ]) ?>
            <div class="intec-editor-menu-item-wrapper">
                <div class="intec-editor-menu-item-wrapper-2">
                    <slot name="icon"></slot>
                </div>
            </div>
        <?= Html::endTag('a') ?>
    </template>
    <span>{{ name }}</span>
</component>
