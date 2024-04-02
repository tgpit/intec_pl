<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>
<div class="intec-basket-counter" data-entity="basket-item-quantity-block">
    <div class="intec-basket-counter-content intec-basket-grid intec-basket-grid-a-v-stretch intec-basket-grid-a-h-center">
        <div class="intec-basket-grid-item-auto">
            <button class="intec-basket-counter-button intec-basket-scheme-color-hover" data-entity="basket-item-quantity-minus">
                -
            </button>
        </div>
        <div class="intec-basket-grid-item-auto">
            <input class="intec-basket-counter-input"
                   id="basket-item-quantity-{{ID}}"
                   type="text"
                   value="{{QUANTITY}}"
                   data-entity="basket-item-quantity-field"
                   data-value="{{QUANTITY}}"
            />
        </div>
        <div class="intec-basket-grid-item-auto">
            <button class="intec-basket-counter-button intec-basket-scheme-color-hover" data-entity="basket-item-quantity-plus">
                +
            </button>
        </div>
    </div>
</div>