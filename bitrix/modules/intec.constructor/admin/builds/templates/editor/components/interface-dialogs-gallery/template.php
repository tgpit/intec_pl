<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\web\assets\vue\application\Component;

/**
 * @var Component $this
 */

$interfaceDialogsConfirm = $this->getComponent('interface-dialogs-confirm');

?>
<?= Html::beginTag('component', [
    'is' => 'v-dialog',
    'content-class' => 'intec-editor-dialog intec-editor-dialog-gallery intec-editor-dialog-theme-default',
    'persistent' => true,
    'max-width' => '800',
    'v-bind:retain-focus' => 'false',
    'v-model' => 'display'
]) ?>
    <div class="intec-editor-dialog-wrapper">
        <div class="intec-editor-dialog-controls">
            <div class="intec-editor-grid intec-editor-grid-nowrap intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                <div v-if="tab !== null" class="intec-editor-grid-item-auto">
                    <?= Html::beginTag('div', [
                        'class' => 'intec-editor-dialog-control',
                        'data-control' => 'button.icon.text',
                        'v-on:click' => '!isUploading ? selectTab(null) : null',
                        'v-bind:data-active' => '!isUploading ? "true" : "false"'
                    ]) ?>
                    <div class="intec-editor-dialog-control-content">
                        <div data-control-part="icon" class="intec-editor-dialog-control-content-part">
                            <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M15.75 8.24998L4.81045 8.24998L8.7802 4.28023L7.7197 3.21973L1.93945 8.99998L7.7197 14.7802L8.7802 13.7197L4.81045 9.74998L15.75 9.74998L15.75 8.24998Z" />
                            </svg>
                        </div>
                        <div data-control-part="text" class="intec-editor-dialog-control-content-part">
                            {{ $root.$localization.getMessage('dialogs.gallery.control.back') }}
                        </div>
                    </div>
                    <?= Html::endTag('div') ?>
                </div>
                <div v-else class="intec-editor-grid-item-auto">
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
                <div class="intec-editor-grid-item-auto">
                    <div class="intec-editor-dialog-control" data-control="button.icon" v-on:click="close">
                        <i class="fal fa-times"></i>
                    </div>
                </div>
            </div>
        </div>
        <template v-if="tab === null">
            <div class="intec-editor-dialog-title" data-align="center">
                {{ $root.$localization.getMessage('dialogs.gallery.title') }}
            </div>
            <div class="intec-editor-dialog-content">
                <template v-if="isRefreshing">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-editor-dialog-content-wrapper',
                            'intec-editor-grid' => [
                                '',
                                'nowrap',
                                'o-vertical',
                                'a-h-center',
                                'a-v-center'
                            ]
                        ]
                    ]) ?>
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-dialog-preload">
                            <?= Html::tag('component', null, [
                                'is' => 'v-progress-circular',
                                'width' => '3',
                                'size' => '120',
                                'indeterminate' => true
                            ]) ?>
                        </div>
                    </div>
                    <?= Html::endTag('div') ?>
                </template>
                <template v-else>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-editor-dialog-content-wrapper',
                            'intec-editor-grid' => [
                                '',
                                'nowrap',
                                'o-vertical'
                            ]
                        ]
                    ]) ?>
                    <div class="intec-editor-grid-item-auto">
                        <div class="intec-editor-dialog-content-part">
                            <?= Html::tag('component', null, [
                                'class' => 'v-input-theme-dialog-default',
                                'is' => 'v-text-field',
                                'v-model' => 'filter',
                                'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.gallery.search")',
                                'hide-details' => 'auto',
                                'solo' => true,
                                'flat' => true
                            ]) ?>
                        </div>
                        <div class="intec-editor-dialog-content-part intec-editor-dialog-gallery-tabs">
                            <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-h-8">
                                <div class="intec-editor-grid-item-2">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-dialog-gallery-tabs-button',
                                        'v-ripple' => '{"class": "white--text"}',
                                        'v-on:click' => 'selectTab("upload.file")'
                                    ]) ?>
                                    <span class="intec-editor-dialog-gallery-tabs-content">
                                                    <span class="intec-editor-dialog-gallery-tabs-content-icon intec-editor-dialog-gallery-tabs-content-part">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M15.8337 10.8337H10.8337V15.8337H9.16699V10.8337H4.16699V9.16699H9.16699V4.16699H10.8337V9.16699H15.8337V10.8337Z" />
                                                        </svg>
                                                    </span>
                                                    <span class="intec-editor-dialog-gallery-tabs-content-text intec-editor-dialog-gallery-tabs-content-part">
                                                        {{ $root.$localization.getMessage('dialogs.gallery.tabs.add.file') }}
                                                    </span>
                                                </span>
                                    <?= Html::endTag('button') ?>
                                </div>
                                <div class="intec-editor-grid-item-2">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-dialog-gallery-tabs-button',
                                        'v-ripple' => '{"class": "white--text"}',
                                        'v-on:click' => 'selectTab("upload.url")'
                                    ]) ?>
                                    <span class="intec-editor-dialog-gallery-tabs-content">
                                                    <span class="intec-editor-dialog-gallery-tabs-content-icon intec-editor-dialog-gallery-tabs-content-part">
                                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                            <path d="M7.05392 9.41089C7.99809 8.46672 9.64476 8.46672 10.5889 9.41089L11.1781 10.0001L12.3564 8.82172L11.7673 8.23255C10.9814 7.44589 9.93476 7.01172 8.82142 7.01172C7.70809 7.01172 6.66142 7.44589 5.87559 8.23255L4.10725 10.0001C3.3275 10.7823 2.88965 11.8418 2.88965 12.9463C2.88965 14.0508 3.3275 15.1103 4.10725 15.8926C4.49382 16.2797 4.95306 16.5866 5.4586 16.7957C5.96414 17.0047 6.50602 17.1119 7.05309 17.1109C7.60031 17.112 8.14235 17.005 8.64805 16.7959C9.15375 16.5868 9.61312 16.2798 9.99976 15.8926L10.5889 15.3034L9.41059 14.1251L8.82142 14.7142C8.35181 15.1817 7.71615 15.4442 7.05351 15.4442C6.39086 15.4442 5.7552 15.1817 5.28559 14.7142C4.81767 14.2448 4.55493 13.6091 4.55493 12.9463C4.55493 12.2835 4.81767 11.6478 5.28559 11.1784L7.05392 9.41089Z" />
                                                            <path d="M10.0002 4.10794L9.41106 4.69711L10.5894 5.87544L11.1786 5.28628C11.6482 4.81877 12.2838 4.5563 12.9465 4.5563C13.6091 4.5563 14.2448 4.81877 14.7144 5.28628C15.1823 5.75567 15.4451 6.39142 15.4451 7.05419C15.4451 7.71697 15.1823 8.35272 14.7144 8.82211L12.9461 10.5896C12.0019 11.5338 10.3552 11.5338 9.41106 10.5896L8.82189 10.0004L7.64355 11.1788L8.23272 11.7679C9.01855 12.5546 10.0652 12.9888 11.1786 12.9888C12.2919 12.9888 13.3386 12.5546 14.1244 11.7679L15.8927 10.0004C16.6725 9.21818 17.1103 8.15871 17.1103 7.05419C17.1103 5.94968 16.6725 4.89021 15.8927 4.10794C15.1107 3.32778 14.0511 2.88965 12.9465 2.88965C11.8418 2.88965 10.7823 3.32778 10.0002 4.10794V4.10794Z" />
                                                        </svg>
                                                    </span>
                                                    <span class="intec-editor-dialog-gallery-tabs-content-text intec-editor-dialog-gallery-tabs-content-part">
                                                        {{ $root.$localization.getMessage('dialogs.gallery.tabs.add.url') }}
                                                    </span>
                                                </span>
                                    <?= Html::endTag('button') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="intec-editor-grid-item">
                        <div style="height: 100%; padding-top: 16px">
                            <component ref="scrollbar" is="vue-scroll" v-on:handle-scroll="handleScroll" v-bind:ops="scrollbarSettings">
                                <div class="intec-editor-dialog-content-part">
                                    <template v-if="filteredItems.length === 0">
                                        <template v-if="isFiltered">
                                            <div class="intec-editor-dialog-gallery-search-not-found">
                                                {{ $root.$localization.getMessage('dialogs.gallery.search.not.found', {'query': filter}) }}
                                            </div>
                                        </template>
                                        <template v-else>
                                            <div>
                                                {{ $root.$localization.getMessage('dialogs.gallery.search.empty') }}
                                            </div>
                                        </template>
                                    </template>
                                    <template v-else>
                                        <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-10">
                                            <div class="intec-editor-grid-item-3" v-for="item in filteredItems">
                                                <div class="intec-editor-dialog-gallery-item" v-bind:data-uploaded="isUploadedFile(item.name) ? 'true' : 'false'">
                                                    <div class="intec-editor-dialog-gallery-item-view">
                                                        <div class="intec-editor-dialog-gallery-item-view-background"></div>
                                                        <?= Html::tag('div', null, [
                                                            'class' => 'intec-editor-dialog-gallery-item-view-picture',
                                                            'v-on:click' => 'selectItem(item)',
                                                            'v-bind:style' => '{
                                                                "background-image": "url(\"" + item.path + "\")"
                                                            }'
                                                        ]) ?>
                                                        <div class="intec-editor-dialog-gallery-item-view-action">
                                                            <?= $interfaceDialogsConfirm->begin([
                                                                'v-on:confirm' => 'deleteItem(item)'
                                                            ]) ?>
                                                                <template v-slot:activator="slot">
                                                                    <div class="intec-editor-dialog-gallery-item-view-action-item" v-ripple v-on="slot.on">
                                                                        <svg width="18" height="18" viewBox="0 0 18 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                            <path d="M4.5 14.25C4.5 15.075 5.175 15.75 6 15.75H12C12.825 15.75 13.5 15.075 13.5 14.25V5.25H4.5V14.25ZM14.25 3H11.625L10.875 2.25H7.125L6.375 3H3.75V4.5H14.25V3Z" />
                                                                        </svg>
                                                                    </div>
                                                                </template>
                                                                <template v-slot:title="slot">
                                                                    {{ $root.$localization.getMessage('dialogs.gallery.delete.confirm.title') }}
                                                                </template>
                                                                <template v-slot:description="slot">
                                                                    {{ $root.$localization.getMessage('dialogs.gallery.delete.confirm.description') }}
                                                                </template>
                                                            <?= $interfaceDialogsConfirm->end() ?>
                                                        </div>
                                                    </div>
                                                    <div class="intec-editor-dialog-gallery-item-name">
                                                        <div class="intec-editor-dialog-gallery-item-name-content">
                                                            {{ item.name }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </component>
                        </div>
                    </div>
                    <?= Html::endTag('div') ?>
                </template>
            </div>
        </template>
        <template v-else-if="tab === 'upload.file'">
            <div class="intec-editor-dialog-title" data-align="center">
                {{ $root.$localization.getMessage('dialogs.gallery.upload.file') }}
            </div>
            <div class="intec-editor-dialog-content">
                <div class="intec-editor-dialog-content-part">
                    <div class="intec-editor-dialog-gallery-upload-file">
                        <?= Html::fileInput('gallery-files', null, [
                            'id' => 'gallery-upload-files',
                            'class' => 'intec-editor-dialog-gallery-upload-file-input',
                            'multiple' => true,
                            'accept' => 'image/*',
                            'v-bind:disabled' => 'limitFiles || isUploading',
                            'v-on:change' => 'inputChange'
                        ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => 'intec-editor-dialog-gallery-upload-file-drop',
                            'v-on:dragenter' => 'dragEnter',
                            'v-on:dragover' => 'dragOver',
                            'v-on:drop' => 'drop'
                        ]) ?>
                        <template v-if="selectedFiles.length">
                            <div class="intec-editor-dialog-gallery-upload-file-selected">
                                <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-8">
                                    <?= Html::beginTag('div', [
                                        'v-bind:class' => '{
                                            "intec-editor-dialog-gallery-upload-file-selected-item": true,
                                            "intec-editor-grid-item-3": selectedFiles.length < 10,
                                            "intec-editor-grid-item-6": selectedFiles.length > 9
                                        }',
                                        'v-for' => '(file, index) in selectedFiles'
                                    ]) ?>
                                    <div class="intec-editor-dialog-gallery-upload-file-selected-item-content">
                                        <div class="intec-editor-dialog-gallery-upload-file-selected-item-background"></div>
                                        <?= Html::tag('div', null, [
                                            'class' => 'intec-editor-dialog-gallery-upload-file-selected-item-picture',
                                            'v-bind:style' => '{
                                                "background-image": "url(" + file.image + ")"
                                            }'
                                        ]) ?>
                                    </div>
                                    <?= Html::beginTag('div', [
                                        'class' => 'intec-editor-dialog-gallery-upload-file-selected-item-action',
                                        'v-on:click' => 'removeSelectedFile(index)'
                                    ]) ?>
                                    <svg width="12" height="12" viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M3 9.5C3 10.05 3.45 10.5 4 10.5H8C8.55 10.5 9 10.05 9 9.5V3.5H3V9.5ZM9.5 2H7.75L7.25 1.5H4.75L4.25 2H2.5V3H9.5V2Z" />
                                    </svg>
                                    <?= Html::endTag('div') ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            </div>
                        </template>
                        <?= Html::beginTag('label', [
                            'class' => 'intec-editor-dialog-gallery-upload-file-dialog',
                            'for' => 'gallery-upload-files',
                            'v-ripple' => true,
                            'v-bind:data-limit' => 'limitFiles ? "true" : "false"'
                        ]) ?>
                        {{ $root.$localization.getMessage('dialogs.gallery.upload.file.input') }}
                        <?= Html::endTag('label') ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-editor-dialog-gallery-upload-file-uploading',
                                'intec-editor-grid' => [
                                    '',
                                    'a-v-center',
                                    'a-h-center'
                                ]
                            ],
                            'v-bind:data-uploading' => 'isUploading ? "true" : "false"'
                        ]) ?>
                        <div class="intec-editor-grid-item-auto">
                            <?= Html::tag('component', null, [
                                'is' => 'v-progress-circular',
                                'v-if' => 'isUploading',
                                'width' => '3',
                                'size' => '80',
                                'color' => '#3A86FF',
                                'indeterminate' => true
                            ]) ?>
                        </div>
                        <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                        <div class="intec-editor-dialog-gallery-upload-file-buttons">
                            <?= Html::beginTag('button', [
                                'class' => 'intec-editor-dialog-gallery-upload-file-button',
                                'data-view' => 'default',
                                'v-bind:disabled' => '!selectedFiles.length',
                                'v-on:click' => 'uploadFiles'
                            ]) ?>
                            {{ $root.$localization.getMessage('dialogs.gallery.upload.file.button.upload') }}
                            <?= Html::endTag('button') ?>
                            <?= Html::beginTag('button', [
                                'class' => 'intec-editor-dialog-gallery-upload-file-button',
                                'data-view' => 'light',
                                'v-bind:disabled' => '!selectedFiles.length',
                                'v-on:click' => 'removeSelectedFiles'
                            ]) ?>
                            {{ $root.$localization.getMessage('dialogs.gallery.upload.file.button.clear') }}
                            <?= Html::endTag('button') ?>
                        </div>
                        <div class="intec-editor-dialog-gallery-upload-file-tabs">
                            <?= Html::beginTag('div', [
                                'class' => 'intec-editor-dialog-gallery-upload-file-tabs-item',
                                'v-bind:data-active' => '!isUploading ? "true" : "false"',
                                'v-on:click' => '!isUploading ? selectTab("upload.url") : null'
                            ]) ?>
                            <div class="intec-editor-dialog-gallery-tabs-content">
                                <div class="intec-editor-dialog-gallery-tabs-content-icon intec-editor-dialog-gallery-tabs-content-part">
                                    <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7.05392 9.41089C7.99809 8.46672 9.64476 8.46672 10.5889 9.41089L11.1781 10.0001L12.3564 8.82172L11.7673 8.23255C10.9814 7.44589 9.93476 7.01172 8.82142 7.01172C7.70809 7.01172 6.66142 7.44589 5.87559 8.23255L4.10725 10.0001C3.3275 10.7823 2.88965 11.8418 2.88965 12.9463C2.88965 14.0508 3.3275 15.1103 4.10725 15.8926C4.49382 16.2797 4.95306 16.5866 5.4586 16.7957C5.96414 17.0047 6.50602 17.1119 7.05309 17.1109C7.60031 17.112 8.14235 17.005 8.64805 16.7959C9.15375 16.5868 9.61312 16.2798 9.99976 15.8926L10.5889 15.3034L9.41059 14.1251L8.82142 14.7142C8.35181 15.1817 7.71615 15.4442 7.05351 15.4442C6.39086 15.4442 5.7552 15.1817 5.28559 14.7142C4.81767 14.2448 4.55493 13.6091 4.55493 12.9463C4.55493 12.2835 4.81767 11.6478 5.28559 11.1784L7.05392 9.41089Z" />
                                        <path d="M10.0002 4.10794L9.41106 4.69711L10.5894 5.87544L11.1786 5.28628C11.6482 4.81877 12.2838 4.5563 12.9465 4.5563C13.6091 4.5563 14.2448 4.81877 14.7144 5.28628C15.1823 5.75567 15.4451 6.39142 15.4451 7.05419C15.4451 7.71697 15.1823 8.35272 14.7144 8.82211L12.9461 10.5896C12.0019 11.5338 10.3552 11.5338 9.41106 10.5896L8.82189 10.0004L7.64355 11.1788L8.23272 11.7679C9.01855 12.5546 10.0652 12.9888 11.1786 12.9888C12.2919 12.9888 13.3386 12.5546 14.1244 11.7679L15.8927 10.0004C16.6725 9.21818 17.1103 8.15871 17.1103 7.05419C17.1103 5.94968 16.6725 4.89021 15.8927 4.10794C15.1107 3.32778 14.0511 2.88965 12.9465 2.88965C11.8418 2.88965 10.7823 3.32778 10.0002 4.10794V4.10794Z" />
                                    </svg>
                                </div>
                                <div class="intec-editor-dialog-gallery-tabs-content-text intec-editor-dialog-gallery-tabs-content-part">
                                    {{ $root.$localization.getMessage('dialogs.gallery.tabs.add.url') }}
                                </div>
                            </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                </div>
            </div>
        </template>
        <template v-else-if="tab === 'upload.url'">
            <div class="intec-editor-dialog-content intec-editor-grid intec-editor-grid-a-v-center">
                <div class="intec-editor-grid-item">
                    <div class="intec-editor-dialog-content-part">
                        <div class="intec-editor-dialog-title" data-align="center">
                            {{ $root.$localization.getMessage('dialogs.gallery.upload.url') }}
                        </div>
                        <div class="intec-editor-dialog-gallery-description">
                            {{ $root.$localization.getMessage('dialogs.gallery.upload.url.description') }}
                        </div>
                        <div class="intec-editor-dialog-gallery-upload-url">
                            <div class="intec-editor-grid intec-editor-grid-wrap intec-editor-grid-i-v-6">
                                <div v-for="(link, index) in uploadLinks" class="intec-editor-grid-item-1">
                                    <div class="intec-editor-grid intec-editor-grid-a-v-center intec-editor-grid-i-h-5">
                                        <div class="intec-editor-grid-item">
                                            <div class="intec-editor-dialog-gallery-upload-url-input">
                                                <?= Html::tag('component', null, [
                                                    'class' => 'v-input-theme-dialog-default',
                                                    'is' => 'v-text-field',
                                                    'v-model' => 'link.value',
                                                    'v-bind:key' => 'index',
                                                    'v-bind:disabled' => 'isUploading',
                                                    'v-bind:placeholder' => '$root.$localization.getMessage("dialogs.gallery.upload.url.field")',
                                                    'hide-details' => 'auto',
                                                    'solo' => true,
                                                    'flat' => true
                                                ]) ?>
                                            </div>
                                        </div>
                                        <div class="intec-editor-grid-item-auto">
                                            <div class="intec-editor-dialog-gallery-upload-url-add-wrap">
                                                <?= Html::beginTag('button', [
                                                    'class' => 'intec-editor-dialog-gallery-upload-url-add',
                                                    'v-ripple' => '{"class": "white--text"}',
                                                    'v-if' => 'index === uploadLinks.length - 1',
                                                    'v-bind:disabled' => 'isUploading || uploadLinks.length >= 8',
                                                    'v-on:click' => '!isUploading && uploadLinks.length < 8 ? addUploadLink() : null'
                                                ]) ?>
                                                <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M15.8337 10.8337H10.8337V15.8337H9.16699V10.8337H4.16699V9.16699H9.16699V4.16699H10.8337V9.16699H15.8337V10.8337Z" />
                                                </svg>
                                                <?= Html::endTag('button') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="intec-editor-grid-item-1" style="text-align: center; margin-top: 20px;">
                                    <?= Html::beginTag('button', [
                                        'class' => 'intec-editor-dialog-gallery-upload-url-button',
                                        'v-ripple' => true,
                                        'v-bind:disabled' => 'isUploading',
                                        'v-on:click' => 'uploadFilesByLinks'
                                    ]) ?>
                                    <template v-if="!isUploading">
                                        {{ $root.$localization.getMessage('dialogs.gallery.upload.url.button') }}
                                    </template>
                                    <template v-else>
                                        <?= Html::tag('v-progress-circular', null, [
                                            'color' => '#64728C',
                                            'indeterminate' => true,
                                            'size' => 28,
                                            'width' => 3
                                        ]) ?>
                                    </template>
                                    <?= Html::endTag('button') ?>
                                </div>
                            </div>
                            <div class="intec-editor-dialog-gallery-upload-url-tabs">
                                <?= Html::beginTag('div', [
                                    'class' => 'intec-editor-dialog-gallery-upload-url-tabs-item',
                                    'v-bind:data-active' => '!isUploading ? "true" : "false"',
                                    'v-on:click' => '!isUploading ? selectTab("upload.file") : null'
                                ]) ?>
                                <div class="intec-editor-dialog-gallery-tabs-content">
                                    <div class="intec-editor-dialog-gallery-tabs-content-icon intec-editor-dialog-gallery-tabs-content-part">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M15.8337 10.8337H10.8337V15.8337H9.16699V10.8337H4.16699V9.16699H9.16699V4.16699H10.8337V9.16699H15.8337V10.8337Z" />
                                        </svg>
                                    </div>
                                    <div class="intec-editor-dialog-gallery-tabs-content-text intec-editor-dialog-gallery-tabs-content-part">
                                        {{ $root.$localization.getMessage('dialogs.gallery.tabs.add.file') }}
                                    </div>
                                </div>
                                <?= Html::endTag('div') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </template>
    </div>
<?= Html::endTag('component') ?>