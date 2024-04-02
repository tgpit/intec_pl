<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 */

$bPicture = ArrayHelper::isIn('PREVIEW_PICTURE', $arParams['COLUMNS_LIST']);
$bPriceApart = ArrayHelper::isIn('PRICE', $arParams['COLUMNS_LIST']) && $arParams['PRICE_DISPLAY_MODE'] === 'Y';
$bTotal = ArrayHelper::isIn('SUM', $arParams['COLUMNS_LIST']);
$bAction = ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST']) || ArrayHelper::isIn('DELAY', $arParams['COLUMNS_LIST']);
$bActionsHide = !ArrayHelper::isIn('DELAY', $arParams['COLUMNS_LIST_MOBILE']) && !ArrayHelper::isIn('DELETE', $arParams['COLUMNS_LIST_MOBILE']);

?>
<script id="basket-item-template" type="text/html">
    <div class="intec-basket-item" id="basket-item-{{ID}}" data-entity="basket-item" data-id="{{ID}}">
        <div class="intec-basket-grid intec-basket-grid-a-v-stretch">
            <div class="intec-basket-grid-item">
                <div class="intec-basket-item-content">
                    <div class="intec-basket-item-content-body intec-basket-grid">
                        {{#SHOW_RESTORE}}
                            <div class="intec-basket-item-content-body-item intec-basket-grid-item">
                                <?php include(__DIR__.'/item/restore.php') ?>
                            </div>
                        {{/SHOW_RESTORE}}
                        {{^SHOW_RESTORE}}
                        <div class="intec-basket-item-content-information intec-basket-item-content-body-item intec-basket-grid-item">
                            <div class="intec-basket-item-content-information-content intec-basket-grid">
                                <?php if ($bPicture) { ?>
                                    <div class="intec-basket-item-content-picture intec-basket-item-content-information-content-item intec-basket-grid-item-auto">
                                        <?php include(__DIR__.'/item/image.php') ?>
                                        <?php if ($bAction && !$bActionsHide)
                                            include(__DIR__.'/item/actions.mobile.php');
                                        ?>
                                    </div>
                                <?php } ?>
                                <div class="intec-basket-item-content-text intec-basket-item-content-information-content-item intec-basket-grid-item">
                                    <div class="intec-basket-item-content-name intec-basket-grid-item">
                                        <?php include(__DIR__.'/item/name.php') ?>
                                        <?php include(__DIR__.'/item/notify.warnings.php') ?>
                                        <?php include(__DIR__.'/item/notify.unavailable.php') ?>
                                        <?php include(__DIR__.'/item/notify.delayed.php') ?>
                                    </div>
                                    <div class="intec-basket-item-content-properties">
                                        <?php foreach ($arParams['PRODUCT_BLOCKS_ORDER'] as $block) {
                                            if ($block === 'sku')
                                                include(__DIR__.'/item/properties.offers.php');
                                            else if ($block === 'props' && ArrayHelper::isIn('PROPS', $arParams['COLUMNS_LIST']))
                                                include(__DIR__.'/item/properties.basket.php');
                                            else if ($block === 'columns')
                                                include(__DIR__.'/item/properties.product.php');
                                        }

                                        unset($block); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="intec-basket-item-content-price intec-basket-item-content-body-item intec-basket-grid-item">
                            <div class="intec-basket-item-content-price-content intec-basket-grid intec-basket-grid-wrap intec-basket-grid-a-h-end">
                                <div class="intec-basket-item-content-price-values intec-basket-item-content-price-item intec-basket-grid-item">
                                    <div class="intec-basket-item-content-price-values-content intec-basket-grid intec-basket-grid-a-v-center intec-basket-grid-a-h-end">
                                        <?php if ($bPriceApart) { ?>
                                            <div class="intec-basket-item-content-price-apart intec-basket-item-content-price-values-item intec-basket-grid-item-auto">
                                                <?php include(__DIR__.'/item/price.apart.php') ?>
                                            </div>
                                        <?php } ?>
                                        <div class="intec-basket-item-content-price-counter intec-basket-item-content-price-values-item intec-basket-grid-item-auto">
                                            <?php include(__DIR__.'/item/counter.php') ?>
                                            <?php if (!$bPriceApart) {
                                                include(__DIR__.'/item/price.along.php');
                                            } ?>
                                        </div>
                                        <?php if ($bTotal) { ?>
                                            <div class="intec-basket-item-content-price-total intec-basket-item-content-price-values-item intec-basket-grid-item-auto" data-mobile-hidden="<?= !ArrayHelper::isIn('SUM', $arParams['COLUMNS_LIST_MOBILE']) ? 'true' : 'false' ?>">
                                                <div class="intec-basket-item-content-price-total-content">
                                                    <?php include(__DIR__.'/item/price.total.php') ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if ($bAction) { ?>
                                    <div class="intec-basket-item-content-price-actions intec-basket-item-content-price-item intec-basket-grid-item" data-mobile-hidden="<?= $bActionsHide ? 'true' : 'false' ?>">
                                        <?php include(__DIR__.'/item/actions.php') ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        {{/SHOW_RESTORE}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</script>
<?php unset($bPicture, $bPriceApart, $bTotal, $bAction) ?>