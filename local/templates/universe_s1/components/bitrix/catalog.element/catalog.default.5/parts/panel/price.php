<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

$arPrice = [];

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

?>
<?= Html::beginTag('div', [
    'class' => 'catalog-element-panel-price',
    'data' => [
        'role' => 'price',
        'show' => !empty($arPrice) ? 'true' : 'false',
        'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false',
        'measure' => !empty($arResult['CATALOG_MEASURE_NAME']) ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-element-panel-price-block">
        <div class="catalog-element-panel-price-current catalog-element-panel-price-part">
            <span class="catalog-element-panel-price-current-value" data-role="price.discount">
                <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
            </span>
            <span class="catalog-element-panel-price-current-separator">/</span>
            <span class="catalog-element-panel-price-current-measure" data-role="price.measure">
                <?= !empty($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : null ?>
            </span>
        </div>
        <?php if ($arVisual['PRICE']['DISCOUNT']['OLD']) { ?>
            <div class="catalog-element-panel-price-discount catalog-element-panel-price-part" data-role="price.base">
                <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($arVisual['PRICE']['DISCOUNT']['PERCENT']) { ?>
        <div class="catalog-element-panel-price-percent-container catalog-element-panel-price-block">
            <div class="catalog-element-panel-price-percent catalog-element-panel-price-part">
                <?= Html::tag('div', '-'.$arPrice['PERCENT'].'%', [
                    'class' => [
                        'catalog-element-panel-price-percent-value',
                        'catalog-element-panel-price-percent-part'
                    ],
                    'data-role' => 'price.percent'
                ]) ?>
                <?php if ($arVisual['PRICE']['DISCOUNT']['ECONOMY']) { ?>
                    <?= Html::tag('div', $arPrice['PRINT_DISCOUNT'], [
                        'class' => [
                            'catalog-element-panel-price-percent-difference',
                            'catalog-element-panel-price-percent-part'
                        ],
                        'data-role' => 'price.difference'
                    ]) ?>
                    <?= Html::tag('div', $arSvg['PRICE']['DIFFERENCE'], [
                        'class' => [
                            'catalog-element-panel-price-percent-decoration',
                            'catalog-element-panel-price-percent-part'
                        ]
                    ]) ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>
<?php unset($arPrice) ?>
