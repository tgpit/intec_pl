<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
    $APPLICATION->AddChainItem(Html::encode($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
$arManager = $arResult['MANAGER'];
$arTickets = $arResult['TICKETS'];
$arSvg = [
    'PHONE' => FileHelper::getFileData(__DIR__.'/images/phone.svg'),
    'EMAIL' => FileHelper::getFileData(__DIR__.'/images/email.svg'),
    'SOCIAL_VK' => FileHelper::getFileData(__DIR__.'/images/vk.svg'),
    'SOCIAL_FB' => FileHelper::getFileData(__DIR__.'/images/facebook.svg'),
    'SOCIAL_INST' => FileHelper::getFileData(__DIR__.'/images/instagram.svg'),
    'SOCIAL_TW' => FileHelper::getFileData(__DIR__.'/images/twitter.svg'),
    'SOCIAL_SKYPE' => FileHelper::getFileData(__DIR__.'/images/skype.svg')
];

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => Html::cssClassFromArray([
        'ns-bitrix' => true,
        'c-sale-personal-section' => true,
        'c-sale-personal-section-template-1' => true,
    ], true),
    'data' => [
        'role' => 'personal'
    ]
]) ?>
<div class="intec-content intec-content-visible">
    <div class="intec-content-wrapper">
        <div class="sale-personal-section-links-desktop">
            <?php include(__DIR__.'/parts/menu_desktop.php') ?>
        </div>
        <div class="sale-personal-section-links-mobile">
            <?php include(__DIR__.'/parts/menu_mobile.php') ?>
        </div>
        <div class="sale-personal-section-orders-wrap">
            <?php include(__DIR__.'/parts/orders.php') ?>
        </div>
        <div class="sale-personal-section-blocks">
            <div class="intec-grid intec-grid-wrap intec-grid-i-12 intec-grid-a-h-center intec-grid-a-v-start">
                <div class="intec-grid-item-2 intec-grid-item-1024-1">
                    <?php include(__DIR__.'/parts/private.php') ?>
                </div>
                <div class="intec-grid-item-2 intec-grid-item-1024-1">
                    <?php if ($arVisual['MANAGER_BLOCK_SHOW']) { ?>
                        <?php include (__DIR__.'/parts/manager.php') ?>
                    <?php } ?>
                    <?php if ($arVisual['CLAIMS_BLOCK_SHOW']) { ?>
                        <?php include (__DIR__.'/parts/claims.php') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>
