<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

Loader::includeModule('intec.core');

if ($arParams['SHOW_ORDER_PAGE'] !== 'Y') {
	LocalRedirect($arParams['SEF_FOLDER']);
}	

if (strlen($arParams['MAIN_CHAIN_NAME']) > 0) {
	$APPLICATION->AddChainItem(htmlspecialcharsbx($arParams['MAIN_CHAIN_NAME']), $arResult['SEF_FOLDER']);
}

$sPrefix = 'ORDERS_STATUS_COLOR';
$arParameters = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length('ORDERS_'));

    $arParameters[$sKey] = $sValue;
}

unset($sPrefix, $sKey, $sValue);

$arParameters = ArrayHelper::merge([
    'PATH_TO_DETAIL' => $arResult['PATH_TO_ORDER_DETAIL'],
    'PATH_TO_CANCEL' => $arResult['PATH_TO_ORDER_CANCEL'],
    'PATH_TO_CATALOG' => $arParams['PATH_TO_CATALOG'],
    'PATH_TO_COPY' => $arResult['PATH_TO_ORDER_COPY'],
    'PATH_TO_BASKET' => $arParams['PATH_TO_BASKET'],
    'PATH_TO_PAYMENT' => $arParams['PATH_TO_PAYMENT'],
    'SAVE_IN_SESSION' => $arParams['SAVE_IN_SESSION'],
    'ORDERS_PER_PAGE' => $arParams['ORDERS_PER_PAGE'],
    'SET_TITLE' => $arParams['SET_TITLE'],
    'ID' => $arResult['VARIABLES']['ID'],
    'NAV_TEMPLATE' => $arParams['NAV_TEMPLATE'],
    'ACTIVE_DATE_FORMAT' => $arParams['ACTIVE_DATE_FORMAT'],
    'HISTORIC_STATUSES' => $arParams['ORDER_HISTORIC_STATUSES'],
    'ALLOW_INNER' => $arParams['ALLOW_INNER'],
    'ONLY_INNER_FULL' => $arParams['ONLY_INNER_FULL'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'CACHE_GROUPS' => $arParams['CACHE_GROUPS'],
    'DEFAULT_SORT' => $arParams['ORDER_DEFAULT_SORT'],
    'RESTRICT_CHANGE_PAYSYSTEM' => $arParams['ORDER_RESTRICT_CHANGE_PAYSYSTEM'],
    'SEF_MODE' => $arParams['SEF_MODE']
], $arParameters);

$arSortOrder = ['ID', 'STATUS', 'DATE_INSERT', 'PRICE'];

if (!empty($_REQUEST) && isset($_REQUEST['by']))
    $arParameters['DEFAULT_SORT'] = ArrayHelper::fromRange($arSortOrder, $_REQUEST['by']);

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_ORDERS'), $arResult['PATH_TO_ORDERS']);
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
	'bitrix:sale.personal.order.list',
	'template.1',
    $arParameters,
	$component
) ?>
<?php unset($arParameters, $arSortOrder) ?>
<?php include(__DIR__.'/parts/script.php') ?>

