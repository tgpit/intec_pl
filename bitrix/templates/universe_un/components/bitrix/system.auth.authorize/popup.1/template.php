<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\Core;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;
use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$this->setFrameMode(true);
$arPost = Core::$app->request->post();

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sPrefix = "C_SYSTEM_AUTH_AUTHORIZE_POPUP_1_";
?>
<!--noindex-->
<div class="ns-bitrix c-system-auth-authorize c-system-auth-authorize-popup-1" id="<?= $sTemplateId ?>">
    <div class="intec-grid intec-grid-wrap intec-grid-i-h-15 intec-grid-i-v-5 intec-grid-a-v-stretch">
        <div class="intec-grid-item-2 intec-grid-item-800-1">
            <div class="system-auth-authorize-form intec-ui-form">
                <form method="POST" action="<?= $arResult['URL']['AUTHORIZE'] ?>">
                    <?= bitrix_sessid_post() ?>
                    <?= Html::hiddenInput('AUTH_FORM', 'Y') ?>
                    <?= Html::hiddenInput('TYPE', 'AUTH') ?>
                    <?php if (!empty($arResult['URL']['BACK'])) { ?>
                        <?= Html::hiddenInput('backurl', $arResult['URL']['BACK']) ?>
                    <?php } ?>
                    <?php foreach ($arResult['POST'] as $sKey => $mValue) { ?>
                        <?= Html::hiddenInput($sKey, $mValue) ?>
                    <?php } ?>
                    <?php if (!empty($arResult['ERRORS'])) { ?>
                        <div class="intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                            <?= implode('<br />', $arResult['ERRORS']) ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arResult['MESSAGES'])) { ?>
                        <div class="intec-ui intec-ui-control-alert intec-ui-scheme-blue intec-ui-m-b-20">
                            <?= implode('<br />', $arResult['MESSAGES']) ?>
                        </div>
                    <?php } ?>
                    <div class="system-auth-authorize-form-fields intec-ui-form-fields">
                        <div class="system-auth-authorize-form-field intec-ui-form-field">
                            <label class="intec-ui-form-field-title" for="USER_LOGIN_2">
                                <?= Loc::getMessage($sPrefix.'FIELDS_LOGIN') ?>
                            </label>
                            <div class="intec-ui-form-field-content">
                                <?= Html::textInput('USER_LOGIN', $arResult['LAST_LOGIN'], [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-2'
                                        ]
                                    ],
                                    'maxlength' => 255,
                                    'id' => 'USER_LOGIN_2'
                                ]) ?>
                            </div>
                        </div>
                        <div class="system-auth-authorize-form-field intec-ui-form-field">
                            <label class="intec-ui-form-field-title" for="USER_PASSWORD_2">
                                <?= Loc::getMessage($sPrefix.'FIELDS_PASSWORD') ?>
                            </label>
                            <div class="intec-ui-form-field-content">
                                <?= Html::passwordInput('USER_PASSWORD', null, [
                                    'id' => 'USER_PASSWORD_2',
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-input',
                                            'mod-block',
                                            'mod-round-3',
                                            'size-2'
                                        ]
                                    ],
                                    'maxlength' => 255
                                ]) ?>
                                <?=FileHelper::getFileData(__DIR__."/svg/icon.open.svg")?>
                                <?=FileHelper::getFileData(__DIR__."/svg/icon.close.svg")?>
                            </div>
                        </div>
                        <?php if (!empty($arResult['CAPTCHA_CODE'])) { ?>
                            <div class="system-auth-authorize-form-field intec-ui-form-field">
                                <label class="intec-ui-form-field-title" for="captcha_word_2">
                                    <?= Loc::getMessage($sPrefix.'FIELDS_CAPTCHA') ?>
                                </label>
                                <div class="intec-ui-form-field-content">
                                    <div class="intec-grid intec-grid-nowrap intec-grid-i-h-5">
                                        <div class="intec-grid-item-auto">
                                            <?= Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHA_CODE'], [
                                                'width' => 180,
                                                'height' => 40,
                                                'alt' => 'CAPTCHA'
                                            ]) ?>
                                        </div>
                                        <div class="intec-grid-item">
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
                                                'id' => 'captcha_word_2'
                                            ]) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arParams['NOT_SHOW_LINKS'] !== 'Y' || $arResult['STORE_PASSWORD'] === 'Y') { ?>
                        <div class="system-auth-authorize-form-additions">
                            <div class="intec-grid intec-grid-nowrap intec-grid-i-h-5">
                                <?php if ($arResult['STORE_PASSWORD'] === 'Y') { ?>
                                    <div class="intec-grid-item-auto">
                                        <label class="system-auth-authorize-form-remember intec-ui intec-ui-control-checkbox intec-ui-scheme-current">
                                            <input type="checkbox" name="USER_REMEMBER" value="Y"/>
                                            <span class="intec-ui-part-selector"></span>
                                            <span class="intec-ui-part-content">
                                                <?= Loc::getMessage($sPrefix.'REMEMBER') ?>
                                            </span>
                                        </label>
                                    </div>
                                <?php } ?>
                                <div class="intec-grid-item"></div>
                                <?php if ($arParams['NOT_SHOW_LINKS'] !== 'Y' && !empty($arResult['URL']['RESTORE'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <a class="system-auth-authorize-form-restore" href="<?= $arResult['URL']['RESTORE'] ?>" rel="nofollow">
                                            <?= Loc::getMessage($sPrefix.'RESTORE') ?>
                                        </a>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="system-auth-authorize-form-buttons">
                        <?= Html::submitInput(Loc::getMessage($sPrefix.'BUTTONS_AUTHORIZE'), [
                            'name' => 'Login',
                            'class' => [
                                'system-auth-authorize-form-button',
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
                </form>
                <?php if (!empty($arResult['AUTH_SERVICES'])) { ?>
                    <div class="system-auth-authorize-form-socials">
                        <div class="system-auth-authorize-form-socials-title">
                            <?= Loc::getMessage($sPrefix.'SOCIALS') ?>
                        </div>
                        <div class="system-auth-authorize-form-socials-content">
                            <?php $APPLICATION->IncludeComponent(
                                'bitrix:socserv.auth.form',
                                '',
                                [
                                    'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
                                    'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
                                    'AUTH_URL' => $arResult['AUTH_URL'],
                                    'POST' => $arResult['POST'],
                                    'SUFFIX' => 'main'
                                ],
                                $component
                            ) ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="intec-grid-item-2 intec-grid-item-800-1">
            <?php if (!empty($arResult['URL']['REGISTER']) && $arParams['NOT_SHOW_LINKS'] !== 'Y' && $arResult['NEW_USER_REGISTRATION'] === 'Y' && $arParams['AUTHORIZE_REGISTRATION'] !== 'Y') { ?>
                <div class="system-auth-authorize-delimiter"></div>
                <div class="system-auth-authorize-registration">
                    <?= Html::a(Loc::getMessage($sPrefix.'BUTTONS_REGISTER'), $arResult['URL']['REGISTER'], [
                        'class' => [
                            'system-auth-authorize-registration-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'mod-round-3',
                                'scheme-current',
                                'size-2'
                            ]
                        ]
                    ]) ?>
                    <div class="system-auth-authorize-registration-text">
                        <?= Loc::getMessage($sPrefix.'REGISTER') ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
<!--/noindex-->
<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;
        var closeIcons = $('[data-role="password.icon.close"]', root);
        var openIcons = $('[data-role="password.icon.open"]', root);

        //for adaptation window
        window.dispatchEvent(new Event('resize'));

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
        'name': '[Component] bitrix:system.auth.authorize (popup.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>