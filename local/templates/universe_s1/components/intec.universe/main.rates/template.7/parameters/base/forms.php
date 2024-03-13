<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$arTemplateParameters['FORM_ORDER_USE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_ORDER_USE'] !== 'Y')
    return;

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

if (!empty($arCurrentValues['FORM_ORDER_ID'])) {
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
                'value' => $value['NAME'].' ('.$value['TEMPLATE'].')'
            ];
    });
} else {
    $formTemplates = [];
}

$arTemplateParameters['FORM_ORDER_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $forms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ORDER_ID'])) {
    $formFields = Arrays::fromDBResult(CFormField::GetList(
        $arCurrentValues['FORM_ORDER_ID'],
        'N',
        $by = null,
        $order = null,
        ['ACTIVE' => 'Y'],
        $filtered = false
    ))->asArray($hFields);

    $arTemplateParameters['FORM_ORDER_TITLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TITLE_DEFAULT')
    ];
    $arTemplateParameters['FORM_ORDER_PROPERTY_INSERT'] = [
        'PARENT' => 'BASE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_PROPERTY_INSERT'),
        'VALUES' => $formFields,
        'ADDITIONAL_VALUES' => 'Y'
    ];
    $arTemplateParameters['FORM_ORDER_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    if ($arCurrentValues['SETTINGS_USE'] !== 'Y') {
        $arTemplateParameters['CONSENT_USE'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_CONSENT_USE'),
            'TYPE' => 'CHECKBOX',
            'DEFAULT' => 'N',
            'REFRESH' => 'Y'
        ];
    }

    if ($arCurrentValues['CONSENT_USE'] === 'Y' || $arCurrentValues['SETTINGS_USE'] === 'Y') {
        $arTemplateParameters['CONSENT_URL'] = [
            'PARENT' => 'BASE',
            'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_CONSENT_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#SITE_DIR#company/consent/'
        ];
    }

    unset($formFields);
}

unset($forms, $hFields);