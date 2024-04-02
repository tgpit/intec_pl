<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Json;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var string $sTemplateId
 */

include(__DIR__.'/sku.list.order.php');
include(__DIR__.'/sku.list.price.range.php');

$vTimer = include(__DIR__.'/sku.list.timer.php');
$vMeasuresSelect = include(__DIR__.'/sku.list.measures.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId.'-sku-list',
    'class' => [
        'catalog-element-sections',
        'catalog-element-sections-wide'
    ]
]) ?>
    <div class="catalog-element-section">
        <div class="catalog-element-section-name">
            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_SKU_LIST_TITLE') ?>
        </div>
        <div class="catalog-element-section-content-visible">
            <div class="catalog-element-section-content-wrapper">
                <div class="catalog-element-section-offers-list" data-role="offers">
                    <?php foreach ($arResult['OFFERS'] as $arOffer) {

                        $arOfferData = ArrayHelper::getValue($arData, ['offers', $arOffer['ID']]);

                        $arPrice = null;

                        if (!empty($arOfferData['prices']))
                            $arPrice = ArrayHelper::getFirstValue($arOfferData['prices']);

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-element-offer',
                            'data' => [
                                'role' => 'offer',
                                'offer-data' => Json::encode($arOfferData, JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true),
                                'available' => $arOfferData['available'] ? 'true' : 'false',
                                'subscribe' => $arOfferData['subscribe'] ? 'true' : 'false',
                            ]
                        ]) ?>
                            <div class="catalog-element-offer-content intec-grid intec-grid-a-v-baseline intec-grid-i-12 intec-grid-800-wrap">
                                <div class="catalog-element-offer-info intec-grid-item-3 intec-grid-item-800-1">
                                    <div class="catalog-element-offer-name">
                                        <?= $arOffer['NAME'] ?>
                                    </div>
                                    <?php if ($arResult['SKU_PROPS_SHOW']) { ?>
                                        <div class="catalog-element-offer-properties">
                                            <?php foreach($arResult['SKU_PROPS'] as $arProperty) {

                                                $sPropertyValue = ArrayHelper::getValue($arProperty, [
                                                    'values',
                                                    $arOffer['TREE']['PROP_'.$arProperty['id']],
                                                    'name'
                                                ]);

                                            ?>
                                                <div class="catalog-element-offer-property intec-grid">
                                                    <div class="catalog-element-offer-property-title intec-grid-auto">
                                                        <?= $arProperty['name'] ?>
                                                    </div>
                                                    <div class="catalog-element-offer-property-value intec-grid-auto">
                                                        <?= $sPropertyValue ?>
                                                    </div>
                                                </div>
                                            <? } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="catalog-element-offer-price-wrap intec-grid-item-3 intec-grid-item-800-2 intec-grid-item-550-1">
                                    <?php if ($arVisual['PRICE']['SHOW']) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'catalog-element-offer-price',
                                                'intec-grid' => [
                                                    '',
                                                    'wrap',
                                                    'i-5',
                                                    'a-v-center',
                                                    'a-h-start'
                                                ]
                                            ],
                                            'data' => [
                                                'role' => 'price',
                                                'show' => !empty($arPrice) ? 'true' : 'false',
                                                'discount' => !empty($arPrice) && $arPrice['discount']['use'] > 0 ? 'true' : 'false'
                                            ]
                                        ]) ?>
                                            <div class="catalog-element-offer-price-discount intec-grid-item-auto">
                                                <span data-role="price.discount">
                                                    <?= !empty($arPrice) ? $arPrice['discount']['display'] : null ?>
                                                </span>
                                                <?php if (!empty($arOffer['CATALOG_MEASURE_NAME'])) { ?>
                                                    <span>
                                                        /
                                                    </span>
                                                    <span>
                                                        <?= $arOffer['CATALOG_MEASURE_NAME'] ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                            <div class="catalog-element-offer-price-base intec-grid-item-auto" data-role="price.base">
                                                <?= $arPrice['base']['display'] ?>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                        <?php if ($arVisual['PRICE']['RANGE']) {
                                            $vPriceRangeSKUList($arOffer, false);
                                        } ?>
                                    <?php } ?>
                                    <?php if ($arVisual['TIMER']['SHOW'])
                                        $vTimer($arOffer)
                                    ?>
                                </div>
                                <div class="catalog-element-offer-buy intec-grid-item-3 intec-grid-item-800-2 intec-grid-item-550-1" data-print="false">
                                    <?php if ($arVisual['MEASURES']['USE']) { ?>
                                        <?php $vMeasuresSelect($arOffer) ?>
                                    <?php } ?>
                                    <div class="catalog-element-offer-purshare-wrap intec-grid intec-grid-wrap intec-grid-a-h-end intec-grid-a-h-550-start intec-grid-700-wrap intec-grid-i-v-5">
                                        <?php if ($arVisual['COUNTER']['SHOW']) { ?>
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'catalog-element-purchase-counter-wrap' => true,
                                                    'intec-grid' => [
                                                        'item-auto' => $arVisual['MENU']['SHOW'],
                                                        'item-2' => !$arVisual['MENU']['SHOW'],
                                                        'item-700-auto' => !$arVisual['MENU']['SHOW']
                                                    ]
                                                ], true),
                                            ]) ?>
                                                <?= Html::beginTag('div', [
                                                    'class' => [
                                                        'catalog-element-purchase-counter-control',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-numeric',
                                                            'scheme-current',
                                                            'view-2',
                                                            'size-4'
                                                        ]
                                                    ],
                                                    'data-role' => 'counter'
                                                ]) ?>
                                                    <?= Html::tag('a', '-', [
                                                        'class' => 'intec-ui-part-decrement',
                                                        'href' => 'javascript:void(0)',
                                                        'data-type' => 'button',
                                                        'data-action' => 'decrement'
                                                    ]) ?>
                                                    <?= Html::input('text', null, 0, [
                                                        'data-type' => 'input',
                                                        'class' => 'intec-ui-part-input'
                                                    ]) ?>
                                                    <div class="intec-ui-part-increment-wrapper">
                                                        <?= Html::tag('a', '+', [
                                                            'class' => 'intec-ui-part-increment',
                                                            'href' => 'javascript:void(0)',
                                                            'data-type' => 'button',
                                                            'data-action' => 'increment'
                                                        ]) ?>

                                                        <?= Html::beginTag('div', [
                                                            'class' => 'catalog-element-counter-control-max-message',
                                                            'data' => [
                                                                'role' => 'max-message-offer'
                                                            ]
                                                        ]) ?>
                                                            <div class="catalog-element-counter-control-max-message-close" data-role="max-message-close">
                                                                &times;
                                                            </div>
                                                            <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_3_MAX_MESSAGE') ?>
                                                        <?= Html::endTag('div') ?>
                                                    </div>
                                                <?= Html::endTag('div') ?>
                                            <?=Html::endTag('div');?>
                                        <?php } ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'catalog-element-purchase-order-buttons' => true,
                                                'intec-grid' => [
                                                    'item-auto' => $arVisual['MENU']['SHOW'],
                                                    'item-2' => !$arVisual['MENU']['SHOW'],
                                                    'item-700-auto' => !$arVisual['MENU']['SHOW']
                                                ]
                                            ], true),
                                        ]) ?>
                                            <?php $vOrder($arOffer); ?>
                                            <?php if ($arResult['DELIVERY_CALCULATION']['USE']) { ?>
                                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-end intec-grid-a-h-550-start">
                                                    <div class="intec-grid-item-auto">
                                                        <?php include(__DIR__.'/delivery.calculation.php'); ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        <?= Html::endTag('div');?>
                                        <?php if ($arVisual['CREDIT']['SHOW']) { ?>
                                            <div class="catalog-element-purchase-order-credit intec-grid-item-1">
                                                <?php include(__DIR__.'/sku.list.credit.php'); ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>
<?php unset($vOrder, $vPriceRangeSKUList) ?>