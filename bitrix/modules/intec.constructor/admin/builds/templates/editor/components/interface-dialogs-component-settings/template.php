<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<component is="v-dialog" v-bind:retain-focus="false" persistent max-width="1700" content-class="intec-editor-dialog intec-editor-dialog-component-settings intec-editor-dialog-theme-default" v-model="display">
    <div class="intec-editor-dialog-panel">
        <div class="intec-editor-dialog-panel-wrapper intec-editor-grid intec-editor-grid-o-vertical">
            <div class="intec-editor-dialog-search intec-editor-grid-item-auto">
                <div class="intec-editor-dialog-search-wrapper">
                    <?= Html::textInput(null, null, [
                        'v-model' => 'filter',
                        'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.componentSettings.search.placeholder")'
                    ]) ?>
                    <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15.5 14H14.71L14.43 13.73C15.4439 12.554 16.0011 11.0527 16 9.5C16 8.21442 15.6188 6.95772 14.9046 5.8888C14.1903 4.81988 13.1752 3.98676 11.9874 3.49479C10.7997 3.00282 9.49279 2.87409 8.23192 3.1249C6.97104 3.3757 5.81285 3.99477 4.90381 4.90381C3.99477 5.81285 3.3757 6.97104 3.1249 8.23192C2.87409 9.49279 3.00282 10.7997 3.49479 11.9874C3.98676 13.1752 4.81988 14.1903 5.8888 14.9046C6.95772 15.6188 8.21442 16 9.5 16C11.11 16 12.59 15.41 13.73 14.43L14 14.71V15.5L19 20.49L20.49 19L15.5 14ZM9.5 14C7.01 14 5 11.99 5 9.5C5 7.01 7.01 5 9.5 5C11.99 5 14 7.01 14 9.5C14 11.99 11.99 14 9.5 14Z" />
                    </svg>
                </div>
            </div>
            <div class="intec-editor-dialog-groups intec-editor-grid-item" v-if="!isRefreshing">
                <component ref="panelScrollbar" is="vue-scroll" v-on:handle-scroll="handlePanelScroll" v-bind:ops="scrollbarSettings">
                    <?= Html::beginTag('div', [
                        'class' => 'intec-editor-dialog-group',
                        'v-for' => 'group in groups',
                        'v-if' => '!group.hidden && group.display',
                        'v-bind:ref' => '"groupItem" + group.code',
                        'v-on:click' => 'scrollBodyScrollToGroup(group)'
                    ]) ?>
                    <div class="intec-editor-dialog-group-wrapper">
                        {{ group.name }}
                    </div>
                    <?= Html::endTag('div') ?>
                </component>
            </div>
        </div>
    </div>
    <div class="intec-editor-dialog-body">
        <div class="intec-editor-dialog-wrapper">
            <div class="intec-editor-dialog-controls">
                <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                    <div class="intec-editor-grid-item-auto">
                        <?= Html::beginTag('div', [
                            'class' => 'intec-editor-dialog-control',
                            'data-control' => 'button.icon',
                            'v-on:click' => 'refresh()'
                        ]) ?>
                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.6517 2.35C12.9116 1.60485 12.0313 1.01356 11.0616 0.610231C10.0919 0.206901 9.05196 -0.000494355 8.00172 8.84845e-07C3.58172 8.84845e-07 0.0117188 3.58 0.0117188 8C0.0117188 12.42 3.58172 16 8.00172 16C11.7317 16 14.8417 13.45 15.7317 10H13.6517C13.2398 11.1695 12.4751 12.1824 11.4631 12.8988C10.4511 13.6153 9.24166 14 8.00172 14C4.69172 14 2.00172 11.31 2.00172 8C2.00172 4.69 4.69172 2 8.00172 2C9.66172 2 11.1417 2.69 12.2217 3.78L9.00172 7H16.0017V8.84845e-07L13.6517 2.35Z" fill="#929BAA"/>
                            </svg>
                        <?= Html::endTag('div') ?>
                    </div>
                    <div class="intec-editor-grid-item"></div>
                    <div class="intec-editor-grid-item-auto" v-if="$root.template.settings.developmentMode">
                        <?= Html::beginTag('div', [
                            'class' => 'intec-editor-dialog-control',
                            'data-control' => 'button.icon',
                            'v-on:click' => 'refresh(true)'
                        ]) ?>
                            <svg width="17" height="16" viewBox="0 0 17 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M13.6517 2.35C12.9116 1.60485 12.0313 1.01356 11.0616 0.610231C10.0919 0.206901 9.05196 -0.000494355 8.00172 8.84845e-07C3.58172 8.84845e-07 0.0117188 3.58 0.0117188 8C0.0117188 12.42 3.58172 16 8.00172 16C11.7317 16 14.8417 13.45 15.7317 10H13.6517C13.2398 11.1695 12.4751 12.1824 11.4631 12.8988C10.4511 13.6153 9.24166 14 8.00172 14C4.69172 14 2.00172 11.31 2.00172 8C2.00172 4.69 4.69172 2 8.00172 2C9.66172 2 11.1417 2.69 12.2217 3.78L9.00172 7H16.0017V8.84845e-07L13.6517 2.35Z" fill="#929BAA"/>
                            </svg>
                        <?= Html::endTag('div') ?>
                    </div>
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                            <i class="fal fa-times"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intec-editor-dialog-title" data-align="left" v-if="!isRefreshing">
                <div class="intec-editor-dialog-title-wrapper">
                    <div class="intec-editor-dialog-title-name">
                        {{ name }}
                    </div>
                    <div class="intec-editor-dialog-title-code">
                        {{ code }}
                    </div>
                </div>
            </div>
            <div class="intec-editor-dialog-content">
                <div class="intec-editor-dialog-content-wrapper intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-o-vertical intec-editor-grid-a-h-center intec-editor-grid-a-v-center" v-if="isRefreshing">
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-dialog-preload">
                            <component is="v-progress-circular" width="3" size="120" indeterminate></component>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-dialog-content-wrapper intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-o-vertical" v-else>
                    <div class="intec-editor-grid-item">
                        <component ref="bodyScrollbar" is="vue-scroll" v-on:handle-scroll="handleBodyScroll" v-bind:ops="scrollbarSettings">
                            <div class="intec-editor-dialog-content-part">
                                <table class="intec-editor-dialog-parameters bxcompprop-content-table">
                                    <template v-for="group in groups" v-if="!group.hidden">
                                        <tr class="bxcompprop-prop-tr" data-row="group" v-bind:ref="'groupRow' + group.code" v-bind:style="{'display': group.display ? null : 'none'}">
                                            <td class="bxcompprop-cont-table-title" colspan="2">{{ group.name }}</td>
                                        </tr>
                                        <template v-if="group.code === 'COMPONENT_TEMPLATE'">
                                            <tr class="bxcompprop-prop-tr" data-row="parameter">
                                                <td class="bxcompprop-cont-table-l">
                                                    <label class="bxcompprop-label">
                                                        {{ $root.$localization.getMessage('dialogs.componentSettings.parameters.template.name') }}
                                                    </label>
                                                </td>
                                                <td class="bxcompprop-cont-table-r">
                                                    <?= Html::beginTag('select', [
                                                        'v-model' => 'template'
                                                    ]) ?>
                                                    <?= Html::tag('option', '{{ template.name }}', [
                                                        'v-for' => 'template in templates',
                                                        'v-bind:value' => 'template.code'
                                                    ]) ?>
                                                    <?= Html::endTag('select') ?>
                                                </td>
                                            </tr>
                                        </template>
                                        <template v-else>
                                            <tr class="bxcompprop-prop-tr" data-row="parameter" v-for="(parameter, index) in group.parameters" v-bind:key="parameter.code" v-if="!parameter.hidden" v-bind:style="{'display': parameter.display ? null : 'none'}">
                                                <td class="bxcompprop-cont-table-l">
                                                    <label class="bxcompprop-label" v-bind:title="parameter.code">
                                                        {{ parameter.name }}
                                                    </label>
                                                </td>
                                                <td class="bxcompprop-cont-table-r" v-bind:ref="'parameterCell' + parameter.code">
                                                    <template v-if="parameter.type === 'CHECKBOX'">
                                                        <?= Html::tag('component', null, [
                                                            'is' => 'v-checkbox',
                                                            'class' => 'v-input-theme-dialog-default',
                                                            'v-bind:ref' => '"parameterInput" + parameter.code',
                                                            'v-model' => 'parameter.value',
                                                            'hide-details' => 'auto',
                                                            'value' => 'Y',
                                                            'inset' => true
                                                        ]) ?>
                                                    </template>
                                                    <template v-else-if="parameter.type === 'LIST'">
                                                        <template v-if="!parameter.multiple">
                                                            <?= Html::beginTag('select', [
                                                                'v-model' => 'parameter.value',
                                                                'v-bind:ref' => '"parameterInput" + parameter.code'
                                                            ]) ?>
                                                                <?= Html::tag('option', '{{ value.name }}', [
                                                                    'v-for' => 'value in parameter.values',
                                                                    'v-bind:value' => 'value.value'
                                                                ]) ?>
                                                            <?= Html::endTag('select') ?>
                                                            <template v-if="parameter.extended">
                                                                <?= Html::tag('input', null, [
                                                                    'type' => 'text',
                                                                    'v-bind:ref' => '"parameterInput" + parameter.code',
                                                                    'v-bind:disabled' => 'parameter.value !== null',
                                                                    'v-model.lazy' => 'parameter.customValue'
                                                                ]) ?>
                                                            </template>
                                                        </template>
                                                        <template v-else>
                                                            <?= Html::beginTag('select', [
                                                                'v-model' => 'parameter.value',
                                                                'v-bind:ref' => '"parameterInput" + parameter.code',
                                                                'multiple' => true
                                                            ]) ?>
                                                                <?= Html::tag('option', '{{ value.name }}', [
                                                                    'v-for' => 'value in parameter.values',
                                                                    'v-bind:value' => 'value'
                                                                ]) ?>
                                                            <?= Html::endTag('select') ?>
                                                            <template v-if="parameter.extended">
                                                                <template v-for="parameterValue in parameter.customValue">
                                                                    <?= Html::tag('input', null, [
                                                                        'type' => 'text',
                                                                        'v-bind:ref' => '"parameterInput" + parameter.code',
                                                                        'v-model.lazy' => 'parameterValue.value'
                                                                    ]) ?>
                                                                </template>
                                                                <input type="button" class="component-prop-button-ok" value="+" v-on:click="parameter.customValue.push({'value': null})" />
                                                            </template>
                                                        </template>
                                                    </template>
                                                    <template v-else-if="parameter.type === 'CUSTOM'">
                                                        <input type="hidden" v-bind:ref="'parameterInput' + parameter.code" v-bind:value="parameter.value" />
                                                    </template>
                                                    <template v-else-if="parameter.type === 'STRING'">
                                                        <template v-if="!parameter.multiple">
                                                            <?= Html::tag('input', null, [
                                                                'type' => 'text',
                                                                'v-bind:ref' => '"parameterInput" + parameter.code',
                                                                'v-model.lazy' => 'parameter.value'
                                                            ]) ?>
                                                        </template>
                                                        <template v-else>
                                                            <template v-for="parameterValue in parameter.value">
                                                                <?= Html::tag('input', null, [
                                                                    'type' => 'text',
                                                                    'v-bind:ref' => '"parameterInput" + parameter.code',
                                                                    'v-model.lazy' => 'parameterValue.value'
                                                                ]) ?>
                                                            </template>
                                                            <input type="button" class="component-prop-button-ok" value="+" v-on:click="parameter.value.push({'value': null})" />
                                                        </template>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                </table>
                            </div>
                        </component>
                    </div>
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-dialog-content-part">
                            <div class="intec-editor-dialog-buttons intec-editor-grid intec-editor-grid-a-h-center intec-editor-grid-i-5">
                                <div class="intec-editor-grid-item-auto">
                                    <button class="intec-editor-dialog-button intec-editor-button" data-scheme="gray" v-ripple v-on:click="close">
                                        {{ $root.$localization.getMessage('dialogs.componentSettings.buttons.close') }}
                                    </button>
                                </div>
                                <div class="intec-editor-grid-item-auto">
                                    <button class="intec-editor-dialog-button intec-editor-button" v-ripple v-on:click="apply">
                                        {{ $root.$localization.getMessage('dialogs.componentSettings.buttons.apply') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</component>
