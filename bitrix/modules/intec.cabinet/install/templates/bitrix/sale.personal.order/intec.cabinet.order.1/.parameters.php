<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\collections\Arrays;

/**
 * @var array $arCurrentValues
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('sale'))
    return;

$arOrdersStatuses = Arrays::fromDBResult(CSaleStatus::GetList(['SORT' => 'ASC'], [
    'LID' => LANGUAGE_ID
]))->indexBy('ID');

$arColors = [
    'green' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR_GREEN'),
    'yellow' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR_YELLOW'),
    'red' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR_RED'),
    'gray' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR_GRAY'),
    'current' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR_CURRENT')
];

foreach ($arOrdersStatuses as &$arOrdersStatus) {
    switch ($arOrdersStatuses['ID']) {
        case 'N': { $arOrdersStatus['COLOR'] = 'green'; break; }
        case 'P': { $arOrdersStatus['COLOR'] = 'yellow'; break; }
        case 'F': { $arOrdersStatus['COLOR'] = 'gray'; break; }
        default: { $arOrdersStatus['COLOR'] = 'gray'; break; }
    }

    $arTemplateParameters['STATUS_COLOR_'.$arOrdersStatus['ID']] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_STATUS_COLOR', [
            '#NAME#' => $arOrdersStatus['NAME']
        ]),
        'TYPE' => 'LIST',
        'VALUES' => $arColors,
        'DEFAULT' => $arOrdersStatus['COLOR']
    ];
}

unset($arOrdersStatus);

$arTemplateParameters['STATUS_COLOR_PSEUDO_CANCELLED'] = [
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_PSEUDO_CANCELLED_COLOR'),
    'TYPE' => 'LIST',
    'VALUES' => $arColors,
    'DEFAULT' => 'red'
];

$arTemplateParameters['PATH_TO_PAYMENT'] = [
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_PATH_TO_PAYMENT'),
    'TYPE' => 'STRING',
    'MULTIPLE' => 'N',
    'DEFAULT' => '/personal/basket/payment/',
    'PARENT' => 'ADDITIONAL_SETTINGS',
];

if (Loader::includeModule('support')) {
    global $USER_FIELD_MANAGER;
    $arUserFields = [];
    $arrUF = $USER_FIELD_MANAGER->GetUserFields( 'SUPPORT', 0, LANGUAGE_ID );

    foreach($arrUF as $FIELD_ID => $arField) {
        $arUserFields[$FIELD_ID] = $arField['EDIT_FORM_LABEL'];
    }

    $arTemplateParameters['PROPERTY_CLAIMS'] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_PROPERTY_CLAIMS'),
        'TYPE' => 'LIST',
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'VALUES' => $arUserFields,
        'ADDITIONAL_VALUES' => 'Y',
        'REFRESH' => 'Y'
    ];

    $arTemplateParameters['PATH_TO_CLAIMS'] = [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_ORDER_TEMPLATE_1_PATH_TO_CLAIMS'),
        'TYPE' => 'STRING',
        'MULTIPLE' => 'N',
        'DEFAULT' => '/personal/profile/claims/',
        'PARENT' => 'ADDITIONAL_SETTINGS',
    ];
}