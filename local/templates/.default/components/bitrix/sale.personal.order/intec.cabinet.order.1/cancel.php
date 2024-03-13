<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.cabinet'))
    return;

IntecCabinet::Initialize();

$APPLICATION->IncludeComponent(
	'bitrix:sale.personal.order.cancel',
	'intec.cabinet.order.cancel.1', [
		'PATH_TO_LIST' => $arResult['PATH_TO_LIST'],
		'PATH_TO_DETAIL' => $arResult['PATH_TO_DETAIL'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'ID' => $arResult['VARIABLES']['ID'],
	],
	$component
);