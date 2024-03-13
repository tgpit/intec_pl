<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<component is="v-dialog" v-bind:retain-focus="false" persistent max-width="700" content-class="intec-editor-dialog intec-editor-dialog-component-list intec-editor-dialog-theme-default" v-model="display">
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
            {{ $root.$localization.getMessage('dialogs.componentList.title') }}
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
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-search">
                        <div class="intec-editor-dialog-content-part">
                            <?= Html::tag('component', null, [
                                'is' => 'v-text-field',
                                'class' => 'v-input-theme-dialog-default',
                                'hide-details' => 'auto',
                                'solo' => true,
                                'flat' => true,
                                'v-model' => 'filter',
                                'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.componentList.search.placeholder")'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-grid-item">
                    <div class="intec-editor-dialog-tree">
                        <component is="vue-scroll" v-bind:ops="scrollbarSettings">
                            <div class="intec-editor-dialog-content-part">
                                <div class="intec-editor-dialog-message" v-if="items.length === 0">
                                    {{ $root.$localization.getMessage('dialogs.componentList.empty', {'query': filter}) }}
                                </div>
                                <div class="intec-editor-dialog-message" v-else-if="filteredItems.length === 0">
                                    {{ $root.$localization.getMessage('dialogs.componentList.search.empty', {'query': filter}) }}
                                </div>
                                <component
                                    is="v-treeview"
                                    v-else
                                    v-bind:items="filteredItems"
                                    v-bind:expand-show="false"
                                    open-on-click
                                    item-children="children"
                                    item-key="code"
                                    item-text="name"
                                >
                                    <template v-slot:label="slot">
                                        <div class="intec-editor-dialog-tree-line" v-if="slot.level > 0"></div>
                                        <div class="intec-editor-dialog-tree-item intec-editor-grid" v-bind:data-type="slot.item.type" v-bind:data-level="slot.level">
                                            <div class="intec-editor-dialog-tree-item-icon intec-editor-grid-item-auto" v-if="slot.item.type === 'section'">
                                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" v-if="!slot.open">
                                                    <path d="M10.8327 7.5H9.16602V10H6.66602V11.6667H9.16602V14.1667H10.8327V11.6667H13.3327V10H10.8327V7.5Z" />
                                                    <path d="M16.666 4.16667H9.51102L8.08852 2.74417C8.01126 2.66663 7.91943 2.60514 7.81832 2.56324C7.71721 2.52133 7.6088 2.49984 7.49935 2.5H3.33268C2.41352 2.5 1.66602 3.2475 1.66602 4.16667V15.8333C1.66602 16.7525 2.41352 17.5 3.33268 17.5H16.666C17.5852 17.5 18.3327 16.7525 18.3327 15.8333V5.83333C18.3327 4.91417 17.5852 4.16667 16.666 4.16667ZM3.33268 15.8333V5.83333H16.666L16.6677 15.8333H3.33268Z" />
                                                </svg>
                                                <svg viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg" v-else>
                                                    <path d="M16.666 4.16667H9.51102L8.08852 2.74417C8.01126 2.66663 7.91943 2.60514 7.81832 2.56324C7.71721 2.52133 7.6088 2.49984 7.49935 2.5H3.33268C2.41352 2.5 1.66602 3.2475 1.66602 4.16667V15.8333C1.66602 16.7525 2.41352 17.5 3.33268 17.5H16.666C17.5852 17.5 18.3327 16.7525 18.3327 15.8333V5.83333C18.3327 4.91417 17.5852 4.16667 16.666 4.16667ZM3.33268 15.8333V5.83333H16.666L16.6677 15.8333H3.33268Z" />
                                                    <path d="M6.5625 10H13.2292V11.6667H6.5625V10Z" />
                                                </svg>
                                            </div>
                                            <div class="intec-editor-dialog-tree-item-text intec-editor-grid-item" v-on:click="selectItem(slot.item)">
                                                <div class="intec-editor-dialog-tree-item-name">{{ slot.item.name ? slot.item.name : slot.item.code }}</div>
                                                <div class="intec-editor-dialog-tree-item-code" v-if="slot.item.type === 'component'">{{ slot.item.code }}</div>
                                            </div>
                                        </div>
                                    </template>
                                    <template v-slot:level="slot">
                                        <div
                                            class="intec-editor-dialog-tree-level"
                                            v-bind:data-open="slot.open ? 'true' : 'false'"
                                            v-bind:data-leaf="slot.leaf ? 'true' : 'false'"
                                            v-bind:data-level="slot.currentLevel"
                                            v-bind:data-first="slot.currentLevel === 0 ? 'true' : 'false'"
                                            v-bind:data-last="slot.currentLevel === slot.level - 1 ? 'true' : 'false'"
                                        ></div>
                                    </template>
                                </component>
                            </div>
                        </component>
                    </div>
                </div>
            </div>
        </div>
    </div>
</component>
