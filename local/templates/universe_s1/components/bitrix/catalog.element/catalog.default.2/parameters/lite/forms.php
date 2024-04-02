<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

$arForms = array();
$dbForms = CStartShopForm::GetList();
while ($arForm = $dbForms->Fetch())
    $arForms[$arForm['ID']] = '['.$arForm['ID'].'] '.(!empty($arForm['LANG'][LANGUAGE_ID]['NAME']) ? $arForm['LANG'][LANGUAGE_ID]['NAME'] : $arForm['CODE']);

$rsTemplates = CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate);

$arTemplateParameters['WEB_FORM_TEXT'] = array(
    'PARENT' => 'VISUAL',
    'NAME' => GetMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_WEB_FORM_TEXT'),
    'TYPE' => 'STRING'
);

$arFields = null;
$arTemplates = null;

$hFieldsList = function ($sKey, $arProperty) {
    return [
        'key' => $arProperty['CODE'],
        'value' => '['.$arProperty['CODE'].'] '.$arProperty['LANG'][LANGUAGE_ID]['NAME']
    ];
};

if (!empty($arCurrentValues['FORM_ID'])) {
    $arFields = Arrays::fromDBResult(CStartShopFormProperty::GetList([], [
        'FORM' => $arCurrentValues['FORM_ID']
    ]))->indexBy('ID');


    $arTemplateParameters['FORM_CHEAPER_PROPERTY_PRODUCT'] = [
        'PARENT' => 'BASE',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_FORM_CHEAPER_PROPERTY_PRODUCT'),
        'TYPE' => 'LIST',
        'VALUES' => $arFields->asArray($hFieldsList),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
}

unset($arFields, $hFieldsList);