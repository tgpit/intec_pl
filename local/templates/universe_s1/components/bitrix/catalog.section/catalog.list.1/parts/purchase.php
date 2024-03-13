<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

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
<?php $vPurchase = function (&$arItem) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <?php $arParentValues = [
        'URL' => $arItem['DETAIL_PAGE_URL']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParentValues, &$sTemplateId, &$APPLICATION, &$component) { ?>
        <?php if ($bOffer || $arItem['DATA']['ACTION'] === 'buy') { ?>
            <?php if ($arItem['DATA']['OFFER'] && !$arVisual['OFFER']['USE'] && !$bOffer)
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
                            'intec-ui-control-button',
                            'intec-ui-mod-transparent',
                            'intec-ui-scheme-current'
                        ],
                        'data' => [
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
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-added',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-scheme-current',
                            'intec-ui-state-hover',
                            'hover'
                        ],
                        'href' => $arResult['URL']['BASKET'],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none'
                        ]
                    ]) ?>
                        <div class="intec-ui-part-icon">
                            <i class="glyph-icon-cart"></i>
                        </div>
                        <div class="intec-ui-part-content">
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_BUTTON_ADDED') ?>
                        </div>
                    <?= Html::endTag('a') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                <?= Html::beginTag('div', [
                    'class' => 'catalog-section-item-purchase-buttons',
                    'data-offer' => $bOffer ? $arItem['ID'] : 'false'
                ]) ?>
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-section-item-purchase-button',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-ui-mod-transparent',
                                'intec-ui-scheme-current'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?php } else if ($arItem['DATA']['ACTION'] === 'detail') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-detail',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-mod-transparent',
                        'intec-ui-scheme-current'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParentValues['URL'] : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                ]) ?>
                    <span>
                        <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_BUTTON_DETAIL') ?>
                    </span>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'order') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-order',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-mod-transparent',
                        'intec-ui-scheme-current'
                    ],
                    'data-role' => 'item.order'
                ]) ?>
                    <span>
                        <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?php if ($arItem['DATA']['OFFER']) { ?>
                    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-detail',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-mod-transparent',
                            'intec-ui-scheme-current'
                        ],
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParentValues['URL'] : null,
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link'
                    ]) ?>
                        <span>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_LIST_1_BUTTON_DETAIL') ?>
                        </span>
                    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                <?php }  else { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-order',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-mod-transparent',
                            'intec-ui-scheme-current'
                        ],
                        'data-role' => 'item.request'
                    ]) ?>
                        <span>
                            <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                        </span>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arItem['DATA']['ACTION'] === 'buy' && $arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'])
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

    ?>
<?php } ?>