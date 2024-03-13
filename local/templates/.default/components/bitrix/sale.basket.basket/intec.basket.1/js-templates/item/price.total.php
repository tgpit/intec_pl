<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-price-apart">
    {{#DISCOUNT_PRICE_PERCENT}}
        <div class="intec-basket-price-apart-upper intec-basket-price-apart-item">
            <span>
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_TOTAL_DISCOUNT') ?>
            </span>
            <span>
                {{DISCOUNT_PRICE_PERCENT_FORMATED}}
            </span>
        </div>
    {{/DISCOUNT_PRICE_PERCENT}}
    <div class="intec-basket-price-apart-main intec-basket-price-apart-item" id="basket-item-sum-price-{{ID}}">
        {{{SUM_PRICE_FORMATED}}}
    </div>
    {{#SHOW_DISCOUNT_PRICE}}
        <div class="intec-basket-price-apart-lower intec-basket-price-apart-item" id="basket-item-sum-price-old-{{ID}}">
            {{{SUM_FULL_PRICE_FORMATED}}}
        </div>
    {{/SHOW_DISCOUNT_PRICE}}
</div>
{{#SHOW_DISCOUNT_PRICE}}
    <div class="intec-basket-price-economy">
        <div class="intec-basket-price-economy-content intec-basket-grid intec-basket-grid-a-v-center">
            <div class="intec-basket-price-economy-title intec-basket-price-economy-item intec-basket-grid-item-auto">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_TOTAL_ECONOMY') ?>
            </div>
            <div class="intec-basket-price-economy-item intec-basket-grid-item-auto">
                <div class="intec-basket-price-economy-value" id="basket-item-sum-price-difference-{{ID}}">
                    {{{SUM_DISCOUNT_PRICE_FORMATED}}}
                </div>
            </div>
        </div>
    </div>
{{/SHOW_DISCOUNT_PRICE}}