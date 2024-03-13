<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Json;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

Loc::loadMessages(__FILE__);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));


if (Loader::includeModule('catalog') &&
    Loader::includeModule('sale') &&
    Loader::includeModule('intec.measures')
)
    $bMeasure = true;

/**
 * @var array $arData
 */
include(__DIR__.'/parts/data.php');

$arVisual = $arResult['VISUAL'];
$arFields = $arResult['FIELDS'];
$arSvg = [
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/gallery.preview.navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/gallery.preview.navigation.right.svg')
    ],
    'SHARES' => FileHelper::getFileData(__DIR__.'/svg/shares.svg'),
    'SIZES' => FileHelper::getFileData(__DIR__.'/svg/sizes.svg'),
    'DELIVERY_CALCULATION' => FileHelper::getFileData(__DIR__.'/svg/delivery.svg'),
    'BUTTONS' => [
        'COMPARE' => FileHelper::getFileData(__DIR__.'/svg/button.action.compare.svg'),
        'DELAY' => FileHelper::getFileData(__DIR__.'/svg/button.action.delay.svg'),
        'BASKET' => FileHelper::getFileData(__DIR__.'/svg/button.action.basket.svg'),
    ],
    'PRICE' => [
        'DIFFERENCE' => FileHelper::getFileData(__DIR__.'/svg/purchase.price.difference.svg'),
        'CHEAPER' => FileHelper::getFileData(__DIR__.'/svg/purchase.cheaper.svg')
    ],
    'STORE' => [
        'LIST' => FileHelper::getFileData(__DIR__.'/svg/store.section.list.svg'),
        'MAP' => FileHelper::getFileData(__DIR__.'/svg/store.section.map.svg')
    ],
    'MEASURES' => [
        'ARROW' => FileHelper::getFileData(__DIR__.'/svg/measures.select.arrow.svg')
    ],
    'PLAY' => FileHelper::getFileData(__DIR__.'/svg/play.svg'),
    'GIF' => FileHelper::getFileData(__DIR__.'/svg/gif.svg')
];

$bOffers = !empty($arResult['OFFERS']);
$bSkuDynamic = $bOffers && $arResult['SKU']['VIEW'] === 'dynamic';
$bSkuList = $bOffers && $arResult['SKU']['VIEW'] === 'list';

$bAdditionalColumn = ($arFields['BRAND']['SHOW'] && $arVisual['BRAND']['ADDITIONAL']['SHOW'] && $arVisual['BRAND']['ADDITIONAL']['POSITION'] === 'column') ||
    ($arFields['DOCUMENTS']['SHOW'] && $arVisual['DOCUMENTS']['POSITION'] === 'column') ||
    ($arFields['RECOMMENDED']['SHOW'] && $arVisual['RECOMMENDED']['POSITION'] === 'column') ||
    ($arFields['ASSOCIATED']['SHOW'] && $arVisual['ASSOCIATED']['POSITION'] === 'column') ||
    ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'column');

if (!$bAdditionalColumn) {
//    $arVisual['INFORMATION']['BUY']['POSITION'] = 'wide';
//    $arVisual['INFORMATION']['PAYMENT']['POSITION'] = 'wide';
    $arVisual['INFORMATION']['SHIPMENT']['POSITION'] = 'wide';
    $arVisual['INFORMATION']['ADDITIONAL_1']['POSITION'] = 'wide';
    $arVisual['INFORMATION']['ADDITIONAL_2']['POSITION'] = 'wide';
}

$bInformation = ($arVisual['INFORMATION']['BUY']['SHOW'] && $arVisual['INFORMATION']['BUY']['POSITION'] === 'wide') ||
    ($arVisual['INFORMATION']['PAYMENT']['SHOW'] && $arVisual['INFORMATION']['PAYMENT']['POSITION'] === 'wide') ||
    ($arVisual['INFORMATION']['SHIPMENT']['SHOW'] && $arVisual['INFORMATION']['SHIPMENT']['POSITION'] === 'wide') ||
    ($arVisual['INFORMATION']['ADDITIONAL_1']['SHOW'] && $arVisual['INFORMATION']['ADDITIONAL_1']['POSITION'] === 'wide') ||
    ($arVisual['INFORMATION']['ADDITIONAL_2']['SHOW'] && $arVisual['INFORMATION']['ADDITIONAL_2']['POSITION'] === 'wide');;

$bRecalculation = false;

if ($bBase && $arVisual['PRICE']['RECALCULATION'])
    $bRecalculation = true;

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-element',
        'c-catalog-element-catalog-default-5'
    ],
    'data' => [
        'data' => Json::encode($arData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'available' => $arData['available'] ? 'true' : 'false',
        'main-view' => $arVisual['MAIN_VIEW']
    ]
]) ?>
    <div class="catalog-element-delimiter"></div>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-body',
        'data' => [
            'role' => 'dynamic',
            'recalculation' => $bRecalculation ? 'true' : 'false'
        ]
    ])?>
        <?php if ($arVisual['PANEL']['MOBILE']['SHOW'] && (!$bOffers || $bSkuDynamic)) { ?>
            <!--noindex-->
            <?php include(__DIR__.'/parts/panel.mobile.php'); ?>
            <!--/noindex-->
        <?php } ?>
        <div class="intec-content intec-content-visible">
            <div class="intec-content-wrapper">
                <div class="catalog-element-main-container">
                    <?php
                        include(__DIR__ . '/parts/main.container.view.'.$arVisual['MAIN_VIEW'].'.php');
                    ?>
                </div>
            </div>
        </div>
    <?= Html::endTag('div') ?>

<?= Html::endTag('div') ?>