<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

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

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));

if ($arParams['AJAX_MODE'] == 'Y') {
    $sAjaxID = CAjax::GetComponentID($component->__name, $component->__template->__name, '');
    $APPLICATION->AddHeadString("<style>#wait_comp_".$sAjaxID." {display: none !important;}</style>");
}

?>
<?= Html::beginTag('div', [
    'class' => [
        'ns-intec',
        'c-startshop-forms-result-new',
        'c-startshop-forms-result-new-template-1'
    ],
    'id' => $sTemplateId
]) ?>
    <?php if (($arResult['ERROR']['CODE'] == 0 || $arResult['ERROR']['CODE'] >= 4) && !$arResult['SENT']) { ?>
        <?= Html::beginForm($APPLICATION->GetCurPageParam(), 'post', [
            'class' => 'startshop-forms-result-new-wrapper'
        ]) ?>
            <?= Html::hiddenInput($arParams['REQUEST_VARIABLE_ACTION'], 'send') ?>
            <?= Html::hiddenInput('sessid', bitrix_sessid()) ?>
            <?php if ($arParams["SHOW_TITLE"] === "Y") { ?>
                <div class="startshop-forms-result-new-caption">
                    <?= Html::encode($arResult['LANG'][LANGUAGE_ID]['NAME']) ?>
                </div>
            <?php } ?>
            <?php foreach ($arResult['PROPERTIES'] as $iPropertyID => $arProperty) { ?>
                <?php $sFieldId = $sTemplateId.'_'.$arProperty['CODE']; ?>
                <?php if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXT) { ?>
                    <div class="startshop-forms-result-new-row">
                        <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name intec-ui-form-field-title">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['INVALID'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_INVALID') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <?
                            $propValue = "";
                            if($_REQUEST[$arProperty['CODE']]){
                                $propValue = $_REQUEST[$arProperty['CODE']];
                            } else {
                                if($arParams["FIELDS"]){
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
                                    'startshop-input-text',
                                    'intec-ui' => ['',
                                        'control-input',
                                        'mod-block',
                                        'mod-round-3',
                                        'size-4'
                                    ]
                                ],
                                'data' => [
                                    'role' => 'input'
                                ],
                                'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                            ]);?>
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
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_TEXTAREA) { ?>
                    <div class="startshop-forms-result-new-row">
                        <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <?= Html::textarea($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                                'id' => $sFieldId,
                                'class' => 'startshop-input-textarea intec-input intec-input-block',
                                'data' => [
                                    'role' => 'input'
                                ],
                                'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                            ]) ?>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_RADIO) { ?>
                    <div class="startshop-forms-result-new-row">
                        <div class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </div>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <div class="startshop-forms-result-new-row-control-box startshop-input-box">
                                <div class="startshop-forms-result-new-row-control-box-wrapper">
                                    <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) { ?>
                                        <div class="startshop-forms-result-new-row-control-box-line">
                                            <label class="intec-input intec-input-radio">
                                                <?= Html::radio($arProperty['CODE'], $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'], [
                                                    'value' => $arValue['VALUE'],
                                                    'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                                ]) ?>
                                                <div class="intec-input-selector"></div>
                                                <div class="intec-input-text">
                                                    <?= Html::encode($arValue['VALUE']) ?>
                                                </div>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_CHECKBOX) { ?>
                    <div class="startshop-forms-result-new-row">
                        <div class="startshop-forms-result-new-row-control">
                            <div class="startshop-forms-result-new-row-control-box-line">
                                <input type="hidden" name="<?=htmlspecialcharsbx($arProperty['CODE'])?>" value="N" />
                                <label class="intec-input intec-input-checkbox">
                                    <?= Html::checkbox($arProperty['CODE'], $_REQUEST[$arProperty['CODE']] == 'Y', [
                                        'value' => 'Y',
                                        'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                                    ]) ?>
                                    <div class="intec-input-selector"></div>
                                    <div class="intec-input-text">
                                        <?= Html::encode(ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME'], '')) ?>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_SELECT) { ?>
                    <div class="startshop-forms-result-new-row">
                        <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <?=Html::beginTag("select", [
                                "id" => $sFieldId,
                                "name" => $arProperty['CODE'],
                                "class" => [
                                    "startshop-input-select",
                                    "startshop-input-select-standart",
                                ],
                                "disabled" => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                            ])?>
                                <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) { ?>
                                    <?=Html::beginTag("option", [
                                        'value' => $arValue['VALUE'],
                                        'selected' => $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'] ? 'selected' : null
                                    ]);?>
                                        <?= Html::encode($arValue['VALUE']) ?>
                                    <?=Html::endTag('option');?>
                                <?php } ?>
                            <?=Html::endTag("select");?>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_MULTISELECT) { ?>
                    <div class="startshop-forms-result-new-row">
                        <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <?=Html::beginTag('select', [
                                'id' => $sFieldId,
                                'name' => htmlspecialcharsbx($arProperty['CODE']).'[]',
                                'multiple' => 'multiple',
                                'class' => 'intec-input',
                                'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                            ]);?>
                                <?php foreach($arProperty['DATA']['VALUES'] as $iValueID => $arValue) {
                                    $bSelected = false;
                                    if (Type::isArray($_REQUEST[$arProperty['CODE']])) {
                                        $bSelected = ArrayHelper::isIn($arValue['VALUE'], $_REQUEST[$arProperty['CODE']]);
                                    } else {
                                        $bSelected = $_REQUEST[$arProperty['CODE']] == $arValue['VALUE'];
                                    }
                                    ?>
                                    <?=Html::beginTag('option', [
                                        'value' => $arValue['VALUE'],
                                        'selected' => $bSelected ? 'selected' : null
                                    ]);?>
                                        <?= Html::encode($arValue['VALUE']) ?>
                                    <?=Html::endTag('option');?>
                                    <?php unset($bSelected) ?>
                                <?php } ?>
                            <?=Html::endTag('select');?>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_PASSWORD) { ?>
                    <div class="startshop-forms-result-new-row">
                        <label for="<?= $sFieldId ?>" class="startshop-forms-result-new-row-name startshop-forms-result-new-table-cell-name">
                            <?= ArrayHelper::getValue($arProperty, ['LANG', LANGUAGE_ID, 'NAME']) ?>
                            <?php if ($arProperty['REQUIRED'] == 'Y') { ?>
                                <span class="startshop-forms-result-new-required">*</span>
                            <?php } ?>
                        </label>
                        <?php if ($arResult['ERROR']['CODE'] == 5) { ?>
                            <?php if (ArrayHelper::keyExists($arProperty['CODE'], $arResult['ERROR']['FIELDS']['EMPTY'])) { ?>
                                <div class="startshop-forms-result-new-message-error">
                                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_FIELD_EMPTY') ?>
                                </div>
                            <?php } ?>
                        <?php } ?>
                        <div class="startshop-forms-result-new-row-control">
                            <?= Html::passwordInput($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                                'id' => $sFieldId,
                                'class' => 'startshop-input-text intec-input intec-input-block',
                                'data' => [
                                    'role' => 'input'
                                ],
                                'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                            ]) ?>
                        </div>
                    </div>
                <?php } else if ($arProperty['TYPE'] == STARTSHOP_FORM_PROPERTY_TYPE_HIDDEN) { ?>
                    <?= Html::hiddenInput($arProperty['CODE'], $_REQUEST[$arProperty['CODE']], [
                        'disabled' => $arProperty['READONLY'] == 'Y' ? 'disabled' : null
                    ]) ?>
                <?php } ?>
            <?php } ?>
            <?php if ($arResult['USE_CAPTCHA'] == 'Y') { ?>
                <div class="startshop-forms-result-new-captcha">
                    <?php $sCaptchaSID = $APPLICATION->CaptchaGetCode() ?>
                    <input type="hidden" name="<?= Html::encode($arParams['FORM_VARIABLE_CAPTCHA_SID']) ?>" value="<?= $sCaptchaSID ?>" />
                    <label for="<?= $sTemplateId.'_captcha' ?>" class="startshop-forms-result-new-captcha-caption">
                        <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_CAPTCHA_CAPTION') ?> <span class="startshop-forms-result-new-required">*</span>
                    </label>
                    <?php if ($arResult['ERROR']['CODE'] == 4) { ?>
                        <div class="startshop-forms-result-new-message-error">
                            <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_CAPTCHA_INVALID') ?>
                        </div>
                    <?php } ?>
                    <div class="intec-grid intec-grid-wrap">
                        <div class="startshop-forms-result-new-captcha-image intec-grid-item-auto">
                            <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $sCaptchaSID ?>" alt="CAPTCHA" />
                        </div>
                        <div class="intec-grid-item startshop-forms-result-new-captcha-code">
                            <?=Html::textInput(
                                $arParams['FORM_VARIABLE_CAPTCHA_CODE'],
                                $_REQUEST[$arParams['FORM_VARIABLE_CAPTCHA_CODE']], [
                                    'id' => $sTemplateId.'_captcha',
                                    'class' => ['intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-2',
                                            'size-2'
                                        ]
                                    ]
                                ]
                            )?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if($arResult['CONSENT']['SHOW']) { ?>
                <div class="startshop-forms-result-new-consent">
                    <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                        <?= Html::checkbox('licenses_popup', $arResult['CONSENT']['CHECKED'], [
                            'value' => 'Y'
                        ]) ?>
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content">
                            <?= Loc::getMessage("STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_CONSENT", [
                                "#URL#" => $arResult['CONSENT']['URL']
                            ]);?>
                        </span>
                    </label>
                </div>
            <?php } ?>
            <div class="startshop-forms-result-new-submit">
                <?=Html::submitButton($arResult['LANG'][LANGUAGE_ID]['BUTTON'],[
                    'class' => [
                        'startshop-forms-result-new-form-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'size-2'
                        ]
                    ],
                    'disabled' => $arResult['CONSENT']['SHOW'] ? 'disabled' : null
                ]);?>
            </div>
        <?= Html::endForm() ?>
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
                elements.input = $('[data-role="input"]', elements.form);
                elements.popup = elements.root.closest('.popup-window');

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
                'name': '[Component] intec:startshop.forms.result.new (template.1)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'name': 'lazy'
                }
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
                    'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
                });
            </script>
        <?php } ?>
    <?php } else if ($arResult['SENT']) { ?>
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
                        <?= Html::tag('button', Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_BUTTONS_CLOSE'), [
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
    <?php } else { ?>
        <div class="startshop-forms-result-new-wrapper">
            <?php if ($arResult['ERROR']['CODE'] == 1) { ?>
                <div class="startshop-forms-result-new-message-error">
                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_ERROR_FORM_NOT_EXISTS') ?>
                </div>
            <?php } else if ($arResult['ERROR']['CODE'] == 2) { ?>
                <div class="startshop-forms-result-new-message-error">
                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_ERROR_FORM_INBOUND_SITE') ?>
                </div>
            <?php } else if ($arResult['ERROR']['CODE'] == 3) { ?>
                <div class="startshop-forms-result-new-message-error">
                    <?= Loc::getMessage('STARTSHOP_FORMS_RESULT_NEW_TEMPLATE_1_ERROR_FORM_FIELDS_NOT_EXISTS') ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>