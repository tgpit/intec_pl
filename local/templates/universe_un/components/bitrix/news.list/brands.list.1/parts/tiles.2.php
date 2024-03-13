<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 * @var array $arLazyLoad
 */

?>
<?= Html::beginTag('div', [
    'class' => [
        'news-list-items',
        'intec-grid' => [
            '',
            'wrap',
            'a-v-stretch'
        ]
    ]
]) ?>
    <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
    <?php
        $sId = $sTemplateId.'_'.$arItem['ID'];
        $sAreaId = $this->GetEditAreaId($sId);
        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);
        $sPicture = null;

        if (!empty($arItem['PREVIEW_PICTURE'])) {
            $sPicture = $arItem['PREVIEW_PICTURE'];
        } else if (!empty($arItem['DETAIL_PICTURE'])) {
            $sPicture = $arItem['DETAIL_PICTURE'];
        }

        $sPicture = CFile::ResizeImageGet($sPicture, array(
            'width' => 400,
            'height' => 200
        ), BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        if (!empty($sPicture)) {
            $sPicture = $sPicture['src'];
        } else {
            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
        }
    ?>
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => Html::cssClassFromArray([
                'news-list-item' => true,
                'intec-grid-item' => [
                    $arVisual['COLUMNS'] => true,
                    '400-1' => $arVisual['COLUMNS'] <= 4,
                    '800-2' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                    '1000-3' => $arVisual['WIDE'] && $arVisual['COLUMNS'] > 3,
                    '700-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                    '720-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                    '950-2' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 2,
                    '1200-3' => !$arVisual['WIDE'] && $arVisual['COLUMNS'] > 3
                ]
            ],  true)
        ]) ?>
            <?= Html::beginTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a', [
                'class' => 'news-list-item-wrapper',
                'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null
            ]) ?>
                <div class="news-list-item-picture-wrap">
                    <?= Html::tag('div', '', [
                        'class' => [
                            'news-list-item-picture'
                        ],
                        'data' => [
                            'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                            'original' => $arLazyLoad['USE'] ? $sPicture : null
                        ],
                        'style' => [
                            'background-image' => !$arLazyLoad['USE'] ? 'url(\''.$sPicture.'\')' : null
                        ],
                        'title' => $arItem['NAME'],
                        'alt' => $arItem['NAME']
                    ]) ?>
                </div>
                <?php if ($arVisual['NAME']['SHOW']) { ?>
                    <div class="news-list-item-name">
                        <?= $arItem['NAME'] ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                    <div class="news-list-item-description">
                        <?= $arItem['PREVIEW_TEXT'] ?>
                    </div>
                <?php } ?>
            <?= Html::endTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a') ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
<?= Html::endTag('div') ?>