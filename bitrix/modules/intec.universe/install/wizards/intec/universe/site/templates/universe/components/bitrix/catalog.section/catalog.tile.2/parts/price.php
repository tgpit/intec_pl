<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php $vPrice = function (&$arPrice) use (&$arVisual, &$arItem, &$arSvg) { ?>
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
                    'o-vertical',
                    'a-h-center',
                    'a-v-'.$arVisual['PRICE']['ALIGN'],
                    'i-5'
                ]
            ]
        ]) ?>
            <div class="intec-grid-item-auto">
                <div class="catalog-section-item-price-discount">
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
            <div class="intec-grid-item-auto">
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