<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$arForms = Arrays::fromDBResult(CForm::GetList(
    $by = 'sort',
    $order = 'asc',
    [],
    $filter = false
))->indexBy('ID');

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray(function ($key, $value) {
        return [
            'key' => $key,
            'value' => '['.$key.'] '.$value['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

unset($arForms);

if (!empty($arCurrentValues['FORM_ID'])) {
    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList(
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

    $arTemplateParameters['FORM_TEMPLATE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates,
        'ADDITIONAL_VALUES' => 'Y',
        'DEFAULT' => '.default'
    ];
    $arTemplateParameters['FORM_TITLE'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_TITLE'),
        'TYPE' => 'STRING',
        'DEFAULT' => Loc::getMessage('C_WIDGET_CONTACTS_1_FORM_TITLE_DEFAULT')
    ];

    unset($arTemplates);
}