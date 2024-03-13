<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;


/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 * @global CUser $USER
 * @global CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$authUrl = $arResult['AUTH_URL'] ? $arResult['AUTH_URL'] : SITE_DIR .'auth/';
if ($arParams['AUTH_URL']) {
    $authUrl = $arParams['AUTH_URL'];
}

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this, true));
$sPrefix = 'SYSTEM_AUTH_AUTHORIZE_TEMPLATE1_';
?>
<!--noindex-->
<?=Html::beginTag('div', [
    'class' => [
        'ns-bitrix',
        'c-system-auth-authorize',
        'c-system-auth-authorize-template-1',
        'main'
    ],
    'id' => $sTemplateId
]);?>
<div>
    <?
    ShowMessage($arParams["~AUTH_RESULT"]);
    ShowMessage($arResult['ERROR_MESSAGE']);
    ?>
</div>
<?=Html::beginForm($authUrl, 'post', [
    'class' => [
        'bx_auth_form',
        'intec-ui-form'
    ],
    'name' => 'form_auth',
    'target' => '_top',
]);?>
<input type="hidden" name="AUTH_FORM" value="Y" />
<input type="hidden" name="TYPE" value="AUTH" />

<?php if (strlen($arParams['BACKURL']) > 0 || strlen($arResult['BACKURL']) > 0) { ?>
    <?=Html::hiddenInput(
        'backurl',
        $arParams['BACKURL'] ? $arParams['BACKURL'] : $arResult['BACKURL']
    );?>
<?php } ?>

<?php foreach ($arResult['POST'] as $key => $value) { ?>
    <?=Html::hiddenInput(
        $key,
        $value
    );?>
<?php } ?>

<div class="system-auth-authorize-field">
    <label class="system-auth-authorize-caption" for="USER_LOGIN_POPUP">
        <?= Loc::getMessage($sPrefix.'LOGIN') ?>
    </label>
    <div class="system-auth-authorize-value">
        <?=Html::textInput(
            "USER_LOGIN",
            $arResult['LAST_LOGIN'], [
                'class' => [
                    'system-auth-authorize-input',
                    'login-input',
                    'intec-ui',
                    'intec-ui-control-input'
                ],
                'id' => 'USER_LOGIN_POPUP',
                'data-role' => 'input',
                'maxlength' => 255
            ]
        );?>
    </div>
</div>
<div class="system-auth-authorize-field">
    <div class="system-auth-authorize-caption-wrap intec-grid intec-grid-nowrap intec-grid-a-v-center">
        <label class="system-auth-authorize-caption intec-grid-item" for="USER_PASSWORD_POPUP">
            <?= Loc::getMessage($sPrefix.'PASSWORD') ?>
        </label>
        <?php if ($arParams['NOT_SHOW_LINKS'] != 'Y') { ?>
            <div class="system-auth-authorize-forgot-psw-wrap intec-grid-item-auto">
                <?=Html::a(
                    Loc::getMessage($sPrefix.'FORGOT_PASSWORD_2'),
                    $arParams['AUTH_FORGOT_PASSWORD_URL'] ? $arParams['AUTH_FORGOT_PASSWORD_URL'] : $arResult['AUTH_FORGOT_PASSWORD_URL'], [
                        'class' => [
                            'system-auth-authorize-forgot-psw',
                            'intec-cl-text'
                        ],
                        'rel' => 'nofollow'
                    ]
                );?>
            </div>
        <?php } ?>
    </div>
    <div class="system-auth-authorize-value">
        <?=Html::passwordInput(
            'USER_PASSWORD',
            '', [
                'id' => 'USER_PASSWORD_POPUP',
                'class' => [
                    'system-auth-authorize-input',
                    'password-input',
                    'intec-ui',
                    'intec-ui-control-input'
                ],
                'data-role' => 'input',
                'maxlength' => 255
            ]
        );?>
    </div>
</div>

<?php if ($arResult['SECURE_AUTH']) { ?>
    <div class="bx-auth-secure" id="bx_auth_secure" title="<?= GetMessage($sPrefix.'SECURE_NOTE') ?>" style="display:none;">
        <div class="bx-auth-secure-icon"></div>
    </div>
    <noscript>
        <div class="bx-auth-secure" title="<?= GetMessage($sPrefix.'NONSECURE_NOTE') ?>">
            <div class="bx-auth-secure-icon bx-auth-secure-unlock"></div>
        </div>
    </noscript>
    <script type="text/javascript">
        document.getElementById('bx_auth_secure').style.display = 'inline-block';
    </script>
<?php } ?>

<?php if ($arResult['CAPTCHA_CODE']) { ?>

    <?=Html::hiddenInput('captcha_sid', $arResult['CAPTCHA_CODE']);?>
    <div class="system-auth-authorize-field">
        <?=Html::img('/bitrix/tools/captcha.php?captcha_sid='.$arResult['CAPTCHA_CODE'], [
            'alt' => 'CAPTCHA',
            'width' => 180,
            'height' => 40
        ])?>
    </div>
    <div class="system-auth-authorize-field">
        <label class="system-auth-authorize-caption" for="captcha_word_popup">
            <?= Loc::getMessage($sPrefix.'CAPTCHA_PROMT') ?>
        </label>:
        <?=Html::textInput('captcha_word', '', [
            'id' => 'captcha_word_popup',
            'class' => [
                'system-auth-authorize-input',
                'login-input',
                'intec-ui' => [
                    '',
                    'control-input',
                    'mod-block',
                ],
                'bx-auth-input'
            ],
            'name' => 'captcha_word',
            'maxlength' => '50',
            'size' => 15,
            'data-role' => 'input'
        ]);?>
    </div>
<?php } ?>

<?php if ($arResult['STORE_PASSWORD'] == 'Y') { ?>
    <div class="system-auth-authorize-button-block intec-grid intec-grid-nowrap intec-grid-a-v-center">
        <div class="intec-grid-item">
            <?=Html::submitInput(
                Loc::getMessage($sPrefix.'AUTHORIZE'), [
                    'class' => [
                        'system-auth-authorize-login-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'mod-round-3',
                            'size-2',
                            'scheme-current'
                        ]
                    ],
                    'name' => 'Login'
                ]
            );?>
        </div>
        <div class="system-auth-authorize-remember intec-grid-item-auto">
            <label for="USER_REMEMBER_D" class="USER_REMEMBER system-auth-authorize-remember-checkbox">
                <input type="checkbox" id="USER_REMEMBER_D" name="USER_REMEMBER" value="Y"/>
                <label for="USER_REMEMBER_D" class="system-auth-authorize-remember-selector"></label>
                <label for="USER_REMEMBER_D" class="system-auth-authorize-remember-text">
                    <?= Loc::getMessage($sPrefix.'REMEMBER_ME');?>
                </label>
            </label>
        </div>
    </div>
<?php } ?>
<?=Html::endForm();?>

<?php if ($arResult['AUTH_SERVICES']) { ?>
    <div class="login-page_socserv_form">
        <div class="login-page_socserv_form_title">
            <?= Loc::getMessage($sPrefix.'SOCSERV_FORM_TITLE') ?>
        </div>
        <?php $APPLICATION->IncludeComponent(
            'bitrix:socserv.auth.form',
            '',
            array(
                'AUTH_SERVICES' => $arResult['AUTH_SERVICES'],
                'CURRENT_SERVICE' => $arResult['CURRENT_SERVICE'],
                'AUTH_URL' => $arResult['AUTH_URL'],
                'POST' => $arResult['POST'],
                'SUFFIX' => 'main'
            ),
            $component,
            array()
        ); ?>
    </div>
<?php } ?>
<?=Html::endTag('div');?>

<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');

        var root = data.nodes;
        var inputs = $('[data-role="input"]', root);
        var update;

        update = function (field) {
            var self = $(field);

            if (self.val() != '') {
                self.addClass('completed');
            } else {
                self.removeClass('completed');
            }
        };

        inputs.each(function () {
            update(this);
        });

        inputs.on('change', function () {
            update(this);
        });
    }, {
        'name': '[Component] bitrix:system.auth.authorize (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>
    });
</script>
<!--/noindex-->