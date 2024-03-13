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

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_PRIVATE'));

if ($arParams['SET_TITLE'] == 'Y') {
	$APPLICATION->SetTitle(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_TITLE_PRIVATE'));
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
    </div>
</div>

<?php $APPLICATION->IncludeComponent(
	'bitrix:main.profile',
	'template.1',
	[
		'SET_TITLE' =>$arParams['SET_TITLE'],
		'AJAX_MODE' => $arParams['AJAX_MODE_PRIVATE'],
		'SEND_INFO' => $arParams['SEND_INFO_PRIVATE'],
		'CHECK_RIGHTS' => $arParams['CHECK_RIGHTS_PRIVATE'],
        'ALL_FIELDS_SHOW' => $arParams['ALL_FIELDS_SHOW'],
        'READ_ONLY' => 'N'
	],
	$component
) ?>
<?php include(__DIR__.'/parts/script.php') ?>
