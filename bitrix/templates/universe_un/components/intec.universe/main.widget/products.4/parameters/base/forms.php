<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$forms = Arrays::fromDBResult(CForm::GetList(
    $by = 'sort',
    $order = 'asc',
    [],
    $isFiltered
))->indexBy('ID')->asArray(function ($key, $value) {
    return [
        'key' => $key,
        'value' => '['.$key.'] '.$value['NAME']
    ];
});

$hFields = function ($key, $value) {
    $fields = Arrays::fromDBResult(CFormAnswer::GetList(
        $value['ID'],
        $by = '',
        $order = '',
        [],
        $filtered = false
    ))->indexBy('ID');

    $fields = $fields->get($value['ID']);

    if (!empty($fields))
        return [
            'key' => 'form_'.$fields['FIELD_TYPE'].'_'.$value['ID'],
            'value' => '['.$fields['ID'].'] '.$value['TITLE']
        ];
    else
        return ['skip' => true];
};

if (!empty($arCurrentValues['FORM_ID']) || !empty($arCurrentValues['FORM_REQUEST_ID'])) {
    $formTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        'bitrix:form.result.new',
        $siteTemplate
    ))->indexBy('NAME')->asArray(function ($key, $value) {
        if (empty($value['TEMPLATE']))
            return [
                'key' => $key,
                'value' => $value['NAME']
            ];
        else
            return [
                'key' => $key,
                'value' => $value['NAME'] . ' (' . $value['TEMPLATE'] . ')'
            ];
    });
} else {
    $formTemplates = [];
}

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $forms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $formFields = Arrays::fromDBResult(CFormField::GetList(
        $arCurrentValues['FORM_ID'],
        'N',
        $by = null,
        $order = null,
        ['ACTIVE' => 'Y'],
        $filtered = false
    ))->asArray($hFields);

    $arTemplateParameters['FORM_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_PROPERTY_PRODUCT'),
        'VALUES' => $formFields,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    unset($formFields);
}

$arTemplateParameters['FORM_REQUEST_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_REQUEST_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $forms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_REQUEST_ID'])) {
    $formFields = Arrays::fromDBResult(CFormField::GetList(
        $arCurrentValues['FORM_REQUEST_ID'],
        'N',
        $by = null,
        $order = null,
        ['ACTIVE' => 'Y'],
        $filtered = false
    ))->asArray($hFields);

    $arTemplateParameters['FORM_REQUEST_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_REQUEST_PROPERTY_PRODUCT'),
        'VALUES' => $formFields,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['FORM_REQUEST_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_PRODUCTS_4_FORM_REQUEST_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    unset($formFields);
}

unset($forms, $hFields, $formTemplates);