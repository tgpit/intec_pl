<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-price-apart">
    <div class="intec-basket-price-apart-upper intec-basket-price-apart-item">
        <span>
            <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_PRICE_APART_BEFORE') ?>
        </span>
        <span>
            {{MEASURE_RATIO}}
        </span>
        <span>
            {{MEASURE_TEXT}}
        </span>
    </div>
    <div class="intec-basket-price-apart-main intec-basket-price-apart-item" id="basket-item-price-{{ID}}">
        {{{PRICE_FORMATED}}}
    </div>
    {{#SHOW_DISCOUNT_PRICE}}
        <div class="intec-basket-price-apart-lower intec-basket-price-apart-item">
            {{{FULL_PRICE_FORMATED}}}
        </div>
    {{/SHOW_DISCOUNT_PRICE}}
</div>
