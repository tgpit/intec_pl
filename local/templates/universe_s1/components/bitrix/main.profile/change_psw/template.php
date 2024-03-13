<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\bitrix\Component;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

?>
<div class="ns-bitrix c-main-profile c-main-profile-password" id="<?= $sTemplateId ?>">
    <div class="main-profile-wrapper intec-content">
        <div class="main-profile-wrapper-2 intec-content-wrapper">
            <div class="main-profile-block">
                <h2 class="main-profile-header intec-ui-markup-header">
                    <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_HEADER') ?>
                </h2>
                <?php if (!empty($arResult['strProfileError'])) { ?>
                    <div class="intec-ui intec-ui-control-alert intec-ui-scheme-red intec-ui-m-b-20">
                        <?= $arResult['strProfileError'] ?>
                    </div>
                <?php } ?>
                <?php if ($arResult['DATA_SAVED'] === 'Y') { ?>
                    <div class="intec-ui intec-ui-control-alert intec-ui-scheme-green intec-ui-m-b-20">
                        <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_MESSAGES_CHANGED') ?>
                    </div>
                <?php } ?>
                <div class="main-profile-form intec-ui-form">
                    <form method="POST" action="<?= $arResult['FORM_TARGET'] ?>" enctype="multipart/form-data">
                        <?= $arResult['BX_SESSION_CHECK'] ?>
                        <?= Html::hiddenInput('lang', LANG) ?>
                        <?= Html::hiddenInput('ID', $arResult['ID']) ?>
                        <?= Html::hiddenInput('LOGIN', $arResult['arUser']['LOGIN']) ?>
                        <?= Html::hiddenInput('EMAIL', $arResult['arUser']['EMAIL']) ?>
                        <div class="main-profile-form-fields intec-ui-form-fields">
                            <div class="main-profile-form-field intec-ui-form-field intec-ui-form-field-required">
                                <div class="intec-ui-form-field-title">
                                    <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_FIELDS_PASSWORD_NEW_TITLE') ?>
                                </div>
                                <div class="intec-ui-form-field-content">
                                    <?= Html::passwordInput('NEW_PASSWORD', null, [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'mod-round-3',
                                                'size-2'
                                            ]
                                        ],
                                        'autocomplete' => 'off'
                                    ]) ?>
                                    <svg class="main-profile-eye-icon main-profile-eye-icon-open" data-role="password.icon.open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.8729 11.611C21.6942 11.3666 17.4371 5.92969 11.9999 5.92969C6.56274 5.92969 2.30539 11.3666 2.12691 11.6108C1.9577 11.8427 1.9577 12.1572 2.12691 12.389C2.30539 12.6335 6.67826 18.0272 12.1154 18.0272C17.5526 18.0272 21.6942 12.6334 21.8729 12.3892C22.0423 12.1574 22.0423 11.8427 21.8729 11.611ZM11.9999 16.593C7.99486 16.593 4.67152 13.2451 3.64468 11.9996C4.67019 10.753 7.98626 7.41642 11.9999 7.41642C16.0048 7.41642 19.3138 10.7534 20.3411 11.9996C19.3155 13.2462 16.0135 16.593 11.9999 16.593Z" fill="#808080"/>
                                        <path d="M11.9999 8.74072C10.2028 8.74072 8.74072 10.2028 8.74072 11.9999C8.74072 13.7969 10.2028 15.259 11.9999 15.259C13.7969 15.259 15.259 13.7969 15.259 11.9999C15.259 10.2028 13.7969 8.74072 11.9999 8.74072ZM11.9999 14.1726C10.8018 14.1726 9.82712 13.1979 9.82712 11.9999C9.82712 10.8018 10.8018 9.82712 11.9999 9.82712C13.1979 9.82712 14.1726 10.8018 14.1726 11.9999C14.1726 13.1979 13.1979 14.1726 11.9999 14.1726Z" fill="#808080" stroke="#808080" stroke-width="0.37"/>
                                    </svg>
                                    <svg class="main-profile-eye-icon main-profile-eye-icon-close" data-role="password.icon.close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.7811 4.21678C19.4954 3.927 19.0301 3.93109 18.7404 4.21678L16.1283 6.82884C12.8918 5.35956 9.43896 5.71872 6.14531 7.86959C3.69242 9.46948 2.20273 11.4816 2.14151 11.5673C1.9456 11.8367 1.95376 12.204 2.16191 12.4652C3.55773 14.1794 5.01886 15.514 6.51263 16.4445L4.21483 18.7423C3.92914 19.028 3.92914 19.4933 4.21483 19.783C4.35768 19.9259 4.54542 19.9994 4.73316 19.9994C4.92091 19.9994 5.10865 19.9259 5.25149 19.783L19.7811 5.25752C20.0668 4.97183 20.0668 4.50656 19.7811 4.21678ZM10.1818 12.7754C10.0756 12.5346 10.0226 12.2734 10.0226 11.9999C10.0226 11.4734 10.2267 10.9755 10.6021 10.6C11.1899 10.0123 12.0592 9.87353 12.7775 10.1796L10.1818 12.7754ZM13.8631 9.09807C12.5285 8.24099 10.7287 8.39608 9.56548 9.55927C8.91247 10.2123 8.55739 11.0775 8.55739 11.9958C8.55739 12.6652 8.74921 13.3059 9.10429 13.8569L7.59011 15.3711C6.26367 14.6038 4.95764 13.4692 3.68834 11.9877C4.23116 11.351 5.37802 10.1225 6.94934 9.09807C9.66751 7.32677 12.3734 6.94312 15.01 7.95122L13.8631 9.09807Z" fill="#808080"/>
                                        <path d="M21.8381 11.5345C20.83 10.2938 19.7852 9.24893 18.7241 8.42041C18.4016 8.17145 17.9405 8.22859 17.6874 8.54693C17.4384 8.86528 17.4956 9.32647 17.8139 9.57951C18.6547 10.2366 19.4954 11.0529 20.3117 12.0079C19.8301 12.5711 18.8914 13.5833 17.618 14.5098C15.1651 16.2933 12.6878 16.9382 10.2594 16.4239C9.86346 16.3382 9.47166 16.5954 9.39003 16.9912C9.30432 17.3871 9.56144 17.7789 9.95734 17.8606C10.5899 17.9953 11.2266 18.0606 11.8674 18.0606C12.8306 18.0606 13.802 17.9096 14.7692 17.6075C16.0385 17.2116 17.2997 16.5586 18.52 15.6689C20.577 14.1629 21.8055 12.5018 21.8585 12.4324C22.0545 12.163 22.0463 11.7957 21.8381 11.5345Z" fill="#808080"/>
                                    </svg>
                                </div>
                                <div class="intec-ui-form-field-description">
                                    <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_FIELDS_PASSWORD_NEW_DESCRIPTION') ?>
                                </div>
                            </div>
                            <div class="main-profile-form-field intec-ui-form-field intec-ui-form-field-required">
                                <div class="intec-ui-form-field-title">
                                    <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_FIELDS_PASSWORD_CONFIRM_TITLE') ?>
                                </div>
                                <div class="intec-ui-form-field-content">
                                    <?= Html::passwordInput('NEW_PASSWORD_CONFIRM', null, [
                                        'class' => [
                                            'intec-ui' => [
                                                '',
                                                'control-input',
                                                'mod-block',
                                                'mod-round-3',
                                                'size-2'
                                            ]
                                        ],
                                        'autocomplete' => 'off'
                                    ]) ?>
                                    <svg class="main-profile-eye-icon main-profile-eye-icon-open" data-role="password.icon.open" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M21.8729 11.611C21.6942 11.3666 17.4371 5.92969 11.9999 5.92969C6.56274 5.92969 2.30539 11.3666 2.12691 11.6108C1.9577 11.8427 1.9577 12.1572 2.12691 12.389C2.30539 12.6335 6.67826 18.0272 12.1154 18.0272C17.5526 18.0272 21.6942 12.6334 21.8729 12.3892C22.0423 12.1574 22.0423 11.8427 21.8729 11.611ZM11.9999 16.593C7.99486 16.593 4.67152 13.2451 3.64468 11.9996C4.67019 10.753 7.98626 7.41642 11.9999 7.41642C16.0048 7.41642 19.3138 10.7534 20.3411 11.9996C19.3155 13.2462 16.0135 16.593 11.9999 16.593Z" fill="#808080"/>
                                        <path d="M11.9999 8.74072C10.2028 8.74072 8.74072 10.2028 8.74072 11.9999C8.74072 13.7969 10.2028 15.259 11.9999 15.259C13.7969 15.259 15.259 13.7969 15.259 11.9999C15.259 10.2028 13.7969 8.74072 11.9999 8.74072ZM11.9999 14.1726C10.8018 14.1726 9.82712 13.1979 9.82712 11.9999C9.82712 10.8018 10.8018 9.82712 11.9999 9.82712C13.1979 9.82712 14.1726 10.8018 14.1726 11.9999C14.1726 13.1979 13.1979 14.1726 11.9999 14.1726Z" fill="#808080" stroke="#808080" stroke-width="0.37"/>
                                    </svg>
                                    <svg class="main-profile-eye-icon main-profile-eye-icon-close" data-role="password.icon.close" width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M19.7811 4.21678C19.4954 3.927 19.0301 3.93109 18.7404 4.21678L16.1283 6.82884C12.8918 5.35956 9.43896 5.71872 6.14531 7.86959C3.69242 9.46948 2.20273 11.4816 2.14151 11.5673C1.9456 11.8367 1.95376 12.204 2.16191 12.4652C3.55773 14.1794 5.01886 15.514 6.51263 16.4445L4.21483 18.7423C3.92914 19.028 3.92914 19.4933 4.21483 19.783C4.35768 19.9259 4.54542 19.9994 4.73316 19.9994C4.92091 19.9994 5.10865 19.9259 5.25149 19.783L19.7811 5.25752C20.0668 4.97183 20.0668 4.50656 19.7811 4.21678ZM10.1818 12.7754C10.0756 12.5346 10.0226 12.2734 10.0226 11.9999C10.0226 11.4734 10.2267 10.9755 10.6021 10.6C11.1899 10.0123 12.0592 9.87353 12.7775 10.1796L10.1818 12.7754ZM13.8631 9.09807C12.5285 8.24099 10.7287 8.39608 9.56548 9.55927C8.91247 10.2123 8.55739 11.0775 8.55739 11.9958C8.55739 12.6652 8.74921 13.3059 9.10429 13.8569L7.59011 15.3711C6.26367 14.6038 4.95764 13.4692 3.68834 11.9877C4.23116 11.351 5.37802 10.1225 6.94934 9.09807C9.66751 7.32677 12.3734 6.94312 15.01 7.95122L13.8631 9.09807Z" fill="#808080"/>
                                        <path d="M21.8381 11.5345C20.83 10.2938 19.7852 9.24893 18.7241 8.42041C18.4016 8.17145 17.9405 8.22859 17.6874 8.54693C17.4384 8.86528 17.4956 9.32647 17.8139 9.57951C18.6547 10.2366 19.4954 11.0529 20.3117 12.0079C19.8301 12.5711 18.8914 13.5833 17.618 14.5098C15.1651 16.2933 12.6878 16.9382 10.2594 16.4239C9.86346 16.3382 9.47166 16.5954 9.39003 16.9912C9.30432 17.3871 9.56144 17.7789 9.95734 17.8606C10.5899 17.9953 11.2266 18.0606 11.8674 18.0606C12.8306 18.0606 13.802 17.9096 14.7692 17.6075C16.0385 17.2116 17.2997 16.5586 18.52 15.6689C20.577 14.1629 21.8055 12.5018 21.8585 12.4324C22.0545 12.163 22.0463 11.7957 21.8381 11.5345Z" fill="#808080"/>
                                    </svg>
                                </div>
                                <div class="intec-ui-form-field-description">
                                    <?= Loc::getMessage('C_MAIN_PROFILE_PASSWORD_FIELDS_PASSWORD_CONFIRM_DESCRIPTION') ?>
                                </div>
                            </div>
                        </div>
                        <div class="main-profile-form-buttons">
                            <?= Html::submitInput(Loc::getMessage('C_MAIN_PROFILE_PASSWORD_BUTTONS_SAVE'), [
                                'name' => 'save',
                                'class' => [
                                    'main-profile-form-button',
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
        'name': '[Component] bitrix:main.profile (change_psw)',
        'nodes': <?= JavaScript::toObject('#'.$sTemplateId) ?>,
        'loader': {
            'name': 'lazy'
        }
    });
</script>