<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var bool $bOffers
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vPanelMobileButton = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component, $arSvg) { ?>
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
                    'catalog-element-panel-mobile-buy-button',
                    'catalog-element-panel-mobile-buy-add',
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
                <span class="catalog-element-panel-mobile-button-content intec-ui-part-content">
                    <?= $arSvg['BUTTONS']['BASKET'] ?>
                    <span class="catalog-element-panel-mobile-button-text">
                        <?= Html::beginTag('span', [
                            'class' => 'catalog-element-panel-mobile-price-content',
                            'data' => [
                                'role' => 'price',
                                'show' => !empty($arPrice) ? 'true' : 'false',
                                'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
                            ]
                        ]) ?>
                            <?php if ($arVisual['PRICE']['DISCOUNT']['OLD']) { ?>
                                <span class="catalog-element-panel-mobile-price-base" data-role="price.base">
                                    <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                                </span>
                            <?php } ?>
                            <span class="catalog-element-panel-mobile-price-discount" data-role="price.discount">
                                <?= !empty($arPrice) ? $arPrice['PRINT_DISCOUNT'] : null ?>
                            </span>
                        <?= Html::endTag('span') ?>
                    </span>
                </span>
                <span class="intec-ui-part-effect intec-ui-part-effect-bounce">
                    <span class="intec-ui-part-effect-wrapper">
                        <i></i><i></i><i></i>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
            <?= Html::beginTag('a', [
                'class' => [
                    'catalog-element-panel-mobile-buy-button',
                    'catalog-element-panel-mobile-buy-added',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'href' => $arResult['URL']['BASKET'],
                'data' => [
                    'basket-id' => $arItem['ID'],
                    'basket-state' => 'none'
                ]
            ]) ?>
                <span class="catalog-element-panel-mobile-button-content">
                    <?= $arSvg['BUTTONS']['BASKET'] ?>
                    <span class="catalog-element-panel-mobile-button-text">
                        <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                    </span>
                </span>
            <?= Html::endTag('a') ?>
        <?php } else { ?>
            <?php if ($arItem['CATALOG_SUBSCRIBE'] === 'Y') { ?>
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.product.subscribe',
                    '.default', [
                    'BUTTON_CLASS' => Html::cssClassFromArray([
                        'catalog-element-panel-mobile-buy-subscribe',
                        'catalog-element-panel-mobile-buy-button',
                        'intec-cl-background',
                        'intec-cl-background-light-hover'
                    ]),
                    'BUTTON_ID' => $sTemplateId.'panel_mobile_subscribe_'.$arItem['ID'],
                    'PRODUCT_ID' => $arItem['ID']
                ],
                    $component
                ) ?>
            <?php } else { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-panel-mobile-buy-button',
                        'catalog-element-panel-mobile-buy-unavailable'
                    ],
                    'data-counter' => $arVisual['COUNTER']['SHOW'] ? 'true' : 'false'
                ]) ?>
                    <span class="catalog-element-panel-mobile-button-content intec-ui-part-content">
                        <span>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_BUY_BUTTON_UNAVAILABLE') ?>
                        </span>
                    </span>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arResult['ACTION'] === 'order') { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-panel-mobile-buy-container',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-mobile-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'order'
            ]) ?>
                <span class="catalog-element-panel-mobile-button-content">
                    <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                </span>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
    <?php } else if ($arResult['ACTION'] === 'request') { ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-panel-mobile-buy-container',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-panel-mobile-buy-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'request'
            ]) ?>
                <span class="catalog-element-panel-mobile-button-content">
                    <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                </span>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?php } ?>
<?php $vPanelMobileButton($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelMobileButton($arOffer, true);

    unset($arOffer);
}

unset($vPanelMobileButton); ?>