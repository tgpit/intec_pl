<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arCurrentValues
 */

$arTemplateParameters = [];

$arTemplateParameters = [
	'DISPLAY_AS_RATING' => [
		'NAME' => Loc::getMessage('TP_BIV_DISPLAY_AS_RATING'),
		'TYPE' => 'LIST',
		'VALUES' => [
			'rating' => Loc::getMessage('TP_BIV_RATING'),
			'vote_avg' => Loc::getMessage('TP_BIV_AVERAGE'),
		],
		'DEFAULT' => 'rating',
	],
	'SHOW_RATING' => [
		'NAME' => Loc::getMessage('TP_BIV_SHOW_RATING'),
		'TYPE' => 'CHECKBOX',
		'DEFAULT' => 'N',
	],
];