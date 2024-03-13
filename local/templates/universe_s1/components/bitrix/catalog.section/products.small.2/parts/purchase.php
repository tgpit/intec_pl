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
<?php return function (&$arItem) use (&$arResult, &$arVisual, &$sTemplateId, &$APPLICATION, &$component) { ?>
    <?php if ($arItem['DATA']['ACTION'] === 'buy') { ?>
        <?php if ($arItem['CAN_BUY']) { ?>
            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <div class="catalog-section-item-purchase-buttons">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-ui',
                        'intec-ui-control-basket-button',
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-add',
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
                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_ADD') ?>
                    </span>
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
                        'intec-cl-background-light'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_ADDED') ?>
                <?= Html::endTag('a') ?>
            </div>
        <?php } else { ?>
            <?php if ($arItem['CATALOG_SUBSCRIBE'] == 'Y') { ?>
                <div class="catalog-section-item-purchase-buttons">
                    <?php $APPLICATION->IncludeComponent(
                        'bitrix:catalog.product.subscribe',
                        '.default', [
                            'BUTTON_CLASS' => Html::cssClassFromArray([
                                'catalog-section-item-purchase-button',
                                'intec-cl-background',
                                'intec-cl-background-light-hover'
                            ]),
                            'BUTTON_ID' => $sTemplateId.'_subscribe_'.$arItem['ID'],
                            'PRODUCT_ID' => $arItem['ID']
                        ],
                        $component
                    ) ?>
                </div>
            <?php } else { ?>
                <div class="catalog-section-item-purchase-buttons">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-purchase-button',
                            'intec-cl-background'
                        ]
                    ]) ?>
                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_UNAVAILABLE') ?>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>
        <?php } ?>
    <?php } else if ($arItem['DATA']['ACTION'] === 'detail') { ?>
        <div class="catalog-section-item-purchase-detail">
            <?= Html::beginTag('a', [
                'class' => [
                    'catalog-section-item-purchase-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
                <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_DETAIL') ?>
            <?= Html::endTag('a') ?>
        </div>
    <?php } else if ($arItem['DATA']['ACTION'] === 'order') { ?>
        <div class="catalog-section-item-purchase-order">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-item-purchase-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'item.order'
            ]) ?>
                <span>
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_ORDER') ?>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } else if ($arItem['DATA']['ACTION'] === 'request') { ?>
        <div class="catalog-section-item-purchase-order">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-section-item-purchase-button',
                    'intec-cl-background',
                    'intec-cl-background-light-hover'
                ],
                'data-role' => 'item.request'
            ]) ?>
            <span>
                    <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_2_PURCHASE_REQUEST') ?>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
<?php } ?>