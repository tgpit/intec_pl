<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var array $mobileColumns
 */

?>
{{#PROPS_SHOW}}
    <div class="intec-basket-properties"
         data-mobile-hidden="<?= array_key_exists('PROPS', $mobileColumns) ? 'false' : 'true' ?>"
    >
        {{#PROPS}}
            <div class="intec-basket-properties-item" data-product-property-type="text">
                <div class="intec-basket-properties-item-value-content intec-basket-grid intec-basket-grid-wrap intec-basket-grid-a-v-center">
                    <div class="intec-basket-properties-item-title intec-basket-properties-item-value-item intec-basket-grid-item-auto">
                        {{{NAME}}}
                    </div>
                    <div class="intec-basket-properties-item-value intec-basket-properties-item-value-item intec-basket-grid-item-auto"
                         data-entity="basket-item-property-value"
                         data-property-code="{{CODE}}"
                    >
                        {{{VALUE}}}
                    </div>
                </div>
            </div>
        {{/PROPS}}
    </div>
{{/PROPS_SHOW}}