<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
$VKAPI_MARKET_MODULE_ID = "vkapi.market";
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule($VKAPI_MARKET_MODULE_ID);
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
$oManager = \VKapi\Market\Manager::getInstance();
$oExportLog = new \VKapi\Market\Export\LogTable();
$oExport = new \VKapi\Market\ExportTable();
$dir = \str_replace($_SERVER['DOCUMENT_ROOT'], '', \_normalizePath(\dirname(__FILE__)));
$PREMISION_DEFINE = $APPLICATION->GetGroupRight($VKAPI_MARKET_MODULE_ID);
if ($PREMISION_DEFINE <= "D") {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
$bReadOnly = \true;
if ($PREMISION_DEFINE == 'W') {
    $bReadOnly = \false;
}
$sTableID = 'vkapi_market_log_table';
$oSort = new \CAdminSorting($sTableID, "SORT", "ASC");
$sAdmin = new \CAdminList($sTableID, $oSort);
// меню
// Массовые операции удаления ---------------------------------
if (!$bReadOnly && ($arID = $sAdmin->GroupAction())) {
    switch ($req->getPost('action_button')) {
        case "delete":
            if ($req->getPost('action_target') == 'selected') {
                $oExportLog->clear();
            } else {
                foreach ($arID as $id) {
                    $res = $oExportLog->delete($id);
                }
            }
            break;
    }
}
// сайты
$arSite = array();
$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
while ($ar = $dbr->Fetch()) {
    $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
}
// / Фильтр ----------------------------------------
$arTypeReference = \VKapi\Market\Export\Log::getTypeListForSelect();
$arExportReference = array('REFERENCE_ID' => array(''), 'REFERENCE' => array(\GetMessage($VKAPI_MARKET_MODULE_ID . '_FILTER_NO_SELECT')));
$dbrExportRef = $oExport->GetList(array());
while ($arExportRef = $dbrExportRef->Fetch()) {
    $arExportReference['REFERENCE_ID'][] = \intval($arExportRef['ID']);
    $arExportReference['REFERENCE'][] = '[' . \intval($arExportRef['ID']) . '] ' . $arExportRef['GROUP_ID'] . ' - ' . $arExportRef['GROUP_NAME'];
}
// проверку значений фильтра для удобства вынесем в отдельную функцию
function CheckFilter()
{
    global $FilterArr, $oAdminList;
    foreach ((array) $FilterArr as $f) {
        global ${$f};
    }
    /**
 * здесь проверяем значения переменных $find_имя и, в случае возникновения ошибки, вызываем $sAdmin->AddFilterError("текст_ошибки").
 */
    return \count((array) $oAdminList->arFilterErrors) == 0;
    // если ошибки есть, вернем false;
}
// опишем элементы фильтра
$FilterArr = array("find_export_id", "find_type");
// инициализируем фильтр
$sAdmin->InitFilter($FilterArr);
// если все значения фильтра корректны, обработаем его
if (\CheckFilter()) {
    // создадим массив фильтрации для выборки CRubric::GetList() на основе значений фильтра
    $arFilter = array();
    if (\intval($find_export_id) > 0) {
        $arFilter['EXPORT_ID'] = \intval($find_export_id);
    }
    if (\strlen(\trim($find_type)) > 0 && \in_array(\trim($find_type), $arTypeReference['REFERENCE_ID'])) {
        $arFilter['TYPE'] = \intval($find_type);
    }
}
// Сортировка ------------------------------
$by = 'ID';
if (isset($_GET['by']) && \in_array($_GET['by'], array('ID', 'TYPE', 'DATE_CREATE'))) {
    $by = $_GET['by'];
}
$arOrder = array($by => \mb_strtoupper($_GET['order']) == 'ASC' ? 'ASC' : 'DESC');
// Постраничная навигация ------------------
$navyParams = \CDBResult::GetNavParams(\CAdminResult::GetNavSize($sTableID, array('nPageSize' => 20, 'sNavID' => $APPLICATION->GetCurPage())));
$usePageNavigation = \true;
if ($navyParams['SHOW_ALL']) {
    $usePageNavigation = \false;
} else {
    $navyParams['PAGEN'] = (int) $navyParams['PAGEN'];
    $navyParams['SIZEN'] = (int) $navyParams['SIZEN'];
}
// Запрос -----------------------------------
$arQuery = array('select' => array('*', 'EX_GROUP_NAME' => 'EXPORT.GROUP_NAME'), 'order' => $arOrder, 'filter' => $arFilter);
if ($usePageNavigation) {
    $totalCount = 0;
    $totalPages = 0;
    $dbrCount = $oExportLog->getList(array('select' => array('CNT'), 'filter' => $arFilter));
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
$dbResultList = new \CAdminResult($oExportLog->getList($arQuery), $sTableID);
if ($usePageNavigation) {
    $dbResultList->NavStart($arQuery['limit'], $navyParams['SHOW_ALL'], $navyParams['PAGEN']);
    $dbResultList->NavRecordCount = $totalCount;
    $dbResultList->NavPageCount = $totalPages;
    $dbResultList->NavPageNomer = $navyParams['PAGEN'];
} else {
    $dbResultList->NavStart();
}
$sAdmin->AddAdminContextMenu();
$sAdmin->NavText($dbResultList->GetNavPrint(\GetMessage($VKAPI_MARKET_MODULE_ID . '_PAGE_LIST_TITLE_NAV_TEXT')));
$sAdmin->AddHeaders(array(array("id" => 'ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.ID'), "sort" => 'ID', "default" => \true), array("id" => 'EXPORT_ID', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.EXPORT_ID'), "sort" => 'EXPORT_ID', "default" => \true), array("id" => 'TYPE', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.TYPE'), "sort" => 'TYPE', "default" => \true), array("id" => 'CREATE_DATE', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.CREATE_DATE'), "sort" => 'CREATE_DATE', "default" => \true), array("id" => 'MSG', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.MSG'), "sort" => 'MSG', "default" => \true), array("id" => 'MORE', "content" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.MORE'), "sort" => '', "default" => \false)));
$arItems = array();
$dbr = $oExportLog->getList($arQuery);
while ($item = $dbr->fetch()) {
    $row =& $sAdmin->AddRow($item['ID'], $sArActions);
    $row->AddField('ID', $item['ID']);
    $row->AddField('EXPORT_ID', $item['EXPORT_ID'] ? '[' . $item['EXPORT_ID'] . '] ' . $item['EX_GROUP_NAME'] : '');
    $row->AddField('TYPE', \GetMessage($VKAPI_MARKET_MODULE_ID . '_HEAD.TYPE_' . $item['TYPE']));
    $row->AddField('CREATE_DATE', $item['CREATE_DATE']->format('H:i:s d.m.Y'));
    $row->AddField('MSG', $item['MSG']);
    $row->AddField('MORE', \Bitrix\Main\Web\Json::encode($item['MORE'], \JSON_HEX_TAG | \JSON_HEX_AMP | \JSON_HEX_APOS | \JSON_HEX_QUOT | \JSON_UNESCAPED_UNICODE));
}
$sAdmin->AddFooter(array(array("title" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_SELECTED'), "value" => $dbResultList->SelectedRowsCount()), array("counter" => \true, "title" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_CHECKED'), "value" => "0")));
if (!$bReadOnly) {
    $sAdmin->AddGroupActionTable(array("delete" => \GetMessage($VKAPI_MARKET_MODULE_ID . '_LIST_DELETE')));
}
$sAdmin->CheckListMode();
$APPLICATION->SetTitle(\GetMessage($VKAPI_MARKET_MODULE_ID . '_PAGE_LIST_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
// создадим объект фильтра
$oFilter = new \CAdminFilter($sTableID . "_filter", array(\GetMessage($VKAPI_MARKET_MODULE_ID . "_HEAD.FILTER_EXPORT_ID"), \GetMessage($VKAPI_MARKET_MODULE_ID . "_HEAD.FILTER_TYPE")));
?>
<form name="find_form" method="get" action="<?php 
echo $APPLICATION->GetCurPage();
?>">
    <?php 
$oFilter->Begin();
?>
    <tr>
        <td><?php 
echo \GetMessage($VKAPI_MARKET_MODULE_ID . "_HEAD.FILTER_EXPORT_ID");
?>:</td>
        <td>
            <?php 
echo \SelectBoxFromArray("find_export_id", $arExportReference, $find_export_id);
?>
        </td>
    </tr>
    <tr>
        <td><?php 
echo \GetMessage($VKAPI_MARKET_MODULE_ID . "_HEAD.FILTER_TYPE");
?>:</td>
        <td>
            <?php 
echo \SelectBoxFromArray("find_type", $arTypeReference, $find_type);
?>
        </td>
    </tr>
    <?php 
$oFilter->Buttons(array("table_id" => $sTableID, "url" => $APPLICATION->GetCurPage(), "form" => "find_form"));
$oFilter->End();
?>
</form>
<?php 
$sAdmin->DisplayList();
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";