<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

/**
 * @var array $arParams
 * @var array $arResult
 * @global CMain $APPLICATION
 */

$arResult['LOGIN_URL'] = $arParams['LOGIN_URL'];
$arResult['LOGOUT_URL'] = $APPLICATION->GetCurPageParam('logout=yes&'.bitrix_sessid_get(), ['logout', 'login', 'sessid']);

$arResult['VISUAL'] = [
    'THEME' => $arParams['THEME']
];