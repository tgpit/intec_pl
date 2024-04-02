<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true ) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 */

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

?>
<div id="<?= $sTemplateId ?>" class="ns-bitrix c-form-result-new c-form-result-new-template-1 intec-ui-form">
    <?php if ($arResult['isFormNote'] === 'Y') { ?>
        <div class="form-result-new-message form-result-new-message-note">
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center">
                <div class="intec-grid-item-1 intec-grid intec-grid-a-v-center intec-grid-i-10">
                    <div class="form-result-new-message-note-icon intec-grid-item-auto intec-cl-svg">
                        <?=FileHelper::getFileData(__DIR__."/svg/success.svg");?>
                    </div>
                    <div class="form-result-new-message-note-text intec-grid-item">
                        <?= $arResult['FORM_NOTE'] ?>
                    </div>
                </div>
                <div class="form-result-new-message-note-buttons intec-grid-item-1" data-role="buttons">
                    <?= Html::tag('button', Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_1_BUTTONS_CLOSE'), [
                        'class' => [
                            'form-result-new-message-note-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current'
                            ]
                        ],
                        'data' => [
                            'role' => 'closeButton'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <?= $arResult['FORM_HEADER'] ?>
            <?php if ($arResult["isFormErrors"] == 'Y') { ?>
                <div class="form-result-new-message intec-ui intec-ui-control-alert intec-ui-scheme-red">
                    <?= $arResult['FORM_ERRORS_TEXT'] ?>
                </div>
            <?php } ?>
            <?php if (!empty($arResult['arForm']['DESCRIPTION'])) { ?>
                <div class="form-result-new-description">
                    <?= $arResult['arForm']['DESCRIPTION'] ?>
                </div>
            <?php } ?>

            <?php if (!empty($arResult['QUESTIONS']) || $arResult['isUseCaptcha']) { ?>
                <div class="form-result-new-fields intec-ui-form-fields">
                    <?php foreach ($arResult['QUESTIONS'] as $question) { ?>
                        <?= Html::beginTag('label', [
                            'class' => Html::cssClassFromArray([
                                'form-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arResult['QUESTIONS']['TEXT']['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <span class="form-result-new-field-title intec-ui-form-field-title">
                                <?= $question['CAPTION'] ?>
                                <?= $question['IS_INPUT_CAPTION_IMAGE'] == 'Y' ? '<br />'. $question['IMAGE']['HTML_CODE'] : '' ?>
                            </span>
                            <span class="form-result-new-field-content intec-ui-form-field-content">
                                <?= $question['HTML_CODE'] ?>
                            </span>
                        <?= Html::endTag('label') ?>
                    <?php } ?>
                    <?php if ($arResult['isUseCaptcha'] == 'Y') { ?>
                        <div class="form-result-new-field form-result-new-field-captcha intec-ui-form-field intec-ui-form-field-required">
                            <div class="form-result-new-field-title intec-ui-form-field-title">
                                <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_1_FIELDS_CAPTCHA') ?>
                            </div>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <?= Html::hiddenInput('captcha_sid', $arResult['CAPTCHACode']) ?>
                                <div class="form-result-new-captcha intec-grid intec-grid-nowrap intec-grid-i-h-5">
                                    <div class="form-result-new-captcha-image intec-grid-item-auto">
                                        <img src="/bitrix/tools/captcha.php?captcha_sid=<?= Html::encode($arResult['CAPTCHACode']) ?>" width="180" height="40" />
                                    </div>
                                    <div class="form-result-new-captcha-input intec-grid-item">
                                        <input type="text" class=" intec-ui intec-ui-control-input intec-ui-mod-block intec-ui-mod-round-3 intec-ui-size-2" name="captcha_word" size="30" maxlength="50" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if ($arResult['CONSENT']['SHOW']) { ?>
                <div class="form-result-new-consent">
                    <label class="intec-ui intec-ui-control-switch intec-ui-scheme-current">
                        <?= Html::checkbox('licenses_popup', $arResult['CONSENT']['CHECKED'], [
                            'value' => 'Y'
                        ]) ?>
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content"><?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_1_CONSENT', [
                            '#URL#' => $arResult['CONSENT']['URL']
                        ]) ?></span>
                    </label>
                </div>
            <?php } ?>
            <div class="form-result-new-buttons">
                <div class="intec-grid-item-auto">
                    <?= Html::hiddenInput('web_form_sent', 'Y') ?>
                    <?= Html::submitButton(!empty($arResult['arForm']['BUTTON']) ? $arResult['arForm']['BUTTON'] : Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_1_BUTTONS_SUBMIT'), [
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current'
                            ]
                        ],
                        'name' => 'web_form_submit',
                        'value' => 'Y',
                        'disabled' => Type::toInteger($arResult['F_RIGHT']) < 10 || $arResult['CONSENT']['SHOW'] ? 'disabled' : null
                    ]) ?>
                </div>
            </div>
        <?= $arResult['FORM_FOOTER'] ?>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>