<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;

$arTemplateParameters = [
	'USER_PROPERTY_NAME' => [
		'NAME' => Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_PROPERTY_NAME'),
		'TYPE' => 'STRING',
		'DEFAULT' => '',
	],
    'READ_ONLY' => [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_READ_ONLY'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N',
        'REFRESH' => 'Y'
    ],
    'ALL_FIELDS_SHOW' => [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_ALL_FIELDS_SHOW'),
        'TYPE' => 'CHECKBOX',
        'DEFAULT' => 'N'
    ]
];

if ($arCurrentValues['READ_ONLY'] === 'Y') {
    $arTemplateParameters['URL_CHANGE_PASSWORD'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_URL_CHANGE_PASSWORD'),
        'TYPE' => 'STRING',
        'DEFAULT' => 'pass.php',
    ];
    $arTemplateParameters['URL_EDIT'] = [
        'PARENT' => 'ADDITIONAL_SETTINGS',
        'NAME' => Loc::getMessage('C_MAIN_PROFILE_TEMPLATE_1_URL_EDIT'),
        'TYPE' => 'STRING'
    ];
}
