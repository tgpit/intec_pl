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
<?php $vOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$APPLICATION, &$component, &$sTemplateId, &$arVisual) { ?>
    <?php if ($arResult['ACTION'] === 'buy') { ?>
        <?php if (!$arItem['CAN_BUY']) { ?>
            <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                <?php if (!empty($arItem['OFFERS']) && !$bOffer) {
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
                    <span class="intec-ui-part-icon">
                        <i class="glyph-icon-cart"></i>
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
                        <i class="glyph-icon-cart"></i>
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
                <div class="intec-ui-part-icon">
                    <i class="glyph-icon-cart"></i>
                </div>
                <div class="intec-ui-part-content">
                    <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                </div>
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
                <div class="intec-ui-part-content">
                    <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
<?php } ?>


