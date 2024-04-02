<?php

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$VKAPI_MARKET_MODULE_ID = 'vkapi.market';
$MODULE_CODE = 'vkapi_market';
$MOD_RIGHT = $APPLICATION->GetGroupRight($VKAPI_MARKET_MODULE_ID);
$moduleSort = 10000;
if ($MOD_RIGHT > "D") {
    $aMenu = array("parent_menu" => "global_menu_vkapi", "sort" => $moduleSort, "section" => $VKAPI_MARKET_MODULE_ID, "url" => '/bitrix/admin/' . $VKAPI_MARKET_MODULE_ID . '_list.php?lang=' . \LANGUAGE_ID, "text" => \GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_NAME'), "title" => \GetMessage($MODULE_CODE . '_MAIN_MENU_LINK_DESCRIPTION'), "icon" => $MODULE_CODE . '_icon', "page_icon" => $MODULE_CODE . '_page_icon', "items_id" => $MODULE_CODE . '_main_menu_items', "items" => array());
    $aMenu['items'][] = array('url' => '/bitrix/admin/settings.php?lang=' . \LANGUAGE_ID . '&mid=' . $VKAPI_MARKET_MODULE_ID . '&mid_menu=1', 'more_url' => array("/bitrix/admin/settings.php?lang='.LANGUAGE_ID.'&mid=' . {$VKAPI_MARKET_MODULE_ID} . '&mid_menu=1"), 'module_id' => $VKAPI_MARKET_MODULE_ID, 'text' => \GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'), "title" => \GetMessage($MODULE_CODE . '_OPTIONS_MENU_LINK_NAME'), 'sort' => $moduleSort++);
    $arMenuItems = array('list.php', 'album_list.php', 'export_list.php', 'export_now.php', 'delete.php', 'order_sync_list.php', 'order_import.php', 'log.php');
    $path = \dirname(__FILE__);
    foreach ($arMenuItems as $item) {
        $aMenu['items'][] = array('url' => '/bitrix/admin/' . $VKAPI_MARKET_MODULE_ID . '_' . $item . '?lang=' . \LANGUAGE_ID, 'more_url' => array('/bitrix/admin/' . $VKAPI_MARKET_MODULE_ID . '_' . \str_replace('list.php', 'edit.php', $item) . '?lang=' . \LANGUAGE_ID), 'module_id' => $VKAPI_MARKET_MODULE_ID, 'text' => \GetMessage($MODULE_CODE . '_' . \substr($item, 0, \strpos($item, '.')) . '_MENU_LINK_NAME'), "title" => \GetMessage($MODULE_CODE . '_' . \substr($item, 0, \strpos($item, '.')) . '_MENU_LINK_DESCRIPTION'), 'sort' => $moduleSort++);
    }
    $aMenu['items'][] = array('url' => "javascript:window.open('https://bxmaker.ru/doc/vk/', '_blank');", 'more_url' => array(), 'module_id' => $VKAPI_MARKET_MODULE_ID, 'text' => \GetMessage($MODULE_CODE . '_DOC_MENU_LINK_NAME'), "title" => \GetMessage($MODULE_CODE . '_DOC_MENU_LINK_NAME'), 'sort' => $moduleSort++);
    \Bitrix\Main\Type\Collection::sortByColumn($aMenu['items'], 'sort');
    $aModuleMenu[] = $aMenu;
    return $aModuleMenu;
}
return \false;