<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$forms = Arrays::fromDBResult(CStartShopForm::GetList())->indexBy('ID')->asArray(function ($key, $value) {
    return [
        'key' => $key,
        'value' => '['.$value['ID'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
    ];
});

$hFields = function ($key, $value) {
    if (empty($value['CODE']))
        return ['skip' => true];

    return [
        'key' => $key,
        'value' => '['.$key.'] '.$value['LANG'][LANGUAGE_ID]['NAME']
    ];
};

if (!empty($arCurrentValues['FORM_ID']) || !empty($arCurrentValues['FORM_REQUEST_ID'])) {
    $formTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
        'intec:startshop.forms.result.new',
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
    $fields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['FORM_ORDER_ID']
    ]))->indexBy('CODE');

    $arTemplateParameters['FORM_ORDER_TITLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TITLE_DEFAULT')
    ];
    $arTemplateParameters['FORM_ORDER_PROPERTY_INSERT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_PROPERTY_INSERT'),
        'TYPE' => 'LIST',
        'VALUES' => $fields->asArray($hFields),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['FORM_ORDER_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_RATES_TEMPLATE_7_FORM_ORDER_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
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

    unset($fields);
}

unset($forms, $hFields, $formTemplates);