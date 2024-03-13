<?php if (!defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$vButtons = function (&$arItem) use (&$arResult, &$arVisual) {
    $arParentValues = [
        'IBLOCK_ID' => $arItem['IBLOCK_ID'],
        'DELAY' => $arItem['DATA']['DELAY']['USE'],
        'COMPARE' => $arItem['DATA']['COMPARE']['USE']
    ];
    $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arParentValues) { ?>
        <div class="catalog-section-item-action-buttons" data-offer="<?= $bOffer ? $arItem['ID'] : 'false' ?>">
            <?php if ($arParentValues['COMPARE']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compare',
                        'intec-cl-background-hover'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'add',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_COMPARE_ADD')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-section-item-action-button',
                        'catalog-section-item-action-button-compared',
                        'intec-cl-background'
                    ],
                    'data' => [
                        'compare-id' => $arItem['ID'],
                        'compare-action' => 'remove',
                        'compare-code' => $arResult['COMPARE']['CODE'],
                        'compare-state' => 'none',
                        'compare-iblock' => $arParentValues['IBLOCK_ID']
                    ],
                'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_COMPARE_REMOVE')
                ]) ?>
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                <?= Html::endTag('div') ?>
            <?php } else if ($arResult['COMPARE']['SHOW_INACTIVE']) { ?>
                <div class="catalog-section-item-action-button catalog-section-item-action-button-compare inactive">
                    <?= FileHelper::getFileData(__DIR__ . '/../svg/compare.svg') ?>
                </div>
            <?php } ?>
            <?php if ($arParentValues['DELAY']) { ?>
                <?php if ($arItem['CAN_BUY']) { ?>
                    <?php $arPrice = ArrayHelper::getValue($arItem, ['ITEM_PRICES', 0]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-action-button',
                            'catalog-section-item-action-button-delay',
                            'intec-cl-background-hover'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-action' => 'delay',
                            'basket-state' => 'none',
                            'basket-price' => !empty($arPrice) ? $arPrice['PRICE_TYPE_ID'] : null
                        ],
                        'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_DELAY_ADD')
                    ]) ?>
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                    <?= Html::endTag('div') ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-section-item-action-button',
                            'catalog-section-item-action-button-delayed',
                            'intec-cl-background'
                        ],
                        'data' => [
                            'basket-id' => $arItem['ID'],
                            'basket-action' => 'remove',
                            'basket-state' => 'none'
                        ],
                    'title' => Loc::getMessage('C_CATALOG_SECTION_CATALOG_TEXT_2_ICON_DELAY_REMOVE')
                    ]) ?>
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                    <?= Html::endTag('div') ?>
                <?php } else if ($arResult['DELAY']['SHOW_INACTIVE']) { ?>
                    <div class="catalog-section-item-action-button catalog-section-item-action-button-delay inactive">
                        <?= FileHelper::getFileData(__DIR__ . '/../svg/delay.svg') ?>
                    </div>
                <?php } ?>
            <?php } ?>
        </div>
    <?php };

    $fRender($arItem);

    if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'])
        foreach ($arItem['OFFERS'] as &$arOffer)
            $fRender($arOffer, true);
};