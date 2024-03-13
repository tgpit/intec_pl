<?php if (defined('B_PROLOG_INCLUDED') && B_PROLOG_INCLUDED === true) ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CBitrixComponent $component
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));
$arVisual = $arResult['VISUAL'];
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-catalog-section',
        'c-catalog-section-services-small-1'
    ],
    'data' => [
        'borders' => $arVisual['BORDERS'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS'],
        'position' => $arVisual['POSITION'],
        'size' => $arVisual['SIZE'],
        'wide' => $arVisual['WIDE'] ? 'true' : 'false',
        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false',
        'slider-dots' => $arVisual['SLIDER']['DOTS'] ? 'true' : 'false',
        'slider-navigation' => $arVisual['SLIDER']['NAVIGATION'] ? 'true' : 'false'
    ]
]) ?>
    <div class="catalog-section-wrapper intec-content intec-content-visible">
        <div class="catalog-section-wrapper-2 intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-section-items' => true,
                    'owl-carousel' => $arVisual['SLIDER']['USE'],
                    'intec-grid' => !$arVisual['SLIDER']['USE'] ? [
                        '' => true,
                        'wrap' => true,
                        'a-h-start' => $arVisual['POSITION'] === 'left',
                        'a-h-center' => $arVisual['POSITION'] === 'center',
                        'a-h-end' => $arVisual['POSITION'] === 'right',
                        'a-v-stretch' => true,
                        'i-5' => true
                    ] : []
                ], true),
                'data' => [
                    'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                ]
            ]) ?>
                <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                <?php
                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $sName = $arItem['NAME'];
                    $sLink = $arItem['DETAIL_PAGE_URL'];
                    $sPrice = $arItem['DATA']['PRICE']['BASE']['DISPLAY'];
                    $sPriceOld = $arItem['DATA']['PRICE']['OLD']['DISPLAY'];

                    $arPictureSize = [
                        'width' => 115,
                        'height' => 115
                    ];

                    $sPicture = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], $arPictureSize, BX_RESIZE_IMAGE_EXACT);

                    if (!empty($sPicture)) {
                        $sPicture = $sPicture['src'];
                    } else {
                        $sPicture = SITE_TEMPLATE_PATH . '/images/picture.missing.png';
                    }

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'catalog-section-item' => true,
                            'intec-grid-item' => $arVisual['WIDE'] ? [
                                $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                '1000-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 3,
                                '720-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 2,
                                '450-1' => !$arVisual['SLIDER']['USE']
                            ] : [
                                $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                '1100-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 3,
                                '800-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 2,
                                '500-1' => !$arVisual['SLIDER']['USE']
                            ]
                        ], true)
                    ]) ?>
                        <div id="<?= $sAreaId ?>" class="catalog-section-item-wrapper">
                            <a href="<?= $sLink ?>" class="catalog-section-item-image intec-ui-picture intec-image-effect">
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                    'alt' => Html::encode($sName),
                                    'title' => Html::encode($sName),
                                    'loading' => 'lazy',
                                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]) ?>
                            </a>
                            <div class="catalog-section-item-information">
                                <div class="catalog-section-item-name intec-cl-text-hover">
                                    <a href="<?= $sLink ?>" class="catalog-section-item-name-wrapper">
                                        <?= $sName ?>
                                    </a>
                                </div>
                                <div class="catalog-section-item-price-holder">
                                    <?php if (!empty($sPriceOld)) { ?>
                                        <div class="catalog-section-item-price-old">
                                            <?= $sPriceOld ?>
                                        </div>
                                    <?php } ?>
                                    <?php if (!empty($sPrice)) { ?>
                                        <div class="catalog-section-item-price">
                                            <?= $sPrice ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="catalog-section-clearfix"></div>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
