<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arAuthParams
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponentTemplate $this
 */

$arAuthParams = !empty($arAuthParams) ? $arAuthParams : [];

?>
<!--noindex-->
<?php $APPLICATION->IncludeComponent(
    'bitrix:system.auth.form',
    'panel.2',
    ArrayHelper::merge([
        'LOGIN_URL' => $arResult['URL']['LOGIN'],
        'PROFILE_URL' => $arResult['URL']['PROFILE'],
        'FORGOT_PASSWORD_URL' => $arResult['URL']['PASSWORD'],
        'REGISTER_URL' => $arResult['URL']['REGISTER'],
        'MODAL_COMPONENT' => $arResult['AUTHORIZATION']['FORM']['COMPONENT'],
        'MODAL_TEMPLATE' => $arResult['AUTHORIZATION']['FORM']['TEMPLATE'],
        'CONSENT_URL' => $arParams['CONSENT_URL']
    ], $arAuthParams),
    $this->getComponent()
) ?>
<!--/noindex-->
<?php unset($arAuthParams) ?>