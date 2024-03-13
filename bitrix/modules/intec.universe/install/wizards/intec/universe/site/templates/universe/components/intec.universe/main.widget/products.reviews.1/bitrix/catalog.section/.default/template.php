<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];
$arSvg = [
    'RATING' => [
        'DECORATION' => FileHelper::getFileData(__DIR__.'/svg/rating.decoration.svg')
    ],
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/slider.arrow.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/slider.arrow.right.svg')
    ]
];

$isSlider = count($arResult['ITEMS']) > 1;

?>
<div class="widget c-widget c-widget-products-reviews-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['HEADER']['TEXT'], [
                            'class' => [
                                'widget-title',
                                'align-'.$arBlocks['HEADER']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-'.$arBlocks['DESCRIPTION']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content widget-items">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items-slider' => true,
                        'owl-carousel' => $isSlider,
                        'intec-grid' => !$isSlider
                    ], true),
                    'data-role' => $isSlider ? 'slider' : null
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sIdProduct = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaIdProduct = $this->GetEditAreaId($sIdProduct);
                        $this->AddEditAction($sIdProduct, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sIdProduct, $arItem['DELETE_LINK']);

                        $sIdReview = $sTemplateId.'_'.$arItem['REVIEW']['ID'];
                        $sAreaIdReview = $this->GetEditAreaId($sIdReview);
                        $this->AddEditAction($sIdReview, $arItem['REVIEW']['EDIT_LINK']);
                        $this->AddDeleteAction($sIdReview, $arItem['REVIEW']['DELETE_LINK']);

                        $sPicture = null;

                        if (!empty($arItem['PREVIEW_PICTURE']))
                            $sPicture = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], [
                                'width' => 100,
                                'height' => 100
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                        if (empty($sPicture) && !empty($arItem['DETAIL_PICTURE']))
                            $sPicture = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], [
                                'width' => 100,
                                'height' => 100
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                        else
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    '2' => !$isSlider,
                                    '768-1' => !$isSlider
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-item-content">
                                <div class="widget-item-content-block" id="<?= $sAreaIdProduct ?>">
                                    <div class="intec-grid intec-grid-600-wrap intec-grid-a-v-center intec-grid-i-h-12 intec-grid-i-v-4">
                                        <div class="intec-grid-item-auto">
                                            <?= Html::beginTag($arVisual['LINK']['USE'] ? 'a' : 'div', [
                                                'class' => [
                                                    'widget-item-product-picture',
                                                    'intec-ui-picture',
                                                    'intec-image-effect'
                                                ],
                                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                            ]) ?>
                                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                    'alt' => !empty($arPicture['ALT']) ? $arPicture['ALT'] : $arItem['NAME'],
                                                    'title' => !empty($arPicture['TITLE']) ? $arPicture['TITLE'] : $arItem['NAME'],
                                                    'data' => [
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                    ]
                                                ]) ?>
                                            <?= Html::endTag($arVisual['LINK']['USE'] ? 'a' : 'div') ?>
                                        </div>
                                        <div class="intec-grid-item intec-grid-item-shrink-1">
                                            <div class="widget-item-product-name">
                                                <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'span', $arItem['NAME'], [
                                                    'class' => Html::cssClassFromArray([
                                                        'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                                    ], true),
                                                    'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                                    'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                            </div>
                                            <?php if ($arVisual['PRICE']['SHOW'] && !empty($arItem['MIN_PRICE'])) { ?>
                                                <div class="widget-item-product-price">
                                                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-4">
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-product-price-current">
                                                                <?php if (!empty($arItem['OFFERS'])) { ?>
                                                                    <span>
                                                                        <?= Loc::getMessage('C_WIDGET_PRODUCTS_REVIEWS_1_TEMPLATE_PRICE_FROM') ?>
                                                                    </span>
                                                                <?php } ?>
                                                                <span>
                                                                    <?= $arItem['MIN_PRICE']['PRINT_DISCOUNT_VALUE'] ?>
                                                                </span>
                                                            </div>
                                                        </div>
                                                        <?php if ($arVisual['PRICE']['DISCOUNT']['SHOW'] && $arItem['MIN_PRICE']['DISCOUNT_DIFF'] > 0) { ?>
                                                            <div class="intec-grid-item-auto">
                                                                <div class="widget-item-product-price-discount">
                                                                    <?= $arItem['MIN_PRICE']['PRINT_VALUE'] ?>
                                                                </div>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                        </div>
                                        <?php if ($arVisual['RATING']['SHOW']) { ?>
                                            <div class="intec-grid-item-auto intec-grid-item-600-1">
                                                <div class="widget-item-review-rating">
                                                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-4">
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-review-rating-decoration intec-ui-picture">
                                                                <?= $arSvg['RATING']['DECORATION'] ?>
                                                            </div>
                                                        </div>
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-review-rating-value">
                                                                <?= $arItem['DATA']['RATING'] ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <?php if (!empty($arItem['REVIEW'])) { ?>
                                    <div class="widget-item-content-block" id="<?= $sAreaIdReview ?>">
                                        <div class="widget-item-review-base">
                                            <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-4">
                                                <div class="intec-grid-item intec-grid-item-shrink-1">
                                                    <div class="widget-item-review-name">
                                                        <?= $arItem['REVIEW']['NAME'] ?>
                                                    </div>
                                                </div>
                                                <?php if ($arItem['REVIEW']['DATA']['DATE']['SHOW']) { ?>
                                                    <div class="intec-grid-item-auto intec-grid-item-600-1">
                                                        <div class="widget-item-review-date">
                                                            <?= $arItem['REVIEW']['DATA']['DATE']['VALUE'] ?>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="widget-item-review-preview">
                                            <div class="widget-item-review-preview-scroll scrollbar-outer" data-role="scroll">
                                                <?= $arItem['REVIEW']['DATA']['PREVIEW'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
                <?php if ($isSlider) { ?>
                    <div class="widget-items-navigation" data-role="slider.navigation">
                        <div class="intec-grid intec-grid-a-h-center intec-grid-a-v-center">
                            <div class="widget-items-navigation-container-left intec-grid-item-auto">
                                <?= Html::tag('button', $arSvg['NAVIGATION']['LEFT'], [
                                    'class' => [
                                        'widget-items-navigation-button',
                                        'intec-ui-picture',
                                        'intec-cl-background-hover',
                                        'intec-cl-border-hover'
                                    ],
                                    'data-role' => 'slider.navigation.left'
                                ]) ?>
                            </div>
                            <div class="widget-items-navigation-counter intec-grid-item-auto">
                                <span data-role="slider.navigation.counter">
                                    1
                                </span>
                                <span>
                                    /
                                </span>
                                <span>
                                    <?= count($arResult['ITEMS']) ?>
                                </span>
                            </div>
                            <div class="widget-items-navigation-container-right intec-grid-item-auto">
                                <?= Html::tag('button', $arSvg['NAVIGATION']['RIGHT'], [
                                    'class' => [
                                        'widget-items-navigation-button',
                                        'intec-ui-picture',
                                        'intec-cl-background-hover',
                                        'intec-cl-border-hover'
                                    ],
                                    'data-role' => 'slider.navigation.right'
                                ]) ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>
