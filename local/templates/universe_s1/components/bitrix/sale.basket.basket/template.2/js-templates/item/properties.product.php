<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
{{#COLUMN_LIST_SHOW}}
    <div class="basket-item-product-properties intec-grid-item-1 intec-grid intec-grid-wrap">
        {{#COLUMN_LIST}}
            {{#IS_IMAGE}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-product-property ',
                        'intec-grid-item-1',
                        'intec-grid',
                        'intec-grid-a-v-center',
                        'intec-grid-i-4'
                    ],
                    'data' => [
                        'entity' => 'basket-item-property',
                        'product-property-type' => 'picture',
                        'mobile-hidden' => '{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}'
                    ]
                ]) ?>
                    <div class="basket-item-product-property-name intec-grid-item-auto">
                        {{NAME}}
                    </div>
                    <div class="basket-item-product-property-values intec-grid-item intec-grid intec-grid-wrap intec-grid-a-v-center">
                        {{#VALUE}}
                            <?= Html::img('{{{IMAGE_SRC}}}', [
                                'class' => [
                                    'basket-item-product-property-value',
                                    'intec-cl-border-hover',
                                    'intec-grid-item-auto'
                                ],
                                'data' => [
                                    'column-property-code' => '{{CODE}}',
                                    'product-property-role' => 'image',
                                    'image-index' => '{{INDEX}}'
                                ]
                            ]) ?>
                        {{/VALUE}}
                    </div>
                <?= Html::endTag('div') ?>
            {{/IS_IMAGE}}
            {{#IS_TEXT}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-product-property ',
                        'intec-grid-item-auto',
                        'intec-grid',
                        'intec-grid-wrap',
                        'intec-grid-i-4'
                    ],
                    'data' => [
                        'entity' => 'basket-item-property',
                        'product-property-type' => 'text',
                        'mobile-hidden' => '{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}'
                    ]
                ]) ?>
                    <div class="basket-item-product-property-name intec-grid-item-auto">
                        {{NAME}}
                    </div>
                    <?= Html::tag('div', '{{VALUE}}', [
                        'class' => 'basket-item-product-property-value intec-grid-item',
                        'data' => [
                            'entity' => 'basket-item-property-column-value',
                            'column-property-code' => '{{CODE}}',
                        ]
                    ]) ?>
                <?= Html::endTag('div') ?>
            {{/IS_TEXT}}
            {{#IS_LINK}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-product-property ',
                        'intec-grid-item-auto',
                        'intec-grid',
                        'intec-grid-wrap',
                        'intec-grid-i-4'
                    ],
                    'data' => [
                        'entity' => 'basket-item-property',
                        'product-property-type' => 'text',
                        'mobile-hidden' => '{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}'
                    ]
                ]) ?>
                    <div class="basket-item-product-property-name intec-grid-item-auto">
                        {{NAME}}
                    </div>
                    <div class="basket-item-product-property-values intec-grid-item">
                        {{#VALUE}}
                            {{{LINK}}}{{^IS_LAST}},{{/IS_LAST}}
                        {{/VALUE}}
                    </div>
                <?= Html::endTag('div') ?>
            {{/IS_LINK}}
            {{#IS_PREVIEW}}
                <?= Html::beginTag('div', [
                    'class' => [
                        'basket-item-product-property ',
                        'intec-grid-item-1',
                        'intec-grid',
                        'intec-grid-i-4'
                    ],
                    'data' => [
                        'entity' => 'basket-item-property',
                        'product-property-type' => 'preview-text',
                        'mobile-hidden' => '{{#HIDE_MOBILE}}true{{/HIDE_MOBILE}}{{^HIDE_MOBILE}}false{{/HIDE_MOBILE}}'
                    ]
                ]) ?>
                    <div class="basket-item-product-property-name intec-grid-item-auto">
                        {{NAME}}
                    </div>
                    <?= Html::tag('div', '{{VALUE}}', [
                        'class' => 'basket-item-product-property-value intec-grid-item',
                        'data' => [
                            'entity' => 'basket-item-property-column-value',
                            'column-property-code' => '{{CODE}}',
                        ]
                    ]) ?>
                <?= Html::endTag('div') ?>
            {{/IS_PREVIEW}}
        {{/COLUMN_LIST}}
    </div>
{{/COLUMN_LIST_SHOW}}