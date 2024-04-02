<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

/* Добавил в генерацию ID рандомное число.
 * Т.к. данная форма уже может быть вызвана на странице,
 * и в ajax вызове новой формы генерируется дублирующийся ID
 *  */
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true)).rand(0, 100);

?>
<?= Html::beginTag('div', [
    'class' => [
        'ns-intec',
        'c-startshop-forms-result-new',
        'c-startshop-forms-result-new-default',
        'intec-ui-form'
    ],
    'id' => $sTemplateId
]) ?>
    <?php if (($arResult['ERROR']['CODE'] == 0 || $arResult['ERROR']['CODE'] >= 4) && !$arResult['SENT']) { ?>
        <?= Html::beginForm($APPLICATION->GetCurPageParam(), 'post') ?>
            <?= Html::hiddenInput($arParams['REQUEST_VARIABLE_ACTION'], 'send') ?>
            <?= Html::hiddenInput('sessid', bitrix_sessid()) ?>
            <div class="startshop-forms-result-new-fields intec-ui-form-fields">
                <?php foreach ($arResult['PROPERTIES'] as $iPropertyID => $arProperty) { ?>
                    <?php $sFieldId = $sTemplateId.'_'.$arProperty['CODE']; ?>
                    <?php if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </label>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['INVALID'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_INVALID') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="startshop-forms-result-new-content intec-ui-form-field-content">
                                <?
                                $propValue = "";
                                if ($_REQUEST[$arProperty['CODE']]) {
                                    $propValue = $_REQUEST[$arProperty['CODE']];
                                } else {
                                    if ($arParams["FIELDS"]) {
                                        foreach($arParams["FIELDS"] as $key => $val){
                                            if($key == $arProperty["ID"]){
                                                $propValue = $val;
                                                break;
                                            }
                                        }
                                    }
                                } ?>
                                <?= Html::textInput($arProperty['CODE'], $propValue, [
                                    'id' => $sFieldId,
                                    'class' => [
                                        'textinput',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-4',
                                        ]
                                    ],
                                    'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                ]) ?>
                                <?php if (!empty($arProperty['DATA']['MASK'])) { ?>
                                    <script type="text/javascript">
                                        $(document).ready(function () {
                                            $('#<?= $sTemplateId ?>')
                                                .find('input[name="<?= Html::encode($arProperty['CODE']) ?>"]')
                                                .mask(<?= JavaScript::toObject($arProperty['DATA']['MASK']) ?>, {
                                                    'placeholder': <?= JavaScript::toObject($arProperty['DATA']['MASK_PLACEHOLDER']) ?>
                                                });
                                        })
                                    </script>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </label>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="startshop-forms-result-new-content intec-ui-form-field-content">
                                <?= Html::textarea($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                                    'id' => $sFieldId,
                                    'class' => [
                                        'textarea',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-2',
                                        ]
                                    ],
                                    'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                ]) ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_RADIO) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <div class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </div>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) { ?>
                                    <label class="intec-ui intec-ui-control-radiobox intec-ui-scheme-current">
                                        <?= Html::radio($arProperty['CODE'], $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'], [
                                            'value' => $arValue['VALUE'],
                                            'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                        ]) ?>
                                        <div class="intec-ui-part-selector"></div>
                                        <div class="intec-ui-part-content">
                                            <?= Html::encode($arValue['VALUE']) ?>
                                        </div>
                                    </label>
                                    <br>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <input type="hidden" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="N" />
                                <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                    <?= Html::checkbox($arProperty['CODE'], $_REQUEST[$arProperty['CODE']] == 'Y', [
                                        'value' => 'Y',
                                        'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                    ]) ?>
                                    <div class="intec-ui-part-selector"></div>
                                    <div class="intec-ui-part-content">
                                        <?= Html::encode(ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME'], '')) ?>
                                    </div>
                                </label>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_SELECT) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </label>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <?= Html::beginTag('select', [
                                    'id' => $sFieldId,
                                    'name' => Html::encode($arProperty['CODE']),
                                    'class' => [
                                        'inputselect',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-2',
                                        ]
                                    ],
                                    'disabled' => $arProperty['READONLY'] == 'Y' ? ' disabled="disabled"' : null
                                ]) ?>
                                    <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) { ?>
                                        <option value="<?= Html::encode($arValue['VALUE']) ?>"<?= $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'] ? ' selected="selected"' : '' ?>>
                                            <?= Html::encode($arValue['VALUE']) ?>
                                        </option>
                                    <?php } ?>
                                <?= Html::endTag('select'); ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </label>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <?= Html::beginTag('select', [
                                    'id' => $sFieldId,
                                    'name' => htmlspecialcharsbx($arProperty['CODE'])."[]",
                                    'multiple' => 'multiple',
                                    'class' => [
                                        'inputselect',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-2',
                                        ]
                                    ],
                                    'disabled' => $arProperty['READONLY'] == 'Y' ? ' disabled="disabled"' : null
                                ]) ?>
                                    <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) { ?>
                                    <?php
                                        $bSelected = false;

                                        if (Type::isArray($_REQUEST[$arProperty['CODE']])) {
                                            $bSelected = ArrayHelper::isIn($arValue['VALUE'], $_REQUEST[$arProperty['CODE']]);
                                        } else {
                                            $bSelected = $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'];
                                        }
                                    ?>
                                        <option value="<?= Html::encode($arValue['VALUE']) ?>"<?= $bSelected ? ' selected="selected"' : '' ?>>
                                            <?= Html::encode($arValue['VALUE']) ?>
                                        </option>
                                    <?php unset($bSelected) ?>
                                    <?php } ?>
                                <?= Html::endTag('select'); ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_PASSWORD) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'startshop-forms-result-new-field' => true,
                                'intec-ui-form-field' => true,
                                'intec-ui-form-field-required' => $arProperty['REQUIRED'] === 'Y'
                            ], true)
                        ]) ?>
                            <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-title intec-ui-form-field-title">
                                <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            </label>
                            <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                                <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                    <div class="startshop-forms-result-new-message-error">
                                        <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_FIELD_EMPTY') ?>
                                    </div>
                                <?php } ?>
                            <?php } ?>
                            <div class="form-result-new-field-content intec-ui-form-field-content">
                                <?= Html::passwordInput($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                                    'id' => $sFieldId,
                                    'class' => [
                                        'inputtext',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-4',
                                        ],
                                    ],
                                    'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                ]) ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_HIDDEN) { ?>
                        <?= Html::hiddenInput($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                            'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                        ]) ?>
                    <?php } ?>
                <?php } ?>
                <?php if ($arResult['USE_CAPTCHA'] == 'Y') { ?>
                    <div class="startshop-forms-result-new-captcha intec-ui-form-field intec-ui-form-field-required">
                        <?php $sCaptchaSID = $APPLICATION->CaptchaGetCode() ?>
                        <label for="<?= $sTemplateId.'_captcha' ?>" class="startshop-forms-result-new-field-title intec-ui-form-field-title">
                            <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_CAPTCHA_CAPTION') ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 4) { ?>
                            <div class="startshop-forms-result-new-message-error">
                                <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_CAPTCHA_INVALID') ?>
                            </div>
                        <?php } ?>
                        <div class="startshop-forms-result-new-field-content intec-ui-form-field-content">
                            <input type="hidden" name="<?= Html::encode($arParams['FORM_VARIABLE_CAPTCHA_SID']) ?>" value="<?= $sCaptchaSID ?>" />
                            <div class="startshop-forms-result-new-captcha intec-grid intec-grid-nowrap intec-grid-i-h-5 intec-grid-a-v-center">
                                <div class="startshop-forms-result-new-captcha-image intec-grid-item-auto">
                                    <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $sCaptchaSID ?>" alt="CAPTCHA" width="180" height="40" />
                                </div>
                                <div class="startshop-forms-result-new-captcha-input intec-grid-item">
                                    <?= Html::input('text',
                                        $arParams['FORM_VARIABLE_CAPTCHA_CODE'],
                                        $_REQUEST[$arParams['FORM_VARIABLE_CAPTCHA_CODE']], [
                                            "id" => $sTemplateId.'_captcha',
                                            "class" => [
                                                "intec-ui" => [
                                                    "",
                                                    "control-input",
                                                    "mod-block",
                                                    "mod-round-3",
                                                    "size-2"
                                                ]
                                            ]
                                    ]);?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if($arResult['CONSENT']['SHOW']) { ?>
                <div class="startshop-forms-result-new-consent">
                    <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                        <?= Html::checkbox('licenses_popup', $arResult['CONSENT']['CHECKED'], [
                            'value' => 'Y'
                        ]) ?>
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content startshop-forms-result-new-consent-text">
                            <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_CONSENT', [
                                '#URL#' => $arResult['CONSENT']['URL']
                            ]) ?>
                        </span>
                    </label>
                </div>
            <?php } ?>
            <div class="startshop-forms-result-new-buttons">
                <div class="intec-grid intec-grid-wrap intec-grid-i-5">
                    <div class="intec-grid-item-auto">
                        <?= Html::submitButton($arResult['LANG'][LANGUAGE_ID]['BUTTON'], [
                            'class' => [
                                'startshop-forms-result-new-submit-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'mod-round-2',
                                    'scheme-current',
                                    'size-1'
                                ]
                            ],
                            'disabled' => $arResult['CONSENT']['SHOW']
                        ]) ?>
                    </div>
                    <div class="intec-grid-item-auto">
                        <?=Html::input('reset', '', Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_BUTTONS_RESET') ,[
                            'class' => [
                                'startshop-forms-result-new-reset-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'mod-round-2',
                                    'mod-transparent',
                                    'size-1'
                                ]
                            ]
                        ]);?>
                    </div>
                </div>
            </div>
        <?= Html::endTag('form') ?>
        <script type="text/javascript">
            template.load(function (data, options) {
                var app = this;
                var $ = app.getLibrary('$');
                var elements = {};

                //for adaptation window
                window.dispatchEvent(new Event('resize'));

                elements.root = $(options.nodes);
                elements.buttons = $('[data-role="buttons"]', elements.root);
                elements.closeButton = $('[data-role="closeButton"]', elements.buttons);
                elements.form = $('form', elements.root);
                elements.popup = elements.root.closest('.popup-window');

                if (elements.buttons.length > 0 && elements.popup.length > 0) {
                    elements.buttons.show();
                    elements.closeButton.on('click', function () {
                        elements.popup.find('.popup-window-close-icon').trigger('click');
                    });
                }

                elements.form.on('submit', function () {
                    app.metrika.reachGoal('forms');
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['ID']) ?>);
                    app.metrika.reachGoal(<?= JavaScript::toObject('forms.'.$arResult['ID'].'.send') ?>);
                });

                BX.ajax({
                    'method': 'GET',
                    'headers': [
                        {'name': 'X-Bitrix-Csrf-Token', 'value': BX.bitrix_sessid()}
                    ],
                    'dataType': 'html',
                    'url': '/bitrix/tools/public_session.php?k=' + <?= JavaScript::toObject($_SESSION['fixed_session_id']) ?>,
                    'data':  '',
                    'lsId': 'sess_expand'
                });
            }, {
                'name': '[Component] intec:startshop.forms.result.new (.default)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
            });
        </script>
        <?php if ($arResult['CONSENT']['SHOW']) { ?>
            <script type="text/javascript">
                template.load(function () {
                    var $ = this.getLibrary('$');
                    var node = $(<?= JavaScript::toObject('#'.$sTemplateId) ?>);
                    var form = $('form', node);
                    var consent = $('[name="licenses_popup"]', form);
                    var submit = $('[type="submit"]', form);

                    if (!form.length || !consent.length || !submit.length)
                        return;

                    var update = function () {
                        submit.prop('disabled', !consent.prop('checked'));
                    };

                    form.on('submit', function () {
                        return consent.prop('checked');
                    });

                    consent.on('change', update);

                    update();
                }, {
                    'name': '[Component] intec:form.result.new (.default)',
                    'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                    'loader': {
                        'name': 'lazy'
                    }
                });
            </script>
        <?php } ?>
    <?php } else if ($arResult['SENT']) { ?>
        <div class="startshop-forms-result-new default">
            <div class="startshop-forms-result-new-wrapper">
                <div class="startshop-forms-result-new-sent">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center">
                        <div class="startshop-forms-result-new-sent-icon intec-grid-item-auto intec-cl-svg">
                            <?=FileHelper::getFileData(__DIR__."/svg/success.svg");?>
                        </div>
                        <div class="startshop-forms-result-new-sent-text intec-grid-item-auto intec-grid-item-shrink-1">
                            <?= nl2br(ArrayHelper::getValue($arResult, ['LANG', LANGUAGE_ID, 'SENT'], '')) ?>
                        </div>
                        <div class="startshop-forms-result-new-sent-buttons intec-grid-item-1" data-role="buttons">
                            <?= Html::tag('button', Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_BUTTONS_CLOSE'), [
                                'class' => [
                                    'startshop-forms-result-new-sent-button',
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
            </div>
        </div>
    <?php } else { ?>
        <div class="startshop-forms-result-new-wrapper">
            <?php if ($arResult['ERROR']['CODE'] == 1) { ?>
                <div class="startshop-forms-result-new-message-error intec-ui intec-ui-control-alert intec-ui-scheme-red">
                    <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_ERROR_FORM_NOT_EXISTS') ?>
                </div>
            <?php } else if ($arResult['ERROR']['CODE'] == 2) { ?>
                <div class="startshop-forms-result-new-message-error intec-ui intec-ui-control-alert intec-ui-scheme-red">
                    <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_ERROR_FORM_INBOUND_SITE') ?>
                </div>
            <?php } else if ($arResult['ERROR']['CODE'] == 3) { ?>
                <div class="startshop-forms-result-new-message-error intec-ui intec-ui-control-alert intec-ui-scheme-red">
                    <?= Loc::getMessage('C_STARTSHOP_FORMS_RESULT_NEW_DEFAULT_ERROR_FORM_FIELDS_NOT_EXISTS') ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?= Html::endTag('div'); ?>