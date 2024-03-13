<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 */

$arPrice = [
    'CURRENT' => $arResult['SET_ITEMS']['PRICE'],
    'OLD' => null,
    'DIFFERENCE' => $arResult['SET_ITEMS']['PRICE_DISCOUNT_DIFFERENCE']
];

if ($arResult['SET_ITEMS']['OLD_PRICE'] > 0)
    $arPrice['OLD'] = $arResult['SET_ITEMS']['OLD_PRICE'];
else
    $arPrice['OLD'] = $arResult['SET_ITEMS']['PRICE'];

?>
<div class="constructor-main-total">
    <div class="constructor-main-total-price">
        <?= Html::tag('div', $arPrice['OLD'], [
            'class' => [
                'constructor-main-total-price-old',
                'constructor-main-total-price-item'
            ],
            'data-role' => 'total.price.old'
        ]) ?>
        <?= Html::tag('div', $arPrice['CURRENT'], [
            'class' => [
                'constructor-main-total-price-current',
                'constructor-main-total-price-item'
            ],
            'data-role' => 'total.price.current'
        ]) ?>
        <div class="constructor-main-total-economy" data-role="total.price.difference">
            <div class="constructor-main-total-economy-content">
                <?= Html::tag('div', $arPrice['DIFFERENCE'], [
                    'class' => [
                        'constructor-main-total-economy-value',
                        'constructor-main-total-economy-item'
                    ],
                    'data-role' => 'total.price.difference.value'
                ]) ?>
                <?= Html::tag('div', $arSvg['TOTAL']['ECONOMY'], [
                    'class' => [
                        'constructor-main-total-economy-icon',
                        'constructor-main-total-economy-item'
                    ]
                ]) ?>
            </div>
        </div>
    </div>
    <div class="constructor-main-total-buy">
        <?= Html::tag('div', Loc::getMessage('C_CONSTRUCTOR_SET_TEMPLATE_1_TEMPLATE_BUTTON_BUY'), [
            'class' => [
                'constructor-main-total-buy-button',
                'intec-cl-background',
                'intec-cl-background-light-hover'
            ],
            'data' => [
                'role' => 'set.buy',
                'active' => 'false'
            ],
        ]) ?>
    </div>
</div>
