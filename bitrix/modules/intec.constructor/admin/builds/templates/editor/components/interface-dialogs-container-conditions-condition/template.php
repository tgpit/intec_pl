<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<?= Html::beginTag('v-expansion-panels', [
    'class' => [
        'intec-editor-dialog-container-conditions-group',
        'v-expansion-panels-theme-dialog-default'
    ],
    'v-if' => 'isGroup()',
    'flat' => true,
    'multiple' => true,
    'accordion' => true,
    'v-bind:data-inner' => 'level > 1 ? "true" : "false"'
]) ?>
    <v-expansion-panel class="intec-editor-dialog-container-conditions-group-panel">
            <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-dialog-container-conditions-group-header" v-bind:style="{
                    'margin-left': '-' + (24 * level) + 'px',
                    'padding-left': (24 * level) + 'px',
                }">
                <span class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                    <v-expansion-panel-header hide-actions>
                        <div class="intec-editor-grid intec-editor-grid-i-h-4 intec-editor-grid-a-v-center">
                            <div class="intec-editor-grid-item-auto">
                                <svg class="intec-editor-dialog-container-conditions-group-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M10 17L15 12L10 7L10 17Z" fill="#929BAA"/>
                                </svg>
                            </div>
                            <div class="intec-editor-grid-item">
                                <span class="intec-editor-dialog-container-conditions-group-name">
                                    {{ root.getGroupName(item) }}
                                </span>
                            </div>
                        </div>
                    </v-expansion-panel-header>
                </span>
                <span class="intec-editor-grid-item-auto intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                        <span class="intec-editor-grid-item-auto">
                            <span class="intec-editor-dialog-container-conditions-property-title">
                                {{ $root.$localization.getMessage('dialogs.container.conditions.property.result.title') }}
                            </span>
                        </span>
                        <span class="intec-editor-grid-item">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-dialog-default',
                                'is' => 'v-select',
                                'v-model' => 'item.operator',
                                'v-bind:items' => 'operators',
                                'item-value' => 'value',
                                'item-text' => 'text',
                                'hide-details' => 'auto',
                                'solo' => true,
                                'flat' => true
                            ]) ?>
                        </span>
                    </span>
                </span>
                <span class="intec-editor-grid-item-auto intec-editor-dialog-container-conditions-item-part">
                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-10">
                        <span class="intec-editor-grid-item-auto">
                            <span class="intec-editor-dialog-container-conditions-property-title">
                                {{ $root.$localization.getMessage('dialogs.container.conditions.property.result.title') }}
                            </span>
                        </span>
                        <span class="intec-editor-grid-item">
                            <?= Html::tag('v-switch', null, [
                                'class' => 'v-input-theme-dialog-default',
                                'v-model' => 'item.result',
                                'v-bind:label' => '$root.$localization.getMessage("dialogs.container.conditions.property.result.caption")',
                                'hide-details' => 'auto',
                                'inset' => true
                            ]) ?>
                        </span>
                    </span>
                </span>
                <span class="intec-editor-grid-item-auto intec-editor-dialog-container-conditions-item-part" data-condition-part="action">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" v-on:click="remove">
                        <path d="M7.75 17C7.75 17.825 8.425 18.5 9.25 18.5H15.25C16.075 18.5 16.75 17.825 16.75 17V8H7.75V17ZM17.5 5.75H14.875L14.125 5H10.375L9.625 5.75H7V7.25H17.5V5.75Z" fill="#929BAA"/>
                    </svg>
                </span>
            </span>
        <v-expansion-panel-content v-if="item.conditions.length > 0">
            <?= Html::tag('component', null, [
                'v-for' => 'condition in item.conditions',
                'is' => 'v-interface-dialogs-container-conditions-condition',
                'v-bind:item' => 'condition',
                'v-bind:key' => 'condition.uid'
            ]) ?>
        </v-expansion-panel-content>
    </v-expansion-panel>
<?= Html::endTag('v-expansion-panels') ?>
<?= Html::beginTag('div', [
    'class' => 'intec-editor-dialog-container-conditions-item',
    'v-else' => 'isCondition()',
    'v-bind:data-inner' => 'level > 1 ? "true" : "false"'
]) ?>
    <div class="intec-editor-grid intec-editor-grid-a-v-center">
        <template v-if="item.type === 'path'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.path') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'match'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.match') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.match.type') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-select',
                    'v-model' => 'item.match',
                    'v-bind:items' => 'matches',
                    'item-value' => 'value',
                    'item-text' => 'text',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'parameter.get'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.parameter.get') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.key',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.value') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'parameter.page'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.parameter.page') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.key',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.condition') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-select',
                    'v-model' => 'item.logic',
                    'v-bind:items' => 'logics',
                    'item-value' => 'value',
                    'item-text' => 'text',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.value') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'parameter.template'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.parameter.template') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.key',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="default">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.condition') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-select',
                    'v-model' => 'item.logic',
                    'v-bind:items' => 'logics',
                    'item-value' => 'value',
                    'item-text' => 'text',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.value') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'expression'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.expression') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-text-field',
                    'v-model' => 'item.value',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <template v-else-if="item.type === 'site'">
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-item-part" data-condition-part="changeable">
                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                    {{ $root.$localization.getMessage('dialogs.container.conditions.groups.site') }}
                </div>
                <?= Html::tag('component', null, [
                    'class' => 'v-input-theme-dialog-default',
                    'is' => 'v-select',
                    'v-model' => 'item.value',
                    'v-bind:items' => 'sites',
                    'item-value' => 'value',
                    'item-text' => 'text',
                    'hide-details' => 'auto',
                    'solo' => true,
                    'flat' => true
                ]) ?>
            </div>
        </template>
        <div class="intec-editor-grid-item-auto intec-editor-dialog-container-conditions-item-part" data-condition-type="no-title">
            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-10">
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-container-conditions-property-title">
                        {{ $root.$localization.getMessage('dialogs.container.conditions.property.result.title') }}
                    </div>
                </div>
                <div class="intec-editor-grid-item">
                    <?= Html::tag('v-switch', null, [
                        'class' => 'v-input-theme-dialog-default',
                        'v-model' => 'item.result',
                        'v-bind:label' => '$root.$localization.getMessage("dialogs.container.conditions.property.result.caption")',
                        'hide-details' => 'auto',
                        'inset' => true
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="intec-editor-grid-item-auto intec-editor-dialog-container-conditions-item-part" data-condition-part="action" data-condition-type="no-title">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" v-on:click="remove">
                <path d="M19 6.41L17.59 5L12 10.59L6.41 5L5 6.41L10.59 12L5 17.59L6.41 19L12 13.41L17.59 19L19 17.59L13.41 12L19 6.41Z" fill="#929BAA"/>
            </svg>
        </div>
    </div>
<?= Html::endTag('div') ?>