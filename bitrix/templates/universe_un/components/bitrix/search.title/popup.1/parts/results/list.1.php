<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

?>
<div class="ns-bitrix c-search-title c-search-title-popup-1 search-title-results search-title-results-list-1 intec-content-wrap">
    <div class="search-title-items">
        <?php foreach ($arResult['CATEGORIES'] as $sKey => $arCategory) { ?>
            <?php foreach ($arCategory['ITEMS'] as $arItem) { ?>
                <?php if (!empty($arItem['ITEM_ID'])) { ?>
                <?php
                    $sName = $arItem['NAME'];
                    $sLink = $arItem['URL'];
                    $sImage = null;
                    $arPrices = null;

                    $arSection = $arItem['SECTION'];
                    $arElement = $arItem['ELEMENT'];

                    if (!empty($arElement)) {
                        if (!empty($arElement['PREVIEW_PICTURE'])) {
                            $sImage = $arElement['PREVIEW_PICTURE'];
                        } else if (!empty($arElement['DETAIL_PICTURE'])) {
                            $sImage = $arElement['DETAIL_PICTURE'];
                        }

                        if (!empty($sImage)) {
                            $sImage = CFile::ResizeImageGet($sImage['ID'], [
                                'width' => 80,
                                'height' => 80
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sImage)) {
                                $sImage = $sImage['src'];
                        }

                        if (empty($sImage))
                            $sImage = null;
                    }
                }

                $bHideRequested = false;

                if ($arVisual['REQUESTED']['HIDE']) {
                    $bHideRequested =  !empty(ArrayHelper::getValue($arItem['ELEMENT']['PROPERTIES'], [
                        $arVisual['REQUESTED']['PROPERTY'],
                        'VALUE'
                    ]));
                }

                ?>
                    <div class="search-title-item search-title-item-hover">
                        <div class="search-title-item-wrapper intec-content">
                            <div class="search-title-item-wrapper-2 intec-content-wrapper">
                                <div class="search-title-item-wrapper-3 intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                                    <?php if (!empty($sImage)) { ?>
                                        <div class="search-title-item-image-wrap intec-grid-item-auto">
                                            <?= Html::beginTag('a', [
                                                'class' => 'search-title-item-image',
                                                'href' => $arItem['URL']
                                            ]) ?>
                                                <?= Html::img($sImage, [
                                                    'alt' => $arItem['NAME'],
                                                    'loading' => 'lazy'
                                                ]) ?>
                                            <?= Html::endTag('a') ?>
                                        </div>
                                    <?php } ?>
                                    <div class="search-title-item-content-wrap intec-grid-item intec-grid-item-shrink-1">
                                        <div class="search-title-item-content">
                                            <?= Html::tag('a', $arItem['NAME'], [
                                                'class' => [
                                                    'search-title-item-name',
                                                    'intec-cl-text-hover'
                                                ],
                                                'href' => $arItem['URL']
                                            ]) ?>
                                        </div>
                                        <?php if (!empty($arElement['PRICES']) && !$bHideRequested) { ?>
                                            <div class="search-title-item-prices">
                                                <?php foreach ($arElement['PRICES'] as $arPrice) { ?>
                                                    <div class="search-title-item-price">
                                                        <div class="search-title-item-price-current">
                                                            <?= $arPrice['PRINT_DISCOUNT_VALUE'] ?>
                                                        </div>
                                                        <?php if (!empty($arPrice['DISCOUNT_DIFF'])) { ?>
                                                            <div class="search-title-item-price-discount">
                                                                <?= $arPrice['PRINT_VALUE'] ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else if ($sKey === 'all') { ?>
                    <div class="search-title-item search-title-item-all">
                        <div class="search-title-item-wrapper intec-content">
                            <div class="search-title-item-wrapper-2 intec-content-wrapper">
                                <div class="search-title-item-wrapper-3 intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                                    <div class="search-title-item-content-wrap intec-grid-item-auto intec-grid-item-shrink-1">
                                        <div class="search-title-item-content">
                                            <a href="<?= $arItem['URL'] ?>" class="intec-ui intec-ui-control-button intec-ui-scheme-current intec-ui-mod-round-2 search-title-item-button">
                                                <?= $arItem['NAME'] ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="search-title-item">
                        <div class="search-title-item-wrapper intec-content">
                            <div class="search-title-item-wrapper-2 intec-content-wrapper">
                                <div class="search-title-item-wrapper-3 intec-grid intec-grid-nowrap intec-grid-i-h-10 intec-grid-a-v-center">
                                    <div class="search-title-item-content-wrap intec-grid-item intec-grid-item-shrink-1">
                                        <div class="search-title-item-content">
                                            <a href="<?= $arItem['URL'] ?>" class="search-title-item-name">
                                                <?= $arItem['NAME'] ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } ?>
    </div>
</div>