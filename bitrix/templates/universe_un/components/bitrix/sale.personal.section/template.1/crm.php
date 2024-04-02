<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

Loader::includeModule('intec.core');

if ($arParams['SHOW_PRIVATE_PAGE'] !== 'Y') {
    LocalRedirect($arParams['SEF_FOLDER']);
}

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_CRM'));

if ($arParams['SET_TITLE'] == 'Y') {
    $APPLICATION->SetTitle(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_TITLE_CRM'));
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
?>

<div class="intec-content intec-content-visible">
    <div class="intec-content-wrapper">
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
            <div class="sale-personal-section-links-desktop">
                <?php include(__DIR__.'/parts/menu_desktop.php') ?>
            </div>
            <div class="sale-personal-section-links-mobile">
                <?php include(__DIR__.'/parts/menu_mobile.php') ?>
            </div>
        <?= Html::endTag('div') ?>
        <?php include(__DIR__.'/parts/script.php') ?>
        <?php if (!empty($arParams['CRM_PATH'])) { ?>
            <?php $APPLICATION->IncludeComponent(
                'intec.universe:main.widget',
                'personal.extranet.1',
                [
                    'CACHE_TIME' => '0',
                    'CACHE_TYPE' => 'A',
                    'PATH_TO_CRM' => $arParams['CRM_PATH'],
                    'COMPONENT_TEMPLATE' => 'personal.extranet.1'
                ],
                $component
            ) ?>
        <?php } else { ?>
            <p class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20">
                <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_ERROR') ?>
            </p>
        <?php } ?>
    </div>
</div>