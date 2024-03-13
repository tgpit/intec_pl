<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CAllMain $APPLICATION
 */

$GLOBALS['arServicesElementFilter'] = [
    'ID' => $arResult['SERVICES']
];

$sPrefix = 'SERVICES_';

$iLength = StringHelper::length($sPrefix);

$arProperties = [];
$arExcluded = [
    'IBLOCK_ID',
    'IBLOCK_TYPE',
    'SETTINGS_USE',
    'LAZYLOAD_USE'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix))
        continue;

    $sKey = StringHelper::cut($sKey, $iLength);

    if (ArrayHelper::isIn($sKey, $arExcluded))
        continue;

    $arProperties[$sKey] = $sValue;
}

unset($sPrefix, $iLength, $arExcluded, $sKey, $sValue);

$arProperties = ArrayHelper::merge($arProperties, [
    'IBLOCK_TYPE' => $arParams['SERVICES_IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['SERVICES_IBLOCK_ID'],
    'BY_LINK' => 'Y',
    'USE_FILTER' => 'Y',
    'FILTER_NAME' => 'arServicesElementFilter',
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['DETAIL_LAZYLOAD_USE']
]);

?>

<div class="news-detail-content-services">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="news-detail-content-services-header">
                <?= Loc::getMessage('N_PROJECTS_N_D_DEFAULT_SERVICES_HEADER') ?>
            </div>
            <div class="news-detail-content-services-items">
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    'services.tile.5',
                    $arProperties,
                    $component
                ) ?>
            </div>
        </div>
    </div>
</div>

<?php unset($arProperties) ?>