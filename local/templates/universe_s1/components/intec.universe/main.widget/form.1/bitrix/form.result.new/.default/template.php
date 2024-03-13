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

$arViewParams = ArrayHelper::getValue($arResult, 'VIEW_PARAMETERS');

$sFormBgTheme = $arViewParams['FORM_BACKGROUND'] == 'theme' ? ' intec-cl-background' : null;
$sFromBgCustom = $arViewParams['FORM_BACKGROUND'] == 'custom' ? $arViewParams['FORM_BACKGROUND_CUSTOM'] : null;

$sBgPicture = $arViewParams['BLOCK_BACKGROUND'];

$arBlockBg = [
    'class' => 'form-result-new-block-bg',
    'data-parallax-ratio' => $arViewParams['BLOCK_BACKGROUND_PARALLAX']['USE'] ? $arViewParams['BLOCK_BACKGROUND_PARALLAX']['RATIO'] : null,
    'data' => [
        'lazyload-use' => $arViewParams['LAZYLOAD']['USE'] && !empty($sBgPicture) ? 'true' : 'false',
        'original' => $arViewParams['LAZYLOAD']['USE'] && !empty($sBgPicture) ? $sBgPicture : null
    ],
    'style' => [
        'background-image' => !$arViewParams['LAZYLOAD']['USE'] && !empty($sBgPicture) ? 'url(\''.$sBgPicture.'\')' : null
    ]
];
$arFormBg = [
    'class' => 'form-result-new-form-background'.$sFormBgTheme,
    'style' => [
        'background-color' => $sFromBgCustom,
        'opacity' => $arViewParams['FORM_BACKGROUND_OPACITY']
    ]
];

$arAdditionalPicture = [];

if ($arViewParams['FORM_ADDITIONAL_PICTURE_SHOW']) {
    $arAdditionalPicture = [
        'class' => [
            'form-result-new-additional-picture',
            $arViewParams['FORM_POSITION'] === 'right' ? 'position-left': 'position-right',
        ],
        'style' => [
            'background-image ' => !$arViewParams['LAZYLOAD']['USE'] ? 'url(\''.$arViewParams['FORM_ADDITIONAL_PICTURE'].'\')' : null,
            'background-position-x' => $arViewParams['FORM_ADDITIONAL_PICTURE_HORIZONTAL'],
            'background-position-y' => $arViewParams['FORM_ADDITIONAL_PICTURE_VERTICAL'],
            'background-size' => $arViewParams['FORM_ADDITIONAL_PICTURE_SIZE']
        ],
        'data' => [
            'lazyload-use' => $arViewParams['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arViewParams['LAZYLOAD']['USE'] ? $arViewParams['FORM_ADDITIONAL_PICTURE'] : null
        ]
    ];
}

$sCaptchaCode = null;
$arCaptcha = [];

if ($arResult['isUseCaptcha'] == 'Y') {
    $sCaptchaCode = Html::encode($arResult['CAPTCHACode']);

    $arCaptcha = [
        'img' => [
            'width' => 180,
            'height' => 40
        ],
        'input' => [
            'class' => 'form-result-new-input type-text type-captcha',
            'placeholder' => Loc::getMessage('C_FORM_RESULT_NEW_FORM1_CAPTCHA_PLACEHOLDER'),
            'required' => true
        ]
    ];
}

$sRequiredSign = '*';
$sSubmitText = ArrayHelper::getValue($arResult, ['arForm', 'BUTTON']);

?>
<div class="ns-bitrix c-form-result-new c-form-result-new-form-1" id="<?= $sTemplateId ?>">
    <?= Html::beginTag('div', $arBlockBg) ?>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="position-<?= $arViewParams['FORM_POSITION'] ?> intec-ui-clearfix">
                    <div class="form-result-new-form-wrap theme-<?= $arViewParams['FORM_TEXT_COLOR'] ?>">
                        <?= Html::tag('div', '', $arFormBg) ?>
                        <div class="form-result-new-form-content">
                            <?php if ($arViewParams['TITLE_SHOW'] || $arViewParams['DESCRIPTION_SHOW']) { ?>
                                <div class="form-result-new-form-header">
                                    <?php if ($arViewParams['TITLE_SHOW']) { ?>
                                        <div class="form-result-new-title">
                                            <?= $arResult['FORM_TITLE'] ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arViewParams['DESCRIPTION_SHOW']) { ?>
                                        <div class="form-result-new-description">
                                            <?= $arResult['FORM_DESCRIPTION'] ?>
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
                                <?php if ($arResult["isFormErrors"] == 'Y') { ?>
                                    <div class="form-result-new-error">
                                        <?= $arResult["FORM_ERRORS_TEXT"] ?>
                                    </div>
                                <?php } ?>
                                <?php foreach ($arResult['QUESTIONS'] as $arQuestion) {

                                    $bRequired = ArrayHelper::getValue($arQuestion, 'REQUIRED') == 'Y';

                                    $sFieldCaption = ArrayHelper::getValue($arQuestion, 'CAPTION');
                                    $sFieldCaption = $bRequired ? $sFieldCaption.' '.$sRequiredSign : $sFieldCaption;
                                    $sFieldId = ArrayHelper::getValue($arQuestion, ['STRUCTURE', 0, 'ID']);
                                    $sFieldType = ArrayHelper::getValue($arQuestion, ['STRUCTURE', 0, 'FIELD_TYPE']);
                                    $sFieldName = 'form_'.$sFieldType.'_'.$sFieldId;
                                    $sFieldValue = Html::encode($request->post($sFieldName));

                                    $arInput = [
                                        'class' => 'form-result-new-input type-'.$sFieldType,
                                        'placeholder' => $sFieldCaption,
                                        'required' => $bRequired
                                    ];

                                    ?>
                                    <div class="form-result-new-element">
                                        <div class="form-result-new-element-input-wrap">
                                            <?php if ($sFieldType == 'text' || $sFieldType == 'email') { ?>
                                                <?= Html::input($sFieldType, $sFieldName, $sFieldValue, $arInput) ?>
                                            <?php } else if ($sFieldType == 'textarea') { ?>
                                                <?= Html::textarea($sFieldName, $sFieldValue, $arInput) ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                                <?php if ($arViewParams['CONSENT']['SHOW']) { ?>
                                    <div class="form-result-new-consent">
                                        <label class="intec-grid intec-grid-inline intec-grid-a-v-center intec-grid-i-h-6">
                                                    <span class="intec-grid-item-auto">
                                                        <span class="form-result-new-consent-indicator">
                                                            <?= Html::checkbox('licenses_popup', $arViewParams['CONSENT']['CHECKED'], [
                                                                'value' => 'Y',
                                                                'required' => true
                                                            ]) ?>
                                                            <span class="form-result-new-consent-indicator-content"></span>
                                                        </span>
                                                    </span>
                                            <span class="intec-grid-item">
                                                        <span class="form-result-new-consent-content">
                                                            <?= Loc::getMessage('C_FORM_RESULT_NEW_FORM1_CONSENT_TEXT', [
                                                                '#URL#' => $arViewParams['CONSENT']['URL']
                                                            ]) ?>
                                                        </span>
                                                    </span>
                                        </label>
                                    </div>
                                <?php } ?>
                                <?php if ($arResult['isUseCaptcha'] == 'Y') { ?>
                                    <div class="form-result-new-captcha">
                                        <div class="form-result-new-captcha-wrap">
                                            <?= Html::hiddenInput('captcha_sid', $sCaptchaCode) ?>
                                            <div class="intec-grid intec-grid-wrap intec-grid-i-h-10 intec-grid-a-v-center">
                                                <div class="captcha-img-wrap intec-grid-item intec-grid-item-500-1">
                                                    <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$sCaptchaCode, $arCaptcha['img']) ?>
                                                </div>
                                                <div class="intec-grid-item intec-grid-item-500-1">
                                                    <?= Html::input('text', 'captcha_word', null, $arCaptcha['input']) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="form-result-new-submit">
                                    <?= Html::submitButton($sSubmitText, [
                                        'class' => [
                                            'form-result-new-submit-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button'
                                            ]
                                        ],
                                        'name' => 'web_form_apply',
                                        'value' => 'Y',
                                        'disabled' => $arResult['F_RIGHT'] < 10 || $arViewParams['CONSENT']['SHOW']
                                    ]) ?>
                                </div>
                                <?= $arResult['FORM_FOOTER'] ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <?php if ($arViewParams['FORM_ADDITIONAL_PICTURE_SHOW']) { ?>
                    <?= Html::tag('div', null , $arAdditionalPicture) ?>
                <?php } ?>
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
            'name': '[Component] intec.universe:main.widget (form.1)',
            'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
            'loader': {
                'name': 'lazy'
            }
        });
    </script>
</div>