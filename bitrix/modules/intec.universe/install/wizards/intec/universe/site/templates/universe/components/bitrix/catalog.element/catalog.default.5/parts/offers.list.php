<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arData
 * @var array $arVisual
 */

if ($arVisual['STORES']['USE']) {
    $sStorePrefix = 'STORE_';

    $iStoreLength = StringHelper::length($sPrefix);

    $arStoreProperties = [];
    $arStoreExcluded = [
        'POSITION',
        'NAME',
        'PATH',
        'COLUMNS',
        'PICTURE_SHOW',
        'SCHEDULE_SHOW',
        'DESCRIPTION_SHOW'
    ];

    foreach ($arParams as $sKey => $sValue) {
        if (!StringHelper::startsWith($sKey, $sStorePrefix))
            continue;

        $sKey = StringHelper::cut($sKey, $iStoreLength);

        if (ArrayHelper::isIn($sKey, $arStoreExcluded))
            continue;

        $arStoreProperties[$sKey] = $sValue;
    }

    unset($sStorePrefix, $iStoreLength, $arStoreExcluded, $sKey, $sValue);

    $arStoreProperties = ArrayHelper::merge([
        'STORES' => $arParams['STORES'],
        'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
        'IBLOCK_ID' => $arParams['IBLOCK_ID'],
        'ELEMENT_ID' => $arResult['ID'],
        'OFFER_ID' => null,
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
    ], $arStoreProperties);
}

$sDir = __DIR__.'/offers.list/';

/**
 * @var Closure $vButtons(&$arOffer)
 * @var Closure $vCounter()
 * @var Closure $vPrice(&$arOffer)
 * @var Closure $vPriceRange(&$arOffer)
 * @var Closure $vProperties(&$arOffer)
 * @var Closure $vPurchase(&$arOffer)
 * @var Closure $vPurchaseFast(&$arOffer)
 * @var Closure $vQuantity(&$arOffer)
 * @var Closure $vStores(&$arOffer)
 */
$vButtons = include($sDir.'buttons.php');
$vCounter = include($sDir.'counter.php');
$vPrice = include($sDir.'price.php');
$vPriceRange = include($sDir.'price.range.php');
$vProperties = include($sDir.'properties.php');
$vPurchase = include($sDir.'purchase.php');
$vPurchaseFast = include($sDir.'order.fast.php');
$vQuantity = include($sDir.'quantity.php');
$vStores = include($sDir.'stores.php');
$vTimer = include($sDir.'timer.php');
$vMeasuresSelect = include($sDir.'measures.php');

if (empty($arResult['SKU']['NAME']))
    $arResult['SKU']['NAME'] = Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_SKU_NAME_DEFAULT');

?>
<div class="catalog-element-offers-list-container catalog-element-additional-block" data-scroll-end="offers">
    <div class="catalog-element-additional-block-name">
        <?= $arResult['SKU']['NAME'] ?>
    </div>
    <div class="catalog-element-additional-block-content">
        <div class="catalog-element-offers-list" data-role="offers">
            <?php foreach ($arResult['OFFERS'] as &$arOffer) {

                $arOfferData = ArrayHelper::getValue($arData, ['offers', $arOffer['ID']]);

            ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-offers-list-item',
                    'data' => [
                        'role' => 'offer',
                        'offer-data' => Json::encode($arOfferData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
                        'available' => $arOfferData['available'] ? 'true' : 'false',
                        'subscribe' => $arOfferData['subscribe'] ? 'true' : 'false',
                        'stores' => $arVisual['STORES']['USE'] ? 'true' : 'false',
                        'expanded' => 'false'
                    ]
                ]) ?>
                    <div class="catalog-element-offers-list-item-content">
                        <div class="intec-grid intec-grid-wrap intec-grid-i-12">
                            <div class="intec-grid-item intec-grid-item-1024-2 intec-grid-item-650-1">
                                <?php if (!empty($arOffer['NAME'])) { ?>
                                    <div class="catalog-element-offers-list-item-name">
                                        <?= $arOffer['NAME'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['QUANTITY']['SHOW'])
                                    $vQuantity($arOffer);
                                ?>
                                <?php $vProperties($arOffer) ?>
                            </div>
                            <div class="intec-grid-item intec-grid-item-1024-2 intec-grid-item-650-1">
                                <?php if ($arVisual['PRICE']['SHOW'])
                                    $vPrice($arOffer);
                                ?>
                                <?php if ($arVisual['PRICE']['RANGE'])
                                    $vPriceRange($arOffer);
                                ?>
                                <?php if ($arVisual['TIMER']['SHOW'])
                                    $vTimer($arOffer)
                                ?>
                            </div>
                            <?php if ($arResult['ACTION'] !== 'none' || $arResult['COMPARE']['USE']) { ?>
                                <div class="intec-grid-item intec-grid-item-650-1">
                                    <?php if ($arVisual['MEASURES']['USE']) { ?>
                                        <?php $vMeasuresSelect($arOffer) ?>
                                    <?php } ?>
                                    <?php if ($arResult['ACTION'] !== 'none') { ?>
                                        <div class="catalog-element-offers-list-item-buy-block">
                                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-i-4">
                                                <?php if ($arVisual['COUNTER']['SHOW'] && $arOffer['CAN_BUY']) { ?>
                                                    <div class="intec-grid-item-auto">
                                                        <?php $vCounter($arOffer) ?>
                                                    </div>
                                                <?php } ?>
                                                <div class="intec-grid-item">
                                                    <?php $vPurchase($arOffer) ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (
                                            $arResult['COMPARE']['USE'] ||
                                            ($arResult['DELAY']['USE'] && $arOffer['CAN_BUY'] && $arResult['ACTION'] !== 'none') ||
                                            ($arResult['ORDER_FAST']['USE'] && $arOffer['CAN_BUY'] && $arResult['ACTION'] !== 'none')
                                    ) { ?>
                                        <div class="catalog-element-offers-list-item-buy-block">
                                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-i-4">
                                                <?php if ($arResult['COMPARE']['USE'] || $arResult['DELAY']['USE']) { ?>
                                                    <div class="intec-grid-item-auto">
                                                        <?php $vButtons($arOffer) ?>
                                                    </div>
                                                <?php } ?>
                                                <?php if ($arResult['ORDER_FAST']['USE'] && $arOffer['CAN_BUY']) { ?>
                                                    <div class="intec-grid-item">
                                                        <?php $vPurchaseFast() ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                                        <div class="catalog-element-offers-list-item-buy-block-special">
                                            <?php include(__DIR__.'/offers.list/credit.list.php') ?>
                                            <?php include(__DIR__.'/offers.list/delivery.calculation.php') ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($arVisual['STORES']['USE']) { ?>
                            <div class="catalog-element-offers-list-item-stores">
                                <?php $vStores($arOffer) ?>
                            </div>
                        <?php } ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php unset($arOffer) ?>
        </div>
    </div>
</div>
<?php unset($arStoreProperties, $sDir, $vButtons, $vCounter,
    $vPrice, $vPriceRange, $vProperties, $vPurchase,
    $vPurchaseFast, $vQuantity, $vStores
) ?>