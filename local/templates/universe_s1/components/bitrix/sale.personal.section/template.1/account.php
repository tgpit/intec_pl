<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

Loader::includeModule('intec.core');

if ($arParams['SHOW_ACCOUNT_PAGE'] !== 'Y') {
	LocalRedirect($arParams['SEF_FOLDER']);
}

if ($arParams['SET_TITLE'] == 'Y') {
	$APPLICATION->SetTitle(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_TITLE_ACCOUNT'));
}

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ACCOUNT'));
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

<?php
if ($arParams['SHOW_ACCOUNT_COMPONENT'] !== 'N') {
	$APPLICATION->IncludeComponent(
		'bitrix:sale.personal.account',
		'',
		[
			'SET_TITLE' => 'N'
		],
		$component
	);
}

if ($arParams['SHOW_ACCOUNT_PAY_COMPONENT'] !== 'N' && $USER->IsAuthorized()) {
	$APPLICATION->IncludeComponent(
		'bitrix:sale.account.pay',
		'',
		[
			'COMPONENT_TEMPLATE' => '.default',
			'REFRESHED_COMPONENT_MODE' => 'Y',
			'ELIMINATED_PAY_SYSTEMS' => $arParams['ACCOUNT_PAYMENT_ELIMINATED_PAY_SYSTEMS'],
			'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
			'PATH_TO_PAYMENT' => $arParams['PATH_TO_PAYMENT'],
			'PERSON_TYPE' => $arParams['ACCOUNT_PAYMENT_PERSON_TYPE'],
			'REDIRECT_TO_CURRENT_PAGE' => 'N',
			'SELL_AMOUNT' => $arParams['ACCOUNT_PAYMENT_SELL_TOTAL'],
			'SELL_CURRENCY' => $arParams['ACCOUNT_PAYMENT_SELL_CURRENCY'],
			'SELL_SHOW_FIXED_VALUES' => $arParams['ACCOUNT_PAYMENT_SELL_SHOW_FIXED_VALUES'],
			'SELL_SHOW_RESULT_SUM' =>  $arParams['ACCOUNT_PAYMENT_SELL_SHOW_RESULT_SUM'],
			'SELL_TOTAL' => $arParams['ACCOUNT_PAYMENT_SELL_TOTAL'],
			'SELL_USER_INPUT' => $arParams['ACCOUNT_PAYMENT_SELL_USER_INPUT'],
			'SELL_VALUES_FROM_VAR' => 'N',
			'SELL_VAR_PRICE_VALUE' => '',
			'SET_TITLE' => 'N',
		],
		$component
	);
}
?>
<?php include(__DIR__.'/parts/script.php') ?>
