<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arVisual
 */

$vPrice = function (&$arItem) use (&$arVisual, &$arSvg) {
    $vPriceRange = function (&$arItem, $bOffer = false) {
        if (empty($arItem['ITEM_PRICES']) || Type::isArray($arItem['ITEM_PRICES']) && count($arItem['ITEM_PRICES']) <= 1)
            return;

    ?>
        <div class="catalog-section-item-price-extended-wrap" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <div class="catalog-section-item-price-extended-button intec-cl-border-hover intec-cl-background-hover intec-grid-item-auto" data-role="price.extended.popup.toggle">
                <div class="dots intec-grid intec-grid-a-v-center intec-grid-a-h-center">
                    <i class="dot intec-grid-item-auto"></i>
                    <i class="dot intec-grid-item-auto"></i>
                    <i class="dot intec-grid-item-auto"></i>
                </div>
            </div>
            <div class="catalog-section-item-price-extended" data-role="price.extended.popup.window">
                <div class="catalog-section-item-price-extended-background">
                    <div class="catalog-section-item-price-extended-header intec-grid intec-grid-a-v-center intec-grid-a-h-between">
                        <div class="catalog-section-item-price-extended-title">
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_PRICE_EXTENDED_TITLE') ?>
                        </div>
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-section-item-price-extended-button-close',
                            'data-role' => 'price.extended.popup.close'
                        ]) ?>
                            <i class="fal fa-times"></i>
                        <?= Html::endTag('div') ?>
                    </div>
                    <div class="catalog-section-item-price-extended-items">
                        <?php foreach ($arItem['ITEM_PRICES'] as $arPrice) { ?>
                            <div class="catalog-section-item-price-extended-item intec-grid intec-grid-a-h-between">
                                <div class="background-border"></div>
                                <?php if (!empty($arPrice['QUANTITY_FROM']) && !empty($arPrice['QUANTITY_TO'])) { ?>
                                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_PRICE_EXTENDED_FROM_TO', [
                                        '#FROM#' => $arPrice['QUANTITY_FROM'],
                                        '#TO#' => $arPrice['QUANTITY_TO']
                                    ]), [
                                        'class' => 'catalog-section-item-price-extended-quantity'
                                    ]) ?>
                                <?php } else if (empty($arPrice['QUANTITY_FROM']) && !empty($arPrice['QUANTITY_TO'])) { ?>
                                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_PRICE_EXTENDED_TO', [
                                        '#FROM#' => !empty($arItem['CATALOG_MEASURE_RATIO']) ? $arItem['CATALOG_MEASURE_RATIO'] : '1',
                                        '#TO#' => $arPrice['QUANTITY_TO']
                                    ]), [
                                        'class' => 'catalog-section-item-price-extended-quantity'
                                    ]) ?>
                                <?php } else if (!empty($arPrice['QUANTITY_FROM']) && empty($arPrice['QUANTITY_TO'])) { ?>
                                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_4_PRICE_EXTENDED_FROM', [
                                        '#FROM#' => $arPrice['QUANTITY_FROM']
                                    ]), [
                                        'class' => 'catalog-section-item-price-extended-quantity'
                                    ]) ?>
                                <?php } ?>
                                <div class="catalog-section-item-price-extended-value">
                                    <?= $arPrice['PRINT_PRICE'] ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php

        $arPrice = null;

        if (!empty($arItem['ITEM_PRICES']))
            $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

    ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-section-item-price',
        'data' => [
            'role' => 'item.price',
            'show' => !empty($arPrice),
            'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-item-price-wrapper',
                'intec-grid' => [
                    '',
                    'a-v-center',
                    'wrap',
                    'i-8'
                ]
            ]
        ]) ?>
            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                <div class="catalog-section-item-price-discount intec-grid intec-grid-a-v-center">
                    <?php if (Type::isArray($arItem['ITEM_PRICES']) && count($arItem['ITEM_PRICES']) > 1 && $arVisual['PRICE']['RANGE']) {
                        $vPriceRange($arItem, false);

                        if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) {
                            foreach ($arItem['OFFERS'] as &$arOffer)
                                $vPriceRange($arOffer, true);

                            unset($arOffer);
                        }

                    ?>
                    <?php } ?>
                    <div class="catalog-section-item-price-discount-block intec-grid-item-auto">
                        <span data-role="item.price.discount">
                            <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                        </span>
                        <?php if (!empty($arPrice) && $arVisual['MEASURE']['SHOW'] && !empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                            /
                            <span data-role="item.price.measure">
                                <?= $arItem['CATALOG_MEASURE_NAME'] ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                <div class="catalog-section-item-price-base" data-role="item.price.base">
                    <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['PRICE']['PERCENT']) { ?>
            <div class="catalog-section-item-price-percent-container">
                <div class="catalog-section-item-price-percent">
                    <div class="catalog-section-item-price-percent-value" data-role="price.percent">
                        <?= '-'.$arPrice['PERCENT'].'%' ?>
                    </div>
                    <?php if ($arVisual['PRICE']['ECONOMY']) { ?>
                        <div class="catalog-section-item-price-percent-difference" data-role="price.difference">
                            <?= $arPrice['PRINT_DISCOUNT'] ?>
                        </div>
                        <div class="catalog-section-item-price-percent-decoration">
                            <?= $arSvg['PRICE_DIFFERENCE'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>