<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<div class="catalog-element-purchase" data-role="purchase" data-print="false">
    <?php if ($arVisual['COUNTER']['SHOW']) { ?>
        <div class="catalog-element-purchase-counter">
            <!--noindex-->
                <div class="catalog-element-purchase-counter-control intec-ui intec-ui-control-numeric intec-ui-view-2 intec-ui-scheme-current intec-ui-size-4" data-role="counter">
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
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_MAX_MESSAGE') ?>
                        </div>
                    </div>
                </div>
                <div class="catalog-element-purchase-counter-quantity">
                <?php $vMeasure = function (&$arItem, $bOffer = false) {
                        if (empty($arItem['CATALOG_MEASURE_NAME']))
                            return;
                ?>
                    <div class="catalog-element-purchase-counter-quantity-wrapper" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
                        <?= $arItem['CATALOG_MEASURE_NAME'].'.' ?>
                    </div>
                <?php } ?>
                <?php $vMeasure($arResult);

                if (!empty($arResult['OFFERS']))
                    foreach ($arResult['OFFERS'] as &$arOffer) {
                        $vMeasure($arOffer, true);

                        unset($arOffer);
                    }

                unset($vMeasure) ?>
                </div>
            <!--/noindex-->
        </div>
    <?php } ?>
    <div class="catalog-element-purchase-order">
        <?php $vOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
            <?php if ($arResult['ACTION'] === 'buy') { ?>
                <?php if (!$arItem['CAN_BUY']) { ?>
                    <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                        if (!empty($arItem['OFFERS']) && $bOffer == false)
                            return;

                    ?>
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
                                    'mod-block',
                                    'size-4'
                                ]
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
                            <span class="intec-ui-part-icon">
                                <i class="button-icon glyph-icon-cart"></i>
                            </span>
                            <span class="intec-ui-part-content">
                                <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                            </span>
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
                                    'mod-block',
                                    'size-4',
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
                                <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PURCHASE_ORDERED') ?>
                            </span>
                        <?= Html::endTag('a') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } else if ($arResult['ACTION'] === 'order') { ?>
                <?php if ($arResult['FORM']['ORDER']['SHOW']) { ?>
                    <div class="catalog-element-purchase-order-buttons">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-purchase-order-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-block',
                                    'size-4'
                                ]
                            ],
                            'data-role' => 'order'
                        ]) ?>
                            <div class="intec-ui-part-icon">
                                <i class="button-icon glyph-icon-cart"></i>
                            </div>
                            <div class="intec-ui-part-content">
                                <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            <?php } else if ($arResult['ACTION'] === 'request') { ?>
                <?php if ($arResult['FORM']['REQUEST']['SHOW']) { ?>
                    <div class="catalog-element-purchase-order-buttons">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-purchase-order-button',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'mod-block',
                                    'size-4'
                                ]
                            ],
                            'data-role' => 'request'
                        ]) ?>
                            <div class="intec-ui-part-content">
                                <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
        <?php $vOrder($arResult);

        if (!empty($arResult['OFFERS']) && $arResult['ACTION'] === 'buy')
            foreach ($arResult['OFFERS'] as &$arOffer) {
                $vOrder($arOffer, true);

                unset($arOffer);
            }

        unset($vOrder) ?>
    </div>
    <?php if ($arResult['ORDER_FAST']['USE']) { ?>
        <div class="catalog-element-purchase-fast">
            <?= Html::beginTag('div', [
                'class' => [
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
                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PURCHASE_ORDER_FAST') ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
</div>