<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

$APPLICATION->IncludeComponent(
	'bitrix:main.profile',
	'template.1',
	[
		'SET_TITLE' => 'N',
		'AJAX_MODE' => $arParams['AJAX_MODE_PRIVATE'],
		'SEND_INFO' => $arParams['SEND_INFO_PRIVATE'],
		'CHECK_RIGHTS' => $arParams['CHECK_RIGHTS_PRIVATE'],
        'READ_ONLY' => 'Y',
        'URL_CHANGE_PASSWORD' => $arParams['CHANGE_PASSWORD_LINK'],
        'ALL_FIELDS_SHOW' => $arParams['ALL_FIELDS_SHOW'],
        'URL_EDIT' => $arParams['PROFILE_LINK']
	],
	$component
);
