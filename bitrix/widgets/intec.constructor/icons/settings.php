<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

$language = $this->getLanguage();

?>
<div class="intec-editor-widget-settings">
    <div class="intec-editor-settings-group">
        <div class="intec-editor-settings-group-name">
            <?= $language->getMessage('settings.icons.groups.header') ?>
        </div>
        <div class="intec-editor-settings-group-content">
            <div class="intec-editor-settings-fields">
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-switch',
                            'v-model' => 'properties.header.show',
                            'label' => $language->getMessage('settings.icons.groups.header.show'),
                            'hide-details' => 'auto',
                            'dark' => true,
                            'inset' => true,
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field" v-if="properties.header.show">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.header.value') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-text-field',
                            'v-model' => 'properties.header.value',
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
            <?= $language->getMessage('settings.icons.groups.icons') ?>
        </div>
        <div class="intec-editor-settings-group-content">
            <div class="intec-editor-settings-field">
                <div class="intec-editor-settings-field-name">
                    <?= $language->getMessage('settings.icons.groups.icons.count') ?>
                </div>
                <div class="intec-editor-settings-field-content">
                    <?= Html::tag('component', null, [
                        'class' => 'v-input-theme-settings',
                        'is' => 'v-select',
                        'v-model' => 'iconsCount',
                        'v-bind:items' => '[
                            {"value": 1, "text": "1"},
                            {"value": 2, "text": "2"},
                            {"value": 3, "text": "3"},
                            {"value": 4, "text": "4"},
                            {"value": 5, "text": "5"}
                        ]',
                        'item-value' => 'value',
                        'item-text' => 'text',
                        'value' => 4,
                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                        'hide-details' => 'auto',
                        'solo' => true,
                        'flat' => true
                    ]) ?>
                </div>
            </div>
            <div class="intec-editor-widget-icons-settings-items" v-if="properties.items.length > 0">
                <div v-for="(item, index) in properties.items" v-bind:key="index" class="intec-editor-widget-icons-settings-item">
                    <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-4">
                        <div class="intec-editor-grid-item-auto">
                            <div class="intec-editor-widget-icons-settings-item-icon">
                                <div class="intec-editor-widget-icons-settings-item-icon-background"></div>
                                <?= Html::tag('div', null, [
                                    'class' => 'intec-editor-widget-icons-settings-item-icon-picture',
                                    'v-on:click' => '$root.interface.dialogs.gallery.open(function (itemIcon) {
                                        iconReplace(item, itemIcon);
                                    })',
                                    'v-bind:style' => '{
                                        "background-image": "url(" + replacePathMacros(item.image) + ")"
                                    }'
                                ]) ?>
                            </div>
                        </div>
                        <div class="intec-editor-grid-item">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-settings',
                                'is' => 'v-text-field',
                                'v-model' => 'item.name',
                                'placeholder' => $language->getMessage('settings.icons.groups.icons.placeholder.name'),
                                'hide-details' => 'auto',
                                'solo' => true,
                                'flat' => true
                            ]) ?>
                        </div>
                        <div class="intec-editor-grid-item-1">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-settings',
                                'is' => 'v-textarea',
                                'v-model' => 'item.description',
                                'placeholder' => $language->getMessage('settings.icons.groups.icons.placeholder.description'),
                                'hide-details' => 'auto',
                                'auto-grow' => true,
                                'rows' => 2,
                                'solo' => true,
                                'flat' => true
                            ]) ?>
                        </div>
                        <div class="intec-editor-grid-item-1">
                            <div class="intec-editor-widget-icons-settings-item-actions">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-8">
                                    <div class="intec-editor-grid-item-auto">
                                        <?= Html::beginTag('button', [
                                            'class' => 'intec-editor-widget-icons-settings-item-action',
                                            'v-on:click' => '$root.interface.dialogs.gallery.open(function (itemIcon) {
                                                iconReplace(item, itemIcon);
                                            })'
                                        ]) ?>
                                        <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                <span class="intec-editor-grid-item-auto">
                                                    <span class="intec-editor-widget-icons-settings-item-action-icon">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M2 11.5V14H4.5L11.8733 6.62671L9.37333 4.12671L2 11.5ZM13.8067 4.69338C13.8685 4.6317 13.9175 4.55844 13.951 4.47779C13.9844 4.39714 14.0016 4.31069 14.0016 4.22338C14.0016 4.13606 13.9844 4.04961 13.951 3.96896C13.9175 3.88831 13.8685 3.81505 13.8067 3.75338L12.2467 2.19338C12.185 2.13157 12.1117 2.08254 12.0311 2.04909C11.9504 2.01563 11.864 1.99841 11.7767 1.99841C11.6894 1.99841 11.6029 2.01563 11.5223 2.04909C11.4416 2.08254 11.3683 2.13157 11.3067 2.19338L10.0867 3.41338L12.5867 5.91338L13.8067 4.69338V4.69338Z" />
                                                        </svg>
                                                    </span>
                                                </span>
                                                <span class="intec-editor-grid-item">
                                                    <span class="intec-editor-widget-icons-settings-item-action-name">
                                                        <?= $language->getMessage('settings.icons.groups.icons.action.edit') ?>
                                                    </span>
                                                </span>
                                            </span>
                                        <?= Html::endTag('button') ?>
                                    </div>
                                    <div class="intec-editor-grid-item-auto">
                                        <?= Html::beginTag('button', [
                                            'class' => 'intec-editor-widget-icons-settings-item-action',
                                            'v-on:click' => 'iconRemove(item)'
                                        ]) ?>
                                        <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                                                <span class="intec-editor-grid-item-auto">
                                                    <span class="intec-editor-widget-icons-settings-item-action-icon">
                                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M4.00065 12.6667C4.00065 13.4 4.60065 14 5.33398 14H10.6673C11.4007 14 12.0007 13.4 12.0007 12.6667V4.66667H4.00065V12.6667ZM12.6673 2.66667H10.334L9.66732 2H6.33399L5.66732 2.66667H3.33398V4H12.6673V2.66667Z" fill="white"/>
                                                        </svg>
                                                    </span>
                                                </span>
                                                <span class="intec-editor-grid-item">
                                                    <span class="intec-editor-widget-icons-settings-item-action-name">
                                                        <?= $language->getMessage('settings.icons.groups.icons.action.delete') ?>
                                                    </span>
                                                </span>
                                            </span>
                                        <?= Html::endTag('button') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="intec-editor-widget-icons-settings-actions">
                <?= Html::beginTag('button', [
                    'class' => 'intec-editor-widget-icons-settings-action',
                    'v-on:click' => '$root.interface.dialogs.gallery.open(iconAdd)'
                ]) ?>
                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-4">
                        <span class="intec-editor-grid-item-auto">
                            <span class="intec-editor-widget-icons-settings-action-icon">
                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M15.8327 10.8333H10.8327V15.8333H9.16602V10.8333H4.16602V9.16663H9.16602V4.16663H10.8327V9.16663H15.8327V10.8333Z" />
                                </svg>
                            </span>
                        </span>
                        <span class="intec-editor-grid-item">
                            <span class="intec-editor-widget-icons-settings-action-name">
                                <?= $language->getMessage('settings.icons.groups.icons.button.add') ?>
                            </span>
                        </span>
                    </span>
                <?= Html::endTag('button') ?>
            </div>
        </div>
    </div>
    <div class="intec-editor-settings-group">
        <div class="intec-editor-settings-group-name">
            <?= $language->getMessage('settings.icons.groups.caption') ?>
        </div>
        <div class="intec-editor-settings-group-content">
            <div class="intec-editor-settings-fields">
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.font.style') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-settings-selectable intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-v-4">
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-caption-style-bold',
                                    'v-model' => 'properties.caption.style.bold'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-caption-style-bold',
                                ]) ?>
                                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.2507 12.9167H8.33398V10.4167H11.2507C11.5822 10.4167 11.9001 10.5484 12.1345 10.7828C12.369 11.0172 12.5007 11.3352 12.5007 11.6667C12.5007 11.9982 12.369 12.3162 12.1345 12.5506C11.9001 12.785 11.5822 12.9167 11.2507 12.9167V12.9167ZM8.33398 5.41671H10.834C11.1655 5.41671 11.4834 5.5484 11.7179 5.78282C11.9523 6.01724 12.084 6.33519 12.084 6.66671C12.084 6.99823 11.9523 7.31617 11.7179 7.55059C11.4834 7.78501 11.1655 7.91671 10.834 7.91671H8.33398V5.41671ZM13.0007 8.99171C13.809 8.42504 14.3757 7.50004 14.3757 6.66671C14.3757 4.78337 12.9173 3.33337 11.0423 3.33337H5.83398V15H11.7007C13.4507 15 14.7923 13.5834 14.7923 11.8417C14.7923 10.575 14.0757 9.49171 13.0007 8.99171V8.99171Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.bold') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-caption-style-italic',
                                    'v-model' => 'properties.caption.style.italic'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-caption-style-italic'
                                ]) ?>
                                <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.33333 3.33337V5.83337H10.175L7.325 12.5H5V15H11.6667V12.5H9.825L12.675 5.83337H15V3.33337H8.33333Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.italic') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-caption-style-underline',
                                    'v-model' => 'properties.caption.style.underline'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-caption-style-underline'
                                ]) ?>
                                <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.16602 17.5H15.8327V15.8333H4.16602V17.5ZM9.99935 14.1667C11.3254 14.1667 12.5972 13.6399 13.5349 12.7022C14.4726 11.7645 14.9994 10.4927 14.9994 9.16667V2.5H12.916V9.16667C12.916 9.94021 12.6087 10.6821 12.0617 11.2291C11.5148 11.776 10.7729 12.0833 9.99935 12.0833C9.2258 12.0833 8.48394 11.776 7.93695 11.2291C7.38997 10.6821 7.08268 9.94021 7.08268 9.16667V2.5H4.99935V9.16667C4.99935 10.4927 5.52613 11.7645 6.46382 12.7022C7.4015 13.6399 8.67327 14.1667 9.99935 14.1667V14.1667Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.underline') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.align') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-select',
                            'v-model' => 'properties.caption.text.align',
                            'v-bind:items' => '[
                                {"value": "left", "text": "'.$language->getMessage('settings.icons.groups.common.align.left').'"},
                                {"value": "right", "text": "'.$language->getMessage('settings.icons.groups.common.align.right').'"},
                                {"value": "center", "text": "'.$language->getMessage('settings.icons.groups.common.align.center').'"}
                            ]',
                            'item-value' => 'value',
                            'item-text' => 'text',
                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.size.value') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-text-field',
                            'v-model' => 'properties.caption.text.size.value',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.color') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                            <div class="intec-editor-grid-item-auto">
                                <?= Html::beginTag('component', [
                                    'is' => 'v-menu',
                                    'v-model' => 'menu.caption',
                                    'v-bind:close-on-content-click' => 'false',
                                    'nudge-bottom' => '46'
                                ]) ?>
                                    <template v-slot:activator="{ on }">
                                        <?= Html::tag('button', null, [
                                            'class' => 'intec-editor-settings-background-indicator',
                                            'v-on' => 'on',
                                            'v-bind:style' => '{
                                                "background-color": properties.caption.text.color,
                                                "border-color": properties.caption.text.color
                                            }'
                                        ]) ?>
                                    </template>
                                    <compnent is="v-list">
                                        <component is="v-list-item">
                                            <?= Html::tag('component', null, [
                                                'is' => 'v-color-picker',
                                                'v-model' => 'captionColor',
                                                'v-bind:value' => 'captionColor',
                                                'mode' => 'hexa',
                                                'hide-mode-switch' => true,
                                                'width' => '300',
                                                'flat' => true
                                            ]) ?>
                                        </component>
                                        <component is="v-list-item">
                                            <div class="intec-editor-widget-icons-settings-color-actions intec-editor-grid intec-editor-grid-a-h-end">
                                                <div class="intec-editor-grid-item-auto">
                                                    <component is="v-btn" text color="#38455D" v-on:click="menu.caption = false">
                                                        <?= $language->getMessage('settings.icons.menu.cancel') ?>
                                                    </component>
                                                </div>
                                            </div>
                                        </component>
                                    </compnent>
                                <?= Html::endTag('component') ?>
                            </div>
                            <div class="intec-editor-grid-item">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-settings',
                                'is' => 'v-text-field',
                                'v-model' => 'properties.caption.text.color',
                                'placeholder' => '#000000',
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
                        <?= $language->getMessage('settings.icons.groups.common.opacity') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                            <div class="intec-editor-grid-item">
                                <?= Html::tag('component', null, [
                                    'class' => 'v-input-theme-settings',
                                    'is' => 'v-slider',
                                    'v-model' => 'properties.caption.text.opacity',
                                    'v-bind:min' => '0',
                                    'v-bind:max' => '100',
                                    'v-bind:step' => '1',
                                    'hide-details' => 'auto'
                                ]) ?>
                            </div>
                            <div class="intec-editor-grid-item-3">
                                <?= Html::tag('component', null, [
                                    'class' => 'v-input-theme-settings',
                                    'is' => 'v-text-field',
                                    'v-model' => 'properties.caption.text.opacity',
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
            <?= $language->getMessage('settings.icons.groups.description') ?>
        </div>
        <div class="intec-editor-settings-group-content">
            <div class="intec-editor-settings-fields">
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.font.style') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-settings-selectable intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-v-4">
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-description-style-bold',
                                    'v-model' => 'properties.description.style.bold'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-description-style-bold',
                                ]) ?>
                                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M11.2507 12.9167H8.33398V10.4167H11.2507C11.5822 10.4167 11.9001 10.5484 12.1345 10.7828C12.369 11.0172 12.5007 11.3352 12.5007 11.6667C12.5007 11.9982 12.369 12.3162 12.1345 12.5506C11.9001 12.785 11.5822 12.9167 11.2507 12.9167V12.9167ZM8.33398 5.41671H10.834C11.1655 5.41671 11.4834 5.5484 11.7179 5.78282C11.9523 6.01724 12.084 6.33519 12.084 6.66671C12.084 6.99823 11.9523 7.31617 11.7179 7.55059C11.4834 7.78501 11.1655 7.91671 10.834 7.91671H8.33398V5.41671ZM13.0007 8.99171C13.809 8.42504 14.3757 7.50004 14.3757 6.66671C14.3757 4.78337 12.9173 3.33337 11.0423 3.33337H5.83398V15H11.7007C13.4507 15 14.7923 13.5834 14.7923 11.8417C14.7923 10.575 14.0757 9.49171 13.0007 8.99171V8.99171Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.bold') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-description-style-italic',
                                    'v-model' => 'properties.description.style.italic'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-description-style-italic'
                                ]) ?>
                                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M8.33333 3.33337V5.83337H10.175L7.325 12.5H5V15H11.6667V12.5H9.825L12.675 5.83337H15V3.33337H8.33333Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.italic') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                            <div class="intec-editor-grid-item-1">
                                <?= Html::input('checkbox', null, null, [
                                    'id' => 'widget-settings-icons-default-description-style-underline',
                                    'v-model' => 'properties.description.style.underline'
                                ]) ?>
                                <?= Html::beginTag('label', [
                                    'class' => 'intec-editor-settings-selectable-item',
                                    'for' => 'widget-settings-icons-default-description-style-underline'
                                ]) ?>
                                    <span class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-6">
                                        <span class="intec-editor-grid-item-auto">
                                            <span class="intec-editor-settings-selectable-item-icon">
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M4.16602 17.5H15.8327V15.8333H4.16602V17.5ZM9.99935 14.1667C11.3254 14.1667 12.5972 13.6399 13.5349 12.7022C14.4726 11.7645 14.9994 10.4927 14.9994 9.16667V2.5H12.916V9.16667C12.916 9.94021 12.6087 10.6821 12.0617 11.2291C11.5148 11.776 10.7729 12.0833 9.99935 12.0833C9.2258 12.0833 8.48394 11.776 7.93695 11.2291C7.38997 10.6821 7.08268 9.94021 7.08268 9.16667V2.5H4.99935V9.16667C4.99935 10.4927 5.52613 11.7645 6.46382 12.7022C7.4015 13.6399 8.67327 14.1667 9.99935 14.1667V14.1667Z" fill="#A3A3A3"/>
                                                </svg>
                                            </span>
                                        </span>
                                        <span class="intec-editor-grid-item">
                                            <span class="intec-editor-settings-selectable-item-name">
                                                <?= $language->getMessage('settings.icons.groups.common.underline') ?>
                                            </span>
                                        </span>
                                    </span>
                                <?= Html::endTag('label') ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.align') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-select',
                            'v-model' => 'properties.description.text.align',
                            'v-bind:items' => '[
                                {"value": "left", "text": "'.$language->getMessage('settings.icons.groups.common.align.left').'"},
                                {"value": "right", "text": "'.$language->getMessage('settings.icons.groups.common.align.right').'"},
                                {"value": "center", "text": "'.$language->getMessage('settings.icons.groups.common.align.center').'"}
                            ]',
                            'item-value' => 'value',
                            'item-text' => 'text',
                            'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.size.value') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-text-field',
                            'v-model' => 'properties.description.text.size.value',
                            'hide-details' => 'auto',
                            'solo' => true,
                            'flat' => true
                        ]) ?>
                    </div>
                </div>
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-name">
                        <?= $language->getMessage('settings.icons.groups.common.color') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                            <div class="intec-editor-grid-item-auto">
                                <?= Html::beginTag('component', [
                                    'is' => 'v-menu',
                                    'v-model' => 'menu.description',
                                    'v-bind:close-on-content-click' => 'false',
                                    'nudge-bottom' => '46'
                                ]) ?>
                                    <template v-slot:activator="{ on }">
                                        <?= Html::tag('button', null, [
                                            'class' => 'intec-editor-settings-background-indicator',
                                            'v-on' => 'on',
                                            'v-bind:style' => '{
                                                    "background-color": properties.description.text.color,
                                                    "border-color": properties.description.text.color
                                                }'
                                        ]) ?>
                                    </template>
                                    <compnent is="v-list">
                                        <component is="v-list-item">
                                            <?= Html::tag('component', null, [
                                                'is' => 'v-color-picker',
                                                'v-model' => 'descriptionColor',
                                                'v-bind:value' => 'descriptionColor',
                                                'mode' => 'hexa',
                                                'hide-mode-switch' => true,
                                                'width' => '300',
                                                'flat' => true
                                            ]) ?>
                                        </component>
                                        <component is="v-list-item">
                                            <div class="intec-editor-widget-icons-settings-color-actions intec-editor-grid intec-editor-grid-a-h-end">
                                                <div class="intec-editor-grid-item-auto">
                                                    <component is="v-btn" text color="#38455D" v-on:click="menu.description = false">
                                                        <?= $language->getMessage('settings.icons.menu.cancel') ?>
                                                    </component>
                                                </div>
                                            </div>
                                        </component>
                                    </compnent>
                                <?= Html::endTag('component') ?>
                            </div>
                            <div class="intec-editor-grid-item">
                                <?= Html::tag('component', null, [
                                    'class' => 'v-input-theme-settings',
                                    'is' => 'v-text-field',
                                    'v-model' => 'properties.description.text.color',
                                    'placeholder' => '#000000',
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
                        <?= $language->getMessage('settings.icons.groups.common.opacity') ?>
                    </div>
                    <div class="intec-editor-settings-field-content">
                        <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                            <div class="intec-editor-grid-item">
                                <?= Html::tag('component', null, [
                                    'class' => 'v-input-theme-settings',
                                    'is' => 'v-slider',
                                    'v-model' => 'properties.description.text.opacity',
                                    'v-bind:min' => '0',
                                    'v-bind:max' => '100',
                                    'v-bind:step' => '1',
                                    'hide-details' => 'auto'
                                ]) ?>
                            </div>
                            <div class="intec-editor-grid-item-3">
                                <?= Html::tag('component', null, [
                                    'class' => 'v-input-theme-settings',
                                    'is' => 'v-text-field',
                                    'v-model' => 'properties.description.text.opacity',
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
            <?= $language->getMessage('settings.icons.groups.background') ?>
        </div>
        <div class="intec-editor-settings-group-content">
            <div class="intec-editor-settings-fields">
                <div class="intec-editor-settings-field">
                    <div class="intec-editor-settings-field-content">
                        <?= Html::tag('component', null, [
                            'class' => 'v-input-theme-settings',
                            'is' => 'v-switch',
                            'v-model' => 'properties.background.show',
                            'label' => $language->getMessage('settings.icons.groups.background.show'),
                            'hide-details' => 'auto',
                            'dark' => true,
                            'inset' => true,
                        ]) ?>
                    </div>
                </div>
                <template v-if="properties.background.show">
                    <div class="intec-editor-settings-field">
                        <div class="intec-editor-settings-field-name">
                            <?= $language->getMessage('settings.icons.groups.common.color') ?>
                        </div>
                        <div class="intec-editor-settings-field-content">
                            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-2">
                                <div class="intec-editor-grid-item-auto">
                                    <?= Html::beginTag('component', [
                                        'is' => 'v-menu',
                                        'v-model' => 'menu.background',
                                        'v-bind:close-on-content-click' => 'false',
                                        'nudge-bottom' => '46'
                                    ]) ?>
                                        <template v-slot:activator="{ on }">
                                            <?= Html::tag('button', null, [
                                                'class' => 'intec-editor-settings-background-indicator',
                                                'v-on' => 'on',
                                                'v-bind:style' => '{
                                                        "background-color": properties.background.color,
                                                        "border-color": properties.background.color
                                                    }'
                                            ]) ?>
                                        </template>
                                        <compnent is="v-list">
                                            <component is="v-list-item">
                                                <?= Html::tag('component', null, [
                                                    'is' => 'v-color-picker',
                                                    'v-model' => 'backgroundColor',
                                                    'v-bind:value' => 'backgroundColor',
                                                    'mode' => 'hexa',
                                                    'hide-mode-switch' => true,
                                                    'width' => '300',
                                                    'flat' => true
                                                ]) ?>
                                            </component>
                                            <component is="v-list-item">
                                                <div class="intec-editor-widget-icons-settings-color-actions intec-editor-grid intec-editor-grid-a-h-end">
                                                    <div class="intec-editor-grid-item-auto">
                                                        <component is="v-btn" text color="#38455D" v-on:click="menu.background = false">
                                                            <?= $language->getMessage('settings.icons.menu.cancel') ?>
                                                        </component>
                                                    </div>
                                                </div>
                                            </component>
                                        </compnent>
                                    <?= Html::endTag('component') ?>
                                </div>
                                <div class="intec-editor-grid-item">
                                    <?= Html::tag('component', null, [
                                        'class' => 'v-input-theme-settings',
                                        'is' => 'v-text-field',
                                        'v-model' => 'properties.background.color',
                                        'placeholder' => '#000000',
                                        'hide-details' => 'auto',
                                        'solo' => true,
                                        'flat' => true
                                    ]) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-settings-field">
                        <div class="intec-editor-settings-field-content">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-settings',
                                'is' => 'v-switch',
                                'v-model' => 'properties.background.rounding.shared',
                                'label' => $language->getMessage('settings.icons.groups.common.rounding.shared'),
                                'hide-details' => 'auto',
                                'dark' => true,
                                'inset' => true,
                            ]) ?>
                        </div>
                    </div>
                    <template v-if="properties.background.rounding.shared">
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                <?= $language->getMessage('settings.icons.groups.common.rounding.value') ?>
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-slider',
                                            'v-model' => 'properties.background.rounding.value',
                                            'v-bind:min' => '0',
                                            'v-bind:max' => '100',
                                            'v-bind:step' => '1',
                                            'hide-details' => 'auto'
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-text-field',
                                            'v-model' => 'properties.background.rounding.value',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                        <div class="intec-editor-settings-field">
                            <div class="intec-editor-settings-field-name">
                                <?= $language->getMessage('settings.icons.groups.common.rounding.top') ?>
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-slider',
                                            'v-model' => 'properties.background.rounding.top.value',
                                            'v-bind:min' => '0',
                                            'v-bind:max' => '100',
                                            'v-bind:step' => '1',
                                            'hide-details' => 'auto'
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-text-field',
                                            'v-model' => 'properties.background.rounding.top.value',
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
                                <?= $language->getMessage('settings.icons.groups.common.rounding.left') ?>
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-slider',
                                            'v-model' => 'properties.background.rounding.left.value',
                                            'v-bind:min' => '0',
                                            'v-bind:max' => '100',
                                            'v-bind:step' => '1',
                                            'hide-details' => 'auto'
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-text-field',
                                            'v-model' => 'properties.background.rounding.left.value',
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
                                <?= $language->getMessage('settings.icons.groups.common.rounding.right') ?>
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-slider',
                                            'v-model' => 'properties.background.rounding.right.value',
                                            'v-bind:min' => '0',
                                            'v-bind:max' => '100',
                                            'v-bind:step' => '1',
                                            'hide-details' => 'auto'
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-text-field',
                                            'v-model' => 'properties.background.rounding.right.value',
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
                                <?= $language->getMessage('settings.icons.groups.common.rounding.bottom') ?>
                            </div>
                            <div class="intec-editor-settings-field-content">
                                <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                    <div class="intec-editor-grid-item">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-slider',
                                            'v-model' => 'properties.background.rounding.bottom.value',
                                            'v-bind:min' => '0',
                                            'v-bind:max' => '100',
                                            'v-bind:step' => '1',
                                            'hide-details' => 'auto'
                                        ]) ?>
                                    </div>
                                    <div class="intec-editor-grid-item-3">
                                        <?= Html::tag('component', null, [
                                            'class' => 'v-input-theme-settings',
                                            'is' => 'v-text-field',
                                            'v-model' => 'properties.background.rounding.bottom.value',
                                            'hide-details' => 'auto',
                                            'solo' => true,
                                            'flat' => true
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>
                    <div class="intec-editor-settings-field">
                        <div class="intec-editor-settings-field-name">
                            <?= $language->getMessage('settings.icons.groups.common.opacity') ?>
                        </div>
                        <div class="intec-editor-settings-field-content">
                            <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-12">
                                <div class="intec-editor-grid-item">
                                    <?= Html::tag('component', null, [
                                        'class' => 'v-input-theme-settings',
                                        'is' => 'v-slider',
                                        'v-model' => 'properties.background.opacity',
                                        'v-bind:min' => '0',
                                        'v-bind:max' => '100',
                                        'v-bind:step' => '1',
                                        'hide-details' => 'auto'
                                    ]) ?>
                                </div>
                                <div class="intec-editor-grid-item-3">
                                    <?= Html::tag('component', null, [
                                        'class' => 'v-input-theme-settings',
                                        'is' => 'v-text-field',
                                        'v-model' => 'properties.background.opacity',
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
    </div>
</div>