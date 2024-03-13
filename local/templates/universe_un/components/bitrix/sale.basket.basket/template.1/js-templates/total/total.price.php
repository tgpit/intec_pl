<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

?>
<div class="intec-grid-item-auto intec-grid-item-600-1">
    <div class="basket-price">
        <?= Html::beginTag('div', [
            'class' => [
                'basket-price-wrapper'
            ]
        ]) ?>
        <div class="intec-grid intec-grid-a-h-start intec-grid-a-h-1200-between intec-grid-a-v-center intec-grid-a-v-320-start basket-price-info">
            <div class="intec-grid-item-auto basket-price-info-name">
                <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_TOTAL') ?>
            </div>

            <div class="intec-grid intec-grid-375-wrap">
                <?= Html::tag('div', '{{{PRICE_FORMATED}}}', [
                    'class' => 'intec-grid-item-auto intec-grid-item-360-1 basket-price-current',
                    'data-entity' => 'basket-total-price'
                ]) ?>
                {{#DISCOUNT_PRICE_FORMATED}}
                <div class="intec-grid-item-auto intec-grid-item-360-1 basket-price-discount">
                    {{{PRICE_WITHOUT_DISCOUNT_FORMATED}}}
                </div>
                {{/DISCOUNT_PRICE_FORMATED}}
            </div>
        </div>
        <div class="intec-grid intec-grid-wrap intec-grid-a-h-between intec-grid-a-v-center basket-price-values">
            <div class="intec-grid-item-auto intec-grid-item-360-1 basket-price-info-values">
                {{#WEIGHT_FORMATED}}
                <div class="basket-price-info-value">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_WEIGHT').' {{{WEIGHT_FORMATED}}}' ?>
                </div>
                {{/WEIGHT_FORMATED}}
                {{#SHOW_VAT}}
                <div class="basket-price-info-value">
                    <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_VAT').' {{{VAT_SUM_FORMATED}}}' ?>
                </div>
                {{/SHOW_VAT}}
            </div>
            {{#DISCOUNT_PRICE_FORMATED}}
            <div class="intec-grid-item-auto basket-price-economy">
                <div class="basket-price-economy-wrapper">
                    <span class="basket-price-economy-name">
                        <?= Loc::getMessage('C_BASKET_DEFAULT_1_TEMPLATE_TOTAL_ECONOMY') ?>
                    </span>
                    <span class="basket-price-economy-value">
                        {{{DISCOUNT_PRICE_FORMATED}}}
                    </span>
                </div>
            </div>
            {{/DISCOUNT_PRICE_FORMATED}}
        </div>
        <?= Html::endTag('div') ?>
    </div>
</div>