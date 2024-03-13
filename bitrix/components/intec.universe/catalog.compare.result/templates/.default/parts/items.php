<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>

<?php return function ($arItems, $position = 'left') use (&$arResult, &$arVisual, &$arSvg) { ?>
    <div class="catalog-compare-result-items">
        <div class="catalog-compare-result-items-index">
            <span data-role="items.index" data-position="<?= $position ?>">1</span>
            <?= Loc::getMessage('C_CATALOG_COMPARE_RESULT_DEFAULT_TEMPLATE_INDEX', [
                '#INDEX#' => count($arItems)
            ]) ?>
        </div>
        <div data-role="items.dots" data-position="<?= $position ?>" class="catalog-compare-result-items-dots">
            <?php $iIndexItem = 1; ?>
            <?php foreach ($arItems as $arItem) { ?>
                <div data-role="items.dot" data-index="<?= $iIndexItem ?>" class="catalog-compare-result-items-dot"></div>
                <?php $iIndexItem++; ?>
            <?php } ?>
            <?php unset($arItem, $iIndexItem) ?>
        </div>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-compare-result-items-slider',
                'intec-grid' => [
                    '',
                    'nowrap',
                    'a-h-start',
                    'a-v-center'
                ]
            ],
            'data' => [
                'type' => 'compare.content',
                'role' => 'slider',
                'position' => $position
            ]
        ]) ?>
            <?php $iIndexItem = 0; ?>
            <?php foreach ($arItems as $arItem) { ?>
                <?php
                $sPicture = $arItem['PREVIEW_PICTURE'];

                if (empty($sPicture))
                    $sPicture = $arItem['DETAIL_PICTURE'];

                if (!empty($sPicture)) {
                    $sPicture = $sPicture['SRC'];
                }

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                ?>
                <div class="catalog-compare-result-item intec-grid-item-5 intec-grid-item-768-1" data-role="slide" data-fixed="false" data-index="<?= $iIndexItem ?>">
                    <div class="catalog-compare-result-item-content">
                        <div class="catalog-compare-result-item-picture">
                            <a class="catalog-compare-result-item-picture-wrapper intec-ui-picture intec-image-effect" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                                <?= Html::img(!$arVisual['LAZYLOAD']['USE'] ? $sPicture : $arVisual['LAZYLOAD']['STUB'], [
                                    'alt' => $arItem['NAME'],
                                    'title' => $arItem['NAME'],
                                    'loading' => 'lazy',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ]
                                ]) ?>
                            </a>
                        </div>
                        <a class="catalog-compare-result-item-name intec-cl-text-hover" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                            <?= $arItem['NAME'] ?>
                        </a>
                        <div class="catalog-compare-result-item-information intec-grid intec-grid-nowrap intec-grid-1024-wrap intec-grid-a-v-center intec-grid-i-h-8">
                            <div class="catalog-compare-result-item-price intec-grid-item intec-grid-item-768-1">
                                <?php if (!empty($arItem['MIN_PRICE'])) { ?>
                                    <?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                                <?php } ?>
                            </div>
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', $arSvg['REMOVE'], [
                                    'class' => [
                                        'catalog-compare-result-item-remove-button',
                                        'intec-ui-picture'
                                    ],
                                    'data' => [
                                        'action' => $arItem['~DELETE_URL'],
                                        'role' => 'item.remove'
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arItem['ACTION'] === 'buy' && !empty($arItem['MIN_PRICE']) && $arItem['CAN_BUY']) { ?>
                                <div class="catalog-compare-result-item-purchase intec-grid-item-auto">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'intec-ui',
                                            'intec-ui-control-basket-button',
                                            'catalog-compare-result-item-purchase-button',
                                            'catalog-compare-result-item-purchase-button-add',
                                            'intec-cl-text',
                                            'intec-cl-text-light-hover'
                                        ],
                                        'data' => [
                                            'basket-id' => $arItem['ID'],
                                            'basket-action' => 'add',
                                            'basket-state' => 'none',
                                            'basket-price' => $arItem['MIN_PRICE']['TYPE']
                                        ]
                                    ]) ?>
                                        <span class="intec-ui-part-content">
                                            <i class="glyph-icon-cart"></i>
                                        </span>
                                        <span class="intec-ui-part-effect intec-ui-part-effect-folding intec-cl-background">
                                            <span class="intec-ui-part-effect-wrapper">
                                                <i></i><i></i><i></i><i></i>
                                            </span>
                                        </span>
                                    <?= Html::endTag('div') ?>
                                    <?= Html::beginTag('a', [
                                        'href' => $arResult['URL']['BASKET'],
                                        'class' => [
                                            'catalog-compare-result-item-purchase-button',
                                            'catalog-compare-result-item-purchase-button-added',
                                            'intec-cl-background'
                                        ],
                                        'data' => [
                                            'basket-id' => $arItem['ID'],
                                            'basket-state' => 'none',
                                        ]
                                    ]) ?>
                                        <i class="intec-basket glyph-icon-cart"></i>
                                    <?= Html::endTag('a') ?>
                                </div>
                            <?php } else if ($arItem['ACTION'] === 'detail') { ?>
                                <?= Html::beginTag('a', [
                                    'href' => $arItem['DETAIL_PAGE_URL'],
                                    'class' => [
                                        'catalog-compare-result-item-purchase-button',
                                        'intec-cl-text',
                                        'intec-cl-text-light-hover'
                                    ]
                                ]) ?>
                                    <i class="intec-basket glyph-icon-cart"></i>
                                <?= Html::endTag('a') ?>
                            <?php } ?>
                        </div>
                        <div class="catalog-compare-result-item-price-base">
                            <?php if ($arItem['MIN_PRICE']['VALUE'] != $arItem['MIN_PRICE']['DISCOUNT_VALUE']) { ?>
                                <?= $arItem['MIN_PRICE']['PRINT_VALUE'] ?>
                            <?php } ?>
                        </div>
                    </div>
                    <?= Html::tag('div', $arSvg['FIXED'], [
                        'class' => [
                            'catalog-compare-result-item-fixed-button',
                            'intec-ui-picture'
                        ],
                        'data' => [
                            'role' => 'item.fixed'
                        ]
                    ]) ?>
                </div>
                <?php $iIndexItem++ ?>
            <?php } ?>
            <?php unset($arItem, $iIndexItem) ?>
        <?= Html::endTag('div') ?>
    </div>
<?php } ?>