<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

$arForms = Arrays::fromDBResult(CStartShopForm::GetList())->indexBy('ID');

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray(function ($key, $value) {
        return [
            'key' => $value['ID'],
            'value' => '['.$value['ID'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplates = Arrays::from(
        CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate)
    )->indexBy('NAME');

    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates->asArray(function ($key, $value) {
            return [
                'key' => $value['NAME'],
                'value' => $value['NAME'].'('.$value['TEMPLATE'].')'
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    $arFields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['FORM_ID']
    ]))->indexBy('ID');

    $arTemplateParameters['FORM_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_FORM_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields->asArray(function ($key, $value) {
            return [
                'key' => $value['CODE'],
                'value' => '['.$value['CODE'].'] '.$value['LANG'][LANGUAGE_ID]['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y'
    ];

    unset($arFields, $arTemplates);
}

unset($arForms);