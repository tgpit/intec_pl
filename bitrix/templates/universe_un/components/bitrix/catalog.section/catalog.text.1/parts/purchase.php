<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $compponent
 */

?>
<?php $vPurchase = function (&$arItem) use (&$arResult, &$sTemplateId, &$APPLICATION, &$component, &$arVisual) { ?>
    <?php $fRender = function (&$arItem) use (&$arResult, &$sTemplateId, &$APPLICATION, &$component, &$arVisual) { ?>
        <?php if ($arItem['DATA']['ACTION'] === 'buy') { ?>
            <?php if (!empty($arItem['OFFERS'])) return ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <div class="catalog-section-item-purchase-buttons">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'intec-ui',
                            'intec-ui-control-basket-button',
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-add',
                            'intec-ui-control-button',
                            'intec-ui-mod-transparent',
                            'intec-ui-scheme-current',
                            'intec-ui-mod-round-2'
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
                        <div class="intec-ui-part-content">
                            <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
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
                            'catalog-section-item-purchase-button',
                            'catalog-section-item-purchase-button-added',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-scheme-current',
                            'intec-ui-mod-round-2',
                            'intec-ui-state-hover',
                            'hover'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-state' => 'none',
                        ]
                    ]) ?>
                    <div class="intec-ui-part-content">
                        <i class="glyph-icon-cart"></i>
                        <?=Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_1_BUTTON_ADDED')?>
                    </div>
                    <?= Html::endTag('a') ?>
                </div>
            <?php } else if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                <div class="catalog-section-item-purchase-buttons">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-section-item-purchase-button',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-ui-mod-transparent',
                                'intec-ui-scheme-current',
                                'intec-ui-mod-round-2'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                </div>
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
                        'intec-ui-scheme-current',
                        'intec-ui-mod-round-2'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <span>
                        <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_1_BUTTON_DETAIL') ?>
                    </span>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'order') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag($arItem['DATA']['OFFER'] ? 'a' : 'div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-order',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-mod-transparent',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-round-2'
                    ],
                    'href' => $arItem['DATA']['OFFER'] ? $arItem['DETAIL_PAGE_URL'] : null,
                    'data-role' => !$arItem['DATA']['OFFER'] ? 'item.order' : null
                ]) ?>
                    <span>
                        <?php if ($arItem['DATA']['OFFER']) { ?>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_1_BUTTON_DETAIL') ?>
                        <?php } else { ?>
                            <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                        <?php } ?>
                    </span>
                <?= Html::endTag($arItem['DATA']['OFFER'] ? 'a' : 'div') ?>
            </div>
        <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag($arItem['DATA']['OFFER'] ? 'a' : 'div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-order',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-mod-transparent',
                        'intec-ui-scheme-current',
                        'intec-ui-mod-round-2'
                    ],
                    'href' => $arItem['DATA']['OFFER'] ? $arItem['DETAIL_PAGE_URL'] : null,
                    'data-role' => !$arItem['DATA']['OFFER'] ? 'item.request' : null
                ]) ?>
                    <span>
                        <?php if ($arItem['DATA']['OFFER']) { ?>
                            <?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_1_BUTTON_DETAIL') ?>
                        <?php } else { ?>
                            <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                        <?php } ?>
                    </span>
                <?= Html::endTag($arItem['DATA']['OFFER'] ? 'a' : 'div') ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php $fRender($arItem) ?>
<?php } ?>