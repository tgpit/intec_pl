<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

?>
<div class="intec-basket-price-along">
    {{#SHOW_PRICE_FOR}}
        <div class="intec-basket-price-along-measure">
            <span>
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_ITEM_PRICE_ALONG_DELIMITER') ?>
            </span>
            <span>
                {{MEASURE_RATIO}}
            </span>
            <span>
                {{MEASURE_TEXT}}
            </span>
        </div>
        <div class="intec-basket-price-along-value" id="basket-item-price-{{ID}}">
            {{{PRICE_FORMATED}}}
        </div>
    {{/SHOW_PRICE_FOR}}
    {{^SHOW_PRICE_FOR}}
        <div class="intec-basket-price-along-measure">
            <span>
                {{MEASURE_TEXT}}
            </span>
        </div>
    {{/SHOW_PRICE_FOR}}
</div>
