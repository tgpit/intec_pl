<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vPurchase = function (&$arItem) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId) { ?>
    <?php $fRender = function (&$arItem) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId, &$arParentValues) { ?>
        <?php if (!empty($arItem['OFFERS'])) { ?>
            <?= Html::beginTag('a', [
                'class' => [
                    'widget-item-purchase-detail-button',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'mod-transparent',
                        'scheme-current'
                    ]
                ],
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
                <?= Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MORE_INFO') ?>
            <?= Html::endTag('a') ?>
        <?php } else { ?>
            <?php if ($arItem['ACTION'] === 'buy') { ?>
                <?php if ($arItem['CAN_BUY']) { ?>
                    <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                    <?= Html::beginTag('div', [
                        'class' => 'widget-item-purchase-buttons',
                        'data-offer' => 'false'
                    ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-ui',
                            'intec-ui-control-basket-button',
                            'widget-item-purchase-button',
                            'widget-item-purchase-button-add',
                            'intec-cl-background',
                            'intec-cl-background-light-hover'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-action' => 'add',
                            'basket-state' => 'none',
                            'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                            'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
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
                    <?= Html::beginTag('a', [
                        'class' => [
                            'widget-item-purchase-button',
                            'widget-item-purchase-button-added',
                            'intec-cl-background-light'
                        ],
                        'href' => $arResult['URL']['BASKET'],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none'
                        ]
                    ]) ?>
                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_BASKET_ADDED') ?>
                    <?= Html::endTag('a') ?>
                    <?= Html::endTag('div') ?>
                <?php } else { ?>
                    <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'widget-item-purchase-buttons',
                            'data-offer' => 'false'
                        ]) ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'widget-item-purchase-button',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                            $component
                        ) ?>
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'widget-item-purchase-buttons',
                            'data-offer' => 'false'
                        ]) ?>
                        <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_UNAVAILABLE'), [
                            'class' => [
                                'widget-item-purchase-button',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_UNAVAILABLE')
                        ]) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?php } ?>
            <?php } else if ($arItem['ACTION'] === 'detail') { ?>
                <div class="widget-item-purchase-detail">
                    <?= Html::beginTag('a', [
                        'class' => [
                            'widget-item-purchase-button',
                            'intec-cl-background',
                            'intec-cl-background-light-hover'
                        ],
                        'href' => $arItem['DETAIL_PAGE_URL']
                    ]) ?>
                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_SMALL_1_MORE_INFO') ?>
                    <?= Html::endTag('a') ?>
                </div>
            <?php } else if ($arItem['ACTION'] === 'order') { ?>
                <div class="widget-item-purchase-order">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-item-purchase-button',
                            'intec-cl-background',
                            'intec-cl-background-light-hover'
                        ],
                        'data-role' => 'item.order'
                    ]) ?>
                        <span>
                            <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                        </span>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php $fRender($arItem) ?>
<?php } ?>