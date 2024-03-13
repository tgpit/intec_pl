<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<component is="v-interface-menu-tab" code="variator" v-bind:name="$localization.getMessage('menu.items.variator.name')" v-bind:active="$root.hasSelection && $root.selection.hasVariator()" flat>
    <template v-if="$root.hasSelection" v-slot:default="slot">
        <div class="intec-editor-settings">
            <div class="intec-editor-settings-groups">
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.variator.groups.settings') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <template v-if="$root.selection.element.variants.length > 0">
                                <template v-if="$root.selection.element.getVariant()">
                                    <div class="intec-editor-settings-field">
                                        <div class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.variator.groups.settings.field.variantName') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.element.getVariant().name',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode">
                                        <div class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.variator.groups.settings.field.variantCode') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.element.getVariant().code',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </template>
                                <template v-else>
                                    <div class="intec-editor-settings-field">
                                        <div class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.variator.groups.settings.field.variantName') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'hide-details' => 'auto',
                                                'disabled' => true,
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode">
                                        <div class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.variator.groups.settings.field.variantCode') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'hide-details' => 'auto',
                                                'disabled' => true,
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </template>
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.variator.groups.settings.field.variants') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <component class="intec-editor-settings-sortable" is="draggable" v-model="$root.selection.element.variants" direction="vertical" handle=".intec-editor-settings-sortable-item-drag" v-on:start="interfaceMenuTabsVariatorVariantsDragStart" v-on:end="interfaceMenuTabsVariatorVariantsDragEnd">
                                            <div class="intec-editor-settings-sortable-item" v-for="variant in $root.selection.element.variants" v-bind:key="variant.uid">
                                                <div class="intec-editor-settings-sortable-item-wrapper">
                                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                                        <div class="intec-editor-grid-item-auto">
                                                            <div class="intec-editor-settings-sortable-item-drag">
                                                                <i class="fas fa-grip-vertical"></i>
                                                            </div>
                                                        </div>
                                                        <div class="intec-editor-grid-item-auto">
                                                            <div class="intec-editor-settings-sortable-item-selector">
                                                                <?= Html::tag('component', null, [
                                                                    'is' => 'v-checkbox',
                                                                    'class' => 'v-input-theme-settings',
                                                                    'v-bind:input-value' => '$root.selection.element.getVariant() === variant',
                                                                    'v-on:change' => 'function (value) { $root.selection.element.setVariant(value ? variant : null) }',
                                                                    'hide-details' => 'auto'
                                                                ]) ?>
                                                            </div>
                                                        </div>
                                                        <div class="intec-editor-grid-item">
                                                            <div class="intec-editor-settings-sortable-item-content">
                                                                {{ variant.name }}
                                                            </div>
                                                        </div>
                                                        <div class="intec-editor-grid-item-auto">
                                                            <div class="intec-editor-settings-sortable-item-buttons">
                                                                <component is="v-interface-dialogs-confirm" v-on:confirm="interfaceMenuTabsVariatorVariantsRemove(variant)">
                                                                    <template v-slot:activator="slot">
                                                                        <div class="intec-editor-settings-sortable-item-button" v-on="slot.on">
                                                                            <i class="far fa-times"></i>
                                                                        </div>
                                                                    </template>
                                                                </component>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </component>
                                    </div>
                                </div>
                            </template>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-content">
                                    <button class="intec-editor-button" data-fullsized="true" v-on:click="interfaceMenuTabsVariatorVariantsAdd">
                                        {{ $localization.getMessage('menu.items.variator.groups.settings.field.variants.add') }}
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
