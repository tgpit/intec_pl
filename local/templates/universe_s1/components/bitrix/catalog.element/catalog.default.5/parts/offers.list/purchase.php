<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$arOffer) use (&$arResult, $sTemplateId, &$APPLICATION, &$component, &$arVisual) { ?>
    <div class="catalog-element-offers-list-item-buy">
        <?php if ($arResult['ACTION'] === 'buy') { ?>
            <?php if ($arOffer['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arOffer, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-offers-list-item-buy-button',
                        'catalog-element-offers-list-item-buy-add',
                        'intec-ui',
                        'intec-ui-control-basket-button',
                        'intec-cl-background',
                        'intec-cl-background-light-hover'
                    ],
                    'data' => [
                        'basket-id' => $arOffer['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-quantity' => $arOffer['CATALOG_MEASURE_RATIO'],
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
                        'catalog-element-offers-list-item-buy-button',
                        'catalog-element-offers-list-item-buy-added',
                        'intec-cl-background',
                        'intec-cl-background-light-hover'
                    ],
                    'href' => $arResult['URL']['BASKET'],
                    'data' => [
                        'basket-id' => $arOffer['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
            <?php } else { ?>
                <?php if ($arOffer['CATALOG_SUBSCRIBE'] === 'Y') { ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-element-offers-list-item-buy-button',
                                'catalog-element-offers-list-item-buy-subscribe',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_offer_'.$arOffer['ID'],
                            'PRODUCT_ID' => $arOffer['ID']
                        ],
                        $component
                    ) ?>
                <?php } else { ?>
                    <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_UNAVAILABLE_NORMAL'), [
                        'class' => [
                            'catalog-element-offers-list-item-buy-button',
                            'catalog-element-offers-list-item-buy-unavailable'
                        ]
                    ]) ?>
                <?php } ?>
            <?php } ?>
        <?php } else if ($arResult['ACTION'] === 'order') { ?>
            <?= Html::tag('div', $arVisual['BUTTONS']['ORDER']['TEXT'], [
                'class' => [
                    'catalog-element-offers-list-item-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'order'
            ]) ?>
        <?php } else if ($arResult['ACTION'] === 'request') { ?>
            <?= Html::tag('div', $arVisual['BUTTONS']['REQUEST']['TEXT'], [
                'class' => [
                    'catalog-element-offers-list-item-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'request'
            ]) ?>
        <?php } ?>
    </div>
<?php } ?>