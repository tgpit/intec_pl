<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$oManager = \VKapi\Market\Manager::getInstance();
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'ADMIN.ORDER_SYNC_LIST');
$oAdmin = new \VKapi\Market\Admin($oManager->getModuleId());
$oSaleSync = new \VKapi\Market\Sale\Order\Sync();
$oAdmin->setTableId('vkapi_market_admin_order_sync_list');
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$oConnect = new \VKapi\Market\Connect();
// проверка доступа
$oManager->base()->checkLevelAccess();
$oSort = new \CAdminSorting($oAdmin->getTableId(), "SORT", "ASC");
$oAdminList = new \CAdminList($oAdmin->getTableId(), $oSort);
// передача по ссылке объекта для работы со списком
$oAdmin->setAdminList($oAdminList);
// языковые сообщения
$oAdmin->setMessage($oMessage);
// строка меню над списком --
$oMenu = new \CAdminContextMenu(array(array("TEXT" => $oMessage->get('BTN_NEW'), "LINK" => $oAdmin->getPageUrl('order_sync_edit'), "TITLE" => $oMessage->get('BTN_NEW'), "ICON" => "btn_new")));
// активация, деактивания, удаление массове
if ($oManager->base()->canActionRight('W') && ($arID = $oAdminList->GroupAction())) {
    switch ($req->getPost('action_button')) {
        case "delete":
            foreach ($arID as $id) {
                $res = $oSaleSync->table()->delete($id);
            }
            break;
    }
}
$arSite = $oAdmin->getSiteList();
// аккаунты ---------------------------
$arAccounts = $oConnect->getAccountsSelectList();
$arSiteSelect = $oManager->getSiteSelectList();
$arSiteList = $oManager->getSiteList();
// фильтрация
$oAdmin->addFilterField('ID');
$oAdmin->addFilterField('ACCOUNT_ID', ['TYPE' => 'LIST', 'VALUES' => $arAccounts]);
$oAdmin->addFilterField('SITE_ID', ['TYPE' => 'LIST', 'VALUES' => $arSiteSelect]);
$oAdmin->addFilterField('GROUP_ID');
$oAdmin->checkFilter();
// поля достпные для сортировки
$oAdmin->setSortFields(array('ID', 'ACTIVE', 'ACCOUNT_ID', 'GROUP_ID', 'SITE_ID'), 'ID');
$arQuery = $oAdmin->getListQuery();
$arQuery['select']['ACCOUNT_NAME'] = 'ACCOUNT.NAME';
$dbResultList = $oSaleSync->table()->getList($arQuery);
$dbResultList = new \CAdminResult($dbResultList, $oAdmin->getTableId());
$dbResultList->NavStart();
$oAdminList->NavText($dbResultList->GetNavPrint($oMessage->get('NAV_PAGE')));
$oAdminList->AddHeaders(array(array("id" => 'ID', "content" => $oMessage->get('HEAD.ID'), "sort" => 'ID', "default" => \true), array("id" => 'ACTIVE', "content" => $oMessage->get('HEAD.ACTIVE'), "sort" => 'ACTIVE', "default" => \true), array("id" => 'SITE_ID', "content" => $oMessage->get('HEAD.SITE_ID'), "sort" => 'SITE_ID', "default" => \true), array("id" => 'ACCOUNT', "content" => $oMessage->get('HEAD.ACCOUNT'), "sort" => 'ACCOUNT_ID', "default" => \true), array("id" => 'GROUP', "content" => $oMessage->get('HEAD.GROUP'), "sort" => 'GROUP_ID', "default" => \true)));
while ($arItem = $dbResultList->NavNext(\false)) {
    $row =& $oAdminList->AddRow($arItem['ID'], $arItem);
    $row->AddField('ACTIVE', $oMessage->get('ACTIVE.' . $arItem['ACTIVE']));
    $row->AddField('SITE_ID', $arSiteList[$arItem['SITE_ID']]);
    $row->AddField('ACCOUNT', \sprintf('<a href="/bitrix/admin/vkapi.market_list.php?lang=ru" target="_blank">[%s] %s</a>', $arItem['ACCOUNT_ID'], $arItem['ACCOUNT_NAME']));
    $row->AddField('GROUP', \sprintf('<a href="//vk.com/club%s" target="_blank">[%s] %s</a>', $arItem['GROUP_ID'], $arItem['GROUP_ID'], $arItem['GROUP_NAME']));
    $arActions = array();
    $arActions[] = array("ICON" => "edit", "TEXT" => $oMessage->get('MENU_EDIT'), "ACTION" => $oAdminList->ActionRedirect($oAdmin->getPageUrl('order_sync_edit', array('ID' => $arItem['ID']))), "DEFAULT" => \true);
    $row->AddActions($arActions);
}
$oAdminList->AddFooter(array(array("title" => $oMessage->get('LIST_SELECTED'), "value" => $dbResultList->SelectedRowsCount()), array("counter" => \true, "title" => $oMessage->get('LIST_CHECKED'), "value" => "0")));
if ($oManager->base()->canActionRight('W')) {
    $oAdminList->AddGroupActionTable(array("delete" => $oMessage->get('LIST_DELETE')));
}
$oAdminList->CheckListMode();
$APPLICATION->SetTitle($oMessage->get('TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
$oAdmin->showFilter();
$oMenu->Show();
$oAdminList->DisplayList();
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";