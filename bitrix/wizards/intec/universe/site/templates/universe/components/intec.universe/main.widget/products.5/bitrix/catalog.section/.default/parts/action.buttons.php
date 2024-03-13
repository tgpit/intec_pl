<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) {

    $arParent = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID']
    ];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParent) {
    
        if ($arItem['VISUAL']['OFFER'] && !$bOffer)
            return;
        
    ?>
        <div class="widget-item-action-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arItem['VISUAL']['COMPARE']['USE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-compare',
                        'intec-cl-background-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_COMPARE_ADD'),
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
                        'widget-item-action-button',
                        'widget-item-action-button-compared',
                        'intec-cl-background'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_COMPARE_REMOVE'),
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
            <?php } else if ($arResult['COMPARE']['SHOW_INACTIVE']) { ?>
                <div class="widget-item-action-button widget-item-action-button-compare inactive">
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                </div>
            <?php } ?>
            <?php if ($arItem['VISUAL']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-item-action-button',
                        'widget-item-action-button-delay',
                        'intec-cl-background-hover'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DELAY_ADD'),
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
                        'widget-item-action-button',
                        'widget-item-action-button-delayed',
                        'intec-cl-background'
                    ],
                    'title' => Loc::getMessage('C_WIDGET_PRODUCTS_5_ICON_DELAY_REMOVE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ]
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['DELAY']['SHOW_INACTIVE']) { ?>
                <div class="widget-item-action-button widget-item-action-button-delay inactive">
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <?php

        $fRender($arItem);
    
        if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS'])) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);
    
            unset($arOffer);
        }

    ?>
<?php } ?>