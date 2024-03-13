<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arGallery
 * @var array $arLazyLoad
 */

?>

<div class="news-detail-content-header-preview-wrapper intec-grid-item-auto" data-role="gallery.preview">
    <div class="news-detail-content-header-preview">
        <ul class="news-detail-content-header-preview-list owl-carousel" data-role="preview.list">
            <?php $sPictureFirst = true; ?>
            <?php foreach ($arGallery as $arGalleryItem) { ?>
                <?php
                $sPicture = CFile::ResizeImageGet($arGalleryItem, [
                    'width' => 64,
                    'height' => 64
                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
                ?>
                <?= Html::tag('li', '', [
                    'class' => [
                        'news-detail-content-header-preview-list-item'
                    ],
                    'data' => [
                        'lazyload-use' => $arLazyLoad['USE'] ? 'true' : 'false',
                        'original' => $arLazyLoad['USE'] ? $sPicture['src'] : null,
                        'active' => $sPictureFirst ? 'true' : 'false',
                        'role' => 'preview.item'
                    ],
                    'style' => [
                        'background-image' => !$arLazyLoad['USE'] ? 'url(\''.$sPicture['src'].'\')' : null
                    ],
                    'title' => $arGalleryItem['NAME'],
                    'alt' => $arGalleryItem['NAME']
                ]) ?>
                <?php $sPictureFirst = false; ?>
            <?php } ?>
        </ul>
    </div>
</div>
