<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;

?>
<div class="intec-basket-sum">
    <div class="intec-basket-sum-item">
        <div class="intec-basket-sum-total intec-basket-grid intec-basket-grid-a-v-baseline">
            <div class="intec-basket-sum-total-item intec-basket-grid-item-auto">
                <div class="intec-basket-sum-total-title">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_TOTAL') ?>
                </div>
            </div>
            <div class="intec-basket-sum-total-item intec-basket-grid-item-auto">
                <div class="intec-basket-sum-total-values intec-basket-grid intec-basket-grid-a-v-baseline">
                    <div class="intec-basket-sum-total-current intec-basket-sum-total-value intec-basket-grid-item-auto" data-entity="basket-total-price">
                        {{{PRICE_FORMATED}}}
                    </div>
                    {{#DISCOUNT_PRICE_FORMATED}}
                        <div class="intec-basket-sum-total-discount intec-basket-sum-total-value intec-basket-grid-item-auto">
                            {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
                        </div>
                    {{/DISCOUNT_PRICE_FORMATED}}
                </div>
            </div>
        </div>
    </div>
    {{#DISCOUNT_PRICE_FORMATED}}
        <div class="intec-basket-sum-item">
            <div class="intec-basket-sum-economy intec-basket-grid intec-basket-grid-a-v-center">
                <div class="intec-basket-sum-economy-item intec-basket-grid-item-auto">
                    <div class="intec-basket-sum-economy-title">
                        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ECONOMY') ?>
                    </div>
                </div>
                <div class="intec-basket-sum-economy-item intec-basket-grid-item-auto">
                    <div class="intec-basket-sum-economy-value">
                    {{{DISCOUNT_PRICE_FORMATED}}}
                    </div>
                </div>
            </div>
        </div>
    {{/DISCOUNT_PRICE_FORMATED}}
    {{#SHOW_VAT}}
        <div class="intec-basket-sum-item">
            <div class="intec-basket-sum-additional">
                <span>
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_VAT') ?>
                </span>
                <span>
                    {{{VAT_SUM_FORMATED}}}
                </span>
            </div>
        </div>
    {{/SHOW_VAT}}
    <?php if (ArrayHelper::isIn('WEIGHT', $arParams['COLUMNS_LIST'])) { ?>
        {{#WEIGHT_FORMATED}}
            <div class="intec-basket-sum-item" data-mobile-hidden="<?= ArrayHelper::isIn('', $arParams['COLUMNS_LIST_MOBILE']) ? 'true' : 'false' ?>">
                <div class="intec-basket-sum-additional">
                    <span>
                        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_WEIGHT') ?>
                    </span>
                    <span>
                        {{{WEIGHT_FORMATED}}}
                    </span>
                </div>
            </div>
        {{/WEIGHT_FORMATED}}
    <?php } ?>
</div>