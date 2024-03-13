<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

?>
<div class="catalog-element-price-range-items">
    <div class="catalog-element-price-range-title">
        <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_PRICE_RANGE_TITLE') ?>
    </div>
    <?php foreach ($arResult['ITEM_PRICES'] as $arPriceRange) { ?>
        <div class="catalog-element-price-range-item">
            <div class="intec-grid intec-grid-a-v-end intec-grid-i-h-2 intec-grid-i-v-3 intec-grid-768-wrap">
                <div class="intec-grid-item-auto intec-grid-item-768-1">
                    <div class="catalog-element-price-range-quantity">
                        <?php if (!empty($arPriceRange['QUANTITY_FROM'])) { ?>
                            <span>
                                <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_PRICE_RANGE_QUANTITY_FROM', [
                                    '#FROM#' => $arPriceRange['QUANTITY_FROM']
                                ]) ?>
                            </span>
                        <?php } ?>
                        <?php if (!empty($arPriceRange['QUANTITY_TO'])) { ?>
                            <span>
                                <?= Loc::getMessage('C_MAIN_WIDGET_PRODUCT_1_TEMPLATE_PRICE_RANGE_QUANTITY_TO', [
                                    '#TO#' => $arPriceRange['QUANTITY_TO']
                                ]) ?>
                            </span>
                        <?php } ?>
                        <?php if (!empty($arResult['CATALOG_MEASURE_NAME'])) { ?>
                            <span>
                                <?= $arResult['CATALOG_MEASURE_NAME'] ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <div class="catalog-element-price-range-separator-container intec-grid-item intec-grid-item-768-auto">
                    <div class="catalog-element-price-range-separator"></div>
                </div>
                <div class="intec-grid-item-auto intec-grid-item-768-1">
                    <div class="catalog-element-price-range-price">
                        <span>
                            <?= $arPriceRange['PRINT_PRICE'] ?>
                        </span>
                        <span>/</span>
                        <span>
                            <?= $arResult['CATALOG_MEASURE_NAME'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <?php unset($arPriceRange) ?>
</div>
