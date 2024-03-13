<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

/** @var array $arParams */

$arDefaultUrlTemplates404 = array(
    'sitemap'           => '',
    'catalog-sections'  => 'catalog/sections.xml',
    'catalog-elements'  => 'catalog/#SECTION_CODE#/elements.xml',
);

$arComponentVariables = array(
    'SECTION_CODE',
    'ELEMENT_ID',
);

$arVariables = array();
$arUrlTemplates = $arDefaultUrlTemplates404;

$componentPage = CComponentEngine::ParseComponentPath(
    $arParams['SEF_FOLDER'],
    $arUrlTemplates,
    $arVariables
);

if (!$componentPage) {
    $componentPage = 'sitemap';
}

$arResult = array(
    'FOLDER'        => $arParams['SEF_FOLDER'],
    'URL_TEMPLATES' => $arUrlTemplates,
    'VARIABLES'     => $arVariables
);
$this->IncludeComponentTemplate($componentPage);
