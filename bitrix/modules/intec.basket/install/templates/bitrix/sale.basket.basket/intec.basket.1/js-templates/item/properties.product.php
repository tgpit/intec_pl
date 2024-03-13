<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>
{{#COLUMN_LIST_SHOW}}
    <div class="intec-basket-properties">
        {{#COLUMN_LIST}}
            {{#IS_IMAGE}}
                <div class="intec-basket-properties-item" data-entity="basket-item-property" data-product-property-type="picture" data-mobile-hidden="{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}">
                    <div class="intec-basket-properties-item-title">
                        {{NAME}}
                    </div>
                    <div class="intec-basket-properties-item-value">
                        <div class="intec-basket-properties-item-value-content intec-basket-grid intec-basket-grid-wrap">
                            {{#VALUE}}
                                <div class="intec-basket-properties-item-value-item intec-basket-scheme-border-hover intec-basket-grid-item-auto intec-basket-picture" data-product-property-role="image" data-column-property-code="{{CODE}}" data-image-index="{{INDEX}}">
                                    <img src="{{IMAGE_SRC}}" alt="{{NAME}}" title="{{NAME}}" loading="lazy"/>
                                </div>
                            {{/VALUE}}
                        </div>
                    </div>
                </div>
            {{/IS_IMAGE}}
            {{#IS_TEXT}}
                <div class="intec-basket-properties-item" data-entity="basket-item-property" data-product-property-type="text" data-mobile-hidden="{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}">
                    <div class="intec-basket-properties-item-value-content intec-basket-grid intec-basket-grid-wrap intec-basket-grid-a-v-center">
                        <div class="intec-basket-properties-item-title intec-basket-properties-item-value-item intec-basket-grid-item-auto">
                            {{NAME}}
                        </div>
                        <div class="intec-basket-properties-item-value intec-basket-properties-item-value-item intec-basket-grid-item-auto">
                            {{VALUE}}
                        </div>
                    </div>
                </div>
            {{/IS_TEXT}}
            {{#IS_LINK}}
                <div class="intec-basket-properties-item" data-entity="basket-item-property" data-product-property-type="text" data-mobile-hidden="{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}">
                    <div class="intec-basket-properties-item-value-content intec-basket-grid intec-basket-grid-wrap intec-basket-grid-a-v-center">
                        <div class="intec-basket-properties-item-title intec-basket-properties-item-value-item intec-basket-grid-item-auto">
                            {{NAME}}
                        </div>
                        <div class="intec-basket-properties-item-value intec-basket-properties-item-value-item intec-basket-grid-item-auto">
                            {{#VALUE}}
                                {{{LINK}}}{{^IS_LAST}}, {{/IS_LAST}}
                            {{/VALUE}}
                        </div>
                    </div>
                </div>
            {{/IS_LINK}}
        {{/COLUMN_LIST}}
    </div>
{{/COLUMN_LIST_SHOW}}
