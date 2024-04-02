<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arGallery
 * @var array $arLazyLoad
 */
?>

<div class="news-detail-content-header-gallery intec-grid-item-auto owl-carousel" data-role="gallery">
    <?php foreach ($arGallery as $arGalleryItem) { ?>
        <?php
        $sPicture = CFile::ResizeImageGet($arGalleryItem, [
            'width' => 590,
            'height' => 352
        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
        ?>
        <?= Html::beginTag('div', [
            'class' => [
                'news-detail-content-header-gallery-image',
                'intec-ui-picture'
            ],
            'data' => [
                'src' => $arGalleryItem['SRC'],
                'role' => 'gallery.pictures',
                'lightgallery' => 'true',
                'exthumbimage' =>  $arGalleryItem['SRC']
            ]
        ]) ?>
            <?= Html::img(null, [
                'class' => 'owl-lazy',
                'loading' => 'lazy',
                'alt' => $arResult['NAME'],
                'title' => $arResult['NAME'],
                'data' => [
                    'src' => $arGalleryItem['SRC']
                ]
            ]) ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
