<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arVisual) { ?>
    <?php $vPriceRange = function (&$arItem, $bOffer = false) {

        if (count($arItem['ITEM_PRICES']) <= 1)
            return;

    ?>
        <div class="widget-item-price-range-items" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>" data-role="price.range">
            <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_RANGE_TITLE', [
                '#MEASURE#' => !empty($arItem['CATALOG_MEASURE_NAME']) ? $arItem['CATALOG_MEASURE_NAME'] : ''
            ]), [
                'class' => 'widget-item-price-range-items-title'
            ]) ?>
            <?php foreach ($arItem['ITEM_PRICES'] as $arPrice) { ?>
                <div class="widget-item-price-range-item intec-grid intec-grid-a-h-between">
                    <?php if (!empty($arPrice['QUANTITY_FROM']) && !empty($arPrice['QUANTITY_TO'])) { ?>
                        <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_RANGE_FROM_TO', [
                            '#FROM#' => $arPrice['QUANTITY_FROM'],
                            '#TO#' => $arPrice['QUANTITY_TO']
                        ]), [
                            'class' => 'widget-item-price-range-quantity'
                        ]) ?>
                    <?php } else if (empty($arPrice['QUANTITY_FROM']) && !empty($arPrice['QUANTITY_TO'])) { ?>
                        <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_RANGE_TO', [
                            '#FROM#' => !empty($arItem['CATALOG_MEASURE_RATIO']) ? $arItem['CATALOG_MEASURE_RATIO'] : '1',
                            '#TO#' => $arPrice['QUANTITY_TO']
                        ]), [
                            'class' => 'widget-item-price-range-quantity'
                        ]) ?>
                    <?php } else if (!empty($arPrice['QUANTITY_FROM']) && empty($arPrice['QUANTITY_TO'])) { ?>
                        <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_RANGE_FROM', [
                            '#FROM#' => $arPrice['QUANTITY_FROM']
                        ]), [
                            'class' => 'widget-item-price-range-quantity'
                        ]) ?>
                    <?php } ?>
                    <div class="widget-item-price-range-value">
                        <?= $arPrice['PRINT_PRICE'] ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php

        $arPrice = null;

        if (!empty($arItem['ITEM_PRICES']))
            $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

    ?>
    <?= Html::beginTag('div', [
        'class' => 'widget-item-price',
        'data' => [
            'role' => 'item.price',
            'show' => !empty($arPrice),
            'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
        ]
    ]) ?>
        <div class="widget-item-price-wrapper">
            <div class="widget-item-price-discount">
                <div class="widget-item-price-discount-wrapper intec-cl-border-hover">
                    <?php if (!$arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) { ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_FORM') ?>
                    <?php } ?>
                    <span data-role="item.price.discount">
                        <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                    </span>
                    <?php

                        if ($arVisual['PRICE']['RANGE']) {
                            $vPriceRange($arItem, false);

                            if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) {
                                foreach ($arItem['OFFERS'] as &$arOffer)
                                    $vPriceRange($arOffer, true);

                                unset($arOffer);
                            }
                        }

                    ?>
                </div>
            </div>
            <div class="widget-item-price-base intec-grid intec-grid-a-v-center">
                <div class="widget-item-price-base-wrapper">
                    <?php if (!$arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) { ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_PRICE_FORM') ?>
                    <?php } ?>
                    <span data-role="item.price.base">
                        <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
                    </span>
                </div>
            </div>
        </div>
        <?php if ($arVisual['PRICE']['PERCENT']) { ?>
            <div class="widget-item-price-percent-container">
                <div class="widget-item-price-percent">
                    <div class="widget-item-price-percent-value" data-role="item.price.percent">
                        <?= '-'.$arPrice['PERCENT'].'%' ?>
                    </div>
                    <?php if ($arVisual['PRICE']['ECONOMY']) { ?>
                        <div class="widget-item-price-percent-difference" data-role="item.price.difference">
                            <?= $arPrice['PRINT_DISCOUNT'] ?>
                        </div>
                        <div class="widget-item-price-percent-decoration">
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/price.difference.svg') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>