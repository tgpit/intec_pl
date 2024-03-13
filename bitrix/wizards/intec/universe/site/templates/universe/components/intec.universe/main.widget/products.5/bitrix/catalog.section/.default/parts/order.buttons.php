<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId) { ?>
        <?php if ($bOffer || $arItem['VISUAL']['ACTION'] === 'buy') { ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <div class="widget-item-order-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
                    <?php if ($arVisual['BUTTON_TOGGLE']['ACTION'] === 'buy') { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item-order-button',
                                'widget-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-cl-background-hover',
                                'active'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_PURCHASE'),
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID'],
                                'basket-action' => 'add',
                                'basket-state' => 'none',
                                'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                                'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                            ]
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item-order-button',
                                'widget-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-cl-background',
                                'added',
                                'active'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_BASKET'),
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID']
                            ]
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item-order-button',
                                'widget-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-cl-background-hover',
                                'active'
                            ],
                            'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_PURCHASE'),
                            'data-toggle' => 'open'
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-item-order-button',
                            'widget-item-order-button-toggle',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-cl-text-hover'
                        ],
                        'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_PURCHASE_CLOSE'),
                        'data-toggle' => 'close'
                    ]) ?>
                        <i class="glyph-icon-cancel"></i>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } else if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                if (!empty($arItem['OFFERS']) && $bOffer == false)
                    return;

            ?>
                <div class="widget-item-order-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>" title="<?= Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_SUBSCRIBE') ?>">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'widget-item-order-button',
                                'widget-item-order-button-subscribe',
                                'intec-ui' => [
                                    '',
                                    'control-button'
                                ],
                                'intec-cl-text-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId . '_subscribe_' . $arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID'],
                            'ICON_USE' => 'Y',
                            'TEXT_SHOW' => 'N'
                        ],
                        $component
                    ) ?>
                </div>
            <?php } ?>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'detail') { ?>
            <div class="widget-item-order-buttons">
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                    'class' => [
                        'widget-item-order-button',
                        'widget-item-order-button-detail',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DETAIL'),
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <i class="far fa-ellipsis-h"></i>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'order') { ?>
            <div class="widget-item-order-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-order-button',
                        'widget-item-order-button-order',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_ORDER'),
                    'data-role' => 'item.order'
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/order.svg') ?>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else if ($arItem['VISUAL']['ACTION'] === 'request') { ?>
            <?php if ($arItem['VISUAL']['OFFER']) { ?>
                <div class="widget-item-order-buttons">
                    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                        'class' => [
                            'widget-item-order-button',
                            'widget-item-order-button-detail',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-cl-text-hover'
                        ],
                        'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DETAIL'),
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                    ]) ?>
                        <i class="far fa-ellipsis-h"></i>
                    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                </div>
            <?php } else { ?>
                <div class="widget-item-order-buttons">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-item-order-button',
                            'widget-item-order-button-order',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-scheme-current',
                        ],
                        'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_REQUEST'),
                        'data-role' => 'item.request'
                    ]) ?>
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/order.svg') ?>
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
        }

    ?>
<?php } ?>