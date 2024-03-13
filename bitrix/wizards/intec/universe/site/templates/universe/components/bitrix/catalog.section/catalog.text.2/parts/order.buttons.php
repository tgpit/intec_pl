<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

/**
 * @param array $arItem
 */
$vOrder = function (&$arItem) use (&$arResult, &$arVisual, &$APPLICATION, &$component, &$sTemplateId) {
    $arParent = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'ACTION' => $arItem['DATA']['ACTION']
    ];
    $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParent, &$APPLICATION, &$component, &$sTemplateId) { ?>
        <?php if ($bOffer || $arParent['ACTION'] === 'buy') { ?>
            <?php if ($arItem['CAN_BUY']) { ?>
                <div class="catalog-section-item-order-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
                    <?php if ($arVisual['BUTTON_TOGGLE']['ACTION'] === 'buy') { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-ui-state-active',
                                'intec-ui-state-hover',
                                'intec-cl-background-hover',
                                'active'
                            ],
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID'],
                                'basket-action' => 'add',
                                'basket-state' => 'none',
                                'basket-quantity' => $arItem['CATALOG_MEASURE_RATIO'],
                                'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_PURCHASE')
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-ui-state-active',
                                'intec-cl-background',
                                'added',
                                'active'
                            ],
                            'data' => [
                                'toggle' => 'open',
                                'basket-id' => $arItem['ID']
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_BASKET')
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-order-button',
                                'catalog-section-item-order-button-toggle',
                                'intec-ui',
                                'intec-ui-control-button',
                                'intec-ui-state-active',
                                'intec-cl-background-hover',
                                'active'
                            ],
                            'data' => [
                                'toggle' => 'open'
                            ],
                            'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_PURCHASE')
                        ]) ?>
                            <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-order-button',
                            'catalog-section-item-order-button-toggle',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-state-active',
                            'intec-cl-text-hover'
                        ],
                        'data' => [
                            'toggle' => 'close'
                        ],
                        'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_PURCHASE_CLOSE')
                    ]) ?>
                        <i class="glyph-icon-cancel"></i>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } else if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                <?php if (!empty($arItem['OFFERS']) && $bOffer == false) {
                    return;
                } ?>
                <div class="catalog-section-item-order-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>" title="<?= Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_SUBSCRIBE') ?>">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default',
                        [
                            'BUTTON_CLASS' => 'catalog-section-item-order-button catalog-section-item-order-button-subscribe intec-cl-text-hover intec-ui intec-ui-control-button',
                            'BUTTON_ID' => $sTemplateId . '_subscribe_' . $arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID'],
                            'ICON_USE' => 'Y',
                            'TEXT_SHOW' => 'N'
                        ],
                        $component
                    ); ?>
                </div>
            <?php } ?>
        <?php } else if ($arParent['ACTION'] === 'detail') { ?>
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
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_DETAIL'),
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <i class="far fa-ellipsis-h"></i>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            </div>
        <?php } else if ($arParent['ACTION'] === 'order') { ?>
            <div class="catalog-section-item-order-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-order-button',
                        'catalog-section-item-order-button-order',
                        'intec-ui',
                        'intec-ui-control-button',
                        'intec-ui-scheme-current',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'role' => 'item.order'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_ORDER')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/order.svg') ?>
                <?= Html::endTag('div') ?>
            </div>
        <?php } else if ($arParent['ACTION'] === 'request') { ?>
            <div class="catalog-section-item-order-buttons">
                <?php if ($arItem['DATA']['OFFER']) { ?>
                    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
                        'class' => [
                            'catalog-section-item-order-button',
                            'catalog-section-item-order-button-detail',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-cl-text-hover'
                        ],
                        'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_DETAIL'),
                        'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                    ]) ?>
                        <i class="far fa-ellipsis-h"></i>
                    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                <?php } else { ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-order-button',
                            'catalog-section-item-order-button-order',
                            'intec-ui',
                            'intec-ui-control-button',
                            'intec-ui-scheme-current',
                            'intec-cl-background-hover'
                        ],
                        'title' => $arVisual['BUTTONS']['REQUEST']['TEXT'],
                        'data-role' => 'item.request'
                    ]) ?>
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/order.svg') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?php };

    $fRender($arItem, false);

    if ($arItem['DATA']['ACTION'] === 'buy' && $arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'])
        foreach ($arItem['OFFERS'] as &$arOffer) {
            $fRender($arOffer, true);

            unset($arOffer);
        }
};