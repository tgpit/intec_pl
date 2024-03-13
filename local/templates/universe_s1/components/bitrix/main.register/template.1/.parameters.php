<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = [
    'USER_PROPERTY_NAME' => [
        'NAME' => Loc::getMessage('USER_PROPERTY_NAME'),
        'TYPE' => 'STRING',
        'DEFAULT' => ''
    ],
    'CONSENT_URL' => [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('CONSENT_URL'),
        'TYPE' => 'STRING',
        'DEFAULT' => '/company/consent/'
    ]
];