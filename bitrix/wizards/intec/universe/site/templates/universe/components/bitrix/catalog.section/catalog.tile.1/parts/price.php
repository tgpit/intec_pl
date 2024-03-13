<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arVisual
 */

?>
<?php $vPrice = function (&$arItem) use (&$arVisual, &$arSvg) {
    $arPrice = null;

    if (!empty($arItem['ITEM_PRICES']))
        $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);
?>
    <div class="catalog-section-item-price-wrapper intec-grid intec-grid-nowrap intec-grid-a-v-center">
        <div class="intec-grid-item">
            <div class="catalog-section-item-price-discount">
                <?php if (!$arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) { ?>
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PRICE_FORM') ?>
                <?php } ?>
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
            <div class="catalog-section-item-price-base">
                <?php if (!$arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) { ?>
                    <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_PRICE_FORM') ?>
                <?php } ?>
                <span data-role="item.price.base">
                    <?= !empty($arPrice) ? $arPrice['PRINT_BASE_PRICE'] : null ?>
                </span>
            </div>
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
        </div>
    </div>
<?php } ?>