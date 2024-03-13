<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

if (!Loader::includeModule('intec.core'))
    return;

if (!Loader::includeModule('intec.cabinet'))
    return;

IntecCabinet::Initialize();

if ($arParams['SHOW_ORDER_PAGE'] !== 'Y') {
	LocalRedirect($arParams['SEF_FOLDER']);
}

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$APPLICATION->SetAdditionalCSS(BX_PERSONAL_ROOT . '/css/intec/style.css', true);
$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ORDERS'), $arResult['PATH_TO_ORDERS']);
$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ORDER_DETAIL', ['#ID#' => $arResult['VARIABLES']['ID']]));
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
	'bitrix:sale.personal.order.cancel',
	'intec.cabinet.order.cancel.1',
	[
		'PATH_TO_LIST' => $arResult['PATH_TO_ORDERS'],
		'PATH_TO_DETAIL' => $arResult['PATH_TO_ORDER_DETAIL'],
		'SET_TITLE' => $arParams['SET_TITLE'],
		'ID' => $arResult['VARIABLES']['ID'],
	],
	$component
) ?>
<?php include(__DIR__.'/parts/script.php') ?>