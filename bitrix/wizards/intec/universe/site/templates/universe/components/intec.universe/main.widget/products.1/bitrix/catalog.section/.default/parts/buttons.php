<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $iBlockId = $arItem['IBLOCK_ID'] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$iBlockId, &$arVisual) {

        if ($arVisual['OFFERS']['USE'] && $arItem['VISUAL']['OFFER'])
            return;

    ?>
        <div class="widget-item-image-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arItem['VISUAL']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-image-button',
                        'widget-item-image-button-delay',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_1_DELAY_ADD_TITLE'),
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
                        'widget-item-image-button',
                        'widget-item-image-button-delayed',
                        'intec-cl-text'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_1_DELAY_ADDED_TITLE'),
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
                        'widget-item-image-button',
                        'widget-item-image-button-compare',
                        'intec-cl-text-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_1_COMPARE_ADD_TITLE'),
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
                        'widget-item-image-button',
                        'widget-item-image-button-compared',
                        'intec-cl-text'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_1_COMPARE_ADDED_TITLE'),
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