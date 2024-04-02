<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arForm = $arResult['FORM'];

/**
 * @var Closure $vInputText()
 * @var Closure $vInputTextArea()
 * @var Closure $vInputSelect()
 * @var Closure $vInputRating()
 */
$vInputText = include(__DIR__.'/form.input.text.php');
$vInputTextArea = include(__DIR__.'/form.input.textarea.php');
$vInputSelect = include(__DIR__.'/form.input.select.php');
$vInputRating = include(__DIR__.'/form.input.rating.php');

$bCaptcha = false;

if (!empty($arForm['CAPTCHA'])) {
    $bCaptcha = true;

    /**
     * @var Closure $vCaptcha()
     */
    $vCaptcha = include(__DIR__.'/form.captcha.php');
}

$arFormSvg = [
    'TOGGLE' => FileHelper::getFileData(__DIR__.'/../svg/form.toggle.svg'),
    'RATING' => FileHelper::getFileData(__DIR__.'/../svg/form.rating.svg')
]

?>
<?php if ($arForm['STATUS'] === 'empty') {

    $sSubmitText = $arVisual['FORM']['SUBMIT']['TEXT'];

    if (empty($sSubmitText))
        $sSubmitText = Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_BUTTON_TEXT')

?>
    <div class="reviews-form-toggle">
        <?= Html::beginTag('div', [
            'class' => [
                'reviews-form-toggle-button',
                'intec-cl-border',
                'intec-cl-background-hover'
            ],
            'data-role' => 'reviews.form.toggle'
        ]) ?>
            <?= Html::tag('div', $arFormSvg['TOGGLE'], [
                'class' => [
                    'reviews-form-toggle-button-icon',
                    'reviews-form-toggle-button-part',
                    'intec-cl-svg-path-stroke'
                ]
            ]) ?>
            <?= Html::tag('div', Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_TOGGLE_BUTTON_TEXT'), [
                'class' => [
                    'reviews-form-toggle-button-text',
                    'reviews-form-toggle-button-part',
                    'intec-cl-text'
                ]
            ]) ?>
        <?= Html::endTag('div') ?>
    </div>
    <?= Html::beginForm($APPLICATION->GetCurPageParam(), 'post', [
        'class' => 'reviews-form',
        'data' => [
            'role' => 'reviews.form.body',
            'expanded' => empty($arForm['ERROR']) ? 'false' : 'true',
            'state' => 'none'
        ]
    ]) ?>
        <div class="reviews-form-body">
            <?php if (!empty($arForm['ERROR'])) { ?>
                <div class="reviews-form-error">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-ui' => [
                                '',
                                'control-alert',
                                'scheme-red'
                            ]
                        ]
                    ]) ?>
                        <?php foreach ($arForm['ERROR'] as $sError) { ?>
                            <div class="reviews-form-error-part">
                                <?php if ($sError === 'required') { ?>
                                    <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_ERROR_REQUIRED') ?>
                                <?php } else if ($sError === 'captcha') { ?>
                                    <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_ERROR_CAPTCHA') ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
            <div class="reviews-form-content">
                <?= bitrix_sessid_post() ?>
                <?php foreach ($arForm['FIELDS'] as $arField) { ?>
                    <?php if ($arField['TYPE'] === 'hidden') { ?>
                        <?= Html::hiddenInput($arField['NAME'], $arField['VALUE']) ?>
                    <?php } ?>
                <?php } ?>
                <?php foreach ($arForm['FIELDS'] as $arField) { ?>
                    <?php if ($arField['TYPE'] === 'text') { ?>
                        <div class="reviews-form-section">
                            <?php $vInputText($arField) ?>
                        </div>
                    <?php } else if ($arField['TYPE'] === 'textarea') { ?>
                        <div class="reviews-form-section">
                            <?php $vInputTextArea($arField) ?>
                        </div>
                    <?php } else if ($arField['TYPE'] === 'select' && !empty($arField['OPTIONS'])) { ?>
                        <div class="reviews-form-section">
                            <?php if ($arField['NAME'] === $arVisual['FORM']['RATING']['CODE']) { ?>
                                <?php $vInputRating($arField) ?>
                            <?php } else { ?>
                                <?php $vInputSelect($arField) ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if ($bCaptcha)
                    $vCaptcha($arForm['CAPTCHA']);
                ?>
                <?php if ($arVisual['CONSENT']['SHOW']) { ?>
                    <div class="reviews-form-section">
                        <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                            <?= Html::input('checkbox', null, null, [
                                'checked' => 'checked',
                                'onchange' => 'this.checked = !this.checked'
                            ]) ?>
                            <span class="intec-ui-part-selector"></span>
                            <span class="intec-ui-part-content">
                                <?php if (!empty($arVisual['CONSENT']['URL'])) { ?>
                                    <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_CONSENT_URL', [
                                         '#URL#' => $arVisual['CONSENT']['URL']
                                    ]) ?>
                                <?php } else { ?>
                                    <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_CONSENT_DEFAULT') ?>
                                <?php } ?>
                            </span>
                        </label>
                    </div>
                <?php } ?>
                <div class="reviews-form-section">
                    <?= Html::submitButton($sSubmitText, [
                        'class' => [
                            'reviews-form-button',
                            'intec-cl-background',
                            'intec-cl-background-light-hover',
                            'intec-cl-background-light-focus',
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
    <?= Html::endForm() ?>
<?php } else if ($arForm['STATUS'] === 'exists') { ?>
    <div class="reviews-form-message reviews-form-success">
        <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_REVIEW_EXISTS') ?>
    </div>
<?php } else if ($arForm['STATUS'] === 'added') { ?>
    <div class="reviews-form-message reviews-form-success">
        <?php if ($arVisual['FORM']['ADD_MODE'] === 'disabled') { ?>
            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_REVIEW_ADDED_DISABLED') ?>
        <?php } else if ($arVisual['FORM']['ADD_MODE'] === 'active') { ?>
            <?= Loc::getMessage('C_REVIEWS_2_TEMPLATE_1_TEMPLATE_FORM_REVIEW_ADDED_ACTIVE') ?>
        <?php } ?>
    </div>
<?php } ?>