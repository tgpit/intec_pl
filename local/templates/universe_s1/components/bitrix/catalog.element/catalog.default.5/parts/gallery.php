<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use Bitrix\Main\Localization\Loc;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bSkuDynamic
 */

?>
<?php $vGallery = function (&$arItem, $bOffer = false) use (&$arVisual, &$arResult, &$arSvg) {
    if ($bOffer) {
        $arVideos = $arResult['GALLERY_VIDEO']['OFFERS'][$arItem['ID']];

        if (empty($arVideos) && empty($arItem['GALLERY']['VALUES']))
            return;
    } else {
        $arVideos = $arResult['GALLERY_VIDEO']['PRODUCT'];
    }

    $bCarousel = false;
    $iCountPictures = 0;

    if (!empty($arItem['GALLERY']['VALUES'])) {
        $iCountPictures += count($arItem['GALLERY']['VALUES']);
        $bCarousel = count($arItem['GALLERY']['VALUES']) > 1;
    }

    if (!empty($arVideos)) {
        $iCountPictures += count($arVideos);
        $bCarousel = count($arVideos) > 1;
    }

    if ($arVisual['MAIN_VIEW'] == 1) {
        $arPictureSizes = [
            'width' => 600,
            'height' => 600
        ];
    } else if ($arVisual['MAIN_VIEW'] == 2) {
        $arPictureSizes = [
            'width' => 1000,
            'height' => 1000
        ];
    } else if ($arVisual['MAIN_VIEW'] == 3) {
        $arPictureSizes = [
            'width' => 1200,
            'height' => 1200
        ];
    }
?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => 'catalog-element-gallery-pictures',
            'data' => [
                'role' => 'gallery.pictures',
                'action' => $arVisual['GALLERY']['ACTION'],
                'zoom' => $arVisual['GALLERY']['ZOOM'] ? 'true' : 'false'
            ]
        ]) ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-element-gallery-pictures-slider' => true,
                    'owl-carousel' => $bCarousel
                ], true),
                'data-role' => $bCarousel ? 'gallery.pictures.slider' : null
            ]) ?>
                <?php if (!empty($arItem['GALLERY']['VALUES']) || ($arVisual['GALLERY']['VIDEO']['USE'] && !empty($arVideos))) { ?>
                    <?php foreach ($arItem['GALLERY']['VALUES'] as $arPicture) {
                        $bImageIsGif = $arPicture['CONTENT_TYPE'] == 'image/gif';
                        $arPictureResize['src'] = $arPicture['SRC'];

                        if (!$bImageIsGif) {
                            $arPictureResize = CFile::ResizeImageGet(
                                $arPicture,
                                $arPictureSizes,
                                BX_RESIZE_IMAGE_PROPORTIONAL
                            );
                        }
                    ?>
                        <div class="catalog-element-gallery-pictures-slider-item" data-role="gallery.pictures.item">
                            <?= Html::beginTag($arVisual['GALLERY']['ACTION'] === 'source' ? 'a' : 'div', [
                                'class' => [
                                    'catalog-element-gallery-pictures-slider-item-picture',
                                    'intec-ui-picture'
                                ],
                                'href' => $arVisual['GALLERY']['ACTION'] === 'source' ? $arPicture['SRC'] : null,
                                'target' => $arVisual['GALLERY']['ACTION'] === 'source' ? '_blank' : null,
                                'data' => [
                                    'role' => 'gallery.pictures.item.picture',
                                    'src' => $arVisual['GALLERY']['ACTION'] === 'popup' || $arVisual['GALLERY']['ZOOM'] ? $arPicture['SRC'] : null,
                                    'type' => $arPicture['CONTENT_TYPE'],
                                    'lightGallery' => $arVisual['GALLERY']['ACTION'] === 'popup' ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPictureResize['src'], [
                                    'alt' => $arItem['GALLERY']['PROPERTIES']['ALT'],
                                    'title' => $arItem['GALLERY']['PROPERTIES']['TITLE'],
                                    'loading' => 'lazy',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPictureResize['src'] : null
                                    ]
                                ]) ?>
                            <?= Html::endTag($arVisual['GALLERY']['ACTION'] === 'source' ? 'a' : 'div') ?>
                        </div>
                    <?php } ?>
                    <?php foreach ($arVideos as $sKeyVideo => $arVideo) { ?>
                        <?php if (!empty($arVideo['LINK'])) {
                            $arVideoInfo = youtube_video($arVideo['LINK']);
                            $sVideoPreview = !empty($arVideoInfo['sddefault']) ? $arVideoInfo['sddefault'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        ?>
                            <div class="catalog-element-gallery-pictures-slider-item" data-role="gallery.pictures.item">
                                <?= Html::beginTag('div', [
                                    'class' => 'catalog-element-gallery-pictures-slider-item-video',
                                    'data' => [
                                        'src' => $arVideoInfo['iframe'],
                                        'lightGallery' => 'true'
                                    ]
                                ]) ?>
                                    <?= Html::tag('div', $arSvg['PLAY'], [
                                        'class' => [
                                            'catalog-element-gallery-pictures-slider-item-video-stub',
                                            'intec-image-effect'
                                        ],
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sVideoPreview : null
                                        ],
                                        'style' => [
                                            'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sVideoPreview
                                        ]
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } else { ?>
                            <div class="catalog-element-gallery-pictures-slider-item" data-role="gallery.pictures.item">
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-gallery-pictures-slider-item-video',
                                        'intec-image-effect'
                                    ],
                                    'data' => [
                                        'html' => '#video-'.$arItem['ID'].$sKeyVideo,
                                        'role' => 'gallery.video',
                                        'lightGallery' => 'true'
                                    ]
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'id' => 'video-'.$arItem['ID'].$sKeyVideo,
                                        'class' => 'catalog-element-gallery-pictures-slider-item-video-wrapper'
                                    ]) ?>
                                        <?= Html::beginTag('video', [
                                            'class' => [
                                                'lg-video-object',
                                                'lg-html5'
                                            ],
                                            'data' => [
                                                'id' => 'video-'.$arItem['ID'].$sKeyVideo,
                                                'role' => 'gallery.uploaded.video',
                                                'src' => !empty($arVideo['FILE_MP4']) ? $arVideo['FILE_MP4']['SRC'].'#t=0.5' : (!empty($arVideo['FILE_WEBM']) ? $arVideo['FILE_WEBM']['SRC'].'#t=0.5' : (!empty($arVideo['FILE_OGV']) ? $arVideo['FILE_OGV']['SRC'].'#t=0.5' : null))
                                            ],
                                            'loop' => true,
                                            'controls' => $arVisual['GALLERY']['VIDEO']['CONTROLS'],
                                            'muted' => true
                                        ]) ?>
                                            <?php if (!empty($arVideo['FILE_MP4'])) { ?>
                                                <source src="<?= $arVideo['FILE_MP4']['SRC'] ?>" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'>
                                            <?php } ?>
                                            <?php if (!empty($arVideo['FILE_WEBM'])) { ?>
                                                <source src="<?= $arVideo['FILE_WEBM']['SRC'] ?>" type='video/webm; codecs="vp8, vorbis"'>
                                            <?php } ?>
                                            <?php if (!empty($arVideo['FILE_OGV'])) { ?>
                                                <source src="<?= $arVideo['FILE_OGV']['SRC'] ?>" type='video/ogg; codecs="theora, vorbis"'>
                                            <?php } ?>
                                        <?= Html::endTag('video') ?>
                                        <?= $arSvg['PLAY'] ?>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="catalog-element-gallery-pictures-slider-item">
                        <div class="catalog-element-gallery-pictures-slider-item-picture intec-ui-picture">
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                                'alt' => $arItem['GALLERY']['PROPERTIES']['ALT'],
                                'title' => $arItem['GALLERY']['PROPERTIES']['TITLE'],
                                'loading' => 'lazy',
                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                            ]) ?>
                        </div>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['GALLERY']['PREVIEW'] && $iCountPictures > 1) { ?>
            <div class="catalog-element-gallery-preview" data-role="gallery.preview">
                <div class="catalog-element-gallery-preview-slider owl-carousel" data-role="gallery.preview.slider">
                    <?php $bPictureFirst = true; ?>
                    <?php foreach ($arItem['GALLERY']['VALUES'] as $arPicture) {
                        $sPicture = CFile::ResizeImageGet($arPicture, [
                            'width' => 100,
                            'height' => 100
                        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT)
                        ?>
                            <?= Html::beginTag('div', [
                                'class' => 'catalog-element-gallery-preview-slider-item',
                                'data' => [
                                    'role' => 'gallery.preview.slider.item',
                                    'active' => $bPictureFirst ? 'true' : 'false'
                                ]
                            ]) ?>
                                <div class="catalog-element-gallery-preview-slider-item-picture intec-ui-picture">
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture['src'], [
                                        'alt' => $arItem['GALLERY']['PROPERTIES']['ALT'],
                                        'title' => $arItem['GALLERY']['PROPERTIES']['TITLE'],
                                        'loading' => 'lazy',
                                        'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture['src'] : null
                                    ]) ?>
                                    <?php if ($arPicture['CONTENT_TYPE'] === 'image/gif') { ?>
                                        <?= $arSvg['GIF'] ?>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                            <?php $bPictureFirst = false; ?>
                     <?php } ?>

                    <?php foreach ($arVideos as $sKeyVideo => $arVideo) { ?>
                        <?= Html::beginTag('div', [
                            'class' => 'catalog-element-gallery-preview-slider-item',
                            'data' => [
                                'role' => 'gallery.preview.slider.item',
                                'active' => $bPictureFirst ? 'true' : 'false'
                            ]
                        ]) ?>
                            <?php if (!empty($arVideo['LINK'])) {
                                $arVideoInfo = youtube_video($arVideo['LINK']);
                                $sVideoPreview = !empty($arVideoInfo['sddefault']) ? $arVideoInfo['sddefault'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                            ?>
                                <div class="catalog-element-gallery-preview-slider-item-picture intec-ui-picture intec-cl-svg-path-stroke">
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sVideoPreview, [
                                        'alt' => Html::encode($arResult['NAME']),
                                        'title' => Html::encode($arResult['NAME']),
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sVideoPreview : null
                                        ]
                                    ]) ?>
                                    <?= $arSvg['PLAY'] ?>
                                </div>
                            <?php } else { ?>
                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-gallery-preview-slider-item-picture-stub',
                                        'intec-ui-picture',
                                        'intec-cl-svg-path-stroke'
                                    ],
                                    'data' => [
                                        'role' => 'video.stub',
                                        'id' => 'video-'.$arItem['ID'].$sKeyVideo
                                    ]
                                ]) ?>
                                    <img src="<?= SITE_TEMPLATE_PATH.'/images/picture.missing.png' ?>" data-role="canvas.stub" />
                                    <?= $arSvg['PLAY'] ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                            <?php $bPictureFirst = false; ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
                <div class="catalog-element-gallery-preview-navigation" data-role="gallery.preview.navigation"></div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<div class="catalog-element-gallery-container catalog-element-main-block">
    <?php $vGallery($arResult);

    if ($bSkuDynamic) {
        foreach ($arResult['OFFERS'] as &$arOffer)
            $vGallery($arOffer, true);

        unset($arOffer);
    } ?>
</div>
<?php unset($vGallery, $iVideoCounter, $iVideoPreviewCounter) ?>