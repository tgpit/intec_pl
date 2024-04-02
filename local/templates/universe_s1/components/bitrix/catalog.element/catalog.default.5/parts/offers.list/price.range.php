<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

?>
<?php return function (&$arOffer) { ?>
    <?php if (count($arOffer['ITEM_PRICES']) <= 1)
        return;
    ?>
    <div class="catalog-element-offers-list-item-price-range">
        <?php foreach ($arOffer['ITEM_PRICES'] as $arPrice) { ?>
            <div class="catalog-element-offers-list-item-price-range-item">
                <div class="intec-grid intec-grid-a-v-end intec-grid-i-h-2">
                    <div class="intec-grid-item-auto">
                        <div class="catalog-element-offers-list-item-price-range-item-quantity">
                            <?php if (!empty($arPrice['QUANTITY_FROM'])) { ?>
                                <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_FROM', [
                                    '#FROM#' => $arPrice['QUANTITY_FROM']
                                ])) ?>
                            <?php } ?>
                            <?php if (!empty($arPrice['QUANTITY_TO'])) { ?>
                                <?= Html::tag('span', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_PRICE_RANGE_TO', [
                                    '#TO#' => $arPrice['QUANTITY_TO']
                                ])) ?>
                            <?php } ?>
                            <?php if (!empty($arOffer['CATALOG_MEASURE_NAME'])) { ?>
                                <?= Html::tag('span', $arOffer['CATALOG_MEASURE_NAME']) ?>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="intec-grid-item">
                        <div class="catalog-element-offers-list-item-price-range-item-separator"></div>
                    </div>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <div class="catalog-element-offers-list-item-price-range-item-price">
                            <?= Html::tag('span', $arPrice['PRINT_PRICE'], [
                                'title' => StringHelper::replace($arPrice['PRINT_PRICE'], [
                                    '&nbsp;' => ' '
                                ])
                            ]) ?>
                            <?php if (!empty($arOffer['CATALOG_MEASURE_NAME'])) { ?>
                                <?= Html::tag('span', '/') ?>
                                <?= Html::tag('span', $arOffer['CATALOG_MEASURE_NAME']) ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>