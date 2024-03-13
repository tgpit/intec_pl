<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var object $APPLICATION */
/** @var array $arParams */
/** @var mixed $component */

$APPLICATION->IncludeComponent('sitemap:catalog-sections', 'main', array(
    'TYPE' => 'index',
    'SEF_FOLDER'  => $arParams['SEF_FOLDER'],
), $component);
