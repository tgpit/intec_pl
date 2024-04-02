<?php

require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php";
if (!\Bitrix\Main\Loader::includeModule('vkapi.market')) {
    $APPLICATION->AuthForm(\GetMessage("ACCESS_DENIED"));
}
\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);
$oManager = \VKapi\Market\Manager::getInstance();
$oAdmin = new \VKapi\Market\Admin($oManager->getModuleId());
$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'ALBUM_EDIT_PAGE');
$oAlbumItem = new \VKapi\Market\Album\Item();
$oCondition = new \VKapi\Market\Condition\Manager();
$oCondition->addCondition(new \VKapi\Market\Condition\Group());
$oCondition->addCondition(new \VKapi\Market\Condition\CatalogField());
$oCondition->addCondition(new \VKapi\Market\Condition\IblockElementFieldBase());
$oCondition->addCondition(new \VKapi\Market\Condition\IblockElementField());
$oCondition->addCondition(new \VKapi\Market\Condition\IblockElementProperty());
$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();
// проверка доступа
$oManager->base()->checkLevelAccess();
// запрос имеющихся значений -------------
if ($req->get('ID') || $req->get('COPY_ID')) {
    $dbr = $oAlbumItem->table()->getList(array('filter' => array('ID' => \intval($req->get('ID')) ? \intval($req->get('ID')) : \intval($req->get('COPY_ID')))));
    if ($ar = $dbr->fetch()) {
        $arResult = $ar;
        if ($req->get('COPY_ID')) {
            $arResult['PICTURE'] = \null;
        }
    }
}
// save ----------------------------
if ($oManager->base()->canActionRight('W') && ($apply || $save || $save_and_add) && \check_bitrix_sessid() && $req->isPost()) {
    do {
        $errors = array();
        $arFields = array();
        if (\strlen(\trim($req->getPost('NAME'))) <= 0) {
            $errors[] = new \Bitrix\Main\Error($oMessage->get('FIELD_ERROR.NAME'));
            break;
        }
        $arFields['NAME'] = \trim($req->getPost('NAME'));
        if (\strlen(\trim($req->getPost('VK_NAME'))) <= 0) {
            $errors[] = new \Bitrix\Main\Error($oMessage->get('FIELD_ERROR.VK_NAME'));
            break;
        }
        $arFields['VK_NAME'] = \trim($req->getPost('VK_NAME'));
        $pictureId = \intval($arResult['PICTURE']);
        // удаление картинки
        if (isset($_POST['PICTURE_del']) && $_POST['PICTURE_del'] == 'Y' && \intval($arResult['PICTURE'])) {
            \CFile::Delete(\intval($arResult['PICTURE']));
            $pictureId = 0;
        }
        // добавление новой картинки
        if (\intval($_FILES['PICTURE']['size'])) {
            $arType = array('image/png' => 'png', 'image/jpeg' => 'jpeg', 'image/jpg' => 'jpg', 'image/gif' => 'gif');
            $arFile = $_FILES["PICTURE"];
            $arFile["MODULE_ID"] = $oAdmin->getModuleId();
            $arFile["name"] = "album_pickture_" . \randString(15) . "." . $arType[$arFile['type']];
            $pictureId = \CFile::SaveFile($arFile, $oAdmin->getModuleId() . "/album/");
        }
        \CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $oAdmin->getModuleId() . "/");
        \CheckDirPath($_SERVER['DOCUMENT_ROOT'] . '/upload/' . $oAdmin->getModuleId() . "/album/");
        $arFields['PICTURE'] = \intval($pictureId) ?: \null;
        $arFields['PARAMS'] = array('CONDITIONS' => $oCondition->parse(), 'CATEGORY_ID' => \intval($req->getPost('CATEGORY_ID')));
        $arResult = $arFields;
        if (empty($errors)) {
            if ($req->get('ID')) {
                $result = $oAlbumItem->table()->update(\intval($req->get('ID')), $arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        \LocalRedirect($APPLICATION->GetCurPageParam());
                    } elseif ($save) {
                        \LocalRedirect($oAdmin->getFullPageUrl('album_list', array(), array('ID', 'COPY_ID')));
                    } elseif ($save_and_add) {
                        \LocalRedirect($oAdmin->getFullPageUrl('album_edit', array(), array('ID', 'COPY_ID')));
                    }
                } else {
                    $errors = $result->getErrors();
                }
            } else {
                $result = $oAlbumItem->table()->add($arFields);
                if ($result->isSuccess()) {
                    if ($apply) {
                        \LocalRedirect($oAdmin->getFullPageUrl('album_edit', array('ID' => $result->getId()), array('COPY_ID')));
                    } elseif ($save) {
                        \LocalRedirect($oAdmin->getFullPageUrl('album_list', array(), array('ID', 'COPY_ID')));
                    } elseif ($save_and_add) {
                        \LocalRedirect($oAdmin->getFullPageUrl('album_edit', array(), array('ID', 'COPY_ID')));
                    }
                } else {
                    $errors = $result->getErrors();
                }
            }
        }
    } while (\false);
}
// строка меню над списком --
$oMenu = new \CAdminContextMenu(array(array("TEXT" => $oMessage->get('BTN_LIST'), "LINK" => $oAdmin->getPageUrl('album_list', array(), array('ID', 'COPY_ID')), "TITLE" => $oMessage->get('BTN_LIST'), "ICON" => "btn_list")));
$tab = new \CAdminTabControl('edit', array(array('DIV' => 'edit', 'TAB' => $oMessage->get('TAB.MAIN'), 'ICON' => '', 'TITLE' => '')));
$APPLICATION->SetTitle($oMessage->get('TITLE'));
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php";
\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();
if ($errors && \is_array($errors)) {
    $arStr = array();
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
        
        <?php 
if ($req->get('ID')) {
    ?>
            <tr>
                <td>ID:</td>
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
echo $oMessage->get('NAME');
?><span class="req"></span></td>
            <td>
                <?php 
echo \InputType('text', 'NAME', isset($arResult['NAME']) ? \htmlspecialchars($arResult['NAME']) : '', '', \false, '', ' placeholder="' . $oMessage->get('NAME_PLACEHOLDER') . '" ');
?>
            </td>
        </tr>
        <tr>
            <td><?php 
echo $oMessage->get('VK_NAME');
?><span class="req"></span></td>
            <td>
                <?php 
echo \InputType('text', 'VK_NAME', isset($arResult['VK_NAME']) ? \htmlspecialchars($arResult['VK_NAME']) : '', '', \false, '', ' placeholder="' . $oMessage->get('VK_NAME_PLACEHOLDER') . '" ');
?>
            </td>
        </tr>

        <tr>
            <td><?php 
echo $oMessage->get('CATEGORY_ID');
?></td>
            <td>
                <?php 
echo $oAlbumItem->getCategorySelectHtml('CATEGORY_ID', $arResult['PARAMS']['CATEGORY_ID']);
?>
            </td>
        </tr>

        <tr>
            <td>
                <?php 
echo $oMessage->get('PICTURE');
?>
                <div class="vkapi-market-field__help">
                    <?php 
echo $oMessage->get('PICTURE_EXPAND');
?>
                </div>
            </td>
            <td>
                <?php 
if (\Bitrix\Main\Loader::includeModule('fileman')) {
    echo \CFileInput::Show("PICTURE", \intval($arResult['PICTURE']), array("IMAGE" => "Y", "PATH" => "Y", "FILE_SIZE" => "Y", "DIMENSIONS" => "Y", "IMAGE_POPUP" => "N"), array('upload' => \true, 'medialib' => \true, 'file_dialog' => \true, 'cloud' => \false, 'del' => \true, 'description' => \false));
} else {
    echo $oMessage->get('MODULE_FILEMAN_NOT_INSTALLED');
}
?>
            </td>
        </tr>

        <tr class="heading">
            <td colspan="2"><?php 
echo $oMessage->get('CONDITIONS');
?></td>
        </tr>
        <tr class="conditions_box">
            <td colspan="2">
                <?php 
$oCondition->show($arResult['PARAMS']['CONDITIONS']);
?>
            </td>
        </tr>
        
        
        <?php 
$tab->EndTab();
?>
        <?php 
$tab->Buttons(array("disabled" => !$oManager->base()->canActionRight('W'), "btnSaveAndAdd" => $oManager->base()->canActionRight('W')));
?>
        <?php 
$tab->End();
?>
    </form>


<?php 
require_once $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php";