<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php $vQuantity = function (&$arItem) use (&$arVisual) { ?>
    <div class="catalog-element-quantity">
        <?php if ($arItem['CAN_BUY']) { ?>
            <?php if ($arVisual['QUANTITY']['MODE'] === 'number') { ?>
                <?php if ($arItem['CATALOG_QUANTITY'] > 0) { ?>
                    <div class="catalog-element-quantity-value" data-color="green">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_AVAILABLE').': ' ?>
                        <?= $arItem['CATALOG_QUANTITY'] ?>
                        <?php if (!empty($arItem['CATALOG_MEASURE_NAME'])) {
                            echo ' '.$arItem['CATALOG_MEASURE_NAME'];
                        } ?>
                    </div>
                <?php } else if (($arItem['CATALOG_QUANTITY_TRACE'] === 'N' || $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y') && $arItem['CATALOG_QUANTITY'] <= 0) { ?>
                    <div class="catalog-element-quantity-value" data-color="green">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_AVAILABLE') ?>
                    </div>
                <?php } ?>
            <?php } else if ($arVisual['QUANTITY']['MODE'] === 'text') { ?>
                <?php if ($arItem['CATALOG_QUANTITY'] > 0 && $arItem['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW']) { ?>
                    <div class="catalog-element-quantity-value" data-color="yellow">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_BOUNDS_FEW') ?>
                    </div>
                <?php } else if ($arItem['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY']) { ?>
                    <div class="catalog-element-quantity-value" data-color="green">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_BOUNDS_MANY') ?>
                    </div>
                <?php } else if ($arItem['CATALOG_QUANTITY'] > $arVisual['QUANTITY']['BOUNDS']['FEW'] && $arItem['CATALOG_QUANTITY'] < $arVisual['QUANTITY']['BOUNDS']['MANY']) { ?>
                    <div class="catalog-element-quantity-value" data-color="light-green">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_BOUNDS_ENOUGH') ?>
                    </div>
                <?php } else if ($arItem['CATALOG_QUANTITY_TRACE'] === 'N' || $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y') { ?>
                    <div class="catalog-element-quantity-value" data-color="green">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_BOUNDS_MANY') ?>
                    </div>
                <?php } ?>
            <?php } else if ($arVisual['QUANTITY']['MODE'] === 'logic') { ?>
                <div class="catalog-element-quantity-value" data-color="green">
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_AVAILABLE') ?>
                </div>
            <?php } ?>
        <?php } else { ?>
            <div class="catalog-element-quantity-value" data-color="red">
                <?= Loc::getMessage('C_CATALOG_ELEMENT_BANNER_PRODUCT_1_QUANTITY_UNAVAILABLE'); ?>
            </div>
        <?php } ?>
    </div>
<?php } ?>