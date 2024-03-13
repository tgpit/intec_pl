<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>

<?php $vGallery = function (&$arItem, $bOffer = false) use (&$arVisual, &$arResult, &$arSvg) {
    if ($bOffer) {
        $arVideos = $arResult['GALLERY_VIDEO']['OFFERS'][$arItem['ID']];

        if (empty($arVideos) && empty($arItem['PICTURES']['VALUES']))
            return;
    } else {
        $arVideos = $arResult['GALLERY_VIDEO']['PRODUCT'];
    }

    $iCountPictures = 0;

    if (!empty($arItem['PICTURES']['VALUES']))
        $iCountPictures += count($arItem['PICTURES']['VALUES']);

    if (!empty($arVideos))
        $iCountPictures += count($arVideos);
?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'catalog-element-gallery-pictures' => true,
                'owl-carousel' => !empty($arItem['PICTURES']['VALUES'])
            ], true),
            'data' => [
                'role' => !empty($arItem['PICTURES']['VALUES']) ? 'gallery.pictures' : 'gallery.empty'
            ]
        ]) ?>
            <?php if (!empty($arItem['PICTURES']['VALUES']) || ($arVisual['GALLERY']['VIDEO']['USE'] && !empty($arVideos))) { ?>
                <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {
                    $bImageIsGif = $arPicture['CONTENT_TYPE'] == 'image/gif';
                    $arPictureResize['src'] = $arPicture['SRC'];

                    if (!$bImageIsGif) {
                        $arPictureResize = CFile::ResizeImageGet($arPicture, [
                            'width' => 500,
                            'height' => 500
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);
                    }
                ?>
                    <?= Html::beginTag('a', [
                        'href' => $arPicture['SRC'],
                        'class' => [
                            'catalog-element-gallery-picture'
                        ],
                        'data' => [
                            'role' => 'gallery.picture',
                            'src' => $arPicture['SRC'],
                            'type' => $arPicture['CONTENT_TYPE'],
                            'lightGallery' => 'true'
                        ]
                    ]) ?>
                        <div class="catalog-element-gallery-picture-wrapper intec-ui-picture">
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPictureResize['src'], [
                                'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arPictureResize['src'] : null
                                ]
                            ]) ?>
                        </div>
                    <?= Html::endTag('a') ?>
                <?php } ?>
                <?php foreach ($arVideos as $sKeyVideo => $arVideo) { ?>
                    <?php if (!empty($arVideo['LINK'])) {
                        $arVideoInfo = youtube_video($arVideo['LINK']);
                        $sVideoPreview = !empty($arVideoInfo['sddefault']) ? $arVideoInfo['sddefault'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-gallery-video'
                            ],
                            'data' => [
                                'src' => $arVideoInfo['iframe'],
                                'role' => 'gallery.video',
                                'lightGallery' => 'true',
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sVideoPreview : null
                            ],
                            'style' => [
                                'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sVideoPreview
                            ]
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-element-gallery-video-play',
                                    'intec-ui-picture'
                                ]
                            ]) ?>
                                <?= $arSvg['PLAY'] ?>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    <?php } else { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-gallery-video'
                            ],
                            'data' => [
                                'html' => '#video-'.$arItem['ID'].$sKeyVideo,
                                'role' => 'gallery.video',
                                'lightGallery' => 'true'
                            ]
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'id' => 'video-'.$arItem['ID'].$sKeyVideo,
                                'class' => 'catalog-element-gallery-video-wrapper'
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
                            <?= Html::endTag('div') ?>
                            <?= Html::tag('div', $arSvg['PLAY'], [
                                'class' => [
                                    'catalog-element-gallery-video-play',
                                    'intec-ui-picture'
                                ]
                            ]) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?php } ?>
            <?php } else { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-gallery-picture'
                    ],
                    'data' => [
                        'active' => 'true'
                    ]
                ]) ?>
                    <div class="catalog-element-gallery-picture-wrapper intec-ui-picture">
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                            'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                            'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                            'loading' => 'lazy',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                            ]
                        ]) ?>
                    </div>
                 <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['GALLERY']['SLIDER'] && $iCountPictures > 1) { ?>
            <div class="catalog-element-gallery-previews owl-carousel" data-role="gallery.previews">
                <?php $bPictureFirst = true; ?>
                <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {
                    $arPictureResize = CFile::ResizeImageGet($arPicture, [
                        'width' => 120,
                        'height' => 120
                    ], BX_RESIZE_IMAGE_PROPORTIONAL);
                ?>
                    <?= Html::beginTag('div', [
                        'class' => 'catalog-element-gallery-preview',
                        'data' => [
                            'active' => $bPictureFirst ? 'true' : 'false',
                            'role' => 'gallery.preview'
                        ]
                    ]) ?>
                        <div class="catalog-element-gallery-preview-wrapper">
                            <div class="catalog-element-gallery-preview-wrapper-2 intec-ui-picture">
                                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPictureResize['src'], [
                                    'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                    'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                    'loading' => 'lazy',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $arPictureResize['src'] : null
                                    ]
                                ]) ?>
                                <?php if ($arPicture['CONTENT_TYPE'] === 'image/gif') { ?>
                                    <?= $arSvg['GIF'] ?>
                                <?php } ?>
                            </div>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?php $bPictureFirst = false; ?>
                <?php } ?>
                <?php foreach ($arVideos as $sKeyVideo => $arVideo) { ?>
                     <?= Html::beginTag('div', [
                         'class' => 'catalog-element-gallery-preview',
                         'data' => [
                             'active' => $bPictureFirst ? 'true' : 'false',
                             'role' => 'gallery.preview'
                         ]
                     ]) ?>
                        <div class="catalog-element-gallery-preview-wrapper">
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'catalog-element-gallery-preview-wrapper-2' => true,
                                    'intec-ui-picture' => !empty($arVideo['LINK']) ? true : false,
                                    'intec-cl-svg-path-stroke' => !empty($arVideo['LINK']) ? true : false
                                ], true)
                            ]) ?>
                                <?php if (!empty($arVideo['LINK'])) {
                                    $arVideoInfo = youtube_video($arVideo['LINK']);
                                    $sVideoPreview = !empty($arVideoInfo['sddefault']) ? $arVideoInfo['sddefault'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                ?>
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sVideoPreview, [
                                        'alt' => Html::encode($arResult['NAME']),
                                        'title' => Html::encode($arResult['NAME']),
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sVideoPreview : null
                                        ],
                                        'width' => 120,
                                        'height' => 120
                                    ]) ?>
                                    <?= $arSvg['PLAY'] ?>
                                <?php } else { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-gallery-preview-stub',
                                            'intec-ui-picture'
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
                            <?= Html::endTag('div') ?>
                        </div>
                    <?= Html::endTag('div') ?>
                    <?php $bPictureFirst = false; ?>
                <?php } ?>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vGallery($arResult);

if (!empty($arResult['OFFERS'])) {
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $vGallery($arOffer, true);
    }

    unset($arOffer);
}

unset($vGallery) ?>