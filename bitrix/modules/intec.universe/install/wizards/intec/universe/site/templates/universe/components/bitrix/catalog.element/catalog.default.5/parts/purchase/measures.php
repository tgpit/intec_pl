<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 * @var bool $bOffers
 */

$arPrice = [];

if (!empty($arResult['ITEM_PRICES']))
    $arPrice = ArrayHelper::getFirstValue($arResult['ITEM_PRICES']);

?>
<?php $vMeasuresSelect = function (&$arItem, $bOffer) use (&$arSvg) { ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-measures-select',
        'data' => [
            'role' => 'measures.select',
            'active' => 'false',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <div class="catalog-element-measures-select-content" data-role="measures.select.content">
            <div class="intec-grid intec-grid-nowrap intec-grid-a-v-center intec-grid-i-h-1">
                <div class="intec-grid-item-auto">
                    <div class="catalog-element-measures-select-content-title">
                        <span>
                            <?= Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_MEASURES') ?>
                        </span>
                        <span data-role="measures.select.content.measure">
                            <?= !empty($arItem['CATALOG_MEASURE_NAME']) ? $arItem['CATALOG_MEASURE_NAME'] : null ?>
                        </span>
                    </div>
                </div>
                <div class="intec-grid-item-auto">
                    <?= Html::tag('div', $arSvg['MEASURES']['ARROW'], [
                        'class' => [
                            'catalog-element-measures-select-content-decoration',
                            'intec-cl-svg-path-stroke'
                        ]
                    ]) ?>
                </div>
            </div>
        </div>
        <div class="catalog-element-measures-select-options">
            <div class="catalog-element-measures-select-options-list">
                <?php foreach ($arItem['MEASURES'] as $arMeasure) { ?>
                    <?= Html::tag('div', $arMeasure['symbol'], [
                        'class' => Html::cssClassFromArray([
                            'catalog-element-measures-select-option' => true,
                            'intec-cl-text-hover' => true,
                            'intec-cl-text' => $arMeasure['base']
                        ], true),
                        'data' => [
                            'role' => 'measures.select.option',
                            'measure-id' => $arMeasure['id'],
                            'selected' => $arMeasure['base'] ? 'true' : 'false'
                        ]
                    ]) ?>
                <?php } ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?= Html::beginTag('div', [
    'class' => [
        'catalog-element-measures',
        'catalog-element-purchase-block'
    ],
    'data' => [
        'role' => 'measures',
        'position' => $arVisual['MEASURES']['POSITION']
    ],
    'style' => [
        'display' => 'none'
    ]
]) ?>
    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-3">
        <div class="intec-grid-item-auto">
            <div class="catalog-element-measures-price" data-role="measures.price">
                <?= !empty($arPrice) ? $arPrice['PRINT_PRICE'] : null ?>
            </div>
        </div>
        <div class="intec-grid-item-auto">
            <?php if (!$bOffers) {
                $vMeasuresSelect($arResult, false);
            } else {
                foreach ($arResult['OFFERS'] as &$arOffer)
                    $vMeasuresSelect($arOffer, true);

                unset($arOffer);
            } ?>
            <?php unset($vMeasuresSelect) ?>
        </div>
    </div>
    <div class="catalog-element-ratio">
        <?= Html::tag('div', Loc::getMessage('C_CATALOG_ELEMENT_DEFAULT_5_TEMPLATE_MEASURES_MULTIPLICITY_ORDER'), [
            'class' => [
                'catalog-element-ratio-message',
                'catalog-element-ratio-part',
                'catalog-element-ratio-text'
            ]
        ]) ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-element-ratio-value',
                'catalog-element-ratio-part',
                'catalog-element-ratio-text',
                'intec-cl-text',
            ],
            'data-role' => 'ratio'
        ]) ?>
            <span data-role="ratio.value">
                <?= $arResult['CATALOG_MEASURE_RATIO'] ?>
            </span>
            <span data-role="ratio.measure">
                <?= $arResult['CATALOG_MEASURE_NAME'] ?>
            </span>
        <?= Html::endTag('div') ?>
    </div>
<?= Html::endTag('div') ?>