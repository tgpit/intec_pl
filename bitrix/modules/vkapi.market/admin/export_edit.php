<?php

use VKapi\Market\Exception\BaseException;
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$oManager = \VKapi\Market\Manager::getInstance();
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'EXPORT_EDIT');
// проверка доступа
$oManager->base()->checkAccess();
$oExport = new \VKapi\Market\Export();
$oAlbumItem = new \VKapi\Market\Album\Item();
$oPhoto = new \VKapi\Market\Export\Photo();
$oConnect = new \VKapi\Market\Connect();
$oConnectTable = new \VKapi\Market\ConnectTable();
$oCondition = new \VKapi\Market\Condition\Manager();
$oCondition->addCondition(new \VKapi\Market\Condition\Group());
$oCondition->addCondition(new \VKapi\Market\Condition\CatalogField());
$oCondition->addCondition(new \VKapi\Market\Condition\IblockElementFieldBase());
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
$bExistsOffers = \false;
// есть оферы
$fname = 'vkapi_market_export_form';
$page_prefix = 'vkapi.market';
$sMenu = new \CAdminContextMenu([["TEXT" => $oMessage->get('TO_LIST'), "LINK" => $oManager->getModuleId() . "_export_list.php?lang=" . \LANG, "TITLE" => $oMessage->get('TO_LIST'), "ICON" => "btn_list"]]);
// сайты ------------------------
$arSite = $oManager->getSiteList();
$arSites = $oManager->getSiteSelectList();
// аккаунты ---------------------------
$arAccounts = $oConnect->getAccountsSelectList();
// инфоблоки
$arIblockItems = $oManager->getIblockItems();
// положение водного знака
$arWatermarkPosition = $oPhoto->getWatermarkPositionSelectList();
// прозрачность водного знака
$arWatermarkOpactity = $oPhoto->getWatermarkOpacitySelectList();
// коэффициент
$arWatermarkKoef = $oPhoto->getWatermarkKoefficientSelectList();
// запрос имеющихся значений -------------
if ($req->get('ID') || (int) $req->get('COPY_ID')) {
    $dbr = $oExport->getTable()->getList(['filter' => ['ID' => (int) $req->get('ID') ?: (int) $req->get('COPY_ID')]]);
    if ($ar = $dbr->fetch()) {
        $arResult = $ar;
    }
}
// AJAX -------------------------------------
if ($req->isPost() && $req->isAjaxRequest() && $req->getPost('method')) {
    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();
    try {
        if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mark" . "" . "e" . "t") == \constant("MODULE_DEMO_EXP" . "I" . "R" . "" . "ED")) {
            throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPIR" . "" . "" . "" . "E" . "D"), "BXMAKER_DE" . "MO_EXPI" . "RED");
        }
        // if (!check_bitrix_sessid('sessid')) {
        // throw new BaseException($oMessage->get('AJAX_ERROR_SESSID'), 'AJAX_ERROR_SESSID');
        // }
        if (!$oManager->base()->canActionRight('W')) {
            throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }
        switch ($req->getPost('method')) {
            // получение аккаунтов пользователей для сайта
            case 'getAccountId':
                if (!$req->getPost('site_id') || \strlen(\trim($req->getPost('site_id'))) <= 0) {
                    throw new \VKapi\Market\Exception\BaseException($oMessage->get('AJAX_ERROR_SITE_ID'), 'AJAX_ERROR_SITE_ID');
                }
                $arRes = [['id' => '', 'name' => $oMessage->get('NO_SELECT')]];
                $dbr = $oConnectTable->getList(['order' => ['NAME' => 'ASC']]);
                while ($ar = $dbr->Fetch()) {
                    $arRes[] = ['id' => $ar['ID'], 'name' => $ar['NAME']];
                }
                $oJsonResponse->setResponseField('count', \count($arRes));
                $oJsonResponse->setResponseField('items', $arRes);
                if (\Bitrix\Main\Loader::includeSharewareModule("vkapi.mark" . "" . "et") == \constant("MODULE_DEMO_EXP" . "IRED")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("V" . "KAPI.M" . "" . "ARKET.DEMO_EXP" . "I" . "RE" . "" . "" . "D"), "BXMA" . "KER_DEMO_" . "EXPI" . "" . "R" . "" . "ED");
                }
                break;
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
            // предпросмотр водного знака
            case "getWatermarkPreview":
                $watermarkId = \intval($arResult['PARAMS']['WATERMARK']);
                $watermarkPosition = \in_array($req->getPost('position'), $arWatermarkPosition['REFERENCE_ID']) ? $req->getPost('position') : $arWatermarkPosition['REFERENCE_ID'][0];
                $watermarkOpacity = \in_array($req->getPost('opactity'), $arWatermarkOpactity['REFERENCE_ID']) ? $req->getPost('opactity') : $arWatermarkOpactity['REFERENCE_ID'][0];
                $watermarkKoef = \in_array($req->getPost('koef'), $arWatermarkKoef['REFERENCE_ID']) ? $req->getPost('koef') : $arWatermarkKoef['REFERENCE_ID'][0];
                $oJsonResponse->setResponse($oManager->getPreviewWatermark($watermarkId, $watermarkPosition, $watermarkOpacity, $watermarkKoef));
                break;
            // условия отбора товаров
            case "getConditions":
                // условия по товарам -----------
                $arIblockIdList = [];
                if (\intval($req->getPost('catalogIblockId'))) {
                    $arIblockIdList[] = \intval($req->getPost('catalogIblockId'));
                }
                if (\intval($req->getPost('offerIblockId'))) {
                    $arIblockIdList[] = \intval($req->getPost('offerIblockId'));
                }
                if (\count($arIblockIdList)) {
                    $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementField(['IBLOCK_ID' => $arIblockIdList]));
                    $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementProperty(['IBLOCK_ID' => $arIblockIdList]));
                }
                $oJsonResponse->setResponseField('conditions', $oCondition->getJsConditionsParams());
                if (\CModule::IncludeModuleEx("vk" . "a" . "pi.mark" . "e" . "" . "" . "t") == \constant("MODULE_" . "DEMO_EXPIRE" . "D")) {
                    throw new \VKapi\Market\Exception\BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MA" . "RKET" . ".DEMO_EX" . "PIR" . "" . "" . "E" . "" . "D"), "BXM" . "AKER_" . "DEM" . "" . "" . "O_EXPIRE" . "D");
                }
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
if ($oManager->base()->canActionRight('W') && ($apply || $save) && \check_bitrix_sessid() && $req->isPost()) {
    do {
        $errors = [];
        $arFields = [];
        $arResult = $req->getPostList()->toArray();
        $result = $oExport->parseExportDataFromPostData();
        if ($result->isSuccess()) {
            $arFields = $result->getData('FIELDS');
            $arResult = \array_merge($arResult, $arFields['PARAMS']);
        } else {
            $errors[] = new \Bitrix\Main\Error($result->getFirstErrorMessage());
        }
        if (empty($errors)) {
            if ($req->get('ID')) {
                $arBaseFields = $oExport->getTable()->getById(\intval($req->get('ID')))->fetch();
                $result = $oExport->getTable()->update(\intval($req->get('ID')), $arFields);
                if ($result->isSuccess()) {
                    // сбрасываем картиник при изменении объединений
                    if ($arBaseFields['PARAMS']['EXTENDED_GOODS'] != $arFields['PARAMS']['EXTENDED_GOODS']) {
                        \VKapi\Market\Export\PhotoTable::deleteAllByGroupId($arFields['GROUP_ID']);
                    } elseif ($arFields['PARAMS']['EXTENDED_GOODS'] != 'Y' && $arBaseFields['PARAMS']['OFFER_COMBINE'] != $arFields['PARAMS']['OFFER_COMBINE']) {
                        \VKapi\Market\Export\PhotoTable::deleteAllByGroupId($arFields['GROUP_ID']);
                    }
                    // если изменилось название или ругие параметры выгрузки подборок
                    $oManager->resetAutoExportState(\intval($req->get('ID')));
                    if ($apply) {
                        \LocalRedirect($APPLICATION->GetCurPageParam('ID=' . \intval($req->get('ID')), ['ID', 'COPY_ID']));
                    } elseif ($save) {
                        \LocalRedirect('/bitrix/admin/' . $page_prefix . '_export_list.php?lang=' . \LANG);
                    }
                } else {
                    $errors = $result->getErrors();
                }
            } else {
                $result = $oExport->getTable()->add($arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        \LocalRedirect($APPLICATION->GetCurPageParam('ID=' . $result->getId(), ['ID', 'COPY_ID']));
                    } elseif ($save) {
                        \LocalRedirect('/bitrix/admin/' . $page_prefix . '_export_list.php?lang=' . \LANG);
                    }
                } else {
                    $errors = $result->getErrors();
                }
            }
        }
    } while (\false);
}
// очистка временной директории с превью картинкаим
$oPhoto->setModePreview(\true);
$oPhoto->deleteTemporaryDirectories();
// условия по товарам -----------
$arIblockIdList = [];
if ((int) $arResult['PARAMS']['CATALOG_IBLOCK_ID']) {
    $arIblockIdList[] = (int) $arResult['PARAMS']['CATALOG_IBLOCK_ID'];
}
if ((int) $arResult['PARAMS']['OFFER_IBLOCK_ID']) {
    $arIblockIdList[] = (int) $arResult['PARAMS']['OFFER_IBLOCK_ID'];
}
if (\count($arIblockIdList)) {
    $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementField(['IBLOCK_ID' => $arIblockIdList]));
    $oCondition->addCondition(new \VKapi\Market\Condition\IblockElementProperty(['IBLOCK_ID' => $arIblockIdList]));
}
$tab = new \CAdminTabControl('edit', [['DIV' => 'edit', 'TAB' => $oMessage->get('TAB.DELETE'), 'ICON' => '', 'TITLE' => '']]);
$APPLICATION->SetTitle($oMessage->get('PAGE_TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
\VKapi\Market\Manager::getInstance()->showErrors($errors);
?>
    <script type="text/javascript">
        BX.message(<?php 
echo \json_encode($oManager->base()->prepareEncoding(['LOADING' => $oMessage->get('LOADING'), 'GROUP_NOT_FOUND' => $oMessage->get('GROUP_NOT_FOUND'), 'ACCOUNT_NOT_FOUND' => $oMessage->get('ACCOUNT_NOT_FOUND'), 'PRICE' => $oMessage->get('PRICE')]));
?>);

        window.VKapiMarketStateJs = new VKapiMarketState(<?php 
echo \Bitrix\Main\Web\Json::encode(['hasCatalog' => $oManager->isInstalledCatalogModule(), 'hasCurrency' => $oManager->isInstalledCurrencyModule(), 'iblockItems' => $oManager->getIblockForJs(), 'iblockPropertyItems' => $oManager->getIblockPropertyForJs(), 'currencyItems' => $oManager->getCurrencyForJs(), 'priceItems' => $oManager->getPricesForJs(), 'userGroupList' => $oManager->getUserGroupListForJs(), 'catalogIblockId' => (int) $arResult['PARAMS']['CATALOG_IBLOCK_ID'], 'offerIblockId' => (int) $arResult['PARAMS']['OFFER_IBLOCK_ID'], 'linkPropertyId' => (int) $arResult['PARAMS']['LINK_PROPERTY_ID'], 'currencyId' => \trim($arResult['PARAMS']['CURRENCY_ID']), 'descriptionDelete' => (array) $arResult['PARAMS']['DESCRIPTION_DELETE'], 'imageToSquare' => (bool) $arResult['PARAMS']['IMAGE_TO_SQUARE'], 'disabledOldAlbumDeleting' => (bool) $arResult['PARAMS']['DISABLED_OLD_ALBUM_DELETING'], 'disabledOldItemDeleting' => (bool) $arResult['PARAMS']['DISABLED_OLD_ITEM_DELETING'], 'productPrice' => \trim($arResult['PARAMS']['PRODUCT_PRICE']), 'productPriceGroups' => empty($arResult['PARAMS']['PRODUCT_PRICE_GROUPS']) ? [2] : $arResult['PARAMS']['PRODUCT_PRICE_GROUPS'], 'productPriceOld' => \trim($arResult['PARAMS']['PRODUCT_PRICE_OLD']), 'productName' => \trim($arResult['PARAMS']['PRODUCT_NAME']), 'productWeight' => \trim($arResult['PARAMS']['PRODUCT_WEIGHT']), 'productLength' => \trim($arResult['PARAMS']['PRODUCT_LENGTH']), 'productHeight' => \trim($arResult['PARAMS']['PRODUCT_HEIGHT']), 'productWidth' => \trim($arResult['PARAMS']['PRODUCT_WIDTH']), 'productQuantity' => \trim($arResult['PARAMS']['PRODUCT_QUANTITY']), 'productPicture' => \trim($arResult['PARAMS']['PRODUCT_PICTURE']), 'productPictureMore' => \trim($arResult['PARAMS']['PRODUCT_PICTURE_MORE']), 'productSku' => \trim($arResult['PARAMS']['PRODUCT_SKU']), 'offerPrice' => \trim($arResult['PARAMS']['OFFER_PRICE']), 'offerPriceGroups' => empty($arResult['PARAMS']['OFFER_PRICE_GROUPS']) ? [2] : $arResult['PARAMS']['OFFER_PRICE_GROUPS'], 'offerPriceOld' => \trim($arResult['PARAMS']['OFFER_PRICE_OLD']), 'offerName' => \trim($arResult['PARAMS']['OFFER_NAME']), 'offerWeight' => \trim($arResult['PARAMS']['OFFER_WEIGHT']), 'offerLength' => \trim($arResult['PARAMS']['OFFER_LENGTH']), 'offerHeight' => \trim($arResult['PARAMS']['OFFER_HEIGHT']), 'offerWidth' => \trim($arResult['PARAMS']['OFFER_WIDTH']), 'offerQuantity' => \trim($arResult['PARAMS']['OFFER_QUANTITY']), 'offerPicture' => \trim($arResult['PARAMS']['OFFER_PICTURE']), 'offerPictureMore' => \trim($arResult['PARAMS']['OFFER_PICTURE_MORE']), 'offerSku' => \trim($arResult['PARAMS']['OFFER_SKU']), 'properties' => (array) $arResult['PARAMS']['PROPERTIES'], 'productNameBaseItems' => $oManager->getPreparedListForJs($oManager->getProductNameBaseList()), 'offerNameBaseItems' => $oManager->getPreparedListForJs($oManager->getOfferNameBaseList()), 'productSkuBaseItems' => $oManager->getPreparedListForJs($oManager->getProductSkuBaseList()), 'offerSkuBaseItems' => $oManager->getPreparedListForJs($oManager->getOfferSkuBaseList()), 'productPictureBaseItems' => $oManager->getPreparedListForJs($oManager->getProductPictureBaseList()), 'offerPictureBaseItems' => $oManager->getPreparedListForJs($oManager->getOfferPictureBaseList()), 'productDefaultText' => (string) $arResult['PARAMS']['PRODUCT_DEFAULT_TEXT'], 'productTemplate' => (string) $arResult['PARAMS']['PRODUCT_TEMPLATE'], 'offerTemplate' => (string) $arResult['PARAMS']['OFFER_TEMPLATE'], 'offerDefaultText' => (string) $arResult['PARAMS']['OFFER_DEFAULT_TEXT'], 'offerTemplateBefore' => (string) $arResult['PARAMS']['OFFER_TEMPLATE_BEFORE'], 'offerTemplateAfter' => (string) $arResult['PARAMS']['OFFER_TEMPLATE_AFTER'], 'offerCombine' => (bool) $arResult['PARAMS']['OFFER_COMBINE'], 'extendedGoods' => (bool) $arResult['PARAMS']['EXTENDED_GOODS'], 'baseTemplatePlaceholderItems' => $oManager->getPreparedListForJs($oManager->getBaseTemplatePlaceholderList()), 'productTemplatePlaceholderItems' => $oManager->getPreparedListForJs($oManager->getProductTemplatePlaceholderList()), 'offerTemplatePlaceholderItems' => $oManager->getPreparedListForJs($oManager->getOfferTemplatePlaceholderList()), 'previewInVkProductId' => $arResult['PARAMS']['PREVIEW_IN_VK_PRODUCT_ID'], 'previewInVkProductName' => $arResult['PARAMS']['PREVIEW_IN_VK_PRODUCT_NAME'], 'previewInVkForProduct' => '', 'previewInVkOfferId' => $arResult['PARAMS']['PREVIEW_IN_VK_OFFER_ID'], 'previewInVkOfferName' => $arResult['PARAMS']['PREVIEW_IN_VK_OFFER_NAME'], 'previewInVkForOffer' => '', 'previewInVkForOfferPreloader' => \false, 'previewInVkForProductPreloader' => \false]);
?>);
    </script>

<?php 
$sMenu->Show();
?>

    <form action="<?php 
$APPLICATION->GetCurPage();
?>" method="POST" name="<?php 
echo $fname;
?>" enctype="multipart/form-data">
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
                <div class="msg_box">

                </div>
            </td>
        </tr>

        <?php 
if (\intval($req->get('ID'))) {
    ?>
            <tr>
                <td>ID</td>
                <td><?php 
    echo $req->get('ID');
    ?></td>
            </tr>
        <?php 
}
?>

        <tr>
            <td><?php 
echo $oMessage->get('NAME');
?><span class="req"></span></td>
            <td><?php 
echo \InputType('text', 'NAME', $arResult['NAME'], '');
?></td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get('ACTIVE');
?></td>
            <td><?php 
echo \InputType('checkbox', 'ACTIVE', 'Y', isset($arResult['ACTIVE']) && !!$arResult['ACTIVE'] ? 'Y' : '');
?></td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get('AUTO');
?></td>
            <td><?php 
echo \InputType('checkbox', 'AUTO', 'Y', isset($arResult['AUTO']) && !!$arResult['AUTO'] ? 'Y' : '');
?></td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get('SITE_ID');
?><span class="req"></span></td>
            <td><?php 
echo \SelectBoxFromArray('SITE_ID', $arSites, isset($arResult['SITE_ID']) ? $arResult['SITE_ID'] : '');
?></td>
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
            <td class="group_id_box">
                <?php 
echo \InputType('text', 'GROUP_ID', isset($arResult['GROUP_ID']) ? $arResult['GROUP_ID'] : '', '', \false, '', ' readonly="readonly" placeholder="' . $oMessage->get('GROUP_ID_PLACEHOLDER') . '" ');
?>
                <?php 
echo \InputType('text', 'GROUP_NAME', isset($arResult['GROUP_NAME']) ? $arResult['GROUP_NAME'] : '', '', \false, '', ' readonly="readonly" placeholder="' . $oMessage->get('GROUP_NAME_PLACEHOLDER') . '" ');
?>
                <div class="result_box">

                </div>
            </td>
        </tr>


        <tr>
            <td><?php 
echo $oMessage->get('CATEGORY_ID');
?><span class="req"></span></td>
            <td><?php 
echo $oAlbumItem->getCategorySelectHtml('CATEGORY_ID', $arResult['PARAMS']['CATEGORY_ID']);
?></td>

        </tr>


        <tr class="heading">
            <td colspan="2">
                <?php 
echo $oMessage->get('WATERMARK_TITLE');
?>
            </td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get('WATERMARK');
?></td>
            <td>
                <?php 
if (\Bitrix\Main\Loader::includeModule('fileman')) {
    echo \CFileInput::Show("WATERMARK", \intval($arResult['PARAMS']['WATERMARK']), ["IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "N"], ['upload' => \true, 'medialib' => \true, 'file_dialog' => \true, 'cloud' => \false, 'del' => \true, 'description' => \false]);
} else {
    echo $oMessage->get('MODULE_FILEMAN_NOT_INSTALLED');
}
?>
            </td>
        </tr>
        <tr>
            <td class="left_td">
                <div><?php 
echo $oMessage->get('WATERMARK_POSITION');
?></div>
                <div><?php 
echo \SelectBoxFromArray('WATERMARK_POSITION', $arWatermarkPosition, isset($arResult['PARAMS']['WATERMARK_POSITION']) ? $arResult['PARAMS']['WATERMARK_POSITION'] : '');
?></div>

                <div><?php 
echo $oMessage->get('WATERMARK_COEFFICIENT');
?></div>
                <div><?php 
echo \SelectBoxFromArray('WATERMARK_COEFFICIENT', $arWatermarkKoef, isset($arResult['PARAMS']['WATERMARK_COEFFICIENT']) ? $arResult['PARAMS']['WATERMARK_COEFFICIENT'] : '');
?></div>

                <div><?php 
echo $oMessage->get('WATERMARK_OPACITY');
?></div>
                <div><?php 
echo \SelectBoxFromArray('WATERMARK_OPACITY', $arWatermarkOpactity, isset($arResult['PARAMS']['WATERMARK_OPACITY']) ? $arResult['PARAMS']['WATERMARK_OPACITY'] : '');
?></div>

            </td>
            <td class="watermark_preview_box">
                <a href="<?php 
echo $oManager->getPreviewPicturePath();
?>?v=1"
                   target="_blank"><?php 
echo $oMessage->get('WATERMARK_PREVIEW');
?></a><br>

                <div class="image_box">
                    <img src='<?php 
echo $oManager->getPreviewPicturePath();
?>?v=1'>
                </div>
            </td>
        </tr>


        <tr class="heading">
            <td colspan="2">
                <?php 
echo $oMessage->get('INFOBLOCK_HEADER');
?>
            </td>
        </tr>
        <tr>
            <td colspan="2">

                <div id="vkapi-market-export-iblock-edit__root"></div>
                <script type="text/javascript">
                    var VKapiMarketExportIblockEditJs = new VKapiMarketExportIblockEdit('vkapi-market-export-iblock-edit__root', window.VKapiMarketStateJs);
                </script>

            </td>
        </tr>

        <tr class="heading">
            <td colspan="2">
                <?php 
echo $oMessage->get('ALBUMS');
?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php 
$arResult['ALBUMS'] = \array_values((array) $arResult['ALBUMS']);
if (!empty($arResult['ALBUMS']) && \is_array($arResult['ALBUMS'][0])) {
    $arResult['ALBUMS'] = [];
}
?>
                <div id="vkapi-market-export-edit-albums"></div>
                <script type="text/javascript">
                    var albumParams = <?php 
echo \Bitrix\Main\Web\Json::encode(['name' => 'ALBUMS', 'values' => $arResult['ALBUMS'], 'items' => $oAlbumItem->getItemsForJs()]);
?>;
                    new VKapiMarketAlbumSelect('vkapi-market-export-edit-albums', albumParams);
                </script>
            </td>
        </tr>


        <tr class="heading">
            <td colspan="2">
                <?php 
echo $oMessage->get('PRODUCT_CONDITION_TITLE');
?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php 
$oCondition->show($arResult['PARAMS']['CONDITIONS']);
?>
            </td>
        </tr>


        <tr class="heading">
            <td colspan="2">
                <?php 
echo $oMessage->get('TEMPLATE_TITLE');
?>
            </td>
        </tr>
        <tr>
            <td colspan="2">
                <?php 
?>
                <div id="vkapi-market-export-template-edit__root"></div>
                <script type="text/javascript">
                    var VKapiMarketExportTemplateEditJS = new VKapiMarketExportTemplateEdit('vkapi-market-export-template-edit__root', window.VKapiMarketStateJs);
                </script>
            </td>
        </tr>


        <?php 
$tab->EndTab();
?>
        <?php 
$tab->Buttons(["disabled" => !$oManager->base()->canActionRight('W')]);
?>
        <?php 
$tab->End();
?>
    </form>


<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";