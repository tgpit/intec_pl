<?php

/**
 * @global CMain $APPLICATION
 */
use Bitrix\Iblock;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
\CModule::IncludeModule("iblock");
\CModule::IncludeModule("vkapi.market");
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$oManager = \VKapi\Market\Manager::getInstance();
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'IBLOCK_ELEMENT_SEARCH');
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
// Init variables
$reloadParams = array();
$get_xml_id = isset($_GET["get_xml_id"]) && $_GET["get_xml_id"] === "Y";
if ($get_xml_id) {
    $reloadParams['get_xml_id'] = 'Y';
}
$showIblockList = \true;
$iblockFix = isset($_GET['iblockfix']) && $_GET['iblockfix'] === 'y';
$IBLOCK_ID = 0;
if ($iblockFix) {
    if (isset($_GET['IBLOCK_ID'])) {
        $IBLOCK_ID = (int) $_GET['IBLOCK_ID'];
    }
    if ($IBLOCK_ID <= 0) {
        $IBLOCK_ID = 0;
        $iblockFix = \false;
    }
}
if ($iblockFix) {
    $reloadParams['iblockfix'] = 'y';
    $showIblockList = \false;
}
$reloadUrl = $APPLICATION->GetCurPage() . '?lang=' . \LANGUAGE_ID;
foreach ($reloadParams as $key => $value) {
    $reloadUrl .= '&' . $key . '=' . $value;
}
unset($key, $value);
$sTableID = 'vkapi_market_iblock_element_search';
if (!$iblockFix) {
    $lAdmin = new \CAdminList($sTableID);
    $lAdmin->InitFilter(array('filter_iblock_id'));
    /**
 * this code - for delete filter
 */
    /**
 * @var string $filter_iblock_id
 */
    $IBLOCK_ID = (int) (isset($_GET['IBLOCK_ID']) && (int) $_GET['IBLOCK_ID'] > 0 ? $_GET['IBLOCK_ID'] : $filter_iblock_id);
    unset($lAdmin);
}
$arIBTYPE = \false;
if ($IBLOCK_ID > 0) {
    $arIBlock = \CIBlock::GetArrayByID($IBLOCK_ID);
    if ($arIBlock) {
        $arIBTYPE = \CIBlockType::GetByIDLang($arIBlock["IBLOCK_TYPE_ID"], \LANGUAGE_ID);
        if (!$arIBTYPE) {
            $APPLICATION->AuthForm($oMessage->get("IBLOCK_BAD_BLOCK_TYPE_ID"));
        }
        $bBadBlock = !\CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
    } else {
        $bBadBlock = \true;
    }
    if ($bBadBlock) {
        $APPLICATION->AuthForm($oMessage->get("IBLOCK_BAD_IBLOCK"));
    }
} else {
    $arIBlock = array("ID" => 0, "ELEMENTS_NAME" => $oMessage->get("ELEMENTS"));
}
$APPLICATION->SetTitle($oMessage->get("TITLE"));
\CModule::IncludeModule('fileman');
$minImageSize = array("W" => 1, "H" => 1);
$maxImageSize = array("W" => \COption::GetOptionString("iblock", "list_image_size"), "H" => \COption::GetOptionString("iblock", "list_image_size"));
$arFilterFields = array("filter_iblock_id", "filter_section", "filter_subsections", "filter_id_start", "filter_id_end", "filter_external_id", "filter_type", "filter_timestamp_from", "filter_timestamp_to", "filter_modified_user_id", "filter_modified_by", "filter_status_id", "filter_status", "filter_active", "filter_intext", "filter_name", "filter_code");
$dbrFProps = \CIBlockProperty::GetList(array("SORT" => "ASC", "NAME" => "ASC"), array("IBLOCK_ID" => $IBLOCK_ID, "ACTIVE" => "Y"));
$arProps = array();
while ($arFProps = $dbrFProps->GetNext()) {
    if (\strlen($arFProps["USER_TYPE"]) > 0) {
        $arFProps["PROPERTY_USER_TYPE"] = \CIBlockProperty::GetUserType($arFProps["USER_TYPE"]);
    } else {
        $arFProps["PROPERTY_USER_TYPE"] = array();
    }
    $arProps[] = $arFProps;
}
foreach ($arProps as $prop) {
    if ($prop["FILTRABLE"] != "Y" || $prop["PROPERTY_TYPE"] == \Bitrix\Iblock\PropertyTable::TYPE_FILE) {
        continue;
    }
    $arFilterFields[] = "find_el_property_" . $prop["ID"];
}
$oSort = new \CAdminSorting($sTableID, "NAME", "ASC");
if (!isset($by)) {
    $by = 'NAME';
}
if (!isset($order)) {
    $order = 'ASC';
}
$arOrder = \strtoupper($by) === "ID" ? array($by => $order) : array($by => $order, "ID" => "ASC");
$lAdmin = new \CAdminList($sTableID, $oSort);
$lAdmin->InitFilter($arFilterFields);
$arFilter = array("IBLOCK_TYPE" => $filter_type, "SECTION_ID" => $filter_section, "MODIFIED_USER_ID" => $filter_modified_user_id, "MODIFIED_BY" => $filter_modified_by, "ACTIVE" => $filter_active, "EXTERNAL_ID" => $filter_external_id, "?NAME" => $filter_name, "?CODE" => $filter_code, "?SEARCHABLE_CONTENT" => $filter_intext, "SHOW_NEW" => "Y");
if ($filter_iblock_id > 0) {
    $arFilter["IBLOCK_ID"] = $filter_iblock_id;
} elseif ($IBLOCK_ID > 0) {
    $arFilter["IBLOCK_ID"] = $IBLOCK_ID;
} else {
    $arFilter["IBLOCK_ID"] = -1;
}
if (\intval($filter_section) < 0 || \strlen($filter_section) <= 0) {
    unset($arFilter["SECTION_ID"]);
} elseif ($filter_subsections == "Y") {
    if ($arFilter["SECTION_ID"] == 0) {
        unset($arFilter["SECTION_ID"]);
    } else {
        $arFilter["INCLUDE_SUBSECTIONS"] = "Y";
    }
}
if (!empty($filter_id_start)) {
    $arFilter[">=ID"] = $filter_id_start;
}
if (!empty($filter_id_end)) {
    $arFilter["<=ID"] = $filter_id_end;
}
if (!empty($filter_timestamp_from)) {
    $arFilter["DATE_MODIFY_FROM"] = $filter_timestamp_from;
}
if (!empty($filter_timestamp_to)) {
    $arFilter["DATE_MODIFY_TO"] = $filter_timestamp_to;
}
if (!empty($filter_status_id)) {
    $arFilter["WF_STATUS"] = $filter_status_id;
}
if (!empty($filter_status) && \strcasecmp($filter_status, "NOT_REF")) {
    $arFilter["WF_STATUS"] = $filter_status;
}
foreach ($arProps as $prop) {
    if ($prop["FILTRABLE"] != 'Y' || $prop["PROPERTY_TYPE"] == \Bitrix\Iblock\PropertyTable::TYPE_FILE) {
        continue;
    }
    if (!empty($prop['PROPERTY_USER_TYPE']) && isset($prop["PROPERTY_USER_TYPE"]["AddFilterFields"])) {
        \call_user_func_array($prop["PROPERTY_USER_TYPE"]["AddFilterFields"], array($prop, array("VALUE" => "find_el_property_" . $prop["ID"]), &$arFilter, &$filtered));
    } else {
        $value = ${"find_el_property_" . $prop["ID"]};
        if (\is_array($value) || \strlen($value)) {
            if ($value === "NOT_REF") {
                $value = \false;
            }
            $arFilter["?PROPERTY_" . $prop["ID"]] = $value;
        }
    }
}
$arFilter["CHECK_PERMISSIONS"] = "Y";
$arHeader = array();
$arHeader[] = array("id" => "ID", "content" => $oMessage->get("FIELD_ID"), "sort" => "id", "align" => "right", "default" => \true);
$arHeader[] = array("id" => "TIMESTAMP_X", "content" => $oMessage->get("FIELD_TIMESTAMP_X"), "sort" => "timestamp_x", "default" => \true);
$arHeader[] = array("id" => "USER_NAME", "content" => $oMessage->get("FIELD_MODIFIED_BY"), "sort" => "modified_by", "default" => \true);
$arHeader[] = array("id" => "ACTIVE", "content" => $oMessage->get("FIELD_ACTIVE"), "sort" => "active", "align" => "center", "default" => \true);
$arHeader[] = array("id" => "NAME", "content" => $oMessage->get("FIELD_NAME"), "sort" => "name", "default" => \true);
$arHeader[] = array("id" => "ACTIVE_FROM", "content" => $oMessage->get("FIELD_ACTIVE_FROM"), "sort" => "date_active_from");
$arHeader[] = array("id" => "ACTIVE_TO", "content" => $oMessage->get("FIELD_ACTIVE_TO"), "sort" => "date_active_to");
$arHeader[] = array("id" => "SORT", "content" => $oMessage->get("FIELD_SORT"), "sort" => "sort", "align" => "right");
$arHeader[] = array("id" => "DATE_CREATE", "content" => $oMessage->get("FIELD_DATE_CREATE"), "sort" => "created");
$arHeader[] = array("id" => "CREATED_USER_NAME", "content" => $oMessage->get("FIELD_CREATED_USER_NAME"), "sort" => "created_by");
$arHeader[] = array("id" => "CODE", "content" => $oMessage->get("FIELD_CODE"), "sort" => "code");
$arHeader[] = array("id" => "EXTERNAL_ID", "content" => $oMessage->get("FIELD_XML_ID"), "sort" => "external_id");
if (\CModule::IncludeModule("workflow")) {
    $arHeader[] = array("id" => "WF_STATUS_ID", "content" => $oMessage->get("FIELD_STATUS"), "sort" => "status", "default" => \true);
    $arHeader[] = array("id" => "LOCKED_USER_NAME", "content" => $oMessage->get("LOCK_BY"));
}
$arHeader[] = array("id" => "SHOW_COUNTER", "content" => $oMessage->get("FIELD_SHOW_COUNTER"), "sort" => "show_counter", "align" => "right");
$arHeader[] = array("id" => "SHOW_COUNTER_START", "content" => $oMessage->get("FIELD_SHOW_COUNTER_START"), "sort" => "show_counter_start", "align" => "right");
$arHeader[] = array("id" => "PREVIEW_PICTURE", "content" => $oMessage->get("FIELD_PREVIEW_PICTURE"), "align" => "right");
$arHeader[] = array("id" => "PREVIEW_TEXT", "content" => $oMessage->get("FIELD_PREVIEW_TEXT"));
$arHeader[] = array("id" => "DETAIL_PICTURE", "content" => $oMessage->get("FIELD_DETAIL_PICTURE"), "align" => "center");
$arHeader[] = array("id" => "DETAIL_TEXT", "content" => $oMessage->get("FIELD_DETAIL_TEXT"));
foreach ($arProps as $prop) {
    $arHeader[] = array("id" => "PROPERTY_" . $prop['ID'], "content" => $prop['NAME'], "align" => $prop["PROPERTY_TYPE"] == 'N' ? "right" : "left", "sort" => $prop["MULTIPLE"] != 'Y' ? "PROPERTY_" . $prop['ID'] : "");
}
$lAdmin->AddHeaders($arHeader);
$arSelectedFields = $lAdmin->GetVisibleHeaderColumns();
$arSelectedProps = array();
foreach ($arProps as $prop) {
    if ($key = \array_search("PROPERTY_" . $prop['ID'], $arSelectedFields)) {
        $arSelectedProps[] = $prop;
        $arSelect[$prop['ID']] = array();
        $props = \CIBlockProperty::GetPropertyEnum($prop['ID']);
        while ($res = $props->Fetch()) {
            $arSelect[$prop['ID']][$res["ID"]] = $res["VALUE"];
        }
        unset($arSelectedFields[$key]);
    }
}
$arSelectedFields[] = "LANG_DIR";
$arSelectedFields[] = "LID";
$arSelectedFields[] = "WF_PARENT_ELEMENT_ID";
if (\in_array("LOCKED_USER_NAME", $arSelectedFields)) {
    $arSelectedFields[] = "WF_LOCKED_BY";
}
if (\in_array("USER_NAME", $arSelectedFields)) {
    $arSelectedFields[] = "MODIFIED_BY";
}
if (\in_array("CREATED_USER_NAME", $arSelectedFields)) {
    $arSelectedFields[] = "CREATED_BY";
}
if (\in_array("PREVIEW_TEXT", $arSelectedFields)) {
    $arSelectedFields[] = "PREVIEW_TEXT_TYPE";
}
if (\in_array("DETAIL_TEXT", $arSelectedFields)) {
    $arSelectedFields[] = "DETAIL_TEXT_TYPE";
}
$arSelectedFields[] = "LOCK_STATUS";
$arSelectedFields[] = "WF_NEW";
$arSelectedFields[] = "WF_STATUS_ID";
$arSelectedFields[] = "DETAIL_PAGE_URL";
$arSelectedFields[] = "SITE_ID";
$arSelectedFields[] = "CODE";
$arSelectedFields[] = "EXTERNAL_ID";
$arSelectedFields[] = "NAME";
$arSelectedFields[] = "XML_ID";
$arSelectedFields[] = "ID";
$rsData = \CIBlockElement::GetList($arOrder, $arFilter, \false, array("nPageSize" => \CAdminResult::GetNavSize($sTableID)), $arSelectedFields);
$rsData = new \CAdminResult($rsData, $sTableID);
$rsData->NavStart();
$lAdmin->NavText($rsData->GetNavPrint($arIBlock["ELEMENTS_NAME"]));
function GetElementName($ID)
{
    $ID = \IntVal($ID);
    static $cache = array();
    if (!\array_key_exists($ID, $cache) && $ID > 0) {
        $rsElement = \CIBlockElement::GetList(array(), array("ID" => $ID, "SHOW_HISTORY" => "Y"), \false, \false, array("ID", "IBLOCK_ID", "NAME"));
        $cache[$ID] = $rsElement->GetNext();
    }
    return $cache[$ID];
}
function GetSectionName($ID)
{
    $ID = \IntVal($ID);
    static $cache = array();
    if (!\array_key_exists($ID, $cache) && $ID > 0) {
        $rsSection = \CIBlockSection::GetList(array(), array("ID" => $ID), \false, array("ID", "IBLOCK_ID", "NAME"));
        $cache[$ID] = $rsSection->GetNext();
    }
    return $cache[$ID];
}
function GetIBlockTypeID($IBLOCK_ID)
{
    $IBLOCK_ID = \IntVal($IBLOCK_ID);
    static $cache = array();
    if (!\array_key_exists($IBLOCK_ID, $cache)) {
        $rsIBlock = \CIBlock::GetByID($IBLOCK_ID);
        if (!($cache[$IBLOCK_ID] = $rsIBlock->GetNext())) {
            $cache[$IBLOCK_ID] = array("IBLOCK_TYPE_ID" => "");
        }
    }
    return $cache[$IBLOCK_ID]["IBLOCK_TYPE_ID"];
}
if ($IBLOCK_ID <= 0) {
    $lAdmin->BeginPrologContent();
    $message = new \CAdminMessage(array("MESSAGE" => $oMessage->get("CHOOSE_IBLOCK"), "TYPE" => "OK"));
    echo $message->Show();
    $lAdmin->EndPrologContent();
}
$arItemsJorJs = array();
while ($arItem = $rsData->GetNext()) {
    $arItem["MODIFIED_BY"] = (int) $arItem["MODIFIED_BY"];
    $arItem["CREATED_BY"] = (int) $arItem["CREATED_BY"];
    $arItem["WF_LOCKED_BY"] = (int) $arItem["WF_LOCKED_BY"];
    foreach ($arSelectedProps as $aProp) {
        if ($arItem["PROPERTY_" . $aProp['ID'] . '_ENUM_ID'] > 0) {
            $arItem["PROPERTY_" . $aProp['ID']] = $arItem["PROPERTY_" . $aProp['ID'] . '_ENUM_ID'];
        } else {
            $arItem["PROPERTY_" . $aProp['ID']] = $arItem["PROPERTY_" . $aProp['ID'] . '_VALUE'];
        }
    }
    $row =& $lAdmin->AddRow($arItem["ID"], $arItem);
    $row->AddViewField("NAME", $arItem["NAME"] . '<input type="hidden" name="n' . $arItem["ID"] . '" id="index_' . $arItem["ID"] . '" value="' . $arItem["ID"] . '"><div style="display:none" id="name_' . $arItem["ID"] . '">' . $arItem["NAME"] . '</div>');
    if ($arItem["MODIFIED_BY"] > 0) {
        $row->AddViewField("MODIFIED_BY", '[<a target="_blank" href="user_edit.php?lang=' . \LANGUAGE_ID . '&ID=' . $arItem["MODIFIED_BY"] . '">' . $arItem["MODIFIED_BY"] . '</a>]&nbsp;' . $arItem["USER_NAME"]);
    } else {
        $row->AddViewField("MODIFIED_BY", '');
    }
    $row->AddCheckField("ACTIVE", \false);
    if ($arItem["CREATED_BY"] > 0) {
        $row->AddViewField("CREATED_USER_NAME", '[<a target="_blank" href="user_edit.php?lang=' . \LANGUAGE_ID . '&ID=' . $arItem["CREATED_BY"] . '">' . $arItem["CREATED_BY"] . '</a>]&nbsp;' . $arItem["CREATED_USER_NAME"]);
    } else {
        $row->AddViewField("CREATED_USER_NAME", '');
    }
    $row->AddViewFileField("PREVIEW_PICTURE", array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => $maxImageSize, "MIN_SIZE" => $minImageSize));
    $row->AddViewFileField("DETAIL_PICTURE", array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => $maxImageSize, "MIN_SIZE" => $minImageSize));
    $row->AddViewField("WF_STATUS_ID", \htmlspecialcharsbx(\CIBlockElement::WF_GetStatusTitle($arItem["WF_STATUS_ID"])) . '<input type="hidden" name="n' . $arItem["ID"] . '" value="' . $arItem["NAME"] . '">');
    if ($arItem["WF_LOCKED_BY"] > 0) {
        $row->AddViewField("LOCKED_USER_NAME", '&nbsp;<a href="user_edit.php?lang=' . \LANGUAGE_ID . '&ID=' . $arItem["WF_LOCKED_BY"] . '" title="' . $oMessage->get("USERINFO") . '">' . $arItem["LOCKED_USER_NAME"] . '</a>');
    } else {
        $row->AddViewField("LOCKED_USER_NAME", '');
    }
    $arProperties = array();
    if (\count($arSelectedProps) > 0) {
        $rsProperties = \CIBlockElement::GetProperty($IBLOCK_ID, $arItem["ID"]);
        while ($ar = $rsProperties->GetNext()) {
            if (!\array_key_exists($ar["ID"], $arProperties)) {
                $arProperties[$ar["ID"]] = array();
            }
            $arProperties[$ar["ID"]][$ar["PROPERTY_VALUE_ID"]] = $ar;
        }
    }
    foreach ($arSelectedProps as $aProp) {
        if (\strlen($aProp["USER_TYPE"]) > 0) {
            $arUserType = \CIBlockProperty::GetUserType($aProp["USER_TYPE"]);
        } else {
            $arUserType = array();
        }
        $v = '';
        foreach ($arProperties[$aProp['ID']] as $property_value_id => $property_value) {
            $property_value['PROPERTY_VALUE_ID'] = \intval($property_value['PROPERTY_VALUE_ID']);
            $VALUE_NAME = 'FIELDS[' . $arItem["ID"] . '][PROPERTY_' . $property_value['ID'] . '][' . $property_value['PROPERTY_VALUE_ID'] . '][VALUE]';
            $DESCR_NAME = 'FIELDS[' . $arItem["ID"] . '][PROPERTY_' . $property_value['ID'] . '][' . $property_value['PROPERTY_VALUE_ID'] . '][DESCRIPTION]';
            $res = '';
            if (\array_key_exists("GetAdminListViewHTML", $arUserType)) {
                $res = \call_user_func_array($arUserType["GetAdminListViewHTML"], array($property_value, array("VALUE" => $property_value["~VALUE"], "DESCRIPTION" => $property_value["~DESCRIPTION"]), array("VALUE" => $VALUE_NAME, "DESCRIPTION" => $DESCR_NAME, "MODE" => "iblock_element_admin", "FORM_NAME" => "form_" . $sTableID)));
            } elseif ($aProp['PROPERTY_TYPE'] == 'F') {
                if (\Bitrix\Main\Loader::includeModule('fileman')) {
                    $res = \CFileInput::Show('NO_FIELDS[' . $property_value_id . ']', $property_value["VALUE"], ["IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "Y", "MAX_SIZE" => $maxImageSize, "MIN_SIZE" => $minImageSize], ['upload' => \false, 'medialib' => \false, 'file_dialog' => \false, 'cloud' => \false, 'del' => \false, 'description' => \false]);
                } else {
                    echo $oMessage->get('MODULE_FILEMAN_NOT_INSTALLED');
                }
            } elseif ($aProp['PROPERTY_TYPE'] == 'G') {
                $t = \GetSectionName($property_value["VALUE"]);
                if ($t) {
                    $res = $t['NAME'] . ' [<a href="' . \htmlspecialcharsbx(\CIBlock::GetAdminSectionEditLink($t['IBLOCK_ID'], $t['ID'])) . '" title="' . $oMessage->get("SECTION_EDIT") . '">' . $t['ID'] . '</a>]';
                }
            } elseif ($aProp['PROPERTY_TYPE'] == 'E') {
                $t = \GetElementName($property_value["VALUE"]);
                if ($t) {
                    $res = $t['NAME'] . ' [<a href="' . \htmlspecialcharsbx(\CIBlock::GetAdminElementEditLink($t['IBLOCK_ID'], $t['ID'])) . '" title="' . $oMessage->get("ELEMENT_EDIT") . '">' . $t['ID'] . '</a>]';
                }
            } elseif ($property_value['PROPERTY_TYPE'] == 'L') {
                $res = $property_value["VALUE_ENUM"];
            } else {
                $res = $property_value["VALUE"];
            }
            if ($res != "") {
                $v .= ($v != '' ? ' / ' : '') . $res;
            }
        }
        if ($v != "") {
            $row->AddViewField("PROPERTY_" . $aProp['ID'], $v);
        }
        unset($arSelectedProps[$aProp['ID']]["CACHE"]);
    }
    $arItemsJorJs[$arItem['ID']] = array('id' => $arItem['ID'], 'xmlId' => $arItem['XML_ID'], 'title' => $arItem["NAME"] . ' [' . $arItem['ID'] . ']', 'xmlTitle' => $arItem["NAME"] . ' [' . $arItem['XML_ID'] . ']', 'name' => $arItem["NAME"]);
    $row->AddActions(array(array("DEFAULT" => "Y", "TEXT" => $oMessage->get("SELECT"), "ACTION" => "javascript:VKapiMarketIblockElementSearchJs.selectedValue(" . $arItem['ID'] . ")")));
}
$lAdmin->AddFooter(array(array("title" => $oMessage->get("MAIN_ADMIN_LIST_SELECTED"), "value" => $rsData->SelectedRowsCount()), array("counter" => \true, "title" => $oMessage->get("MAIN_ADMIN_LIST_CHECKED"), "value" => "0")));
$lAdmin->AddAdminContextMenu(array(), \false);
// данные ---
$lAdmin->BeginPrologContent();
?>
    <script type="text/javascript">
        var VKapiMarketIblockElementSearchJs = new VKapiMarketIblockElementSearch(<?php 
echo \Bitrix\Main\Web\Json::encode(array('tableId' => $sTableID, 'reloadUrl' => $reloadUrl, 'items' => $arItemsJorJs));
?>);
    </script>
<?php 
$lAdmin->EndPrologContent();
$lAdmin->CheckListMode();
/**
 * 
 * HTML form
 * /
 */
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_popup_admin.php";
// подключаем стили и скрипты ----
$oManager->showAdminPageCssJs();
?>
    <form name="form1" method="GET" action="<?php 
echo $APPLICATION->GetCurPage();
?>">
        <?php 
function _ShowGroupPropertyField($name, $property_fields, $values, $oMessage)
{
    if (!\is_array($values)) {
        $values = array();
    }
    $res = "";
    $bWas = \false;
    $sections = \CIBlockSection::GetTreeList(array("IBLOCK_ID" => $property_fields["LINK_IBLOCK_ID"]), array("ID", "NAME", "DEPTH_LEVEL"));
    while ($ar = $sections->GetNext()) {
        $res .= '<option value="' . $ar["ID"] . '"';
        if (\in_array($ar["ID"], $values)) {
            $bWas = \true;
            $res .= ' selected';
        }
        $res .= '>' . \str_repeat(" . ", $ar["DEPTH_LEVEL"]) . $ar["NAME"] . '</option>';
    }
    echo '<select name="' . $name . '[]">';
    echo '<option value=""' . (!$bWas ? ' selected' : '') . '>' . $oMessage->get("NOT_SET") . '</option>';
    echo $res;
    echo '</select>';
}
$arFindFields = array();
if (!$iblockFix) {
    $arFindFields['IBLOCK_ID'] = $oMessage->get('IBLOCK');
}
$arFindFields["id"] = "ID";
$arFindFields["date"] = $oMessage->get("F_DATE");
$arFindFields["chn"] = $oMessage->get("F_CHANGED");
if (\CModule::IncludeModule("workflow")) {
    $arFindFields["stat"] = $oMessage->get("F_STATUS");
}
if (\is_array($arIBTYPE) && $arIBTYPE["SECTIONS"] == "Y") {
    $arFindFields["sec"] = $oMessage->get("F_SECTION");
}
$arFindFields["act"] = $oMessage->get("F_ACTIVE");
$arFindFields["ext_id"] = $oMessage->get("FIELD_EXTERNAL_ID");
$arFindFields["tit"] = $oMessage->get("F_TITLE");
$arFindFields["code"] = $oMessage->get("FIELD_CODE");
$arFindFields["dsc"] = $oMessage->get("F_DSC");
foreach ($arProps as $prop) {
    if ($prop["FILTRABLE"] == "Y" && $prop["PROPERTY_TYPE"] != "F") {
        $arFindFields["p" . $prop["ID"]] = $prop["NAME"];
    }
}
$oFilter = new \CAdminFilter($sTableID . "_filter", $arFindFields);
?>

        <?php 
if ($iblockFix) {
    ?><input type="hidden" name="IBLOCK_ID" value="<?php 
    echo $IBLOCK_ID;
    ?>">
                <input type="hidden" name="filter_iblock_id" value="<?php 
    echo $IBLOCK_ID;
    ?>"><?php 
}
$oFilter->Begin();
if (!$iblockFix) {
    ?>
                <tr>
                    <td><b><?php 
    echo $oMessage->get("IBLOCK");
    ?></b></td>
                    <td><?php 
    echo \GetIBlockDropDownListEx($IBLOCK_ID, "filter_type", "filter_iblock_id", array('MIN_PERMISSION' => 'S'), '', 'VKapiMarketIblockElementSearchJs.changeIblock(this)');
    ?></td>
                </tr>
                <?php 
}
?>
        <tr>
            <td><?php 
echo $oMessage->get("FROMTO_ID");
?></td>
            <td>
                <input type="text" name="filter_id_start" size="10"
                       value="<?php 
echo \htmlspecialcharsbx($filter_id_start);
?>">
                ...
                <input type="text" name="filter_id_end" size="10" value="<?php 
echo \htmlspecialcharsbx($filter_id_end);
?>">
            </td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get("FIELD_TIMESTAMP_X") . ":";
?></td>
            <td><?php 
echo \CalendarPeriod("filter_timestamp_from", \htmlspecialcharsbx($filter_timestamp_from), "filter_timestamp_to", \htmlspecialcharsbx($filter_timestamp_to), "form1");
?></td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get("FIELD_MODIFIED_BY");
?>:</td>
            <td>
                <?php 
echo \FindUserID("filter_modified_user_id", $filter_modified_user_id, "", "form1", "5", "", " ... ", "", "");
?>
            </td>
        </tr>
        <?php 
if (\CModule::IncludeModule("workflow")) {
    ?>
            <tr>
                <td><?php 
    echo $oMessage->get("FIELD_STATUS");
    ?>:</td>
                <td><input type="text" name="filter_status_id" value="<?php 
    echo \htmlspecialcharsbx($filter_status_id);
    ?>"
                           size="3">
                    <select name="filter_status">
                        <option value=""><?php 
    echo $oMessage->get("IBLOCK_VALUE_ANY");
    ?></option>
                        <?php 
    $rs = \CWorkflowStatus::GetDropDownList("Y");
    while ($arRs = $rs->GetNext()) {
        ?>
                                <option value="<?php 
        echo $arRs["REFERENCE_ID"];
        ?>"<?php 
        if ($filter_status == $arRs["~REFERENCE_ID"]) {
            echo " selected";
        }
        ?>><?php 
        echo $arRs["REFERENCE"];
        ?></option><?php 
    }
    ?>
                    </select></td>
            </tr>
        <?php 
}
?>
        
        <?php 
if (\is_array($arIBTYPE) && $arIBTYPE["SECTIONS"] == "Y") {
    ?>
            <tr>
                <td><?php 
    echo $oMessage->get("FIELD_SECTION_ID");
    ?>:</td>
                <td>
                    <select name="filter_section">
                        <option value=""><?php 
    echo $oMessage->get("IBLOCK_VALUE_ANY");
    ?></option>
                        <option value="0"<?php 
    if ($filter_section == "0") {
        echo " selected";
    }
    ?>><?php 
    echo $oMessage->get("IBLOCK_UPPER_LEVEL");
    ?></option>
                        <?php 
    $bsections = \CIBlockSection::GetTreeList(array("IBLOCK_ID" => $IBLOCK_ID), array("ID", "NAME", "DEPTH_LEVEL"));
    while ($arSection = $bsections->GetNext()) {
        ?>
                                <option value="<?php 
        echo $arSection["ID"];
        ?>"<?php 
        if ($arSection["ID"] == $filter_section) {
            echo " selected";
        }
        ?>><?php 
        echo \str_repeat("&nbsp;.&nbsp;", $arSection["DEPTH_LEVEL"]);
        echo $arSection["NAME"];
        ?></option><?php 
    }
    ?>
                    </select><br>

                    <input type="checkbox" name="filter_subsections"
                           value="Y"<?php 
    if ($filter_subsections == "Y") {
        echo " checked";
    }
    ?>> <?php 
    echo $oMessage->get("INCLUDING_SUBSECTIONS");
    ?>

                </td>
            </tr>
        <?php 
}
?>

        <tr>
            <td><?php 
echo $oMessage->get("FIELD_ACTIVE");
?>:</td>
            <td>
                <select name="filter_active">
                    <option value=""><?php 
echo \htmlspecialcharsbx($oMessage->get('IBLOCK_VALUE_ANY'));
?></option>
                    <option value="Y"<?php 
if ($filter_active == "Y") {
    echo " selected";
}
?>><?php 
echo \htmlspecialcharsbx($oMessage->get("IBLOCK_YES"));
?></option>
                    <option value="N"<?php 
if ($filter_active == "N") {
    echo " selected";
}
?>><?php 
echo \htmlspecialcharsbx($oMessage->get("IBLOCK_NO"));
?></option>
                </select>
            </td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get("FIELD_EXTERNAL_ID");
?>:</td>
            <td><input type="text" name="filter_external_id" value="<?php 
echo \htmlspecialcharsbx($filter_external_id);
?>"
                       size="30"></td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get("FIELD_NAME");
?>:</td>
            <td>
                <input type="text" name="filter_name" value="<?php 
echo \htmlspecialcharsbx($filter_name);
?>" size="30">
            </td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get("FIELD_CODE");
?>:</td>
            <td>
                <input type="text" name="filter_code" value="<?php 
echo \htmlspecialcharsbx($filter_code);
?>" size="30">
            </td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get("DESC");
?></td>
            <td>
                <input type="text" name="filter_intext" value="<?php 
echo \htmlspecialcharsbx($filter_intext);
?>" size="30">&nbsp;<?php 
echo \ShowFilterLogicHelp();
?>
            </td>
        </tr>
        <?php 
foreach ($arProps as $prop) {
    if ($prop["FILTRABLE"] != "Y" || $prop["PROPERTY_TYPE"] == "F") {
        continue;
    }
    ?>
                <tr>
                    <td><?php 
    echo $prop["NAME"];
    ?>:</td>
                    <td>
                        <?php 
    if (\array_key_exists("GetAdminFilterHTML", $prop["PROPERTY_USER_TYPE"])) {
        echo \call_user_func_array($prop["PROPERTY_USER_TYPE"]["GetAdminFilterHTML"], array($prop, array("VALUE" => "find_el_property_" . $prop["ID"], "TABLE_ID" => $sTableID)));
    } elseif ($prop["PROPERTY_TYPE"] == 'L') {
        ?>
                            <select name="find_el_property_<?php 
        echo $prop["ID"];
        ?>">
                                <option value=""><?php 
        echo $oMessage->get("IBLOCK_VALUE_ANY");
        ?></option><?php 
        $dbrPEnum = \CIBlockPropertyEnum::GetList(array("SORT" => "ASC", "VALUE" => "ASC"), array("PROPERTY_ID" => $prop["ID"]));
        while ($arPEnum = $dbrPEnum->GetNext()) {
            ?>
                                        <option value="<?php 
            echo $arPEnum["ID"];
            ?>"<?php 
            if (${"find_el_property_" . $prop["ID"]} == $arPEnum["ID"]) {
                echo " selected";
            }
            ?>><?php 
            echo $arPEnum["VALUE"];
            ?></option>
                                    <?php 
        }
        ?></select>
                        <?php 
    } elseif ($prop["PROPERTY_TYPE"] == 'G') {
        \_ShowGroupPropertyField('find_el_property_' . $prop["ID"], $prop, ${'find_el_property_' . $prop["ID"]}, $oMessage);
    } else {
        ?>
                            <input type="text" name="find_el_property_<?php 
        echo $prop["ID"];
        ?>"
                                   value="<?php 
        echo \htmlspecialcharsbx(${"find_el_property_" . $prop["ID"]});
        ?>"
                                   size="30">&nbsp;<?php 
        echo \ShowFilterLogicHelp();
        ?>
                        <?php 
    }
    ?>
                    </td>
                </tr>
            <?php 
}
$oFilter->Buttons();
?>
        <span class="adm-btn-wrap"><input type="submit" class="adm-btn" name="set_filter"
                                          value="<?php 
echo $oMessage->get("SET_BUTTON");
?>"
                                          title="<?php 
echo $oMessage->get("SET_BUTTON");
?>"
                                          onclick="VKapiMarketIblockElementSearchJs.applyFilter(this);"></span>
        <span class="adm-btn-wrap"><input type="submit" class="adm-btn" name="del_filter"
                                          value="<?php 
echo $oMessage->get("CLEAR_BUTTON");
?>"
                                          title="<?php 
echo $oMessage->get("CLEAR_BUTTON");
?>"
                                          onclick="VKapiMarketIblockElementSearchJs.deleteFilter(this);"></span>
        <?php 
$oFilter->End();
?>
    </form>
<?php 
$lAdmin->DisplayList();
require $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_popup_admin.php";