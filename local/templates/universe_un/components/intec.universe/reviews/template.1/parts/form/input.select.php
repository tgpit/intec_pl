<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
<?php return function (&$arField) { ?>
    <?= Html::beginTag('label', [
        'class' => Html::cssClassFromArray([
            'intec-ui-form-field' => [
                '' => true,
                'required' => $arField['REQUIRED']
            ]
        ], true)
    ]) ?>
    <span class="intec-ui-form-field-title">
        <?= $arField['CAPTION'] ?>
    </span>
    <span class="intec-ui-form-field-content">
        <?= Html::dropDownList($arField['NAME'], $arField['VALUE'], $arField['OPTIONS'], [
            'class' => [
                'intec-ui' => [
                    '',
                    'control-input',
                    'size-2',
                    'mod-block',
                    'mod-round-2'
                ]
            ],
            'data-error' => !empty($arField['ERROR']) ? 'true' : 'false'
        ]) ?>
    </span>
    <?= Html::endTag('label') ?>
<?php } ?>