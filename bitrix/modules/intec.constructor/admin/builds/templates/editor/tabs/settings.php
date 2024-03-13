<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\Application;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Application $application
 * @var Component[] $components
 */

?>
<component is="v-interface-menu-tab" code="settings" v-bind:name="$localization.getMessage('menu.items.settings.name')">
    <template v-slot:icon>
        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M19.43 12.98C19.47 12.66 19.5 12.34 19.5 12C19.5 11.66 19.47 11.34 19.43 11.02L21.54 9.37005C21.73 9.22005 21.78 8.95005 21.66 8.73005L19.66 5.27005C19.54 5.05005 19.27 4.97005 19.05 5.05005L16.56 6.05005C16.04 5.65005 15.48 5.32005 14.87 5.07005L14.49 2.42005C14.4735 2.30239 14.4146 2.19481 14.3244 2.11748C14.2342 2.04016 14.1188 1.9984 14 2.00005H10C9.75002 2.00005 9.54002 2.18005 9.51002 2.42005L9.13002 5.07005C8.52002 5.32005 7.96002 5.66005 7.44002 6.05005L4.95002 5.05005C4.72002 4.96005 4.46002 5.05005 4.34002 5.27005L2.34002 8.73005C2.21002 8.95005 2.27002 9.22005 2.46002 9.37005L4.57002 11.02C4.53002 11.34 4.50002 11.67 4.50002 12C4.50002 12.33 4.53002 12.66 4.57002 12.98L2.46002 14.63C2.27002 14.78 2.22002 15.05 2.34002 15.27L4.34002 18.73C4.46002 18.95 4.73002 19.03 4.95002 18.95L7.44002 17.95C7.96002 18.35 8.52002 18.68 9.13002 18.93L9.51002 21.58C9.54002 21.82 9.75002 22 10 22H14C14.25 22 14.46 21.82 14.49 21.58L14.87 18.93C15.48 18.68 16.04 18.34 16.56 17.95L19.05 18.95C19.28 19.04 19.54 18.95 19.66 18.73L21.66 15.27C21.78 15.05 21.73 14.78 21.54 14.63L19.43 12.98ZM12 15.5C10.07 15.5 8.50002 13.93 8.50002 12C8.50002 10.07 10.07 8.50005 12 8.50005C13.93 8.50005 15.5 10.07 15.5 12C15.5 13.93 13.93 15.5 12 15.5Z" stroke="none" />
        </svg>
    </template>
    <template v-slot:default>
        <div class="intec-editor-settings">
            <div class="intec-editor-settings-fields intec-editor-grid intec-editor-grid-wrap intec-editor-grid-a-v-center">
                <div class="intec-editor-settings-field intec-editor-grid-item-1">
                    <div class="intec-editor-settings-field-name">
                        {{ $localization.getMessage('menu.items.settings.fields.site.name') }}
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('v-select', null, [
                            'class' => 'v-input-theme-settings',
                            'v-model' => 'siteId',
                            'v-bind:items' => 'sites',
                            'v-bind:item-text' => 'function (item) { return "[" + item.id + "] " + item.name }',
                            'v-bind:item-value' => 'function (item) { return item.id }',
                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                            'hide-details' => 'auto',
                            'dark' => true,
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field intec-editor-grid-item-1">
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('v-switch', null, [
                            'class' => 'v-input-theme-settings',
                            'v-model' => 'template.settings.containersHiddenShow',
                            'v-bind:label' => '$localization.getMessage("menu.items.settings.fields.containersHiddenShow.name")',
                            'hide-details' => 'auto',
                            'dark' => true,
                            'inset' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field intec-editor-grid-item-1">
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('v-switch', null, [
                            'class' => 'v-input-theme-settings',
                            'v-model' => 'template.settings.containersStructureShow',
                            'v-bind:label' => '$localization.getMessage("menu.items.settings.fields.containersStructureShow.name")',
                            'hide-details' => 'auto',
                            'dark' => true,
                            'inset' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field intec-editor-grid-item-1">
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('v-switch', null, [
                            'class' => 'v-input-theme-settings',
                            'v-model' => 'template.settings.developmentMode',
                            'v-bind:label' => '$localization.getMessage("menu.items.settings.fields.developmentMode.name")',
                            'hide-details' => 'auto',
                            'dark' => true,
                            'inset' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field intec-editor-grid-item-1" v-if="$root.hasLayouts">
                    <div class="intec-editor-settings-field-name">
                        {{ $localization.getMessage('menu.items.settings.fields.layout') }}
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-h-6 intec-editor-grid-i-v-8">
                            <div class="intec-editor-grid-item-2" v-for="layout in layouts">
                                <?= $components['interface-dialogs-confirm']->begin([
                                    'v-on:confirm' => 'interfaceMenuTabsSettingsLayoutSet(layout)'
                                ]) ?>
                                    <template v-slot:activator="confirmSlot">
                                        <component is="v-tooltip" bottom>
                                            <template v-slot:activator="tooltipSlot">
                                                <div class="intec-editor-settings-layout" v-bind:data-active="isCurrentLayout(layout)" v-on="tooltipSlot.on" v-on:click="confirmSlot.on.click" v-bind:style="{
                                                    'background-image': layout.picture ? 'url(\'' + layout.picture + '\')' : null
                                                }"></div>
                                            </template>
                                            <span>
                                                {{ layout.name }}
                                            </span>
                                        </component>
                                    </template>
                                    <template v-slot:title>
                                        {{ $localization.getMessage('menu.items.settings.fields.layout.confirm.title') }}
                                    </template>
                                    <template v-slot:description>
                                        {{ $localization.getMessage('menu.items.settings.fields.layout.confirm.description', {
                                            'layout': layout.name
                                        }) }}
                                    </template>
                                <?= $components['interface-dialogs-confirm']->end() ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</component>