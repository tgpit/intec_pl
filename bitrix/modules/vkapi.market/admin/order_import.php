<?php

use VKapi\Market\Exception\BaseException;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
\Bitrix\Main\Loader::includeModule('vkapi.market');
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
$oManager = \VKapi\Market\Manager::getInstance();
// проверка доступа
$oManager->base()->checkAccess();
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'ADMIN.ORDER_IMPORT');
$oVkExportParam = \VKapi\Market\Param::getInstance();
$oOrderImport = new \VKapi\Market\Sale\Order\Import();
if ($req->isPost() && $req->getPost('method')) {
    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
    try {
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.ma" . "rke" . "t") == \constant("MODULE" . "_DEMO_EXPIRE" . "" . "" . "" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BX" . "MAKE" . "" . "" . "R_DEMO" . "_EXPIRE" . "" . "D");
        }
        if (!$oManager->base()->canActionRight('W')) {
            throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }
        switch ($req->getPost('method')) {
            case 'import':
                if (\intval($req->getPost('syncId')) <= 0) {
                    throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_SYNC_ID'), 'AJAX_ERROR_SYNC_ID');
                }
                // парамтеры ------------------------------------------------
                $bReset = !!$req->get('reset');
                $bStop = !!$req->get('stop');
                $syncId = \intval($req->get('syncId'));
                $oState = new \VKapi\Market\State('order_import_hand_' . $syncId);
                $arSteps = [1 => ['name' => $oMessage->get('AJAX.STEP1'), 'percent' => 0, 'items' => []], 2 => ['name' => $oMessage->get('AJAX.STEP2'), 'percent' => 0, 'items' => []], 3 => ['name' => $oMessage->get('AJAX.STEP3'), 'percent' => 0, 'items' => []]];
                // определяем текущее состояние
                $stateData = \array_merge(['step' => 1, 'complete' => \false, 'steps' => $arSteps], $oState->get());
                // сброс данных с прошлого ручного экспорта
                if ($stateData['complete'] || $bReset) {
                    $stateData = ['step' => 1, 'complete' => \false, 'steps' => $arSteps];
                    $oOrderImport->item($syncId)->state()->clean();
                }
                // если необходимо остановить экспорт -------------------------
                if ($bStop) {
                    $oJsonResponse->setResponse(['repeat' => \false, 'msg' => $oMessage->get('AJAX.IMPORT.STOP')]);
                    // снимаем флаг оставновки автоэкспорта
                    $oVkExportParam->set('AUTO_EXPORT_STOP', 'N');
                    break;
                }
                if (\CModule::IncludeModuleEx("vkapi.market") === \constant("MODULE_DEM" . "O_EXPIR" . "E" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_E" . "XPIRED"), "BXMA" . "KER_DEMO_E" . "XPIR" . "E" . "" . "D");
                }
                try {
                    // приостановка автоматического экспорта --
                    if ($stateData['step'] == 1) {
                        // останавливаем автоматический экспорт
                        $oVkExportParam->set('AUTO_EXPORT_STOP', 'Y');
                        $stateData['steps'][1]['percent'] = 100;
                        $stateData['step']++;
                        $oJsonResponse->setResponseField('state', $stateData);
                    } elseif ($stateData['step'] == 2) {
                        $resultImport = $oOrderImport->item($syncId)->run();
                        if (!$resultImport->isSuccess()) {
                            $oJsonResponse->setErrorFromResult($resultImport);
                            break;
                        }
                        $resultImportData = $resultImport->getData();
                        if (isset($resultImportData['steps'])) {
                            $stateData['steps'][2]['percent'] = $oState->calcPercentByData($resultImportData);
                            $stateData['steps'][2]['items'] = $resultImportData['steps'];
                            if (\CModule::IncludeModuleEx("vkapi." . "mark" . "e" . "" . "t") == \constant("MODULE_DEMO_EXP" . "I" . "" . "RE" . "" . "D")) {
                                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEM" . "" . "" . "O_EXPIRED"), "BXMAKE" . "R_DEMO_EXPIRE" . "D");
                            }
                            if ($resultImportData['complete']) {
                                $stateData['step']++;
                            }
                        }
                        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "" . "k" . "et") == \constant("MODULE_DEMO_EXPIRE" . "" . "D")) {
                            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIR" . "" . "ED"), "BXMAKER_D" . "EMO_EX" . "P" . "" . "" . "" . "IRED");
                        }
                        $oJsonResponse->setResponseField('state', $stateData);
                    } elseif ($stateData['step'] == 3) {
                        // подготовка разделов
                        $oVkExportParam->set('AUTO_EXPORT_STOP', 'N');
                        // операция выполнена
                        $stateData['complete'] = \true;
                        $stateData['steps'][5]['percent'] = 100;
                        $stateData['step']++;
                        $oJsonResponse->setResponseField('state', $stateData);
                    }
                } catch (\VKapi\Market\Exception\BaseException $e) {
                    $e->setCustomDataField('state', $stateData);
                    $oJsonResponse->setException($e);
                }
                // сохраняем состояние
                $oState->set($stateData)->save();
                break;
            default:
                throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_UNKNOWN_METHOD'), 'AJAX_ERROR_UNKNOWN_METHOD');
        }
    } catch (\Throwable $ex) {
        $oJsonResponse->setException($ex);
    }
    $oJsonResponse->output();
}
$tab = new \CAdminTabControl('edit', [['DIV' => 'edit', 'TAB' => $oMessage->get('TAB.MAIN'), 'ICON' => '', 'TITLE' => '']]);
$APPLICATION->SetTitle($oMessage->get('PAGE_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
?>

    <form action="<?php 
echo $APPLICATION->GetCurPage();
?>" method="POST" name="vkapi-market-order-import__form">
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
                <?php 
// показ блока экспорта вручную
$oOrderImport->showImportByHand();
?>
            </td>
        </tr>

        <?php 
$tab->EndTab();
?>
        <?php 
$tab->End();
?>
    </form>


<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";