<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vButtons = function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $arParent = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParent) { ?>
        <?php if ($arItem['DATA']['OFFER'] && !$bOffer)
            return;
        ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-section-item-price-buttons',
            'data-offer' => $bOffer ? $arItem['ID'] : 'false'
        ]) ?>
            <?php if ($arItem['DATA']['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-price-button',
                        'catalog-section-item-price-button-compare',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParent['IBLOCK_ID']
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-price-button',
                        'catalog-section-item-price-button-compared',
                        'intec-cl-background'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParent['IBLOCK_ID']
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arItem['DATA']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-price-button',
                        'catalog-section-item-price-button-delay',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-price-button',
                        'catalog-section-item-price-button-delayed',
                        'intec-cl-background'
                    ],
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }
    ?>
<?php } ?>