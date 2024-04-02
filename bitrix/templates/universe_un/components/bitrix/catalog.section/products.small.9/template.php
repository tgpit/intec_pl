<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-products-small-9'
    ]
]) ?>
    <?php if ($arVisual['TITLE']['SHOW'] || $arVisual['NAVIGATION']['SHOW']) { ?>
        <div class="catalog-section-header">
            <div class="intec-grid intec-grid-a-v-center">
                <?php if ($arVisual['TITLE']['SHOW']) { ?>
                    <div class="intec-grid-item">
                        <div class="catalog-section-title">
                            <?= $arVisual['TITLE']['TEXT'] ?>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($arVisual['NAVIGATION']['SHOW']) { ?>
                    <?= Html::tag('div', null, [
                        'class' => [
                            'catalog-section-navigation',
                            'intec-grid-item-auto'
                        ],
                        'data' => [
                            'role' => 'navigation'
                        ]
                    ]) ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="catalog-section-items owl-carousel" data-role="items">
        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
        <?php
            $arQuantity = [
                'state' => 'empty',
                'text' => Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_UNAVAILABLE'),
                'number' => null,
                'measure' => null
            ];

            $arPrice = [
                'base' => null,
                'discount' => null
            ];

            if ($arItem['CAN_BUY']) {
                if ($arVisual['QUANTITY']['MODE'] === 'number') {
                    $arQuantity['state'] = 'many';
                    $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_AVAILABLE');

                    if ($arItem['CATALOG_QUANTITY'] > 0) {
                        $arQuantity['number'] = $arItem['CATALOG_QUANTITY'];

                        if (!empty($arItem['CATALOG_MEASURE_NAME']))
                            $arQuantity['measure'] = $arItem['CATALOG_MEASURE_NAME'];
                    }
                } else if ($arVisual['QUANTITY']['MODE'] === 'text') {
                    if ($arItem['CATALOG_QUANTITY'] >= $arVisual['QUANTITY']['BOUNDS']['MANY']) {
                        $arQuantity['state'] = 'many';
                        $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_MANY');
                    } else if ($arItem['CATALOG_QUANTITY'] < $arVisual['QUANTITY']['BOUNDS']['MANY'] && $arItem['CATALOG_QUANTITY'] > $arVisual['QUANTITY']['BOUNDS']['FEW']) {
                        $arQuantity['state'] = 'enough';
                        $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_ENOUGH');
                    } else if ($arItem['CATALOG_QUANTITY'] <= $arVisual['QUANTITY']['BOUNDS']['FEW'] && $arItem['CATALOG_QUANTITY'] > 0) {
                        $arQuantity['state'] = 'few';
                        $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_FEW');
                    } else if ($arItem['CATALOG_QUANTITY_TRACE'] === 'N' || $arItem['CATALOG_CAN_BUY_ZERO'] === 'Y') {
                        $arQuantity['state'] = 'many';
                        $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_MANY');
                    }
                } else {
                    $arQuantity['state'] = 'many';
                    $arQuantity['text'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_QUANTITY_AVAILABLE');
                }
            }

            if (!empty($arItem['MIN_PRICE'])) {
                $arPrice['base'] = $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'];

                if ($arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0)
                    $arPrice['discount'] = $arItem['MIN_PRICE']['PRINT_VALUE'];

                if (!empty($arItem['CATALOG_MEASURE_NAME'])) {
                    $arPrice['base'] .= ' / '.$arItem['CATALOG_MEASURE_NAME'];

                    if ($arPrice['discount'] !== null)
                        $arPrice['discount'] .= ' / '.$arItem['CATALOG_MEASURE_NAME'];
                }

                if (!empty($arItem['OFFERS'])) {
                    $arPrice['base'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_PRICE_FROM', [
                        '#PRICE#' => $arPrice['base']
                    ]);

                    if ($arPrice['discount'] !== null)
                        $arPrice['discount'] = Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_PRICE_FROM', [
                            '#PRICE#' => $arPrice['discount']
                        ]);
                }
            }
        ?>
            <?= Html::beginTag('a', [
                'class' => 'catalog-section-item',
                'data' => [
                    'role' => 'item'
                ],
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
                <div class="catalog-section-item-wrapper">
                    <div class="catalog-section-item-images" >
                        <div class="catalog-section-item-images-wrapper owl-carousel" data-role="item.images">
                            <?php if (!empty($arItem['PICTURES']['VALUES'])) { ?>
                                <?php foreach ($arItem['PICTURES']['VALUES'] as $arValue) { ?>
                                    <div class="catalog-section-item-image">
                                        <div class="catalog-section-item-image-wrapper intec-ui-picture">
                                            <?= Html::img($arValue['SRC']) ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="catalog-section-item-image">
                                    <div class="catalog-section-item-image-wrapper intec-ui-picture">
                                        <?= Html::img(SITE_TEMPLATE_PATH.'/images/picture.missing.png') ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="catalog-section-item-name">
                        <?= $arItem['NAME'] ?>
                    </div>
                    <div class="catalog-section-item-information intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-a-h-start intec-grid-i-h-8 intec-grid-i-v-2">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-section-item-quantity',
                                'intec-grid-item'
                            ],
                            'data' => [
                                'quantity-state' => $arQuantity['state']
                            ]
                        ]) ?>
                            <div class="catalog-section-item-quantity-indicator">
                                <div class="catalog-section-item-quantity-indicator-part"></div>
                            </div>
                            <div class="catalog-section-item-quantity-value">
                                <?php if ($arQuantity['text'] !== null) { ?>
                                    <div class="catalog-section-item-quantity-value-text">
                                        <?= $arQuantity['text'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arQuantity['number'] !== null) { ?>
                                    <div class="catalog-section-item-quantity-value-number">
                                        <?= $arQuantity['number'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arQuantity['measure'] !== null) { ?>
                                    <div class="catalog-section-item-quantity-value-measure">
                                        <?= $arQuantity['measure'] ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                        <?php if (!empty($arItem['DATA']['ARTICLE'])) { ?>
                            <div class="catalog-section-item-article intec-grid-item-auto">
                                <?= Loc::getMessage('C_CATALOG_SECTION_PRODUCTS_SMALL_9_TEMPLATE_ARTICLE', [
                                    '#ARTICLE#' => $arItem['DATA']['ARTICLE']
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?php if ($arPrice['base'] !== null) { ?>
                        <div class="catalog-section-item-price">
                            <div class="catalog-section-item-price-base">
                                <?= $arPrice['base'] ?>
                            </div>
                            <?php if ($arPrice['discount'] !== null) { ?>
                                <div class="catalog-section-item-price-discount">
                                    <?= $arPrice['discount'] ?>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?= Html::endTag('a') ?>
        <?php } ?>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>