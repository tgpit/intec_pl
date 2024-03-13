<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<component is="v-interface-menu-tab" code="container" v-bind:name="$localization.getMessage('menu.items.container.name')" v-bind:active="$root.hasSelection" flat>
    <template v-if="$root.hasSelection" v-slot:popups="slot">
        <component is="v-interface-menu-tab-popup" code="size" style="width: 600px;">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.size.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content">
                    <div class="intec-editor-settings-fields">
                        <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-a-v-center intec-editor-grid-i-h-16">
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.size.field.width.min') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.width.min.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '$root.selection.getStyleProperty(\'width\').measures',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.width.min.measure',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.size.field.width.max') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.width.max.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '$root.selection.getStyleProperty(\'width\').measures',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.width.max.measure',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.size.field.height.min') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.height.min.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '$root.selection.getStyleProperty(\'height\').measures',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.height.min.measure',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.size.field.height.max') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.height.max.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '$root.selection.getStyleProperty(\'height\').measures',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.height.max.measure',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="indent" style="width: 600px;">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.indent.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content" data-children="true">
                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-start intec-editor-grid-i-h-16">
                        <div class="intec-editor-grid-item-2">
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.indent.child.margin') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.top') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.margin.top.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'margin\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.margin.top.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.bottom') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.margin.bottom.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'margin\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.margin.bottom.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.left') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.margin.left.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'margin\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.margin.left.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.right') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.margin.right.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'margin\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.margin.right.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="intec-editor-grid-item-2">
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.indent.child.padding') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.top') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.padding.top.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'padding\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.padding.top.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.bottom') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.padding.bottom.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'padding\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.padding.bottom.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.left') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.padding.left.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'padding\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.padding.left.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.indent.child.field.right') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.padding.right.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'padding\').measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.padding.right.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="background">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.background.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content">
                    <div class="intec-editor-settings-fields">
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.background.field.repeat') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-settings',
                                    'v-bind:items' => '[
                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.null")},
                                        {"value": "no-repeat", "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.no-repeat")},
                                        {"value": "repeat", "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.repeat")},
                                        {"value": "repeat-x", "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.repeat-x")},
                                        {"value": "repeat-y", "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.repeat-y")},
                                        {"value": "inherit", "text": $localization.getMessage("menu.items.container.groups.background.field.repeat.inherit")}
                                    ]',
                                    'item-value' => 'value',
                                    'item-text' => 'text',
                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                    'v-model' => '$root.selection.properties.background.repeat',
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.background.field.position.top') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('v-text-field', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-model' => '$root.selection.properties.background.position.top.value',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('v-select', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-bind:items' => '$root.selection.getStyleProperty(\'background\').measures',
                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                            'v-model' => '$root.selection.properties.background.position.top.measure',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.background.field.position.left') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('v-text-field', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-model' => '$root.selection.properties.background.position.left.value',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('v-select', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-bind:items' => '$root.selection.getStyleProperty(\'background\').measures',
                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                            'v-model' => '$root.selection.properties.background.position.left.measure',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.background.field.size.type') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-settings',
                                    'v-bind:items' => '[
                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.background.field.size.type.null")},
                                        {"value": "auto", "text": $localization.getMessage("menu.items.container.groups.background.field.size.type.auto")},
                                        {"value": "cover", "text": $localization.getMessage("menu.items.container.groups.background.field.size.type.cover")},
                                        {"value": "contain", "text": $localization.getMessage("menu.items.container.groups.background.field.size.type.contain")},
                                        {"value": "custom", "text": $localization.getMessage("menu.items.container.groups.background.field.size.type.custom")}
                                    ]',
                                    'item-value' => 'value',
                                    'item-text' => 'text',
                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                    'v-model' => '$root.selection.properties.background.size.type',
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                        </div>
                        <template v-if="$root.selection.properties.background.size.type === 'custom'">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.background.field.size.width') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.background.size.width.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'background\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.background.size.width.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.background.field.size.height') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.background.size.height.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'background\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.background.size.height.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="background.color" style="width: 372px;">
            <template v-slot>
                <?= Html::tag('v-color-picker', null, [
                    'class' => '',
                    'v-model' => '$root.selection.propertyBackgroundColor',
                    'v-bind:value' => '$root.selection.propertyBackgroundColor',
                    'mode' => 'hexa',
                    'hide-mode-switch' => true,
                    'flat' => true,
                    'dark' => true,
                ]) ?>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="border" style="width: 600px;">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.border.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content" data-children="true">
                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-start intec-editor-grid-i-h-16">
                        <div class="intec-editor-grid-item-2">
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.child.top') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.width') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.border.top.width.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'border\').width.measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.border.top.width.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.color') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.border.top.color.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.style') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '[
                                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.border.field.style.null")},
                                                        {"value": "solid", "text": $localization.getMessage("menu.items.container.groups.border.field.style.solid")},
                                                        {"value": "dotted", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dotted")},
                                                        {"value": "dashed", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dashed")},
                                                        {"value": "double", "text": $localization.getMessage("menu.items.container.groups.border.field.style.double")},
                                                        {"value": "groove", "text": $localization.getMessage("menu.items.container.groups.border.field.style.groove")},
                                                        {"value": "ridge", "text": $localization.getMessage("menu.items.container.groups.border.field.style.ridge")},
                                                        {"value": "inset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.inset")},
                                                        {"value": "outset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.outset")}
                                                    ]',
                                                    'item-value' => 'value',
                                                    'item-text' => 'text',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.border.top.style.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.child.bottom') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.width') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.border.bottom.width.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'border\').width.measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.border.bottom.width.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.color') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.border.bottom.color.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.style') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '[
                                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.border.field.style.null")},
                                                        {"value": "solid", "text": $localization.getMessage("menu.items.container.groups.border.field.style.solid")},
                                                        {"value": "dotted", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dotted")},
                                                        {"value": "dashed", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dashed")},
                                                        {"value": "double", "text": $localization.getMessage("menu.items.container.groups.border.field.style.double")},
                                                        {"value": "groove", "text": $localization.getMessage("menu.items.container.groups.border.field.style.groove")},
                                                        {"value": "ridge", "text": $localization.getMessage("menu.items.container.groups.border.field.style.ridge")},
                                                        {"value": "inset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.inset")},
                                                        {"value": "outset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.outset")}
                                                    ]',
                                                    'item-value' => 'value',
                                                    'item-text' => 'text',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.border.bottom.style.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="intec-editor-grid-item-2">
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.child.left') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.width') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.border.left.width.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'border\').width.measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.border.left.width.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.color') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.border.left.color.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.style') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '[
                                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.border.field.style.null")},
                                                        {"value": "solid", "text": $localization.getMessage("menu.items.container.groups.border.field.style.solid")},
                                                        {"value": "dotted", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dotted")},
                                                        {"value": "dashed", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dashed")},
                                                        {"value": "double", "text": $localization.getMessage("menu.items.container.groups.border.field.style.double")},
                                                        {"value": "groove", "text": $localization.getMessage("menu.items.container.groups.border.field.style.groove")},
                                                        {"value": "ridge", "text": $localization.getMessage("menu.items.container.groups.border.field.style.ridge")},
                                                        {"value": "inset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.inset")},
                                                        {"value": "outset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.outset")}
                                                    ]',
                                                    'item-value' => 'value',
                                                    'item-text' => 'text',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.border.left.style.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-group-child">
                                <div class="intec-editor-settings-group-child-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.child.right') }}
                                </div>
                                <div class="intec-editor-settings-group-child-content">
                                    <div class="intec-editor-settings-fields">
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.width') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                    <div class="intec-editor-grid-item">
                                                        <?= Html::tag('v-text-field', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-model' => '$root.selection.properties.border.right.width.value',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                    <div class="intec-editor-grid-item-3">
                                                        <?= Html::tag('v-select', null, [
                                                            'class' => 'v-input-theme-settings',
                                                            'v-bind:items' => '$root.selection.getStyleProperty(\'border\').width.measures',
                                                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                            'v-model' => '$root.selection.properties.border.right.width.measure',
                                                            'hide-details' => 'auto',
                                                            'solo' => true,
                                                            'flat' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.color') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.properties.border.right.color.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="intec-editor-settings-field">
                                            <div class="intec-editor-settings-field-name">
                                                {{ $localization.getMessage('menu.items.container.groups.border.field.style') }}
                                            </div>
                                            <div class="intec-editor-settings-field-content">
                                                <?= Html::tag('v-select', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:items' => '[
                                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.border.field.style.null")},
                                                        {"value": "solid", "text": $localization.getMessage("menu.items.container.groups.border.field.style.solid")},
                                                        {"value": "dotted", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dotted")},
                                                        {"value": "dashed", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dashed")},
                                                        {"value": "double", "text": $localization.getMessage("menu.items.container.groups.border.field.style.double")},
                                                        {"value": "groove", "text": $localization.getMessage("menu.items.container.groups.border.field.style.groove")},
                                                        {"value": "ridge", "text": $localization.getMessage("menu.items.container.groups.border.field.style.ridge")},
                                                        {"value": "inset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.inset")},
                                                        {"value": "outset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.outset")}
                                                    ]',
                                                    'item-value' => 'value',
                                                    'item-text' => 'text',
                                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                    'v-model' => '$root.selection.properties.border.right.style.value',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="border.color" style="width: 372px;">
            <template v-slot>
                <?= Html::tag('v-color-picker', null, [
                    'class' => '',
                    'v-model' => '$root.selection.propertyBorderColor',
                    'v-bind:value' => '$root.selection.propertyBorderColor',
                    'mode' => 'hexa',
                    'hide-mode-switch' => true,
                    'flat' => true,
                    'dark' => true,
                ]) ?>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="border.radius" style="width: 600px;">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.border.radius.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content">
                    <div class="intec-editor-settings-fields">
                        <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-a-v-center intec-editor-grid-i-h-16">
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.border.field.radius.top') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-slider', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:min' => '0',
                                                    'v-bind:max' => '100',
                                                    'v-bind:step' => '1',
                                                    'v-model' => '$root.selection.propertyBorderTopRadiusValue',
                                                    'hide-details' => 'auto'
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.propertyBorderTopRadiusValue',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.border.field.radius.right') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-slider', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:min' => '0',
                                                    'v-bind:max' => '100',
                                                    'v-bind:step' => '1',
                                                    'v-model' => '$root.selection.propertyBorderRightRadiusValue',
                                                    'hide-details' => 'auto'
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.propertyBorderRightRadiusValue',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.border.field.radius.left') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-slider', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:min' => '0',
                                                    'v-bind:max' => '100',
                                                    'v-bind:step' => '1',
                                                    'v-model' => '$root.selection.propertyBorderLeftRadiusValue',
                                                    'hide-details' => 'auto'
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.propertyBorderLeftRadiusValue',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-grid-item-2">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-name">
                                        {{ $localization.getMessage('menu.items.container.groups.border.field.radius.bottom') }}
                                    </div>
                                    <div class="intec-editor-settings-field-content">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                            <div class="intec-editor-grid-item">
                                                <?= Html::tag('v-slider', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-bind:min' => '0',
                                                    'v-bind:max' => '100',
                                                    'v-bind:step' => '1',
                                                    'v-model' => '$root.selection.propertyBorderBottomRadiusValue',
                                                    'hide-details' => 'auto'
                                                ]) ?>
                                            </div>
                                            <div class="intec-editor-grid-item-3">
                                                <?= Html::tag('v-text-field', null, [
                                                    'class' => 'v-input-theme-settings',
                                                    'v-model' => '$root.selection.propertyBorderBottomRadiusValue',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="overflow">
            <template v-slot>
                <div class="intec-editor-settings-group-name">
                    {{ $localization.getMessage('menu.items.container.groups.overflow.advanced') }}
                </div>
                <div class="intec-editor-settings-group-content">
                    <div class="intec-editor-settings-fields">
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.overflow.field.horizontal') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-settings',
                                    'v-bind:items' => '[
                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.overflow.value.null")},
                                        {"value": "visible", "text": $localization.getMessage("menu.items.container.groups.overflow.value.visible")},
                                        {"value": "hidden", "text": $localization.getMessage("menu.items.container.groups.overflow.value.hidden")},
                                        {"value": "scroll", "text": $localization.getMessage("menu.items.container.groups.overflow.value.scroll")},
                                        {"value": "auto", "text": $localization.getMessage("menu.items.container.groups.overflow.value.auto")},
                                        {"value": "inherit", "text": $localization.getMessage("menu.items.container.groups.overflow.value.inherit")}
                                    ]',
                                    'item-value' => 'value',
                                    'item-text' => 'text',
                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                    'v-model' => '$root.selection.properties.overflow.x.value',
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                        </div>
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                {{ $localization.getMessage('menu.items.container.groups.overflow.field.vertical') }}
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-settings',
                                    'v-bind:items' => '[
                                        {"value": null, "text": $localization.getMessage("menu.items.container.groups.overflow.value.null")},
                                        {"value": "visible", "text": $localization.getMessage("menu.items.container.groups.overflow.value.visible")},
                                        {"value": "hidden", "text": $localization.getMessage("menu.items.container.groups.overflow.value.hidden")},
                                        {"value": "scroll", "text": $localization.getMessage("menu.items.container.groups.overflow.value.scroll")},
                                        {"value": "auto", "text": $localization.getMessage("menu.items.container.groups.overflow.value.auto")},
                                        {"value": "inherit", "text": $localization.getMessage("menu.items.container.groups.overflow.value.inherit")}
                                    ]',
                                    'item-value' => 'value',
                                    'item-text' => 'text',
                                    'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                    'v-model' => '$root.selection.properties.overflow.y.value',
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </component>
        <component is="v-interface-menu-tab-popup" code="text.color" style="width: 372px;">
            <template v-slot>
                <?= Html::tag('v-color-picker', null, [
                    'class' => '',
                    'v-model' => '$root.selection.propertyTextColor',
                    'v-bind:value' => '$root.selection.propertyTextColor',
                    'mode' => 'hexa',
                    'hide-mode-switch' => true,
                    'flat' => true,
                    'dark' => true,
                ]) ?>
            </template>
        </component>
    </template>
    <template v-if="$root.hasSelection" v-slot:default="slot">
        <div class="intec-editor-settings">
            <div class="intec-editor-settings-groups">
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.container.groups.container') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.code') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-text-field', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.code',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode && $root.selection.isConvertable()">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.convert') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <component is="v-interface-dialogs-confirm" v-if="!$root.selection.hasArea()" v-on:confirm="$root.interface.dialogs.areaSelect.open(function (area) { $root.selection.convertToArea(area); })">
                                        <template v-slot:activator="slot">
                                            <button class="intec-editor-settings-field-button"  v-on="slot.on">
                                                {{ $localization.getMessage('menu.items.container.groups.container.field.convert.area') }}
                                            </button>
                                        </template>
                                        <template v-slot:description="slot">
                                            {{ $root.$localization.getMessage('menu.items.container.groups.container.field.convert.area.warning') }}
                                        </template>
                                    </component>
                                    <component is="v-interface-dialogs-confirm" v-if="!$root.selection.hasVariator()" v-on:confirm="$root.selection.convertToVariator()">
                                        <template v-slot:activator="slot">
                                            <button class="intec-editor-settings-field-button" v-on="slot.on">
                                                {{ $localization.getMessage('menu.items.container.groups.container.field.convert.variator') }}
                                            </button>
                                        </template>
                                        <template v-slot:description="slot">
                                            {{ $root.$localization.getMessage('menu.items.container.groups.container.field.convert.variator.warning') }}
                                        </template>
                                    </component>
                                    <component is="v-interface-dialogs-confirm" v-if="$root.selection.hasElement()" v-on:confirm="$root.selection.convertToSimple()">
                                        <template v-slot:activator="slot">
                                            <button class="intec-editor-settings-field-button" v-on="slot.on">
                                                {{ $localization.getMessage('menu.items.container.groups.container.field.convert.simple') }}
                                            </button>
                                        </template>
                                        <template v-slot:description="slot">
                                            {{ $root.$localization.getMessage('menu.items.container.groups.container.field.convert.simple.warning') }}
                                        </template>
                                    </component>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode && !$root.selection.hasElement()">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.type') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-bind:items' => '[
                                            {"value": "normal", "text": $localization.getMessage("menu.items.container.groups.container.field.type.normal")},
                                            {"value": "absolute", "text": $localization.getMessage("menu.items.container.groups.container.field.type.absolute")}
                                        ]',
                                        'item-value' => 'value',
                                        'item-text' => 'text',
                                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                        'v-model' => '$root.selection.type',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode && $root.selection.getType() !== 'absolute'">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.float') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-bind:items' => '[
                                            {"value": null, "text": $localization.getMessage("menu.items.container.groups.container.field.float.null")},
                                            {"value": "none", "text": $localization.getMessage("menu.items.container.groups.container.field.float.none")},
                                            {"value": "left", "text": $localization.getMessage("menu.items.container.groups.container.field.float.left")},
                                            {"value": "right", "text": $localization.getMessage("menu.items.container.groups.container.field.float.right")}
                                        ]',
                                        'item-value' => 'value',
                                        'item-text' => 'text',
                                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                        'v-model' => '$root.selection.properties.float',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.opacity') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-slider', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:min' => '0',
                                                'v-bind:max' => '100',
                                                'v-bind:step' => '1',
                                                'v-model' => '$root.selection.propertyOpacity',
                                                'hide-details' => 'auto'
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.propertyOpacity',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <template v-if="$root.template.settings.developmentMode && $root.selection.getType() === 'absolute'">
                                <div class="intec-editor-settings-field">
                                    <div class="intec-editor-settings-field-content">
                                        <?= Html::tag('v-switch', null, [
                                            'class' => 'v-input-theme-settings',
                                            'v-model' => '$root.selection.properties.grid.show',
                                            'dark' => true,
                                            'hide-details' => 'auto',
                                            'inset' => true,
                                            'v-bind:label' => '$localization.getMessage("menu.items.container.groups.container.field.grid.show")'
                                        ]) ?>
                                    </div>
                                </div>
                                <template v-if="$root.selection.properties.grid.show">
                                    <div class="intec-editor-settings-field">
                                        <div class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.container.groups.container.field.grid.type') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '[
                                                    {"value": "none", "text": $localization.getMessage("menu.items.container.groups.container.field.grid.type.none")},
                                                    {"value": "adaptive", "text": $localization.getMessage("menu.items.container.groups.container.field.grid.type.adaptive")},
                                                    {"value": "fixed", "text": $localization.getMessage("menu.items.container.groups.container.field.grid.type.fixed")}
                                                ]',
                                                'item-value' => 'value',
                                                'item-text' => 'text',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.grid.type',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="intec-editor-settings-field">
                                        <div v-if="$root.selection.properties.grid.type !== 'fixed'" class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.container.groups.container.field.grid.width.adaptive') }}
                                        </div>
                                        <div v-else class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.container.groups.container.field.grid.width.fixed') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.grid.width',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="intec-editor-settings-field">
                                        <div v-if="$root.selection.properties.grid.type !== 'fixed'" class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.container.groups.container.field.grid.height.adaptive') }}
                                        </div>
                                        <div v-else class="intec-editor-settings-field-name">
                                            {{ $localization.getMessage('menu.items.container.groups.container.field.grid.height.fixed') }}
                                        </div>
                                        <div class="intec-editor-settings-field-content">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.grid.height',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </template>
                            </template>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-switch', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.display',
                                        'dark' => true,
                                        'hide-details' => 'auto',
                                        'inset' => true,
                                        'v-bind:label' => '$localization.getMessage("menu.items.container.groups.container.field.display")'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field" v-if="$root.template.settings.developmentMode">
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-settings-field-button',
                                    'v-ripple' => true,
                                    'v-on:click' => 'interface.dialogs.conditions.open($root.selection.condition)'
                                ]) ?>
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.condition') }}
                                <?= Html::endTag('button') ?>
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-settings-field-button',
                                    'v-ripple' => true,
                                    'v-on:click' => 'interface.dialogs.containerScript.open($root.selection)'
                                ]) ?>
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.script') }}
                                <?= Html::endTag('button') ?>
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-settings-field-button',
                                    'v-ripple' => true,
                                    'v-on:click' => 'interface.dialogs.containerStructure.open($root.selection)'
                                ]) ?>
                                    {{ $localization.getMessage('menu.items.container.groups.container.field.structure') }}
                                <?= Html::endTag('button') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group" v-if="$root.selection.isInContainer() && $root.selection.parent.getType() === 'absolute'">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.container.groups.side') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.side.field.top') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.top.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'top\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.top.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.side.field.bottom') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.bottom.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'bottom\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.bottom.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.side.field.left') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.left.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'left\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.left.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.side.field.right') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.right.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'right\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.right.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.container.groups.attributes') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.attributes.field.id') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-text-field', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.properties.id',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.attributes.field.class') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-text-field', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.properties.class',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item">
                                {{ $localization.getMessage('menu.items.container.groups.size') }}
                            </div>
                            <div class="intec-editor-griditem-auto">
                                <template v-if="slot.component.getPopup('size')">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-menu-tab-popup-open',
                                        'v-bind:data-active' => 'slot.component.getPopup("size").isActive ? "true" : "false"',
                                        'v-ripple' => true,
                                        'v-on:click' => 'function (event) { slot.component.togglePopup("size", event.target) }'
                                    ]) ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" fill="white"/>
                                        </svg>
                                    <?= Html::endTag('button') ?>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.size.field.width') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.width.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'width\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.width.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.size.field.height') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.height.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'height\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.height.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item">
                                {{ $localization.getMessage('menu.items.container.groups.indent') }}
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <template v-if="slot.component.getPopup('indent')">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-menu-tab-popup-open',
                                        'v-bind:data-active' => 'slot.component.getPopup("indent").isActive ? "true" : "false"',
                                        'v-ripple' => true,
                                        'v-on:click' => 'function (event) { slot.component.togglePopup("indent", event.target) }'
                                    ]) ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" fill="white"/>
                                        </svg>
                                    <?= Html::endTag('button') ?>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-switch', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.properties.margin.isAuto',
                                        'dark' => true,
                                        'hide-details' => 'auto',
                                        'inset' => true,
                                        'v-bind:label' => '$localization.getMessage("menu.items.container.groups.indent.child.field.margin.auto")'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field" v-if="!$root.selection.properties.margin.isAuto">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.indent.child.margin') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.margin.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'margin\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.margin.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.indent.child.padding') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.padding.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'padding\').measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.padding.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.container.groups.background') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.background.field.color') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                                        <div class="intec-editor-grid-item-auto">
                                            <template v-if="slot.component.getPopup('background.color')">
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-on:click' => 'function (event) { slot.component.togglePopup("background.color", event.target) }',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.background.color,
                                                        "border-color": $root.selection.properties.background.color
                                                    }'
                                                ]) ?>
                                            </template>
                                            <template v-else>
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.background.color,
                                                        "border-color": $root.selection.properties.background.color
                                                    }'
                                                ]) ?>
                                            </template>
                                        </div>
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.background.color',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true,
                                                'placeholder' => '#000000'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                                        <div class="intec-editor-grid-item">
                                            {{ $localization.getMessage('menu.items.container.groups.background.field.image') }}
                                        </div>
                                        <div class="intec-editor-grid-auto">
                                            <template v-if="slot.component.getPopup('background')">
                                                <?= Html::beginTag('button', [
                                                    'class' => 'intec-editor-menu-tab-popup-open',
                                                    'v-bind:data-active' => 'slot.component.getPopup("background").isActive ? "true" : "false"',
                                                    'v-ripple' => true,
                                                    'v-on:click' => 'function (event) { slot.component.togglePopup("background", event.target) }'
                                                ]) ?>
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" />
                                                    </svg>
                                                <?= Html::endTag('button') ?>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-settings-background-preview">
                                        <template v-if="$root.selection.properties.background.image.url === null">
                                            <?= Html::beginTag('div', [
                                                'class' => 'intec-editor-settings-background-preview-container',
                                                'data-state' => 'empty',
                                                'v-ripple' => '{"class": "white--text"}',
                                                'v-on:click' => 'interface.dialogs.gallery.open($root.gallerySelectItemCallback)'
                                            ]) ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'intec-editor-settings-background-preview-content',
                                                        'intec-editor-grid' => [
                                                            '',
                                                            'nowrap',
                                                            'a-v-center'
                                                        ]
                                                    ]
                                                ]) ?>
                                                    <div class="intec-editor-grid-item">
                                                        <div class="intec-editor-settings-background-preview-content-part" data-type="icon">
                                                            <svg width="32" height="32" viewBox="0 0 32 32" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                <path d="M23.9993 19.9999V25.3332C23.9993 26.0666 23.3993 26.6666 22.666 26.6666H6.66602C5.93268 26.6666 5.33268 26.0666 5.33268 25.3332V9.33324C5.33268 8.59991 5.93268 7.99991 6.66602 7.99991H10.6927C11.426 7.99991 12.026 7.39991 12.026 6.66658C12.026 5.93324 11.426 5.33324 10.6927 5.33324H5.33268C3.86602 5.33324 2.66602 6.53324 2.66602 7.99991V26.6666C2.66602 28.1332 3.86602 29.3332 5.33268 29.3332H23.9993C25.466 29.3332 26.666 28.1332 26.666 26.6666V19.9999C26.666 19.2666 26.066 18.6666 25.3327 18.6666C24.5993 18.6666 23.9993 19.2666 23.9993 19.9999ZM20.666 23.9999H8.69268C8.13268 23.9999 7.82602 23.3599 8.17268 22.9199L10.4927 19.9466C10.5541 19.8679 10.6325 19.804 10.7219 19.7597C10.8114 19.7154 10.9097 19.6917 11.0095 19.6904C11.1093 19.6891 11.2081 19.7103 11.2987 19.7523C11.3893 19.7943 11.4692 19.8562 11.5327 19.9332L13.6127 22.4399L16.746 18.4132C17.0127 18.0666 17.546 18.0666 17.7993 18.4266L21.1993 22.9466C21.5327 23.3732 21.2127 23.9999 20.666 23.9999ZM25.7327 11.8532C26.3727 10.8266 26.7327 9.62658 26.6527 8.30658C26.4793 5.43991 24.1994 3.01324 21.3594 2.70658C20.5186 2.60876 19.6666 2.68996 18.8594 2.94483C18.0522 3.1997 17.3081 3.62247 16.676 4.18538C16.0438 4.74828 15.5379 5.43857 15.1915 6.2109C14.8451 6.98323 14.666 7.82012 14.666 8.66658C14.666 11.9866 17.346 14.6666 20.6527 14.6666C21.826 14.6666 22.9193 14.3199 23.8393 13.7332L27.0527 16.9466C27.5727 17.4666 28.426 17.4666 28.946 16.9466C29.466 16.4266 29.466 15.5732 28.946 15.0532L25.7327 11.8532ZM20.666 11.9999C19.782 11.9999 18.9341 11.6487 18.309 11.0236C17.6839 10.3985 17.3327 9.55063 17.3327 8.66658C17.3327 7.78252 17.6839 6.93468 18.309 6.30955C18.9341 5.68443 19.782 5.33324 20.666 5.33324C21.5501 5.33324 22.3979 5.68443 23.023 6.30955C23.6482 6.93468 23.9993 7.78252 23.9993 8.66658C23.9993 9.55063 23.6482 10.3985 23.023 11.0236C22.3979 11.6487 21.5501 11.9999 20.666 11.9999Z" />
                                                            </svg>
                                                        </div>
                                                        <div class="intec-editor-settings-background-preview-content-part" data-type="text">
                                                            {{ $localization.getMessage('menu.items.container.groups.background.field.image.preview.empty') }}
                                                        </div>
                                                    </div>
                                                <?= Html::endTag('div') ?>
                                            <?= Html::endTag('div') ?>
                                        </template>
                                        <template v-else>
                                            <div class="intec-editor-settings-background-preview-container" data-state="selected">
                                                <div class="intec-editor-settings-background-preview-content">
                                                    <?= Html::tag('div', null, [
                                                        'class' => 'intec-editor-settings-background-preview-picture',
                                                        'v-bind:style' => '{
                                                            "background-image": "url(\'" + $root.backgroundImageUrl + "\')"
                                                        }'
                                                    ]) ?>
                                                    <div class="intec-editor-settings-background-preview-overlay intec-editor-grid intec-editor-grid-wrap intec-editor-grid-a-v-center">
                                                        <div class="intec-editor-grid-item-1">
                                                            <div class="intec-editor-settings-background-preview-content-part">
                                                                <?= Html::beginTag('div', [
                                                                    'class' => 'intec-editor-settings-background-preview-button',
                                                                    'data-view' => 'default',
                                                                    'v-on:click' => 'interface.dialogs.gallery.open($root.gallerySelectItemCallback)'
                                                                ]) ?>
                                                                    {{ $localization.getMessage('menu.items.container.groups.background.field.image.preview.change') }}
                                                                <?= Html::endTag('div') ?>
                                                            </div>
                                                            <div class="intec-editor-settings-background-preview-content-part">
                                                                <?= Html::beginTag('div', [
                                                                    'class' => 'intec-editor-settings-background-preview-button',
                                                                    'data-view' => 'minimal',
                                                                    'v-on:click' => '$root.propertiesClearBackgroundImage'
                                                                ]) ?>
                                                                    {{ $localization.getMessage('menu.items.container.groups.background.field.image.preview.clear') }}
                                                                <?= Html::endTag('div') ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-settings-field-button',
                                        'v-ripple' => true,
                                        'v-on:click' => 'interface.dialogs.gallery.open($root.gallerySelectItemCallback)'
                                    ]) ?>
                                        {{ $localization.getMessage('menu.items.container.groups.background.field.image.button.gallery') }}
                                    <?= Html::endTag('button') ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.background.field.image.url') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-text-field', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.properties.background.image.url',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true,
                                        'placeholder' => 'https://link_name.com'
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item">
                                {{ $localization.getMessage('menu.items.container.groups.border') }}
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <template v-if="slot.component.getPopup('border')">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-menu-tab-popup-open',
                                        'v-bind:data-active' => 'slot.component.getPopup("border").isActive ? "true" : "false"',
                                        'v-ripple' => true,
                                        'v-on:click' => 'function (event) { slot.component.togglePopup("border", event.target) }'
                                    ]) ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" fill="white"/>
                                        </svg>
                                    <?= Html::endTag('button') ?>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.field.width') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.border.width.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'border\').width.measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.border.width.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.field.color') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                                        <div class="intec-editor-grid-item-auto">
                                            <template v-if="slot.component.getPopup('border.color')">
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-on:click' => 'function (event) { slot.component.togglePopup("border.color", event.target) }',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.border.color,
                                                        "border-color": $root.selection.properties.border.color
                                                    }'
                                                ]) ?>
                                            </template>
                                            <template v-else>
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.border.color,
                                                        "border-color": $root.selection.properties.border.color
                                                    }'
                                                ]) ?>
                                            </template>
                                        </div>
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.border.color',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true,
                                                'placeholder' => '#000000'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.border.field.style') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-bind:items' => '[
                                            {"value": null, "text": $localization.getMessage("menu.items.container.groups.border.field.style.null")},
                                            {"value": "solid", "text": $localization.getMessage("menu.items.container.groups.border.field.style.solid")},
                                            {"value": "dotted", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dotted")},
                                            {"value": "dashed", "text": $localization.getMessage("menu.items.container.groups.border.field.style.dashed")},
                                            {"value": "double", "text": $localization.getMessage("menu.items.container.groups.border.field.style.double")},
                                            {"value": "groove", "text": $localization.getMessage("menu.items.container.groups.border.field.style.groove")},
                                            {"value": "ridge", "text": $localization.getMessage("menu.items.container.groups.border.field.style.ridge")},
                                            {"value": "inset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.inset")},
                                            {"value": "outset", "text": $localization.getMessage("menu.items.container.groups.border.field.style.outset")}
                                        ]',
                                        'item-value' => 'value',
                                        'item-text' => 'text',
                                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                        'v-model' => '$root.selection.properties.border.style',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                                        <div class="intec-editor-grid-item">
                                            {{ $localization.getMessage('menu.items.container.groups.border.field.radius') }}
                                        </div>
                                        <div class="intec-editor-grid-item-auto">
                                            <template v-if="slot.component.getPopup('border.radius')">
                                                <?= Html::beginTag('button', [
                                                    'class' => 'intec-editor-menu-tab-popup-open',
                                                    'v-bind:data-active' => 'slot.component.getPopup("border.radius").isActive ? "true" : "false"',
                                                    'v-ripple' => true,
                                                    'v-on:click' => 'function (event) { slot.component.togglePopup("border.radius", event.target) }'
                                                ]) ?>
                                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" fill="white"/>
                                                    </svg>
                                                <?= Html::endTag('button') ?>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-slider', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:min' => '0',
                                                'v-bind:max' => '100',
                                                'v-bind:step' => '1',
                                                'v-model' => '$root.selection.propertyBorderRadiusValue',
                                                'hide-details' => 'auto'
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.propertyBorderRadiusValue',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group" v-if="$root.template.settings.developmentMode">
                    <div class="intec-editor-settings-group-name">
                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item">
                                {{ $localization.getMessage('menu.items.container.groups.overflow') }}
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <template v-if="slot.component.getPopup('overflow')">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-menu-tab-popup-open',
                                        'v-bind:data-active' => 'slot.component.getPopup("overflow").isActive ? "true" : "false"',
                                        'v-ripple' => true,
                                        'v-on:click' => 'function (event) { slot.component.togglePopup("overflow", event.target) }'
                                    ]) ?>
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M7.00065 10.3334C6.08398 10.3334 5.33398 11.0834 5.33398 12C5.33398 12.9167 6.08398 13.6667 7.00065 13.6667C7.91732 13.6667 8.66732 12.9167 8.66732 12C8.66732 11.0834 7.91732 10.3334 7.00065 10.3334ZM17.0007 10.3334C16.084 10.3334 15.334 11.0834 15.334 12C15.334 12.9167 16.084 13.6667 17.0007 13.6667C17.9173 13.6667 18.6673 12.9167 18.6673 12C18.6673 11.0834 17.9173 10.3334 17.0007 10.3334ZM12.0007 10.3334C11.084 10.3334 10.334 11.0834 10.334 12C10.334 12.9167 11.084 13.6667 12.0007 13.6667C12.9173 13.6667 13.6673 12.9167 13.6673 12C13.6673 11.0834 12.9173 10.3334 12.0007 10.3334Z" fill="white"/>
                                        </svg>
                                    <?= Html::endTag('button') ?>
                                </template>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.overflow.field.overall') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-bind:items' => '[
                                            {"value": null, "text": $localization.getMessage("menu.items.container.groups.overflow.value.null")},
                                            {"value": "visible", "text": $localization.getMessage("menu.items.container.groups.overflow.value.visible")},
                                            {"value": "hidden", "text": $localization.getMessage("menu.items.container.groups.overflow.value.hidden")},
                                            {"value": "scroll", "text": $localization.getMessage("menu.items.container.groups.overflow.value.scroll")},
                                            {"value": "auto", "text": $localization.getMessage("menu.items.container.groups.overflow.value.auto")},
                                            {"value": "inherit", "text": $localization.getMessage("menu.items.container.groups.overflow.value.inherit")}
                                        ]',
                                        'item-value' => 'value',
                                        'item-text' => 'text',
                                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                        'v-model' => '$root.selection.properties.overflow.value',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-group">
                    <div class="intec-editor-settings-group-name">
                        {{ $localization.getMessage('menu.items.container.groups.text') }}
                    </div>
                    <div class="intec-editor-settings-group-content">
                        <div class="intec-editor-settings-fields">
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.text.field.font') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-bind:items' => '(function () {
                                            var result = [{
                                                "text": $localization.getMessage("menu.items.container.groups.text.field.font.value.null"),
                                                "value": null
                                            }];
                                            
                                            $root.fonts.forEach(function (font) {
                                                result.push({
                                                    "text": font.name,
                                                    "value": font.family
                                                });
                                            });
                                            
                                            return result;
                                        })()',
                                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                        'v-bind:label' => '$localization.getMessage("menu.items.container.groups.text.field.font.value.null")',
                                        'v-model' => '$root.selection.properties.text.font',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.text.field.size') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.text.size.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'text\').size.measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.text.size.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.text.field.color') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                                        <div class="intec-editor-grid-item-auto">
                                            <template v-if="slot.component.getPopup('text.color')">
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-on:click' => 'function (event) { slot.component.togglePopup("text.color", event.target) }',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.text.color,
                                                        "border-color": $root.selection.properties.text.color
                                                    }'
                                                ]) ?>
                                            </template>
                                            <template v-else>
                                                <?= Html::tag('button', null, [
                                                    'class' => 'intec-editor-settings-background-indicator',
                                                    'v-bind:style' => '{
                                                        "background-color": $root.selection.properties.text.color,
                                                        "border-color": $root.selection.properties.text.color
                                                    }'
                                                ]) ?>
                                            </template>
                                        </div>
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.text.color',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true,
                                                'placeholder' => '#000000'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-content">
                                    <?= Html::tag('v-switch', null, [
                                        'class' => 'v-input-theme-settings',
                                        'v-model' => '$root.selection.properties.text.uppercase',
                                        'dark' => true,
                                        'hide-details' => 'auto',
                                        'inset' => true,
                                        'v-bind:label' => '$localization.getMessage("menu.items.container.groups.text.field.uppercase")'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.text.field.letterSpacing') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.text.letterSpacing.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'text\').letterSpacing.measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.text.letterSpacing.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="intec-editor-settings-field">
                                <div class="intec-editor-settings-field-name">
                                    {{ $localization.getMessage('menu.items.container.groups.text.field.lineHeight') }}
                                </div>
                                <div class="intec-editor-settings-field-content">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                        <div class="intec-editor-grid-item">
                                            <?= Html::tag('v-text-field', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-model' => '$root.selection.properties.text.lineHeight.value',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                        <div class="intec-editor-grid-item-3">
                                            <?= Html::tag('v-select', null, [
                                                'class' => 'v-input-theme-settings',
                                                'v-bind:items' => '$root.selection.getStyleProperty(\'text\').lineHeight.measures',
                                                'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                                                'v-model' => '$root.selection.properties.text.lineHeight.measure',
                                                'hide-details' => 'auto',
                                                'solo' => true,
                                                'flat' => true
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</component>