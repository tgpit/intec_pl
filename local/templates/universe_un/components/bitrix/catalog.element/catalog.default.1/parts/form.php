<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

/**
 * @var array $arParams
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$APPLICATION->IncludeComponent(
    'intec.universe:main.widget',
    'form.5', [
        'CACHE_TIME' => $arParams['CACHE_TIME'],
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CONSENT_URL' => $arParams['CONSENT_URL'],
        'FORM_ID' => $arParams['WEB_FORM_ID'],
        'FORM_TEMPLATE' => $arParams['WEB_FORM_TEMPLATE']
    ],
    $component
);