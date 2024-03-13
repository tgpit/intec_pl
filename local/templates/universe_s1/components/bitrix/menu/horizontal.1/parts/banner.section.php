<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\core\helpers\FileHelper;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var int $iLevel
 * @var array $arItem
 * @var array $arItems
 */

$bFirstItem = true;

?>
<div class="menu-submenu menu-submenu-<?=$iLevel?> menu-submenu-banner-section <?=$sLongMenu?>" data-role="menu" data-menu="menu<?=$iCount?>">
    <div class="menu-submenu-banner-section-wrapper intec-grid">
        <div class="menu-submenu-main-section scrollbar-inner intec-grid-item" data-role="scrollbar">
            <div class="menu-submenu-main-section-wrapper">
                <?php foreach ($arItems as $arItem) { ?>
                    <?php
                        $bActive = $arItem['ACTIVE'];
                        $sUrl = $bActive ? null : $arItem['LINK'];
                        $sTag = $bActive ? 'div' : 'a';
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
                    ?>
                    <div class="menu-submenu-main-section-item <?= $bSelected ? 'active' : null ?>" data-role="main-item">
                        <?= Html::beginTag($sTag, array(
                            'class' => Html::cssClassFromArray([
                                'menu-submenu-main-section-item-text' => true,
                                'intec' => [
                                    'grid' => true,
                                    'grid-a-v-center' => true,
                                    'cl-text' => $bSelected
                                ]
                            ], true),
                            'href' => $arItem['LINK']
                        )); ?>
                            <?php if ($arParams['SECTION_BANNER_SHOW_ICONS_ROOT_ITEMS'] === 'Y') { ?>
                                <div class="intec-grid-item-auto">
                                    <?php if ($arImage['TYPE'] === 'svg') { ?>
                                        <?= Html::tag('div', FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                            'class' => [
                                                'menu-submenu-main-section-image',
                                                'intec-cl-svg',
                                                'intec-image-effect',
                                                'intec-ui-picture'
                                            ]
                                        ]) ?>
                                    <?php } else { ?>
                                        <?= Html::tag('div', null, [
                                            'class' => [
                                                'menu-submenu-main-section-image',
                                                'intec-image-effect'
                                            ],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arImage['SOURCE'] : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arImage['SOURCE'].'\')' : null
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                            <div class="intec-grid-item">
                                <?= Html::encode($arItem['TEXT']) ?>
                            </div>
                            <?php if (!empty($arItem['ITEMS'])) { ?>
                                <div class="intec-grid-item-auto">
                                    <i class="fa fa-angle-right" aria-hidden="true"></i>
                                </div>
                            <?php } ?>
                        <?= Html::endTag($sTag) ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <div class="menu-submenu-section intec-grid-item">
            <div class="menu-submenu-section-wrapper">
                <?php foreach ($arItems as $arItemMain) {
                    if (empty($arItemMain['ITEMS']) && empty($arItemMain['PARAMS']['BANNER'])) {
                        echo Html::tag('div','',array(
                           'class' => [
                                   'menu-submenu-section-items-container'
                           ],
                           'data' => [
                                   'role' => 'menu-item-content'
                           ]
                        ));
                        continue;
                    } else {
                        include('banner.section.items.php');
                    }
                } ?>
            </div>
        </div>
    </div>
</div>