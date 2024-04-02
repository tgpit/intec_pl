<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
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
    } else {
        $arVideos = $arResult['GALLERY_VIDEO']['PRODUCT'];
    }
    $arPictures = $arItem['PICTURES']['VALUES'];

    $arFormats = ['MP4','WEBM','OGV'];

    $bVideoGalleryUse = false;
    if ($arVisual['GALLERY']['VIDEO']['USE'] && !empty($arVideos)) {
        $bVideoGalleryUse = true;
    }

    $sStub = $arVisual['LAZYLOAD']['STUB'];

    if ($bOffer) {
        if (empty($arPictures)) {
            if (!$bVideoGalleryUse) {
                return;
            }
        }
    }
    ?>

    <?= Html::beginTag('div', [
        'class' => 'catalog-element-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <div class="intec-grid intec-grid-a-v-center">
            <?php if ($arVisual['GALLERY']['PREVIEW']) { ?>
                <div class="catalog-element-gallery-preview intec-grid-item-auto">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-gallery-preview-wrapper'
                        ],
                        'data' => [
                            'role' => 'gallery.preview'
                        ]
                    ]) ?>
                        <?php
                        $iPicturesCount = count($arPictures);

                        if (empty($arPictures) && $bVideoGalleryUse) {
                            $iPicturesCount = 1;
                        }

                        $iSumCount = $iPicturesCount + count(($arVideos)? : []);

                        if ((!empty($arPictures) && $iPicturesCount > 1) || $bVideoGalleryUse)  {
                            $iCount = 0;
                            if (!empty($arPictures)) {
                                foreach ($arPictures as $arPicture) {
                                    $iCount++;
                                    $arPictureResize = CFile::ResizeImageGet($arPicture, [
                                        'width' => 100,
                                        'height' => 100
                                    ], BX_RESIZE_IMAGE_EXACT);

                                    if ($iCount != 6) { ?>
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'catalog-element-gallery-preview-item',
                                                'intec-ui-picture'
                                            ],
                                            'data' => [
                                                'role' => 'gallery.preview.item',
                                                'active' => 'false'
                                            ]
                                        ]) ?>
                                            <?php if ($arPicture['CONTENT_TYPE'] === 'image/gif') {?>
                                                <?= $arSvg['GIF'];?>
                                            <?php } ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $arPictureResize['src'], [
                                                'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                                'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                                'loading' => 'lazy',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arPictureResize['src'] : null
                                                ]
                                            ]) ?>
                                        <?= Html::endTag('div') ?>
                                    <?php } else {
                                        if ($arVisual['GALLERY']['POPUP']) {?>
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'catalog-element-gallery-preview-popup',
                                                    'intec-cl-text-hover'
                                                ],
                                                'data' => [
                                                    'role' => 'gallery.preview.popup'
                                                ]
                                            ]);?>
                                                <span>
                                                    <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PREVIEW_MORE') ?>
                                                </span>
                                                <span>
                                                    <?= $iSumCount - 5 ?>
                                                </span>
                                            <?= Html::endTag('div') ?>
                                        <?php }
                                        break;
                                    }
                                }
                            } else {
                                $iCount++; ?>

                                <?= Html::beginTag('div', [
                                    'class' => [
                                        'catalog-element-gallery-preview-item',
                                        'intec-ui-picture'
                                    ],
                                    'data' => [
                                        'role' => 'gallery.preview.item',
                                        'active' => 'false'
                                    ]
                                ]) ?>
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                                        'alt' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_NO_IMAGE_TITLE'),
                                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_NO_IMAGE_TITLE'),
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                                        ]
                                    ]) ?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>

                            <?php if ($bVideoGalleryUse && $iCount != 6) {
                                $iCount++;

                                foreach ($arVideos as $sKeyVideo => $arVideo) {
                                    if ($iCount >= 6) {
                                        if ($arVisual['GALLERY']['POPUP']) { ?>
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'catalog-element-gallery-preview-popup',
                                                    'intec-cl-text-hover'
                                                ],
                                                'data' => [
                                                    'role' => 'gallery.preview.popup'
                                                ]
                                            ]) ?>
                                            <span>
                                                <?= Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_PREVIEW_MORE') ?>
                                            </span>
                                            <span>
                                                <?= $iSumCount - 5 ?>
                                            </span>
                                            <?= Html::endTag('div') ?>
                                        <?php }
                                        break;
                                    }

                                    $arVideoInfo = null;

                                    if (!empty($arVideo['LINK']))
                                        $arVideoInfo = youtube_video($arVideo['LINK']); ?>

                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'catalog-element-gallery-preview-item' => true,
                                            'intec-ui-picture' => (!empty($arVideo['LINK'])),
                                            'intec-cl-svg-path-stroke' => (!empty($arVideo['LINK']))
                                        ], true),
                                        'data' => [
                                            'role' => 'gallery.preview.item',
                                            'active' => 'false'
                                        ]
                                    ]) ?>
                                        <?php if (!empty($arVideo['LINK'])) { ?>
                                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $arVideoInfo['image_maxresdefault'], [
                                                'alt' => Html::encode($arResult['NAME']),
                                                'title' => Html::encode($arResult['NAME']),
                                                'loading' => 'lazy',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arVideoInfo['image_maxresdefault'] : null
                                                ],
                                                'width' => 100,
                                                'height' => 100
                                            ]) ?>
                                            <?= $arSvg["PLAY"];?>
                                        <?php } else { ?>
                                            <?= Html::beginTag('div', [
                                                'class' => [
                                                    'catalog-element-gallery-preview-item-stub',
                                                    'intec-cl-svg-path-stroke'
                                                ],
                                                'data' => [
                                                    'role' => 'video.stub',
                                                    'id' => 'video-'.$arItem['ID'].$sKeyVideo
                                                ]
                                            ]) ?>
                                                <div data-role="canvas.stub"></div>
                                                <?= $arSvg["PLAY"];?>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            <?php } ?>
                            <?php unset($iSumCount, $iPicturesCount, $iSumCount, $iCount); ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
            <?php } ?>

            <div class="intec-grid-item">
                <div class="catalog-element-gallery-pictures">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-gallery-pictures-wrapper',
                            'owl-carousel'
                        ],
                        'data' => [
                            'role' => (!empty($arPictures) || $bVideoGalleryUse) ? 'gallery.pictures' : 'gallery.empty'
                        ]
                    ]) ?>
                        <?php if (!empty($arPictures)) {

                            $bPictureFirst = true;
                            foreach ($arPictures as $arPicture) {
                                if ($arPicture['CONTENT_TYPE'] !== 'image/gif') {
                                    $arPictureResize = CFile::ResizeImageGet($arPicture, [
                                        'width' => 500,
                                        'height' => 500
                                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                    $arPicture['RESIZE_SRC'] = $arPictureResize['src'];
                                }?>
                                <?= Html::beginTag('a', [
                                    'href' => $arPicture['SRC'],
                                    'class' => [
                                        'catalog-element-gallery-picture',
                                        'catalog-element-gallery-element',
                                        'intec-ui-picture'
                                    ],
                                    'data' => [
                                        'active' => $bPictureFirst ? 'true' : 'false',
                                        'role' => 'gallery.picture',
                                        'src' => $arPicture['SRC'],
                                        'type' => $arPicture['CONTENT_TYPE'],
                                        'lightGallery' => 'true'
                                    ]
                                ]) ?>
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $arPicture['RESIZE_SRC'], [
                                        'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                        'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['RESIZE_SRC'] : null
                                        ]
                                    ]) ?>
                                <?= Html::endTag('a') ?>
                                <?php $bPictureFirst = false;
                            }
                        } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'catalog-element-gallery-element',
                                    'catalog-element-gallery-picture'
                                ],
                                'data' => [
                                    'role' => $bVideoGalleryUse ? 'gallery.picture' : null,
                                    'active' => 'true',
                                    'lightGallery' => 'true',
                                    'src' => SITE_TEMPLATE_PATH.'/images/picture.missing.png'
                                ]
                            ]); ?>
                                <div class="catalog-element-gallery-picture-wrapper intec-ui-picture">
                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                                        'alt' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_NO_IMAGE_TITLE'),
                                        'title' => Loc::getMessage('C_CATALOG_ELEMENT_CATALOG_DEFAULT_2_NO_IMAGE_TITLE'),
                                        'loading' => 'lazy',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                                        ]
                                    ]); ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>

                        <?php if ($bVideoGalleryUse) {
                            foreach ($arVideos as $sKeyVideo => $arVideo) {
                                if  (!empty($arVideo['LINK'])) {
                                    $arVideoInfo = youtube_video($arVideo['LINK']);
                                    $sVideoPreview = !empty($arVideoInfo['sddefault']) ? $arVideoInfo['sddefault'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png';?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-gallery-video',
                                            'catalog-element-gallery-element'
                                        ],
                                        'data' => [
                                            'src' => $arVideoInfo['iframe'],
                                            'role' => 'gallery.video',
                                            'lightGallery' => 'true'
                                        ]
                                    ]); ?>
                                        <?= Html::tag('div', $arSvg['PLAY'], [
                                            'class' => [
                                                'catalog-element-gallery-video-stub',
                                                'intec-image-effect'
                                            ],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sVideoPreview : null
                                            ],
                                            'style' => [
                                                'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sVideoPreview
                                            ]
                                        ]);?>
                                    <?= Html::endTag('div') ?>
                                <?php } else { ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-gallery-video',
                                            'catalog-element-gallery-element',
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
                                                    'src' =>
                                                        !empty($arVideo['FILES']['FILE_MP4']) ?
                                                            $arVideo['FILES']['FILE_MP4']['SRC'].'#t=0.5' :
                                                                (!empty($arVideo['FILES']['FILE_WEBM']) ?
                                                                    $arVideo['FILES']['FILE_WEBM']['SRC'].'#t=0.5' :
                                                                    (!empty($arVideo['FILES']['FILE_OGV']) ?
                                                                        $arVideo['FILES']['FILE_OGV']['SRC'].'#t=0.5'
                                                                        : null))
                                                ],
                                                'loop' => true,
                                                'controls' => $arVisual['GALLERY']['VIDEO']['CONTROLS'],
                                                'muted' => true
                                            ]) ?>
                                                <?php
                                                foreach ($arFormats as $sFormat) {
                                                    echo Html::tag('source', '', [
                                                        'src' => !empty($arVideo['FILES']['FILE_'.$sFormat]) ? $arVideo['FILES']['FILE_'.$sFormat]['SRC'] . '#t=0.5' : null,
                                                        'type' => !empty($arVideo['FILES']['FILE_'.$sFormat]) ? $arVideo['FILES']['FILE_'.$sFormat]['TYPE'] : null,
                                                    ]);
                                                }?>
                                            <?= Html::endTag('video') ?>
                                            <?= $arSvg['PLAY'] ?>
                                        <?= Html::endTag('div') ?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            <?php } ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                </div>
                <?php if (((Type::isArray($arItem['PICTURES']['VALUES'])
                            && count($arItem['PICTURES']['VALUES']) > 1) || $bVideoGalleryUse)
                            && $arVisual['GALLERY']['PANEL']) { ?>
                    <div class="catalog-element-gallery-panel" data-role="gallery.panel" data-print="false">
                        <?php if ($arVisual['GALLERY']['POPUP']) { ?>
                            <div class="catalog-element-gallery-panel-item" data-role="gallery.popup">
                                <i class="far fa-th-large"></i>
                            </div>
                        <?php } ?>
                        <div class="catalog-element-gallery-panel-item" data-role="gallery.previous">
                            <i class="far fa-chevron-left"></i>
                        </div>
                        <div class="catalog-element-gallery-panel-item" data-role="gallery.current"></div>
                        <div class="catalog-element-gallery-panel-item" data-role="gallery.next">
                            <i class="far fa-chevron-right"></i>
                        </div>
                        <?php if ($arVisual['GALLERY']['POPUP']) { ?>
                            <div class="catalog-element-gallery-panel-item" data-role="gallery.play">
                                <i class="far fa-play"></i>
                            </div>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?= Html::endTag('div') ?>
    <?php unset($bVideoGalleryUse); ?>
    <?php unset($arFormats); ?>
<?php } ?>
<?php $vGallery($arResult);

if (!empty($arResult['OFFERS']))
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $vGallery($arOffer, true);

        unset($arOffer);
    }

unset($vGallery);