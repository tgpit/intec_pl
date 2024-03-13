<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 * @global CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

if (!CModule::IncludeModule('intec.core'))
    return;

$APPLICATION->IncludeComponent(
    'intec.universe:iblock.elements',
    '',
    Array(
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'SECTIONS_ID' => $arParams['SECTIONS_ID'],
        'SECTIONS_CODE' => array(),
        'ELEMENTS_ID' => $arParams['ELEMENTS_ID'],
        'ELEMENTS_COUNT' => $arParams['ELEMENTS_COUNT'],
        'HEADER_SHOW' => $arParams['HEADER_SHOW'],
        'HEADER_CENTER' => $arParams['HEADER_CENTER'],
        'HEADER' => $arParams['HEADER'],
        'DESCRIPTION_SHOW' => $arParams['DESCRIPTION_SHOW'],
        'DESCRIPTION_CENTER' => $arParams['DESCRIPTION_CENTER'],
        'DESCRIPTION' => $arParams['DESCRIPTION'],
        'BIG_FIRST_BLOCK' => $arParams['BIG_FIRST_BLOCK'],
        'HEADER_ELEMENT_SHOW' => $arParams['HEADER_ELEMENT_SHOW'],
        'DESCRIPTION_ELEMENT_SHOW' => $arParams['DESCRIPTION_ELEMENT_SHOW'],
        'SETTINGS_USE' => $arParams['SETTINGS_USE'],
        'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
        'SEE_ALL_SHOW' => $arParams['SEE_ALL_SHOW'],
        'SEE_ALL_POSITION' => $arParams['SEE_ALL_POSITION'],
        'SEE_ALL_TEXT' => $arParams['SEE_ALL_TEXT'],
        'SEE_ALL_URL' => $arParams['SEE_ALL_URL'],
        'HIDDE_NON_ACTIVE' => $arParams['HIDDE_NON_ACTIVE']
    ),
    $component
);?>