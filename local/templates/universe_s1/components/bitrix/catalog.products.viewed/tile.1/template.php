<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arParams
 * @var array $arResult
 */

if (empty($arResult['ITEMS']))
    return

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'ns-bitrix',
        'c-catalog-products-viewed',
        'c-catalog-products-viewed-tile-1'
    ],
    'data' => [
        'collapsed' => $arVisual['COLUMNS'] > 5 ? 'true' : 'false'
    ]
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <div class="catalog-products-viewed">
                <?php if ($arVisual['TITLE']['SHOW'] && !empty($arVisual['TITLE']['VALUE'])) { ?>
                    <div class="widget-header">
                        <div class="widget-title">
                            <?= $arVisual['TITLE']['VALUE'] ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="widget-content">
                    <div class="catalog-products-viewed-items">
                        <div class="owl-carousel" data-role="slider">
                            <?php $iIndex = 0 ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                            <?php
                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $arPrice = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);
                                $sPicture = null;
                                $sPictureAlt = $arItem['NAME'];
                                $sPictureTitle = $arItem['NAME'];

                                if (!empty($arItem['IPROPERTY_VALUES'])) {
                                    if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'])) {
                                        $sPictureAlt = $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_ALT'];
                                    } else if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'])) {
                                        $sPictureAlt = $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_ALT'];
                                    }

                                    if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'])) {
                                        $sPictureTitle = $arItem['IPROPERTY_VALUES']['ELEMENT_PREVIEW_PICTURE_FILE_TITLE'];
                                    } else if (!empty($arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'])) {
                                        $sPictureTitle = $arItem['IPROPERTY_VALUES']['ELEMENT_DETAIL_PICTURE_FILE_TITLE'];
                                    }
                                }

                                if (!empty($arItem['PREVIEW_PICTURE'])) {
                                    $sPicture = $arItem['PREVIEW_PICTURE'];
                                } else if (!empty($arItem['DETAIL_PICTURE'])) {
                                    $sPicture = $arItem['DETAIL_PICTURE'];
                                }

                                if (!empty($sPicture))
                                    $sPicture = CFile::ResizeImageGet($sPicture['ID'], array(
                                        'width' => 100,
                                        'height' => 100
                                    ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($sPicture)) {
                                    $sPicture = $sPicture['src'];
                                } else {
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                }

                                $bHideRequested = false;

                                if ($arVisual['REQUESTED']['HIDE']) {
                                    $bHideRequested =  !empty(ArrayHelper::getValue($arItem['PROPERTIES'], [
                                        $arVisual['REQUESTED']['PROPERTY'],
                                        'VALUE'
                                    ]));
                                }
                            ?>
                                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="catalog-products-viewed-item" id="<?= $sAreaId ?>">
                                    <div class="catalog-products-viewed-item-wrapper intec-grid">
                                        <div class="catalog-products-viewed-image intec-grid-item">
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'catalog-products-viewed-image-wrapper',
                                                    'intec-ui-picture'
                                                ]
                                            ]) ?>
                                                <?= Html::img($sPicture, [
                                                    'alt' => $sPictureAlt,
                                                    'title' => $sPictureTitle,
                                                    'loading' => 'lazy',
                                                    'data' => [
                                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                    ]
                                                ]) ?>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                        <div class="catalog-products-viewed-information intec-grid-item">
                                            <div class="catalog-products-viewed-name">
                                                <div class="catalog-products-viewed-name-wrapper intec-cl-text-hover">
                                                    <?= $arItem['NAME'] ?>
                                                </div>
                                            </div>
                                            <?php if (!empty($arPrice) && !$bHideRequested) { ?>
                                                <div class="catalog-products-viewed-price-wrap">
                                                    <div class="catalog-products-viewed-price">
                                                        <?= $arPrice['PRINT_PRICE'] ?>
                                                    </div>
                                                    <?php if ($arPrice['DISCOUNT'] !== 0) { ?>
                                                        <div class="catalog-products-viewed-price-base">
                                                            <?= $arPrice['PRINT_BASE_PRICE'] ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </a>
                                <?php $iIndex++ ?>
                            <?php } ?>
                            <?php while ($iIndex < $arVisual['COLUMNS']) { ?>
                                <div class="catalog-products-viewed-item">
                                    <div class="catalog-products-viewed-item-wrapper blank">
                                    </div>
                                </div>
                                <?php $iIndex++ ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
