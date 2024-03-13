<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
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
include(__DIR__.'/parts/purchase.php');
include(__DIR__.'/parts/quantity.php');
include(__DIR__.'/parts/sku.php');
include(__DIR__.'/parts/measure.php');

?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-catalog-list-1'
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
        <?php foreach($arResult['ITEMS'] as $arItem) {

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
                <div class="catalog-section-item-wrapper catalog-section-item-background ">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-between intec-grid-a-h-500-center intec-grid-i-10">
                        <div class="catalog-section-item-image intec-grid-item-auto">
                            <div class="catalog-section-item-image-wrapper">
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
                        </div>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-purchase',
                                'intec-grid-item' => [
                                    'auto',
                                    '850-2',
                                    '500-1'
                                ]
                            ],
                            'data' => [
                                'timer' => $arItem['DATA']['TIMER']['SHOW'] ? 'true' : 'false',
                                'quantity' => $arVisual['TIMER']['QUANTITY']['SHOW'] ? 'true' : 'false'
                            ]
                        ]) ?>
                            <div class="catalog-section-item-purchase-wrapper">
                                <?php if ($arItem['DATA']['TIMER']['SHOW']) { ?>
                                    <div class="catalog-section-item-timer">
                                        <?php include(__DIR__ . '/parts/timer.php'); ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arItem['DATA']['PRICE']['SHOW'])
                                    $vPrice($arItem);
                                ?>
                                <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                                    <!--noindex-->
                                    <div class="catalog-section-item-counter">
                                        <div class="catalog-section-item-counter-wrapper">
                                            <div class="intec-ui intec-ui-control-numeric intec-ui-view-1 intec-ui-scheme-current" data-role="item.counter">
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
                                                        <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_MAX_MESSAGE') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--/noindex-->
                                <?php } ?>
                                <!--noindex-->
                                <?php $vPurchase($arItem) ?>
                                <?php if ($arItem['DATA']['DELAY']['USE'] || $arItem['DATA']['COMPARE']['USE']) { ?>
                                    <div class="catalog-section-item-price-buttons-wrap">
                                        <?php $vButtons($arItem) ?>
                                    </div>
                                <?php } ?>
                                <!--/noindex-->
                            </div>
                        <?= Html::endTag('div')?>
                        <div class="catalog-section-item-content intec-grid-item intec-grid-item-1024-1">
                            <div class="catalog-section-item-name">
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
                                ]) ?>
                            </div>
                            <?php if ($arVisual['VOTE']['SHOW'] || $arItem['DATA']['QUANTITY']['SHOW']) { ?>
                                <!--noindex-->
                                <div class="catalog-section-item-vote-block">
                                    <div class="intec-grid intec-grid-wrap intec-grid-i-15 intec-grid-a-v-center">
                                        <?php if ($arVisual['VOTE']['SHOW']) { ?>
                                            <div class="intec-grid-item intec-grid-item-auto">
                                                <div class="catalog-section-item-vote">
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
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if ($arItem['DATA']['QUANTITY']['SHOW']) { ?>
                                            <div class="intec-grid-item intec-grid-item-auto">
                                                <div class="catalog-section-item-quantity-wrap">
                                                    <?php $vQuantity($arItem) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <!--/noindex-->
                            <?php } ?>
                            <?php if ($arItem['DATA']['MEASURE']['SHOW']) { ?>
                                <div class="catalog-section-item-ratio">
                                    <?php $vMeasure($arItem) ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                <div class="catalog-section-item-description">
                                    <?= TruncateText($arItem['PREVIEW_TEXT'], 250) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['PROPERTIES']['SHOW'] && !empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                <div class="catalog-section-item-properties">
                                    <ul class="catalog-section-item-properties-wrapper intec-ui-mod-simple">
                                        <?php foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty) { ?>
                                            <li>
                                                <span class="intec-cl-text bullet">
                                                    &#x2022;
                                                </span>
                                                <span>
                                                    <?= $arProperty['NAME'].' &#8212; '.(!Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                                        $arProperty['DISPLAY_VALUE'] :
                                                        implode(', ', $arProperty['DISPLAY_VALUE'])
                                                    ) ?>
                                                </span>
                                            </li>
                                        <?php } ?>
                                        <?php unset($arProperty) ?>
                                    </ul>
                                </div>
                            <?php } ?>
                            <?php if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) { ?>
                                <!--noindex-->
                                <?php $vSku($arSkuProps) ?>
                                <!--/noindex-->
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
            <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'middle' && !$bGiftShowed) {
                if ($iGiftPositionInMiddleAfter == $iCounter) {
                    include(__DIR__.'/parts/sale.products.gift.section.php');
                    $bGiftShowed = true;
                }
            } ?>
            <?php $iCounter++ ?>
        <?php } ?>
    <?= Html::endTag('div');?>
    <!-- items-container -->
    <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
        <!--noindex-->
        <?= Html::beginTag('div', [
            'class' => 'catalog-section-more',
            'data-use' => 'show-more-'.$arNavigation['NavNum']
        ]) ?>
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
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_LAZY_TEXT') ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <!--/noindex-->
    <?php } ?>
    <?php if ($arVisual['NAVIGATION']['BOTTOM']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-navigation',
                'catalog-section-navigation-bottom'
            ],
            'data-pagination-num' => $arNavigation['NavNum']
        ]) ?>
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php if ($arVisual['GIFTS']['SHOW'] && $arVisual['GIFTS']['POSITION'] === 'bottom')
        include(__DIR__.'/parts/sale.products.gift.section.php');
    ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div');?>