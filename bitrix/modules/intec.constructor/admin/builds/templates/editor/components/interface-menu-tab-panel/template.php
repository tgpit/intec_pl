<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<div class="intec-editor-menu-tab-panel" v-if="isActive">
    <component ref="scrollbar" is="vue-scroll" v-on:handle-scroll="handleScroll" v-bind:ops="scrollbarSettings">
        <div class="intec-editor-menu-tab-panel-content">
            <slot v-bind:component="this"></slot>
        </div>
    </component>
</div>
