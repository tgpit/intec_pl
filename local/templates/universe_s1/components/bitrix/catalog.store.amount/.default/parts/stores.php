<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\StringHelper;

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
        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-v-5 intec-grid-i-h-15">
            <div class="intec-grid-item intec-grid-item-768-1 catalog-store-amount-item-block" data-code="address">
                <?php if (!empty($arStore['TITLE'])) { ?>
                    <?= Html::tag( !empty($arStore['URL']) ? 'a' : 'div', $arStore['TITLE'], [
                        'class' => 'catalog-store-amount-item-address',
                        'href' => !empty($arStore['URL']) ? $arStore['URL'] : null
                    ]) ?>
                <?php } ?>
                <?php if (!empty($arStore['DESCRIPTION'])) { ?>
                    <div class="catalog-store-amount-item-description">
                        <?= $arStore['DESCRIPTION'] ?>
                    </div>
                <?php } ?>
            </div>
            <div class="intec-grid-item intec-grid-item-768-1 catalog-store-amount-item-block" data-code="contacts">
                <?php if (!empty($arStore['PHONE'])) { ?>
                    <a href="tel:<?= StringHelper::replace($arStore['PHONE'], [
                        '(' => '',
                        ')' => '',
                        ' ' => '',
                        '-' => ''
                    ]) ?>" class="catalog-store-amount-item-phone intec-cl-text-hover">
                        <?= $arStore['PHONE'] ?>
                    </a>
                <?php } ?>
                <?php if (!empty($arStore['EMAIL'])) { ?>
                    <a href="mailto:<?= $arStore['EMAIL'] ?>" class="catalog-store-amount-item-email intec-cl-text-hover">
                        <?= $arStore['EMAIL'] ?>
                    </a>
                <?php } ?>
            </div>
            <div class="intec-grid-item intec-grid-item-768-1 catalog-store-amount-item-block" data-code="schedule">
                <?php if (!empty($arStore['SCHEDULE'])) { ?>
                    <div class="catalog-store-amount-item-schedule">
                        <?= Html::decode($arStore['SCHEDULE']) ?>
                    </div>
                <?php } ?>
            </div>
            <?= Html::beginTag('div', [
                'class' => [
                    'intec-grid' => [
                        'item',
                        'item-768-1'
                    ],
                    'catalog-store-amount-item-block',
                    'intec-ui-align'
                ],
                'data' => [
                    'role' => 'store.state',
                    'code' => 'amount'
                ]
            ]) ?>
                <?= Html::tag('i', '', [
                    'class' => [
                        'catalog-store-amount-item-icon-amount',
                        'fas',
                        'fa-check'
                    ]
                ]) ?>
                <?= Html::tag('i', '', [
                    'class' => [
                        'catalog-store-amount-item-icon-amount',
                        'fas',
                        'fa-times'
                    ]
                ]) ?>
                <?php if (!$bMinAmountUse) { ?>
                    <?= Html::tag('span', Loc::getMessage('C_CATALOG_STORE_AMOUNT_DEFAULT_TEMPLATE_IN_STOCK'), [
                        'class' => [
                            'catalog-store-amount-item-value'
                        ]
                    ]) ?>
                <?php } ?>
                <?= Html::tag('span', $arStore['AMOUNT_PRINT'], [
                    'class' => 'catalog-store-amount-item-value',
                    'data' => [
                        'role' => 'store.quantity'
                    ]
                ]) ?>
                <?php if (!$bMinAmountUse) { ?>
                    <?php $sMeasureName = empty($arParams['OFFER_ID']) ? ArrayHelper::getFirstValue($arResult['MEASURES']) : ArrayHelper::getValue($arResult, ['MEASURES', $arParams['OFFER_ID']]) ?>
                    <?= Html::tag('span', $sMeasureName, [
                        'class' => 'catalog-store-amount-item-value',
                        'data' => [
                            'role' => 'store.measure'
                        ]
                    ]) ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>

<?php
    $vStores($arStore);
    unset($vStores);
?>