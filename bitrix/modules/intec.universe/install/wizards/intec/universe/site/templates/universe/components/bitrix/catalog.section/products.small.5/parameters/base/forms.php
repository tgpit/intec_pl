<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

$arForms = Arrays::fromDBResult(CForm::GetList(
    $by = 'sort',
    $order = 'asc',
    [],
    $isFiltered = false
))->indexBy('ID')->asArray(function ($key, $value) {
    return [
        'key' => $key,
        'value' => '['.$key.'] '.$value['NAME']
    ];
});
$arTemplates = Arrays::from(
    CComponentUtil::GetTemplatesList('bitrix:form.result.new', $siteTemplate)
)->indexBy('NAME')->asArray(function ($key, $value) {
    return [
        'key' => $key,
        'value' => $key.(!empty($value['TEMPLATE']) ? ' ('.$value['TEMPLATE'].')' : null)
    ];
});

$hAnswers = function ($key, $value) {
    $field = Arrays::fromDBResult(CFormAnswer::GetList(
        $value['ID'],
        $sort = '',
        $order = '',
        [],
        $isFiltered = false
    ))->getFirst();

    if (empty($field['FIELD_TYPE']))
        return ['skip' => true];

    return [
        'key' => 'form_'.$field['FIELD_TYPE'].'_'.$field['ID'],
        'value' => '['.$field['ID'].'] '.$value['TITLE']
    ];
};

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $arFields = Arrays::fromDBResult(CFormField::GetList(
        $arCurrentValues['FORM_ID'],
        'N',
        $by = null,
        $asc = null,
        ['ACTIVE' => 'Y'],
        $filtered = false
    ))->indexBy('ID')->asArray($hAnswers);

    $arTemplateParameters['FORM_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    unset($arFields);
}

$arTemplateParameters['FORM_REQUEST_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_REQUEST_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_REQUEST_ID'])) {
    $arFields = Arrays::fromDBResult(CFormField::GetList(
        $arCurrentValues['FORM_REQUEST_ID'],
        'N',
        $by = null,
        $asc = null,
        ['ACTIVE' => 'Y'],
        $filtered = false
    ))->indexBy('ID')->asArray($hAnswers);

    $arTemplateParameters['FORM_REQUEST_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_REQUEST_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['FORM_REQUEST_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_FORM_REQUEST_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    unset($arFields);
}

unset($arForms, $arTemplates, $hAnswers);