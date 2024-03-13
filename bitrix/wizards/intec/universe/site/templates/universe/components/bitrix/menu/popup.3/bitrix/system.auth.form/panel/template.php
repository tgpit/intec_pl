<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\JavaScript;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @global CMain $APPLICATION
 */

if (!CModule::IncludeModule('intec.core'))
    return;

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sFormType = $arResult['FORM_TYPE'];

$oFrame = $this->createFrame();

$arVisual = $arResult['VISUAL'];
?>
<div class="menu-authorization" id="<?= $sTemplateId ?>">
    <?php $oFrame->begin() ?>
        <?php if ($sFormType == 'login') { ?>
            <div class="menu-authorization-button" data-action="login">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'menu-authorization-button-wrapper' => true,
                        'intec-grid' => true,
                        'intec-grid-a-v-center' => true,
                        'intec-grid-i-h-4' => true,
                        'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false
                    ], true)
                ]) ?>
                    <div class="menu-authorization-button-icon-wrap intec-grid-item-auto">
                        <div class="menu-authorization-button-icon glyph-icon-login_2"></div>
                    </div>
                    <div class="menu-authorization-button-text intec-grid-item-auto">
                        <?= Loc::getMessage('W_HEADER_S_A_F_LOGIN') ?>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else { ?>
            <?= Html::beginTag('a', [
                'class' => Html::cssClassFromArray([
                    'menu-authorization-button' => true,
                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false
                ], true),
                'href' => $arResult['PROFILE_URL'],
                'rel' => 'nofollow'
            ]) ?>
                <div class="menu-authorization-button-wrapper intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                    <div class="menu-authorization-button-icon-wrap intec-grid-item-auto">
                        <div class="menu-authorization-button-icon glyph-icon-user_2"></div>
                    </div>
                    <div class="menu-authorization-button-text intec-grid-item-auto">
                        <?= $arResult['USER_LOGIN'] ?>
                    </div>
                </div>
            <?= Html::endTag('a') ?>
            <?= Html::beginTag('a', [
                'class' => Html::cssClassFromArray([
                    'menu-authorization-button' => true,
                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false
                ], true),
                'href' => $arResult['LOGOUT_URL'],
                'rel' => 'nofollow'
            ]) ?>
                <div class="menu-authorization-button-wrapper intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                    <div class="menu-authorization-button-icon-wrap intec-grid-item-auto">
                        <div class="menu-authorization-button-icon glyph-icon-logout_2"></div>
                    </div>
                    <div class="menu-authorization-button-text intec-grid-item-auto">
                        <?= Loc::getMessage('W_HEADER_S_A_F_LOGOUT') ?>
                    </div>
                </div>
            <?= Html::endTag('a') ?>
        <?php } ?>
    <?php $oFrame->beginStub() ?>
        <div class="menu-authorization-button" data-action="login">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'menu-authorization-button-wrapper' => true,
                    'intec-grid' => true,
                    'intec-grid-a-v-center' => true,
                    'intec-grid-i-h-4' => true,
                    'intec-cl-text-hover' => $arVisual['THEME'] == 'light' ? true : false
                ], true)
            ]) ?>
                <div class="menu-authorization-button-icon-wrap intec-grid-item-auto">
                    <div class="menu-authorization-button-icon glyph-icon-login_2"></div>
                </div>
                <div class="menu-authorization-button-text intec-grid-item-auto">
                    <?= Loc::getMessage('W_HEADER_S_A_F_LOGIN') ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    <?php $oFrame->end() ?>
    <?php if (!defined('EDITOR')) { ?>
        <script type="text/javascript">
            template.load(function () {
                var $ = this.getLibrary('$');
                var app = this;
                var root = arguments[0].nodes;
                var buttons;
                var modal = {};
                var data;

                data = <?= JavaScript::toObject([
                    'id' => $sTemplateId . '-modal',
                    'title' => Loc::getMessage('W_HEADER_S_A_F_AUTH_FORM_TITLE')
                ]) ?>;

                <?php
                $arComponentParams = [
                    'component' => $arParams['MODAL_COMPONENT'],
                    'template' => $arParams['MODAL_TEMPLATE'],
                    'parameters' => [
                        'AUTH_URL' => $arParams['LOGIN_URL'],
                        'AUTH_FORGOT_PASSWORD_URL' => $arParams['FORGOT_PASSWORD_URL'],
                        'AUTH_REGISTER_URL' => $arParams['REGISTER_URL'],
                        'AJAX_MODE' => 'N',
                        'CONSENT_URL' => $arParams['CONSENT_URL']
                    ],
                    'settings' => [
                        'title' => Loc::getMessage('W_HEADER_S_A_F_AUTH_FORM_TITLE'),
                        'parameters' => [
                            'width' => 800,
                            'zIndex' => 0,
                            'offsetLeft' => 0,
                            'offsetTop' => 0,
                            'overlay' => true
                        ]
                    ]
                ];
                ?>

                var modalParams = <?= JavaScript::toObject($arComponentParams) ?>;

                modalParams.settings.parameters.closeIcon = {
                    'right': '20px',
                    'top': '22px'
                };

                modalParams.settings.parameters.titleBar = {
                    'content': BX.create('span', {
                        'html': data.title,
                        'props': {
                            'className': 'access-title-bar'
                        }
                    })
                };

                modal.open = function () {
                    app.api.components.show(modalParams);
                };

                buttons = {};
                buttons.login = $('[data-action="login"]', root);
                buttons.login.on('click', modal.open);
            }, {
                'name': '[Component] bitrix:menu (popup.3) > bitrix:system.auth.form (panel)',
                'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
                'loader': {
                    'options': {
                        'await': [
                            'composite'
                        ]
                    }
                }
            });
        </script>
    <?php } ?>
</div>