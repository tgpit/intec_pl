<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<div class="intec-editor-layout">
    <slot v-bind:layout="this"></slot>
</div>
