<?php

global $APPLICATION;
$VKAPI_MARKET_MODULE_ID = 'vkapi.market';
$module_id = 'vkapi.market';
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
// првоерка установки ----
if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
    die;
}
$oManager = \VKapi\Market\Manager::getInstance();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
// проверка доступа -------
$oManager->base()->checkLevelAccess();
\CUtil::InitJSCore('jquery');
$arIblock = ['REFERENCE' => [], 'REFERENCE_ID' => []];
$arPrice = ['REFERENCE' => [], 'REFERENCE_ID' => []];
$arInterval = ['REFERENCE' => [0.5, 0.6, 0.7, 0.8, 0.9, 1, 1.1, 1.2, 1.3, 1.4, 1.5], 'REFERENCE_ID' => [500, 600, 700, 800, 900, 1000, 1100, 1200, 1300, 1400, 1500]];
$arSite = [];
$dbr = \CSite::GetList($by = 'sort', $order = 'asc');
while ($ar = $dbr->Fetch()) {
    $arSite[$ar['ID']] = '[' . $ar['ID'] . '] ' . $ar['NAME'];
}
$site = '';
if (\CMain::IsHTTPS()) {
    $site = 'https://';
} else {
    $site = 'http://';
}
$site .= \preg_replace('/:[\\d]+$/', '', $_SERVER['HTTP_HOST']);
$arOptions = [];
// основное
$sid = '';
$arOptionCurrent = ['NAME' => \GetMessage('AP_EDIT_TAB.GEN'), 'OPTIONS' => [['SID' => $sid, 'CODE' => 'DEBUG', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => \VKapi\Market\Export\Log::LEVEL_NONE, 'VALUES' => \VKapi\Market\Export\Log::getLevelListForSelect()], ['SID' => $sid, 'CODE' => 'DISABLE_UPDATE_PICTURE', 'TYPE' => 'CHECKBOX', 'DEFAULT_VALUE' => 'N'], ['SID' => $sid, 'CODE' => 'CONNECT_INTERVAL', 'TYPE' => 'LIST', 'VALUES' => $arInterval, 'DEFAULT_VALUE' => '500'], ['SID' => $sid, 'CODE' => 'TIMEOUT', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => '45'], ['SID' => $sid, 'CODE' => 'DESCRIPTION_LENGTH_LIMIT', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => '5000'], ['SID' => $sid, 'CODE' => 'ADD_TO_VK_PACK_LENGTH', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => '1'], ['SID' => $sid, 'CODE' => 'TIME_TO_START_EXEC', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'CODE' => 'CRON', 'TYPE' => 'INFO', 'DEFAULT_VALUE' => $oManager->getModulePath(\true) . '/tools/cron.php'], ['GROUP' => 'VK_LINK', 'SID' => $sid, 'CODE' => 'URL_HTTPS', 'TYPE' => 'CHECKBOX', 'DEFAULT_VALUE' => 'N'], ['SID' => $sid, 'CODE' => 'URL_UTM', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['GROUP' => 'VK', 'SID' => $sid, 'CODE' => 'APP_ID', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'CODE' => 'APP_SECRET', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'GROUP' => 'ANTIGATE', 'CODE' => 'ANTIGATE_KEY', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'GROUP' => 'PROXY', 'CODE' => 'ENABLE_PROXY', 'TYPE' => 'CHECKBOX', 'DEFAULT_VALUE' => 'N'], ['SID' => $sid, 'CODE' => 'PROXY_HOST', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'CODE' => 'PROXY_PORT', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'CODE' => 'PROXY_LOGIN', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => ''], ['SID' => $sid, 'CODE' => 'PROXY_PASS', 'TYPE' => 'STRING', 'DEFAULT_VALUE' => '']]];
$arOptions[] = $arOptionCurrent;
// интернет магазин
foreach ($arSite as $siteId => $siteName) {
    $arOptionCurrent = ['NAME' => \GetMessage('AP_EDIT_TAB.SALE', ['#SITE_NAME#' => $siteName]), 'OPTIONS' => [['SID' => $siteId, 'GROUP' => 'SALE_STATUS', 'CODE' => 'STATUS_0', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_1', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_2', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_3', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_4', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_5', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'CODE' => 'STATUS_6', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleStatusSelect()], ['SID' => $siteId, 'GROUP' => 'DELIVERY', 'CODE' => 'DELIVERY_ID', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleDeliveryIdsSelect()], ['SID' => $siteId, 'CODE' => 'DELIVERY_ID_COURIER', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleDeliveryIdsSelect()], ['SID' => $siteId, 'CODE' => 'DELIVERY_ID_POCHTA', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleDeliveryIdsSelect()], ['SID' => $siteId, 'CODE' => 'DELIVERY_ID_POINT', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSaleDeliveryIdsSelect()], ['SID' => $siteId, 'GROUP' => 'SALE_OTHER', 'CODE' => 'PERSONAL_TYPE', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePersonaleTypeSelect()], ['SID' => $siteId, 'CODE' => 'PAYMENT_ID', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePaymentIdsSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_VKORDER', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_FIO', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_PHONE', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_ADDRESS', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_COMMENT_FOR_USER', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_WIDTH', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_HEIGHT', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_LENGTH', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()], ['SID' => $siteId, 'CODE' => 'SALE_PROPERTY_WEIGHT', 'TYPE' => 'LIST', 'DEFAULT_VALUE' => '', 'VALUES' => $oManager->getSalePropertiesSelect()]]];
    $arOptions[] = $arOptionCurrent;
}
// ////////////////////////////////////////////////////////////////////////////
// ////////////////////////////////////////////////////////////////////////////
$PERMISSION = $APPLICATION->GetGroupRight($VKAPI_MARKET_MODULE_ID);
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
if ($PERMISSION == "W") {
    if (($apply || $save) && \check_bitrix_sessid() && $_POST) {
        foreach ($arOptions as $arOption) {
            $key = $arOption['KEY'];
            foreach ($arOption['OPTIONS'] as $arItem) {
                switch ($arItem['TYPE']) {
                    case 'STRING':
                        \Bitrix\Main\Config\Option::set($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $req->getPost($arItem['CODE'] . '_' . $arItem['SID']) ? \trim($req->getPost($arItem['CODE'] . '_' . $arItem['SID'])) : $arItem['DEFAULT_VALUE'], $arItem['SID']);
                        break;
                    case 'NUMBER':
                        \Bitrix\Main\Config\Option::set($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $req->getPost($arItem['CODE'] . '_' . $arItem['SID']) ? \intval($req->getPost($arItem['CODE'] . '_' . $arItem['SID'])) : $arItem['DEFAULT_VALUE'], $arItem['SID']);
                        break;
                    case 'CHECKBOX':
                        \Bitrix\Main\Config\Option::set($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $req->getPost($arItem['CODE'] . '_' . $arItem['SID']) && $req->getPost($arItem['CODE'] . '_' . $arItem['SID']) == 'Y' ? 'Y' : 'N', $arItem['SID']);
                        break;
                    case 'LIST':
                        \Bitrix\Main\Config\Option::set($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], !\is_null($req->getPost($arItem['CODE'] . '_' . $arItem['SID'])) && \in_array($req->getPost($arItem['CODE'] . '_' . $arItem['SID']), $arItem['VALUES']['REFERENCE_ID']) ? $req->getPost($arItem['CODE'] . '_' . $arItem['SID']) : '', $arItem['SID']);
                        break;
                }
            }
        }
    }
}
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
// TABS
$tabs = [];
foreach ($arOptions as $k => $arOption) {
    $tabs[] = ['DIV' => $k, 'TAB' => $arOption['NAME'], 'ICON' => '', 'TITLE' => isset($arOption['DESCRIPTION']) ? $arOption['DESCRIPTION'] : $arOption['NAME']];
}
$tabs[] = ['DIV' => \count($tabs), 'TAB' => \GetMessage('AP_EDIT_TAB.ACCESS'), 'ICON' => '', 'TITLE' => \GetMessage('AP_EDIT_TAB.ACCESS')];
$tab = new \CAdminTabControl('options_tabs', $tabs);
$tab->Begin();
?>


<form class="vkapi-market-admin-option-page" method="post"
      action="<?php 
echo $APPLICATION->GetCurPage();
?>?mid=<?php 
echo \urlencode($mid);
?>&amp;lang=<?php 
echo \LANGUAGE_ID;
?>&amp;mid_menu=<?php 
echo $mid_menu;
?>"><?php 
echo \bitrix_sessid_post();
?>

    <input type="hidden" name="Update" value="Y">

    <?php 
$oOption = new \Bitrix\Main\Config\Option();
foreach ($arOptions as $k => $arOption) {
    $tab->BeginNextTab();
    foreach ($arOption['OPTIONS'] as $arItem) {
        if (isset($arItem['GROUP'])) {
            ?>
                <tr class="heading">
                    <td colspan="2"><?php 
            echo \GetMessage('AP_OPTION.GROUP.' . $arItem['GROUP']);
            ?></td>
                </tr>
                <?php 
        }
        ?>


            <tr>
                <td class="first"
                    style="width:30%;"><?php 
        echo isset($arItem['CODE_NAME']) ? $arItem['CODE_NAME'] : \GetMessage('AP_OPTION.' . $arItem['CODE']);
        ?></td>
                <td><?php 
        switch ($arItem['TYPE']) {
            case 'STRING':
                echo \InputType('text', $arItem['CODE'] . '_' . $arItem['SID'], $oOption->get($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']), '');
                break;
            case 'NUMBER':
                echo \InputType('text', $arItem['CODE'] . '_' . $arItem['SID'], \intval($oOption->get($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID'])), '');
                break;
            case 'CHECKBOX':
                echo \InputType('checkbox', $arItem['CODE'] . '_' . $arItem['SID'], 'Y', [$oOption->get($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID'])]);
                break;
            case 'LIST':
                echo \SelectBoxFromArray($arItem['CODE'] . '_' . $arItem['SID'], $arItem['VALUES'], $oOption->get($VKAPI_MARKET_MODULE_ID, $arItem['CODE'], $arItem['DEFAULT_VALUE'], $arItem['SID']));
                break;
            case 'INFO':
                echo $arItem['DEFAULT_VALUE'];
                break;
        }
        \ShowJSHint(\GetMessage('AP_OPTION.' . $arItem['CODE'] . '.HELP'));
        ?>
                    <?php 
        echo \GetMessage('AP_OPTION.' . $arItem['CODE'] . '.EXPAND');
        ?>
                </td>
                <td></td>
            </tr>
            <?php 
    }
}
$tab->BeginNextTab();
// вкладка с правами доступа ---
$module_id = $VKAPI_MARKET_MODULE_ID;
require_once $_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/admin/group_rights.php';
$tab->Buttons(["disabled" => \false]);
$tab->End();
?>
</form>
<?php 