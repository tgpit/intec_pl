<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Json;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var bool $bOffers
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vPanelPurchase = function (&$arItem, $bOffer = false) use (&$arResult, &$sTemplateId, &$APPLICATION, &$component, &$arVisual) { ?>
    <?php if (!empty($arItem['OFFERS']) && !$bOffer) return ?>
    <?php if ($arResult['ACTION'] === 'buy') { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-buy-container',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-panel-buy-button',
                        'catalog-element-panel-buy-add',
                        'intec-ui',
                        'intec-ui-control-basket-button',
                        'intec-cl-background',
                        'intec-cl-background-light-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null,
                        'basket-data' => Json::htmlEncode([
                            'additional' => true
                        ])
                    ]
                ]) ?>
                    <span class="intec-ui-part-content">
                        <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                    </span>
                    <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                        <span class="intec-ui-part-effect-wrapper">
                            <i></i><i></i><i></i>
                        </span>
                    </span>
                <?= Html::endTag('div') ?>
                <?= Html::tag('a', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_BASKET_ADDED'), [
                    'class' => [
                        'catalog-element-panel-buy-button',
                        'catalog-element-panel-buy-added',
                        'intec-cl-background',
                        'intec-cl-background-light-hover'
                    ],
                    'href' => $arResult['URL']['BASKET'],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
            <?php } else { ?>
                <?php if ($arItem['CATALOG_SUBSCRIBE'] === 'Y') { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-element-panel-buy-button',
                                'catalog-element-panel-buy-subscribe',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'panel_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                <?php } else { ?>
                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_UNAVAILABLE'), [
                        'class' => [
                            'catalog-element-panel-buy-button',
                            'catalog-element-panel-buy-unavailable'
                        ]
                    ]) ?>
                <?php } ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arResult['ACTION'] === 'order') { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-buy-container',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?= Html::tag('div', $arVisual['BUTTONS']['ORDER']['TEXT'], [
                'class' => [
                    'catalog-element-panel-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'order'
            ]) ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arResult['ACTION'] === 'request') { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-buy-container',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?= Html::tag('div', $arVisual['BUTTONS']['REQUEST']['TEXT'], [
                'class' => [
                    'catalog-element-panel-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'request'
            ]) ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
<?php $vPanelPurchase($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelPurchase($arOffer, true);

    unset($arOffer);
}

unset($vPanelPurchase) ?>
