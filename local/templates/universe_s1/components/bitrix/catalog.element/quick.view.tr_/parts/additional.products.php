<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arFields
 * @var CMail $APPLICATION
 * @var CBitrixComponent $component
 */

$GLOBALS['arCatalogProductsAdditional'] = [
    'ID' => $arVisual['ADDITIONAL_PRODUCTS']['VALUES']
];

$APPLICATION->IncludeComponent(
    'bitrix:catalog.section',
    'products.additional.1',
    [
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_USER_FIELDS' => [],
        'SHOW_ALL_WO_SECTION' => 'Y',
        'FILTER_NAME' => 'arCatalogProductsAdditional',
        'PRICE_CODE' => $arParams['PRICE_CODE'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID']
    ],
    $component,
    ['HIDE_ICONS' => 'Y']
);
