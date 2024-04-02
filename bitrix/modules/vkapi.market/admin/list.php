<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
$VKAPI_MARKET_MODULE_ID = "VKAPI.MARKET";
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule("vkapi.market");
$oConnectTable = new \VKapi\Market\ConnectTable();
$oConnect = new \VKapi\Market\Connect();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
$oManager = \VKapi\Market\Manager::getInstance();
// проверка доступа
$oManager->base()->checkAccess();
$resultAuth = $oConnect->checkAuthCodeFlow();
if ($resultAuth->isSuccess()) {
    \LocalRedirect($APPLICATION->GetCurPageParam('account=1&lang=' . \LANG, ['state', 'code']));
}
$sTableID = 'vkapi_market_list_table';
$oSort = new \CAdminSorting($sTableID, "SORT", "ASC");
$sAdmin = new \CAdminList($sTableID, $oSort);
// меню
// Массовые операции удаления ---------------------------------
if ($oManager->base()->canActionRight('W') && ($arID = $sAdmin->GroupAction())) {
    switch ($req->getPost('action_button')) {
        case "delete":
            foreach ($arID as $id) {
                $res = $oConnectTable->delete($id);
            }
            break;
    }
}
// Сортировка ------------------------------
$by = 'ID';
if (isset($_GET['by']) && \in_array($_GET['by'], ['ID', 'USER_ID', 'USER_ID_VK', 'NAME'])) {
    $by = $_GET['by'];
}
$arOrder = [$by => $_GET['order'] == 'ASC' ? 'ASC' : 'DESC'];
// Постраничная навигация ------------------
$navyParams = \CDBResult::GetNavParams(\CAdminResult::GetNavSize($sTableID, ['nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage()]));
$usePageNavigation = \true;
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = \false;
} else {
    $navyParams['PAGEN'] = (int) $navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int) $navyParams['SIZEN'];
}
// Запрос -----------------------------------
$arQuery = ['select' => ['*'], 'order' => $arOrder];
if ($usePageNavigation) {
    $totalCount = 0;
    $totalPages = 0;
    $dbrCount = $oConnectTable->getList(['select' => ['CNT']]);
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
$dbResultList = new \CAdminResult($oConnectTable->getList($arQuery), $sTableID);
if ($usePageNavigation) {
    $dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $dbResultList->NavRecordCount = $totalCount;
    $dbResultList->NavPageCount = $totalPages;
    $dbResultList->NavPageNomer = $navyParams['PAGEN'];
} else {
    $dbResultList->NavStart();
}
$sAdmin->NavText($dbResultList->GetNavPrint(\GetMessage($VKAPI_MARKET_MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));
$sAdmin->AddHeaders([["id" => 'ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.ID'), "sort" => 'ID', "default" => \true], ["id" => 'USER_ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.USER_ID'), "sort" => 'USER_ID', "default" => \true], ["id" => 'USER_ID_VK', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.USER_ID_VK'), "sort" => 'USER_ID_VK', "default" => \true], ["id" => 'NAME', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.NAME'), "sort" => 'NAME', "default" => \true], ["id" => 'EXPIRES_IN', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.EXPIRES_IN'), "sort" => 'EXPIRES_IN', "default" => \true]]);
$arItems = [];
$arUserID = [];
while ($arFields = $dbResultList->Fetch()) {
    $arItems[] = $arFields;
    $arUserID[$arFields['USER_ID']] = [];
}
$oUser = new \Bitrix\Main\UserTable();
$dbr = $oUser->getList(['filter' => ['ID' => \array_keys($arUserID)], 'select' => ['ID', 'NAME', 'LAST_NAME', 'EMAIL', 'LOGIN']]);
while ($ar = $dbr->Fetch()) {
    $name = \trim($ar['NAME'] . ' ' . $ar['LAST_NAME']);
    if (\strlen($name) <= 0) {
        $name = $ar['EMAIL'];
    }
    $arUserID[$ar['ID']] = $name;
}
foreach ($arItems as $item) {
    $row =& $sAdmin->AddRow($item['ID'], $sArActions);
    $row->AddField('ID', $item['ID']);
    $row->AddField('USER_ID', isset($arUserID[$item['USER_ID']]) ? '[' . $item['USER_ID'] . '] ' . \trim($arUserID[$item['USER_ID']]) : $item['USER_ID']);
    $row->AddField('USER_ID_VK', $item['USER_ID_VK']);
    $row->AddField('NAME', $item['NAME']);
    $row->AddField('EXPIRES_IN', $item['EXPIRES_IN']);
}
$sAdmin->AddFooter([["title" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_SELECTED'), "value" => $dbResultList->SelectedRowsCount()], ["counter" => \true, "title" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_CHECKED'), "value" => "0"]]);
if ($oManager->base()->canActionRight('W')) {
    $sAdmin->AddGroupActionTable(["delete" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_DELETE')]);
}
$sAdmin->CheckListMode();
$APPLICATION->SetTitle(\GetMessage($VKAPI_MARKET_MODULE_ID . '_PAGE_LIST_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
?>


    <div class="vkapi-market-auth__area">

        <div class="vkapi-market-auth__title"><?php 
echo \GetMessage($VKAPI_MARKET_MODULE_ID . '_FORM.LABEL');
?></div>

        <?php 
if ($resultAuth->getFirstError()->getCode()) {
    ?>
            <div class="msg_box error"><?php 
    echo $resultAuth->getFirstError()->getMessage();
    ?></div>
        <?php 
} elseif ($resultAuth->isSuccess()) {
    ?>
            <div class="msg_box success"><?php 
    echo $resultAuth->getData('MSG');
    ?></div>
        <?php 
} elseif ($req->getQuery('account') && $req->getQuery('account') == 1) {
    ?>
            <div class="msg_box success"><?php 
    echo \GetMessage($VKAPI_MARKET_MODULE_ID . '_FORM.ACCOUNT_ADDED');
    ?></div>
        <?php 
}
?>

        <div class="vkapi-market-auth__info">
            <?php 
echo \GetMessage($VKAPI_MARKET_MODULE_ID . '_FORM.INFO', ['#DOMEN#' => $oConnect->getDomain(), '#REDIRECT_URI#' => $oConnect->getAuthCodeFlowRedirectUri()]);
?>
        </div>


        <a target="_blank" class="btn_auth adm-btn"
           href="<?php 
echo $oConnect->getAuthCodeFlowUrl();
?>"><?php 
echo \GetMessage($VKAPI_MARKET_MODULE_ID . '_FORM.FIELD.BTN_AUTH');
?></a>


    </div>


<?php 
$sAdmin->DisplayList();
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";