<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
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
$arSvg = [
    'BASKET' => FileHelper::getFileData(__DIR__.'/svg/basket.svg'),
    'COMPARE' => FileHelper::getFileData(__DIR__.'/svg/compare.svg'),
    'DELAY' => FileHelper::getFileData(__DIR__.'/svg/delay.svg'),
    'PHONE' => FileHelper::getFileData(__DIR__.'/svg/phone.svg')
];

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$sTemplateContainer = $sTemplateId.'-'.$arNavigation['NavNum'];
$arVisual = $arResult['VISUAL'];
$arVisual['NAVIGATION']['LAZY']['BUTTON'] =
    $arVisual['NAVIGATION']['LAZY']['BUTTON'] &&
    $arNavigation['NavPageNomer'] < $arNavigation['NavPageCount'];
$iPropertiesCounter = 0;

$dData = include(__DIR__.'/parts/data.php');
$vPrice = include(__DIR__.'/parts/price.php');
$vMeasure = include(__DIR__.'/parts/measure.php');
$vButtons = include(__DIR__.'/parts/action.buttons.php');
$vOrder = include(__DIR__.'/parts/order.buttons.php');
$vQuantity = include(__DIR__.'/parts/quantity.php');
$vSku = include(__DIR__.'/parts/sku.php');
$vPurchase = include(__DIR__.'/parts/purchase.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-5'
    ],
    'data' => [
        'properties' => !empty($arResult['SKU_PROPS']) ? Json::encode($arResult['SKU_PROPS'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : '',
        'wide' => $arVisual['WIDE'] ? 'true' : 'false'
    ]
]) ?>
    <?php if ($arVisual['NAVIGATION']['TOP']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-navigation',
                'catalog-section-navigation-top'
            ],
            'data-pagination-num' => $arNavigation['NavNum']
        ]) ?>
            <!-- pagination-container -->
            <?= $arResult['NAV_STRING'] ?>
            <!-- pagination-container -->
        <?= Html::endTag('div') ?>
    <?php } ?>
    <div class="catalog-section-header">
        <div class="catalog-section-header-wrapper intec-grid intec-grid-a-v-center intec-grid-i-h-8">
            <div class="catalog-section-header-name intec-grid-item">
                <div class="catalog-section-header-name-wrapper">
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ITEM_NAME') ?>
                </div>
                <?php if(!empty($arResult['DISPLAY_PROPERTIES'])) { ?>
                    <div class="catalog-section-header-name-properties intec-grid intec-grid-wrap intec-grid-i-h-7 intec-grid-a-v-center">
                        <?php if ($arVisual['PROPERTIES']['AMOUNT'] >= 1) { ?>
                            <div class="catalog-section-header-name-property intec-grid-item-auto">
                                <?= $arResult['DISPLAY_PROPERTIES'][0]['NAME'] ?>
                            </div>
                        <?php } ?>
                        <?php if(!empty($arResult['DISPLAY_PROPERTIES'][1]) && $arVisual['PROPERTIES']['AMOUNT'] >= 2) { ?>
                            <div class="catalog-section-header-name-property intec-grid-item-auto">
                                <?= $arResult['DISPLAY_PROPERTIES'][1]['NAME'] ?>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <div class="catalog-section-header-properties-wrap intec-grid-item">
                <?php if (count($arResult['DISPLAY_PROPERTIES']) > 2 && $arVisual['PROPERTIES']['AMOUNT'] > 2) { ?>
                    <div class="catalog-section-header-properties intec-grid intec-grid-1200-wrap intec-grid-a-v-center intec-grid-i-h-16 intec-grid-i-v-10">
                        <?php foreach($arResult['DISPLAY_PROPERTIES'] as $arProperty) {
                            $iPropertiesCounter ++;
                            if ($iPropertiesCounter <= 2)
                                continue;

                            if ($iPropertiesCounter > $arVisual['PROPERTIES']['AMOUNT'])
                                break;
                            ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-section-header-property' => true,
                                    'intec-grid-item' => [
                                        '4' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                        '3' => $arVisual['PROPERTIES']['COLUMNS'] == 3,
                                        '2' => $arVisual['PROPERTIES']['COLUMNS'] == 2,
                                        '1' => $arVisual['PROPERTIES']['COLUMNS'] == 1,
                                        '1200-3' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                        '1000-2' => $arVisual['PROPERTIES']['COLUMNS'] > 2,
                                    ]
                                ], true)
                            ]) ?>
                                <div class="catalog-section-header-property-name">
                                    <?= $arProperty['NAME'] ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
            <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                <div class="catalog-section-header-quantity-wrap intec-grid-item">
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ITEM_QUANTITY') ?>
                </div>
            <?php } ?>
            <div class="catalog-section-header-price-wrap intec-grid-item">
                <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ITEM_UNIT_PRICE') ?>
            </div>
            <div class="catalog-section-header-buttons-wrap intec-grid-item"></div>
        </div>
    </div>
    <!-- items-container -->
    <?= Html::beginTag('div', [
        'class' => 'catalog-section-items',
        'data-entity' => $sTemplateContainer
    ]) ?>
    <?php foreach($arResult['ITEMS'] as $arItem) {

        $sId = $sTemplateId.'_'.$arItem['ID'];
        $sAreaId = $this->GetEditAreaId($sId);
        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

        $bOffers = $arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']);
        $bQuantity = $arVisual['QUANTITY']['SHOW'] && ($bOffers || empty($arItem['OFFERS']));
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
                'data' => Json::encode($dData($arItem), JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
                'entity' => 'items-row',
                'id' => $arItem['ID'],
                'role' => 'item',
                'available' => $arItem['CAN_BUY'] ? 'true' : 'false',
                'properties' => !empty($arSkuProps) ? Json::encode($arSkuProps, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true) : ''
            ]
        ]) ?>
        <div class="catalog-section-item-background">
            <div class="catalog-section-item-wrapper">
                <div class="catalog-section-item-content">
                    <div class="intec-grid intec-grid-900-wrap intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-8">
                        <div class="catalog-section-item-name intec-grid-item">
                            <?= Html::tag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', $arItem['NAME'], [
                                'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'class' => [
                                    'catalog-section-item-name-wrapper',
                                    'intec-cl-text-hover'
                                ],
                                'data' => [
                                    'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                                ]
                            ])?>
                            <?php if ($arVisual['VOTE']['SHOW']) { ?>
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
                            <?php } ?>
                            <?php if (!empty($arItem['DISPLAY_PROPERTIES'])) { ?>
                                <div class="catalog-section-item-name-properties intec-grid intec-grid-wrap intec-grid-i-h-8 intec-grid-i-v-2 intec-grid-a-v-center">
                                    <?php foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty) {

                                        if ($iPropertiesCounter >= 2)
                                            break;

                                        $iPropertiesCounter ++;

                                        if (empty($arProperty['NAME']) || empty($arProperty['DISPLAY_VALUE']))
                                            continue;

                                    ?>
                                        <div class="catalog-section-item-name-property intec-grid-item-auto">
                                            <span class="catalog-section-item-name-property-name">
                                                <?= $arProperty['NAME'] ?>
                                            </span>
                                            <span class="catalog-section-item-name-property-value">
                                                <?= !Type::isArray($arProperty['DISPLAY_VALUE']) ?
                                                    $arProperty['DISPLAY_VALUE'] :
                                                    implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                                            </span>
                                        </div>
                                    <?php } ?>
                                    <?php $iPropertiesCounter = 0 ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="catalog-section-item-properties-wrap intec-grid-item">
                            <div class="catalog-section-item-properties intec-grid intec-grid-1200-wrap intec-grid-a-v-center intec-grid-i-h-16 intec-grid-i-v-10">
                                <?php foreach($arItem['DISPLAY_PROPERTIES'] as $arProperty) {

                                    $iPropertiesCounter ++;

                                    if ($iPropertiesCounter <= 2)
                                        continue;

                                    if ($iPropertiesCounter > $arVisual['PROPERTIES']['AMOUNT'])
                                        break;

                                ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'catalog-section-item-property' => true,
                                            'intec-grid-item' => [
                                                '4' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                                '3' => $arVisual['PROPERTIES']['COLUMNS'] == 3,
                                                '2' => $arVisual['PROPERTIES']['COLUMNS'] == 2,
                                                '1' => $arVisual['PROPERTIES']['COLUMNS'] == 1,
                                                '1200-3' => $arVisual['PROPERTIES']['COLUMNS'] == 4,
                                                '1000-2' => $arVisual['PROPERTIES']['COLUMNS'] > 2,
                                            ]
                                        ], true)
                                    ]) ?>
                                        <div class="catalog-section-item-property-name">
                                            <?= $arProperty['NAME'] ?>
                                        </div>
                                        <div class="catalog-section-item-property-value">
                                            <?php if (Type::isArray($arProperty['DISPLAY_VALUE'])) { ?>
                                                <?= implode(', ', $arProperty['DISPLAY_VALUE']) ?>
                                            <?php } else { ?>
                                                <?= $arProperty['DISPLAY_VALUE'] ?>
                                            <?php } ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                        <?php if ($arVisual['QUANTITY']['SHOW']) { ?>
                            <div class="catalog-section-item-quantity-wrap intec-grid-item">
                                <?php if ($arItem['DATA']['QUANTITY']['SHOW'])
                                    $vQuantity($arItem);
                                ?>
                            </div>
                        <?php } ?>
                        <div class="catalog-section-item-price-wrap intec-grid-item">
                            <?php if ($arItem['DATA']['PRICE']['SHOW'])
                                $vPrice($arItem);
                            ?>
                        </div>
                        <div class="catalog-section-item-buttons-wrap intec-grid-item">
                            <div class="catalog-section-item-buttons intec-grid intec-grid-wrap intec-grid-i-5">
                                <?php if ($arItem['DATA']['DELAY']['USE'] || $arItem['DATA']['COMPARE']['USE']) { ?>
                                    <div class="catalog-section-item-action-buttons-wrap intec-grid-item intec-grid-item-900-auto">
                                        <!--noindex-->
                                        <?php $vButtons($arItem) ?>
                                        <!--/noindex-->
                                    </div>
                                <?php } ?>
                                <div class="catalog-section-item-order-buttons-wrap intec-grid-item-1 intec-grid-item-900-auto">
                                    <!--noindex-->
                                    <?php $vOrder($arItem) ?>
                                    <!--/noindex-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if ($bOffers) { ?>
                    <div class="catalog-section-item-offers-wrap">
                        <!--noindex-->
                        <?php $vSku($arSkuProps) ?>
                        <!--/noindex-->
                    </div>
                <?php } ?>
            </div>
            <?php if ($arItem['DATA']['ACTION'] === 'buy') { ?>
                <div class="catalog-section-item-additional-wrap" data-role="item.toggle">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'catalog-section-item-additional' => true,
                            'intec-grid' => [
                                '' => true,
                                'wrap' => true,
                                'a-v-center' => true,
                                'a-h-end' => true,
                                'a-h-1000-between' => true,
                                'i-5' => true
                            ]
                        ], true)
                    ]) ?>
                    <?php if ($arItem['DATA']['COUNTER']['SHOW']) { ?>
                        <!--noindex-->
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-counter',
                                'intec-grid' => [
                                    '',
                                    'a-v-center',
                                    'a-h-400-between'
                                ],
                                'intec-grid-item' => [
                                    '',
                                    'auto',
                                    '768-1'
                                ]
                            ]
                        ]) ?>
                            <div class="catalog-section-item-ratio">
                                <?php $vMeasure($arItem) ?>
                            </div>
                            <div class="catalog-section-item-counter-wrapper">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'intec-ui' => [
                                            '',
                                            'control-numeric',
                                            'scheme-current',
                                            'size-5',
                                            'view-5'
                                        ]
                                    ],
                                    'data-role' => 'item.counter'
                                ]) ?>
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
                                    <?= Html::tag('a', '+', [
                                        'class' => 'intec-ui-part-increment',
                                        'href' => 'javascript:void(0)',
                                        'data-type' => 'button',
                                        'data-action' => 'increment'
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?= Html::endTag('div') ?>
                        <!--/noindex-->
                    <?php } ?>
                    <div class="catalog-section-item-purchase-wrap intec-grid-item-auto">
                        <!--noindex-->
                        <?php $vPurchase($arItem) ?>
                        <!--/noindex-->
                    </div>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?= Html::endTag('div') ?>
    <!-- items-container -->
    <?php if ($arVisual['NAVIGATION']['LAZY']['BUTTON']) { ?>
        <!--noindex-->
        <?= Html::beginTag('div', [
            'class' => 'catalog-section-more',
            'data-use' => 'show-more-'.$arNavigation['NavNum']
        ]) ?>
            <div class="catalog-section-more-button">
                <div class="catalog-section-more-icon intec-cl-background">
                    <i class="glyph-icon-show-more"></i>
                </div>
                <div class="catalog-section-more-text intec-cl-text">
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_LAZY_TEXT') ?>
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
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>