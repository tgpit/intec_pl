<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var object $APPLICATION */
/** @var array $arResult */
/** @var array $arParams */
/** @var mixed $component */

$APPLICATION->IncludeComponent('sitemap:catalog-elements', 'main', array(
    'SECTION_CODE' => $arResult['VARIABLES']['SECTION_CODE'],
    'SEF_FOLDER'   => $arParams['SEF_FOLDER'],
), $component);
