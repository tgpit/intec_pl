<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

$arVisual = [
    'SHOW_IFRAME' => !empty($arParams['PATH_TO_CRM'])
];

$arResult['VISUAL'] = $arVisual;

unset($arVisual);