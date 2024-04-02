<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arParams
 * @var array $arFields
 * @var CMail $APPLICATION
 * @var CBitrixComponent $component
 */

$GLOBALS['arCatalogProductsAdditional'] = [
    'ID' => $arFields['ADDITIONAL']['VALUES']
];

?>
<?php $APPLICATION->IncludeComponent(
    'bitrix:catalog.section',
    'products.additional.1',
    array(
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTION_USER_FIELDS' => array(),
        'SHOW_ALL_WO_SECTION' => 'Y',
        'FILTER_NAME' => 'arCatalogProductsAdditional',
        'PRICE_CODE' => $arParams['PRICE_CODE'],
        'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
        'CURRENCY_ID' => $arParams['CURRENCY_ID'],
        'RECALCULATION_ADDITIONAL_PRICES_USE' => $arParams['RECALCULATION_ADDITIONAL_PRICES_USE']
    ),
    $component
) ?>