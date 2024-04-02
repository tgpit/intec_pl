<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

?>
{{#IS_SKU}}
    <div class="intec-basket-offers">
        {{#SKU_BLOCK_LIST}}
            {{#IS_IMAGE}}
                <div class="intec-basket-offers-item"
                     data-entity="basket-item-sku-block"
                     data-offer-property-type="picture"
                >
                    <div class="intec-basket-offers-title">
                        {{NAME}}
                    </div>
                    <div class="intec-basket-offers-values">
                        <div class="intec-basket-offers-values-content intec-basket-grid intec-basket-grid-wrap">
                            {{#SKU_VALUES_LIST}}
                                <div class="intec-basket-offers-values-item intec-basket-grid-item-auto{{#SELECTED}} selected{{/SELECTED}}{{#NOT_AVAILABLE_OFFER}} not-available{{/NOT_AVAILABLE_OFFER}}"
                                     title="{{NAME}}"
                                     data-entity="basket-item-sku-field"
                                     data-value-id="{{VALUE_ID}}"
                                     data-property="{{PROP_CODE}}"
                                     data-sku-name="{{NAME}}"
                                     data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
                                >
                                    <button class="intec-basket-offers-values-item-content {{#SELECTED}}intec-basket-scheme-border{{/SELECTED}}{{^SELECTED}}intec-basket-scheme-border-hover{{/SELECTED}} intec-basket-picture intec-basket-align-middle" style="background-image: url('{{PICT}}')">
                                        <svg width="14" height="14" viewBox="0 0 14 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M11.918 2.28226L4.72539 9.47487L2.08256 6.83204C1.95443 6.70391 1.74667 6.70391 1.61851 6.83204L0.845123 7.60543C0.71699 7.73356 0.71699 7.94132 0.845123 8.06948L4.49335 11.7177C4.62149 11.8458 4.82924 11.8458 4.9574 11.7177L13.1554 3.5197C13.2835 3.39157 13.2835 3.18381 13.1554 3.05565L12.382 2.28226C12.2539 2.15412 12.0461 2.15412 11.918 2.28226Z" fill="white"/>
                                        </svg>
                                    </button>
                                </div>
                            {{/SKU_VALUES_LIST}}
                        </div>
                    </div>
                </div>
            {{/IS_IMAGE}}
            {{^IS_IMAGE}}
                <div class="intec-basket-offers-item"
                     data-entity="basket-item-sku-block"
                     data-offer-property-type="text"
                >
                    <div class="intec-basket-offers-title">
                        {{NAME}}
                    </div>
                    <div class="intec-basket-offers-values">
                        <div class="intec-basket-offers-values-content intec-basket-grid intec-basket-grid-wrap">
                            {{#SKU_VALUES_LIST}}
                                <div class="intec-basket-offers-values-item intec-basket-grid-item-auto{{#SELECTED}} selected{{/SELECTED}}{{#NOT_AVAILABLE_OFFER}} not-available{{/NOT_AVAILABLE_OFFER}}"
                                     title="{{NAME}}"
                                     data-entity="basket-item-sku-field"
                                     data-value-id="{{VALUE_ID}}"
                                     data-property="{{PROP_CODE}}"
                                     data-sku-name="{{NAME}}"
                                     data-initial="{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}"
                                >
                                    <button class="intec-basket-offers-values-item-content {{#SELECTED}}intec-basket-scheme-border{{/SELECTED}}{{^SELECTED}}intec-basket-scheme-border-hover{{/SELECTED}}">
                                        {{NAME}}
                                    </button>
                                </div>
                            {{/SKU_VALUES_LIST}}
                        </div>
                    </div>
                </div>
            {{/IS_IMAGE}}
        {{/SKU_BLOCK_LIST}}
        <?php include(__DIR__.'/notify.similar.php') ?>
    </div>
{{/IS_SKU}}