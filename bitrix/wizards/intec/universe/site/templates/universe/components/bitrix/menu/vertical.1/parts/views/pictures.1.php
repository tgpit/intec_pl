<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arParams
 * @var array $arResult
 * @var Closure $fView
 */

/**
 * @param array $arItems
 * @param integer $iLevel
 */
return function ($arItems, $iLevel) use (&$sView, &$fView, &$arVisual) { ?>
    <div class="menu-item-submenu-wrapper intec-grid intec-grid-wrap intec-grid-a-h-start intec-grid-a-v-center intec-grid-i-h-15 intec-grid-i-v-15">
        <?php foreach ($arItems as $arItem) { ?>
        <?php
            $bSelected = ArrayHelper::getValue($arItem, 'SELECTED');
            $bSelected = Type::toBoolean($bSelected);
            $bActive = ArrayHelper::getValue($arItem, 'ACTIVE');
            $sTag = $bActive ? 'div' : 'a';

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
            <?= Html::beginTag($sTag, [
                'class' => Html::cssClassFromArray([
                    'menu-item-submenu-item' => true,
                    'intec-grid-item-3' => true,
                    'intec-cl' => [
                        'text' => $bSelected,
                        'text-hover' => !$bSelected,
                    ]
                ], true),
                'href' => !$bActive ? $arItem['LINK'] : null,
                'data' => [
                    'active' => $bActive ? 'true' : 'false',
                    'selected' => $bSelected ? 'true' : 'false',
                    'role' => 'item',
                    'level' => $iLevel
                ]
            ]) ?>
                <div class="menu-item-submenu-item-wrapper intec-grid intec-grid-nowrap intec-grid-i-h-10">
                    <div class="menu-item-submenu-item-part intec-grid-item-auto">
                        <?php if ($arImage['TYPE'] === 'svg') { ?>
                            <?= Html::tag('div', FileHelper::getFileData('@root/'.$arImage['SOURCE']), [
                                'class' => [
                                    'menu-item-submenu-item-picture',
                                    'intec-cl-svg',
                                    'intec-image-effect'
                                ]
                            ]) ?>
                        <?php } else { ?>
                            <?= Html::tag('div', null, [
                                'class' => [
                                    'menu-item-submenu-item-picture',
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
                    <div class="menu-item-submenu-item-part intec-grid-item">
                        <div class="menu-item-submenu-item-text">
                            <?= $arItem['TEXT'] ?>
                        </div>
                    </div>
                </div>
            <?= Html::endTag($sTag) ?>
        <?php } ?>
    </div>
<?php };