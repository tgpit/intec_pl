<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?php $vPurchase = function (&$arItem, $bMobile = false) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId) { ?>
    <?php $arParent = [
        'URL' => $arItem['DETAIL_PAGE_URL']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId, &$arParent, $bMobile) { ?>
        <?php if ($bOffer || $arItem['DATA']['ACTION'] === 'buy') { ?>
            <?php if ($arItem['DATA']['OFFER'] && !$bOffer)
                return;
            ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-section-item-purchase-buttons',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-ui',
                            'intec-ui-control-basket-button',
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-add',
                            'intec-cl-text',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-text-light-hover'
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
                            <?php if ($bMobile) { ?>
                                <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_ADD') ?>
                            <?php } else { ?>
                                <i class="glyph-icon-cart"></i>
                            <?php } ?>
                        </span>
                        <span class="intec-ui-part-effect intec-ui-part-effect-folding">
                            <?= Html::beginTag('span', [
                                'class' => Html::cssClassFromArray([
                                    'intec-ui-part-effect-wrapper' => true,
                                    'intec-cl-background' => $bMobile
                                ], true)
                            ]) ?>
                                <i></i><i></i><i></i><i></i>
                            <?= Html::endTag('span')?>
                        </span>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('a', [
                        'href' => $arResult['URL']['BASKET'],
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-added',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-background-light',
                            $bMobile ? 'intec-cl-text' : null
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none',
                        ]
                    ]) ?>
                        <?php if ($bMobile) { ?>
                            <span>
                                <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_ADDED') ?>
                            </span>
                        <?php } else { ?>
                            <i class="intec-basket glyph-icon-cart"></i>
                        <?php } ?>
                    <?= Html::endTag('a') ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                    <?= Html::beginTag('div', [
                        'class' => 'catalog-section-item-purchase-buttons',
                        'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                    ]) ?>
                        <?php $sMobile = $bMobile ? '_mobile' : '' ?>
                        <?php $APPLICATION->IncludeComponent(
                            'bitrix:catalog.product.subscribe',
                            '.default', [
                                'BUTTON_CLASS' => 'catalog-section-item-purchase-button intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current',
                                'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'].$sMobile,
                                'PRODUCT_ID' => $arItem['ID']
                            ],
                            $component
                        ) ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?php } ?>
        <?php } else if ($arItem['DATA']['ACTION'] === 'detail') { ?>
            <div class="catalog-section-item-purchase-buttons-detail">
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-detail',
                        'intec-cl-text',
                        $bMobile ? 'intec-cl-border' : 'intec-cl-text-light-hover'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                ]) ?>
                    <span>
                        <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_DETAIL') ?>
                    </span>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'order') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-add',
                        'intec-cl-text',
                        $bMobile ? 'intec-cl-border' : 'intec-cl-text-light-hover'
                    ],
                    'data-role' => 'item.order'
                ]) ?>
                    <?php if ($bMobile) { ?>
                        <span>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_ADD') ?>
                        </span>
                    <?php } else { ?>
                        <i class="intec-basket glyph-icon-cart"></i>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
            <?php if ($arItem['DATA']['OFFER']) { ?>
                <div class="catalog-section-item-purchase-buttons-detail">
                    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-detail',
                            'intec-cl-text',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-text-light-hover'
                        ],
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                        'data' => [
                            'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                            'type' => 'item.detail'
                        ]
                    ]) ?>
                        <span>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_DETAIL') ?>
                        </span>
                    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                </div>
            <?php } else { ?>
                <div class="catalog-section-item-purchase-buttons">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-add',
                            'intec-cl-text',
                            $bMobile ? 'intec-cl-border' : 'intec-cl-text-light-hover'
                        ],
                        'data-role' => 'item.request'
                    ]) ?>
                    <?php if ($bMobile) { ?>
                        <span>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TILE_1_BUTTON_REQUEST') ?>
                        </span>
                    <?php } else { ?>
                        <i class="intec-basket glyph-icon-cart"></i>
                    <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem, false);

        if ($arItem['DATA']['ACTION'] === 'buy' && $arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);

            if ($arVisual['COLUMNS']['MOBILE'] == 2) {
                $arItem['DATA']['ACTION'] = 'detail';
                $fRender($arItem, false);
            }
        }

    ?>
<?php } ?>