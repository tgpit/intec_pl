<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
{{#IS_SKU}}
    <?= Html::beginTag('div', [
        'class' => [
            'basket-item-offers',
            'intec-grid',
            'intec-grid-wrap',
            'intec-grid-i-16',
            'intec-grid-item-1'
        ]
    ]) ?>
        {{#SKU_BLOCK_LIST}}
            {{#IS_IMAGE}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-offers-property',
                        'intec-grid',
                        'intec-grid-i-8',
                        'intec-grid-a-v-center',
                        'intec-grid-item-auto'
                    ],
                    'data' => [
                        'entity' => 'basket-item-sku-block',
                        'offer-property-type' => 'picture'
                    ]
                ]) ?>
                    <div class="basket-item-offers-property-name intec-grid-item">
                        {{NAME}}
                    </div>
                    <div class="basket-item-offers-property-values intec-grid-item-auto">
                        <div class="basket-item-offers-property-values-wrapper">
                            {{#SKU_VALUES_LIST}}
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'basket-item-offers-property-value',
                                        '{{#SELECTED}}selected{{/SELECTED}}',
                                        '{{#NOT_AVAILABLE_OFFER}}not-available{{/NOT_AVAILABLE_OFFER}}'
                                    ],
                                    'title' => '{{NAME}}',
                                    'data' => [
                                        'entity' => 'basket-item-sku-field',
                                        'value-id' => '{{VALUE_ID}}',
                                        'property' => '{{PROP_CODE}}',
                                        'sku-name' => '{{NAME}}',
                                        'initial' => '{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}'
                                    ]
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'basket-item-offers-property-value-content',
                                            'intec-cl-border-hover'
                                        ],
                                        'style' => [
                                            'background-image' => 'url(\'{{PICT}}\')'
                                        ]
                                    ]) ?>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            {{/SKU_VALUES_LIST}}
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            {{/IS_IMAGE}}
            {{^IS_IMAGE}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-offers-property',
                        'intec-grid',
                        'intec-grid-i-8',
                        'intec-grid-a-v-center',
                        'intec-grid-item-auto'
                    ],
                    'data' => [
                        'entity' => 'basket-item-sku-block',
                        'offer-property-type' => 'text'
                    ]
                ]) ?>
                    <div class="basket-item-offers-property-name intec-grid-item">
                        {{NAME}}
                    </div>
                    <div class="basket-item-offers-property-values intec-grid-item-auto">
                        <div class="basket-item-offers-property-values-wrapper">
                            {{#SKU_VALUES_LIST}}
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'basket-item-offers-property-value',
                                        '{{#SELECTED}}selected{{/SELECTED}}',
                                        '{{#NOT_AVAILABLE_OFFER}}not-available{{/NOT_AVAILABLE_OFFER}}'
                                    ],
                                    'title' => '{{NAME}}',
                                    'data' => [
                                        'entity' => 'basket-item-sku-field',
                                        'value-id' => '{{VALUE_ID}}',
                                        'property' => '{{PROP_CODE}}',
                                        'sku-name' => '{{NAME}}',
                                        'initial' => '{{#SELECTED}}true{{/SELECTED}}{{^SELECTED}}false{{/SELECTED}}'
                                    ]
                                ]) ?>
                                    <?= Html::tag('div', '{{NAME}}', [
                                        'class' => [
                                            'basket-item-offers-property-value-content',
                                            'intec-cl-border-hover'
                                        ]
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            {{/SKU_VALUES_LIST}}
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            {{/IS_IMAGE}}
        {{/SKU_BLOCK_LIST}}
    <?= Html::endTag('div') ?>
{{/IS_SKU}}