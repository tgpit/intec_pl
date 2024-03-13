<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

$arForms = Arrays::fromDBResult(CStartShopForm::GetList())->indexBy('ID');

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
$arTemplates = Arrays::from(
    CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate)
)->indexBy('NAME');

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray($hFormsList),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];
$arTemplateParameters['FORM_TEMPLATE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arTemplates->asArray($hTemplatesList),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

unset($arForms, $hFormsList, $hTemplatesList, $arTemplates);