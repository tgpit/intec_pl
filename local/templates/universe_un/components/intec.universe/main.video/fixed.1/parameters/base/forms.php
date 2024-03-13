<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

$arForms = Arrays::fromDBResult(CForm::GetList(
    $sBy = 'sort',
    $sOrder = 'asc',
    [],
    $bIsFiltered
))->indexBy('ID');

$rsFormsTemplates = CComponentUtil::GetTemplatesList('bitrix:form.result.new', $siteTemplate);
$arFormsTemplates = [];

foreach ($rsFormsTemplates as $arFormsTemplate)
    $arFormsTemplates[$arFormsTemplate['NAME']] = $arFormsTemplate['NAME'].(!empty($arFormsTemplate['TEMPLATE']) ? ' ('.$arFormsTemplate['TEMPLATE'].')' : null);

unset($rsFormsTemplates, $arFormsTemplate);

$arTemplateParameters['FORM_ID'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray(function ($iId, $arForm) {
        return [
            'key' => $arForm['ID'],
            'value' => '[' . $arForm['ID'].'] '.$arForm['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y'
];
$arTemplateParameters['FORM_TEMPLATE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_MAIN_VIDEO_FIXED_1_FORM_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arFormsTemplates,
    'DEFAULT' => '.default'
];

unset($arForms, $arFormsTemplates);