<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
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
$arVisual = $arResult['VISUAL'];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>

<div class="ns-bitrix c-main-profile c-main-profile-template-1" id="<?= $sTemplateId ?>">
    <div class="main-profile-wrapper intec-content">
        <div class="main-profile-wrapper-2 intec-content-wrapper">
            <?php if (!empty($arResult['strProfileError'])) { ?>
                <div class="intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                    <?= $arResult['strProfileError'] ?>
                </div>
            <?php } ?>
            <?php if ($arResult['DATA_SAVED'] === 'Y') { ?>
                <div class="intec-ui intec-ui-control-alert intec-ui-scheme-green intec-ui-m-b-20">
                    <?= Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_MESSAGES_CHANGED') ?>
                </div>
            <?php } ?>
            <div class="main-profile-block">
                <div class="main-profile-header">
                    <div class="main-profile-title">
                        <?= Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_HEADER') ?>
                    </div>
                </div>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'main-profile-form' => true,
                        'intec-ui-form' => !$arVisual['READ_ONLY']
                    ], true)
                ]) ?>
                    <?php if (!$arVisual['READ_ONLY']) { ?>
                        <form method="POST" action="<?= $arResult['FORM_TARGET'] ?>?" enctype="multipart/form-data">
                            <?= $arResult['BX_SESSION_CHECK'] ?>
                            <?= Html::hiddenInput('lang', LANG) ?>
                            <?= Html::hiddenInput('ID', $arResult['ID']) ?>
                            <div class="main-profile-form-fields intec-ui-form-fields">
                    <?php } ?>
                            <?php foreach ($arResult['MAIN_FIELDS'] as $keyField => $valueField) { ?>
                                <?php if (!$arVisual['ALL_FIELDS_SHOW'] && !$valueField) continue;?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'main-profile-form-field' => true,
                                        'intec-ui-form-field' => !$arVisual['READ_ONLY']
                                    ], true)
                                ]) ?>
                                    <?= Html::tag('label', Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_FIELDS_TITLE_'.$keyField), [
                                        'class' => Html::cssClassFromArray([
                                            'main-profile-form-field-title' => $arVisual['READ_ONLY'],
                                            'intec-ui-form-field-title' => !$arVisual['READ_ONLY']
                                        ], true),
                                        'for' => $keyField
                                    ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'main-profile-form-field-content' => $arVisual['READ_ONLY'],
                                            'intec-ui-form-field-content' => !$arVisual['READ_ONLY']
                                        ], true)
                                    ]) ?>
                                        <?php if ($arVisual['READ_ONLY']) { ?>
                                            <?= Html::tag('div', $valueField, [
                                                'class' => 'main-profile-form-field-content-value'
                                            ]) ?>
                                        <?php } else { ?>
                                            <?= Html::textInput($keyField, $valueField, [
                                                'class' => [
                                                    'intec-ui' => [
                                                        '',
                                                        'control-input',
                                                        'mod-block',
                                                        'mod-round-3',
                                                        'size-2'
                                                    ]
                                                ],
                                                'id' => $keyField
                                            ]) ?>
                                        <?php } ?>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php unset($keyField, $valueField) ?>
                            <?php if (!$arVisual['READ_ONLY']) { ?>
                                <?php $arSvg = [
                                        "OPEN" => FileHelper::getFileData(__DIR__."/svg/open.svg"),
                                        "CLOSE" => FileHelper::getFileData(__DIR__."/svg/close.svg"),
                                ];?>
                                <div class="main-profile-form-field intec-ui-form-field">
                                    <label class="intec-ui-form-field-title" for="NEW_PASSWORD">
                                        <?= Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_FIELDS_TITLE_PASSWORD') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::passwordInput('NEW_PASSWORD', '', [
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'id' => 'NEW_PASSWORD',
                                            'autocomplete' => "off",
                                            'maxlength' => '50'
                                        ]) ?>
                                        <?=$arSvg["OPEN"]?>
                                        <?=$arSvg["CLOSE"]?>
                                    </div>
                                </div>
                                <div class="main-profile-form-field intec-ui-form-field">
                                    <label class="intec-ui-form-field-title" for="NEW_PASSWORD_CONFIRM">
                                        <?= Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_FIELDS_TITLE_PASSWORD_CONFIRM') ?>
                                    </label>
                                    <div class="intec-ui-form-field-content">
                                        <?= Html::passwordInput("NEW_PASSWORD_CONFIRM","", [
                                            'id' => 'NEW_PASSWORD_CONFIRM',
                                            'class' => [
                                                'intec-ui' => [
                                                    '',
                                                    'control-input',
                                                    'mod-block',
                                                    'mod-round-3',
                                                    'size-2'
                                                ]
                                            ],
                                            'autocomplete' => "off",
                                            'maxlength' => '50'
                                        ]);
                                        ?>
                                        <?=$arSvg["OPEN"];?>
                                        <?=$arSvg["CLOSE"];?>
                                    </div>
                                </div>
                            </div>
                            <div class="main-profile-form-buttons intec-grid intec-grid-wrap intec-grid-i-6">
                                <div class="intec-grid-item-auto">
                                    <?= Html::submitInput(Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_BUTTONS_SAVE'), [
                                        'name' => 'save',
                                        'class' => [
                                            'main-profile-form-button',
                                            'intec-ui' => [
                                                '',
                                                'control-button',
                                                'mod-transparent',
                                                'scheme-current'
                                            ]
                                        ]
                                    ]) ?>
                                </div>
                            </div>
                        </form>
                    <?php } else { ?>
                        <?php if (!empty($arParams['URL_CHANGE_PASSWORD']) || !empty($arParams['URL_EDIT'])) { ?>
                            <div class="main-profile-form-buttons intec-grid intec-grid-wrap intec-grid-i-6">
                                <?php if (!empty($arParams['URL_EDIT'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag('a', Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_BUTTONS_EDIT'), [
                                            'class' => [
                                                'main-profile-form-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-transparent',
                                                    'scheme-current'
                                                ]
                                            ],
                                            'href' => $arParams['URL_EDIT']
                                        ]) ?>
                                    </div>
                                <?php } ?>
                                <?php if (!empty($arParams['URL_CHANGE_PASSWORD'])) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::tag('a', Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_TEMPLATE_BUTTONS_PASSWORD_CHANGE'), [
                                            'class' => [
                                                'main-profile-form-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'mod-transparent',
                                                    'scheme-current'
                                                ]
                                            ],
                                            'href' => $arParams['URL_CHANGE_PASSWORD']
                                        ]) ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    template.load(function (data) {
        var $ = this.getLibrary('$');
        var root = data.nodes;

        var inputPassword = $('#NEW_PASSWORD', root);
        var inputConfirmPassword = $('#NEW_PASSWORD_CONFIRM', root);

        var newCloseIcon = $('.main-profile-eye-icon-close', inputPassword.parent());
        var newOpenIcon = $('.main-profile-eye-icon-open', inputPassword.parent());

        var confirmCloseIcon = $('.main-profile-eye-icon-close', inputConfirmPassword.parent());
        var confirmOpenIcon = $('.main-profile-eye-icon-open', inputConfirmPassword.parent());

        newOpenIcon.on('click', function () {
            inputPassword.attr('type', 'text');
            newCloseIcon.fadeIn();
            $(this).fadeOut();
        });

        newCloseIcon.on('click', function () {
            inputPassword.attr('type', 'password');
            newOpenIcon.fadeIn();
            $(this).fadeOut();
        });

        confirmOpenIcon.on('click', function () {
            inputConfirmPassword.attr('type', 'text');
            confirmCloseIcon.fadeIn();
            $(this).fadeOut();
        });

        confirmCloseIcon.on('click', function () {
            inputConfirmPassword.attr('type', 'password');
            confirmOpenIcon.fadeIn();
            $(this).fadeOut();
        });
    }, {
        'name': '[Component] bitrix:main.profile (template.1)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>