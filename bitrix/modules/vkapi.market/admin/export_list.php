<?php

use VKapi\Market\Exception\BaseException;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
$VKAPI_MARKET_MODULE_ID = "vkapi.market";
$VKAPI_MARKET_MODULE_ID_LANG = "VKAPI.MARKET.";
global $APPLICATION;
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule($VKAPI_MARKET_MODULE_ID);
$oManager = \VKapi\Market\Manager::getInstance();
$oExport = new \VKapi\Market\Export();
$oExportTable = new \VKapi\Market\ExportTable();
$oGoodReferenceExport = new \VKapi\Market\Good\Reference\Export();
$oParam = \VKapi\Market\Param::getInstance();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$PREMISION_DEFINE = $APPLICATION->GetGroupRight($VKAPI_MARKET_MODULE_ID);
if ($PREMISION_DEFINE <= "D") {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
$bReadOnly = \true;
if ($PREMISION_DEFINE == 'W') {
    $bReadOnly = \false;
}
$sTableID = 'vkapi__market__export__list';
$sCurPage = $APPLICATION->GetCurPage();
$editPage = $VKAPI_MARKET_MODULE_ID . '_export_edit.php';
$listPage = $VKAPI_MARKET_MODULE_ID . '_export_list.php';
$exportNowPage = $VKAPI_MARKET_MODULE_ID . '_export_now.php';
$oSort = new \CAdminSorting($sTableID, "SORT", "ASC");
$sAdmin = new \CAdminList($sTableID, $oSort);

$sContent = [["TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_BTN_NEW_TITLE'), "LINK" => $editPage . "?lang=" . \LANG, "TITLE" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_BTN_NEW_TITLE'), "ICON" => "btn_new"]];
$sMenu = new \CAdminContextMenu($sContent);
if ($req->isPost() && $req->getPost('method')) {
    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
    try {
        if (!$oManager->base()->canActionRight('W')) {
            throw new \VKapi\Market\Exception\BaseException($oManager->getMessage('EXPORT_LIST.AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }
        if (\CModule::IncludeModuleEx("vkapi.marke" . "" . "t") == \constant("MODULE_DEMO_EXPIRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXMA" . "KER_" . "DEMO_EXP" . "IRE" . "" . "D");
        }
        switch ($req->getPost('method')) {
            // убираем флаг ручного жкспорта если не был убран автоматчиески
            case 'auto_export_stop':
                $oParam->set('AUTO_EXPORT_STOP', 'N');
                $oJsonResponse->setResponseField('msg', 'OK');
                break;
            default:
                throw new \VKapi\Market\Exception\BaseException($oManager->getMessage('EXPORT_LIST.AJAX_ERROR_UNKNOWN_METHOD'), 'AJAX_ERROR_UNKNOWN_METHOD');
        }
    } catch (\Throwable $ex) {
        $oJsonResponse->setException($ex);
    }
    $oJsonResponse->output();
}
// операции над строками
if (!!$req->get('action') && !!$req->get('export_id')) {
    switch ($req->get('action')) {
        case 'reset_state':
            // если изменилось название или другие параметры выгрузки подборок - надо все обновить
            $oManager->resetAutoExportState(\intval($req->get('export_id')));
            break;
        case 'auto_export_enable':
            // включенеи автоматической выгрузки
            $oExportTable->update(\intval($req->get('export_id')), ['AUTO' => \true]);
            // отмечаем потвторную выгрузку
            $oManager->resetAutoExportState(\intval($req->get('export_id')));
            break;
        case 'auto_export_disable':
            // отключение автоматической выгрузки
            $oExportTable->update(\intval($req->get('export_id')), ['AUTO' => \false]);
            break;
    }
    \LocalRedirect($APPLICATION->GetCurPageParam('', ['action', 'export_id']));
}
// активация, деактивания, удаление массове
if (!$bReadOnly && ($arID = $sAdmin->GroupAction())) {
    switch ($req->get('action')) {
        case "deactivate":
            foreach ($arID as $id) {
                $res = $oExportTable->update($id, ['ACTIVE' => \false]);
            }
            break;
        case "active":
            foreach ($arID as $id) {
                $res = $oExportTable->update($id, ['ACTIVE' => \true]);
            }
            break;
    }
    switch ($req->getPost('action_button')) {
        case "delete":
            foreach ($arID as $id) {
                $res = $oExportTable->delete($id);
            }
            break;
    }
}
// сайты
$arSite = $oManager->getSiteList();
// инфоблоки
$arIblocks = $oManager->getIblockList();
// цены
$arPrices = $oManager->getPriceList();
$by = 'ID';
if (isset($_GET['by']) && \in_array($_GET['by'], ['ID', 'SITE_ID', 'GROUP_ID', 'GROUP_NAME', 'ACTIVE', 'CATALOG_ID', 'PRICE_ID'])) {
    $by = $_GET['by'];
}
$arOrder = [$by => $_GET['order'] == 'ASC' ? 'ASC' : 'DESC'];
$navyParams = \CDBResult::GetNavParams(\CAdminResult::GetNavSize($sTableID, ['nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage()]));
$usePageNavigation = \true;
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = \false;
} else {
    $navyParams['PAGEN'] = (int) $navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int) $navyParams['SIZEN'];
}
// -----------------------------------
$arQuery = ['select' => ['*'], 'order' => $arOrder];
if ($usePageNavigation) {
    $arQuery['limit'] = $navyParams['SIZEN'];
    $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
}
if ($usePageNavigation) {
    $totalCount = 0;
    $totalPages = 0;
    $dbrCount = $oExportTable->getList(['select' => ['CNT']]);
    if ($ar = $dbrCount->fetch()) {
        $totalCount = $ar['CNT'];
    }
    if ($totalCount > 0) {
        $totalPages = \ceil($totalCount / $navyParams['SIZEN']);
        if ($navyParams['PAGEN'] > $totalPages) {
            $navyParams['PAGEN'] = $totalPages;
        }
        $arQuery['limit'] = $navyParams['SIZEN'];
        $arQuery['offset'] = $navyParams['SIZEN'] * ($navyParams['PAGEN'] - 1);
    } else {
        $navyParams['PAGEN'] = 1;
        $arQuery['limit'] = $navyParams['SIZEN'];
        $arQuery['offset'] = 0;
    }
}
$dbResultList = $oExportTable->getList($arQuery);
$dbResultList = new \CAdminResult($dbResultList, $sTableID);
if ($usePageNavigation) {
    $dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $dbResultList->NavRecordCount = $totalCount;
    $dbResultList->NavPageCount = $totalPages;
    $dbResultList->NavPageNomer = $navyParams['PAGEN'];
} else {
    $dbResultList->NavStart();
}
$sAdmin->NavText($dbResultList->GetNavPrint(\GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'PAGE_LIST_TITLE_NAV_TEXT')));
$sAdmin->AddHeaders([["id" => 'ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.ID'), "sort" => 'ID', "default" => \true], ["id" => 'NAME', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.NAME'), "sort" => 'NAME', "default" => \true], ["id" => 'ACTIVE', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.ACTIVE'), "sort" => 'ACTIVE', "default" => \true], ["id" => 'SITE_ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.SITE_ID'), "sort" => 'SITE_ID', "default" => \false], ["id" => 'GROUP_NAME', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.GROUP_NAME'), "sort" => 'GROUP_NAME', "default" => \true], ["id" => 'STATUS', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.STATUS'), "sort" => '', "default" => \true], ["id" => 'CATALOG_ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.CATALOG_ID'), "sort" => 'CATALOG_ID', "default" => \false]]);
while ($arItem = $dbResultList->NavNext(\true, 's_')) {
    $row =& $sAdmin->AddRow($arItem['ID'], $arItem);
    $row->AddField('SITE_ID', isset($arSite[$arItem['SITE_ID']]) ? $arSite[$arItem['SITE_ID']] : $arItem['SITE_ID']);
    $row->AddField('GROUP_NAME', '<a href="//vk.com/club' . $arItem['GROUP_ID'] . '" target="_blank" >[' . $arItem['GROUP_ID'] . '] ' . $arItem['GROUP_NAME'] . '</a>');
    $row->AddField('CATALOG_ID', isset($arIblocks[$arItem['CATALOG_ID']]) ? $arIblocks[$arItem['CATALOG_ID']] : $arItem['CATALOG_ID']);
    $row->AddField('PRICE_ID', isset($arPrices[$arItem['PRICE_ID']]) ? $arPrices[$arItem['PRICE_ID']] : $arItem['PRICE_ID']);
    $row->AddField('ACTIVE', \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.ACTIVE_' . $arItem['ACTIVE']) . '' . \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'HEAD.AUTO_' . $arItem['AUTO']));
    $row->AddField('ID', $arItem['ID']);
    $oState = new \VKapi\Market\State('auto_' . $arItem['ID']);
    $oStateData = $oState->get();
    $arStatus = [];
    if (!empty($oStateData)) {
        if ($oStateData['complete']) {
            $arStatus[] = \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'ITEM.STATUS.COMPLETE', ['#TIME0#' => $oStateData['dateTimeStartFormat'], '#TIME1#' => $oStateData['dateTimeStopFormat']]);
        } else {
            $arStatus[] = \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'ITEM.STATUS.RUNNING', ['#TIME0#' => $oStateData['dateTimeStartFormat']]);
            foreach ($oStateData['steps'] as $arStep) {
                if (\is_null($arStep['name'])) {
                    continue;
                }
                $arStatus[] = '<b>' . $arStep['name'] . ' - ' . $arStep['percent'] . '%</b>';
                foreach ($arStep['items'] as $arSubStep) {
                    $arStatus[] = '<span class="vkapi__market__export__list-status-item">' . ($arSubStep['percent'] >= 100 ? '+ ' : '. ') . $arSubStep['name'] . ' - ' . $arSubStep['percent'] . '%</span>';
                }
            }
        }
    } else {
        $arStatus[] = \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'ITEM.STATUS.EMPTY');
    }
    $row->AddField('STATUS', \implode('<br />', $arStatus));
    $arActions = [];
    $arActions[] = ["ICON" => "edit", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_EDIT'), "ACTION" => $sAdmin->ActionRedirect($editPage . "?ID=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
    $arActions[] = ["ICON" => "copy", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_COPY'), "ACTION" => $sAdmin->ActionRedirect($editPage . "?COPY_ID=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
    if ($arItem['AUTO']) {
        $arActions[] = ["ICON" => "update", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_RESET_STATE'), "ACTION" => $sAdmin->ActionRedirect($listPage . "?action=reset_state&export_id=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
        $arActions[] = ["ICON" => "disable", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_AUTO_EXPORT_DISABLE'), "ACTION" => $sAdmin->ActionRedirect($listPage . "?action=auto_export_disable&export_id=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
    } else {
        $arActions[] = ["ICON" => "update", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_EXPORT_NOW_START'), "ACTION" => $sAdmin->ActionRedirect($exportNowPage . "?export_id=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
        $arActions[] = ["ICON" => "disable", "TEXT" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'MENU_AUTO_EXPORT_ENABLE'), "ACTION" => $sAdmin->ActionRedirect($listPage . "?action=auto_export_enable&export_id=" . $arItem['ID'] . "&lang=" . \LANG . ""), "DEFAULT" => \true];
    }
    $row->AddActions($arActions);
}
$sAdmin->AddFooter([["title" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'LIST_SELECTED'), "value" => $dbResultList->SelectedRowsCount()], ["counter" => \true, "title" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'LIST_CHECKED'), "value" => "0"]]);
if (!$bReadOnly) {
    $sAdmin->AddGroupActionTable(["delete" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'LIST_DELETE'), "active" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'LIST_ACTIVE'), "deactivate" => \GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'LIST_DEACTIVATE')]);
}
$sAdmin->CheckListMode();
$APPLICATION->SetTitle(\GetMessage($VKAPI_MARKET_MODULE_ID_LANG . 'PAGE_LIST_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
\VKapi\Market\Manager::getInstance()->showAutoExportError();
$sMenu->Show();
$sAdmin->DisplayList();
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";