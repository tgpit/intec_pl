<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

if (!Loader::includeModule('intec.core'))
    return;

$bBase = false;

if (Loader::includeModule('catalog') && Loader::includeModule('sale'))
    $bBase = true;

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
 * @var Closure $vButtons
 * @var Closure $vCounter
 * @var Closure $dData
 * @var Closure $vImage
 * @var Closure $vPrice
 * @var Closure $vPurchase
 * @var Closure $vQuantity
 * @var Closure $vSku
 * @var Closure $vQuickView
 */
include(__DIR__.'/parts/buttons.php');
include(__DIR__.'/parts/counter.php');
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
        'c-catalog-section-catalog-tile-3'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'columns-mobile' => $arVisual['COLUMNS']['MOBILE'],
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
        <?php foreach ($arResult['ITEMS'] as $arItem) {

            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);

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
                        '550-1' => $arVisual['COLUMNS']['DESKTOP'] >= 2 && $arVisual['COLUMNS']['MOBILE'] == 1,
                        '850-2' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] >= 3,
                        '1200-3' => $arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] >= 4,
                        '720-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] >= 2,
                        '900-1' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] >= 2,
                        '1200-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS']['DESKTOP'] >= 3
                    ]
                ], true),
                'data' => [
                    'id' => $arItem['ID'],
                    'role' => 'item',
                    'products' => 'main',
                    'data' => $sData,
                    'entity' => 'items-row',
                    'expanded' => 'false',
                    'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                    'properties' => !empty($arSkuProps) ? Json::encode($arSkuProps, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
                ]
            ]) ?>
                <div class="catalog-section-item-wrapper">
                    <div class="catalog-section-item-base">
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-section-item-image-block',
                            'style' => 'padding-top:' . $arVisual['IMAGE']['ASPECT_RATIO'] . '%'
                        ]) ?>
                            <div class="catalog-section-item-image-wrap">
                                <?php $vImage($arItem) ?>
                                <?php if ($arResult['QUICK_VIEW']['USE'] && !$arResult['QUICK_VIEW']['DETAIL']) { ?>
                                    <div class="catalog-section-item-quick-view intec-ui-align">
                                        <!--noindex-->
                                        <div class="catalog-section-item-quick-view-button" data-role="quick.view">
                                            <div class="catalog-section-item-quick-view-button-icon">
                                                <i class="intec-ui-icon intec-ui-icon-eye-1"></i>
                                            </div>
                                            <div class="catalog-section-item-quick-view-button-text">
                                                <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_QUICK_VIEW') ?>
                                            </div>
                                        </div>
                                        <!--/noindex-->
                                    </div>
                                <?php } ?>
                            </div>
                            <!--noindex-->
                            <div class="catalog-section-item-marks">
                                <?php $APPLICATION->IncludeComponent(
                                    'intec.universe:main.markers',
                                    'template.1', [
                                        'HIT' => $arItem['DATA']['MARKS']['VALUES']['HIT'] ? 'Y' : 'N',
                                        'NEW' => $arItem['DATA']['MARKS']['VALUES']['NEW'] ? 'Y' : 'N',
                                        'RECOMMEND' => $arItem['DATA']['MARKS']['VALUES']['RECOMMEND'] ? 'Y' : 'N',
                                        'SHARE' => $arItem['DATA']['MARKS']['VALUES']['SHARE'] ? 'Y' : 'N',
                                        'ORIENTATION' => $arVisual['MARKS']['ORIENTATION']
                                    ],
                                    $component,
                                    ['HIDE_ICONS' => 'Y']
                                ) ?>
                            </div>
                            <!--/noindex-->
                            <?php if ($arItem['DATA']['DELAY']['USE'] || $arItem['DATA']['COMPARE']['USE']) { ?>
                                <!--noindex-->
                                <?php $vButtons($arItem) ?>
                                <!--/noindex-->
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                        <!--noindex-->
                        <?php if ($arVisual['VOTE']['SHOW']) { ?>
                            <div class="catalog-section-item-vote" data-align="<?= $arVisual['VOTE']['ALIGN'] ?>">
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:iblock.vote',
                                    'template.1', [
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
                                            4 => '5'
                                        ],
                                        'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                        'CACHE_TIME' => $arParams['CACHE_TIME'],
                                        'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                        'SHOW_RATING' => 'N'
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
                        <div class="catalog-section-item-name" data-align="<?= $arVisual['NAME']['ALIGN'] ?>">
                            <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'class' => [
                                    'intec-cl-text-hover',
                                    'section-item-name',
                                ],
                                'data' => [
                                    'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                                    'id' => $arItem['ID'],
                                ]
                            ]) ?>
                        </div>
                        <?php if ($arItem['DATA']['WEIGHT']['SHOW']) { ?>
                            <?= Html::tag('div', null, [
                                'class' => [
                                    'catalog-section-item-weight',
                                    'intec-cl-text'
                                ],
                                'data' => [
                                    'role' => 'item.weight',
                                    'align' => $arVisual['WEIGHT']['ALIGN']
                                ]
                            ]) ?>
                        <?php } ?>
                        <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                            <div class="catalog-section-item-description" data-align="<?= $arVisual['DESCRIPTION']['ALIGN'] ?>">
                                <?= Html::stripTags($arItem['PREVIEW_TEXT']) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'] && !empty($arSkuProps)) { ?>
                            <!--noindex-->
                            <?php $vSku($arSkuProps) ?>
                            <!--/noindex-->
                        <?php } ?>
                        <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                            <div class="catalog-section-item-timer">
                                <?php include(__DIR__ . '/parts/timer.php'); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arItem['DATA']['PRICE']['SHOW'] || $arItem['DATA']['ACTION'] !== 'none') { ?>
                        <!--noindex-->
                        <div class="catalog-section-item-advanced">
                            <?php if ($arItem['DATA']['PRICE']['SHOW']) {
                                $arPrice = null;

                                if (!empty($arItem['ITEM_PRICES']))
                                    $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

                                $vPrice($arPrice);
                            } ?>
                            <?php if ($arItem['DATA']['ACTION'] !== 'none') { ?>
                                <div class="catalog-section-item-purchase-block intec-grid intec-grid-a-v-center">
                                    <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                                        <div class="catalog-section-item-counter-block intec-grid-item intec-grid-item-shrink-1">
                                            <?php $vCounter($arItem) ?>
                                        </div>
                                    <?php } ?>
                                    <div class="intec-grid-item">
                                        <div class="catalog-section-item-purchase-desktop">
                                            <?php $vPurchase($arItem) ?>
                                        </div>
                                        <div class="catalog-section-item-purchase-mobile">
                                            <?php $vPurchase($arItem, true) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <!--/noindex-->
                    <?php } ?>
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
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_3_LAZY_TEXT') ?>
                </div>
            </div>
        </div>
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
<?= Html::endTag('div') ?>
<?php include(__DIR__.'/parts/script.php') ?>