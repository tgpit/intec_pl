<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php return function (&$arOffer) use (&$arVisual, &$arSvg) {

    $arPrice = null;

    if (!empty($arOffer['ITEM_PRICES']))
        $arPrice = ArrayHelper::getFirstValue($arOffer['ITEM_PRICES']);

?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-offers-list-item-price',
        'data' => [
            'role' => 'price',
            'show' => !empty($arPrice) ? 'true' : 'false',
            'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false',
            'measure' => !empty($arOffer['CATALOG_MEASURE_NAME']) ? 'true' : 'false'
        ]
    ]) ?>
        <div class="catalog-element-offers-list-item-price-value-container">
            <div class="catalog-element-offers-list-item-price-value">
                <div class="catalog-element-offers-list-item-price-value-current catalog-element-offers-list-item-price-value-part">
                    <span data-role="price.discount">
                        <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                    </span>
                    <?php if (!empty($arOffer['CATALOG_MEASURE_NAME'])) { ?>
                        <span class="catalog-element-offers-list-item-price-value-current-separator">/</span>
                        <span class="catalog-element-offers-list-item-price-value-current-measure" data-role="price.measure">
                            <?= $arOffer['CATALOG_MEASURE_NAME'] ?>
                        </span>
                    <?php } ?>
                </div>
                <?php if ($arVisual['PRICE']['DISCOUNT']['OLD'] && $arPrice['PERCENT'] > 0) { ?>
                    <div class="catalog-element-offers-list-item-price-value-discount catalog-element-offers-list-item-price-value-part">
                        <span data-role="price.base">
                            <?= $arPrice['PRINT_BASE_PRICE'] ?>
                        </span>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if ($arVisual['PRICE']['DISCOUNT']['PERCENT'] && $arPrice['PERCENT'] > 0) { ?>
            <div class="catalog-element-offers-list-item-price-discount-container">
                <div class="catalog-element-offers-list-item-price-discount">
                    <?= Html::tag('div', '-'.$arPrice['PERCENT'].'%', [
                        'class' => [
                            'catalog-element-offers-list-item-price-discount-percent',
                            'catalog-element-offers-list-item-price-discount-part'
                        ],
                        'data-role' => 'price.percent'
                    ]) ?>
                    <?php if ($arVisual['PRICE']['DISCOUNT']['ECONOMY']) { ?>
                        <?= Html::tag('div', $arPrice['PRINT_DISCOUNT'], [
                            'class' => [
                                'catalog-element-offers-list-item-price-discount-difference',
                                'catalog-element-offers-list-item-price-discount-part'
                            ],
                            'data-role' => 'price.difference'
                        ]) ?>
                        <?= Html::tag('div', $arSvg['PRICE']['DIFFERENCE'], [
                            'class' => [
                                'catalog-element-offers-list-item-price-discount-decoration',
                                'catalog-element-offers-list-item-price-discount-part'
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>