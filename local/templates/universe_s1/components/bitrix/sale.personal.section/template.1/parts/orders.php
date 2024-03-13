<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;

$arParameters = [
    'PATH_TO_DETAIL' => $arResult['PATH_TO_ORDER_DETAIL'],
    'PATH_TO_CANCEL' => $arResult['PATH_TO_ORDER_CANCEL'],
    'PATH_TO_CATALOG' => $arParams['PATH_TO_CATALOG'],
    'PATH_TO_COPY' => $arResult['PATH_TO_ORDER_COPY'],
    'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
    'PATH_TO_PAYMENT' => $arParams['PATH_TO_PAYMENT'],
    'SAVE_IN_SESSION' => $arParams['SAVE_IN_SESSION'],
    'ORDERS_PER_PAGE' => $arParams['ORDERS_PER_PAGE'],
    'SET_TITLE' => 'N',
    'ID' => $arResult['VARIABLES']['ID'],
    'NAV_TEMPLATE' => $arParams['NAV_TEMPLATE'],
    'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
    'HISTORIC_STATUSES' => $arParams['ORDER_HISTORIC_STATUSES'],
    'ALLOW_INNER' => $arParams['ALLOW_INNER'],
    'ONLY_INNER_FULL' => $arParams['ONLY_INNER_FULL'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'DEFAULT_SORT' => $arParams['ORDER_DEFAULT_SORT'],
    'RESTRICT_CHANGE_PAYSYSTEM' => $arParams['ORDER_RESTRICT_CHANGE_PAYSYSTEM'],
    'USE_SEARCH' => 'N',
    'USE_FILTER' => 'N',
    'SHOW_ONLY_CURRENT_ORDERS' => 'Y',
    'CURRENT_ORDERS_LINK' => $arParams['ORDERS_LINK'],
    'SEF_MODE' => $arParams['SEF_MODE']
];

$arSortOrder = ['ID', 'STATUS', 'DATE_INSERT', 'PRICE'];

if (!empty($_REQUEST) && isset($_REQUEST['by']))
    $arParameters['DEFAULT_SORT'] = ArrayHelper::fromRange($arSortOrder, $_REQUEST['by']);

$APPLICATION->IncludeComponent(
	'bitrix:sale.personal.order.list',
	'template.1',
    $arParameters,
	$component
);

unset($arParameters, $arSortOrder);
