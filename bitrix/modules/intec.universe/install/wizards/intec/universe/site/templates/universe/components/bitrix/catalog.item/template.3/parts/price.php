<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arItem
 */

$arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

?>
<?php if (!empty($arPrice) && Type::isArray($arPrice)) { ?>
    <div class="catalog-item-price-container">
        <div class="intec-grid intec-grid-i-h-8 intec-grid-a-h-end intec-grid-a-v-center">
            <div class="intec-grid-item-auto">
                <div class="catalog-item-price-current">
                    <?= $arPrice['PRINT_RATIO_PRICE'] ?>
                </div>
            </div>
            <?php if ($arParams['SHOW_OLD_PRICE'] === 'Y' && $arPrice['DISCOUNT'] > 0) { ?>
                <div class="intec-grid-item-auto">
                    <div class="catalog-item-price-discount">
                        <?= $arPrice['PRINT_RATIO_BASE_PRICE'] ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
<?php } ?>
<?php unset($arPrice) ?>
