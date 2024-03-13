<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

global $APPLICATION;

if ($arResult['PARTS']['BREADCRUMB']['USE'] && $arResult['PARTS']['BREADCRUMB']['ADD'])
    $APPLICATION->AddChainItem($arResult['PARTS']['BREADCRUMB']['VALUE'], $arResult['PARTS']['BREADCRUMB']['LINK']);