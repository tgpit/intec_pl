<?php require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\AdminNotify;
use intec\Core;

global $APPLICATION;

Loc::loadMessages(__FILE__);

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/classes/general/update_client_partner.php");

if (!CModule::IncludeModule("intec.core") || !class_exists('intec\core\AdminNotify'))
    return;

/** @global CMain $APPLICATION */

global $APPLICATION;

Core::$app->web->css->addFile(Core::getAlias('@intec/core/resources/css/notify.css'));

$notification = new AdminNotify();

if (!$notification->getUse())
    return false;

$expired = $notification->getGeneralExpired();

if (!$expired['HAS'] || $notification->isBannerClose())
    return false;

?>
<div class="intec-banner-notify hidden" id="intecBannerNotify">
    <div class="fragment">
        <div class="fragment-content-wrapper">
            <div class="fragment-title">
                <?php if ($expired['HAS'] && $expired['MAX_STATE'] > 0) { ?>
                    <?= GetMessage('INTEC_GD_NOTIFY_TITLE_EXPIRE') ?>
                <?php } elseif ($expired['HAS'] && $expired['MAX_STATE'] === 0) { ?>
                    <?= GetMessage('INTEC_GD_NOTIFY_TITLE_EXPIRED') ?>
                <?php } else { ?>
                    <?= GetMessage('INTEC_GD_NOTIFY_TITLE') ?>
                <?php } ?>
            </div>
            <div class="fragment-content">
                <?php if ($expired['HAS']) { ?>
                    <?= GetMessage('INTEC_GD_NOTIFY_TEXT_EXPIRE') ?>
                <?php } ?>
            </div>
        </div>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'fragment-button' => true,
                'danger' => $expired['HAS'] && $expired['MAX_STATE'] === 0
            ], true)
        ])  ?>
            <a href="<?= SITE_DIR . '/bitrix/admin/intec_notifyer.php'?>">
                <?= GetMessage('INTEC_GD_NOTIFY_BUTTON') ?>
            </a>
        <?= Html::endTag('div') ?>
        <div class="fragment-lines"></div>
    </div>
    <div class="fragment-close" id="intecCoreBannerClose" title="<?= Loc::getMessage('INTEC_GD_NOTIFY_TITLE_CLOSE') ?>"></div>
    <a class="fragment-gear" href="<?= SITE_DIR . '/bitrix/admin/settings.php?mid=intec.core' ?>" title="<?= Loc::getMessage('INTEC_GD_NOTIFY_TITLE_SETTING') ?>"></a>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const admWorkarea = document.querySelector('#adm-workarea');
            const mainNavchain = document.querySelector('#main_navchain');
            const notify = document.querySelector('#intecBannerNotify');

            if (typeof mainNavchain !== 'undefined' && mainNavchain !== null) {
                mainNavchain.after(notify);
                notify.classList.remove('hidden');
            } else {
                admWorkarea.prepend(notify);
                notify.classList.remove('hidden');
            }

            let time = '<?= time() ?>';
            let cookieKey = '<?= $notification->getCookieKey() ?>';

            document.querySelector('#intecCoreBannerClose').onclick = () => {
                document.cookie = cookieKey + '=' + time;
                document.querySelector('#intecBannerNotify').style.display = "none";
            };

        });
    </script>
</div>
