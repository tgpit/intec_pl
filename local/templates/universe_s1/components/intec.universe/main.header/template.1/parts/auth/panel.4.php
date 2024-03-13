<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;

$arAuthParams = !empty($arAuthParams) ? $arAuthParams : [];

?>
<!--noindex-->
<?php $APPLICATION->IncludeComponent(
    'bitrix:system.auth.form',
    'panel.3',
    ArrayHelper::merge([
        'LOGIN_URL' => $arResult['URL']['LOGIN'],
        'PROFILE_URL' => $arResult['URL']['PROFILE'],
        'FORGOT_PASSWORD_URL' => $arResult['URL']['PASSWORD'],
        'REGISTER_URL' => $arResult['URL']['REGISTER'],
        'MENU_PERSONAL_SECTION' => $arParams['MENU_PERSONAL_SECTION'],
        'MODAL_COMPONENT' => $arResult['AUTHORIZATION']['FORM']['COMPONENT'],
        'MODAL_TEMPLATE' => $arResult['AUTHORIZATION']['FORM']['TEMPLATE'],
        'CONSENT_URL' => $arParams['CONSENT_URL']
    ], $arAuthParams),
    $this->getComponent()
) ?>
<!--/noindex-->
<?php unset($arAuthParams) ?>