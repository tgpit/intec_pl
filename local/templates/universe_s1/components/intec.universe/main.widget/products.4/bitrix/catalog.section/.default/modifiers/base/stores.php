<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 */

$arResult['STORES'] = [
    'PARAMETERS' => [
        'STORES' => $arParams['STORES_ID'],
        'ELEMENT_ID' => null,
        'OFFER_ID' => null,
        'FIELDS' => $arParams['STORES_FIELDS'],
        'USE_MIN_AMOUNT' => $arParams['STORES_MIN_AMOUNT_USE'] === 'Y' ? 'Y' : 'N',
        'MIN_AMOUNT' => $arParams['STORES_MIN_AMOUNT'],
        'SHOW_GENERAL_STORE_INFORMATION' => $arParams['STORES_GENERAL_INFORMATION'] === 'Y' ? 'Y' : 'N',
        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
        'CACHE_TIME' => $arParams['CACHE_TIME']
    ]
];