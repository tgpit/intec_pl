<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

global $USER_FIELD_MANAGER;
$arUserFields = [];
$arrUF = $USER_FIELD_MANAGER->GetUserFields( 'SUPPORT', 0, LANGUAGE_ID );

foreach($arrUF as $FIELD_ID => $arField) {
    $arUserFields[$FIELD_ID] = $arField['EDIT_FORM_LABEL'];
}

$arTemplateParameters = [];

$arTemplateParameters['TICKET_EDIT_TEMPLATE'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SUPPORT_WIZARD_1_TICKET_EDIT_TEMPLATE'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['TICKET_LIST_URL'] = [
    'PARENT' => 'URL_TEMPLATES',
    'NAME' => Loc::getMessage('C_SUPPORT_WIZARD_1_TICKET_LIST_URL'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['FILTER_USER_FIELD'] = [
    'NAME' => Loc::getMessage('C_SUPPORT_WIZARD_1_LIST_FILTER_USER_FIELD'),
    'TYPE' => 'LIST',
    'PARENT' => 'ADDITIONAL_SETTINGS',
    'VALUES' => $arUserFields,
    'ADDITIONAL_VALUES' => 'Y',
    'REFRESH' => 'Y'
];

if (!empty($arCurrentValues['FILTER_USER_FIELD'])) {
    $arTemplateParameters['ORDER_DETAIL_URL'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_SUPPORT_WIZARD_1_ORDER_DETAIL_URL'),
        'TYPE' => 'STRING'
    ];
}
    
?>