<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="basket-prices intec-grid-item-auto intec-grid-item-400-1">
    {{#DISCOUNT_PRICE_FORMATED}}
        <div class="basket-price-discount intec-grid intec-grid-400-wrap intec-grid-i-6 intec-grid-a-h-between">
            <span class="basket-price-name intec-grid-item-auto">
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_PRICE_DISCOUNT') ?>
            </span>
            <span class="basket-price-value intec-grid-item-auto">
                {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
            </span>
        </div>
    {{/DISCOUNT_PRICE_FORMATED}}
    {{#DISCOUNT_PRICE_FORMATED}}
        <div class="basket-price-economy intec-grid intec-grid-400-wrap intec-grid-i-6 intec-grid-a-h-between">
            <span class="basket-price-name intec-grid-item-auto">
                <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_PRICE_ECONOMY') ?>
            </span>
            <span class="basket-price-value intec-grid-item-auto">
                {{{DISCOUNT_PRICE_FORMATED}}}
            </span>
        </div>
    {{/DISCOUNT_PRICE_FORMATED}}
    <div class="basket-price-total intec-grid intec-grid-400-wrap intec-grid-i-6 intec-grid-a-h-between">
        <span class="basket-price-name intec-grid-item-auto">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_PRICE_TOTAL') ?>
        </span>
        <?= Html::tag('span', '{{{PRICE_FORMATED}}}', [
            'class' => 'basket-price-value intec-grid-item-auto',
            'data-entity' => 'basket-total-price'
        ]) ?>
    </div>
    {{#SHOW_VAT}}
    <div class="basket-price-vat intec-grid intec-grid-400-wrap intec-grid-i-6 intec-grid-a-h-between">
        <span class="basket-price-name intec-grid-item-auto">
            <?= Loc::getMessage('C_BASKET_DEFAULT_2_TEMPLATE_TOTAL_PRICE_VAT') ?>
        </span>
        <span class="basket-price-value intec-grid-item-auto">
            {{{VAT_SUM_FORMATED}}}
        </span>
    </div>
    {{/SHOW_VAT}}
</div>