<?
    /** @global CMain $APPLICATION */
    /** @global CUser $USER */
    /** @global CDatabase $DB */
    /** @global CUserTypeManager $USER_FIELD_MANAGER */
    
    require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
    
    CModule::IncludeModule("iblock");
    CModule::IncludeModule("vkapi.market");
    
    \Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
    
    $oManager = \VKapi\Market\Manager::getInstance();
    $oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'IBLOCK_SECTION_SEARCH');
    
    $app = \Bitrix\Main\Application::getInstance();
    $req = $app->getContext()->getRequest();
    
    
    //Init variables
    $reloadParams = array();
    
    // возможен выбор нескольких вараинтов
    
    $showIblockList = true;
    $iblockFix = isset($_GET['iblockfix']) && $_GET['iblockfix'] === 'y';
    $IBLOCK_ID = 0;
    if ($iblockFix) {
        if (isset($_GET['IBLOCK_ID'])) {
            $IBLOCK_ID = (int)$_GET['IBLOCK_ID'];
        }
        if ($IBLOCK_ID <= 0) {
            $IBLOCK_ID = 0;
            $iblockFix = false;
        }
    }
    if ($iblockFix) {
        $reloadParams['iblockfix'] = 'y';
        $showIblockList = false;
        $hideIblockId = 0;
    }
    if ($hideIblockId > 0) {
        $reloadParams['hideiblock'] = $hideIblockId;
    }
    
    $simpleName = (isset($_REQUEST['simplename']) && $_REQUEST['simplename'] === 'Y');
    if ($simpleName) {
        $reloadParams['simplename'] = 'Y';
    }
    
    $reloadUrl = $APPLICATION->GetCurPage() . '?lang=' . LANGUAGE_ID;
    foreach ($reloadParams as $key => $value) {
        $reloadUrl .= '&' . $key . '=' . $value;
    }
    unset($key, $value);
    
    $extReloadUrl = $reloadUrl;
    if ($iblockFix) {
        $extReloadUrl .= '&IBLOCK_ID=' . $IBLOCK_ID;
    }
    
    $sTableID = 'vkapi_market_condition_iblock_section_search';
    
    if (!$iblockFix) {
        $lAdmin = new CAdminList($sTableID);
        $lAdmin->InitFilter(array('find_iblock_id'));
        /* this code - for delete filter */
        /** @var string $find_iblock_id */
        $IBLOCK_ID = (int)(isset($_GET['IBLOCK_ID']) && (int)$_GET['IBLOCK_ID'] > 0 ? $_GET['IBLOCK_ID'] : $find_iblock_id);
        unset($lAdmin);
    }
    
    $arIBTYPE = false;
    if ($IBLOCK_ID > 0) {
        $arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
        if ($arIBlock) {
            $arIBTYPE = CIBlockType::GetByIDLang($arIBlock["IBLOCK_TYPE_ID"], LANGUAGE_ID);
            if (!$arIBTYPE) {
                $APPLICATION->AuthForm($oMessage->get("IBLOCK_BAD_BLOCK_TYPE_ID"));
            }
            
            $bBadBlock = !CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, "iblock_admin_display");
        } else {
            $bBadBlock = true;
        }
        if ($bBadBlock) {
            $APPLICATION->AuthForm($oMessage->get("IBLOCK_BAD_IBLOCK"));
        }
    } else {
        $arIBlock = array(
            "ID" => 0,
            "NAME" => "",
            "SECTIONS_NAME" => $oMessage->get("SECTIONS"),
        );
    }
    
    $useParentFilter = $iblockFix;
    
    // заголовок страницы -------
    $APPLICATION->SetTitle($oMessage->get("TITLE"));
    
    $entity_id = ($IBLOCK_ID > 0 ? "IBLOCK_" . $IBLOCK_ID . "_SECTION" : false);
    
    //сортировка списка -------------
    $oSort = new \CAdminSorting($sTableID, "NAME", "ASC");
    if (!isset($by)) {
        $by = 'NAME';
    }
    if (!isset($order)) {
        $order = 'ASC';
    }
    $arOrder = (strtoupper($by) === "ID" ? array($by => $order) : array($by => $order, "ID" => "ASC"));
    
    $lAdmin = new CAdminList($sTableID, $oSort);
    
    $arFilterFields = array(
        "find_iblock_id",
        "find_section_id",
        "find_section_timestamp_1",
        "find_section_timestamp_2",
        "find_section_modified_by",
        "find_section_date_create_1",
        "find_section_date_create_2",
        "find_section_created_by",
        "find_section_name",
        "find_section_active",
        "find_section_section",
        "find_section_code",
        "find_section_external_id"
    );
    
    if ($entity_id) {
        $USER_FIELD_MANAGER->AdminListAddFilterFields($entity_id, $arFilterFields);
    }
    
    $find_section_section = strlen($find_section_section) > 0 ? intval($find_section_section) : "";
    $lAdmin->InitFilter($arFilterFields);
    
    
    ############################################
    
    $arFilter = array(
        "?NAME" => $find_section_name,
        "SECTION_ID" => $find_section_section,
        "ID" => $find_section_id,
        ">=TIMESTAMP_X" => $find_section_timestamp_1,
        "<=TIMESTAMP_X" => $find_section_timestamp_2,
        "MODIFIED_BY" => $find_section_modified_user_id ? $find_section_modified_user_id : $find_section_modified_by,
        ">=DATE_CREATE" => $find_section_date_create_1,
        "<=DATE_CREATE" => $find_section_date_create_2,
        "CREATED_BY" => $find_section_created_user_id ? $find_section_created_user_id : $find_section_created_by,
        "ACTIVE" => $find_section_active,
        "CODE" => $find_section_code,
        "EXTERNAL_ID" => $find_section_external_id,
    );
    if ($entity_id) {
        $USER_FIELD_MANAGER->AdminListAddFilter($entity_id, $arFilter);
    }
    
    if ($find_section_section === "" || !$useParentFilter) {
        unset($arFilter["SECTION_ID"]);
    }
    
    if ($IBLOCK_ID > 0) {
        $arFilter["IBLOCK_ID"] = $IBLOCK_ID;
    } else {
        $arFilter["IBLOCK_ID"] = -1;
    }
    
    $arFilter["CHECK_PERMISSIONS"] = "Y";
    
    // заголовки списка ---------------
    $arHeaders = array(
        array(
            "id" => "ID",
            "content" => $oMessage->get("ID"),
            "sort" => "id",
            "default" => true,
            "align" => "right",
        ),
        array(
            "id" => "NAME",
            "content" => $oMessage->get("NAME"),
            "sort" => "name",
            "default" => true,
        ),
        array(
            "id" => "ACTIVE",
            "content" => $oMessage->get("ACTIVE"),
            "sort" => "active",
            "default" => true,
            "align" => "center",
        ),
        array(
            "id" => "SORT",
            "content" => $oMessage->get("SORT"),
            "sort" => "sort",
            "default" => true,
            "align" => "right",
        ),
        array(
            "id" => "CODE",
            "content" => $oMessage->get("CODE"),
            "sort" => "code",
        ),
        array(
            "id" => "XML_ID",
            "content" => $oMessage->get("XML_ID"),
        ),
        array(
            "id" => "ELEMENT_CNT",
            "content" => $oMessage->get("ELEMENT_CNT"),
            "sort" => "element_cnt",
            "align" => "right",
        ),
        array(
            "id" => "SECTION_CNT",
            "content" => $oMessage->get("SECTION_CNT"),
            "default" => true,
            "align" => "right",
        ),
        array(
            "id" => "TIMESTAMP_X",
            "content" => $oMessage->get("TIMESTAMP"),
            "sort" => "timestamp_x",
        ),
        array(
            "id" => "MODIFIED_BY",
            "content" => $oMessage->get("MODIFIED_BY"),
            "sort" => "modified_by",
        ),
        array(
            "id" => "DATE_CREATE",
            "content" => $oMessage->get("DATE_CREATE"),
            "sort" => "date_create",
        ),
        array(
            "id" => "CREATED_BY",
            "content" => $oMessage->get("CREATED_BY"),
            "sort" => "created_by",
        ),
    );
    if ($entity_id) {
        $USER_FIELD_MANAGER->AdminListAddHeaders($entity_id, $arHeaders);
    }
    $lAdmin->AddHeaders($arHeaders);
    
    
    $arVisibleColumns = $lAdmin->GetVisibleHeaderColumns();
    $arVisibleColumnsMap = array();
    foreach ($arVisibleColumns as $value) {
        $arVisibleColumnsMap[$value] = true;
    }
    
    $arVisibleColumns[] = 'DEPTH_LEVEL';
    $arVisibleColumns[] = 'XML_ID';
    $arVisibleColumns[] = 'NAME';
    $arVisibleColumns[] = 'ID';
    $arVisibleColumns = array_values(array_unique($arVisibleColumns));
    
    if (array_key_exists("ELEMENT_CNT", $arVisibleColumnsMap)) {
        $arFilter["CNT_ALL"] = "Y";
        $arFilter["ELEMENT_SUBSECTIONS"] = "N";
        $rsData = CIBlockSection::GetList($arOrder, $arFilter, true, $arVisibleColumns);
    } else {
        $rsData = CIBlockSection::GetList($arOrder, $arFilter, false, $arVisibleColumns);
    }
    
    $rsData = new CAdminResult($rsData, $sTableID);
    $rsData->NavStart();
    $lAdmin->NavText($rsData->GetNavPrint($arIBlock["SECTIONS_NAME"]));
    
    $strPath = "";
    $jsPath = "";
    $nameSeparator = "";
    if (!$simpleName && intval($find_section_section) > 0) {
        $nameSeparator = "&nbsp;/&nbsp;";
        $nav = CIBlockSection::GetNavChain($IBLOCK_ID, $find_section_section);
        while ($ar_nav = $nav->GetNext()) {
            $strPath .= htmlspecialcharsbx($ar_nav["~NAME"], ENT_QUOTES) . $nameSeparator;
            $jsPath .= htmlspecialcharsbx(CUtil::JSEscape($ar_nav["~NAME"]), ENT_QUOTES) . $nameSeparator;
        }
    }
    
    $arUsersCache = array();
    
    $arItemsJorJs = array();
    while ($arItem = $rsData->NavNext(false)) {
        
        $sectionListUrl = $extReloadUrl . '&find_section_section=' . $arItem['ID'];
        
        $row =& $lAdmin->AddRow($arItem['ID'], $arItem);
        
        if ($entity_id) {
            $USER_FIELD_MANAGER->AddUserFields($entity_id, $arItem, $row);
        }
        
        $row->AddViewField("NAME",
            '<a href="' . $sectionListUrl . '" onclick="' . $lAdmin->ActionRedirect($sectionListUrl) . '; return false;" title="' . $oMessage->get("LIST") . '">' . $arItem['NAME'] . '</a><div style="display:none" id="name_' . $arItem['ID'] . '">' . $strPath . $arItem['NAME'] . $nameSeparator . '</div>');
        
        $row->AddCheckField("ACTIVE", false);
        
        if (array_key_exists("ELEMENT_CNT", $arVisibleColumnsMap)) {
            $row->AddViewField("ELEMENT_CNT",
                $arItem['ELEMENT_CNT'] . '(' . IntVal(CIBlockSection::GetSectionElementsCount($arItem['ID'],
                    Array("CNT_ALL" => "Y"))) . ')');
        }
        
        if (array_key_exists("SECTION_CNT", $arVisibleColumnsMap)) {
            $arFilter = Array("IBLOCK_ID" => $IBLOCK_ID, "SECTION_ID" => $arItem['ID']);
            $row->AddViewField("SECTION_CNT",
                '<a href="' . $sectionListUrl . '" onclick="' . $lAdmin->ActionRedirect($sectionListUrl) . '; return false;" title="' . $oMessage->get("LIST") . '">' . IntVal(CIBlockSection::GetCount($arFilter)) . '</a>');
        }
        
        if (array_key_exists("MODIFIED_BY", $arVisibleColumnsMap) && intval($arItem['MODIFIED_BY']) > 0) {
            if (!array_key_exists($arItem['MODIFIED_BY'], $arUsersCache)) {
                $rsUser = CUser::GetByID($arItem['MODIFIED_BY']);
                $arUsersCache[$arItem['MODIFIED_BY']] = $rsUser->Fetch();
            }
            
            if ($arUser = $arUsersCache[$arItem['MODIFIED_BY']]) {
                $row->AddViewField("MODIFIED_BY",
                    '[<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $arItem['MODIFIED_BY'] . '" title="' . $oMessage->get("USERINFO") . '">' . $arItem['MODIFIED_BY'] . "</a>]&nbsp;(" . htmlspecialcharsEx($arUser["LOGIN"]) . ") " . htmlspecialcharsEx($arUser["NAME"] . " " . $arUser["LAST_NAME"]));
            }
        }
        
        if (array_key_exists("CREATED_BY", $arVisibleColumnsMap) && intval($arItem['CREATED_BY']) > 0) {
            if (!array_key_exists($arItem['CREATED_BY'], $arUsersCache)) {
                $rsUser = CUser::GetByID($arItem['CREATED_BY']);
                $arUsersCache[$arItem['CREATED_BY']] = $rsUser->Fetch();
            }
            if ($arUser = $arUsersCache[$arItem['MODIFIED_BY']]) {
                $row->AddViewField("CREATED_BY",
                    '[<a href="user_edit.php?lang=' . LANGUAGE_ID . '&ID=' . $arItem['CREATED_BY'] . '" title="' . $oMessage->get("USERINFO") . '">' . $arItem['CREATED_BY'] . "</a>]&nbsp;(" . htmlspecialcharsEx($arUser["LOGIN"]) . ") " . htmlspecialcharsEx($arUser["NAME"] . " " . $arUser["LAST_NAME"]));
            }
        }
        
        
        $arItemsJorJs[$arItem['ID']] = array(
            'id' => $arItem['ID'],
            'xmlId' => $arItem['XML_ID'],
            'title' => $arItem["NAME"] . ' [' . $arItem['ID'] . ']',
            'name' => $arItem["NAME"],
            'path' => $jsPath,
            'level' => $arItem['DEPTH_LEVEL'],
        );
        
        $row->AddActions(array(
            array(
                "DEFAULT" => "Y",
                "TEXT" => $oMessage->get("SELECT"),
                "ACTION" => "javascript:VKapiMarketIblockSectionSearchJs.selectedValue(" . $arItem['ID'] . ")",
            ),
        ));
    }
    
    $lAdmin->AddFooter(
        array(
            array(
                "title" => $oMessage->get("LIST_SELECTED"),
                "value" => $rsData->SelectedRowsCount()
            ),
            array(
                "counter" => true,
                "title" => $oMessage->get("LIST_CHECKED"),
                "value" => "0"
            ),
        )
    );
    
    
    $lAdmin->AddAdminContextMenu(array(), false);
    
    if ($IBLOCK_ID > 0) {
        $chain = $lAdmin->CreateChain();
        if (intval($find_section_section) > 0) {
            $nav = CIBlockSection::GetNavChain($IBLOCK_ID, $find_section_section);
            while ($ar_nav = $nav->GetNext()) {
                if ($find_section_section == $ar_nav["ID"]) {
                    $chain->AddItem(array(
                        "TEXT" => $ar_nav["NAME"],
                    ));
                } else {
                    $chain->AddItem(array(
                        "TEXT" => $ar_nav["NAME"],
                        "LINK" => $extReloadUrl . '&find_section_section=' . $ar_nav["ID"],
                        "ONCLICK" => $lAdmin->ActionRedirect($extReloadUrl . '&find_section_section=' . $ar_nav["ID"]) . ';return false;',
                    ));
                }
            }
        }
        $lAdmin->ShowChain($chain);
    } else {
        $lAdmin->BeginPrologContent();
        $message = new CAdminMessage(array(
            "MESSAGE" => $oMessage->get("CHOOSE_IBLOCK"),
            "TYPE" => "OK"
        ));
        echo $message->Show();
        $lAdmin->EndPrologContent();
    }
    
    //данные ---
    $lAdmin->BeginPrologContent();
?>
    <script type="text/javascript">
        var VKapiMarketIblockSectionSearchJs = new VKapiMarketIblockSectionSearch(<?= \Bitrix\Main\Web\Json::encode(array(
            'tableId' => $sTableID,
            'reloadUrl' => $reloadUrl,
            'items' => $arItemsJorJs
        ));?>);
    </script>
<?php
    $lAdmin->EndPrologContent();
    
    
    $lAdmin->CheckListMode();
    
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_popup_admin.php");
    
    // подключаем стили и скрипты ----
    $oManager->showAdminPageCssJs();
    
    
    // вывод над фильтром ссылки на католог -----------------
    $chain = new \CAdminChain("main_navchain");
    $chain->AddItem(array(
        "TEXT" => htmlspecialcharsbx($arIBlock["NAME"]),
        "LINK" => $extReloadUrl . '&find_section_section=0',
        "ONCLICK" => $lAdmin->ActionRedirect($extReloadUrl . '&find_section_section=0') . ';return false;',
    ));
    $chain->Show();

?>


    <form method="GET" name="find_section_form" action="<? echo $APPLICATION->GetCurPage() ?>">
        <?
            $arFindFields = array();
            if (!$iblockFix) {
                $arFindFields['IBLOCK_ID'] = $oMessage->get('IBLOCK');
            }
            if ($useParentFilter) {
                $arFilterFields['parent'] = $oMessage->get('PARENT_ID');
            }
            $arFindFields = array_merge(
                $arFindFields,
                array(
                    "name" => $oMessage->get("NAME"),
                    "id" => $oMessage->get("ID"),
                    "timestamp_x" => $oMessage->get("TIMESTAMP"),
                    "modified_by" => $oMessage->get("MODIFIED_BY"),
                    "date_create" => $oMessage->get("DATE_CREATE"),
                    "created_by" => $oMessage->get("CREATED_BY"),
                    "code" => $oMessage->get("CODE"),
                    "xml_id" => $oMessage->get("XML_ID"),
                    "active" => $oMessage->get("ACTIVE"),
                )
            );
            $USER_FIELD_MANAGER->AddFindFields($entity_id, $arFindFields);
            
            $oFilter = new CAdminFilter($sTableID . "_filter", $arFindFields);
        
        ?>
        
        <?
            if ($iblockFix) {
                ?><input type="hidden" name="IBLOCK_ID" value="<?= $IBLOCK_ID; ?>">
                <input type="hidden" name="find_iblock_id" value="<?= $IBLOCK_ID; ?>"><?
            }
            $oFilter->Begin();
            if (!$iblockFix) {
                $iblockFilter = array(
                    'MIN_PERMISSION' => 'S'
                );
                if ($hideIblockId > 0) {
                    $iblockFilter['!ID'] = $hideIblockId;
                }
                ?>
                <tr>
                    <td><b><? echo $oMessage->get("IBLOCK") ?></b></td>
                    <td><? echo GetIBlockDropDownListEx(
                            $IBLOCK_ID,
                            "find_type",
                            "find_iblock_id",
                            $iblockFilter,
                            '',
                            'VKapiMarketIblockSectionSearchJs.changeIblock(this)'
                        ); ?></td>
                </tr>
                <?
            }
            if ($useParentFilter) {
                ?>
                <tr>
                <td><? echo $oMessage->get("PARENT_ID") ?>:</td>
                <td>
                    <select name="find_section_section">
                        <option value=""><? echo $oMessage->get("ALL_PARENTS") ?></option>
                        <option value="0"<? if ($find_section_section == "0") echo " selected" ?>><? echo $oMessage->get("ROOT_PARENT_ID") ?></option>
                        <?
                            $bsections = CIBlockSection::GetTreeList(array("IBLOCK_ID" => $IBLOCK_ID),
                                array("ID", "NAME", "DEPTH_LEVEL"));
                            while ($arSection = $bsections->GetNext()):
                                ?>
                                <option value="<? echo $arSection["ID"] ?>"<? if ($arSection["ID"] == $find_section_section) echo " selected" ?>><? echo str_repeat("&nbsp;.&nbsp;",
                                $arSection["DEPTH_LEVEL"]) ?><? echo $arSection["NAME"] ?></option><?
                            endwhile;
                        ?>
                    </select>
                </td>
                </tr><?
            }
        ?>
        <tr>
            <td><b><? echo $oMessage->get("NAME") ?>:</b></td>
            <td><input type="text" name="find_section_name" value="<? echo htmlspecialcharsbx($find_section_name) ?>"
                       size="47">&nbsp;<?= ShowFilterLogicHelp() ?></td>
        </tr>

        <tr>
            <td><? echo $oMessage->get("ID") ?>:</td>
            <td><input type="text" name="find_section_id" size="47"
                       value="<? echo htmlspecialcharsbx($find_section_id) ?>"></td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("TIMESTAMP") . ":" ?></td>
            <td><? echo CalendarPeriod("find_section_timestamp_1", htmlspecialcharsbx($find_section_timestamp_1),
                    "find_section_timestamp_2", htmlspecialcharsbx($find_section_timestamp_2), "find_section_form",
                    "Y") ?></td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("MODIFIED_BY") ?>:</td>
            <td>
                <? echo \FindUserID(
                    "find_section_modified_user_id",
                    $find_section_modified_by,
                    "",
                    "find_section_form",
                    "5",
                    "",
                    " ... ",
                    "",
                    ""
                ); ?>
            </td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("DATE_CREATE") . ":" ?></td>
            <td><? echo CalendarPeriod("find_section_date_create_1", htmlspecialcharsbx($find_section_date_create_1),
                    "find_section_date_create_2", htmlspecialcharsbx($find_section_date_create_2),
                    "find_section_form") ?></td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("CREATED_BY") ?>:</td>
            <td>
                <? echo \FindUserID(
                    "find_section_created_user_id",
                    $find_section_created_by,
                    "",
                    "find_section_form",
                    "5",
                    "",
                    " ... ",
                    "",
                    ""
                ); ?>
            </td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("CODE") ?>:</td>
            <td><input type="text" name="find_section_code" size="47"
                       value="<? echo htmlspecialcharsbx($find_section_code) ?>"></td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("XML_ID") ?>:</td>
            <td><input type="text" name="find_section_external_id" size="47"
                       value="<? echo htmlspecialcharsbx($find_section_external_id) ?>"></td>
        </tr>
        <tr>
            <td><? echo $oMessage->get("ACTIVE") ?>:</td>
            <td>
                <select name="find_section_active">
                    <option value=""><?= htmlspecialcharsbx($oMessage->get('IBLOCK_ALL')) ?></option>
                    <option value="Y"<? if ($find_section_active == "Y") echo " selected" ?>><?= htmlspecialcharsbx($oMessage->get("IBLOCK_YES")) ?></option>
                    <option value="N"<? if ($find_section_active == "N") echo " selected" ?>><?= htmlspecialcharsbx($oMessage->get("IBLOCK_NO")) ?></option>
                </select>
            </td>
        </tr>
        <?
            $USER_FIELD_MANAGER->AdminListShowFilter($entity_id);
            
            $oFilter->Buttons();
        ?>
        <span class="adm-btn-wrap">
            <input type="submit" class="adm-btn" name="set_filter"
                   value="<? echo $oMessage->get("FILTER.BTN_SET"); ?>"
                   title="<? echo $oMessage->get("FILTER.BTN_SET_TITLE"); ?>"
                   onclick="VKapiMarketIblockSectionSearchJs.applyFilter(this);">
        </span>
        <span class="adm-btn-wrap">
            <input type="submit" class="adm-btn" name="del_filter"
                   value="<? echo $oMessage->get("FILTER.BTN_CLEAR"); ?>"
                   title="<? echo $oMessage->get("FILTER.BTN_CLEAR_TITLE"); ?>"
                   onclick="VKapiMarketIblockSectionSearchJs.deleteFilter(this);">
        </span>
        <?
            $oFilter->End();
        ?>
    </form>


<?
    $lAdmin->DisplayList();
?>
<?
    require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_popup_admin.php");
    