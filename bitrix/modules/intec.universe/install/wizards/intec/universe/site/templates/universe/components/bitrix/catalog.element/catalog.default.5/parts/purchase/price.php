<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
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
    'class' => [
        'catalog-element-price-container',
        'catalog-element-purchase-block'
    ],
    'data' => [
        'role' => 'price',
        'show' => !empty($arPrice) ? 'true' : 'false',
        'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false',
        'measure' => !empty($arResult['CATALOG_MEASURE_NAME']) ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-element-price">
        <div class="catalog-element-price-current catalog-element-price-part">
            <span class="catalog-element-price-current-value" data-role="price.discount">
                <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
            </span>
            <span class="catalog-element-price-current-separator">/</span>
            <span class="catalog-element-price-current-measure" data-role="price.measure">
                <?= !empty($arResult['CATALOG_MEASURE_NAME']) ? $arResult['CATALOG_MEASURE_NAME'] : null ?>
            </span>
        </div>
        <?php if ($arVisual['PRICE']['DISCOUNT']['OLD']) { ?>
            <div class="catalog-element-price-discount catalog-element-price-part" data-role="price.base">
                <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($arVisual['PRICE']['DISCOUNT']['PERCENT']) { ?>
        <div class="catalog-element-price-percent-container">
            <div class="catalog-element-price-percent">
                <div class="catalog-element-price-percent-value" data-role="price.percent">
                    <?= '-'.$arPrice['PERCENT'].'%' ?>
                </div>
                <?php if ($arVisual['PRICE']['DISCOUNT']['ECONOMY']) { ?>
                    <div class="catalog-element-price-percent-difference" data-role="price.difference">
                        <?= $arPrice['PRINT_DISCOUNT'] ?>
                    </div>
                    <div class="catalog-element-price-percent-decoration">
                        <?= $arSvg['PRICE']['DIFFERENCE'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
<?= Html::endTag('div') ?>
<?php unset($arPrice) ?>