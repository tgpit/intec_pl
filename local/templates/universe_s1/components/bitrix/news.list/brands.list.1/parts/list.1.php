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
<div class="news-list-items">
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
            'class' => [
                'news-list-item'
            ]
        ]) ?>
            <?= Html::beginTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a', [
                'href' => !$arItem['DATA']['HIDE_LINK'] ? $arItem['DETAIL_PAGE_URL'] : null,
                'class' => Html::cssClassFromArray([
                    'news-list-item-wrapper' => true,
                    'intec-grid' => [
                        '' => true,
                        '1000-wrap' => !$arVisual['WIDE'],
                        '600-wrap' => $arVisual['WIDE'],
                        'important' => true,
                        'a-v-center' => true
                    ]
                ],  true)
            ]) ?>
                <?= Html::beginTag('div', [
                    'id' => $sAreaId,
                    'class' => Html::cssClassFromArray([
                        'news-list-item-picture-wrap' => true,
                        'intec-grid-item' => [
                            '5' => true,
                            '1000-1' => !$arVisual['WIDE'],
                            '600-1' => $arVisual['WIDE']
                        ]
                    ],  true)
                ]) ?>
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
                <?= Html::endTag('div') ?>
                <?php if ($arVisual['NAME']['SHOW'] || ($arVisual['DESCRIPTION']['SHOW'] && !empty($arItem['PREVIEW_TEXT']))) { ?>
                    <div class="news-list-item-text intec-grid-item">
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
                    </div>
                <?php } ?>
            <?= Html::endTag($arItem['DATA']['HIDE_LINK'] ? 'div' : 'a') ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>