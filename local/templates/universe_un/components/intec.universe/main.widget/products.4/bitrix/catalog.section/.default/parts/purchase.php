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
<?php return function (&$arItem, $bMobile = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) {

    $arParent = [
        'URL' => $arItem['DETAIL_PAGE_URL']
    ];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, $bMobile, &$arVisual, &$APPLICATION, &$component, &$sTemplateId, &$arParent) { ?>
        <?php if ($bOffer || $arItem['VISUAL']['ACTION'] === 'buy') {

            if ($arItem['VISUAL']['OFFER'] && !$bOffer)
                return;

        ?>
            <?php if ($arItem['CAN_BUY']) {

                $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]);

            ?>
                <?= Html::beginTag('div', [
                    'class' => 'widget-item-purchase-buttons',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false' 
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-ui',
                            'intec-ui-control-basket-button',
                            'widget-item-purchase-button',
                            'widget-item-purchase-button-add',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                            $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
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
                            <?= Html::beginTag('span', [
                                'class' => Html::cssClassFromArray([
                                    'intec-ui-part-effect-wrapper' => true,
                                    'intec-cl-background' => $bMobile
                                ], true)
                            ]) ?>
                                <i></i><i></i><i></i>
                            <?= Html::endTag('span') ?>
                        </span>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('a', [
                        'class' => [
                            'widget-item-purchase-button',
                            'widget-item-purchase-button-added',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background-light',
                            $bMobile ? 'intec-cl-text' : null
                        ],
                        'href' => $arResult['URL']['BASKET'],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none'
                        ]
                    ]) ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_BASKET_ADDED') ?>
                    <?= Html::endTag('a') ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'widget-item-purchase-buttons',
                        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                    ]) ?>
                        <?php $sMobile = $bMobile ? '_mobile' : '' ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '.default', [
                                'BUTTON_CLASS' => Html::cssClassFromArray([
                                    'widget-item-purchase-button',
                                    $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                                    $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                                ]),
                                'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'].$sMobile,
                                'PRODUCT_ID' => $arItem['ID']
                            ],
                            $component
                        ) ?>
                    <?= Html::endTag('div') ?>
                <?php } else { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'widget-item-purchase-buttons',
                        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                    ]) ?>
                        <?= Html::tag('div', Loc::getMessage('C_WIDGET_PRODUCTS_4_UNAVAILABLE'), [
                            'class' => [
                                'widget-item-purchase-button',
                                $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                                $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_4_UNAVAILABLE')
                        ]) ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } ?>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'detail') { ?>
            <div class="widget-item-purchase-detail">
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'class' => [
                        'widget-item-purchase-button',
                        $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                        $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_MORE_INFO') ?>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'order') { ?>
            <div class="widget-item-purchase-order">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-purchase-button',
                        $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                        $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                    ],
                    'data-role' => 'item.order'
                ]) ?>
                    <span>
                        <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'request') { ?>
            <?php if ($arItem['VISUAL']['OFFER']) { ?>
                <div class="widget-item-purchase-detail">
                    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                        'class' => [
                            'widget-item-purchase-button',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                            $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                        ],
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                    ]) ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_MORE_INFO') ?>
                    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                </div>
            <?php } else { ?>
                <div class="widget-item-purchase-order">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-item-purchase-button',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                            $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                        ],
                        'data-role' => 'item.request'
                    ]) ?>
                        <span>
                            <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                        </span>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem, false);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && $arItem['VISUAL']['ACTION'] === 'buy') {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
    <?php if ($arVisual['COLUMNS']['MOBILE'] == 2) { ?>
        <div class="widget-item-purchase-detail mobile">
            <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                'class' => [
                    'widget-item-purchase-button',
                    $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                    $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                ],
                'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
            ]) ?>
                <?= Loc::getMessage('C_WIDGET_PRODUCTS_4_MORE_INFO') ?>
            <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
        </div>
    <?php } ?>
<?php } ?>