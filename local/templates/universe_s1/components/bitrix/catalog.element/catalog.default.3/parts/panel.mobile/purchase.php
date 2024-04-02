<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;
use intec\core\bitrix\Component;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vPanelOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <?php if ($bOffer || $arResult['ACTION'] === 'buy') { ?>
        <?php if ($arItem['CAN_BUY']) { ?>
            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-element-purchase-buttons',
                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-ui',
                        'intec-ui-control-basket-button',
                        'catalog-element-purchase-button',
                        'catalog-element-purchase-button-add',
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
                    <div class="catalog-element-purchase-button-content intec-ui-part-content">
                        <i class="glyph-icon-cart"></i>
                        <div class="catalog-element-purchase-button-text">
                            <?= Html::beginTag('div', [
                                'class' => 'catalog-element-panel-mobile-price-content',
                                'data' => [
                                    'role' => 'price',
                                    'show' => !empty($arPrice) ? 'true' : 'false',
                                    'discount' => !empty($arPrice) && $arPrice['PERCENT'] > 0 ? 'true' : 'false'
                                ]
                            ]) ?>
                            <div class="catalog-element-panel-mobile-price-discount" data-role="price.discount">
                                <?= !empty($arPrice) ? $arPrice['PRINT_DISCOUNT'] : null ?>
                            </div>
                            <div class="catalog-element-panel-mobile-price-percent-wrap">
                                <div class="catalog-element-panel-mobile-price-base" data-role="price.base">
                                    <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
                                </div>
                            </div>
                            <?= Html::endTag('div') ?>
                        </div>
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
                        'catalog-element-purchase-button',
                        'catalog-element-purchase-button-added',
                        'intec-cl-background-light'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <span class="catalog-element-purchase-button-content">
                        <i class="glyph-icon-cart"></i>
                        <span>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_ADDED') ?>
                        </span>
                    </span>
                <?= Html::endTag('a') ?>
            <?= Html::endTag('div') ?>
        <?php } else { ?>
            <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                if (!empty($arItem['OFFERS']) && $bOffer == false)
                    return;
                $sSubscribeId = Html::getUniqueId(null, Component::getUniqueId($this));
            ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-purchase-subscribe',
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
                            'BUTTON_ID' => $sSubscribeId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-element-purchase-buttons',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-purchase-button',
                            'catalog-element-purchase-button-unavailable',
                            'intec-cl-background'
                        ],
                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_UNAVAILABLE')
                    ]) ?>
                        <span class="catalog-element-purchase-button-content">
                            <i class="far fa-times-circle"></i>
                            <span>
                                <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_PURCHASE_UNAVAILABLE') ?>
                            </span>
                        </span>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } ?>
    <?php } else if ($arResult['ACTION'] === 'order') { ?>
        <div class="catalog-section-item-purchase-buttons">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-purchase-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'order'
            ]) ?>
                <span class="catalog-element-purchase-button-content">
                    <span>
                        <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } else if ($arResult['ACTION'] === 'request') { ?>
        <div class="catalog-section-item-purchase-buttons">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-purchase-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'request'
            ]) ?>
                <span class="catalog-element-purchase-button-content">
                    <span>
                        <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                    </span>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
<?php } ?>
<?php $vPanelOrder($arResult);

if (!empty($arResult['OFFERS']) && $arResult['ACTION'] === 'buy') {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelOrder($arOffer, true);

    unset($arOffer);
}

unset($vPanelOrder) ?>