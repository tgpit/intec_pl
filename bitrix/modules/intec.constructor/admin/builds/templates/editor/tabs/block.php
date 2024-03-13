<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<component is="v-interface-menu-tab" code="block" v-bind:name="$localization.getMessage('menu.items.block.name')" v-bind:active="$root.hasSelection && $root.selection.hasBlock()" flat>
    <template v-if="$root.hasSelection" v-slot:default="slot">
        <div class="intec-editor-settings">
            <div class="intec-editor-settings-groups">
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.block.groups.settings') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.block.groups.settings.field.name') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-text-field', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.element.name',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-content">
                                    <button class="intec-editor-button" data-fullsized="true" v-on:click="interfaceMenuTabsBlockConvert">
                                        {{ $localization.getMessage('menu.items.block.groups.settings.field.convert') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</component>
