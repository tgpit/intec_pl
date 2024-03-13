<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;

if (!Loader::includeModule('iblock') || !Loader::includeModule('intec.core'))
    return;

$arParameters = [];
$arParameters['TITLE_SET'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_TITLE_SET'),
    'TYPE' => 'CHECKBOX',
    'DEFAULT' => 'N'
];

$arParameters['CHAIN_MAIN_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_CHAIN_MAIN_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_SALE_PERSONAL_SECTION_CHAIN_MAIN_NAME_DEFAULT')
];

$arParameters['PRIVATE_PAGE_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_PRIVATE_PAGE_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_SALE_PERSONAL_SECTION_PRIVATE_PAGE_NAME_DEFAULT')
];

$arParameters['ORDERS_PAGE_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_ORDERS_PAGE_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_SALE_PERSONAL_SECTION_ORDERS_PAGE_NAME_DEFAULT')
];

$arParameters['PAGE_VARIABLE'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_PAGE_VARIABLE'),
    'TYPE' => 'STRING',
    'DEFAULT' => 'page'
];

$arParameters['SEF_MODE'] = [
    'private' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_SEF_PRIVATE_PAGE'),
        'DEFAULT' => 'private/',
        'VARIABLES' => []
    ],
    'order' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_SEF_ORDER_PAGE'),
        'DEFAULT' => 'orders/#ORDER_ID#/',
        'VARIABLES' => ['ORDER_ID']
    ],
    'orders' => [
        'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_SEF_ORDERS_PAGE'),
        'DEFAULT' => 'orders/',
        'VARIABLES' => []
    ]
];

if (!Loader::includeModule('intec.startshop')) {
    unset(
        $arParameters['SEF_MODE']['order'],
        $arParameters['SEF_MODE']['orders']
    );
}

$arComponentParameters = [
    'PARAMETERS' => $arParameters
];

CIBlockParameters::Add404Settings($arComponentParameters, $arCurrentValues);
