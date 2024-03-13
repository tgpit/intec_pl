<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 * @var array $arSvg
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

$sPrefix = 'STORE_';
$sPrefixMap = 'STOREMAP_';

$iLength = StringHelper::length($sPrefix);
$iLengthMap = StringHelper::length($sPrefixMap);

$arProperties = [];
$arPropertiesMap = [];
$arExcluded = [
    'POSITION',
    'NAME',
    'PATH'
];
$arExcludedMap = [
    'TEMPLATE'
];

foreach ($arParams as $sKey => $sValue) {
    if (!StringHelper::startsWith($sKey, $sPrefix) && !StringHelper::startsWith($sKey, $sPrefixMap))
        continue;

    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut($sKey, $iLength);

        if (ArrayHelper::isIn($sKey, $arExcluded))
            continue;

        $arProperties[$sKey] = $sValue;
    } else if (StringHelper::startsWith($sKey, $sPrefixMap)) {
        $sKey = StringHelper::cut($sKey, $iLengthMap);

        if (ArrayHelper::isIn($sKey, $arExcludedMap))
            continue;

        $arPropertiesMap[$sKey] = $sValue;
    }
}

unset($sPrefix, $sPrefixMap, $iLength, $iLengthMap, $arExcluded, $arExcludedMap, $sKey, $sValue);

$arProperties = ArrayHelper::merge([
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'STORES' => $arParams['STORES'],
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'ELEMENT_ID' => $arResult['ID'],
    'OFFER_ID' => '',
    'STORE_PATH' => $arParams['STORE_PATH'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'MAIN_TITLE' => '',
    'USER_FIELDS' => $arParams['USER_FIELDS'],
    'FIELDS' => $arParams['FIELDS'],
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
    'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
    'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
    'SHOW_GENERAL_STORE_INFORMATION' => 'N',
    'STORE_BLOCK_DESCRIPTION_USE' => $arParams['STORE_BLOCK_DESCRIPTION_USE'],
    'STORE_BLOCK_DESCRIPTION_TEXT' => $arParams['STORE_BLOCK_DESCRIPTION_TEXT']
], $arProperties);

$arPropertiesMap = ArrayHelper::merge([
    'SETTINGS_USE' => $arParams['SETTINGS_USE'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'STORES' => $arParams['STORES'],
    'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['IBLOCK_ID'],
    'ELEMENT_ID' => $arResult['ID'],
    'OFFER_ID' => '',
    'STORE_PATH' => $arParams['STORE_PATH'],
    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
    'CACHE_TIME' => $arParams['CACHE_TIME'],
    'MAIN_TITLE' => '',
    'USER_FIELDS' => $arParams['USER_FIELDS'],
    'FIELDS' => $arParams['FIELDS'],
    'SHOW_EMPTY_STORE' => $arParams['SHOW_EMPTY_STORE'],
    'USE_MIN_AMOUNT' => $arParams['USE_MIN_AMOUNT'],
    'MIN_AMOUNT' => $arParams['MIN_AMOUNT'],
    'SHOW_GENERAL_STORE_INFORMATION' => 'N',
    'STORE_BLOCK_DESCRIPTION_USE' => $arParams['STORE_BLOCK_DESCRIPTION_USE'],
    'STORE_BLOCK_DESCRIPTION_TEXT' => $arParams['STORE_BLOCK_DESCRIPTION_TEXT']
], $arPropertiesMap);

if (empty($arVisual['STORES']['NAME']))
    $arVisual['STORES']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_STORES_NAME_DEFAULT');

?>
<div class="catalog-element-additional-block">
    <div class="catalog-element-additional-block-name">
        <?= $arVisual['STORES']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <div class="catalog-element-stores" data-role="store.section">
            <div class="catalog-element-stores-sections">
                <div class="catalog-element-stores-sections-content">
                    <?= Html::beginTag('div', [
                        'class' => 'catalog-element-stores-sections-item',
                        'data' => [
                            'role' => 'store.section.tab',
                            'store-section-id' => 'list',
                            'store-section-active' => 'true'
                        ]
                    ]) ?>
                        <div class="catalog-element-stores-sections-icon catalog-element-stores-sections-part">
                            <?= $arSvg['STORE']['LIST'] ?>
                        </div>
                        <div class="catalog-element-stores-sections-name catalog-element-stores-sections-part">
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_STORES_SECTION_LIST') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => 'catalog-element-stores-sections-item',
                        'data' => [
                            'role' => 'store.section.tab',
                            'store-section-id' => 'map',
                            'store-section-active' => 'false'
                        ]
                    ]) ?>
                        <div class="catalog-element-stores-sections-icon catalog-element-stores-sections-part">
                            <?= $arSvg['STORE']['MAP'] ?>
                        </div>
                        <div class="catalog-element-stores-sections-name catalog-element-stores-sections-part">
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_STORES_SECTION_MAP') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
            </div>
            <div class="catalog-element-stores-content">
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-stores-content-item',
                    'data' => [
                        'role' => 'store.section.content',
                        'store-section-id' => 'list',
                        'store-section-active' => 'true'
                    ]
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.store.amount',
                        $arVisual['STORES']['TEMPLATE'],
                        $arProperties,
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-stores-content-item',
                    'data' => [
                        'role' => 'store.section.content',
                        'store-section-id' => 'map',
                        'store-section-active' => 'false'
                    ]
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.store.amount',
                        $arVisual['STORES']['MAP_TEMPLATE'],
                        $arPropertiesMap,
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>
<?php unset($arProperties, $arPropertiesMap) ?>