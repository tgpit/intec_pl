<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CBitrixComponent $component
 */

?>
<div class="sale-personal-order-detail-block" data-role="block" data-block="products">
    <div class="sale-personal-order-detail-block-title">
        <div class="intec-grid intec-grid-nowrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-8">
            <div class="intec-grid-item">
                <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_TITLE') ?>
            </div>
            <div class="intec-grid-item-auto">
                <div class="sale-personal-order-detail-block-button intec-cl-svg-path-stroke intec-cl-svg-rect-stroke intec-ui-picture" data-role="collapse" data-state="true">
                    <?= $arSvg['BLOCK_TOGGLE'] ?>
                </div>
            </div>
        </div>
    </div>
    <div class="sale-personal-order-detail-block-content" data-role="content">
        <div class="sale-personal-order-detail-block-products">
            <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-header">
                <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-6">
                    <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="picture"></div>
                    <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="name">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_NAME') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="price">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_PRICE') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="quantity">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_QUANTITY') ?>
                    </div>
                    <div class="sale-personal-order-detail-block-product-item sale-personal-order-detail-block-product-item-header intec-grid-item" data-code="sum">
                        <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_SUM') ?>
                    </div>
                </div>
            </div>
            <?php foreach ($arResult['BASKET'] as $arProduct) { ?>
                <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-mobile">
                    <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-12">
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item-auto">
                            <?= Html::beginTag($arProduct['DETAIL_PAGE_URL'] ? 'a' : 'span', [
                                'href' => $arProduct['DETAIL_PAGE_URL'],
                                'target' => '_blank',
                                'class' => [
                                    'sale-personal-order-detail-block-product-picture',
                                    'intec-ui-picture'
                                ]
                            ]) ?>
                            <?= Html::tag('img', '', [
                                'src' => !empty($arProduct['PICTURE']) ? $arProduct['PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                                'alt' => $arProduct['NAME'],
                                'title' => $arProduct['NAME']
                            ]) ?>
                            <?= Html::endTag('a') ?>
                            <div class="sale-personal-order-detail-block-product-item-text" style="text-align: center;">
                                <?= $arProduct['QUANTITY'].' '.$arProduct['MEASURE_NAME'] ?>
                            </div>
                        </div>
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item">
                            <?= Html::tag($arProduct['DETAIL_PAGE_URL'] ? 'a' : 'span', $arProduct['NAME'], [
                                'href' => $arProduct['DETAIL_PAGE_URL'],
                                'class' => [
                                    'intec-cl-text',
                                    'intec-cl-text-light-hover',
                                    'sale-personal-order-detail-block-product-item-text'
                                ],
                                'style' => [
                                    'margin-bottom' => '8px'
                                ]
                            ]) ?>
                            <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-4" style="margin-bottom: 4px;">
                                <div class="sale-personal-order-detail-block-product-item-text intec-grid-item-3" style="font-weight: 500;">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_PRICE') ?>:
                                </div>
                                <div class="intec-grid-item">
                                    <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-discount">
                                        <?= $arProduct['PRICE_FORMATED'] ?>
                                    </div>
                                    <?php if ($arProduct['PRICE_FORMATED'] != $arProduct['BASE_PRICE_FORMATED']) { ?>
                                        <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-base">
                                            <?= $arProduct['BASE_PRICE_FORMATED'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="intec-grid intec-grid-nowrap intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-h-4">
                                <div class="sale-personal-order-detail-block-product-item-text intec-grid-item-3" style="font-weight: 500;">
                                    <?= Loc::getMessage('C_SALE_PERSONAL_ORDER_DETAIL_TEMPLATE_1_TEMPLATE_BLOCKS_PRODUCTS_SUM') ?>:
                                </div>
                                <div class="intec-grid-item">
                                    <div class="sale-personal-order-detail-block-product-item-text">
                                        <?= $arProduct['FORMATED_SUM'] ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sale-personal-order-detail-block-product sale-personal-order-detail-block-product-desktop">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-6">
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="picture">
                            <?= Html::beginTag($arProduct['DETAIL_PAGE_URL'] ? 'a' : 'span', [
                                'href' => $arProduct['DETAIL_PAGE_URL'],
                                'target' => '_blank',
                                'class' => [
                                    'sale-personal-order-detail-block-product-picture',
                                    'intec-ui-picture'
                                ]
                            ]) ?>
                                <?= Html::tag('img', '', [
                                    'src' => !empty($arProduct['PICTURE']) ? $arProduct['PICTURE']['SRC'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png',
                                    'alt' => $arProduct['NAME'],
                                    'title' => $arProduct['NAME']
                                ]) ?>
                            <?= Html::endTag('a') ?>
                        </div>
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="name">
                            <?= Html::tag($arProduct['DETAIL_PAGE_URL'] ? 'a' : 'span', $arProduct['NAME'], [
                                'href' => $arProduct['DETAIL_PAGE_URL'],
                                'class' => [
                                    'intec-cl-text',
                                    'intec-cl-text-light-hover',
                                    'sale-personal-order-detail-block-product-item-text'
                                ]
                            ]) ?>
                        </div>
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="price">
                            <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-discount">
                                <?= $arProduct['PRICE_FORMATED'] ?>
                            </div>
                            <?php if ($arProduct['PRICE_FORMATED'] != $arProduct['BASE_PRICE_FORMATED']) { ?>
                                <div class="sale-personal-order-detail-block-product-item-text sale-personal-order-detail-block-product-price-base">
                                    <?= $arProduct['BASE_PRICE_FORMATED'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="quantity">
                            <span class="sale-personal-order-detail-block-product-item-text">
                                <?= $arProduct['QUANTITY'].' '.$arProduct['MEASURE_NAME'] ?>
                            </span>
                        </div>
                        <div class="sale-personal-order-detail-block-product-item intec-grid-item" data-code="sum">
                            <span class="sale-personal-order-detail-block-product-item-text">
                                <b><?= $arProduct['FORMATED_SUM'] ?></b>
                            </span>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php unset($arProduct) ?>
        </div>
    </div>
</div>