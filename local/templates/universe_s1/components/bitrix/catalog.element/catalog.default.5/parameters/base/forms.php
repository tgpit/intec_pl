<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;
use intec\core\helpers\ArrayHelper;

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

$arTemplateParameters['FORM_CHEAPER_SHOW'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_FORM_CHEAPER_SHOW'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_CHEAPER_SHOW'] === 'Y') {
    $arTemplateParameters['FORM_CHEAPER_ID'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_FORM_CHEAPER_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arForms->asArray(function ($iId, $arForm) {
            return [
                'key' => $arForm['ID'],
                'value' => '[' . $arForm['ID'].'] '.$arForm['NAME']
            ];
        }),
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];
    $arTemplateParameters['FORM_CHEAPER_TEMPLATE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_FORM_CHEAPER_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arFormsTemplates,
        'DEFAULT' => '.default'
    ];

    $arForm = ArrayHelper::getValue($arCurrentValues, 'FORM_CHEAPER_ID');
    $arForm = $arForms->get($arForm);

    if (!empty($arForm)) {
        $arFormFields = [];
        $rsFormFields = CFormField::GetList(
            $arForm['ID'],
            'N',
            $by = null,
            $asc = null,
            ['ACTIVE' => 'Y'],
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

            unset($rsFormAnswers, $arFormAnswer, $sType, $sId);
        }

        unset($arFormField, $rsFormFields);

        $arTemplateParameters['FORM_CHEAPER_PROPERTY_PRODUCT'] = [
            'PARENT' => 'VISUAL',
            'TYPE' => 'LIST',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_FORM_CHEAPER_PROPERTY_PRODUCT'),
            'VALUES' => $arFormFields,
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arFormFields);
    }

    unset($arForm);
}

unset($arForms, $arFormsTemplates);