<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<transition name="intec-editor-menu-tab">
    <div class="intec-editor-menu-tab" v-if="isActive" v-bind:data-flat="flat ? 'true' : 'false'">
        <div class="intec-editor-menu-tab-popups">
            <slot name="popups" v-bind:component="this"></slot>
        </div>
        <div class="intec-editor-menu-tab-container">
            <div class="intec-editor-menu-tab-header">
                <div class="intec-editor-menu-tab-header-wrapper">
                    <div class="intec-editor-menu-tab-header-text">
                        {{ name }}
                    </div>
                    <div class="intec-editor-menu-tab-header-button" v-on:click="close()">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
            <div class="intec-editor-menu-tab-content">
                <?= Html::beginTag('component', [
                    'class' => 'intec-editor-menu-tab-content-wrapper',
                    'is' => 'vue-scroll',
                    'v-bind:ops' => 'scrollbarSettings'
                ]) ?>
                    <div class="intec-editor-menu-tab-content-wrapper-2">
                        <div class="intec-editor-menu-tab-content-wrapper-3">
                            <slot v-bind:component="this"></slot>
                        </div>
                    </div>
                <?= Html::endTag('component') ?>
            </div>
        </div>
    </div>
</transition>
