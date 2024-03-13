<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = [];
$arTemplateParameters['BASKET_PAGE_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_BASKET_PAGE_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_SALE_PERSONAL_SECTION_BASKET_PAGE_NAME_DEFAULT')
];

$arTemplateParameters['BASKET_URL'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_BASKET_URL'),
    'TYPE' => 'STRING'
];

$arTemplateParameters['CONTACTS_PAGE_NAME'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_CONTACTS_PAGE_NAME'),
    'TYPE' => 'STRING',
    'DEFAULT' => Loc::getMessage('C_SALE_PERSONAL_SECTION_CONTACTS_PAGE_NAME_DEFAULT')
];

$arTemplateParameters['CONTACTS_URL'] = [
    'PARENT' => 'BASE',
    'NAME' => Loc::getMessage('C_SALE_PERSONAL_SECTION_CONTACTS_URL'),
    'TYPE' => 'STRING'
];
