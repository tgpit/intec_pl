<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<component is="v-interface-menu-tab" code="widgets" v-bind:name="$localization.getMessage('menu.items.widgets.name')">
    <template v-slot:icon>
        <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M4 6H2V20C2 21.1 2.9 22 4 22H18V20H4V6ZM20 2H8C6.9 2 6 2.9 6 4V16C6 17.1 6.9 18 8 18H20C21.1 18 22 17.1 22 16V4C22 2.9 21.1 2 20 2ZM19 11H15V15H13V11H9V9H13V5H15V9H19V11Z" stroke="none"/>
        </svg>
    </template>
    <template v-slot:popups="slot">
        <?= Html::beginTag('component', [
            'is' => 'v-interface-menu-tab-panel',
            'v-bind:active' => 'presetsGroup !== null && isPresetsGroupDisplay(presetsGroup)'
        ]) ?>
            <template v-slot>
                <template v-for="preset in sortedPresets">
                    <div class="intec-editor-preset" v-if="preset.group === presetsGroup && isPresetDisplay(preset)" v-bind:data-type="preset.type">
                        <div class="intec-editor-preset-picture" v-on:click="slot.component.$emit('selected', preset)" v-bind:style="{
                            'background-image': preset.picture ? 'url(\'' + preset.picture + '\')' : null
                        }">
                            <div class="intec-editor-preset-type">
                                {{ $root.$localization.getMessage('widgets.presets.type.' + preset.type) }}
                            </div>
                        </div>
                        <div class="intec-editor-preset-name" v-on:click="slot.component.$emit('selected', preset)">
                            {{ preset.name }}
                        </div>
                    </div>
                </template>
            </template>
        <?= Html::endTag('component') ?>
    </template>
    <template v-slot:default="slot">
        <div class="intec-editor-presets">
            <div class="intec-editor-presets-search">
                <?= Html::input('text', null, null, [
                    'class' => 'intec-editor-presets-search-input',
                    'v-bind:placeholder' => '$root.$localization.getMessage("menu.items.widgets.search")',
                    'v-model' => 'presetsFilter'
                ]) ?>
                <div class="intec-editor-presets-search-icon">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14H14.71L14.43 13.73C15.4439 12.554 16.0011 11.0527 16 9.5C16 8.21442 15.6188 6.95772 14.9046 5.8888C14.1903 4.81988 13.1752 3.98676 11.9874 3.49479C10.7997 3.00282 9.49279 2.87409 8.23192 3.1249C6.97104 3.3757 5.81285 3.99477 4.90381 4.90381C3.99477 5.81285 3.3757 6.97104 3.1249 8.23192C2.87409 9.49279 3.00282 10.7997 3.49479 11.9874C3.98676 13.1752 4.81988 14.1903 5.8888 14.9046C6.95772 15.6188 8.21442 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" />
                    </svg>
                </div>
            </div>
            <div class="intec-editor-presets-list">
                <?= Html::beginTag('div', [
                    'class' => 'intec-editor-presets-item',
                    'v-for' => 'group in sortedPresetsGroups',
                    'v-if' => 'isPresetsGroupDisplay(group)',
                    'v-bind:data-active' => 'isPresetsGroupSelected(group) ? "true" : "false"',
                    'v-on:click' => 'interfaceMenuTabsWidgetsGroupToggle(group)'
                ]) ?>
                    <div class="intec-editor-presets-item-content">
                        {{ group.name }}
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </template>
</component>