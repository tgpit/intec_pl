<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

if (empty($arResult['ITEMS']))
    return;

$arNavigation = !empty($arResult['NAV_RESULT']) ? [
    'NavPageCount' => $arResult['NAV_RESULT']->NavPageCount,
    'NavPageNomer' => $arResult['NAV_RESULT']->NavPageNomer,
    'NavNum' => $arResult['NAV_RESULT']->NavNum
] : [
    'NavPageCount' => 1,
    'NavPageNomer' => 1,
    'NavNum' => $this->randString()
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sTemplateContainer = $sTemplateId.'-'.$arNavigation['NavNum'];
$arVisual = $arResult['VISUAL'];
$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];

$iCounter = 1;
$iItemsCount = count($arResult['ITEMS']);
$bGiftShowed = false;
$arSvg = [
    'PRICE_DIFFERENCE' => FileHelper::getFileData(__DIR__.'/svg/price.difference.svg')
];

/**
 * @var Closure $dData(&$arItem)
 * @var Closure $vButtons(&$arItem)
 * @var Closure $vImage(&$arItem)
 * @var Closure $vPrice(&$arItem)
 * @var Closure $vPurchase(&$arItem)
 * @var Closure $vQuantity(&$arItem)
 * @var Closure $vSku($arProperties)
 */
include(__DIR__.'/parts/buttons.php');
include(__DIR__.'/parts/data.php');
include(__DIR__.'/parts/image.php');
include(__DIR__.'/parts/price.php');
include(__DIR__.'/parts/purchase.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/sku.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-catalog-tile-1'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
        'columns-desktop' => $arVisual['COLUMNS']['DESKTOP'],
        'columns-mobile' => $arVisual['COLUMNS']['MOBILE'],
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ]
]) ?>
    <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'top') { ?>
        <?php include(__DIR__.'/parts/sale.products.gift.section.php'); ?>
    <?php } ?>
    <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
        <div class="catalog-section-navigation catalog-section-navigation-top" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        </div>
    <?php } ?>
    <!-- items-container -->
    <?= Html::beginTag('div', [
        'class' => [
            'catalog-section-items',
            'intec-grid' => [
                '',
                'wrap',
                'a-v-stretch',
                'a-h-start'
            ]
        ],
        'data' => [
            'role' => 'items',
            'filtered' => !empty($arResult['OFFERS_FILTERED_APPLY']) ? Json::encode($arResult['OFFERS_FILTERED_APPLY'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
            'entity' => $sTemplateContainer
        ]
    ]) ?>
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
        <?php
            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
            $bOffers = $arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']);
            $bQuantity = $arVisual['QUANTITY']['SHOW'] && ($bOffers || empty($arItem['OFFERS']));
            $bVote = $arVisual['VOTE']['SHOW'];
            $bCounter = $arVisual['COUNTER']['SHOW'] && $arItem['ACTION'] === 'buy';
            $arPrice = null;

            if (!empty($arItem['ITEM_PRICES']))
                $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

            $arSkuProps = [];

            if (!empty($arResult['SKU_PROPS']))
                $arSkuProps = $arResult['SKU_PROPS'];
            else if (!empty($arItem['SKU_PROPS']))
                $arSkuProps = $arItem['SKU_PROPS'];
        ?>
            <?= Html::beginTag('div', [
                'id' => $sAreaId,
                'class' => Html::cssClassFromArray([
                    'catalog-section-item' => true,
                    'intec-grid-item' => [
                        $arVisual['COLUMNS']['DESKTOP'] => true,
                        '450-1' => $arVisual['COLUMNS']['DESKTOP'] < 4 && $arVisual['COLUMNS']['MOBILE'] == 1,
                        '800-2' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                        '1000-3' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3,
                        '700-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                        '720-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                        '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 2,
                        '1200-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] > 3
                    ]
                ], true),
                'data' => [
                    'id' => $arItem['ID'],
                    'role' => 'item',
                    'products' => 'main',
                    'data' => $sData,
                    'expanded' => 'false',
                    'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                    'subscribe' => $arData['subscribe'] ? 'true' : 'false',
                    'entity' => 'items-row',
                    'properties' => !empty($arSkuProps) ? Json::encode($arSkuProps, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
                ]
            ]) ?>
                <div class="catalog-section-item-wrapper">
                    <div class="catalog-section-item-background"></div>
                    <div class="catalog-section-item-content">
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-section-item-image',
                            'style' => 'padding-top:'.$arVisual['IMAGE']['ASPECT_RATIO'].'%'
                        ]) ?>
                            <!--noindex-->
                            <div class="catalog-section-item-image-marks">
                                <?php $APPLICATION->IncludeComponent(
                                    'intec.universe:main.markers',
                                    'template.1', [
                                        'HIT' => $arItem['DATA']['MARKS']['VALUES']['HIT'],
                                        'NEW' => $arItem['DATA']['MARKS']['VALUES']['NEW'],
                                        'RECOMMEND' => $arItem['DATA']['MARKS']['VALUES']['RECOMMEND'],
                                        'SHARE' => $arItem['DATA']['MARKS']['VALUES']['SHARE'],
                                        'ORIENTATION' => 'vertical'
                                    ],
                                    $component,
                                    ['HIDE_ICONS' => 'Y']
                                ) ?>
                            </div>
                            <!--/noindex-->
                            <?php $vImage($arItem) ?>
                            <?php if ($arItem['DATA']['DELAY']['USE'] || $arItem['DATA']['COMPARE']['USE']) { ?>
                                <!--noindex-->
                                <?php $vButtons($arItem) ?>
                                <!--/noindex-->
                            <?php } ?>
                            <?php if ($arResult['QUICK_VIEW']['USE'] && !$arResult['QUICK_VIEW']['DETAIL']) { ?>
                                <!--noindex-->
                                <div class="catalog-section-item-quick-view intec-ui-align">
                                    <div class="catalog-section-item-quick-view-button" data-role="quick.view">
                                        <div class="catalog-section-item-quick-view-button-icon">
                                            <i class="intec-ui-icon intec-ui-icon-eye-1"></i>
                                        </div>
                                        <div class="catalog-section-item-quick-view-button-text">
                                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_QUICK_VIEW') ?>
                                        </div>
                                    </div>
                                </div>
                                <!--/noindex-->
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                        <?php if ($arVisual['VOTE']['SHOW'] || $arItem['DATA']['QUANTITY']['SHOW']) { ?>
                            <!--noindex-->
                            <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                <div class="catalog-section-item-vote">
                                    <?php $APPLICATION->IncludeComponent(
                                        'bitrix:iblock.vote',
                                        'template.1', [
                                            'COMPONENT_TEMPLATE' => 'template.1',
                                            'IBLOCK_TYPE' => $arParams['IBLOCK_TYPE'],
                                            'IBLOCK_ID' => $arParams['IBLOCK_ID'],
                                            'ELEMENT_ID' => $arItem['ID'],
                                            'ELEMENT_CODE' => $arItem['CODE'],
                                            'MAX_VOTE' => '5',
                                            'VOTE_NAMES' => [
                                                0 => '1',
                                                1 => '2',
                                                2 => '3',
                                                3 => '4',
                                                4 => '5',
                                            ],
                                            'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                            'SHOW_RATING' => 'N',
                                            'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                            'CACHE_TIME' => $arParams['CACHE_TIME']
                                        ],
                                        $component,
                                        ['HIDE_ICONS' => 'Y']
                                    ) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arItem['DATA']['QUANTITY']['SHOW']) { ?>
                                <div class="catalog-section-item-quantity-wrap">
                                    <?php $vQuantity($arItem) ?>
                                </div>
                            <?php } ?>
                            <!--/noindex-->
                        <?php } ?>
                        <div class="catalog-section-item-name">
                            <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                'class' => [
                                    'catalog-section-item-name-wrapper',
                                    'intec-cl-text-hover',
                                ],
                                'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'data' => [
                                    'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                                    'id' => $arItem['ID'],
                                ]
                            ]) ?>
                        </div>
                        <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                            <div class="catalog-section-item-timer">
                                <?php include(__DIR__ . '/parts/timer.php'); ?>
                            </div>
                        <?php } ?>
                        <?php if ($arItem['DATA']['COUNTER']['SHOW'] || $arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) { ?>
                            <?php if ($arItem['DATA']['PRICE']['SHOW']) {?>
                                <?= Html::beginTag('div', [
                                    'class' => 'catalog-section-item-price',
                                    'data' => [
                                        'role' => 'item.price',
                                        'show' => !empty($arPrice),
                                        'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <?php $vPrice($arItem) ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => 'catalog-section-item-price',
                                'data' => [
                                    'role' => 'item.price',
                                    'show' => !empty($arPrice),
                                    'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false',
                                    'request' => !$arItem['DATA']['PRICE']['SHOW'] ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'intec-grid' => [
                                            '' => true,
                                            'nowrap' => true,
                                            'i-5' => true,
                                            'a-v-center' => true,
                                            'a-h-center' => $arItem['DATA']['PRICE']['SHOW'],
                                            'a-h-end' => !$arItem['DATA']['PRICE']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?php if ($arItem['DATA']['PRICE']['SHOW']) { ?>
                                        <div class="intec-grid-item">
                                            <?php $vPrice($arItem) ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arItem['DATA']['ACTION'] !== 'none') { ?>
                                        <div class="catalog-section-item-purchase-desktop intec-grid-item-auto">
                                            <!--noindex-->
                                            <?php $vPurchase($arItem) ?>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <div class="catalog-section-item-advanced" data-role="item-advanced">
                            <?php if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) { ?>
                                <!--noindex-->
                                <?php $vSku($arSkuProps) ?>
                                <!--/noindex-->
                            <?php } ?>
                            <!--noindex-->
                            <?php if ($arItem['DATA']['ACTION'] !== 'none') { ?>
                                <div class="catalog-section-item-purchase">
                                    <div class="intec-grid intec-grid-wrap intec-grid-i-5 intec-grid-a-v-center intec-grid-a-h-end">
                                        <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                                            <div class="catalog-section-item-purchase-counter-wrap intec-grid-item">
                                                <div class="catalog-section-item-purchase-counter intec-ui intec-ui-control-numeric intec-ui-view-1 intec-ui-scheme-current" data-role="item.counter">
                                                    <?= Html::tag('a', '-', [
                                                        'class' => 'intec-ui-part-decrement',
                                                        'href' => 'javascript:void(0)',
                                                        'data-type' => 'button',
                                                        'data-action' => 'decrement'
                                                    ]) ?>
                                                    <?= Html::input('text', null, 0, [
                                                        'data-type' => 'input',
                                                        'class' => 'intec-ui-part-input'
                                                    ]) ?>
                                                    <div class="intec-ui-part-increment-wrapper">
                                                        <?= Html::tag('a', '+', [
                                                            'class' => 'intec-ui-part-increment',
                                                            'href' => 'javascript:void(0)',
                                                            'data-type' => 'button',
                                                            'data-action' => 'increment'
                                                        ]) ?>
                                                        <div class="catalog-section-item-purchase-counter-max-message" data-role="max-message">
                                                            <div class="catalog-section-item-purchase-counter-max-message-close" data-role="max-message-close">
                                                                &times;
                                                            </div>
                                                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_MAX_MESSAGE') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arItem['DATA']['COUNTER']['SHOW'] || $arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) { ?>
                                            <div class="catalog-section-item-purchase-desktop intec-grid-item-auto">
                                                <?php $vPurchase($arItem) ?>
                                            </div>
                                        <?php } ?>
                                        <div class="catalog-section-item-purchase-mobile intec-grid-item-1">
                                            <?php $vPurchase($arItem, true) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <!--/noindex-->
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
            <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'middle' && !$bGiftShowed) { ?>
                <?php if ($iItemsCount > $arVisual['COLUMNS']['DESKTOP'] && $arVisual['COLUMNS']['DESKTOP'] == $iCounter ||
                    $iItemsCount <= $arVisual['COLUMNS']['DESKTOP'] && $iItemsCount == $iCounter) { ?>
                    <?php include(__DIR__.'/parts/sale.products.gift.section.php'); ?>
                    <?php $bGiftShowed = true; ?>
                <?php } ?>
            <?php } ?>
            <?php $iCounter++ ?>
        <?php } ?>
    <?= Html::endTag('div') ?>
    <!-- items-container -->
    <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
        <!--noindex-->
        <div class="catalog-section-more" data-use="show-more-<?= $arNavigation['NavNum'] ?>">
            <div class="catalog-section-more-button">
                <div class="catalog-section-more-icon intec-cl-svg">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                        <path d="M16.5059 9.00153L15.0044 10.5015L13.5037 9.00153"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.75562 4.758C5.84237 3.672 7.34312 3 9.00137 3C12.3171 3 15.0051 5.6865 15.0051 9.0015C15.0051 9.4575 14.9496 9.9 14.8536 10.3268" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M1.4939 8.99847L2.9954 7.49847L4.49615 8.99847"  stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M13.2441 13.242C12.1574 14.328 10.6566 15 8.99838 15C5.68263 15 2.99463 12.3135 2.99463 8.99853C2.99463 8.54253 3.05013 8.10003 3.14613 7.67328" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <div class="catalog-section-more-text intec-cl-text">
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_LAZY_TEXT') ?>
                </div>
            </div>
        </div>
        <!--/noindex-->
    <?php } ?>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <div class="catalog-section-navigation catalog-section-navigation-bottom" data-pagination-num="<?= $arNavigation['NavNum'] ?>">
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        </div>
    <?php } ?>
    <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'bottom') { ?>
        <?php include(__DIR__.'/parts/sale.products.gift.section.php'); ?>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>