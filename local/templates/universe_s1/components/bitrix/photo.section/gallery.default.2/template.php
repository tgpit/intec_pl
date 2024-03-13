<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\ArrayHelper;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arSvg = [
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/navigation.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/navigation.right.svg'),
    ],
    'BACK' => [
        'ICON' => FileHelper::getFileData(__DIR__.'/svg/back.icon.svg')
    ]
];

?>
<div class="ns-bitrix c-photo-section c-photo-section-default-2" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="photo-section-content">
                <div class="photo-section-content-block">
                    <div class="photo-section-main" data-role="main">
                        <?= Html::tag('div', null, [
                            'class' => [
                                'photo-section-loader',
                                'photo-section-loader-slider',
                                'photo-section-main-picture'
                            ],
                            'data-role' => 'main.slider.loader'
                        ]) ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'photo-section-main-slider',
                                'owl-carousel'
                            ],
                            'data' => [
                                'role' => 'main.slider',
                                'loaded' => 'false'
                            ]
                        ]) ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sPicture = null;
                                $sPicturePreview = null;

                                if (!empty($arItem['PREVIEW_PICTURE']))
                                    $arPicture = $arItem['PREVIEW_PICTURE'];
                                else if (!empty($arItem['DETAIL_PICTURE']))
                                    $arPicture = $arItem['PREVIEW_PICTURE'];
                                else
                                    $arPicture = [];

                                if (!empty($arPicture)) {
                                    $sPicture = CFile::ResizeImageGet($arPicture, [
                                        'width' => 1600,
                                        'height' => 1600
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];

                                    $sPicturePreview = CFile::ResizeImageGet($arPicture, [
                                        'width' => 250,
                                        'height' => 250
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                    if (!empty($sPicturePreview))
                                        $sPicturePreview = $sPicturePreview['src'];
                                }

                                if (empty($sPicture))
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                if (empty($sPicturePreview))
                                    $sPicturePreview = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                            ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'photo-section-main-picture',
                                    'data' => [
                                        'role' => 'main.slider.item',
                                        'src' => $sPicture,
                                        'exthumbimage' => $sPicturePreview
                                    ]
                                ]) ?>
                                    <div class="photo-section-main-picture-container intec-ui-picture">
                                        <?= Html::img(null, [
                                            'class' => 'owl-lazy',
                                            'alt' => $arItem['NAME'],
                                            'title' => $arItem['NAME'],
                                            'loading' => 'lazy',
                                            'data-src' => $sPicture
                                        ]) ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                        <div class="photo-section-navigation" data-role="main.slider.navigation"></div>
                    </div>
                    <div class="photo-section-main-count" data-role="main.count" data-loaded="false">
                        <?= Html::tag('div', null, [
                            'class' => [
                                'photo-section-loader',
                                'photo-section-loader-count',
                            ],
                            'data-role' => 'main.count.loader'
                        ]) ?>
                        <div class="photo-section-main-count-content" data-role="main.count.content" data-loaded="false">
                            <span data-role="main.count.value">
                                1
                            </span>
                            <span>
                                /
                            </span>
                            <span>
                                <?= count($arResult['ITEMS']) ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="photo-section-content-block">
                    <div class="photo-section-preview" data-role="preview" data-loaded="false">
                        <div class="photo-section-loader-preview" data-role="preview.loader">
                            <div class="intec-grid intec-grid-i-h-8">
                                <?php for ($count = 1; $count <= 6; $count++) { ?>
                                    <div class="intec-grid-item">
                                        <div class="photo-section-loader-preview-item">
                                            <div class="photo-section-loader photo-section-preview-picture"></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="photo-section-preview-slider owl-carousel" data-role="preview.slider" data-loaded="false">
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $sPicture = null;

                                if (!empty($arItem['PREVIEW_PICTURE']))
                                    $arPicture = $arItem['PREVIEW_PICTURE'];
                                else if (!empty($arItem['DETAIL_PICTURE']))
                                    $arPicture = $arItem['PREVIEW_PICTURE'];
                                else
                                    $arPicture = [];

                                if (!empty($arPicture)) {
                                    $sPicture = CFile::ResizeImageGet($arPicture, [
                                        'width' => 250,
                                        'height' => 250
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];
                                }

                                if (empty($sPicture))
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                            ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sAreaId,
                                    'class' => 'photo-section-preview-item',
                                    'data-role' => 'preview.item'
                                ]) ?>
                                    <?= Html::tag('div', null, [
                                        'class' => [
                                            'photo-section-preview-picture',
                                            'owl-lazy'
                                        ],
                                        'title' => $arItem['NAME'],
                                        'data-src' => $sPicture
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                        <div class="photo-section-navigation" data-role="preview.navigation"></div>
                    </div>
                </div>
            </div>
            <div class="photo-section-footer">
                <div class="photo-section-back">
                    <?= Html::beginTag('a', [
                        'class' => [
                            'photo-section-back-button',
                            'intec-cl-text-hover',
                            'intec-cl-svg-path-stroke-hover'
                        ],
                        'href' => $arResult['LIST_PAGE_URL']
                    ]) ?>
                        <div class="intec-grid intec-grid-i-h-4 intec-grid-a-v-center">
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', $arSvg['BACK']['ICON'], [
                                    'class' => [
                                        'photo-section-back-icon',
                                        'intec-ui-picture'
                                    ]
                                ]) ?>
                            </div>
                            <div class="intec-grid-item">
                                <div class="photo-section-back-content">
                                    <?= Loc::getMessage('C_PHOTO_SECTION_GALLERY_DEFAULT_2_TEMPLATE_BACK_DEFAULT') ?>
                                </div>
                            </div>
                        </div>
                    <?= Html::endTag('a') ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>