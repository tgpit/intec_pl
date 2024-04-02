<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die ();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arParams
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

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

$sTimerPosition = $arVisual['TIMER']['POSITION'];

$iCounter = 1;
$iGiftPositionInMiddleAfter = 1;
$bCloseBorder = false;
$bGiftShowed = false;

if (count($arResult['ITEMS']) > 1)
    $iGiftPositionInMiddleAfter = 2;

if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'middle')
    $bCloseBorder = true;

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
include(__DIR__.'/parts/price.total.php');
include(__DIR__.'/parts/measure.php');
include(__DIR__.'/parts/purchase.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/sku.php');

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-catalog-list-2'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
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
        'class' => 'catalog-section-items',
        'data' => [
            'role' => 'items',
            'filtered' => !empty($arResult['OFFERS_FILTERED_APPLY']) ? Json::encode($arResult['OFFERS_FILTERED_APPLY'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
            'entity' => $sTemplateContainer
        ]
    ]) ?>
        <?php foreach($arResult['ITEMS'] as $arItem) { ?>
        <?php
            $sId = $sTemplateId.'_'.$arItem['ID'];
            $sAreaId = $this->GetEditAreaId($sId);
            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

            $sData = Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true);
            $iPropertiesCounter = 0;

            $arSkuProps = [];

            if (!empty($arResult['SKU_PROPS']))
                $arSkuProps = $arResult['SKU_PROPS'];
            else if (!empty($arItem['SKU_PROPS']))
                $arSkuProps = $arItem['SKU_PROPS'];

        ?>
            <?= Html::beginTag('div', [
                'id' => $sAreaId,
                'class' => 'catalog-section-item',
                'data' => [
                    'id' => $arItem['ID'],
                    'role' => 'item',
                    'products' => 'main',
                    'data' => $sData,
                    'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                    'entity' => 'items-row',
                    'properties' => !empty($arSkuProps) ? Json::encode($arSkuProps, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
                ]
            ]) ?>
                <div class="catalog-section-item-wrapper">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'catalog-section-item-background' => true,
                            'intec-grid' => [
                                '' => true,
                                '768-wrap' => $arVisual['WIDE'],
                                '1000-wrap' => !$arVisual['WIDE']
                            ]
                        ], true)
                    ]) ?>
                        <?php if ($arVisual['IMAGE']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-item-image' => true,
                                    'intec-grid-item' => [
                                        'auto' => true,
                                        '768-1' => $arVisual['WIDE'],
                                        '1000-1' => !$arVisual['WIDE']
                                    ]
                                ], true)
                            ]) ?>
                                <div class="catalog-section-item-image-wrapper">
                                    <?php $vImage($arItem) ?>
                                    <?php if ($arResult['QUICK_VIEW']['USE'] && !$arResult['QUICK_VIEW']['DETAIL']) { ?>
                                        <div class="catalog-section-item-quick-view intec-ui-align">
                                            <!--noindex-->
                                            <div class="catalog-section-item-quick-view-button" data-role="quick.view">
                                                <i class="intec-ui-icon intec-ui-icon-eye-1"></i>
                                            </div>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                </div>
                                <!--noindex-->
                                <div class="catalog-section-item-image-marks">
                                    <?php $APPLICATION->IncludeComponent(
                                        'intec.universe:main.markers',
                                        'template.2', [
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
                            <?= Html::endTag('div');?>
                        <?php } ?>
                        <div class="catalog-section-item-content intec-grid-item">
                            <div class="catalog-section-item-header intec-grid intec-grid-1000-wrap intec-grid-a-h-between intec-grid-a-v-start">
                                <div class="catalog-section-item-name intec-grid-item intec-grid-item-1000-1">
                                    <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                        'class' => [
                                            'catalog-section-item-name-wrapper',
                                            'intec-cl-text-hover'
                                        ],
                                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                        'data' => [
                                            'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                                            'id' => $arItem['ID'],
                                        ]
                                    ])?>
                                    <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                        <div class="catalog-section-item-name-properties intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-4 intec-grid-a-v-center">
                                            <?php foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
                                                if ($iPropertiesCounter >= 2)
                                                    break;
                                            ?>
                                                <div class="catalog-section-item-name-property intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <span class="catalog-section-item-name-property-title">
                                                        <?= $arProperty['NAME'] ?>
                                                    </span>
                                                    <span class="catalog-section-item-name-property-value">
                                                        <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                                            $arProperty['DISPLAY_VALUE'] :
                                                            implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                                                    </span>
                                                </div>
                                                <?php $iPropertiesCounter ++ ?>
                                            <?php } ?>
                                            <?php $iPropertiesCounter = 0 ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="catalog-section-item-header-information intec-grid-item intec-grid-item-1000-1">
                                    <?php if ($arItem['DATA']['QUANTITY']['SHOW']) { ?>
                                        <div class="catalog-section-item-quantity-wrap">
                                            <!--noindex-->
                                            <?php $vQuantity($arItem) ?>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                        <div class="catalog-section-item-vote">
                                            <!--noindex-->
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
                                                        4 => '5',
                                                    ],
                                                    'CACHE_TYPE' => $arParams['CACHE_TYPE'],
                                                    'CACHE_TIME' => $arParams['CACHE_TIME'],
                                                    'DISPLAY_AS_RATING' => $arVisual['VOTE']['MODE'] === 'rating' ? 'rating' : 'vote_avg',
                                                    'SHOW_RATING' => 'N'
                                                ],
                                                $component,
                                                ['HIDE_ICONS' => 'Y']
                                            ) ?>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                <div class="catalog-section-item-description">
                                    <?= $arItem['PREVIEW_TEXT'] ?>
                                </div>
                            <?php } ?>
                            <div class="catalog-section-item-properties-wrap intec-grid intec-grid-1000-wrap intec-grid-a-h-between intec-grid-a-v-start intec-grid-i-8">
                                <div class="catalog-section-item-features intec-grid-item">
                                    <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                        <div class="catalog-section-item-properties intec-grid intec-grid-wrap intec-grid-i-h-16 intec-grid-i-v-10">
                                            <?php foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty) {
                                                $iPropertiesCounter ++;

                                                if ($iPropertiesCounter <= 2)
                                                    continue;

                                            ?>
                                                <div class="catalog-section-item-property intec-grid-item-auto intec-grid-item-shrink-1">
                                                    <div class="catalog-section-item-property-name">
                                                        <?= $arProperty['NAME'] ?>
                                                    </div>
                                                    <div class="catalog-section-item-property-value">
                                                        <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                                            $arProperty['DISPLAY_VALUE'] :
                                                            implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) { ?>
                                        <!--noindex-->
                                        <?php $vSku($arSkuProps) ?>
                                        <!--/noindex-->
                                    <?php } ?>
                                </div>
                                <?php if ($arItem['DATA']['TIMER']['SHOW'] && $sTimerPosition === 'left') { ?>
                                    <div class="catalog-section-item-timer intec-grid-item-auto intec-grid-item-1000-1" data-position="<?= $sTimerPosition ?>">
                                        <?php include(__DIR__ . '/parts/timer.php'); ?>
                                    </div>
                                <?php } ?>
                                <?php if (
                                    $arItem['DATA']['PRICE']['SHOW'] ||
                                    $arItem['DATA']['TIMER']['SHOW'] && (
                                        $sTimerPosition === 'top' ||
                                        $sTimerPosition === 'bottom'
                                    )
                                ) { ?>
                                    <div class="catalog-section-item-price-wrap intec-grid-item-auto intec-grid-item-1000-1">
                                        <?php if ($arItem['DATA']['TIMER']['SHOW'] && $sTimerPosition === 'top') { ?>
                                            <div class="catalog-section-item-timer" data-position="<?= $sTimerPosition ?>">
                                                <?php include(__DIR__ . '/parts/timer.php'); ?>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arItem['DATA']['PRICE']['SHOW'])
                                            $vPrice($arItem);
                                        ?>
                                        <?php if ($arItem['DATA']['TIMER']['SHOW'] && $sTimerPosition === 'bottom') { ?>
                                            <div class="catalog-section-item-timer" data-position="<?= $sTimerPosition ?>">
                                                <?php include(__DIR__ . '/parts/timer.php'); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arItem['DATA']['TIMER']['SHOW'] && $sTimerPosition === 'right') { ?>
                                    <div class="catalog-section-item-timer intec-grid-item-auto intec-grid-item-1000-1" data-position="<?= $sTimerPosition ?>">
                                        <?php include(__DIR__ . '/parts/timer.php'); ?>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php if (
                                $arItem['DATA']['COUNTER']['SHOW'] ||
                                $arItem['DATA']['DELAY']['USE'] ||
                                $arItem['DATA']['COMPARE']['USE'] ||
                                $arItem['DATA']['ACTION'] !== 'none'
                            ) { ?>
                                <div class="catalog-section-item-separator"></div>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'catalog-section-item-purchase-wrap' => true,
                                        'intec-grid' => [
                                            '' => true,
                                            'wrap' => true,
                                            'a-v-center' => true,
                                            'a-h-end' => true,
                                            'a-h-1000-start' => true,
                                            'a-h-400-between' => true,
                                            'i-5' => true
                                        ]
                                    ], true)
                                ]) ?>
                                    <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                                        <?php if ($arItem['DATA']['PRICE']['RECALCULATION'])
                                            $vPriceTotal($arItem);
                                        ?>
                                        <!--noindex-->
                                        <div class="catalog-section-item-counter intec-grid-item-auto intec-grid-item-768-1 intec-grid intec-grid-a-v-center intec-grid-a-h-400-between">
                                            <?php if ($arVisual['MEASURE']['SHOW']) { ?>
                                                <div class="catalog-section-item-ratio">
                                                    <?php $vMeasure($arItem) ?>
                                                </div>
                                            <?php } ?>
                                            <div class="catalog-section-item-counter-wrapper">
                                                <div class="intec-ui intec-ui-control-numeric intec-ui-view-5 intec-ui-size-5 intec-ui-scheme-current" data-role="item.counter">
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

                                                        <div class="catalog-section-item-counter-max-message" data-role="max-message">
                                                            <div class="catalog-section-item-counter-max-message-close" data-role="max-message-close">
                                                                &times;
                                                            </div>
                                                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_2_MAX_MESSAGE') ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!--/noindex-->
                                    <?php } ?>
                                    <?php if ($arItem['DATA']['DELAY']['USE'] || $arItem['DATA']['COMPARE']['USE']) { ?>
                                        <div class="catalog-section-item-price-buttons-wrap intec-grid-item-auto">
                                            <!--noindex-->
                                            <?php $vButtons($arItem) ?>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                    <?php if ($arItem['DATA']['ACTION'] !== 'none') { ?>
                                        <div class="catalog-section-item-purchase-buttons-wrap intec-grid-item-auto">
                                            <!--noindex-->
                                            <?php $vPurchase($arItem) ?>
                                            <!--/noindex-->
                                        </div>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    <?= Html::endTag('div') ?>
                </div>
            <?= Html::endTag('div');?>
            <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'middle' && !$bGiftShowed) { ?>
                <?php if ($iGiftPositionInMiddleAfter == $iCounter) { ?>
                    <?php include(__DIR__.'/parts/sale.products.gift.section.php'); ?>
                    <?php $bGiftShowed = true; ?>
                <?php } ?>
            <?php } ?>
            <?php $iCounter++ ?>
        <?php } ?>
    <?= Html::endTag('div');?>
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
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_2_LAZY_TEXT') ?>
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
<?= Html::endTag('div');?>
