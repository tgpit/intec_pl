<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 */

$this->setFrameMode(true);

if (empty($arResult['PRODUCTS']))
    return;

$GLOBALS[$arParams['PRODUCTS_FILTER']] = [
    'ID' => $arResult['PRODUCTS']
];

$APPLICATION->IncludeComponent(
    'bitrix:catalog.section',
    '.default',
    $arResult['PARAMETERS'],
    $component
);