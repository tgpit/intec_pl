<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$arForms = Arrays::fromDBResult(CStartShopForm::GetList())->indexBy('ID');

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray(function ($key, $value) {
        $name = !empty($value['LANG'][LANGUAGE_ID]['NAME']) ? $value['LANG'][LANGUAGE_ID]['NAME'] : $value['CODE'];

        return [
            'key' => $key,
            'value' => '['.$key.'] '.$name
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

unset($arForms);

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
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

    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];

    unset($arTemplates);
}