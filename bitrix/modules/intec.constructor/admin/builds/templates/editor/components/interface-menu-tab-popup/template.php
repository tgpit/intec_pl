<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<div class="intec-editor-menu-tab-popup" v-if="isActive">
    <div class="intec-editor-menu-tab-popup-button" v-on:click="close">
        <i class="fal fa-times"></i>
    </div>
    <div class="intec-editor-menu-tab-popup-content">
        <slot v-bind:component="this"></slot>
    </div>
</div>
