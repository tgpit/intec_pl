<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

$arForms = array();
$rsForms = CForm::GetList($by = 'sort', $order = 'asc', array(), $filtered = false);

while ($arForm = $rsForms->Fetch())
    $arForms[$arForm['ID']] = '['. $arForm['ID'] .'] '. $arForm['NAME'];

$rsTemplates = CComponentUtil::GetTemplatesList('bitrix:form.result.new', $siteTemplate);


if ($arCurrentValues['FORM_CHEAPER_SHOW'] === 'Y' && !empty($arCurrentValues['FORM_CHEAPER_ID'])) {
    $arForm = ArrayHelper::getValue($arCurrentValues, 'FORM_CHEAPER_ID');

    if (!empty($arForm)) {
        $arFormFields = [];
        $rsFormFields = (new CFormField)->GetList(
            $arForm,
            'N',
            $by = null,
            $asc = null,
            [
                'ACTIVE' => 'Y'
            ],
            $filtered = false
        );

        while ($arFormField = $rsFormFields->GetNext()) {
            $rsFormAnswers = (new CFormAnswer)->GetList(
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

        $arTemplateParameters['FORM_CHEAPER_PROPERTY_PRODUCT'] = array(
            'PARENT' => 'BASE',
            'TYPE' => 'LIST',
            'NAME' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_FORM_CHEAPER_PROPERTY_PRODUCT'),
            'VALUES' => $arFormFields,
            'ADDITIONAL_VALUES' => 'Y'
        );

        unset($arFormFields);
    }
}