<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

$arParams = ArrayHelper::merge([
    'META_TITLE_USE' => 'Y',
    'META_TITLE_AREA' => 'seoFilterMetaTitle',
    'META_TITLE_VARIABLE' => 'seoFilterMetaTitle',
    'META_KEYWORDS_USE' => 'Y',
    'META_KEYWORDS_AREA' => 'seoFilterMetaKeywords',
    'META_KEYWORDS_VARIABLE' => 'seoFilterMetaKeywords',
    'META_DESCRIPTION_USE' => 'Y',
    'META_DESCRIPTION_AREA' => 'seoFilterMetaDescription',
    'META_DESCRIPTION_VARIABLE' => 'seoFilterMetaDescription',
    'META_PAGE_TITLE_USE' => 'Y',
    'META_PAGE_TITLE_AREA' => 'seoFilterMetaPageTitle',
    'META_PAGE_TITLE_VARIABLE' => 'seoFilterMetaPageTitle',
    'META_BREADCRUMB_USE' => 'Y',
    'META_BREADCRUMB_ADD' => 'Y',
    'META_BREADCRUMB_AREA' => 'seoFilterMetaBreadcrumbName',
    'META_BREADCRUMB_VARIABLE' => 'seoFilterMetaBreadcrumbName',
    'META_DESCRIPTION_TOP_USE' => 'Y',
    'META_DESCRIPTION_TOP_AREA' => 'seoFilterMetaDescriptionTop',
    'META_DESCRIPTION_TOP_VARIABLE' => 'seoFilterMetaDescriptionTop',
    'META_DESCRIPTION_BOTTOM_USE' => 'Y',
    'META_DESCRIPTION_BOTTOM_AREA' => 'seoFilterMetaDescriptionBottom',
    'META_DESCRIPTION_BOTTOM_VARIABLE' => 'seoFilterMetaDescriptionBottom',
    'META_DESCRIPTION_ADDITIONAL_USE' => 'Y',
    'META_DESCRIPTION_ADDITIONAL_AREA' => 'seoFilterMetaDescriptionAdditional',
    'META_DESCRIPTION_ADDITIONAL_VARIABLE' => 'seoFilterMetaDescriptionAdditional'
], $arParams);

$arResult['PARTS'] = [
    'TITLE' => 'title',
    'KEYWORDS' => 'keywords',
    'DESCRIPTION' => 'description',
    'PAGE_TITLE' => 'pageTitle',
    'BREADCRUMB' => 'breadcrumbName',
    'DESCRIPTION_TOP' => 'descriptionTop',
    'DESCRIPTION_BOTTOM' => 'descriptionBottom',
    'DESCRIPTION_ADDITIONAL' => 'descriptionAdditional'
];

foreach ($arResult['PARTS'] as $sKey => $sMeta) {
    $arPart = [
        'USE' => $arParams['META_'.$sKey.'_USE'] === 'Y',
        'AREA' => $arParams['META_'.$sKey.'_AREA'],
        'VARIABLE' => $arParams['META_'.$sKey.'_VARIABLE'],
        'VALUE' => null
    ];

    if (!empty($sMeta))
        $arPart['VALUE'] = $arResult['META'][$sMeta];

    if ($sKey === 'BREADCRUMB') {
        $arPart['ADD'] = $arParams['META_'.$sKey.'_ADD'] === 'Y';
        $arPart['LINK'] = $arResult['META']['breadcrumbLink'];
    }

    if (empty($arPart['VALUE']) && !Type::isNumeric($arPart['VALUE']))
        $arPart['USE'] = false;

    if ($arPart['USE'] && !empty($arPart['VARIABLE']))
        $GLOBALS[$arPart['VARIABLE']] = $arPart['VALUE'];

    $arResult['PARTS'][$sKey] = $arPart;
}