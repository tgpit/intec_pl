<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\UI\PageNavigation;
use intec\Core;
use intec\core\net\Url;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (!Loader::includeModule('intec.core'))
    return;

$arGet = Core::$app->getRequest()->get();
$oGridOptions = new CGridOptions($arResult["GRID_ID"]);
$arFilter = $oGridOptions->GetFilter($arResult["FILTER"]);
$sSortBy = $arGet['by'] ?? '';
$sSortOrder = $arGet['order'] ?? '';
$arUserFields = $arParams['SET_SHOW_USER_FIELD'];
$iNumPage = isset($arGet['PAGEN_1']) ? Html::encode($arGet['PAGEN_1']) : 1;

if (!empty($arParams['FILTER_USER_FIELD']) && isset($arGet[$arParams['FILTER_USER_FIELD']]) && !empty($arGet[$arParams['FILTER_USER_FIELD']]))
    $arFilter[$arParams['FILTER_USER_FIELD']] = $arGet[$arParams['FILTER_USER_FIELD']];

$oRequest = Core::$app->request;
$oUrl = new Url($oRequest->getUrl());

$arSortId = [
    'by' => 's_id',
    'order' => isset($_REQUEST['order']) && $_REQUEST['order'] == 'asc' ? 'desc' : 'asc'
];
$arSortDate = [
    'by' => 's_timestamp_x',
    'order' => isset($_REQUEST['order']) && $_REQUEST['order'] == 'asc' ? 'desc' : 'asc'
];

$arResult['HEADERS'] = [];
$arResult['TICKETS_PAGENAVIGATION'] = '';

$oUrl->getQuery()->setRange($arSortId);
$arResult['HEADERS']['ID'] = $oUrl->build();
$oUrl->getQuery()->setRange($arSortDate);
$arResult['HEADERS']['DATE'] = $oUrl->build();

$rsTickets = CTicket::GetList(
    $sSortBy,
    $sSortOrder,
    $arFilter,
    $isFiltered,
    $check_rights = "Y",
    $get_user_name = "N",
    $get_dictionary_name = "Y",
    false,
    [
        'SELECT' => $arUserFields
    ]
);

$arTickets = [];

while ($arTicket = $rsTickets->GetNext())
{
    $arTickets[$arTicket['ID']] = $arTicket;
    $sEditUrl = CComponentEngine::MakePathFromTemplate($arParams['TICKET_EDIT_TEMPLATE'], ['ID' => $arTicket['ID']]);
    $arTickets[$arTicket['ID']]['TICKET_EDIT_URL'] = $sEditUrl;
}

unset($arGet, $oGridOptions, $arFilter, $sSortBy, $sSortOrder, $arUserFields, $sEditUrl);

if (!empty($arParams['TICKETS_PER_PAGE']) && $arParams['TICKETS_PER_PAGE'] < count($arTickets)) {
    $navigation = new PageNavigation('TICKETS_PAGENAVIGATION');

    $navigation->setPageSize($arParams['TICKETS_PER_PAGE'])
        ->allowAllRecords(false)
        ->initFromUri();

    $navigation->setRecordCount(count($arTickets));

    if ($navigation->getCurrentPage() > $navigation->getPageCount())
        $navigation->setCurrentPage($navigation->getPageCount());

    $arResult['TICKETS_PAGENAVIGATION'] = $navigation;

    $arTickets = ArrayHelper::slice($arTickets, ($navigation->getCurrentPage() - 1) * $arParams['TICKETS_PER_PAGE'], $arParams['TICKETS_PER_PAGE'], true);

    unset($navigation);
}

$arResult['TICKETS_ITEMS'] = $arTickets;

unset($rsTickets, $arTickets, $arTicket);