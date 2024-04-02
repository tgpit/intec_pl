<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) {

    $iBlockId = $arItem['IBLOCK_ID'];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$iBlockId) {

        if ($arItem['VISUAL']['OFFER'] && !$bOffer)
            return;

    ?>
        <div class="widget-item-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arItem['VISUAL']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-button',
                        'widget-item-button-delay',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_3_DELAY_ADD_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <i class="intec-ui-icon intec-ui-icon-heart-1"></i>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-button',
                        'widget-item-button-delayed',
                        'intec-cl-text'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_3_DELAY_ADDED_TITLE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <i class="intec-ui-icon intec-ui-icon-heart-1"></i>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arItem['VISUAL']['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-button',
                        'widget-item-button-compare',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_3_COMPARE_ADD_TITLE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $iBlockId
                    ]
                ]) ?>
                    <i class="glyph-icon-compare"></i>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-button',
                        'widget-item-button-compared',
                        'intec-cl-text'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_3_COMPARE_ADDED_TITLE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $iBlockId
                    ]
                ]) ?>
                    <i class="glyph-icon-compare"></i>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>