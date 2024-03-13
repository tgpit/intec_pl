<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<component is="v-dialog" v-bind:retain-focus="false" persistent max-width="400" content-class="intec-editor-dialog intec-editor-dialog-auto intec-editor-dialog-area-select intec-editor-dialog-theme-default" v-model="display">
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-controls">
            <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                <div class="intec-editor-grid-item"></div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="intec-editor-dialog-title" data-align="left">
            {{ $root.$localization.getMessage('dialogs.areaSelect.title') }}
        </div>
        <div class="intec-editor-dialog-content">
            <div class="intec-editor-dialog-content-wrapper intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-o-vertical">
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-content-part">
                        <?= Html::tag('v-select', null, [
                            'class' => 'v-input-theme-dialog-default',
                            'v-bind:items' => 'areas',
                            'v-bind:item-text' => 'function (item) { return "[" + item.id + "] " + item.name }',
                            'v-bind:item-value' => 'function (item) { return item }',
                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-dialog-default"}',
                            'v-model' => 'area',
                            'v-bind:no-data-text' => '$root.$localization.getMessage("dialogs.areaSelect.data.unavailable")',
                            'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.areaSelect.data.unavailable")',
                            'flat' => true,
                            'solo' => true,
                            'hide-details' => 'auto'
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-buttons intec-editor-dialog-content-part">
                        <button class="intec-editor-dialog-button intec-editor-button" v-ripple data-fullsized="true" v-on:click="selectArea">
                            {{ $root.$localization.getMessage('dialogs.areaSelect.buttons.select') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</component>
