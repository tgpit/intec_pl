<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\StringHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var int $iLevel
 * @var array $arItem
 * @var array $arItems
 */

?>
<div class="menu-submenu-section-items-container" data-role="menu-item-content">
    <div class="menu-submenu-section-items-wrapper-2 scrollbar-inner" data-role="scrollbar">
        <div class="menu-susection-items-container intec-grid intec-grid-a-v-start">
            <?php if (!empty($arItemMain['ITEMS'])) {
                $sStub = Properties::get('template-images-lazyload-stub');?>
                <div class="menu-submenu-items intec-grid-item intec-grid intec-grid-wrap">
                    <?php foreach ($arItemMain['ITEMS'] as $arItem) { ?>
                        <?php
                        $bActive = $arItem['ACTIVE'];
                        $bSelected = ArrayHelper::getValue($arItem, 'SELECTED');
                        $bSelected = Type::toBoolean($bSelected);

                        $arImage = [
                            'TYPE' => 'picture',
                            'SOURCE' => null
                        ];

                        if (!empty($arItem['IMAGE'])) {
                            if ($arItem['IMAGE']['CONTENT_TYPE'] === 'image/svg+xml') {
                                $arImage['TYPE'] = 'svg';
                                $arImage['SOURCE'] = $arItem['IMAGE']['SRC'];
                            } else {
                                $arImage['SOURCE'] = CFile::ResizeImageGet($arItem['IMAGE'], array(
                                    'width' => 90,
                                    'height' => 90
                                ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($arImage['SOURCE'])) {
                                    $arImage['SOURCE'] = $arImage['SOURCE']['src'];
                                } else {
                                    $arImage['SOURCE'] = null;
                                }
                            }
                        }

                        if (empty($arImage['SOURCE'])) {
                            $arImage['TYPE'] = 'picture';
                            $arImage['SOURCE'] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        }

                        $sUrl = $bActive ? null : $arItem['LINK'];
                        $sTag = $bActive ? 'div' : 'a';
                        ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'menu-submenu-section' => true,
                                'menu-submenu-section-with-images' => true,
                                'menu-submenu-section-active' => $bSelected,
                                'intec-grid-item' => [
                                    $arVisual['SECTION']['COLUMNS'] => true,
                                    '1100-3' => $arVisual['SECTION']['COLUMNS'] >= 4,
                                    '1000-2' => $arVisual['SECTION']['COLUMNS'] >= 3
                                ]
                            ], true)
                        ]) ?>
                            <div class="menu-submenu-section-wrapper">
                                <div class="menu-submenu-banner-section-links">
                                    <div class="menu-submenu-section-header">
                                        <?= Html::beginTag($sTag, array(
                                            'class' => [
                                                'menu-submenu-section-header-wrapper',
                                                'intec-grid',
                                                'intec-grid-a-v-center',
                                                $bActive ? 'intec-cl-text' :'intec-cl-text-hover'
                                            ],
                                            'href' => $sUrl
                                        )); ?>
                                            <div class="intec-grid-item-auto">
                                                <div class="menu-submenu-banner-section-image intec-image-effect intec-cl-svg intec-ui-picture">
                                                    <?php if ($arImage['TYPE'] === 'svg') { ?>
                                                        <?= FileHelper::getFileData('@root/'.$arImage['SOURCE']) ?>
                                                    <?php } else { ?>
                                                        <?= Html::img(!$arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : $sStub, [
                                                            'alt' => $arItem['TEXT'],
                                                            'data' => [
                                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                                            ]
                                                        ]) ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item">
                                                <?= $arItem['TEXT'] ?>
                                            </div>
                                        <?= Html::endTag($sTag) ?>
                                    </div>
                                    <?php if (!empty($arItem['ITEMS'])) { ?>
                                        <div class="menu-submenu-section-items">
                                            <div class="menu-submenu-section-items-wrapper">
                                                <?php $iSubItemsCount = 0 ?>
                                                <?php foreach ($arItem['ITEMS'] as $arSubItem) { ?>
                                                    <?php
                                                    $iSubItemsCount++;

                                                    if ($iSubItemsCount > $arVisual['SECTION']['ITEMS'])
                                                        break;

                                                    $bActive = $arSubItem['ACTIVE'];
                                                    $bSelected = ArrayHelper::getValue($arSubItem, 'SELECTED');
                                                    $bSelected = Type::toBoolean($bSelected);

                                                    $sSubUrl = $bActive ? null : $arSubItem['LINK'];
                                                    $sSubTag = $bActive ? 'div' : 'a';
                                                    ?>
                                                    <div class="menu-submenu-section-item<?= $bSelected ? ' menu-submenu-section-item-active' : null ?>">
                                                        <?= Html::beginTag($sSubTag, array(
                                                            'class' => [
                                                                'menu-submenu-section-item-wrapper',
                                                                $bActive ? 'intec-cl-text' : 'intec-cl-text-hover'
                                                            ],
                                                            'href' => $sSubUrl
                                                        )); ?>
                                                        <?= $arSubItem['TEXT'] ?>
                                                        <?= Html::endTag($sSubTag) ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
            <?php if ($arVisual['BANNER']['SHOW'] && !empty($arItemMain['PARAMS']['BANNER'])) { ?>
                <div class="menu-submenu-items-banner-wrapper intec-grid-item">
                    <div class="menu-submenu-items-banner-wrapper-2">
                        <div class="menu-submenu-items-banner owl-carousel" data-role="slider">
                            <?php foreach ($arItemMain['PARAMS']['BANNER'] as $arItem) { ?>
                                <?php $sDescription = null;
                                    if (!empty($arItem['PREVIEW_TEXT']))
                                        $sDescription =  $arItem['PREVIEW_TEXT'];
                                    else
                                        $sDescription = $arItem['DETAIL_TEXT'];  ?>

                                <?= Html::beginTag(!empty($arItem['PROPERTIES']['LINK']) ? "a" : "div", array(
                                    'class' => [
                                        'menu-submenu-item-container'
                                    ],
                                    'href' => !empty($arItem['PROPERTIES']['LINK']) ? $arItem['PROPERTIES']['LINK'] : null
                                )); ?>
                                    <div class="menu-submenu-item-banner-image" style="background-image: url('<?= $arItem['PROPERTIES']['IMAGE']['SRC'] ?>')"></div>
                                    <?php if ($arVisual['BANNER']['HEADER'] && !empty($arItem['NAME'])) { ?>
                                        <div class="menu-submenu-item-banner-header">
                                            <?= $arItem['NAME'] ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['BANNER']['DESCRIPTION']['SHOW'] && !empty($sDescription)) { ?>
                                        <div class="menu-submenu-item-description">
                                            <?php if ($arVisual['BANNER']['DESCRIPTION']['LIMIT']) { ?>
                                                <?= StringHelper::truncate($sDescription, 70) ?>
                                            <?php } else { ?>
                                                <?= $sDescription ?>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                <?= Html::endTag(!empty($arItem['PROPERTIES']['LINK']) ? "a" : "div") ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
</div>