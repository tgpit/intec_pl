<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\collections\Arrays;
use Bitrix\Main\Localization\Loc;

$arForms = Arrays::fromDBResult(CStartShopForm::GetList())->indexBy('ID');
$arFields = null;
$arTemplates = null;

$hFormsList = function ($sKey, $arProperty) {
    return [
        'key' => $arProperty['ID'],
        'value' => '['.$arProperty['ID'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
    ];
};
$hTemplatesList = function ($sKey, $arProperty) {
    return [
        'key' => $arProperty['NAME'],
        'value' => $arProperty['NAME'].'('.$arProperty['TEMPLATE'].')'
    ];
};
$hFieldsList = function ($sKey, $arProperty) {
    return [
        'key' => $arProperty['CODE'],
        'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
    ];
};

if (!empty($arCurrentValues['SUMMARY_FORM_ID'])) {
    $arFields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['SUMMARY_FORM_ID']
    ]))->indexBy('ID');
    $arTemplates = Arrays::from(CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate))->indexBy('NAME');
}

$arTemplateParameters['SUMMARY_FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray($hFormsList),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['SUMMARY_FORM_ID'])) {
    $arTemplateParameters['SUMMARY_FORM_VACANCY'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_VACANCY'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields->asArray($hFieldsList),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['SUMMARY_FORM_TEMPLATE'] = [
        'PARENT' => 'DATA_SOURCE',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates->asArray($hTemplatesList),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

unset($arForms, $arFields, $arTemplates, $hFormsList, $hFieldsList, $hTemplatesList);
