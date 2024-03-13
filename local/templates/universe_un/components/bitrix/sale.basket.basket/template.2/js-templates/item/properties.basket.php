<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 */

if (!ArrayHelper::isIn('PROPS', $arParams['COLUMNS_LIST']))
    return;

?>
{{#PROPS_SHOW}}
    <?= Html::beginTag('div', [
        'class' => [
            'basket-item-basket-properties',
            'intec-grid',
            'intec-grid-wrap',
            'intec-grid-i-10',
            'intec-grid-item-1'
        ],
        'data-mobile-hidden' => ArrayHelper::keyExists('PROPS', $mobileColumns) ? 'false' : 'true'
    ]) ?>
        {{#PROPS}}
            <div class="basket-item-basket-property intec-grid-item-auto intec-grid intec-grid-i-2">
                <div class="basket-item-basket-property-name intec-grid-item-auto">
                    {{{NAME}}}
                </div>
                    <?= Html::tag('div', '{{{VALUE}}}', [
                        'class' => [
                            'basket-item-basket-property-value',
                            'intec-grid-item'
                        ],
                        'data' => [
                            'entity' => 'basket-item-property-value',
                            'property-code' => '{{CODE}}'
                        ]
                    ]) ?>
            </div>
        {{/PROPS}}
    <?= Html::endTag('div') ?>
{{/PROPS_SHOW}}