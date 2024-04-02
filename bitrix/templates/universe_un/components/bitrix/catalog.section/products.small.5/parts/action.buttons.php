<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual, $arSvg) {

    $arParentValues = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'DELAY' => $arItem['DELAY']['USE']
    ];

?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParentValues, &$arSvg) { ?>
        <div class="catalog-section-item-action-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arItem['DATA']['DELAY']['USE'] && $arItem['CAN_BUY']) { ?>
                <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-delay',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_DELAY_ADD'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'delay',
                        'basket-state' => 'none',
                        'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                    ]
                ]) ?>
                <?= Html::tag('div', $arSvg['DELAY'], [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-delayed',
                        'intec-cl-background',
                        'intec-cl-border',
                        'intec-cl-background-light-hover',
                        'intec-cl-border-light-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_DELAY_REMOVE'),
                    'data' => [
                        'basket-id' => $arItem['ID'],
                        'basket-action' => 'remove',
                        'basket-state' => 'none'
                    ]
                ]) ?>
            <?php } else if ($arResult['DELAY']['SHOW_INACTIVE']) { ?>
                <div class="catalog-section-item-action-button catalog-section-item-action-button-delay inactive">
                    <?= $arSvg['DELAY'] ?>
                </div>
            <?php } ?>
            <?php if ($arItem['DATA']['COMPARE']['USE']) { ?>
                <?= Html::tag('div', $arSvg['COMPARE'], [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compare',
                        'intec-cl-background-hover',
                        'intec-cl-border-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_COMPARE_ADD'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ]
                ]) ?>
                <?= Html::tag('div', $arSvg['COMPARE'], [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compared',
                        'intec-cl-background',
                        'intec-cl-border',
                        'intec-cl-background-light-hover',
                        'intec-cl-border-light-hover'
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_5_ICON_COMPARE_REMOVE'),
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ]
                ]) ?>
            <?php } else if ($arResult['COMPARE']['SHOW_INACTIVE']) { ?>
                <div class="catalog-section-item-action-button catalog-section-item-action-button-compare inactive">
                    <?= $arSvg['COMPARE'] ?>
                </div>
            <?php } ?>
        </div>
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