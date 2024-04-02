<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

Loader::includeModule('intec.core');

if ($arParams['SHOW_PRIVATE_PAGE'] !== 'Y') {
    LocalRedirect($arParams['SEF_FOLDER']);
}

$APPLICATION->AddChainItem(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_CHAIN_PROFILE_ADD'));

if ($arParams['SET_TITLE'] == 'Y') {
    $APPLICATION->SetTitle(Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_TITLE_PROFILE_ADD'));
}

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sPrefix = 'PROFILE_ADD_';
$sTemplate = 'template.' . $arParams[$sPrefix.'TEMPLATE'];
$arProperties = [];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));

    if ($sKey === 'TEMPLATE')
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $sKey, $sValue);

$arProperties['PATH_TO_LIST'] = $arResult['PATH_TO_PROFILE'];

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
        <?php if ($arParams['PROFILE_ADD_USE'] === 'Y') { ?>
            <?php $APPLICATION->IncludeComponent(
                'intec.universe:profile.add',
                $sTemplate,
                $arProperties,
                $component
            ) ?>
        <?php } else { ?>
            <p class="intec-ui intec-ui-control-alert intec-ui-scheme-current intec-ui-m-b-20">
                <?= Loc::getMessage('C_SALE_PERSONAL_SECTION_TEMPLATE_1_TEMPLATE_ERROR') ?>
            </p>
        <?php } ?>
        <?php unset($sTemplate, $arProperties); ?>
    </div>
</div>
