<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual) { ?>
        <?php if ($bOffer || $arItem['VISUAL']['ACTION'] === 'buy' && $arItem['CAN_BUY']) {

            $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]);

        ?>
            <div class="widget-item-purchase-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-basket-button',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ],
                        'widget-item-purchase-button',
                        'widget-item-purchase-button-add'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <span class="intec-ui-part-content">
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                        <span style="display: inline-block; vertical-align: middle">
                            <?= $arVisual['BUTTONS']['BASKET']['TEXT'] ?>
                        </span>
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
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ],
                        'widget-item-purchase-button',
                        'widget-item-purchase-button-added',
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'add',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                    <span style="display: inline-block; vertical-align: middle">
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_BUTTON_ADDED') ?>
                    </span>
                <?= Html::endTag('a') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-4'
                        ],
                        'widget-item-purchase-button',
                        'widget-item-purchase-button-update'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'setQuantity',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/purchase.svg') ?>
                    <span style="display: inline-block; vertical-align: middle">
                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_5_BUTTON_UPDATE') ?>
                    </span>
                <?= Html::endTag('div') ?>
            </div>
        <?php } ?>
    <?php } ?>
    <?php

        $fRender($arItem, false);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'] && $arItem['VISUAL']['ACTION'] === 'buy') {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>