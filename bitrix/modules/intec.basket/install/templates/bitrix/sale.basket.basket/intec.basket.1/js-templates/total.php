<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $arResult
 * @var array $arParams
 */

?>
<script id="basket-total-template" type="text/html">
    <div class="intec-basket-total" data-entity="basket-checkout-aligner" data-coupons="<?= $arParams['HIDE_COUPON'] !== 'Y' ? 'true' : 'false' ?>">
        <div class="intec-basket-total-items intec-basket-grid intec-basket-grid-a-h-between">
            <?php if ($arParams['HIDE_COUPON'] !== 'Y') { ?>
                <div class="intec-basket-total-coupon intec-basket-total-item intec-basket-grid-item-auto intec-basket-grid-item-shrink">
                    <?php include(__DIR__.'/total/coupon.php') ?>
                </div>
                <div class="intec-basket-total-price intec-basket-total-item intec-basket-grid-item-auto">
                    <div class="intec-basket-total-price-content intec-basket-grid intec-basket-grid-a-v-start">
                        <div class="intec-basket-total-price-item intec-basket-grid-item-auto">
                            <?php include(__DIR__.'/total/price.php') ?>
                        </div>
                        <div class="intec-basket-total-price-item intec-basket-grid-item-auto">
                            <?php include(__DIR__.'/total/order.php') ?>
                        </div>
                    </div>
                </div>
            <?php } else { ?>
                <div class="intec-basket-total-item intec-basket-grid-item-auto">
                    <?php include(__DIR__.'/total/price.php') ?>
                </div>
                <div class="intec-basket-total-item intec-basket-grid-item-auto">
                    <?php include(__DIR__.'/total/order.php') ?>
                </div>
            <?php } ?>
        </div>
    </div>
</script>