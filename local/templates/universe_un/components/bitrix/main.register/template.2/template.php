<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\FileHelper;

/**
 * Bitrix vars
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @param array $arParams
 * @param array $arResult
 * @param CBitrixComponentTemplate $this
 */

if ($arResult['SHOW_SMS_FIELD'] == true)
    CJSCore::Init('phone_auth');

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));
$arSvg = [
    'SHOW_PASSWORD' => FileHelper::getFileData(__DIR__.'/svg/eye_open.svg'),
    'HIDE_PASSWORD' => FileHelper::getFileData(__DIR__.'/svg/eye_close.svg')
];
$sPrefix = 'C_MAIN_REGISTER_TEMPLATE_2_TEMPLATE_';
?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-main-register c-main-register-template-2">
    <script>
        BX.message({
            phone_auth_resend: '<?= GetMessageJS($sPrefix.'phone_auth_resend') ?>',
            phone_auth_resend_link: '<?= GetMessageJS($sPrefix.'phone_auth_resend_link') ?>'
        });
    </script>

    <?php if ($USER->IsAuthorized()) { ?>
        <p>
            <?= Loc::getMessage($sPrefix.'AUTHORIZED') ?>
        </p>
        <script type="text/javascript">
            (function () {
                setTimeout(function () {
                    window.location.reload();
                }, 2000);
            })()
        </script>
    <?php } else { ?>
    <?php if (count($arResult['ERRORS']) > 0) {
        foreach ($arResult['ERRORS'] as $key => $error)
            if (intval($key) == 0 && $key !== 0)
                $arResult['ERRORS'][$key] = str_replace('#FIELD_NAME#', '&quot;' . Loc::getMessage($sPrefix.'REGISTER_FIELD_' . $key) . '&quot;', $error);

        ShowError(implode('<br />', $arResult['ERRORS']));
    } else if ($arResult['USE_EMAIL_CONFIRMATION'] === 'Y') { ?>
        <?= Html::tag('p', Loc::getMessage($sPrefix.'REGISTER_EMAIL_WILL_BE_SENT'), []) ?>
    <?php } ?>
    <?php
    $arServices = [];

    //Для авторизации через соц сети, необходимо указать URL личного кабинета
    //для корректного редиректа после авторизации
    $currentPage = $APPLICATION->GetCurPage();
    $APPLICATION->SetCurPage($arParams['AUTH_URL']);

    if (CModule::IncludeModule('socialservices')) {
        $oAuthManager = new CSocServAuthManager();
        $arServices = $oAuthManager->GetActiveAuthServices($arResult);
    }

    $APPLICATION->SetCurPage($currentPage);
    ?>
    <?php if (!empty($arServices)) { ?>
        <div class="main-register-socserv">
            <div class="main-register-socserv-title">
                <?= Loc::getMessage($sPrefix.'SOCIALS') ?>
            </div>
            <div class="main-register-socserv-content">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:socserv.auth.form',
                    '',
                    [
                        'AUTH_SERVICES' => $arServices,
                        'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
                        'AUTH_URL' => $arParams['AUTH_URL'],
                        'POST' => $arResult['POST'],
                        'SUFFIX' => 'main'
                    ],
                    $component
                ) ?>
            </div>
            <div class="main-register-socserv-bottom">
                <?= Html::tag('span', Loc::getMessage($sPrefix.'SOCIALS_POSTFIX'), []) ?>
            </div>
        </div>
    <?php } ?>
    <?php if ($arResult['SHOW_SMS_FIELD']) { ?>
        <form class="main-register-form intec-ui-form" method="post" action="<?= POST_FORM_ACTION_URI ?>" name="regform">
            <?php if ($arResult['BACKURL'] <> '') { ?>
                <?=Html::hiddenInput('backurl', $arResult['BACKURL'])?>
            <?php } ?>
            <input type="hidden" name="SIGNED_DATA" value="<?= htmlspecialcharsbx($arResult['SIGNED_DATA']) ?>"/>
            <div class="intec-ui-form-fields">
                <div class="intec-ui-form-field intec-ui-form-field-required">
                    <label class="intec-ui-form-field-title" for="SMS_CODE_POPUP_2">
                        <?= Loc::getMessage($sPrefix.'SMS') ?>
                    </label>
                    <div class="intec-ui-form-field-content">
                        <?= Html::input('text', 'SMS_CODE', htmlspecialcharsbx($arResult['SMS_CODE']), [
                            'class' => [
                                'inputtext',
                                'intec-ui' => [
                                    '',
                                    'control-input',
                                    'mod-block',
                                    'mod-round-3',
                                    'size-4'
                                ]
                            ],
                            'size' => 30,
                            'autocomplete' => 'off',
                            'data' => [
                                'role' => 'input'
                            ],
                            'id' => 'SMS_CODE_POPUP_2'
                        ]) ?>
                    </div>
                </div>
                <div class="intec-ui-form-field">
                    <div id="bx_main_register_error" class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20" style="display:none"><? ShowError('error') ?></div>
                    <div id="bx_main_register_resend" class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20"></div>
                </div>
            </div>
            <div class="">
                <?= Html::input('submit', 'code_submit_button', Loc::getMessage($sPrefix.'SMS_SEND'), [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-button',
                            'mod-round-3',
                            'scheme-current',
                            'size-1'
                        ]
                    ]
                ]) ?>
            </div>
        </form>
        <script>
            new BX.PhoneAuth({
                containerId: 'bx_main_register_resend',
                errorContainerId: 'bx_main_register_error',
                interval: <?= $arResult['PHONE_CODE_RESEND_INTERVAL'] ?>,
                data:
                <?= CUtil::PhpToJSObject([
                    'signedData' => $arResult['SIGNED_DATA'],
                ]) ?>,
                onError:
                    function (response) {
                        var errorDiv = BX('bx_main_register_error');
                        var errorNode = BX.findChildByClassName(errorDiv, 'errortext');
                        errorNode.innerHTML = '';
                        for (var i = 0; i < response.errors.length; i++) {
                            errorNode.innerHTML = errorNode.innerHTML + BX.util.htmlspecialchars(response.errors[i].message) + '<br>';
                        }
                        errorDiv.style.display = '';
                    }
            });
        </script>
    <?php } else { ?>
        <form class="main-register-form intec-ui-form" method="post" action="<?= POST_FORM_ACTION_URI ?>" name="regform" enctype="multipart/form-data">
            <?php if ($arResult['BACKURL'] <> '') { ?>
                <input type="hidden" name="backurl" value="<?= $arResult['BACKURL'] ?>"/>
            <?php } ?>
            <div class="intec-ui-form-fields">
                <?php foreach ($arResult['SHOW_FIELDS'] as $FIELD) { ?>
                    <?php if ($FIELD == 'PERSONAL_PHOTO' || $FIELD == 'WORK_LOGO') continue; ?>
                    <?php if ($FIELD == 'AUTO_TIME_ZONE' && $arResult['TIME_ZONE_ENABLED'] == true) { ?>
                        <div class="intec-ui-form-field <?= $arResult['REQUIRED_FIELDS_FLAGS'][$FIELD] == 'Y' ? 'intec-ui-form-field-required' : '' ?>">
                            <label class="intec-ui-form-field-title" for="REGISTER_AUTO_TIME_ZONE">
                                <?= Loc::getMessage($sPrefix.'main_profile_time_zones_auto') ?>
                            </label>
                            <div class="intec-ui-form-field-content">
                                <?= Html::beginTag('select', [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'size-4'
                                        ]
                                    ],
                                    'name' => 'REGISTER[AUTO_TIME_ZONE]',
                                    'onchange' => 'this.form.elements[\'REGISTER[TIME_ZONE]\'].disabled=(this.value != \'N\')',
                                    'data' => [
                                        'role' => 'input'
                                    ],
                                    'id' => 'REGISTER_AUTO_TIME_ZONE'
                                ]) ?>
                                <option value="">
                                    <?= Loc::getMessage($sPrefix.'main_profile_time_zones_auto_def') ?>
                                </option>
                                <option value="Y"<?= $arResult['VALUES'][$FIELD] == 'Y' ? ' selected="selected"' : '' ?>>
                                    <?= Loc::getMessage($sPrefix.'main_profile_time_zones_auto_yes') ?>
                                </option>
                                <option value="N"<?= $arResult['VALUES'][$FIELD] == 'N' ? ' selected="selected"' : '' ?>>
                                    <?= Loc::getMessage($sPrefix.'main_profile_time_zones_auto_no') ?>
                                </option>
                                <?= Html::endTag('select') ?>
                            </div>
                        </div>
                        <div class="intec-ui-form-field">
                            <label class="intec-ui-form-field-title" for="REGISTER_TIME_ZONE">
                                <?= Loc::getMessage($sPrefix.'main_profile_time_zones_zones') ?>
                            </label>
                            <div class="intec-ui-form-field-content">
                                <?= Html::beginTag('select', [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'size-4'
                                        ]
                                    ],
                                    'name' => 'REGISTER[TIME_ZONE]',
                                    'data' => [
                                        'role' => 'input'
                                    ],
                                    'disabled' => !isset($_REQUEST['REGISTER']['TIME_ZONE']) ? 'disabled' : null,
                                    'id' => 'REGISTER_TIME_ZONE'
                                ]) ?>
                                <?php foreach ($arResult['TIME_ZONE_LIST'] as $tz => $tz_name) { ?>
                                    <?= Html::tag('option', htmlspecialcharsbx($tz_name), [
                                        'value' => htmlspecialcharsbx($tz),
                                        'selected' => $arResult['VALUES']['TIME_ZONE'] == $tz ? 'selected' : null
                                    ]) ?>
                                <?php } ?>
                                <?= Html::endTag('select') ?>
                            </div>
                        </div>
                    <?php } else { ?>
                        <div class="intec-ui-form-field <?= $arResult['REQUIRED_FIELDS_FLAGS'][$FIELD] == 'Y' ? 'intec-ui-form-field-required' : '' ?>">
                            <label class="intec-ui-form-field-title" for="REGISTER_<?=$FIELD?>_POPUP2">
                                <?= Loc::getMessage($sPrefix.'REGISTER_FIELD_' . $FIELD) ?>
                            </label>
                            <div class="intec-ui-form-field-content">
                                <?php switch ($FIELD) {
                                case 'PASSWORD': ?>
                                    <?= Html::input('password', 'REGISTER['.$FIELD.']', $arResult['VALUES'][$FIELD], [
                                        'class' => [
                                            'bx-auth-input',
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'size-4'
                                            ]
                                        ],
                                        'size' => 30,
                                        'autocomplete' => 'off',
                                        'data' => [
                                            'role' => 'input',
                                            'code' => 'password'
                                        ],
                                        'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                    ]) ?>
                                    <div class="main-register-form-password-icon intec-ui-picture" data-role="password.change" data-action="show" data-active="true" data-target="password">
                                        <?= $arSvg['SHOW_PASSWORD'] ?>
                                    </div>
                                    <div class="main-register-form-password-icon intec-ui-picture" data-role="password.change" data-action="hide" data-active="false" data-target="password">
                                        <?= $arSvg['HIDE_PASSWORD'] ?>
                                    </div>
                                <?php if ($arResult['SECURE_AUTH']) { ?>
                                    <span class="bx-auth-secure" id="bx_auth_secure" title="<?= Loc::getMessage("C_MAIN_REGISTER_TEMPLATE_2_TEMPLATE_AUTH_SECURE_NOTE") ?>" style="display:none">
                                                <div class="bx-auth-secure-icon"></div>
                                            </span>
                                    <noscript>
                                                <span class="bx-auth-secure" title="<?= Loc::getMessage("C_MAIN_REGISTER_TEMPLATE_2_TEMPLATE_AUTH_NONSECURE_NOTE") ?>">
                                                    <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
                                                </span>
                                    </noscript>
                                    <script type="text/javascript">
                                        document.getElementById('bx_auth_secure').style.display = 'inline-block';
                                    </script>
                                <?php } ?>
                                <? break;
                                case 'CONFIRM_PASSWORD': ?>
                                    <?= Html::input('password', 'REGISTER['.$FIELD.']', $arResult['VALUES'][$FIELD], [
                                        'class' => [
                                            'bx-auth-input',
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'size-4'
                                            ]
                                        ],
                                        'size' => 30,
                                        'autocomplete' => 'off',
                                        'data' => [
                                            'role' => 'input',
                                            'code' => 'confirm_password'
                                        ],
                                        'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                    ]) ?>
                                    <?=Html::tag('div', $arSvg['SHOW_PASSWORD'], [
                                        'class' => [
                                            'main-register-form-password-icon',
                                            'intec-ui-picture'
                                        ],
                                        'data' => [
                                            'role' => 'password.change',
                                            'action' => 'show',
                                            'active' => 'true',
                                            'target' => 'confirm_password'
                                        ]
                                    ])?>
                                    <?=Html::tag('div', $arSvg['HIDE_PASSWORD'], [
                                        'class' => [
                                            'main-register-form-password-icon',
                                            'intec-ui-picture'
                                        ],
                                        'data' => [
                                            'role' => 'password.change',
                                            'action' => 'hide',
                                            'active' => 'false',
                                            'target' => 'confirm_password'
                                        ]
                                    ])?>
                                    <? break;
                                case 'PERSONAL_GENDER': ?>
                                <?= Html::beginTag('select', [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'size-4'
                                        ]
                                    ],
                                    'name' => 'REGISTER['.$FIELD.']',
                                    'data' => [
                                        'role' => 'input'
                                    ],
                                    'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                ]) ?>
                                    <option value="">
                                        <?= Loc::getMessage($sPrefix.'USER_DONT_KNOW') ?>
                                    </option>
                                    <option value="M"<?= $arResult['VALUES'][$FIELD] == 'M' ? ' selected="selected"' : '' ?>>
                                        <?= Loc::getMessage($sPrefix.'USER_MALE') ?>
                                    </option>
                                    <option value="F"<?= $arResult['VALUES'][$FIELD] == 'F' ? ' selected="selected"' : '' ?>>
                                        <?= Loc::getMessage($sPrefix.'USER_FEMALE') ?>
                                    </option>
                                <?= Html::endTag('select') ?>
                                <? break;
                                case 'PERSONAL_COUNTRY':
                                case 'WORK_COUNTRY': ?>
                                    <?= Html::beginTag('select', [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'size-4'
                                            ]
                                        ],
                                        'name' => 'REGISTER['.$FIELD.']',
                                        'data' => [
                                            'role' => 'input'
                                        ],
                                        'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                    ]) ?>
                                    <?php foreach ($arResult['COUNTRIES']['reference_id'] as $key => $value) { ?>
                                        <?= Html::tag('option', $arResult['COUNTRIES']['reference'][$key], [
                                            'value' => $value,
                                            'selected' => $value == $arResult['VALUES'][$FIELD] ? 'selected' : null
                                        ]) ?>
                                    <? } ?>
                                    <?= Html::endTag('select') ?>
                                    <? break;
                                case 'PERSONAL_NOTES':
                                case 'WORK_NOTES': ?>
                                    <?= Html::textarea('REGISTER['.$FIELD.']', $arResult['VALUES'][$FIELD], [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block'
                                            ]
                                        ],
                                        'cols' => 30,
                                        'rows' => 5,
                                        'data' => [
                                            'role' => 'input'
                                        ],
                                        'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                    ]) ?>
                                    <? break;
                                default: ?>
                                <?= Html::input('text', 'REGISTER['.$FIELD.']', $arResult['VALUES'][$FIELD], [
                                    'class' => [
                                        'date-picker',
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'size-4'
                                        ]
                                    ],
                                    'size' => 30,
                                    'placeholder' => $FIELD == 'PERSONAL_BIRTHDAY' ? $arResult['DATE_FORMAT'] : null,
                                    'data' => [
                                        'role' => 'input'
                                    ],
                                    'id' => 'REGISTER_'.$FIELD.'_POPUP2'
                                ]) ?>
                                <?php if ($FIELD == 'PERSONAL_BIRTHDAY') { ?>
                                    <div class="main-register-form-calendar-icon">
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:main.calendar',
                                            '',
                                            [
                                                'SHOW_INPUT' => 'N',
                                                'FORM_NAME' => 'regform',
                                                'INPUT_NAME' => 'REGISTER[PERSONAL_BIRTHDAY]',
                                                'SHOW_TIME' => 'N'
                                            ],
                                            null,
                                            ['HIDE_ICONS' => 'Y']
                                        ); ?>
                                    </div>
                                <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
                <?php if ($arResult['USE_CAPTCHA'] == 'Y') { ?>
                    <div class="intec-ui-form-field intec-ui-form-field-required">
                        <div class="intec-ui-form-field-title" for="">
                            <?= Loc::getMessage($sPrefix.'REGISTER_CAPTCHA_TITLE') ?>
                        </div>
                        <div class="intec-ui-form-field-content">
                            <div class="intec-ui-m-b-10">
                                <?=Html::hiddenInput('captcha_sid', $arResult['CAPTCHA_CODE']);?>
                                <?=Html::img(
                                    '/bitrix/tools/captcha.php?captcha_sid='.$arResult["CAPTCHA_CODE"], [
                                        'width' => 180,
                                        'height' => 40,
                                        'alt' => 'CAPTCHA'
                                    ]
                                );?>
                            </div>
                            <div>
                                <label class="intec-ui-form-field-title" for="captcha_word_popup_2">
                                    <?= Loc::getMessage($sPrefix.'REGISTER_CAPTCHA_PROMT');?>:
                                </label>
                                <div class="intec-ui-form-field-content">
                                    <?=Html::textInput('captcha_word', '', [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'size-4'
                                            ]
                                        ],
                                        'maxlength' => 50,
                                        'id' => 'captcha_word_popup_2'
                                    ]);?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <?php if ($arResult['CONSENT']['SHOW']) { ?>
                <div class="main-register-consent">
                    <label class="intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                        <input type="checkbox" checked="checked" onchange="this.checked = !this.checked" />
                        <span class="intec-ui-part-selector"></span>
                        <span class="intec-ui-part-content"><?= Loc::getMessage($sPrefix.'DEFAULT_CONSENT', [
                                '#URL#' => $arResult['CONSENT']['URL']
                            ]) ?></span>
                    </label>
                </div>
            <?php } ?>
            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-16">
                <div class="intec-grid-item-auto">
                    <?= Html::submitInput(Loc::getMessage($sPrefix.'AUTH_REGISTER'), [
                        'class' => [
                            'main-register-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-round-2',
                                'scheme-current',
                                'size-4'
                            ]
                        ],
                        'name' => 'register_submit_button'
                    ]);?>
                </div>
                <div class="intec-grid-item-auto intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-4">
                    <div class="intec-grid-item-auto">
                            <span class="main-register-auth-info">
                                <?= Loc::getMessage($sPrefix.'SOCIALS_HAVE_ACCOUNT') ?>
                            </span>
                    </div>
                    <div class="intec-grid-item-auto">
                        <a href="<?= $arParams['AUTH_URL'] ?>" class="main-register-auth-link intec-cl-text intec-cl-text-light-hover">
                            <?= Loc::getMessage($sPrefix.'SOCIALS_COME_IN') ?>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php } ?>
    <?php } ?>
</div>

<script>
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var inputs = $('[data-role="input"]', root);
        var update;
        var buttonPasswordChange = $('[data-role="password.change"]', root);

        //for adaptation window
        window.dispatchEvent(new Event('resize'));

        buttonPasswordChange.on('click', function () {
            var currentButton = $(this)[0];
            $('[data-target="' + currentButton.dataset.target + '"]', root).each(function () {
                $(this)[0].dataset.active = true;
            });
            currentButton.dataset.active = false;
            var targetInput = $('[data-role="input"][data-code="' + currentButton.dataset.target + '"]', root)[0];

            if (currentButton.dataset.action == 'show') {
                targetInput.setAttribute('type', 'text');
            } else if (currentButton.dataset.action == 'hide') {
                targetInput.setAttribute('type', 'password');
            }
        });

        update = function () {
            var self = $(this);

            if (self.val() != '') {
                self.addClass('completed');
            } else {
                self.removeClass('completed');
            }
        };

        inputs.each(function () {
            update.call(this);
        });

        inputs.on('change', function () {
            update.call(this);
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
        'name': '[Component] bitrix:main.register (template.2)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>