<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

?>
<?= Html::beginTag('div', [
    'class' => 'intec-editor-container',
    'v-bind:class' => 'classes',
    'v-bind:data-type' => 'type',
    'v-bind:data-display' => 'isDisplaying ? "true" : "false"',
    'v-bind:data-root' => 'isRoot ? "true" : "false"',
    'v-bind:data-internal' => 'isInternal ? "true" : "false"',
    'v-bind:data-element' => 'hasElement',
    'v-bind:data-element-type' => 'elementType',
    'v-bind:data-containers' => 'hasContainers',
    'v-bind:data-buffered' => 'isBuffered ? "true" : "false"',
    'v-bind:data-selected' => 'isSelected ? "true" : "false"',
    'v-bind:data-hovered' => 'isHovered ? "true" : "false"',
    'v-bind:data-contains-hovered' => 'hasHovered ? "true" : "false"',
    'v-bind:style' => '!isInternal && isDisplaying ? style : null'
]) ?>
    <template v-if="isDisplaying">
        <div class="intec-editor-container-border" ref="border"></div>
        <template v-if="canAddElementOutside && isHovered">
            <div class="intec-editor-container-add" data-position="before">
                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-container-insert" v-on:click="addElementBefore">
                            <i class="fal fa-plus"></i>
                        </div>
                    </div>
                    <div v-if="canPasteContainerOutsideFromBuffer" class="intec-editor-grid-item-auto">
                        <div class="intec-editor-container-paste" v-on:click="pasteContainerBeforeFromBuffer">
                            {{ $root.$localization.getMessage('container.panel.buttons.paste.before') }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="intec-editor-container-add" data-position="after">
                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-container-insert" v-on:click="addElementAfter">
                            <i class="fal fa-plus"></i>
                        </div>
                    </div>
                    <div v-if="canPasteContainerOutsideFromBuffer" class="intec-editor-grid-item-auto">
                        <div class="intec-editor-container-paste" v-on:click="pasteContainerAfterFromBuffer">
                            {{ $root.$localization.getMessage('container.panel.buttons.paste.after') }}
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-if="node.width >= 700">
            <transition name="intec-editor-container-panels">
                <div class="intec-editor-container-panels" v-if="showPanels" data-panel="edit">
                    <div class="intec-editor-container-panel">
                        <div class="intec-editor-container-panel-group intec-editor-grid intec-editor-grid-nowrap">
                            <div class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="openSettings">
                                <div class="intec-editor-container-panel-button-wrapper">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M14.5798 9.7425L14.5723 9.7575C14.6023 9.51 14.6323 9.255 14.6323 9C14.6323 8.745 14.6098 8.505 14.5798 8.2575L14.5873 8.2725L16.4173 6.8325L14.5948 3.6675L12.4423 4.5375L12.4498 4.545C12.0598 4.245 11.6323 3.99 11.1673 3.795H11.1748L10.8298 1.5H7.17734L6.84734 3.8025H6.85484C6.38984 3.9975 5.96234 4.2525 5.57234 4.5525L5.57984 4.545L3.41984 3.6675L1.58984 6.8325L3.41984 8.2725L3.42734 8.2575C3.39734 8.505 3.37484 8.745 3.37484 9C3.37484 9.255 3.39734 9.51 3.43484 9.7575L3.42734 9.7425L1.85234 10.98L1.60484 11.175L3.42734 14.325L5.58734 13.4625L5.57234 13.4325C5.96984 13.74 6.39734 13.995 6.86984 14.19H6.84734L7.18484 16.5H10.8223C10.8223 16.5 10.8448 16.365 10.8673 16.185L11.1523 14.1975H11.1448C11.6098 14.0025 12.0448 13.7475 12.4423 13.44L12.4273 13.47L14.5873 14.3325L16.4098 11.1825C16.4098 11.1825 16.3048 11.0925 16.1623 10.9875L14.5798 9.7425ZM8.99984 11.625C7.55234 11.625 6.37484 10.4475 6.37484 9C6.37484 7.5525 7.55234 6.375 8.99984 6.375C10.4473 6.375 11.6248 7.5525 11.6248 9C11.6248 10.4475 10.4473 11.625 8.99984 11.625Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-text">
                                                {{ $root.$localization.getMessage('container.panel.buttons.settings') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div v-if="canOpenElementSettings" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="openElementSettings" data-scheme="blue">
                                <div class="intec-editor-container-panel-button-wrapper">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.25 12.9375V15.75H5.0625L13.3575 7.455L10.545 4.6425L2.25 12.9375ZM15.5325 5.28C15.602 5.21062 15.6572 5.1282 15.6948 5.03747C15.7325 4.94674 15.7518 4.84948 15.7518 4.75125C15.7518 4.65303 15.7325 4.55576 15.6948 4.46503C15.6572 4.3743 15.602 4.29189 15.5325 4.2225L13.7775 2.4675C13.7081 2.39797 13.6257 2.34281 13.535 2.30518C13.4442 2.26754 13.347 2.24817 13.2488 2.24817C13.1505 2.24817 13.0533 2.26754 12.9625 2.30518C12.8718 2.34281 12.7894 2.39797 12.72 2.4675L11.3475 3.84L14.16 6.6525L15.5325 5.28Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-text">
                                                {{ $root.$localization.getMessage('container.panel.buttons.edit') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <a v-if="hasBlock && model.element.id" target="_blank" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-bind:href="$root.$links.get('builds.templates.blocks.editor', { 'block': model.element.id })">
                                <div class="intec-editor-container-panel-button-wrapper">
                                    <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M2.25 12.9375V15.75H5.0625L13.3575 7.455L10.545 4.6425L2.25 12.9375ZM15.5325 5.28C15.602 5.21062 15.6572 5.1282 15.6948 5.03747C15.7325 4.94674 15.7518 4.84948 15.7518 4.75125C15.7518 4.65303 15.7325 4.55576 15.6948 4.46503C15.6572 4.3743 15.602 4.29189 15.5325 4.2225L13.7775 2.4675C13.7081 2.39797 13.6257 2.34281 13.535 2.30518C13.4442 2.26754 13.347 2.24817 13.2488 2.24817C13.1505 2.24817 13.0533 2.26754 12.9625 2.30518C12.8718 2.34281 12.7894 2.39797 12.72 2.4675L11.3475 3.84L14.16 6.6525L15.5325 5.28Z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-container-panel-button-text">
                                                {{ $root.$localization.getMessage('container.panel.buttons.editor') }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </transition>
            <transition name="intec-editor-container-panels">
                <div class="intec-editor-container-panels intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-12" v-if="showPanels" data-panel="actions">
                    <div v-if="hasVariator" class="intec-editor-container-panel intec-editor-grid-item-auto">
                        <div class="intec-editor-container-panel-group intec-editor-grid intec-editor-grid-nowrap">
                            <div class="intec-editor-container-panel-button intec-editor-grid-item-auto">
                                <component is="v-menu" offset-y nudge-bottom="4">
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-dropdown-button" v-on="element.variants.length > 0 ? on : null">
                                            <div class="intec-editor-container-panel-dropdown-button-content intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-8">
                                                <div class="intec-editor-grid-item">
                                                    <div class="intec-editor-container-panel-dropdown-button-text" v-if="element.hasVariant()">
                                                        {{ element.getVariant().name }}
                                                    </div>
                                                    <div class="intec-editor-container-panel-dropdown-button-text" v-else>
                                                        {{ $root.$localization.getMessage('container.panel.buttons.variant.empty') }}
                                                    </div>
                                                </div>
                                                <div class="intec-editor-grid-item-auto">
                                                    <div class="intec-editor-container-panel-dropdown-button-icon">
                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4 7L9.5 12L15 7H4Z" fill="#292929"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                    <component class="v-menu-theme-container-panel" is="v-list">
                                        <?= Html::beginTag('component', [
                                            'is' => 'v-list-item',
                                            'v-for' => 'variant in element.getSortedVariants()',
                                            'v-bind:key' => 'variant.uid',
                                            'v-bind:data-active' => 'variant === element.getVariant() ? "true" : "false"',
                                            'v-on:click' => 'element.setVariant(variant)',
                                            'v-ripple' => true
                                        ]) ?>
                                            <component is="v-list-item-title">
                                                {{ variant.name }}
                                            </component>
                                        <?= Html::endTag('component') ?>
                                    </component>
                                </component>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-container-panel intec-editor-grid-item-auto">
                        <div class="intec-editor-container-panel-group intec-editor-grid intec-editor-grid-nowrap">
                            <div v-if="canAddElementInside" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="addElementInside">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.5,10.5H11c-0.3,0-0.5,0.2-0.5,0.5v4.5c0,0.3-0.2,0.5-0.5,0.5H8c-0.3,0-0.5-0.2-0.5-0.5V11c0-0.3-0.2-0.5-0.5-0.5H2.5 C2.2,10.5,2,10.3,2,10V8c0-0.3,0.2-0.5,0.5-0.5H7c0.3,0,0.5-0.2,0.5-0.5V2.5C7.5,2.2,7.7,2,8,2h2c0.3,0,0.5,0.2,0.5,0.5V7 c0,0.3,0.2,0.5,0.5,0.5h4.5C15.8,7.5,16,7.7,16,8v2C16,10.3,15.8,10.5,15.5,10.5z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.addElementInside') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canPasteContainerInsideFromBuffer" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="pasteContainerInsideFromBuffer">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.395 3.245V11.395H3.245V3.245H11.395ZM12.08 1.875H2.56C2.18184 1.875 1.875 2.18184 1.875 2.56V12.08C1.875 12.4582 2.18184 12.765 2.56 12.765H12.08C12.4582 12.765 12.765 12.4582 12.765 12.08V2.56C12.765 2.18184 12.4582 1.875 12.08 1.875Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                    <path d="M14.88 13.355H14.755V13.48V14.755H13.48H13.355V14.88V16V16.125H13.48H15.44C15.8181 16.125 16.125 15.8182 16.125 15.44V13.48V13.355H16H14.88Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                    <path d="M9.17334 14.755H9.04834V14.88V16V16.125H9.17334H12.1861H12.3111V16V14.88V14.755H12.1861H9.17334Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                    <path d="M7.87986 14.755H6.60486V13.48V13.355H6.47986H5.35986H5.23486V13.48V15.44C5.23486 15.8182 5.54171 16.125 5.91986 16.125H7.87986H8.00486V16V14.88V14.755H7.87986Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                    <path d="M13.48 5.23499H13.355V5.35999V6.47999V6.60499H13.48H14.755V7.87999V8.00499H14.88H16H16.125V7.87999V5.91999C16.125 5.54184 15.8181 5.23499 15.44 5.23499H13.48Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                    <path d="M14.8799 9.04861H14.7549V9.17361V12.1864V12.3114H14.8799H15.9999H16.1249V12.1864V9.17361V9.04861H15.9999H14.8799Z" fill="#292929" stroke="#292929" stroke-width="0.25"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.paste') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canBeCopy" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="copy">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15 1.5H7.5C6.67275 1.5 6 2.17275 6 3V6H3C2.17275 6 1.5 6.67275 1.5 7.5V15C1.5 15.8273 2.17275 16.5 3 16.5H10.5C11.3273 16.5 12 15.8273 12 15V12H15C15.8273 12 16.5 11.3273 16.5 10.5V3C16.5 2.17275 15.8273 1.5 15 1.5ZM3 15V7.5H10.5L10.5015 15H3ZM15 10.5H12V7.5C12 6.67275 11.3273 6 10.5 6H7.5V3H15V10.5Z" />
                                                    <path d="M4.5 9H9V10.5H4.5V9ZM4.5 12H9V13.5H4.5V12Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.copy') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canBeCut" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="cut">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M16,4.3c-0.6-0.9-1.9-1.2-2.9-0.5L8.4,7.1L6.7,5.9C7.3,4.8,7,3.4,5.9,2.6C4.8,1.8,3.2,2.1,2.5,3.2 C1.7,4.4,1.9,5.9,3.1,6.7c0.8,0.5,1.8,0.6,2.5,0.2l2.1,1.5L5.6,9.9c-0.8-0.4-1.8-0.4-2.5,0.2c-1.1,0.8-1.4,2.4-0.6,3.5 c0.8,1.1,2.4,1.4,3.5,0.6C7,13.4,7.3,12,6.7,10.9l1.7-1.2l4.8,3.3c0.9,0.6,2.2,0.4,2.9-0.5l-5.8-4.1L16,4.3z M4.5,6	C3.8,6,3.2,5.4,3.2,4.7s0.6-1.3,1.3-1.3c0.7,0,1.3,0.6,1.3,1.3S5.2,6,4.5,6z M4.5,13.4c-0.7,0-1.3-0.6-1.3-1.3s0.6-1.3,1.3-1.3 c0.7,0,1.3,0.6,1.3,1.3C5.8,12.8,5.2,13.4,4.5,13.4z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.cut') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canBeRefreshed" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="refresh">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9,2.1C5.3,2.1,2.3,5,2.1,8.7h-2L3.4,13l3.3-4.4h-2c0.2-2.2,2-4,4.3-4c2.4,0,4.3,1.9,4.3,4.3s-1.9,4.3-4.3,4.3 c-1.2,0-2.3-0.5-3.1-1.4L4.3,14c1.2,1.2,2.9,1.9,4.7,1.9c3.8,0,6.9-3.1,6.9-6.9S12.8,2.1,9,2.1z" />
                                                    <path d="M9,4.6" />
                                                    <path d="M9,13.4" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.refresh') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canBeRemoved" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="remove">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.5 14.25C4.5 15.075 5.175 15.75 6 15.75H12C12.825 15.75 13.5 15.075 13.5 14.25V5.25H4.5V14.25ZM14.25 3H11.625L10.875 2.25H7.125L6.375 3H3.75V4.5H14.25V3Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.remove') }}
                                    </span>
                                </component>
                            </div>
                            <div v-if="canToggleDisplay" class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="toggleDisplay" v-bind:data-active="!model.display">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M9.9297 4.51902C9.62449 4.48504 9.31437 4.46556 9.00001 4.46072C7.62634 4.46699 6.20236 4.81094 4.8545 5.46999C3.85372 5.97949 2.87885 6.69877 2.03223 7.58602C1.61643 8.03895 1.08575 8.69475 1 9.39445C1.01013 10.0006 1.64204 10.7487 2.03223 11.2029C2.82613 12.0553 3.77562 12.754 4.8545 13.3189C4.89116 13.3373 4.92796 13.3554 4.96484 13.3732L3.96388 15.1726L5.32395 16L12.6762 2.82361L11.367 2L9.9297 4.51902ZM13.0342 5.41772L12.0352 7.19999C12.4948 7.81462 12.7676 8.57257 12.7676 9.39445C12.7676 11.443 11.0806 13.1038 8.99902 13.1038C8.90903 13.1038 8.82182 13.0938 8.73339 13.0877L8.07226 14.2659C8.37717 14.2995 8.68546 14.324 8.99999 14.3282C10.375 14.3218 11.7981 13.9739 13.1445 13.3189C14.1453 12.8094 15.1211 12.0901 15.9678 11.2029C16.3836 10.75 16.9142 10.0942 17 9.39445C16.9899 8.78834 16.358 8.04022 15.9678 7.58601C15.1739 6.73361 14.2234 6.03491 13.1445 5.46997C13.1081 5.45179 13.0708 5.43545 13.0342 5.41772ZM8.99903 5.68512C9.09031 5.68512 9.18087 5.68888 9.27051 5.69516L8.4961 7.07536C7.40924 7.30218 6.59375 8.25406 6.59375 9.39346C6.59375 9.67968 6.64496 9.9537 6.73926 10.2077C6.73936 10.208 6.73915 10.2084 6.73926 10.2087L5.96288 11.5929C5.5022 10.9777 5.23046 10.2173 5.23046 9.39443C5.23047 7.34592 6.91744 5.6851 8.99903 5.68512ZM11.252 8.59529L9.50685 11.7075C10.5879 11.4764 11.3975 10.5289 11.3975 9.39346C11.3975 9.11246 11.343 8.8453 11.252 8.59529Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.toggleDisplay') }}
                                    </span>
                                </component>
                            </div>
                        </div>
                    </div>
                    <div v-if="canChangeOrder" class="intec-editor-container-panel intec-editor-grid-item-auto">
                        <div class="intec-editor-container-panel-group intec-editor-grid intec-editor-grid-nowrap">
                            <div class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="orderUp">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M13.9255 8.86415L9.80979 3.16449C9.77304 3.11365 9.72461 3.07222 9.66849 3.04362C9.61237 3.01501 9.55018 3.00006 9.48707 3H9.48612C9.42278 3.00013 9.36038 3.01524 9.30411 3.04406C9.24785 3.07289 9.19934 3.1146 9.16261 3.16574L5.07341 8.8654C5.03113 8.92433 5.00607 8.99366 5.00097 9.06578C4.99587 9.1379 5.01093 9.21001 5.0445 9.2742C5.07788 9.33847 5.12852 9.39237 5.19085 9.42996C5.25317 9.46754 5.32475 9.48735 5.39771 9.48721H7.32218L7.32202 14.6064C7.322 14.6581 7.33227 14.7093 7.35223 14.7571C7.3722 14.8049 7.40148 14.8483 7.43839 14.8848C7.4753 14.9214 7.51912 14.9504 7.56735 14.9701C7.61558 14.9899 7.66727 15 7.71946 15L11.2801 14.9998C11.3323 14.9999 11.384 14.9897 11.4323 14.9699C11.4805 14.9501 11.5243 14.9211 11.5612 14.8845C11.5982 14.8479 11.6274 14.8045 11.6474 14.7567C11.6673 14.7089 11.6776 14.6576 11.6776 14.6059V9.48736H13.6027C13.7511 9.48736 13.8875 9.40441 13.956 9.27342C13.9895 9.20903 14.0043 9.13675 13.9989 9.06453C13.9935 8.99232 13.9681 8.92298 13.9255 8.86415Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.orderUp') }}
                                    </span>
                                </component>
                            </div>
                            <div class="intec-editor-container-panel-button intec-editor-grid-item-auto" v-on:click="orderDown">
                                <component is="v-tooltip" bottom>
                                    <template v-slot:activator="{ on }">
                                        <div class="intec-editor-container-panel-button-wrapper" v-on="on">
                                            <div class="intec-editor-container-panel-button-icon">
                                                <svg viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M5.07446 9.13577L9.1902 14.8355C9.22695 14.8863 9.27539 14.9278 9.33151 14.9564C9.38763 14.985 9.44981 14.9999 9.51292 15H9.51387C9.57722 14.9999 9.63961 14.9848 9.69588 14.9559C9.75215 14.9271 9.80066 14.8854 9.83738 14.8343L13.9266 9.13468C13.9689 9.07575 13.9939 9.00642 13.999 8.9343C14.0041 8.86218 13.9891 8.79007 13.9555 8.72588C13.9221 8.6616 13.8715 8.6077 13.8092 8.57012C13.7468 8.53253 13.6753 8.51272 13.6023 8.51287H11.6778L11.678 3.39362C11.678 3.34191 11.6677 3.2907 11.6478 3.24293C11.6278 3.19515 11.5985 3.15174 11.5616 3.11519C11.5247 3.07863 11.4809 3.04964 11.4326 3.02988C11.3844 3.01011 11.3327 2.99996 11.2805 3L7.71988 3.00016C7.66768 3.00014 7.61599 3.01031 7.56776 3.03009C7.51953 3.04987 7.47571 3.07888 7.4388 3.11545C7.40189 3.15201 7.37262 3.19543 7.35265 3.24322C7.33268 3.291 7.32242 3.34222 7.32244 3.39393V8.51255H5.39749C5.24901 8.51255 5.11268 8.5955 5.04413 8.7265C5.01061 8.79086 4.99571 8.86315 5.00106 8.93537C5.00642 9.0076 5.03182 9.07695 5.07446 9.13577Z" />
                                                </svg>
                                            </div>
                                        </div>
                                    </template>
                                    <span>
                                        {{ $root.$localization.getMessage('container.panel.buttons.orderDown') }}
                                    </span>
                                </component>
                            </div>
                        </div>
                    </div>
                </div>
            </transition>
        </template>
        <template v-else>
            <div v-if="showPanels" class="intec-editor-container-panels" data-panel="edit">
                <div class="intec-editor-container-panel">
                    <div class="intec-editor-container-panel-group">
                        <component is="v-menu" offset-y nudge-bottom="4">
                            <template v-slot:activator="menu">
                                <div class="intec-editor-container-panel-button" v-on="menu.on" v-if="node.width >= 250">
                                    <div class="intec-editor-container-panel-button-wrapper">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                            <div class="intec-editor-grid-item-auto">
                                                <div class="intec-editor-container-panel-button-text">
                                                    {{ $root.$localization.getMessage('container.panel.buttons.control') }}
                                                </div>
                                            </div>
                                            <div class="intec-editor-grid-item-auto">
                                                <div class="intec-editor-container-panel-button-icon">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4 7L9.5 12L15 7H4Z" fill="#292929"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="intec-editor-container-panel-button" v-on="menu.on" v-else>
                                    <div class="intec-editor-container-panel-button-wrapper">
                                        <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                            <div class="intec-editor-grid-item-auto">
                                                <div class="intec-editor-container-panel-button-icon">
                                                    <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                        <path d="M4 7L9.5 12L15 7H4Z" fill="#292929"/>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <component class="v-menu-theme-container-panel" is="v-list">
                                <component is="v-list-item" v-on:click="openSettings">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.settings') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canOpenElementSettings" v-on:click="openElementSettings">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.edit') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="hasBlock && model.element.id">
                                    <a class="v-list-item__title" v-bind:href="$root.$links.get('builds.templates.blocks.editor', { 'block': model.element.id })">
                                        {{ $root.$localization.getMessage('container.panel.buttons.editor') }}
                                    </a>
                                </component>
                                <component is="v-list-item" v-if="canAddElementInside" v-on:click="addElementInside">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.addElementInside') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canPasteContainerInsideFromBuffer" v-on:click="pasteContainerInsideFromBuffer">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.paste') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canBeCopy" v-on:click="copy">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.copy') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canBeCut" v-on:click="cut">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.cut') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canBeRefreshed" v-on:click="refresh">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.refresh') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canBeRemoved" v-on:click="remove">
                                    <component is="v-list-item-title">
                                        {{ $root.$localization.getMessage('container.panel.buttons.remove') }}
                                    </component>
                                </component>
                                <component is="v-list-item" v-if="canToggleDisplay" v-on:click="toggleDisplay">
                                    <component is="v-list-item-title">
                                        <template v-if="model.display">
                                            {{ $root.$localization.getMessage('container.panel.buttons.displayHide') }}
                                        </template>
                                        <template v-else>
                                            {{ $root.$localization.getMessage('container.panel.buttons.displayShow') }}
                                        </template>
                                    </component>
                                </component>
                                <template v-if="canChangeOrder">
                                    <component is="v-list-item" v-on:click="orderUp">
                                        <component is="v-list-item-title">
                                            {{ $root.$localization.getMessage('container.panel.buttons.orderUp') }}
                                        </component>
                                    </component>
                                    <component is="v-list-item" v-on:click="orderDown">
                                        <component is="v-list-item-title">
                                            {{ $root.$localization.getMessage('container.panel.buttons.orderDown') }}
                                        </component>
                                    </component>
                                </template>
                            </component>
                        </component>
                    </div>
                </div>
            </div>
        </template>
        <div class="intec-editor-container-content" ref="content">
            <div class="intec-editor-container-content-wrapper">
                <template v-if="isGrid">
                    <?= $this->getComponent('editor-container-grid')->apply([
                        'v-bind:type' => 'gridType',
                        'v-bind:width' => 'gridWidth',
                        'v-bind:height' => 'gridHeight'
                    ]) ?>
                </template>
                <div class="intec-editor-container-element" v-if="hasElement">
                    <component v-bind:is="elementComponent" v-if="elementComponent" v-bind:model="element" v-on:begin-busy="beginBusy" v-on:end-busy="endBusy"></component>
                </div>
                <div class="intec-editor-container-containers" v-if="!hasElement && hasContainers">
                    <?= $this->begin([
                        'v-for' => 'child in sortedContainers',
                        'v-bind:key' => 'child.uid',
                        'v-bind:model' => 'child'
                    ]) ?>
                    <?= $this->end() ?>
                </div>
            </div>
        </div>
        <transition name="intec-editor-container-overlay">
            <?= Html::beginTag('div', [
                'class' => [
                    'intec-editor-container-overlay',
                    'intec-editor-grid' => [
                        '',
                        'a-v-center',
                        'a-h-center'
                    ]
                ],
                'ref' => 'overlay',
                'v-if' => 'isBusy'
            ]) ?>
                <div class="intec-editor-grid-item-auto">
                    <?= Html::tag('component', null, [
                        'is' => 'v-progress-circular',
                        'width' => '3',
                        'size' => '40',
                        'color' => '#3A86FF',
                        'indeterminate' => true,
                    ]) ?>
                </div>
            <?= Html::endTag('div') ?>
        </transition>
    </template>
<?= Html::endTag('div') ?>
