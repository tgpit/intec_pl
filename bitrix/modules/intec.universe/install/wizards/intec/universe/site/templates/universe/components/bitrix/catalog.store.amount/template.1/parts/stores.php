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
        <div class="catalog-store-amount-item-block">
            <?= Html::beginTag('div', [
                'class' => 'catalog-store-amount-item-state',
                'data' => [
                    'role' => 'store.state'
                ]
            ]) ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'nowrap',
                            'a-v-center',
                            'i-h-4'
                        ]
                    ]
                ]) ?>
                    <div class="intec-grid-item-auto">
                        <div class="catalog-store-amount-item-state-indicator"></div>
                    </div>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1">
                        <?php if (!$bMinAmountUse) { ?>
                            <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_TEMPLATE_1_TEMPLATE_IN_STOCK'), [
                                'class' => [
                                    'catalog-store-amount-item-state-value',
                                    'catalog-store-amount-item-state-colored'
                                ]
                            ]) ?>
                        <?php } ?>
                        <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                            'class' => [
                                'catalog-store-amount-item-state-value',
                                'catalog-store-amount-item-state-colored'
                            ],
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
                <?= Html::endTag('div') ?>
            <?= Html::endTag('div') ?>
        </div>
        <div class="catalog-store-amount-item-block">
            <div class="catalog-store-amount-item-title">
                <?= $arStore['TITLE'] ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>

<?php
    $vStores($arStore);
    unset($vStores);
?>