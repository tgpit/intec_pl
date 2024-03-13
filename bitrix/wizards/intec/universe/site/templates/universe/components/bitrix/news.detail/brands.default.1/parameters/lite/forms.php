<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

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
        'value' => '['.$value['CODE'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
    ];
};

if (!empty($arCurrentValues['PRODUCTS_FORM_ID']) || !empty($arCurrentValues['PRODUCTS_FORM_REQUEST_ID'])) {
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
                'value' => $value['NAME'] . ' (' . $value['TEMPLATE'] . ')'
            ];
    });
} else {
    $formTemplates = [];
}

$arTemplateParameters['PRODUCTS_FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $forms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['PRODUCTS_FORM_ID'])) {
    $fields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['PRODUCTS_FORM_ID']
    ]))->indexBy('CODE');

    $arTemplateParameters['PRODUCTS_FORM_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $fields->asArray($hFields),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PRODUCTS_FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    unset($fields);
}

$arTemplateParameters['PRODUCTS_FORM_REQUEST_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_REQUEST_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $forms,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['PRODUCTS_FORM_REQUEST_ID'])) {
    $fields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['PRODUCTS_FORM_REQUEST_ID']
    ]))->indexBy('CODE');

    $arTemplateParameters['PRODUCTS_FORM_REQUEST_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_REQUEST_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $fields->asArray($hFields),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['PRODUCTS_FORM_REQUEST_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_NEWS_DETAIL_BRANDS_DETAIL_1_PRODUCTS_FORM_REQUEST_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $formTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    unset($fields);
}

unset($forms, $hFields, $formTemplates);