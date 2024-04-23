<?php if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

use intec\core\AdminNotify;
use intec\core\helpers\Html;

/** @global CMain $APPLICATION */

global $APPLICATION;

$APPLICATION->SetAdditionalCSS('/bitrix/gadgets/intec/intec_notify/style.css');

$notification = new AdminNotify();
$expired = $notification->getGeneralExpired();

?>
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
            <?php } else { ?>
                <?= GetMessage('INTEC_GD_NOTIFY_TEXT') ?>
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
