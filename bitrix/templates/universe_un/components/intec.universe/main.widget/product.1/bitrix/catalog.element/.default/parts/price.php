<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

if (empty($arPrice))
    return;

if ($arVisual['PRICE']['DISCOUNT']['SHOW'] && empty($arPrice['PERCENT']))
    $arVisual['PRICE']['DISCOUNT']['SHOW'] = false;

?>
<div class="catalog-element-price">
    <div class="intec-grid intec-grid intec-grid-i-h-4">
        <?php if ($arVisual['PRICE']['RANGE']['SHOW']) { ?>
            <div class="intec-grid-item-auto">
                <div class="catalog-element-price-range">
                    <div class="catalog-element-price-range-open intec-cl-border-hover intec-ui-align">
                        <span></span>
                        <span></span>
                        <span></span>
                    </div>
                    <div class="catalog-element-price-range-content">
                        <?php include(__DIR__.'/price/range.php') ?>
                    </div>
                </div>
            </div>
        <?php } ?>
        <div class="intec-grid-item">
            <div class="catalog-element-price-value">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-baseline intec-grid-i-h-8 intec-grid-i-v-4">
                    <div class="catalog-element-price-value-current intec-grid-item-auto intec-grid-item-1024-1">
                        <span data-role="product.price.current">
                            <?php if (!empty($arResult['OFFERS'])) { ?>
                                <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_PRICE_FROM', [
                                    '#PRICE#' => $arPrice['PRINT_PRICE']
                                ]) ?>
                            <?php } else { ?>
                                <?= $arPrice['PRINT_PRICE'] ?>
                            <?php } ?>
                        </span>
                        <?php if (!empty($arResult['CATALOG_MEASURE_NAME'])) { ?>
                            <span>/</span>
                            <span>
                                <?= $arResult['CATALOG_MEASURE_NAME'] ?>
                            </span>
                        <?php } ?>
                    </div>
                    <?php if ($arVisual['PRICE']['DISCOUNT']['SHOW'] && ($arPrice['BASE_PRICE'] > $arPrice['PRICE'])) { ?>
                        <div class="catalog-element-price-value-previous intec-grid-item-auto intec-grid-item-1024-1">
                            <span data-role="product.price.discount">
                                <?= $arPrice['PRINT_BASE_PRICE'] ?>
                            </span>
                            <?php if (!empty($arResult['CATALOG_MEASURE_NAME'])) { ?>
                                <span>/</span>
                                <span>
                                    <?= $arResult['CATALOG_MEASURE_NAME'] ?>
                                </span>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <?php if (
                $arVisual['PRICE']['DISCOUNT']['SHOW'] &&
                ($arVisual['PRICE']['DISCOUNT']['PERCENT'] || $arVisual['PRICE']['DISCOUNT']['ECONOMY']) &&
                ($arPrice['BASE_PRICE'] > $arPrice['PRICE'])
            ) { ?>
                <div class="catalog-element-price-discount">
                    <div class="catalog-element-price-discount-content intec-grid intec-grid-inline intec-grid-a-v-center">
                        <?php if ($arVisual['PRICE']['DISCOUNT']['PERCENT']) { ?>
                            <div class="catalog-element-price-discount-item intec-grid-item-auto">
                                <div class="catalog-element-price-discount-value" data-view="tile">
                                    <?= '-'.$arPrice['PERCENT'].'%' ?>
                                </div>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['PRICE']['DISCOUNT']['ECONOMY']) { ?>
                            <div class="catalog-element-price-discount-item intec-grid-item-auto">
                                <?= Html::tag('div', $arPrice['PRINT_DISCOUNT'], [
                                    'class' => 'catalog-element-price-discount-value',
                                    'data' => [
                                        'role' => 'product.price.economy',
                                        'view' => $arVisual['PRICE']['DISCOUNT']['PERCENT'] ? 'default' : 'tile'
                                    ]
                                ]) ?>
                            </div>
                            <div class="catalog-element-price-discount-item intec-grid-item-auto">
                                <div class="catalog-element-price-discount-icon intec-ui-picture">
                                    <?= FileHelper::getFileData(__DIR__.'/../svg/price.discount.value.icon.svg') ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>