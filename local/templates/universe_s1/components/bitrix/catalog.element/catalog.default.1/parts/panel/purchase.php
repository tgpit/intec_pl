<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 * @var array $arVisual
 */

?>
<?php $vPanelOrder = function (&$arItem, $bOffer = false) use (&$arResult, &$arParams, &$arVisual) { ?>
    <?php if ($arResult['ACTION'] === 'buy') { ?>
        <?php if (!$arItem['CAN_BUY']) {
            return;
        } else { ?>
            <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-element-purchase-order-buttons',
                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-purchase-order-button',
                        'catalog-element-purchase-order-button-add',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'control-basket-button',
                            'scheme-current',
                            'size-4',
                            'mod-block'
                        ]
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
                        <i class="button-icon glyph-icon-cart"></i>
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
                        'catalog-element-purchase-order-button',
                        'catalog-element-purchase-order-button-added',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'size-4',
                            'mod-block',
                            'state-hover'
                        ]
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="button-icon glyph-icon-cart"></i>
                    </span>
                    <span class="intec-ui-part-content">
                        <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_1_PURCHASE_ORDERED') ?>
                    </span>
                <?= Html::endTag('a') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?php } else if ($arResult['ACTION'] === 'order') { ?>
        <div class="catalog-element-purchase-order-buttons">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-purchase-order-button',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'scheme-current',
                        'size-4',
                        'mod-block'
                    ]
                ],
                'data-role' => 'order'
            ]) ?>
                <span class="intec-ui-part-icon">
                    <i class="button-icon glyph-icon-cart"></i>
                </span>
                <span class="intec-ui-part-content">
                    <?= $arVisual['BUTTONS']['ORDER']['TEXT'] ?>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } else if ($arResult['ACTION'] === 'request') { ?>
        <div class="catalog-element-purchase-order-buttons">
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-purchase-order-button',
                    'intec-ui' => [
                        '',
                        'control-button',
                        'scheme-current',
                        'size-4',
                        'mod-block'
                    ]
                ],
                'data-role' => 'request'
            ]) ?>
                <span class="intec-ui-part-content">
                    <?= $arVisual['BUTTONS']['REQUEST']['TEXT'] ?>
                </span>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
<?php } ?>
<?php $vPanelOrder($arResult);

if (!empty($arResult['OFFERS']) && $arResult['ACTION'] === 'buy') {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelOrder($arOffer, true);

    unset($arOffer);
}

unset($vPanelOrder);