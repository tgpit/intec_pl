<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual) { ?>
        <?php if ($bOffer || $arItem['ACTION']['DATA'] === 'buy' && $arItem['CAN_BUY']) {

            $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]);

        ?>
            <?= Html::beginTag('div', [
                'class' => 'catalog-section-item-purchase-buttons',
                'data-offer' => $bOffer ? $arItem['ID'] : 'false'
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-add',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'control-basket-button',
                            'scheme-current',
                            'mod-round-4'
                        ]
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="glyph-icon-cart"></i>
                    </span>
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
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'state-hover',
                            'mod-round-4'
                        ]
                    ],
                    'href' => $arResult['URL']['BASKET'],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <span class="intec-ui-part-icon">
                        <i class="glyph-icon-cart"></i>
                    </span>
                    <span class="intec-ui-part-content">
                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_BUTTON_ADDED') ?>
                    </span>
                <?= Html::endTag('a') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-purchase-button',
                        'catalog-section-item-purchase-button-update',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ]
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'setQuantity',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <div class="intec-ui-part-icon">
                        <i class="glyph-icon-cart"></i>
                    </div>
                    <div class="intec-ui-part-content">
                        <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_BUTTON_UPDATE') ?>
                    </div>
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem, false);

        if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'] && $arItem['DATA']['ACTION'] === 'buy') {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>