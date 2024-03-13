<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<?php $vPriceRange = function (&$arItem, $bOffer = false) {

    if (count($arItem['ITEM_PRICES']) <= 1)
        return;

?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-price-range',
        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
    ]) ?>
        <div class="catalog-element-price-range-items">
            <?php foreach ($arItem['ITEM_PRICES'] as $arPrice) { ?>
                <div class="catalog-element-price-range-item">
                    <div class="catalog-element-price-range-item-text">
                        <?php if (!empty($arPrice['QUANTITY_FROM'])) {
                            echo Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PRICE_RANGE_FROM').' ';
                            echo $arPrice['QUANTITY_FROM'];

                            if (!empty($arPrice['QUANTITY_TO']))
                                echo ' ';
                            else
                                echo ' '.Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PRICE_RANGE_MORE');
                        }

                        if (!empty($arPrice['QUANTITY_TO'])) {
                            echo Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PRICE_RANGE_TO').' ';
                            echo $arPrice['QUANTITY_TO'];
                        } ?>
                    </div>
                    <div class="catalog-element-price-range-item-value">
                        <span>
                            <?= $arPrice['PRINT_PRICE'] ?>
                        </span>
                        <?php if (!empty($arItem['CATALOG_MEASURE_NAME'])) { ?>
                            <span>
                                /
                            </span>
                            <span>
                                <?= $arItem['CATALOG_MEASURE_NAME'] ?>
                            </span>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>