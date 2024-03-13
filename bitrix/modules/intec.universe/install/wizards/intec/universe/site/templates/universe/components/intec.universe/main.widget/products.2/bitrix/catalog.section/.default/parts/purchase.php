<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

?>
<?php return function (&$arItem, $bMobile = false) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) {

    $sLink = $arItem['DETAIL_PAGE_URL'];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$bMobile, &$arVisual, &$sTemplateId, &$sLink, &$APPLICATION, &$component) { ?>
        <?php if ($bOffer || $arItem['VISUAL']['ACTION'] === 'buy') { ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <div class="widget-item-purchase-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
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
                            <?= Html::endTag('span')?>
                        </span>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('a', [
                        'href' => $arResult['URL']['BASKET'],
                        'class' => [
                            'widget-item-purchase-button',
                            'widget-item-purchase-button-added',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background-light',
                            $bMobile ? 'intec-cl-text' : null
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none'
                        ]
                    ]) ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_2_BASKET_ADDED') ?>
                    <?= Html::endTag('a') ?>
                </div>
            <?php } else { ?>
                <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                    if ($arItem['VISUAL']['OFFER'] && !$bOffer)
                        return;

                ?>
                    <div class="widget-item-purchase-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
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
                    </div>
                <?php } else { ?>
                    <div class="widget-item-purchase-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item-purchase-button',
                                $bMobile ? 'intec-cl-border' : 'intec-cl-background',
                                $bMobile ? 'intec-cl-text' : 'intec-cl-background-light-hover'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_2_UNAVAILABLE')
                        ]) ?>
                            <?= Loc::getMessage('C_WIDGET_PRODUCTS_2_UNAVAILABLE') ?>
                        <?= Html::endTag('div') ?>
                    </div>
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
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? Html::decode($sLink) : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                ]) ?>
                    <?= Loc::getMessage('C_WIDGET_PRODUCTS_2_MORE_INFO') ?>
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
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? Html::decode($sLink) : null,
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                    ]) ?>
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_2_MORE_INFO') ?>
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

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && $arItem['VISUAL']['ACTION'] === 'buy') {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);

            if ($bMobile) {
                $arItem['VISUAL']['ACTION'] = 'detail';
                $fRender($arItem, false);
            }
        }

    ?>
<?php } ?>