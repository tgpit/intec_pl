<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

//Для авторизации через соц сети, необходимо указать URL личного кабинета
//для корректного редиректа после авторизации
$currentPage = $APPLICATION->GetCurPage();
$APPLICATION->SetCurPage($arParams['AUTH_URL']);

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arAuthOptions = [
    'component' => 'bitrix:system.auth.authorize',
    'template' => 'popup.2',
    'parameters' => [
        'AUTH_URL' => $arParams['AUTH_URL'],
        'BACKURL' => $arResult['BACKURL'],
        'AUTH_REGISTER_URL' => $arParams['AUTH_REGISTER_URL'],
        'AUTH_FORGOT_PASSWORD_URL' => $arParams['AUTH_FORGOT_PASSWORD_URL'],
        'AJAX_MODE' => 'N',
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_auth_form',
        'AUTH_RESULT' => $APPLICATION->arAuthResult
    ]
];

$bUserPhoneRequired = COption::GetOptionString('main','new_user_phone_auth');

$arRegFields = [
    'NAME',
    'EMAIL'
];

if ($bUserPhoneRequired !== 'Y')
    $arRegFields[] = 'PERSONAL_PHONE';

$arRegOptions = [
    'component' => 'bitrix:main.register',
    'template' => 'template.2',
    'parameters' => [
        'SHOW_FIELDS' => [],
        'REQUIRED_FIELDS' => [
            0 => 'EMAIL'
        ],
        'AUTH' => 'Y',
        'AUTH_URL' => $arParams['AUTH_URL'],
        'USE_BACKURL' => 'Y',
        'SUCCESS_PAGE' => '',
        'SET_TITLE' => 'N',
        'USER_PROPERTY' => [],
        'USER_PROPERTY_NAME' => '',
        'COMPONENT_TEMPLATE' => 'template.2',
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_ADDITIONAL' => $sTemplateId.'_register_form',
        'AJAX_OPTION_JUMP' => 'N',
        'CONSENT_URL' => $arParams['CONSENT_URL']
    ]
];

?>

<div id="<?= $sTemplateId ?>" class="ns-bitrix c-system-auth-form c-system-auth-form-template-2">
    <div class="system-auth-form-control-tabs" data-ui-control="tabs">
        <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-16">
            <div class="system-auth-form-control-tab-container intec-grid-item-auto">
                <a href="#<?= $sTemplateId ?>_tab_auth" data-type="tab" class="system-auth-form-control-tab">
                    <?= Loc::getMessage('C_SYSTEM_AUTH_FORM_TEMPLATE_2_TEMPLATE_TAB_AUTH') ?>
                </a>
                <div class="system-auth-form-control-tab-border intec-cl-background"></div>
            </div>
            <?php if (COption::GetOptionString('main', 'new_user_registration', 'N') != 'N') { ?>
                <div class="system-auth-form-control-tab-container intec-grid-item-auto">
                    <a href="#<?= $sTemplateId ?>_tab_registration" data-type="tab" class="system-auth-form-control-tab">
                        <?= Loc::getMessage('C_SYSTEM_AUTH_FORM_TEMPLATE_2_TEMPLATE_TAB_REGISTRATION') ?>
                    </a>
                    <div class="system-auth-form-control-tab-border intec-cl-background"></div>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="intec-ui intec-ui-control-tabs-content">
        <div id="<?= $sTemplateId ?>_tab_auth" class="intec-ui-part-tab">
            <?php $APPLICATION->IncludeComponent(
                $arAuthOptions['component'],
                $arAuthOptions['template'],
                $arAuthOptions['parameters'],
                false
            ) ?>
        </div>
        <?php if (COption::GetOptionString('main', 'new_user_registration', 'N') != 'N') { ?>
            <?php $APPLICATION->SetCurPage($currentPage);?>
            <div id="<?= $sTemplateId ?>_tab_registration" class="intec-ui-part-tab">
                <?php $APPLICATION->IncludeComponent(
                    $arRegOptions['component'],
                    $arRegOptions['template'],
                    $arRegOptions['parameters'],
                    false
                ) ?>
            </div>
        <?php } ?>
    </div>
</div>
