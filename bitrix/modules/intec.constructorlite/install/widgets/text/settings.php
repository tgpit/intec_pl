<?php

use intec\core\helpers\Html;
use intec\constructor\structure\Widget;

/**
 * @var Widget $this
 */

$language = $this->getLanguage();

?>
<div class="intec-editor-settings-group">
    <div class="intec-editor-settings-group-name">
        <?= $language->getMessage('settings.groups.text.name') ?>
    </div>
    <div class="intec-editor-settings-group-content">
        <div class="intec-editor-settings-fields">
            <div class="intec-editor-settings-field">
                <div class="intec-editor-settings-field-name">
                    <?= $language->getMessage('settings.groups.text.fields.font.name') ?>
                </div>
                <div class="intec-editor-settings-field-content">
                    <?= Html::tag('v-select', null, [
                        'class' => 'v-input-theme-settings',
                        'v-model' => 'properties.font',
                        'v-bind:items' => 'fonts',
                        'v-bind:item-text' => 'function (item) { return item.text }',
                        'v-bind:item-value' => 'function (item) { return item.value }',
                        'v-bind:menu-props' => '{"contentClass": "v-menu-theme-settings", "dark": true}',
                        'hide-details' => 'auto',
                        'dark' => true,
                        'solo' => true,
                        'flat' => true
                    ]) ?>
                </div>
            </div>
            <div class="intec-editor-settings-field">
                <div class="intec-editor-settings-field-name">
                    <?= $language->getMessage('settings.groups.text.fields.text.name') ?>
                </div>
                <div class="intec-editor-settings-field-content">
                    <textarea ref="text"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
