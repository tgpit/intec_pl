<?

use \VKapi\Market\Exception\BaseException;
use \VKapi\Market\Group\ClearManager;

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");

\Bitrix\Main\Localization\Loc::loadMessages(__FILE__);

\Bitrix\Main\Loader::includeModule('vkapi.market');


$oManager = \VKapi\Market\Manager::getInstance();

//проверка доступа
$oManager->base()->checkAccess();

$oMessage = new \VKapi\Market\Message($oManager->getModuleId(), 'DELETE');

$oVK = new \VKapi\Market\Connect();
$oConnect = new VKapi\Market\Connect();


$app = \Bitrix\Main\Application::getInstance();
$req = $app->getContext()->getRequest();
$asset = \Bitrix\Main\Page\Asset::getInstance();


//обработка запросов ---
if ($req->isPost() && $req->getPost('method')) {

    $oJsonResponse = new \VKapi\Market\Ajax\JsonResponse();

    try {

//        if (!check_bitrix_sessid('sessid')) {
//            throw new BaseException($oMessage->get('AJAX_ERROR_SESSID'), 'AJAX_ERROR_SESSID');
//        }


                                                                 if(\Bitrix\Main\Loader::includeSharewareModule("vkapi.mar"    .     "ket") === constant("MODULE_"  .     ""  .    "DEMO_EX".  ""  .    "PIRED")){ throw new BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKA"  .   "P".   "I.MARKET" .     ".D"   .     "EM"  .   "O_EXP" .""  .    "".  "IRED"), "BXMAKE".   "R_DEMO_EXPIRED"); }


        if (!$oManager->base()->canActionRight('W')) {
            throw new BaseException($oMessage->get('AJAX_ERROR_ACCESS'), 'AJAX_ERROR_ACCESS');
        }

        $oClearManager = new ClearManager();

        switch ($req->getPost('method')) {
            case 'account':
            {

                if (!$req->getPost('id') || intval($req->getPost('id')) <= 0) {
                    throw new BaseException($oMessage->get('AJAX_ERROR_ACCOUNT_ID'), 'AJAX_ERROR_ACCOUNT_ID');
                }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                    if(\CModule::IncludeModuleEx("vka"  .     "pi.ma"    .    "r"  .    "k".     "e"  .     "t") === constant("M"  .     "ODULE"    ."_DEMO_EX"    .   "PI" .  "RED")){ throw new BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EX"  .   "PIRE"    .    ""     .""    .  "D"), "BXMAK"   .   "ER_DEMO_EXPI"     .  "RED"); }

                $oClearManager->setAccountId($req->getPost('id'));

                $arGroups = $oClearManager->getGroups();

                $oJsonResponse->setResponseField('groups', $arGroups);

                break;
            }
            case 'delete' :
            {

                																																																																																																																																																																																																																																																																														if(\Bitrix\Main\Loader::includeSharewareModule("v"    ."kapi".     ".marke". "".  "t") == constant("MO"    .    "DULE_DEMO_EX"    . "PIR"   .   "E".     ""  .     "" .   "".     "D")){ throw new BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET.DEMO_EXPI"     .  "RED"), "BXMAKER_DEMO_EXPIRED"); }

                if (!$req->getPost('id') || intval($req->getPost('id')) <= 0) {
                    throw new BaseException($oMessage->get('AJAX_ERROR_ACCOUNT_ID'), 'AJAX_ERROR_ACCOUNT_ID');
                }

                if (!$req->getPost('group') || strlen(trim($req->getPost('group'))) <= 0) {
                    throw new BaseException($oMessage->get('AJAX_ERROR_GROUP_ID'), 'AJAX_ERROR_GROUP_ID');
                }

                $oClearManager->setAccountId($req->getPost('id'));
                $oClearManager->setGroupId($req->getPost('group'));


                if ($req->getPost('repeate') && $req->getPost('repeate') == 'false') {
                    $oClearManager->state()->clean();
                }

                $result = $oClearManager->clearGroup();

                $oJsonResponse->setResponse($result->getData());

                break;
            }
            default:
            {
                throw new BaseException($oMessage->get('AJAX_ERROR_UNKNOWN_METHOD'), 'AJAX_ERROR_UNKNOWN_METHOD');
            }
        }

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        if(\CModule::IncludeModuleEx("vkapi.market") == constant("MODU" .     "LE_DEMO_EXP"    .  "I"    .     "RE"   .  ""     . ""   .  "D")){ throw new BaseException(\Bitrix\Main\Localization\Loc::getMessage("VKAPI.MARKET" . ".DEMO_EXPIRED"), "BXMAKE"  ."R_DEMO_EX" .    ""     . "" . "PIRE"   .   "" .   "D"); }

    }
    catch (\Throwable $ex) {
        $oJsonResponse->setException($ex);
    }

    $oJsonResponse->output();
}


$tab = new CAdminTabControl('edit', array(
    array(
        'DIV' => 'edit',
        'TAB' => $oMessage->get('TAB.DELETE'),
        'ICON' => '',
        'TITLE' => '' //$oMessage->get( 'TAB.DELETE')
    ),
));

$APPLICATION->SetTitle($oMessage->get('PAGE_TITLE'));

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

\VKapi\Market\Manager::getInstance()->showAdminPageCssJs();
\VKapi\Market\Manager::getInstance()->showAdminPageMessages();

?>

    <script type="text/javascript">
        BX.message({
            'LOADING': '<?=$oMessage->get('LOADING');?>',
            'MARKET_DELETE_START': '<?=$oMessage->get('MARKET_DELETE_START');?>',
            'ACCOUNT_NOT_FOUND': '<?=$oMessage->get('ACCOUNT_NOT_FOUND');?>'
        });
    </script>

    <form action="<?
    $APPLICATION->GetCurPage() ?>" method="POST" name="vkapi_market_delete_form">
        <?
        echo bitrix_sessid_post(); ?>

        <?
        $tab->Begin(); ?>
        <?
        $tab->BeginNextTab(); ?>

        <tr>
            <td colspan="2">
                <div class="msg_box">

                </div>
            </td>
        </tr>


        <tr>
            <td><?= $oMessage->get('ACCOUNT'); ?></td>
            <td><?= SelectBoxFromArray('account', $oConnect->getAccountsSelectList()); ?></td>
        </tr>


        <tr>
            <td colspan="2">
                <div class="result_box">
                    <div class="account_select_box">

                    </div>
                    <div class="vkapi-market-btn-box">

                        <div class="btn_start btn_type1 btn"><?= $oMessage->get('BTN_START'); ?></div>

                    </div>
                </div>
            </td>
        </tr>


        <?
        $tab->EndTab(); ?>
        <?
        $tab->End(); ?>
    </form>


<?


require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>