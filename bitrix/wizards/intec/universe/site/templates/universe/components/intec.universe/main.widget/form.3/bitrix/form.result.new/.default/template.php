<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$sReqSign = ' *';

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-form-result-new',
        'c-form-result-new-form-3'
    ],
    'data-theme' => $arVisual['THEME'],
    'data-background' => $arVisual['BACKGROUND']['USE'] ? 'true' : null
]) ?>
    <?php if ($arVisual['BACKGROUND']['USE']) { ?>
        <?= Html::tag('div', '', [
            'class' => Html::cssClassFromArray([
                'widget-form-result-new-background' => true,
                'intec-cl-background' => $arVisual['BACKGROUND']['COLOR']['VALUE'] == 'theme'
            ], true),
            'style' => [
                'background-color' => $arVisual['BACKGROUND']['COLOR']['VALUE'] == 'custom' &&
                    !empty($arVisual['BACKGROUND']['COLOR']['CUSTOM']) ?
                    $arVisual['BACKGROUND']['COLOR']['CUSTOM'] : null,
                'opacity' => $arVisual['BACKGROUND']['OPACITY']
            ]
        ]) ?>
    <?php } ?>
    <div class="widget-form-result-new-body intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arVisual['TITLE']['SHOW']) { ?>
                <div class="widget-form-result-new-title" data-align="<?= $arVisual['TITLE']['POSITION'] ?>">
                    <?= $arResult['FORM_TITLE'] ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-form-result-new-description" data-align="<?= $arVisual['DESCRIPTION']['POSITION'] ?>">
                    <?= $arResult['FORM_DESCRIPTION'] ?>
                </div>
            <?php } ?>
            <?php if ($arResult['isFormNote'] === 'Y') { ?>
                <div class="widget-form-result-new-sent" data-align="<?= $arVisual['TITLE']['POSITION'] ?>">
                    <?= $arResult['FORM_NOTE'] ?>
                </div>
            <?php } else { ?>
                <?= $arResult['FORM_HEADER'] ?>
                <div class="<?= Html::cssClassFromArray([
                    'widget-form-result-new-fields',
                    'intec-grid' => [
                        '',
                        'wrap',
                        'a-v-end',
                        'i-h-20'
                    ]
                ]) ?>">
                    <?php if ($arResult['isFormErrors'] == 'Y') { ?>
                        <div class="widget-form-result-new-error intec-grid-item-1">
                            <?= $arResult['FORM_ERRORS_TEXT'] ?>
                        </div>
                    <?php } ?>
                    <?php foreach ($arResult['QUESTIONS'] as $arQuestion) {

                        $bRequired = $arQuestion['REQUIRED'] == 'Y';

                        $sCaption = $bRequired ? $arQuestion['CAPTION'].$sReqSign : $arQuestion['CAPTION'];
                        $sId = $arQuestion['STRUCTURE'][0]['ID'];
                        $sType = $arQuestion['STRUCTURE'][0]['FIELD_TYPE'];
                        $sName = 'form_'.$sType.'_'.$sId;
                        $sValue = Html::encode(Core::$app->request->post($sName));

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-form-result-new-field-wrap' => true,
                                'intec-grid-item-1' => $sType == 'textarea',
                                'intec-grid-item-3' => $sType == 'text' || $sType == 'email',
                                'intec-grid-item-600-1' => true
                            ], true),
                        ]) ?>
                            <div class="widget-form-result-new-field" data-role="field" data-active="<?= !empty($sValue) ? "true" : "false" ?>">
                                <label for="<?= $sName ?>">
                                    <?= $sCaption ?>
                                </label>
                                <?php if ($sType == 'text' || $sType == 'email') { ?>
                                    <?= Html::input($sType, $sName, $sValue, [
                                        'class' => 'widget-form-result-new-field-input',
                                        'data-role' => 'input',
                                        'required' => $bRequired ? '' : null
                                    ]) ?>
                                <?php } elseif ($sType == 'textarea') { ?>
                                    <?= Html::textarea($sName, $sValue, [
                                        'class' => 'widget-form-result-new-field-input',
                                        'data-role' => 'input',
                                        'required' => $bRequired ? '' : null
                                    ]) ?>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
                <?php if ($arResult['isUseCaptcha'] == 'Y') { ?>
                    <div class="widget-form-result-new-captcha-wrap" data-align="<?= $arVisual['BUTTON']['POSITION'] ?>">
                        <div class="widget-form-result-new-captcha">
                            <div class="widget-form-result-new-captcha-title">
                                <?= Loc::getMessage('C_WIDGET_FORM_3_WEB_FORM_CAPTCHA_TITLE') ?>
                            </div>
                            <?= Html::hiddenInput('captcha_sid', Html::encode($arResult['CAPTCHACode'])) ?>
                            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.Html::encode($arResult['CAPTCHACode']), [
                                'width' => 180,
                                'height' => 40
                            ]) ?>
                            <div class="clear"></div>
                            <?= Html::input('text', 'captcha_word', null, [
                                'class' => 'widget-form-result-new-field-input widget-form-result-new-captcha-input',
                                'required' => '',
                                'placeholder' => Loc::getMessage('C_WIDGET_FORM_3_WEB_FORM_CAPTCHA_PLACEHOLDER')
                            ]) ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="widget-form-result-new-submit-wrap">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-20 intec-grid-i-v-16">
                        <div class="widget-form-result-new-submit-container intec-grid-item-auto intec-grid-item-500-1">
                            <?= Html::submitButton($arResult['arForm']['BUTTON'], [
                                'class' => [
                                    'widget-form-result-new-submit',
                                    'intec-cl-text',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'size-4',
                                        'mod-round-half'
                                    ]
                                ],
                                'name' => 'web_form_submit',
                                'value' => 'Y',
                                'disabled' => $arResult['F_RIGHT'] < 10
                            ]) ?>
                        </div>
                        <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                            <div class="widget-form-result-new-consent-container intec-grid-item intec-grid-item-500-1">
                                <div class="widget-form-result-new-consent">
                                    <label class="intec-grid intec-grid-inline intec-grid-a-v-center intec-grid-i-h-6">
                                        <span class="intec-grid-item-auto">
                                            <span class="widget-form-result-new-consent-checkbox">
                                                <?= Html::checkbox('licenses_popup', $arVisual['CONSENT']['CHECKED'], [
                                                    'value' => 'Y',
                                                    'required' => true
                                                ]) ?>
                                                <span class="widget-form-result-new-consent-indicator"></span>
                                            </span>
                                        </span>
                                        <span class="intec-grid-item">
                                            <span class="widget-form-result-new-consent-content">
                                                <?= Loc::getMessage('C_WIDGET_FORM_3_WEB_FORM_CONSENT_TEXT_REPLACE', [
                                                    '#URL#' => $arVisual['CONSENT']['LINK']
                                                ]) ?>
                                            </span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?= $arResult['FORM_FOOTER'] ?>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/script.php') ?>
<?= Html::endTag('div') ?>