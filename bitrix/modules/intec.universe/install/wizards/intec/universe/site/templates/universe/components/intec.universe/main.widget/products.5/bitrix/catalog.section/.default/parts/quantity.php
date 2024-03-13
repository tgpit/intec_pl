<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arVisual) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arVisual) {

        if ($arItem['VISUAL']['OFFER'] && !$bOffer)
            return;

    ?>
        <?= Html::beginTag('div', [
            'class' => 'widget-item-quantity',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false',
            'data-role' => 'item.quantity'
        ]) ?>
            <div class="intec-grid intec-grid-a-v-baseline">
                <?php if ($arItem['CAN_BUY']) { ?>
                    <?php if ($arVisual['QUANTITY']['MODE'] === 'number') { ?>
                        <?php if ($arItem['CATALOG_QUANTITY'] > 0) { ?>
                            <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-available"></div>
                            <div class="widget-item-quantity-value intec-grid-item">
                                <div>
                                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_AVAILABLE') ?>
                                </div>
                                <div>
                                    <?= $arItem['CATALOG_QUANTITY'] ?> <?= !empty($arItem['CATALOG_MEASURE_NAME']) ? $arItem['CATALOG_MEASURE_NAME'].'.' : '' ?>
                                </div>
                            </div>
                        <?php } else { ?>
                            <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-available"></div>
                            <span class="widget-item-quantity-value intec-grid-item">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_AVAILABLE') ?>
                            </span>
                        <?php } ?>
                    <?php } else if ($arVisual['QUANTITY']['MODE'] === 'text') {?>
                        <?php if ($arItem['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY'] || $arItem['CATALOG_QUANTITY'] <= 0) { ?>
                            <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-available"></div>
                            <div class="widget-item-quantity-value intec-grid-item">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_BOUNDS_MANY') ?>
                            </div>
                        <?php } else if ($arItem['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW']) { ?>
                            <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-few"></div>
                            <div class="widget-item-quantity-value intec-grid-item">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_BOUNDS_FEW') ?>
                            </div>
                        <?php } else if ($arItem['CATALOG_QUANTITY'] > $arVisual['QUANTITY']['BOUNDS']['FEW'] && $arItem['CATALOG_QUANTITY'] < $arVisual['QUANTITY']['BOUNDS']['MANY']) { ?>
                            <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-available"></div>
                            <div class="widget-item-quantity-value intec-grid-item">
                                <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_BOUNDS_ENOUGH') ?>
                            </div>
                        <?php } ?>
                    <?php } else if ($arVisual['QUANTITY']['MODE'] === 'logic') { ?>
                        <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-available"></div>
                        <div class="widget-item-quantity-value intec-grid-item">
                            <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_AVAILABLE') ?>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <div class="widget-item-quantity-icon intec-grid-item-auto widget-item-quantity-unavailable"></div>
                    <div class="widget-item-quantity-value intec-grid-item">
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_QUANTITY_UNAVAILABLE') ?>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>