<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = [
	'USER_PROPERTY_NAME' => [
		'NAME' => Loc::getMessage('C_MAIN_REGISTER_TEMPLATE_2_USER_PROPERTY_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => ''
	],
    'CONSENT_URL' => [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_MAIN_REGISTER_TEMPLATE_2_CONSENT_URL'),
        'TYPE' => 'STRING'
    ]
];
