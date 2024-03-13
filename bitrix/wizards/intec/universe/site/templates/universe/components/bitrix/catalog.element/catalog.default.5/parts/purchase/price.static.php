<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

$arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

?>
<?php if (!empty($arPrice)) { ?>
    <div class="catalog-element-purchase-block">
        <div class="catalog-element-price-container">
            <div class="catalog-element-price">
                <div class="catalog-element-price-current catalog-element-price-part">
                    <span class="catalog-element-price-current-value">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_FROM', [
                            '#FROM#' => $arPrice['PRINT_PRICE']
                        ]) ?>
                    </span>
                </div>
                <?php if ($arVisual['PRICE']['DISCOUNT']['OLD'] && $arPrice['PERCENT'] > 0) { ?>
                    <div class="catalog-element-price-discount catalog-element-price-part">
                        <?= $arPrice['PRINT_BASE_PRICE'] ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php if ($arVisual['PRICE']['DISCOUNT']['PERCENT'] && $arPrice['PERCENT'] > 0) { ?>
            <div class="catalog-element-price-percent-container">
                <div class="catalog-element-price-percent">
                    <div class="catalog-element-price-percent-value">
                        <?= '-'.$arPrice['PERCENT'].'%' ?>
                    </div>
                    <?php if ($arVisual['PRICE']['DISCOUNT']['ECONOMY']) { ?>
                        <div class="catalog-element-price-percent-difference">
                            <?= $arPrice['PRINT_DISCOUNT'] ?>
                        </div>
                        <div class="catalog-element-price-percent-decoration">
                            <?= $arSvg['PRICE']['DIFFERENCE'] ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
<?php unset($arPrice) ?>