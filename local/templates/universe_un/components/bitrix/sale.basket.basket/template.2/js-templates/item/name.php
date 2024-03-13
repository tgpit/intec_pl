<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

?>
{{#DETAIL_PAGE_URL}}
    {{#IS_SKU}}
        <?= Html::tag('a', '{{NAME}}', [
            'class' => 'intec-cl-text-hover',
            'href' => '{{DETAIL_PAGE_URL}}?'.$arResult['OFFERS_VARIABLE_SELECT'].'={{PRODUCT_ID}}',
            'data-entity' => 'basket-item-name'
        ]) ?>
    {{/IS_SKU}}
    {{^IS_SKU}}
        <?= Html::tag('a', '{{NAME}}', [
            'class' => 'intec-cl-text-hover',
            'href' => '{{DETAIL_PAGE_URL}}',
            'data-entity' => 'basket-item-name'
        ]) ?>
    {{/IS_SKU}}
{{/DETAIL_PAGE_URL}}
{{^DETAIL_PAGE_URL}}
    <?= Html::tag('span', '{{NAME}}', [
        'data-entity' => 'basket-item-name'
    ]) ?>
{{/DETAIL_PAGE_URL}}