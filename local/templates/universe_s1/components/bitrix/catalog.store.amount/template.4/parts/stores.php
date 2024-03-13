<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arSvg
 * @var bool $bOffers
 */

?>
<?php $vStores = function (&$arStore) use (&$arResult, &$arVisual, &$arParams) { ?>
    <?php $bMinAmountUse = $arVisual['MIN_AMOUNT']['USE']; ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-store-amount-item',
        'data' => [
            'role' => 'store',
            'store-id' => $arStore['ID'],
            'store-state' => $arStore['AMOUNT_STATUS']
        ]
    ]) ?>
        <div class="catalog-store-amount-item-container">
            <div class="catalog-store-amount-item-block">
                <?= Html::beginTag('div', [
                    'class' => 'catalog-store-amount-item-state',
                    'data' => [
                        'role' => 'store.state'
                    ]
                ]) ?>
                    <div class="catalog-store-amount-item-state-content">
                        <div class="catalog-store-amount-item-state-indicator catalog-store-amount-item-state-part"></div>
                        <div class="catalog-store-amount-item-state-text catalog-store-amount-item-state-part">
                            <?php if (!$bMinAmountUse) { ?>
                                <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_4_TEMPLATE_IN_STOCK'), [
                                    'class' => [
                                        'catalog-store-amount-item-state-value',
                                        'catalog-store-amount-item-state-colored'
                                    ]
                                ]) ?>
                            <?php } ?>
                            <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                                'class' => 'catalog-store-amount-item-state-value',
                                'data' => [
                                    'role' => 'store.quantity'
                                ]
                            ]) ?>
                            <?php if (!$bMinAmountUse) { ?>
                                <?php $sMeasureName = empty($arParams['OFFER_ID']) ? ArrayHelper::getFirstValue($arResult['MEASURES']) : ArrayHelper::getValue($arResult, ['MEASURES', $arParams['OFFER_ID']]) ?>
                                <?= Html::tag('span', $sMeasureName, [
                                    'class' => 'catalog-store-amount-item-state-value',
                                    'data' => [
                                        'role' => 'store.measure'
                                    ]
                                ]) ?>
                            <?php } ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
            <div class="catalog-store-amount-item-block">
                <div class="catalog-store-amount-item-title">
                    <?= $arStore['TITLE'] ?>
                </div>
            </div>
            <?php if ($arVisual['PHONE']['SHOW'] && !empty($arStore['PHONE'])) { ?>
                <div class="catalog-store-amount-item-block">
                    <?= Html::tag('a', $arStore['PHONE']['PRINT'], [
                        'class' => [
                            'catalog-store-amount-item-contact',
                            'intec-cl-text-hover'
                        ],
                        'title' => $arStore['PHONE']['PRINT'],
                        'href' => 'tel:'.$arStore['PHONE']['HTML']
                    ]) ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['EMAIL']['SHOW'] && !empty($arStore['EMAIL'])) { ?>
                <div class="catalog-store-amount-item-block">
                    <?= Html::tag('a', $arStore['EMAIL'], [
                        'class' => [
                            'catalog-store-amount-item-contact',
                            'intec-cl-text-hover'
                        ],
                        'title' => $arStore['EMAIL'],
                        'href' => 'mailto:'.$arStore['EMAIL']
                    ]) ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['SCHEDULE']['SHOW'] && !empty($arStore['SCHEDULE'])) { ?>
                <div class="catalog-store-amount-item-block">
                    <div class="catalog-store-amount-item-title">
                        <?= $arStore['SCHEDULE'] ?>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php
    $vStores($arStore);
    unset($vStores);
?>