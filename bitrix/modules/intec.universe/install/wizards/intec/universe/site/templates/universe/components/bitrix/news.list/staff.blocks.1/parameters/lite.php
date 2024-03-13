<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arTemplateParameters
 * @var array $arCurrentValues
 */

$arTemplateParameters['FORM_ASK_USE'] = [
    'PARENT' => 'VISUAL',
    'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_USE'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N',
    'REFRESH' => 'Y'
];

if ($arCurrentValues['FORM_ASK_USE'] === 'Y') {
    $arTemplates = [];
    $rsTemplates = CComponentUtil::GetTemplatesList('intec:startshop.forms.result.new', $siteTemplate);

    foreach ($rsTemplates as $arTemplate)
        $arTemplates[$arTemplate['NAME']] = $arTemplate['NAME'].(!empty($arTemplate['TEMPLATE']) ? ' ('.$arTemplate['TEMPLATE'].')' : null);

    unset($rsTemplates, $arTemplate);

    $arTemplateParameters['FORM_ASK_TEMPLATE'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_TEMPLATE'),
        'TYPE' => 'LIST',
        'VALUES' => $arTemplates
    ];

    unset($arTemplates);

    $arForms = [];
    $rsForms = CStartShopForm::GetList();

    while ($arForm = $rsForms->Fetch())
        $arForms[$arForm['ID']] = '['.$arForm['ID'].'] '.(!empty($arForm['LANG'][LANGUAGE_ID]['NAME']) ? $arForm['LANG'][LANGUAGE_ID]['NAME'] : $arForm['CODE']);

    unset($rsForms, $arForm);

    $arTemplateParameters['FORM_ASK_ID'] = [
        'PARENT' => 'VISUAL',
        'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_ID'),
        'TYPE' => 'LIST',
        'VALUES' => $arForms,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    unset($arForms);

    if (!empty($arCurrentValues['FORM_ASK_ID'])) {
        $arFields = [];
        $rsFields = CStartShopFormProperty::GetList([], [
            'FORM' => $arCurrentValues['FORM_ASK_ID']
        ]);

        while ($arField = $rsFields->GetNext())
            $arFields[$arField['ID']] = '['.$arField['ID'].'] '.$arField['LANG']['ru']['NAME'];

        unset($rsFields, $arField);

        $arTemplateParameters['FORM_ASK_FIELD'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_FIELD'),
            'TYPE' => 'LIST',
            'VALUES' => $arFields,
            'ADDITIONAL_VALUES' => 'Y'
        ];

        unset($arFields);

        $arTemplateParameters['FORM_ASK_TITLE'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_TITLE'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_TITLE_DEFAULT')
        ];
        $arTemplateParameters['FORM_ASK_BUTTON_TEXT'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_BUTTON_TEXT'),
            'TYPE' => 'STRING',
            'DEFAULT' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_BUTTON_TEXT_DEFAULT')
        ];
        $arTemplateParameters['FORM_ASK_CONSENT_URL'] = [
            'PARENT' => 'VISUAL',
            'NAME' => Loc::getMessage('C_NEWS_LIST_STAFF_BLOCKS_1_FORM_ASK_CONSENT_URL'),
            'TYPE' => 'STRING',
            'DEFAULT' => '#SITE_DIR#company/consent/'
        ];
    }
}