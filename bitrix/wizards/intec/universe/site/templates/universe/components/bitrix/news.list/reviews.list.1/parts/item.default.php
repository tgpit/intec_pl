<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php return function (&$item) use (&$arResult, &$arVisual, &$arSvg) { ?>
    <?= Html::beginTag('div', [
        'class' => 'news-list-item-container',
        'data-answer' => $item['DATA']['ANSWER']['SHOW'] ? 'true' : 'false'
    ]) ?>
        <div class="news-list-item-block">
            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-12 intec-grid-i-v-8 intec-grid-400-wrap">
                <?php if ($arVisual['PICTURE']['SHOW']) {

                    $sPicture = null;

                    if (!empty($item['DATA']['PICTURE'])) {
                        $sPicture = CFile::ResizeImageGet($item['DATA']['PICTURE'], [
                            'width' => 120,
                            'height' => 120
                        ], BX_RESIZE_IMAGE_EXACT);

                        if (!empty($sPicture))
                            $sPicture = $sPicture['src'];
                    }

                    if (empty($sPicture))
                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
    
                ?>
                    <div class="intec-grid-item-auto intec-grid-item-400-1">
                        <div class="news-list-item-portrait intec-ui-picture">
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                'class' => 'intec-image-effect',
                                'alt' => $item['DATA']['PICTURE']['ALT'],
                                'title' => $item['DATA']['PICTURE']['TITLE'],
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]
                            ]) ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="intec-grid-item intec-grid-item-400-1">
                    <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-12 intec-grid-i-v-4 intec-grid-550-wrap">
                        <div class="intec-grid-item intec-grid-item-550-1">
                            <?php if ($arVisual['DATE']['SHOW']) { ?>
                                <div class="news-list-item-date">
                                    <?= $item['DATA']['DATE'] ?>
                                </div>
                            <?php } ?>
                            <div class="news-list-item-name">
                                <?= $item['NAME'] ?>
                            </div>
                            <?php if ($item['DATA']['INFORMATION']['SHOW']) { ?>
                                <div class="news-list-item-information">
                                    <?= $item['DATA']['INFORMATION']['VALUE'] ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($item['DATA']['RATING']['SHOW']) { ?>
                            <div class="intec-grid-item-auto intec-grid-item-550-1">
                                <?= Html::beginTag('div', [
                                    'class' => 'news-list-item-rating',
                                    'title' => $arResult['RATING_VALUES'][$item['DATA']['RATING']['VALUE']],
                                    'data-role' => 'rating'
                                ]) ?>
                                    <?php $bRatingActive = true ?>
                                    <?php foreach ($arResult['RATING_VALUES'] as $key => $value) { ?>
                                        <?= Html::tag('div', $arSvg['RATING'], [
                                            'class' => [
                                                'news-list-item-rating-item',
                                                'intec-ui-picture'
                                            ],
                                            'data' => [
                                                'role' => 'rating.item',
                                                'active' => $bRatingActive ? 'true' : 'false'
                                            ]
                                        ]) ?>
                                        <?php if ($key == $item['DATA']['RATING']['VALUE'])
                                            $bRatingActive = false;
                                        ?>
                                    <?php } ?>
                                    <?php unset($bRatingActive, $key, $value) ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="news-list-item-text">
            <?= $item['DATA']['TEXT'] ?>
        </div>
        <?php if ($item['DATA']['VIDEO']['SHOW'] || $item['DATA']['PICTURES']['SHOW']) { ?>
            <div class="news-list-item-gallery news-list-item-block" data-role="gallery">
                <div class="intec-grid intec-grid-wrap intec-grid-i-4">
                    <?php if ($item['DATA']['VIDEO']['SHOW']) { ?>
                        <?php foreach ($item['DATA']['VIDEO']['VALUES'] as $video) {
    
                            $sPicture = null;
    
                            if (!empty($video['PICTURE'])) {
                                $sPicture = $video['PICTURE'];
    
                                $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 120,
                                    'height' => 120
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
    
                                $sPicture = $sPicture['src'];
                            }
    
                            if (empty($sPicture))
                                $sPicture = $video['SERVICE']['PICTURES'][$arVisual['VIDEO']['QUALITY']];
    
                        ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('div', [
                                    'class' => 'news-list-item-gallery-item',
                                    'data' => [
                                        'role' => 'gallery.item',
                                        'src' => $video['SERVICE']['LINKS']['embed'],
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => 'url(\''.(
                                            $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                        ).'\')'
                                    ]
                                ]) ?>
                                    <div class="news-list-item-gallery-fade"></div>
                                    <div class="news-list-item-gallery-play intec-cl-background intec-ui-align">
                                        <div class="news-list-item-gallery-play-icon intec-ui-picture">
                                            <?= $arSvg['GALLERY']['PLAY'] ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                        <?php unset($video, $sPicture) ?>
                    <?php } ?>
                    <?php if ($item['DATA']['PICTURES']['SHOW']) { ?>
                        <?php foreach ($item['DATA']['PICTURES']['VALUES'] as $picture) {
    
                            $sPicture = CFile::ResizeImageGet($picture, [
                                'width' => 120,
                                'height' => 120
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);
    
                            $sPicture = $sPicture['src'];
    
                        ?>
                            <div class="intec-grid-item-auto">
                                <?= Html::beginTag('div', [
                                    'class' => 'news-list-item-gallery-item',
                                    'data' => [
                                        'role' => 'gallery.item',
                                        'thumb' => $sPicture,
                                        'src' => $picture['SRC'],
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => 'url(\''.(
                                            $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                                        ).'\')'
                                    ]
                                ]) ?>
                                    <div class="news-list-item-gallery-fade"></div>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>
                        <?php unset($picture, $sPicture) ?>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
        <?php if ($item['DATA']['FILES']['SHOW']) { ?>
            <div class="news-list-item-files news-list-item-block">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-h-20 intec-grid-i-v-8">
                    <?php foreach ($item['DATA']['FILES']['VALUES'] as $file) {
    
                        if ($file['CONTENT_TYPE'] === 'application/pdf')
                            $fileIcon = $arSvg['FILES']['PDF'];
                        else if ($file['CONTENT_TYPE'] === 'application/msword')
                            $fileIcon = $arSvg['FILES']['DOC'];
                        else
                            $fileIcon = $arSvg['FILES']['COMMON'];
    
                    ?>
                        <div class="intec-grid-item-3 intec-grid-item-1024-2 intec-grid-item-500-1">
                            <div class="news-list-item-files-item">
                                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8">
                                    <div class="intec-grid-item-auto">
                                        <div class="news-list-item-files-icon">
                                            <?= Html::tag('a', $fileIcon, [
                                                'class' => 'intec-ui-picture',
                                                'href' => $file['SRC'],
                                                'target' => '_blank'
                                            ]) ?>
                                        </div>
                                    </div>
                                    <div class="intec-grid-item">
                                        <div class="news-list-item-files-name">
                                            <?= Html::tag('a', $file['ORIGINAL_NAME'], [
                                                'class' => 'intec-cl-text-hover',
                                                'href' => $file['SRC'],
                                                'target' => '_blank'
                                            ]) ?>
                                        </div>
                                        <div class="news-list-item-files-size">
                                            <?= CFile::FormatSize($file['FILE_SIZE']) ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php unset($file, $fileIcon) ?>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php };