<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;use intec\core\helpers\Html;

return function ($sKey, $arProperty) { ?>
    <div class="system-settings-property-value">
        <?= Html::hiddenInput('properties['.$sKey.']', 0) ?>
        <label class="intec-ui intec-ui-control-switch">
            <?= Html::checkbox('properties['.$sKey.']', $arProperty['value'], [
                'data' => [
                    'role' => 'property.input'
                ]
            ]) ?>
            <span class="intec-ui-part-selector"></span>
            <?php if ($arProperty['title'] === 'inner') { ?>
                <?php $sName = ArrayHelper::getValue($arProperty, 'name') ?>
                <span class="intec-ui-part-content">
                    <?= $sName ?>
                </span>
            <?php } ?>
        </label>
    </div>
<?php };