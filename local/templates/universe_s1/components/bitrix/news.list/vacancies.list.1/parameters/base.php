<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arCurrentValues
 * @var array $arTemplateParameters
 */

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;


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

unset($arFormsTemplate);
unset($rsFormsTemplates);

$arForm = ArrayHelper::getValue($arCurrentValues, 'SUMMARY_FORM_ID');
$arForm = $arForms->get($arForm);

$arTemplateParameters['SUMMARY_FORM_ID'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_ID'),
    'TYPE' => 'LIST',
    'VALUES' => $arForms->asArray(function ($iId, $arForm) {
        return [
            'key' => $arForm['ID'],
            'value' => '[' . $arForm['ID'] . '] ' . $arForm['NAME']
        ];
    }),
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arForm)) {
    $arFormFields = [];
    $rsFormFields = CFormField::GetList(
        $arForm['ID'],
        'N',
        $by = null,
        $asc = null,
        [
            'ACTIVE' => 'Y'
        ],
        $filtered = false
    );

    while ($arFormField = $rsFormFields->GetNext()) {
        $rsFormAnswers = CFormAnswer::GetList(
            $arFormField['ID'],
            $sort = '',
            $order = '',
            [],
            $filtered = false
        );

        while ($arFormAnswer = $rsFormAnswers->GetNext()) {
            $sType = $arFormAnswer['FIELD_TYPE'];

            if (empty($sType))
                continue;

            $sId = 'form_'.$sType.'_'.$arFormAnswer['ID'];
            $arFormFields[$sId] = '['.$arFormAnswer['ID'].'] '.$arFormField['TITLE'];
        }
    }

    unset($arFormField);

    $arTemplateParameters['SUMMARY_FORM_VACANCY'] = array(
        'PARENT' => 'DATA_SOURCE',
        'TYPE' => 'LIST',
        'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_VACANCY'),
        'VALUES' => $arFormFields,
        'ADDITIONAL_VALUES' => 'Y'
    );

    unset($arFormFields);
}

$arTemplateParameters['SUMMARY_FORM_TEMPLATE'] = [
    'PARENT' => 'DATA_SOURCE',
    'NAME' => Loc::getMessage('C_NEWS_LIST_VACANCIES_LIST_1_SUMMARY_FORM_TEMPLATE'),
    'TYPE' => 'LIST',
    'VALUES' => $arFormsTemplates,
    'ADDITIONAL_VALUES' => 'Y'
];

unset($arForm);
unset($arFormsTemplates);
unset($arForms);
