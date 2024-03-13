<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<component is="v-interface-menu-tab" code="widget" v-bind:name="$localization.getMessage('menu.items.widget.name')" v-bind:active="$root.hasSelection && $root.selection.hasWidget()" flat>
    <template v-slot:default="slot">
        <div class="intec-editor-settings" v-if="$root.hasSelection">
            <template v-if="$root.hasSelectionWidget">
                <div class="intec-editor-settings-groups">
                    <div class="intec-editor-settings-group">
                        <div class="intec-editor-settings-group-name">
                            {{ $localization.getMessage('menu.items.widget.groups.settings') }}
                        </div>
                        <div class="intec-editor-settings-group-content">
                            <div class="intec-editor-settings-fields">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.widget.fields.template.name') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <?= Html::tag('v-select', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-model' => '$root.selection.element.template',
                                            'v-bind:items' => '$root.selectionWidget.templates',
                                            'v-bind:item-text' => 'function (item) { return item.code }',
                                            'v-bind:item-value' => 'function (item) { return item.code }',
                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                            'hide-details' => 'auto',
                                            'dark' => true,
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?= Html::tag('component', null, [
                        'v-bind:is' => '$root.selectionWidget.compileSettingsComponent($root.selection.element)',
                        'v-if' => '$root.isSelectionWidgetResourceLoaded',
                        'v-bind:model' => '$root.selection.element',
                        'v-on:save-properties' => 'function (properties) { $root.selection.element.properties = properties; }'
                    ]) ?>
                </div>
            </template>
        </div>
    </template>
</component>
