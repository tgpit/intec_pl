<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;
use intec\core\helpers\JavaScript;
use intec\core\bitrix\Component;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);

if ($arResult['SHOW_SMS_FIELD'])
    CJSCore::Init('phone_auth');
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sPrefix = 'C_SYSTEM_AUTH_REGISTRATION_DEFAULT_';
?>
<div class="ns-bitrix c-system-auth-registration c-system-auth-registration-default" id="<?= $sTemplateId ?>">
    <div class="system-auth-registration-wrapper intec-content">
        <div class="system-auth-registration-wrapper-2 intec-content-wrapper">
            <div class="system-auth-registration-block">
                <?php if ($arParams['BLOCK_HEADER_SHOW'] === 'Y') { ?>
                    <h2 class="system-auth-registration-header intec-ui-markup-header">
                        <?php if (!empty($arParams['BLOCK_HEADER_TEXT'])) { ?>
                            <?= $arParams['BLOCK_HEADER_TEXT'] ?>
                        <?php } else { ?>
                            <?= Loc::getMessage($sPrefix.'TITLE') ?>
                        <?php } ?>
                    </h2>
                <?php } ?>
                <div class="system-auth-registration-form intec-ui-form">
                    <?php if (!empty($arResult['ERRORS'])) { ?>
                        <div class="intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                            <?= implode('<br />', $arResult['ERRORS']) ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arResult['MESSAGES']) ||
                        $arResult["SHOW_EMAIL_SENT_CONFIRMATION"] ||
                        (!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y")) { ?>
                        <div class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20">
                            <?= implode('<br />', $arResult['MESSAGES']) ?><br/>
                            <?php if ($arResult["SHOW_EMAIL_SENT_CONFIRMATION"]) { ?>
                                    <?= Loc::getMessage($sPrefix.'EMAIL_SENT')?><br />
                            <?php } ?>
                            <?php if (!$arResult["SHOW_EMAIL_SENT_CONFIRMATION"] && $arResult["USE_EMAIL_CONFIRMATION"] === "Y") { ?>
                                    <?= Loc::getMessage($sPrefix.'EMAIL_WILL_BE_SENT')?><br />
                            <?php } ?>
                        </div>
                    <?php } ?>
                    <?php if ($arResult['SHOW_SMS_FIELD']) { ?>
                        <form method="post" action="<?= $arResult['URL']['REGISTER'] ?>" name="Register">
                            <?= Html::hiddenInput('SIGNED_DATA', htmlspecialcharsbx($arResult["SIGNED_DATA"])) ?>
                            <div class="system-auth-registration-form-field intec-ui-form-field intec-ui-form-field-required">
                                <label class="intec-ui-form-field-title" for="SMS_CODE_REGISTRATION">
                                    <?= Loc::getMessage($sPrefix.'REGISTER_SMS_CODE') ?>
                                </label>
                                <div class="intec-ui-form-field-content">
                                    <?= Html::textInput('SMS_CODE', $arResult['SMS_CODE'], [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'mod-round-3',
                                                'size-2'
                                            ]
                                        ],
                                        'autocomplete' => 'off',
                                        'id' => 'SMS_CODE_REGISTRATION'
                                    ]) ?>
                                </div>
                            </div>
                            <div class="system-auth-registration-form-field">
                                <?= Html::beginTag('div', [
                                    'id' => 'register-error',
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-alert',
                                            'scheme-red',
                                            'm-b-20'
                                        ]
                                    ],
                                    'style' => [
                                        'display' => empty($arResult['ERRORS']) ? 'none' : null
                                    ]
                                ]) ?>
                                    <?= implode('<br />', $arResult['ERRORS']) ?>
                                <?= Html::endTag('div')?>
                                <div id="register-resend" class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20"></div>
                            </div>
                            <div class="system-auth-registration-form-buttons">
                                <div class="intec-grid intec-grid-wrap intec-grid-i-h-10 intec-grid-i-v-5 intec-grid-a-v-center">
                                    <div class="intec-grid-item-auto">
                                        <?= Html::submitInput(Loc::getMessage($sPrefix.'REGISTER_SMS_SEND'), [
                                            'name' => 'code_submit_button',
                                            'class' => [
                                                'system-auth-registration-form-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-round-3',
                                                    'scheme-current',
                                                    'size-2'
                                                ]
                                            ]
                                        ]) ?>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <script>
                            new BX.PhoneAuth({
                                containerId: 'register-resend',
                                errorContainerId: 'register-error',
                                interval: <?=$arResult["PHONE_CODE_RESEND_INTERVAL"]?>,
                                data:
                                <?= CUtil::PhpToJSObject([
                                    'signedData' => $arResult["SIGNED_DATA"],
                                ]) ?>,
                                onError:
                                    function(response)
                                    {
                                        var errorDiv = BX('register-error');
                                        var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                                        errorNode.innerHTML = '';
                                        for(var i = 0; i < response.errors.length; i++)
                                        {
                                            errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                                        }
                                        errorDiv.style.display = '';
                                    }
                            });
                        </script>
                    <?php } else if (!$arResult['SHOW_EMAIL_SENT_CONFIRMATION']) { ?>
                        <form method="POST" name="Register" action="<?= $arResult['URL']['REGISTER'] ?>">
                            <?= bitrix_sessid_post() ?>
                            <?= Html::hiddenInput('AUTH_FORM', 'Y') ?>
                            <?= Html::hiddenInput('TYPE', 'REGISTRATION') ?>
                            <?php if (!empty($arResult['URL']['BACK'])) { ?>
                                <?= Html::hiddenInput('backurl', $arResult['URL']['BACK']) ?>
                            <?php } ?>
                            <div class="system-auth-registration-form-description intec-ui-markup-blockquote">
                                <?= Loc::getMessage($sPrefix.'DESCRIPTION') ?>
                            </div>
                            <div class="system-auth-registration-form-fields intec-ui-form-fields">
                                <div class="system-auth-registration-form-field intec-ui-form-field">
                                    <label class="intec-ui-form-field-title" for="USER_NAME_REGISTRATION">
                                        <?= Loc::getMessage($sPrefix.'FIELDS_NAME') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::textInput('USER_NAME', $arResult['USER_NAME'], [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'USER_NAME_REGISTRATION'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="system-auth-registration-form-field intec-ui-form-field">
                                    <label class="intec-ui-form-field-title" for="USER_LAST_NAME_REGISTRATION">
                                        <?= Loc::getMessage($sPrefix.'FIELDS_LAST_NAME') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::textInput('USER_LAST_NAME', $arResult['USER_LAST_NAME'], [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'USER_LAST_NAME_REGISTRATION'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="system-auth-registration-form-field intec-ui-form-field intec-ui-form-field-required">
                                    <label class="intec-ui-form-field-title" for="USER_LOGIN_REGISTRATION">
                                        <?= Loc::getMessage($sPrefix.'FIELDS_LOGIN') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::textInput('USER_LOGIN', $arResult['USER_LOGIN'], [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'USER_LOGIN_REGISTRATION'
                                        ]) ?>
                                    </div>
                                </div>
                                <div class="system-auth-registration-form-field intec-ui-form-field intec-ui-form-field-required">
                                    <label class="intec-ui-form-field-title" for="USER_PASSWORD_REGISTRATION">
                                        <?= Loc::getMessage($sPrefix.'FIELDS_PASSWORD') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::passwordInput('USER_PASSWORD', $arResult['USER_PASSWORD'], [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'USER_PASSWORD_REGISTRATION'
                                        ]) ?>
                                       <?=FileHelper::getFileData(__DIR__.'/svg/icon.open.svg');?>
                                       <?=FileHelper::getFileData(__DIR__.'/svg/icon.close.svg');?>
                                    </div>
                                </div>
                                <div class="system-auth-registration-form-field intec-ui-form-field intec-ui-form-field-required">
                                    <label class="intec-ui-form-field-title" for="USER_CONFIRM_PASSWORD_REGISTRATION">
                                        <?= Loc::getMessage($sPrefix.'FIELDS_PASSWORD_CONFIRM') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::passwordInput('USER_CONFIRM_PASSWORD', $arResult['USER_CONFIRM_PASSWORD'], [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'USER_CONFIRM_PASSWORD_REGISTRATION'
                                        ]) ?>
                                        <?=FileHelper::getFileData(__DIR__.'/svg/icon.open.svg');?>
                                        <?=FileHelper::getFileData(__DIR__.'/svg/icon.close.svg');?>
                                    </div>
                                </div>
                                <?php if ($arResult["EMAIL_REGISTRATION"]) { ?>
                                    <div class="system-auth-registration-form-field intec-ui-form-field <?=($arResult["EMAIL_REQUIRED"]) ? "intec-ui-form-field-required" : ""?>">
                                        <label class="intec-ui-form-field-title" for="USER_EMAIL_REGISTRATION">
                                            <?= Loc::getMessage($sPrefix.'FIELDS_EMAIL') ?>
                                        </label>
                                        <div class="intec-ui-form-field-content">
                                            <?= Html::textInput('USER_EMAIL', $arResult['USER_EMAIL'], [
                                                'class' => [
                                                    'intec-ui' => [
                                                        '',
                                                        'control-input',
                                                        'mod-block',
                                                        'mod-round-3',
                                                        'size-2'
                                                    ]
                                                ],
                                                'id' => 'USER_EMAIL_REGISTRATION'
                                            ]) ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($arResult["PHONE_REGISTRATION"]) { ?>
                                    <div class="system-auth-registration-form-field intec-ui-form-field <?=($arResult["PHONE_REQUIRED"]) ? "intec-ui-form-field-required" : ""?>">
                                        <label class="intec-ui-form-field-title" for="USER_PHONE_NUMBER_REGISTRATION">
                                            <?= Loc::getMessage($sPrefix.'REGISTER_PHONE_NUMBER') ?>
                                        </label>
                                        <div class="intec-ui-form-field-content">
                                            <?= Html::textInput('USER_PHONE_NUMBER', $arResult['USER_PHONE_NUMBER'], [
                                                'class' => [
                                                    'intec-ui' => [
                                                        '',
                                                        'control-input',
                                                        'mod-block',
                                                        'mod-round-3',
                                                        'size-2'
                                                    ]
                                                ],
                                                'id' => 'USER_PHONE_NUMBER_REGISTRATION'
                                            ]) ?>
                                        </div>
                                    </div>
                                <?php } ?>

                                <?php if ($arResult['USER_PROPERTIES']['SHOW'] === 'Y') { ?>
                                    <?php foreach ($arResult['USER_PROPERTIES']['DATA'] as $sKey => $arField) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'system-auth-forgotpasswd-form-field' => true,
                                                'intec-ui-form-field' => true,
                                                'intec-ui-form-field-required' => $arField['MANDATORY'] === 'Y'
                                            ], true)
                                        ]) ?>
                                            <div class="intec-ui-form-field-title">
                                                <?= $arField['EDIT_FORM_LABEL'] ?>
                                            </div>
                                            <div class="intec-ui-form-field-content">
                                                <?php $APPLICATION->IncludeComponent(
                                                    "bitrix:system.field.edit",
                                                    $arField['USER_TYPE']['USER_TYPE_ID'],
                                                    [
                                                        'bVarsFromForm' => $arResult['bVarsFromForm'],
                                                        'arUserField' => $arField,
                                                        'form_name' => 'Register'
                                                    ],
                                                    null,
                                                    ['HIDE_ICONS' => 'Y']
                                                ) ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    <?php } ?>
                                <?php } ?>
                                <?php if ($arResult['USE_CAPTCHA'] === 'Y') { ?>
                                    <div class="system-auth-forgotpasswd-form-field intec-ui-form-field intec-ui-form-field-required">
                                        <label class="intec-ui-form-field-title" for="captcha_word_registration">
                                            <?= Loc::getMessage($sPrefix.'FIELDS_CAPTCHA') ?>
                                        </label>
                                        <div class="intec-ui-form-field-content">
                                            <div class="intec-grid intec-grid-nowrap intec-grid-400-wrap intec-grid-i-5">
                                                <div class="intec-grid-item-auto intec-grid-item-400-1">
                                                    <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHA_CODE'], [
                                                        'width' => 180,
                                                        'height' => 40,
                                                        'alt' => 'CAPTCHA'
                                                    ]) ?>
                                                </div>
                                                <div class="intec-grid-item intec-grid-item-400-1">
                                                    <?= Html::hiddenInput('captcha_sid', $arResult['CAPTCHA_CODE']) ?>
                                                    <?= Html::textInput('captcha_word', null, [
                                                        'class' => [
                                                            'intec-ui' => [
                                                                '',
                                                                'control-input',
                                                                'mod-block',
                                                                'mod-round-3',
                                                                'size-2'
                                                            ]
                                                        ],
                                                        'id' => 'captcha_word_registration'
                                                    ]) ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <div class="system-auth-registration-form-buttons">
                                <div class="intec-grid intec-grid-wrap intec-grid-i-h-10 intec-grid-i-v-5 intec-grid-a-v-center">
                                    <div class="intec-grid-item-auto">
                                        <?= Html::submitInput(Loc::getMessage($sPrefix.'BUTTONS_REGISTER'), [
                                            'name' => 'Register',
                                            'class' => [
                                                'system-auth-registration-form-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-round-3',
                                                    'scheme-current',
                                                    'size-2'
                                                ]
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="system-auth-registration-form-auth-button intec-grid-item-500-1 intec-grid-item">
                                        <?= Loc::getMessage($sPrefix.'AUTH', ['#AUTH_URL#' => $arResult['AUTH_AUTH_URL']])?>
                                    </div>
                                    <?php if ($arResult['CONSENT']['SHOW']) { ?>
                                        <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                                            <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                                <?= Html::checkbox(null, true, [
                                                    'onchange' => 'this.checked = !this.checked'
                                                ]) ?>
                                                <span class="intec-ui-part-selector"></span>
                                                <span class="intec-ui-part-content">
                                                    <?= Loc::getMessage($sPrefix.'CONSENT') ?>
                                                </span>
                                            </label>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </form>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var closeIcons = $('[data-role="password.icon.close"]', root);
        var openIcons = $('[data-role="password.icon.open"]', root);

        openIcons.on('click', function () {
            var input = $('input[type="password"]', $(this).parent());
            var closeIcon = $('[data-role="password.icon.close"]', $(this).parent());
            input.attr('type', 'text');
            closeIcon.fadeIn();
            $(this).fadeOut();
        });

        closeIcons.on('click', function () {
            var input = $('input[type="text"]', $(this).parent());
            var openIcon = $('[data-role="password.icon.open"]', $(this).parent());
            input.attr('type', 'password');
            openIcon.fadeIn();
            $(this).fadeOut();
        });
    }, {
        'name': '[Component] bitrix:system.auth.registration (.default)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>