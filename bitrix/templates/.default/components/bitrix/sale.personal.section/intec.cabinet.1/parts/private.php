<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

$APPLICATION->IncludeComponent(
	'bitrix:main.profile',
	'intec.cabinet.main.profile.1',
	[
		'SET_TITLE' => 'N',
		'AJAX_MODE' => $arParams['AJAX_MODE_PRIVATE'],
		'SEND_INFO' => $arParams['SEND_INFO_PRIVATE'],
		'CHECK_RIGHTS' => $arParams['CHECK_RIGHTS_PRIVATE'],
        'READ_ONLY' => 'Y',
        'URL_CHANGE_PASSWORD' => $arParams['CHANGE_PASSWORD_LINK'],
        'URL_EDIT' => $arParams['PROFILE_LINK']
	],
	$component
);
