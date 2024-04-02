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
$oExport = new \VKapi\Market\Export();
$oConnect = new \VKapi\Market\Connect();
$oVkExportParam = \VKapi\Market\Param::getInstance();
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'EXPORT_NOW');
if ($req->isPost() && $req->getPost('method')) {
    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
    try {
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar" . "k" . "e" . "" . "" . "" . "t") == \constant("MODULE_DEMO" . "_EXPIRE" . "" . "D")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI" . ".MARKET.DEMO_EXPIRED"), "BXMAKER_" . "DEM" . "O_EXPI" . "R" . "ED");
        }
        if (!$oManager->base()->canActionRight('W')) {
            throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }
        $allPhoto = $req->getPost('all_photo') == '1';
        switch ($req->getPost('method')) {
            case 'export':
                if ((int) $req->getPost('export_id') <= 0) {
                    throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_EXPORT_ID'), 'AJAX_ERROR_EXPORT_ID');
                }
                // парамтеры ------------------------------------------------
                $bReset = !!$req->get('reset');
                $bStop = !!$req->get('stop');
                $exportId = (int) $req->get('export_id');
                $oState = new \VKapi\Market\State('hand');
                $oExportItem = new \VKapi\Market\Export\Item($exportId);
                $oExportItem->load();
                $oAlbumExport = new \VKapi\Market\Album\Export($oExportItem);
                $oGoodExport = new \VKapi\Market\Good\Export($oExportItem);
                $oPropertyExport = new \VKapi\Market\Property\Export($oExportItem);
                $arSteps = [1 => ['name' => $oMessage->get('AJAX.STEP1'), 'percent' => 0, 'items' => []], 2 => ['name' => $oMessage->get('AJAX.STEP2'), 'percent' => 0, 'items' => []], 3 => ['name' => $oMessage->get('AJAX.STEP3'), 'percent' => 0, 'items' => []], 4 => ['name' => $oMessage->get('AJAX.STEP4'), 'percent' => 0, 'items' => []], 5 => ['name' => $oMessage->get('AJAX.STEP5'), 'percent' => 0, 'items' => []]];
                // определяем текущее состояние
                $stateData = \array_merge(['step' => 1, 'complete' => \false, 'steps' => $arSteps], $oState->get());
                // сброс данных с прошлого ручного экспорта
                if ($stateData['complete'] || $bReset) {
                    $stateData = ['step' => 1, 'complete' => \false, 'steps' => $arSteps];
                    $oAlbumExport->state()->clean();
                    $oGoodExport->state()->clean();
                    $oPropertyExport->state()->clean();
                }
                // если необходимо остановить экспорт -------------------------
                if ($bStop) {
                    $oJsonResponse->setResponse(['repeat' => \false, 'msg' => $oMessage->get('AJAX.EXPORT.STOP')]);
                    // снимаем флаг оставновки автоэкспорта
                    $oVkExportParam->set('AUTO_EXPORT_STOP', 'N');
                    break;
                }
                if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.marke" . "t") === \constant("MODULE_DEMO_E" . "XPIRE" . "" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIR" . "E" . "D"), "BXMAKER" . "_DEMO_EXP" . "" . "I" . "RED");
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
                        $resultExportAlbum = $oAlbumExport->exportRun();
                        $resultExportAlbumData = $resultExportAlbum->getData();
                        if (isset($resultExportAlbumData['steps'])) {
                            $stateData['steps'][2]['percent'] = $oState->calcPercentByData($resultExportAlbumData);
                            $stateData['steps'][2]['items'] = $resultExportAlbumData['steps'];
                            if (\CModule::IncludeModuleEx("vkapi.mar" . "ke" . "" . "t") == \constant("MODUL" . "E_DEMO_EX" . "PIRE" . "" . "D")) {
                                throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET" . ".DEMO_EXPIR" . "" . "ED"), "BX" . "MAKER_DEMO_EXPIR" . "ED");
                            }
                            if ($resultExportAlbumData['complete']) {
                                $stateData['step']++;
                            }
                        }
                        if (\Bitrix\Main\Loader::includeSharewareModule("vk" . "a" . "pi." . "m" . "arke" . "t") === \constant("MODULE" . "_DEM" . "O_EXPIRED")) {
                            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIRED"), "BXMAKER_DEMO_EXPIR" . "E" . "D");
                        }
                        $oJsonResponse->setResponseField('state', $stateData);
                    } elseif ($stateData['step'] == 3) {
                        // экспорт свойств
                        $oPropertyExport->exportRun();
                        $stateData['steps'][3]['items'] = $oPropertyExport->getSteps();
                        $stateData['steps'][3]['percent'] = $oPropertyExport->getPercent();
                        if ($oPropertyExport->isComplete()) {
                            $stateData['step']++;
                        }
                        if (\CModule::IncludeModuleEx("vk" . "api.mark" . "e" . "t") == \constant("MODULE" . "_DE" . "MO_EXPIRED")) {
                            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.M" . "ARKET.DEMO_EXPIRED"), "BXMAKER_DEMO_" . "EXPIRE" . "" . "D");
                        }
                        $oJsonResponse->setResponseField('state', $stateData);
                    } elseif ($stateData['step'] == 4) {
                        if (\CModule::IncludeModuleEx("vkapi" . "." . "mar" . "" . "k" . "" . "et") === \constant("MODULE_DEMO_EXPIRE" . "" . "D")) {
                            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKE" . "T.DEMO_E" . "XP" . "" . "IRED"), "BXMAKE" . "R_" . "DEM" . "O_EXPIRE" . "" . "" . "D");
                        }
                        $resultExportGoods = $oGoodExport->exportRun();
                        $resultExportGoodsData = $resultExportGoods->getData();
                        if (isset($resultExportGoodsData['steps'])) {
                            $stateData['steps'][4]['percent'] = $oState->calcPercentByData($resultExportGoodsData);
                            $stateData['steps'][4]['items'] = $resultExportGoodsData['steps'];
                            if ($resultExportGoodsData['complete']) {
                                $stateData['step']++;
                            }
                        }
                        $oJsonResponse->setResponseField('state', $stateData);
                    } elseif ($stateData['step'] == 5) {
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
$tab = new \CAdminTabControl('edit', [['DIV' => 'edit', 'TAB' => $oMessage->get('TAB.DELETE'), 'ICON' => '', 'TITLE' => '']]);
$APPLICATION->SetTitle($oMessage->get('PAGE_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
?>

    <form action="<?php 
$APPLICATION->GetCurPage();
?>" method="POST" name="vkapi-market-hand-export__form">
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
$oExport->showExportBlockByHand();
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