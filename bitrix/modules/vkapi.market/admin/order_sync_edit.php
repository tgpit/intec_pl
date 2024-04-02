<?php

use VKapi\Market\Exception\BaseException;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$oManager = \VKapi\Market\Manager::getInstance();
$oAdmin = new \VKapi\Market\Admin($oManager->getModuleId());
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'ADMIN.ORDER_SYNC_EDIT');
$oConnect = new \VKapi\Market\Connect();
$oSaleSync = new \VKapi\Market\Sale\Order\Sync();
// аккаунты ---------------------------
$arAccounts = $oConnect->getAccountsSelectList();
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
// проверка доступа
$oManager->base()->checkLevelAccess();
$arSiteSelect = $oManager->getSiteSelectList();
// запрос имеющихся значений -------------
if ($req->get('ID') || \intval($req->get('COPY_ID'))) {
    $dbr = $oSaleSync->table()->getList(['filter' => ['ID' => \intval($req->get('ID')) ? \intval($req->get('ID')) : \intval($req->get('COPY_ID'))]]);
    if ($ar = $dbr->fetch()) {
        $arResult = $ar;
    }
}
// AJAX -------------------------------------
if ($req->isPost() && $req->isAjaxRequest() && $req->getPost('method')) {
    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
    try {
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi" . ".mark" . "" . "et") === \constant("M" . "ODULE" . "_DEM" . "O" . "_EXPIRED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRE" . "D"), "BXMAKER_DEMO_" . "" . "EXPI" . "" . "" . "RED");
        }
        if (!$oManager->base()->canActionRight('W')) {
            throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }
        switch ($req->getPost('method')) {
            // группу вконтакте
            case 'getGroup':
                if (!$req->getPost('account_id') || \intval($req->getPost('account_id')) <= 0) {
                    throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_ACCOUNT_ID'), 'AJAX_ERROR_ACCOUNT_ID');
                }
                $oConnect->initAccountId($req->getPost('account_id'));
                /**
                 * @var \VKapi\Market\Result $result
                 */
                $result = $oConnect->method('groups.get', ['filter' => 'editor', 'extended' => 1]);
                $response = $result->getData('response');
                $oJsonResponse->setResponse($response);
                break;
            default:
                throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_UNKNOWN_METHOD'), 'AJAX_ERROR_UNKNOWN_METHOD');
        }
    } catch (\Throwable $ex) {
        $oJsonResponse->setException($ex);
    }
    $oJsonResponse->output();
}
// save ----------------------------
if ($oManager->base()->canActionRight('W') && ($apply || $save || $save_and_add) && \check_bitrix_sessid() && $req->isPost()) {
    do {
        $errors = [];
        $arFields = [];
        if (\strlen(\trim($req->getPost('ACCOUNT_ID'))) <= 0) {
            $errors[] = new \Bitrix\Main\Error($oMessage->get('FIELD_ERROR.ACCOUNT_ID'));
            break;
        }
        if (\strlen(\trim($req->getPost('GROUP_ID'))) <= 0) {
            $errors[] = new \Bitrix\Main\Error($oMessage->get('FIELD_ERROR.GROUP_ID'));
            break;
        }
        if (\strlen(\trim($req->getPost('SITE_ID'))) <= 0 || !\in_array($req->getPost('SITE_ID'), $arSiteSelect['REFERENCE_ID'])) {
            $errors[] = new \Bitrix\Main\Error($oMessage->get('FIELD_ERROR.SITE_ID'));
            break;
        }
        $arFields['ACCOUNT_ID'] = \intval($req->getPost('ACCOUNT_ID'));
        $arFields['GROUP_ID'] = \intval($req->getPost('GROUP_ID'));
        $arFields['GROUP_NAME'] = \trim($req->getPost('GROUP_NAME'));
        $arFields['ACTIVE'] = $req->getPost('ACTIVE') == 'Y';
        $arFields['EVENT_ENABLED'] = $req->getPost('EVENT_ENABLED') == 'Y';
        $arFields['EVENT_CODE'] = \trim($req->getPost('EVENT_CODE'));
        $arFields['EVENT_SECRET'] = \trim($req->getPost('EVENT_SECRET'));
        $arFields['SITE_ID'] = \trim($req->getPost('SITE_ID'));
        $arFields['GROUP_ACCESS_TOKEN'] = \trim($req->getPost('GROUP_ACCESS_TOKEN'));
        $arFields['PARAMS']['IMPORT_LAST_COUNT'] = (int) $req->getPost('IMPORT_LAST_COUNT');
        $arFields['PARAMS']['IMPORT_START_TIMESTAMP'] = 0;
        $dateTime = \trim($req->getPost('IMPORT_START_TIMESTAMP'));
        if (\preg_match('/^(\\d\\d\\.\\d\\d\\.\\d\\d\\d\\d\\s\\d\\d:\\d\\d:\\d\\d)$/', $dateTime, $match)) {
            $date = new \Bitrix\Main\Type\DateTime($dateTime, 'd.m.Y H:i:s');
            $arFields['PARAMS']['IMPORT_START_TIMESTAMP'] = $date->getTimestamp();
        }
        $arResult = $arFields;
        if (empty($errors)) {
            if ($req->get('ID')) {
                $result = $oSaleSync->table()->update(\intval($req->get('ID')), $arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        \LocalRedirect($APPLICATION->GetCurPageParam());
                    } elseif ($save) {
                        \LocalRedirect($oAdmin->getFullPageUrl('order_sync_list', [], ['ID', 'COPY_ID']));
                    } elseif ($save_and_add) {
                        \LocalRedirect($oAdmin->getFullPageUrl('order_sync_edit', [], ['ID', 'COPY_ID']));
                    }
                } else {
                    $errors = $result->getErrors();
                }
            } else {
                $result = $oSaleSync->table()->add($arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        \LocalRedirect($oAdmin->getFullPageUrl('order_sync_edit', ['ID' => $result->getId()], ['COPY_ID']));
                    } elseif ($save) {
                        \LocalRedirect($oAdmin->getFullPageUrl('order_sync_list', [], ['ID', 'COPY_ID']));
                    } elseif ($save_and_add) {
                        \LocalRedirect($oAdmin->getFullPageUrl('order_sync_edit', [], ['ID', 'COPY_ID']));
                    }
                } else {
                    $errors = $result->getErrors();
                }
            }
        }
    } while (\false);
}
// строка меню над списком --
$oMenu = new \CAdminContextMenu([["TEXT" => $oMessage->get('BTN_LIST'), "LINK" => $oAdmin->getPageUrl('order_sync_list', [], ['ID', 'COPY_ID']), "TITLE" => $oMessage->get('BTN_LIST'), "ICON" => "btn_list"]]);
$tab = new \CAdminTabControl('edit', [['DIV' => 'edit', 'TAB' => $oMessage->get('TAB.MAIN'), 'ICON' => '', 'TITLE' => '']]);
$APPLICATION->SetTitle($oMessage->get('TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
if ($errors && \is_array($errors)) {
    $arStr = [];
    foreach ($errors as $error) {
        $arStr[] = $error->getMessage();
    }
    \CAdminMessage::ShowMessage(\implode('<br />', $arStr));
}
// меню --
$oMenu->Show();
?>

    <form action="<?php 
$APPLICATION->GetCurPage();
?>" method="POST" name="vkapi-market-admin-order-sync-edit"
          enctype="multipart/form-data">
        <?php 
echo \bitrix_sessid_post();
?>

        <?php 
$tab->Begin();
?>
        <?php 
$tab->BeginNextTab();
?>

        <tr>
            <td colspan="2">
                <div class="vkapi-market-admin-message-block"></div>
            </td>
        </tr>

        <?php 
if ($req->get('ID')) {
    ?>
            <tr>
                <td><?php 
    echo $oMessage->get('ID');
    ?>:</td>
                <td>
                    <?php 
    echo $req->get('ID');
    ?>
                </td>
            </tr>
        <?php 
}
?>

        <tr>
            <td><?php 
echo $oMessage->get('ACTIVE');
?></td>
            <td><?php 
echo \InputType('checkbox', 'ACTIVE', 'Y', isset($arResult['ACTIVE']) && !!$arResult['ACTIVE'] ? 'Y' : '');
?>
            </td>
        </tr>


        <tr>
            <td><?php 
echo $oMessage->get('ACCOUNT_ID');
?><span class="req"></span></td>
            <td><?php 
echo \SelectBoxFromArray('ACCOUNT_ID', $arAccounts, isset($arResult['ACCOUNT_ID']) ? $arResult['ACCOUNT_ID'] : '');
?></td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get('GROUP_ID');
?><span class="req"></span></td>
            <td class="vkapi-market-admin-order-sync-edit__group">
                <?php 
echo \InputType('text', 'GROUP_ID', isset($arResult['GROUP_ID']) ? $arResult['GROUP_ID'] : '', '', \false, '', ' readonly="readonly" placeholder="' . $oMessage->get('GROUP_ID_PLACEHOLDER') . '" ');
?>
                <?php 
echo \InputType('text', 'GROUP_NAME', isset($arResult['GROUP_NAME']) ? $arResult['GROUP_NAME'] : '', '', \false, '', ' readonly="readonly" placeholder="' . $oMessage->get('GROUP_NAME_PLACEHOLDER') . '" ');
?>
                <div class="vkapi-market-admin-order-sync-edit__group-options">

                </div>
            </td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get('SITE_ID');
?><span class="req"></span></td>
            <td><?php 
echo \SelectBoxFromArray('SITE_ID', $arSiteSelect, isset($arResult['SITE_ID']) ? $arResult['SITE_ID'] : '');
?></td>
        </tr>


        <tr>
            <td><?php 
echo $oMessage->get('IMPORT_START_TIMESTAMP');
?></td>
            <td>
                <?php 
echo \CAdminCalendar::CalendarDate("IMPORT_START_TIMESTAMP", $arResult['PARAMS']['IMPORT_START_TIMESTAMP'] ? \date('d.m.Y H:i:s', $arResult['PARAMS']['IMPORT_START_TIMESTAMP']) : '', 10, \true);
?>
            </td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get('IMPORT_LAST_COUNT');
?></td>
            <td>
                <?php 
echo \InputType('text', 'IMPORT_LAST_COUNT', (int) $arResult['PARAMS']['IMPORT_LAST_COUNT'] ? (int) $arResult['PARAMS']['IMPORT_LAST_COUNT'] : '', '');
?>
            </td>
        </tr>

        <?php 
if (!$req->get('ID')) {
    ?>
            <tr>
                <td colspan="2">
                    <div style="background-color:#fffdba;padding:8px 16px; line-height: 1.5;margin-top: 16px;">
                        <?php 
    echo $oMessage->get('SHOW_NOTICE');
    ?>
                    </div>

                </td>
            </tr>
        <?php 
}
?>

        <?php 
if ($req->get('ID')) {
    ?>

            <tr class="heading">
                <td colspan="2" class="header">
                    <?php 
    echo $oMessage->get('EVENT_HEADER');
    ?>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <div style="background-color:#fffdba;padding:8px 16px; line-height: 1.5;margin-top: 16px;">
                        <?php 
    echo $oMessage->get('SHOW_MANUAL', ['#URL#' => $oSaleSync->getApiCallbackUrl($req->get('ID'))]);
    ?>
                    </div>
                </td>
            </tr>

            <tr>
                <td><?php 
    echo $oMessage->get('ENABLED');
    ?></td>
                <td><?php 
    echo \InputType('checkbox', 'EVENT_ENABLED', 'Y', isset($arResult['EVENT_ENABLED']) && !!$arResult['EVENT_ENABLED'] ? 'Y' : '');
    ?>
                </td>
            </tr>

            <tr>
                <td><?php 
    echo $oMessage->get('CODE');
    ?></td>
                <td><?php 
    echo \InputType('text', 'EVENT_CODE', $arResult['EVENT_CODE'], '');
    ?>
                </td>
            </tr>

            <tr>
                <td><?php 
    echo $oMessage->get('SECRET');
    ?></td>
                <td><?php 
    echo \InputType('text', 'EVENT_SECRET', $arResult['EVENT_SECRET'], '');
    ?>
                </td>
            </tr>

            <tr class="heading">
                <td colspan="2" class="header">
                    <?php 
    echo $oMessage->get('GROUP_ACCESS_TOKEN_HEADER');
    ?>
                </td>
            </tr>
            <tr>
                <td><?php 
    echo $oMessage->get('GROUP_ACCESS_TOKEN');
    ?></td>
                <td><?php 
    echo \InputType('text', 'GROUP_ACCESS_TOKEN', $arResult['GROUP_ACCESS_TOKEN'], '');
    ?>
                </td>
            </tr>


        <?php 
}
?>





        <?php 
$tab->EndTab();
?>
        <?php 
$tab->Buttons(["disabled" => !$oManager->base()->canActionRight('W'), "btnSaveAndAdd" => $oManager->base()->canActionRight('W')]);
?>
        <?php 
$tab->End();
?>
    </form>


<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";