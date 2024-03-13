<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $arVisual
 */

$arVisual = $arResult['VISUAL'];

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
                'intec-grid-item-auto' => true,
                'intec-grid-item' => [
                    $arVisual['COLUMNS']['DESKTOP'] => true,
                    '375-1' => $arVisual['COLUMNS']['MOBILE'] == 1,
                    '500-2' => true,
                    '768-3' => true,
                    '900-4' => true,
                    '1000-5' => $arVisual['COLUMNS']['DESKTOP'] >= 5
                ]
            ],  true),
            'data-role' => 'item'
        ]) ?>
            <?= Html::beginTag('a', [
                'class' => Html::cssClassFromArray([
                    'news-list-item-wrapper' => true,
                    'news-list-item-wrapper-menu-top' => $arVisual['MENU_POSITION'] == 'top' ? true : false
                ], true),
                'href' => $arItem['DETAIL_PAGE_URL']
            ]) ?>
            <div class="news-list-item-picture-wrap">
                <?= Html::tag('div', '', [
                    'class' => Html::cssClassFromArray([
                        'news-list-item-picture' => true,
                        'news-list-item-picture-menu-top' => $arVisual['MENU_POSITION'] == 'top' ? true : false
                    ], true),
                    'data' => [
                        'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                        'original' => $arLazyLoad['USE'] ? $sPicture : null
                    ],
                    'style' => [
                        'background-image' => !$arLazyLoad['USE'] ? 'url(\''.$sPicture.'\')' : null
                    ]
                ]) ?>
            </div>
            <?php if ($arVisual['NAME']['SHOW']) { ?>
                <div class="news-list-item-name" data-role="item.name">
                    <?= $arItem['NAME'] ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                <div class="news-list-item-description">
                    <?= $arItem['PREVIEW_TEXT'] ?>
                </div>
            <?php } ?>
            <?= Html::endTag('a') ?>
        <?= Html::endTag('div') ?>

    <?php } ?>
<?= Html::endTag('div') ?>