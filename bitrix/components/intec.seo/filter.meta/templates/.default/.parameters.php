<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('iblock'))
    return;

if (!Loader::includeModule('intec.core'))
    return;

$arCurrentValues = ArrayHelper::merge([
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
], $arCurrentValues);

$arTemplateParameters = [];
$arTemplateParameters['META_TITLE_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_TITLE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_TITLE_USE'] === 'Y') {
    $arTemplateParameters['META_TITLE_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_TITLE_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaTitle'
    ];

    $arTemplateParameters['META_TITLE_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_TITLE_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaTitle'
    ];
}

$arTemplateParameters['META_KEYWORDS_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_KEYWORDS_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_KEYWORDS_USE'] === 'Y') {
    $arTemplateParameters['META_KEYWORDS_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_KEYWORDS_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaKeywords'
    ];

    $arTemplateParameters['META_KEYWORDS_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_KEYWORDS_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaKeywords'
    ];
}

$arTemplateParameters['META_DESCRIPTION_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_DESCRIPTION_USE'] === 'Y') {
    $arTemplateParameters['META_DESCRIPTION_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescription'
    ];

    $arTemplateParameters['META_DESCRIPTION_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescription'
    ];
}

$arTemplateParameters['META_PAGE_TITLE_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_PAGE_TITLE_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_PAGE_TITLE_USE'] === 'Y') {
    $arTemplateParameters['META_PAGE_TITLE_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_PAGE_TITLE_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaPageTitle'
    ];

    $arTemplateParameters['META_PAGE_TITLE_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_PAGE_TITLE_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaPageTitle'
    ];
}

$arTemplateParameters['META_BREADCRUMB_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_BREADCRUMB_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_BREADCRUMB_USE'] === 'Y') {
    $arTemplateParameters['META_BREADCRUMB_ADD'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_BREADCRUMB_ADD'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'Y'
    ];

    $arTemplateParameters['META_BREADCRUMB_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_BREADCRUMB_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaBreadcrumbName'
    ];

    $arTemplateParameters['META_BREADCRUMB_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_BREADCRUMB_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaBreadcrumbName'
    ];
}

$arTemplateParameters['META_DESCRIPTION_TOP_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_TOP_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_DESCRIPTION_TOP_USE'] === 'Y') {
    $arTemplateParameters['META_DESCRIPTION_TOP_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_TOP_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionTop'
    ];

    $arTemplateParameters['META_DESCRIPTION_TOP_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_TOP_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionTop'
    ];
}

$arTemplateParameters['META_DESCRIPTION_BOTTOM_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_BOTTOM_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_DESCRIPTION_BOTTOM_USE'] === 'Y') {
    $arTemplateParameters['META_DESCRIPTION_BOTTOM_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_BOTTOM_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionBottom'
    ];

    $arTemplateParameters['META_DESCRIPTION_BOTTOM_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_BOTTOM_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionBottom'
    ];
}

$arTemplateParameters['META_DESCRIPTION_ADDITIONAL_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_ADDITIONAL_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'Y',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['META_DESCRIPTION_ADDITIONAL_USE'] === 'Y') {
    $arTemplateParameters['META_DESCRIPTION_ADDITIONAL_AREA'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_ADDITIONAL_AREA'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionAdditional'
    ];

    $arTemplateParameters['META_DESCRIPTION_ADDITIONAL_VARIABLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_INTEC_SEO_FILTER_DEFAULT_META_DESCRIPTION_ADDITIONAL_VARIABLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'seoFilterMetaDescriptionAdditional'
    ];
}