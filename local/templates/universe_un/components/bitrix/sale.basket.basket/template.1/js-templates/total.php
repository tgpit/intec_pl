<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 */

?>
<script id="basket-total-template" type="text/html">
    <?= Html::beginTag('div', [
        'class' => 'basket-total-container',
        'data-entity' => 'basket-checkout-aligner'
    ]) ?>
        <div class="basket-total-wrapper intec-grid intec-grid-a-h-between intec-grid-1024-wrap">
            <?php if ($arParams['HIDE_COUPON'] !== 'Y') { ?>
                <div class="intec-grid-item-auto intec-grid-item-768-1" data-print="false">
                    <div class="basket-coupon">
                        <?php include(__DIR__.'/total/coupon.field.php') ?>
                        <?php include(__DIR__.'/total/coupon.message.php') ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="intec-grid-item-auto"></div>
            <?php } ?>
            <div class="intec-grid-item-auto intec-grid-item-900-1">
                <div class="basket-price-wrap intec-grid intec-grid-wrap intec-grid-i-12 intec-grid-a-h-around intec-grid-a-h-1200-between">
                    <?php include(__DIR__.'/total/total.price.php') ?>
                    <?php include(__DIR__.'/total/total.order.php') ?>
                </div>
            </div>
        </div>
	<?= Html::endTag('div') ?>
</script>