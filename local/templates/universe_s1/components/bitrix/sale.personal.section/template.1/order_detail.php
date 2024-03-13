<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;

Loader::includeModule('intec.core');

if ($arParams['SHOW_ORDER_PAGE'] !== 'Y') {
	LocalRedirect($arParams['SEF_FOLDER']);
}

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ORDERS'), $arResult['PATH_TO_ORDERS']);
$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ORDER_DETAIL', ['#ID#' => urldecode($arResult['VARIABLES']['ID'])]));
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arDetParams = [
    'PATH_TO_LIST' => $arResult['PATH_TO_ORDERS'],
    'PATH_TO_CANCEL' => $arResult['PATH_TO_ORDER_CANCEL'],
    'PATH_TO_COPY' => $arResult['PATH_TO_ORDER_COPY'],
    'PATH_TO_PAYMENT' => $arParams['PATH_TO_PAYMENT'],
    'SET_TITLE' =>$arParams['SET_TITLE'],
    'ID' => $arResult['VARIABLES']['ID'],
    'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
    'ALLOW_INNER' => $arParams['ALLOW_INNER'],
    'ONLY_INNER_FULL' => $arParams['ONLY_INNER_FULL'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'RESTRICT_CHANGE_PAYSYSTEM' => $arParams['ORDER_RESTRICT_CHANGE_PAYSYSTEM'],
    'HIDE_USER_INFO' => $arParams['ORDER_HIDE_USER_INFO'],
    'CUSTOM_SELECT_PROPS' => $arParams['CUSTOM_SELECT_PROPS'],
    'DISALLOW_CANCEL' => $arParams['ORDER_DISALLOW_CANCEL']
];

foreach($arParams as $key => $val) {
	if(strpos($key, 'PROP_') !== false)
		$arDetParams[$key] = $val;
}

if (Loader::includeModule('support')) {
    $arDetParams['PROPERTY_CLAIMS'] = isset($arParams['CLAIMS_FILTER_USER_FIELD']) ? $arParams['CLAIMS_FILTER_USER_FIELD'] : '';
    $arDetParams['PATH_TO_CLAIMS'] = $arResult['ALL_TICKET_URL'];
}

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
	'bitrix:sale.personal.order.detail',
	'template.1',
	$arDetParams,
	$component
) ?>
<?php include(__DIR__.'/parts/script.php') ?>
