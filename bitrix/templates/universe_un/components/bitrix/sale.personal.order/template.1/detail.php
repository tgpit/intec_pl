<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use intec\core\helpers\StringHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arParameters = [
	'PATH_TO_LIST' => $arResult['PATH_TO_LIST'],
	'PATH_TO_CANCEL' => $arResult['PATH_TO_CANCEL'],
	'PATH_TO_COPY' => $arResult['SEF_FOLDER'].'?COPY_ORDER=Y&ID=#ID#',
	'PATH_TO_PAYMENT' => $arParams['PATH_TO_PAYMENT'],
	'SET_TITLE' => $arParams['SET_TITLE'],
	'ID' => $arResult['VARIABLES']['ID'],
    'DISALLOW_CANCEL' => $arParams['DISALLOW_CANCEL'],
	'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
	'ALLOW_INNER' => $arParams['ALLOW_INNER'],
	'ONLY_INNER_FULL' => $arParams['ONLY_INNER_FULL'],
	'CACHE_TYPE' => $arParams['CACHE_TYPE'],
	'CACHE_TIME' => $arParams['CACHE_TIME'],
	'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
	'RESTRICT_CHANGE_PAYSYSTEM' => $arParams['RESTRICT_CHANGE_PAYSYSTEM'],
	'CUSTOM_SELECT_PROPS' => $arParams['CUSTOM_SELECT_PROPS'],
	'HIDE_USER_INFO' => $arParams['DETAIL_HIDE_USER_INFO']
];

foreach ($arParams as $sKey => $mValue)
    if (StringHelper::startsWith($sKey, 'PROP_') || StringHelper::startsWith($sKey, 'STATUS_COLOR_'))
        $arParameters[$sKey] = $mValue;

if (Loader::includeModule('support')) {
    $arParameters['PATH_TO_CLAIMS'] = $arParams['PATH_TO_CLAIMS'];
    $arParameters['PROPERTY_CLAIMS'] = $arParams['PROPERTY_CLAIMS'];
}

$APPLICATION->IncludeComponent(
	'bitrix:sale.personal.order.detail',
	'template.1',
	$arParameters,
	$component
);

unset($arParameters);