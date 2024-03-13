<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTemplateId
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, &$arSvg, &$APPLICATION, &$component, &$sTemplateId) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arSvg, &$APPLICATION, &$component, &$sTemplateId) { ?>
        <?php if ($bOffer || $arItem['DATA']['ACTION'] === 'buy') { ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-section-item-order-buttons',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                ]) ?>
                    <?php if ($arVisual['BUTTON_TOGGLE']['ACTION'] === 'buy') { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'scheme-current',
                                    'state-hover',
                                    'state-active'
                                ],
                                'active'
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_PURCHASE'),
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID'],
                                'basket-action' => 'add',
                                'basket-state' => 'none',
                                'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                                'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                            ]
                        ]) ?>
                            <div class="intec-ui-part-icon">
                                <i class="glyph-icon-cart"></i>
                            </div>
                        <?= Html::endTag('div') ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'state-active'
                                ],
                                'intec-cl-text',
                                'added',
                                'active'
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_BASKET'),
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID']
                            ]
                        ]) ?>
                            <div class="intec-ui-part-icon">
                                <i class="glyph-icon-cart"></i>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui' => [
                                    '',
                                    'control-button',
                                    'state-active'
                                ],
                                'intec-cl-text-hover',
                                'active'
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_PURCHASE'),
                            'data-toggle' => 'open'
                        ]) ?>
                            <div class="intec-ui-part-icon">
                                <i class="glyph-icon-cart"></i>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-order-button',
                            'catalog-section-item-order-button-toggle',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-cl-text-hover'
                        ],
                        'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_PURCHASE_CLOSE'),
                        'data-toggle' => 'close'
                    ]) ?>
                        <div class="intec-ui-part-icon">
                            <i class="glyph-icon-cancel"></i>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') {

                if (!empty($arItem['OFFERS']) && $bOffer == false)
                    return;

            ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-order-buttons',
                        'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_SUBSCRIBE'),
                        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                    ]
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-subscribe',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-cl-text-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID'],
                            'ICON_USE' => 'Y',
                            'TEXT_SHOW' => 'N'
                        ],
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } else if ($arItem['DATA']['ACTION'] === 'detail') { ?>
            <div class="catalog-section-item-order-buttons">
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                    'class' => [
                        'catalog-section-item-order-button',
                        'catalog-section-item-order-button-detail',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_DETAIL'),
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <i class="far fa-ellipsis-h"></i>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'order') { ?>
            <div class="catalog-section-item-order-buttons">
                <?= Html::tag('div', $arSvg['PHONE'], [
                    'class' => [
                        'catalog-section-item-order-button',
                        'catalog-section-item-order-button-order',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current'
                        ],
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_ORDER'),
                    'data-role' => 'item.order'
                ]) ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
            <div class="catalog-section-item-order-buttons">
                <?= Html::tag('div', $arSvg['PHONE'], [
                    'class' => [
                        'catalog-section-item-order-button',
                        'catalog-section-item-order-button-order',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current'
                        ],
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_REQUEST'),
                    'data-role' => 'item.request'
                ]) ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem, false);

        if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'] && $arItem['DATA']['ACTION'] === 'buy') {
            foreach ($arItem['OFFERS'] as &$arOffer) {
                $fRender($arOffer, true);
            }

            unset($arOffer);
        }

    ?>
<?php } ?>