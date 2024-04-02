<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
{{#DISABLE_CHECKOUT}}
    <button class="intec-basket-order-button-disabled intec-basket-scheme-background-dark intec-basket-scheme-border-dark" disabled="disabled" data-entity="basket-checkout-button">
        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ORDER') ?>
    </button>
{{/DISABLE_CHECKOUT}}
{{^DISABLE_CHECKOUT}}
    <button class="intec-basket-order-button intec-basket-scheme-background intec-basket-scheme-border intec-basket-scheme-background-light-hover intec-basket-scheme-border-light-hover" data-entity="basket-checkout-button">
        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ORDER') ?>
    </button>
{{/DISABLE_CHECKOUT}}