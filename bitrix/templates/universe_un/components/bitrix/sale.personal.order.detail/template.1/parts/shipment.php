<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="sale-personal-order-detail-block" data-role="block" data-block="shipment">
    <div class="sale-personal-order-detail-block-title">
        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item">
                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_TITLE') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                    <?= $arSvg['BLOCK_TOGGLE'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sale-personal-order-detail-block-content" data-role="content">
        <div class="sale-personal-order-detail-block-shipments">
            <?php foreach ($arResult['SHIPMENT'] as $arShipment) { ?>
            <?php
                $arStore = !empty($arShipment['STORE_ID']) && !empty($arResult['DELIVERY']['STORE_LIST'][$arShipment['STORE_ID']]) ?
                    $arResult['DELIVERY']['STORE_LIST'][$arShipment['STORE_ID']] :
                    null
            ?>
                <div class="sale-personal-order-detail-block-shipment">
                    <div class="sale-personal-order-detail-block-shipment-title">
                    <?php
                        echo Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_NAME_1', [
                            '#NUMBER#' => Html::encode($arShipment['ACCOUNT_NUMBER'])
                        ]);

                        if (!empty($arShipment['DATE_DEDUCTED'])) {
                            echo ' ';
                            echo Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_NAME_2', [
                                '#DATE#' => $arShipment['DATE_DEDUCTED']->format($arParams['ACTIVE_DATE_FORMAT'])
                            ]);
                        }

                        echo Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_NAME_3', [
                            '#SUM#' => !empty($arShipment['PRICE_DELIVERY_FORMATED']) ? $arShipment['PRICE_DELIVERY_FORMATED'] : '0'
                        ]);
                    ?>
                    </div>
                    <?php if (!empty($arShipment['DELIVERY_NAME'])) { ?>
                        <div class="sale-personal-order-detail-block-shipment-field">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_DELIVERY', [
                                '#DELIVERY#' => Html::encode($arShipment['DELIVERY_NAME'])
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arShipment['STATUS_NAME'])) { ?>
                        <div class="sale-personal-order-detail-block-shipment-field">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_STATE', [
                                '#STATE#' => Html::encode($arShipment['STATUS_NAME'])
                            ]) ?>
                        </div>
                    <?php } ?>
                    <?php if (!empty($arShipment['TRACKING_NUMBER'])) { ?>
                        <div class="sale-personal-order-detail-block-shipment-field">
                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_TRACKING_NUMBER', [
                                '#TRACKNUM#' => Html::encode($arShipment['TRACKING_NUMBER'])
                            ]) ?>
                        </div>
                        <?php if (!empty($arShipment['TRACKING_URL'])) { ?>
                            <div class="sale-personal-order-detail-block-shipment-field">
                                <?= Html::tag('a', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_TRACKING'), [
                                    'href' => $arShipment['TRACKING_URL'],
                                    'target' => '_blank',
                                    'class' => 'intec-ui-mod-dashed'
                                ]) ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <?php if (!empty($arStore) || !empty($arShipment['ITEMS'])) { ?>
                        <div class="sale-personal-order-detail-block-shipment-buttons">
                            <?= Html::tag('div', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_SWITCH_EXPAND'), [
                                'class' => [
                                    'sale-personal-order-detail-block-shipment' => [
                                        'button',
                                        'expand'
                                    ],
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ]
                            ]) ?>
                            <?= Html::tag('div', Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_SWITCH_COLLAPSE'), [
                                'class' => [
                                    'sale-personal-order-detail-block-shipment' => [
                                        'button',
                                        'collapse'
                                    ],
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'mod-round-2',
                                        'scheme-current'
                                    ]
                                ],
                                'style' => [
                                    'display' => 'none'
                                ]
                            ]) ?>
                        </div>
                        <div class="sale-personal-order-detail-block-shipment-information" style="display: none;">
                            <?php if (!empty($arStore)) { ?>
                                <div class="sale-personal-order-detail-block-shipment-store">
                                    <?php if (!empty($arStore['TITLE'])) { ?>
                                        <div class="sale-personal-order-detail-block-shipment-store-title">
                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_STORE_TITLE', [
                                                '#TITLE#' => Html::encode($arStore['TITLE'])
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                    <div class="sale-personal-order-detail-block-shipment-store-map">
                                        <?php $APPLICATION->IncludeComponent(
                                            'bitrix:map.yandex.view',
                                            '.default',
                                            [
                                                'INIT_MAP_TYPE' => 'COORDINATES',
                                                'MAP_DATA' => !empty($arStore['GPS_S']) && !empty($arStore['GPS_N']) ? serialize([
                                                    'yandex_lon' => $arStore['GPS_S'],
                                                    'yandex_lat' => $arStore['GPS_N'],
                                                    'PLACEMARKS' => [[
                                                        'LON' => $arStore['GPS_S'],
                                                        'LAT' => $arStore['GPS_N'],
                                                        'TEXT' => Html::encode(!empty($arStore['TITLE']) ? $arStore['TITLE'] : $arStore['ADDRESS'])
                                                    ]]
                                                ]) : null,
                                                'MAP_WIDTH' => '100%',
                                                'MAP_HEIGHT' => '300px',
                                                'OVERLAY' => 'Y',
                                                'CONTROLS' => [
                                                    'ZOOM',
                                                    'SMALLZOOM',
                                                    'SCALELINE'
                                                ],
                                                'OPTIONS' => [
                                                    'ENABLE_DRAGGING',
                                                    'ENABLE_SCROLL_ZOOM',
                                                    'ENABLE_DBLCLICK_ZOOM'
                                                ],
                                                'MAP_ID' => ''
                                            ],
                                            $component
                                        ) ?>
                                    </div>
                                    <?php if (!empty($arStore['ADDRESS'])) { ?>
                                        <div class="sale-personal-order-detail-block-shipment-store-address">
                                            <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_SHIPMENT_STORE_ADDRESS', [
                                                '#ADDRESS#' => Html::encode($arStore['ADDRESS'])
                                            ]) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <?php if (!empty($arShipment['ITEMS'])) { ?>
                                <div class="sale-personal-order-detail-block-shipment-products">
                                    <div class="sale-personal-order-detail-block-products">
                                        <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-header">
                                            <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-6">
                                                <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="picture"></div>
                                                <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="name">
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_NAME') ?>
                                                </div>
                                                <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="price">
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_PRICE') ?>
                                                </div>
                                                <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="quantity">
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_QUANTITY') ?>
                                                </div>
                                                <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="sum">
                                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_SUM') ?>
                                                </div>
                                            </div>
                                        </div>
                                        <?php foreach ($arShipment['ITEMS'] as $arShipmentBasketItem) { ?>
                                        <?php
                                            $arProduct = ArrayHelper::getValue($arResult['BASKET'], $arShipmentBasketItem['BASKET_ID']);

                                            if (empty($arProduct))
                                                continue;
                                        ?>
                                            <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-mobile">
                                                <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-12">
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item-auto">
                                                        <?= Html::beginTag('a', [
                                                            'href' => $arProduct['DETAIL_PAGE_URL'],
                                                            'target' => '_blank',
                                                            'class' => [
                                                                'sale-personal-order-detail-block-product-picture',
                                                                'intec-ui-picture'
                                                            ]
                                                        ]) ?>
                                                        <?= Html::tag('img', '', [
                                                            'src' => !empty($arProduct['PICTURE']) ? $arProduct['PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                                                            'alt' => $arProduct['NAME'],
                                                            'title' => $arProduct['NAME']
                                                        ]) ?>
                                                        <?= Html::endTag('a') ?>
                                                        <div class="sale-personal-order-detail-block-product-item-text" style="text-align: center;">
                                                            <?= $arProduct['QUANTITY'].' '.$arProduct['MEASURE_NAME'] ?>
                                                        </div>
                                                    </div>
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item">
                                                        <?= Html::tag('a', $arProduct['NAME'], [
                                                            'href' => $arProduct['DETAIL_PAGE_URL'],
                                                            'class' => [
                                                                'intec-cl-text',
                                                                'intec-cl-text-light-hover',
                                                                'sale-personal-order-detail-block-product-item-text'
                                                            ],
                                                            'style' => [
                                                                'margin-bottom' => '8px'
                                                            ]
                                                        ]) ?>
                                                        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-4" style="margin-bottom: 4px;">
                                                            <div class="sale-personal-order-detail-block-product-item-text intec-grid-item-3" style="font-weight: 500;">
                                                                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_PRICE') ?>:
                                                            </div>
                                                            <div class="intec-grid-item">
                                                                <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-discount">
                                                                    <?= $arProduct['PRICE_FORMATED'] ?>
                                                                </div>
                                                                <?php if ($arProduct['PRICE_FORMATED'] != $arProduct['BASE_PRICE_FORMATED']) { ?>
                                                                    <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-base">
                                                                        <?= $arProduct['BASE_PRICE_FORMATED'] ?>
                                                                    </div>
                                                                <?php } ?>
                                                            </div>
                                                        </div>
                                                        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-4">
                                                            <div class="sale-personal-order-detail-block-product-item-text intec-grid-item-3" style="font-weight: 500;">
                                                                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_SUM') ?>:
                                                            </div>
                                                            <div class="intec-grid-item">
                                                                <div class="sale-personal-order-detail-block-product-item-text">
                                                                    <?= $arProduct['FORMATED_SUM'] ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-desktop">
                                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-6">
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="picture">
                                                        <?= Html::beginTag('a', [
                                                            'href' => $arProduct['DETAIL_PAGE_URL'],
                                                            'target' => '_blank',
                                                            'class' => [
                                                                'sale-personal-order-detail-block-product-picture',
                                                                'intec-ui-picture'
                                                            ]
                                                        ]) ?>
                                                        <?= Html::tag('img', '', [
                                                            'src' => !empty($arProduct['PICTURE']) ? $arProduct['PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                                                            'alt' => $arProduct['NAME'],
                                                            'title' => $arProduct['NAME']
                                                        ]) ?>
                                                        <?= Html::endTag('a') ?>
                                                    </div>
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="name">
                                                        <?= Html::tag('a', $arProduct['NAME'], [
                                                            'href' => $arProduct['DETAIL_PAGE_URL'],
                                                            'class' => [
                                                                'intec-cl-text',
                                                                'intec-cl-text-light-hover',
                                                                'sale-personal-order-detail-block-product-item-text'
                                                            ]
                                                        ]) ?>
                                                    </div>
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="price">
                                                        <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-discount">
                                                            <?= $arProduct['PRICE_FORMATED'] ?>
                                                        </div>
                                                        <?php if ($arProduct['PRICE_FORMATED'] != $arProduct['BASE_PRICE_FORMATED']) { ?>
                                                            <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-base">
                                                                <?= $arProduct['BASE_PRICE_FORMATED'] ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="quantity">
                                                        <span class="sale-personal-order-detail-block-product-item-text">
                                                            <?= $arProduct['QUANTITY'].' '.$arProduct['MEASURE_NAME'] ?>
                                                        </span>
                                                    </div>
                                                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="sum">
                                                        <span class="sale-personal-order-detail-block-product-item-text">
                                                            <b><?= $arProduct['FORMATED_SUM'] ?></b>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php unset($arProduct, $arShipmentBasketItem) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <?php unset($arStore) ?>
        </div>
    </div>
</div>