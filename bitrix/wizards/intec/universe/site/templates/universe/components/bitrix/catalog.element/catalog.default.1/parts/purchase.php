<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-purchase" data-role="purchase">
    <?php if ($arVisual['COUNTER']['SHOW']) { ?>
        <!--noindex-->
            <div class="catalog-element-purchase-counter">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-purchase-counter-control',
                        'intec-ui' => [
                            '',
                            'control-numeric',
                            'scheme-current',
                            'view-1'
                        ]
                    ],
                    'data-role' => 'counter'
                ]) ?>
                    <?= Html::tag('a', '-', [
                        'class' => 'intec-ui-part-decrement',
                        'href' => 'javascript:void(0)',
                        'data-type' => 'button',
                        'data-action' => 'decrement'
                    ]) ?>
                    <?= Html::input('text', null, 0, [
                        'data-type' => 'input',
                        'class' => 'intec-ui-part-input'
                    ]) ?>
                    <div class="intec-ui-part-increment-wrapper">
                        <?= Html::tag('a', '+', [
                            'class' => 'intec-ui-part-increment',
                            'href' => 'javascript:void(0)',
                            'data-type' => 'button',
                            'data-action' => 'increment'
                        ]) ?>

                        <div class="catalog-element-purchase-counter-control-max-message" data-role="max-message">
                            <div class="catalog-element-purchase-counter-control-max-message-close" data-role="max-message-close">
                                &times;
                            </div>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_MAX_MESSAGE') ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
                <div class="catalog-element-purchase-counter-quantity">
                    <?php $vMeasure = function (&$arItem, $bOffer = false) {

                        if (empty($arItem['CATALOG_MEASURE_NAME']) || !empty($arItem['OFFERS']))
                            return;

                        ?>
                        <?= Html::tag('div', $arItem['CATALOG_MEASURE_NAME'].'.', [
                            'class' => 'catalog-element-purchase-counter-quantity-wrapper',
                            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                        ]) ?>
                    <?php } ?>
                    <?php $vMeasure($arResult);

                    if (!empty($arResult['OFFERS'])) {
                        foreach ($arResult['OFFERS'] as &$arOffer)
                            $vMeasure($arOffer, true);

                        unset($arOffer);
                    }

                    unset($vMeasure) ?>
                </div>
            </div>
        <!--/noindex-->
    <?php } ?>
    <div class="catalog-element-purchase-order">
        <?php if ($arResult['ACTION'] !== 'none') { ?>
            <?php $vOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$APPLICATION, &$component, &$sTemplateId, &$arVisual) { ?>
                <?php if ($arResult['ACTION'] === 'buy') { ?>
                    <?php if (!$arItem['CAN_BUY']) { ?>
                        <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                            if (!empty($arItem['OFFERS']) && $bOffer == false) {
                                return;

                        } ?>
                            <?= Html::beginTag('div', [
                                'class' => 'catalog-element-purchase-order-subscribe',
                                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                            ]) ?>
                                <?php $APPLICATION->IncludeComponent(
                                    'bitrix:catalog.product.subscribe',
                                    '.default', [
                                    'BUTTON_CLASS' => Html::cssClassFromArray([
                                        'intec-ui' => [
                                            '',
                                            'control-button',
                                            'scheme-current',
                                            'size-4'
                                        ]
                                    ]),
                                    'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                                    'PRODUCT_ID' => $arItem['ID']
                                ],
                                    $component
                                ) ?>
                            <?= Html::endTag('div') ?>
                        <?php } else {
                            return;
                        } ?>
                    <?php } else { ?>
                        <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-element-purchase-order-buttons',
                            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-element-purchase-order-button',
                                    'catalog-element-purchase-order-button-add',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'control-basket-button',
                                        'scheme-current',
                                        'size-4',
                                        'mod-block'
                                    ]
                                ],
                                'data' => [
                                    'basket-id' => $arItem['ID'],
                                    'basket-action' => 'add',
                                    'basket-state' => 'none',
                                    'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                                    'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                                ]
                            ]) ?>
                                <div class="intec-ui-part-icon">
                                    <i class="button-icon glyph-icon-cart"></i>
                                </div>
                                <div class="intec-ui-part-content">
                                    <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                                </div>
                                <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                                    <span class="intec-ui-part-effect-wrapper">
                                        <i></i><i></i><i></i>
                                    </span>
                                </span>
                            <?= Html::endTag('div') ?>
                            <?= Html::beginTag('a', [
                                'href' => $arResult['URL']['BASKET'],
                                'class' => [
                                    'catalog-element-purchase-order-button',
                                    'catalog-element-purchase-order-button-added',
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'scheme-current',
                                        'size-4',
                                        'mod-block',
                                        'state-hover'
                                    ]
                                ],
                                'data' => [
                                    'basket-id' => $arItem['ID'],
                                    'basket-state' => 'none'
                                ]
                            ]) ?>
                                <span class="intec-ui-part-icon">
                                    <i class="button-icon glyph-icon-cart"></i>
                                </span>
                                <span class="intec-ui-part-content">
                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_PURCHASE_ORDERED') ?>
                                </span>
                            <?= Html::endTag('a') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?php } else if ($arResult['ACTION'] === 'order') { ?>
                    <div class="catalog-element-purchase-order-buttons">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-purchase-order-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'size-4',
                                    'mod-block'
                                ]
                            ],
                            'data-role' => 'order'
                        ]) ?>
                            <span class="intec-ui-part-icon">
                                <i class="button-icon glyph-icon-cart"></i>
                            </span>
                            <span class="intec-ui-part-content">
                                <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                            </span>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } else if ($arResult['ACTION'] === 'request') { ?>
                    <div class="catalog-element-purchase-order-buttons">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-purchase-order-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'size-4',
                                    'mod-block'
                                ]
                            ],
                            'data-role' => 'request'
                        ]) ?>
                            <span class="intec-ui-part-content">
                                <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                            </span>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            <?php } ?>
            <?php $vOrder($arResult);

            if (!empty($arResult['OFFERS']) && $arResult['ACTION'] === 'buy') {
                foreach ($arResult['OFFERS'] as &$arOffer)
                    $vOrder($arOffer, true);

                unset($arOffer);
            }

            unset($vOrder) ?>
            <?php if ($arResult['ORDER_FAST']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-purchase-order-fast',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-link'
                        ]
                    ],
                    'data-role' => 'orderFast'
                ]) ?>
                    <div class="intec-ui-part-icon">
                        <i class="button-icon glyph-icon-one_click"></i>
                    </div>
                    <div class="intec-ui-part-content button-text">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_PURCHASE_ORDER_FAST') ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } ?>
    </div>
    <div class="catalog-element-purchase-buttons">
        <!--noindex-->
        <?php $vButtons = function (&$arItem, $bOffer = false) use (&$arResult) {

            if (!$arResult['COMPARE']['USE'] && !$arResult['DELAY']['USE'])
                return;

            if (!empty($arItem['OFFERS']))
                return;

        ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-element-purchase-buttons-wrapper',
                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
            ]) ?>
                <?php if ($arResult['COMPARE']['USE']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-purchase-button',
                            'catalog-element-purchase-button-compare',
                            'intec-cl-text-hover'
                        ],
                        'data' => [
                            'compare-id' => $arItem['ID'],
                            'compare-action' => 'add',
                            'compare-code' => $arResult['COMPARE']['CODE'],
                            'compare-state' => 'none',
                            'compare-iblock' => $arResult['IBLOCK_ID']
                        ]
                    ]) ?>
                        <i class="glyph-icon-compare"></i>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-purchase-button',
                            'catalog-element-purchase-button-compared',
                            'intec-cl-text'
                        ],
                        'data' => [
                            'compare-id' => $arItem['ID'],
                            'compare-action' => 'remove',
                            'compare-code' => $arResult['COMPARE']['CODE'],
                            'compare-state' => 'none',
                            'compare-iblock' => $arResult['IBLOCK_ID']
                        ]
                    ]) ?>
                        <i class="glyph-icon-compare"></i>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php if ($arResult['DELAY']['USE'] && $arItem['CAN_BUY'] && ($bOffer || empty($arItem['OFFERS']))) { ?>
                    <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-purchase-button',
                            'catalog-element-purchase-button-delay',
                            'intec-cl-text-hover'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-action' => 'delay',
                            'basket-state' => 'none',
                            'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                        ]
                    ]) ?>
                        <i class="fas fa-heart"></i>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-purchase-button',
                            'catalog-element-purchase-button-delayed',
                            'intec-cl-text'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-action' => 'remove',
                            'basket-state' => 'none'
                        ]
                    ]) ?>
                        <i class="fas fa-heart"></i>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
        <?php $vButtons($arResult);

        if (!empty($arResult['OFFERS'])) {
            foreach ($arResult['OFFERS'] as &$arOffer)
                $vButtons($arOffer, true);

            unset($arOffer);
        }

        unset($vButtons) ?>
        <!--/noindex-->
    </div>
</div>