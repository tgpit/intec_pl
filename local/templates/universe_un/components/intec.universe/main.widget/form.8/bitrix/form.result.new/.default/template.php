<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;

/**
 * @var array $arResult
 */

$request = Core::$app->request;

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
$sSubmitText = ArrayHelper::getValue($arResult, ['arForm', 'BUTTON']);

?>

<div class="ns-bitrix c-form-result-new c-form-result-new-form-8" id="<?= $sTemplateId ?>">
    <?= Html::beginTag('div', [
        'class' => 'form-result-new-wrapper',
        'data-parallax-ratio' => $arVisual['BACKGROUND']['PARALLAX']['USE'] ? $arVisual['BACKGROUND']['PARALLAX']['RATIO'] : null,
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? $arVisual['BACKGROUND']['PATH'] : null
        ],
        'style' => [
            'background-image' => !$arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? 'url(\''.$arVisual['BACKGROUND']['PATH'].'\')' : null
        ]
    ]) ?>
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'intec-grid' => [
                        '' => true,
                        'wrap' => true,
                        'a-v-stretch' => true,
                        'a-h-start' => $arVisual['FORM']['POSITION'] != 'center',
                        'a-h-center' => $arVisual['FORM']['POSITION'] == 'center',
                        'o-horizontal-reverse' => $arVisual['FORM']['POSITION'] == 'right',
                        'i-h-25' => true
                    ]
                ], true)
            ]) ?>
            <div class="intec-grid-item-2 intec-grid-item-768-1">
                <div class="form-result-new-content intec-ui-form">
                    <?php if ($arVisual['FORM']['TITLE']['SHOW'] || $arVisual['FORM']['DESCRIPTION']['SHOW']) { ?>
                        <div class="form-result-new-header">
                            <?php if ($arVisual['FORM']['TITLE']['SHOW']) { ?>
                                <div class="form-result-new-title">
                                    <?= $arVisual['FORM']['TITLE']['VALUE'] ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['FORM']['DESCRIPTION']['SHOW']) { ?>
                                <div class="form-result-new-description">
                                    <?= $arVisual['FORM']['DESCRIPTION']['VALUE'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['isFormNote'] === 'Y') { ?>
                        <div class="form-result-new-sent">
                            <?= $arResult['FORM_NOTE'] ?>
                        </div>
                    <?php } else { ?>
                            <?= $arResult['FORM_HEADER'] ?>
                                <?php if ($arResult['isFormErrors'] == 'Y') { ?>
                                    <?= Html::tag('div', $arResult['FORM_ERRORS_TEXT'], [
                                        'class' => [
                                            'form-result-new-error',
                                            'intec-ui' => [
                                                '',
                                                'control-alert',
                                                'scheme-red'
                                            ]
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <div class="form-result-new-fields intec-ui-form-fields">
                                    <?php foreach ($arResult['QUESTIONS'] as $arQuestion) { ?>
                                        <?= Html::beginTag('label', [
                                            'class' => Html::cssClassFromArray([
                                                'form-result-new-field' => true,
                                                'intec-ui-form-field' => true,
                                                'intec-ui-form-field-required' => $arQuestion['REQUIRED'] === 'Y'
                                            ], true)
                                        ]) ?>
                                            <span class="form-result-new-field-title intec-ui-form-field-title">
                                                <?= $arQuestion['CAPTION'] ?>
                                            </span>
                                            <span class="form-result-new-field-content intec-ui-form-field-content">
                                                <span class="form-result-new-field-title-mobile intec-ui-form-field-title">
                                                    <?= $arQuestion['CAPTION'] ?>
                                                </span>
                                                <?= $arQuestion['HTML_CODE'] ?>
                                            </span>
                                        <?= Html::endTag('label') ?>
                                    <?php } ?>
                                    <?php if ($arVisual['CAPTCHA']['USE']) { ?>
                                        <div class="form-result-new-field form-result-new-field-captcha intec-ui-form-field intec-ui-form-field-required">
                                            <div class="form-result-new-field-title intec-ui-form-field-title">
                                                <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_FIELDS_CAPTCHA') ?>
                                            </div>
                                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                                <?= Html::hiddenInput('captcha_sid', $arResult['CAPTCHACode']) ?>
                                                <div class="form-result-new-captcha intec-grid intec-grid-nowrap intec-grid-500-wrap intec-grid-i-h-5">
                                                    <div class="form-result-new-captcha-image intec-grid-item-auto intec-grid-item-500-1">
                                                        <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHACode'], [
                                                            'width' => 180,
                                                            'height' => 40
                                                        ]) ?>
                                                    </div>
                                                    <div class="form-result-new-captcha-input intec-grid-item intec-grid-item-500-1">
                                                        <?= Html::textInput('captcha_word', null, [
                                                            'class' => [
                                                                'intec-ui' => [
                                                                    '',
                                                                    'control-input',
                                                                    'size-2',
                                                                    'mod-block',
                                                                    'mod-round-3'
                                                                ]
                                                            ],
                                                            'size' => 30,
                                                            'maxlength' => 50,
                                                            'required' => true
                                                        ]) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                                    <div class="form-result-new-consent">
                                        <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                            <?= Html::checkbox('licenses_popup', $arVisual['CONSENT']['CHECKED'], [
                                                'required' => true
                                            ]) ?>
                                            <span class="intec-ui-part-selector"></span>
                                            <span class="intec-ui-part-content">
                                                <?= Loc::getMessage('C_FORM_RESULT_NEW_TEMPLATE_8_TEMPLATE_CONSENT', [
                                                    '#URL#' => $arVisual['CONSENT']['URL']
                                                ]) ?>
                                            </span>
                                        </label>
                                    </div>
                                <?php } ?>
                                <div class="form-result-new-buttons">
                                    <?= Html::submitButton($sSubmitText, [
                                        'class' => [
                                            'form-result-new-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'scheme-current',
                                                'mod-round-2'
                                            ]
                                        ],
                                        'name' => 'web_form_apply',
                                        'value' => 'Y',
                                        'disabled' => $arResult['F_RIGHT'] < 10 || $arVisual['CONSENT']['SHOW']
                                    ]) ?>
                                </div>
                                <?= $arResult['FORM_FOOTER'] ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?php if ($arVisual['ADDITIONAL_PICTURE']['SHOW']) { ?>
                        <div class="intec-grid-item-2 intec-grid-item-768-1 form-result-new-additional-picture-wrapper">
                            <?= Html::tag('div', null, [
                                'class' => 'form-result-new-additional-picture',
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] && !empty($arVisual['ADDITIONAL_PICTURE']['PATH']) ? 'url(\''.$arVisual['ADDITIONAL_PICTURE']['PATH'].'\')' : null,
                                    'background-position-x' => 'center',
                                    'background-position-y' => $arVisual['ADDITIONAL_PICTURE']['VERTICAL_ALIGN'],
                                    'background-size' => $arVisual['ADDITIONAL_PICTURE']['SIZE']
                                ],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['ADDITIONAL_PICTURE']['PATH']) ? $arVisual['ADDITIONAL_PICTURE']['PATH'] : null
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <script type="text/javascript">
        template.load(function (data) {
            var $ = this.getLibrary('$');
            var component = {};

            component.form = $('form', data.nodes);
            component.consent = $('[name="licenses_popup"]', component.form);
            component.submit = $('[type="submit"]', component.form);

            if (!component.form.length || !component.consent.length || !component.submit.length)
                return;

            component.handler = {
                'change': function () {
                    component.submit.prop('disabled', !component.consent.prop('checked'));
                },
                'submit': function () {
                    return component.consent.prop('checked');
                }
            };

            component.form.on('submit', component.handler.submit);
            component.consent.on('change', component.handler.change);

            component.handler.change();
        }, {
            'name': '[Component] intec.universe:main.widget (form.8)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>