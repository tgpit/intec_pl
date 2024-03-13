<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$condition = $this->getComponent('interface-dialogs-container-conditions-condition');

?>
<?= Html::beginTag('component', [
    'is' => 'v-dialog',
    'content-class' => 'intec-editor-dialog intec-editor-dialog-container-conditions intec-editor-dialog-theme-default',
    'persistent' => true,
    'max-width' => '1434',
    'v-model' => 'display',
    'v-bind:retain-focus' => 'false',
]) ?>
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-controls">
            <div class="intec-editor-grid intec-editor-grid-i-h-12">
                <div class="intec-editor-grid-item"></div>
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="intec-editor-dialog-content">
            <?= Html::beginTag('div', [
                'class' => [
                    'intec-editor-dialog-content-wrapper',
                    'intec-editor-grid' => [
                        '',
                        'nowrap',
                        'o-vertical'
                    ]
                ],
                'v-if' => 'condition'
            ]) ?>
            <div class="intec-editor-grid-item-auto">
                <div class="intec-editor-dialog-content-part">
                    <div class="intec-editor-dialog-container-conditions-header">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-editor-grid' => [
                                    '',
                                    'wrap',
                                    'a-v-center',
                                    'i-16'
                                ]
                            ],
                        ]) ?>
                        <div class="intec-editor-grid-item">
                            <div class="intec-editor-dialog-title">
                                {{ $root.$localization.getMessage('dialogs.container.conditions.title') }}
                            </div>
                        </div>
                        <div class="intec-editor-grid-item-auto">
                            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-6">
                                <div class="intec-editor-grid-item-auto">
                                    <div class="intec-editor-dialog-container-conditions-property-title">
                                        {{ $root.$localization.getMessage('dialogs.container.conditions.property.logic') }}
                                    </div>
                                </div>
                                <div class="intec-editor-grid-item">
                                    <?= Html::tag('v-select', null, [
                                        'class' => 'v-input-theme-dialog-default',
                                        'v-model' => 'condition.operator',
                                        'v-bind:items' => 'operators',
                                        'item-value' => 'value',
                                        'item-text' => 'text',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <div class="intec-editor-grid-item-auto">
                            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-10">
                                <div class="intec-editor-grid-item-auto">
                                    <div class="intec-editor-dialog-container-conditions-property-title">
                                        {{ $root.$localization.getMessage('dialogs.container.conditions.property.result.title') }}
                                    </div>
                                </div>
                                <div class="intec-editor-grid-item">
                                    <?= Html::tag('v-switch', null, [
                                        'class' => 'v-input-theme-dialog-default',
                                        'v-model' => 'condition.result',
                                        'v-bind:label' => '$root.$localization.getMessage("dialogs.container.conditions.property.result.caption")',
                                        'hide-details' => 'auto',
                                        'inset' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                        <?= Html::endTag('div') ?>
                    </div>
                    <div class="intec-editor-dialog-container-conditions-select">
                        <div class="intec-editor-grid intec-editor-grid-a-v-end intec-editor-grid-i-8">
                            <div class="intec-editor-grid-item-auto">
                                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                                    {{ $root.$localization.getMessage('dialogs.container.conditions.property.group.title') }}
                                </div>
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-dialog-default',
                                    'v-model' => 'selectedGroup',
                                    'v-bind:items' => 'groups',
                                    'v-bind:item-text' => 'function (item) { return getGroupName(item); }',
                                    'return-object' => true,
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <div class="intec-editor-dialog-container-conditions-property-title" data-position="above">
                                    {{ $root.$localization.getMessage('dialogs.container.conditions.property.condition.title') }}
                                </div>
                                <?= Html::tag('v-select', null, [
                                    'class' => 'v-input-theme-dialog-default',
                                    'v-model' => 'selectedType',
                                    'v-bind:items' => 'types',
                                    'item-value' => 'value',
                                    'item-text' => 'text',
                                    'hide-details' => 'auto',
                                    'solo' => true,
                                    'flat' => true
                                ]) ?>
                            </div>
                            <div class="intec-editor-grid-item-auto">
                                <?= Html::beginTag('button', [
                                    'class' => 'intec-editor-button',
                                    'v-on:click' => 'add',
                                    'v-ripple' => true
                                ]) ?>
                                {{ $root.$localization.getMessage('dialogs.container.conditions.select.add') }}
                                <?= Html::endTag('button') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intec-editor-grid-item intec-editor-dialog-container-conditions-items" v-if="condition.conditions.length > 0">
                <div style="height: 100%;">
                    <component ref="scrollbar" is="vue-scroll" v-on:handle-scroll="handleScroll" v-bind:ops="scrollbarSettings">
                        <div class="intec-editor-dialog-content-part">
                            <?= $condition->apply([
                                'v-for' => 'item in condition.conditions',
                                'v-bind:item' => 'item',
                                'v-bind:key' => 'item.uid'
                            ]) ?>
                        </div>
                    </component>
                </div>
            </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('component') ?>
