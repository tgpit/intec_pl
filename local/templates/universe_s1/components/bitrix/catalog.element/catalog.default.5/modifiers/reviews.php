<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['REVIEWS'] = [
    'SHOW' => $arParams['REVIEWS_SHOW'] === 'Y',
    'NAME' => $arParams['REVIEWS_NAME']
];

if ($arResult['REVIEWS']['SHOW'] && (empty($arParams['REVIEWS_IBLOCK_ID']) || empty($arParams['REVIEWS_PROPERTY_ELEMENT_ID'])))
    $arResult['REVIEWS']['USE'] = false;