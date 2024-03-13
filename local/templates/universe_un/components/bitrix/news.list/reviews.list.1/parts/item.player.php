<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arSvg
 */

?>
<?php return function (&$item) use (&$arResult, &$arVisual, &$arSvg) {

    $player = ArrayHelper::getFirstValue($item['DATA']['VIDEO']['VALUES']);

    if (!empty($player['PICTURE']))
        $sPicture = $player['PICTURE']['SRC'];
    else
        $sPicture = $player['SERVICE']['PICTURES'][$arVisual['VIDEO']['QUALITY']];
    
?>
    <div class="news-list-item-block" data-role="gallery">
        <?= Html::beginTag('div', [
            'class' => 'news-list-item-player',
            'data' => [
                'role' => 'gallery.item',
                'src' => $player['SERVICE']['LINKS']['embed'],
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
            ],
            'style' => [
                'background-image' => 'url(\''.(
                    $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture
                ).'\')'
            ]
        ]) ?>
            <div class="news-list-item-player-play intec-cl-background intec-ui-align">
                <div class="news-list-item-player-play-icon intec-ui-picture">
                    <?= $arSvg['GALLERY']['PLAY'] ?>
                </div>
            </div>
            <div class="news-list-item-player-container news-list-item-block">
                <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-6 intec-grid-550-wrap">
                    <?php if ($arVisual['PICTURE']['SHOW']) {

                        $sPicture = null;

                        if (!empty($item['DATA']['PICTURE'])) {
                            $sPicture = CFile::ResizeImageGet($item['DATA']['PICTURE'], [
                                'width' => 72,
                                'height' => 72
                            ], BX_RESIZE_IMAGE_EXACT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <div class="intec-grid-item-auto intec-grid-item-400-1">
                            <div class="news-list-item-player-portrait intec-ui-picture">
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
                        <?php if ($arVisual['DATE']['SHOW']) { ?>
                            <div class="news-list-item-player-date">
                                <?= $item['DATA']['DATE'] ?>
                            </div>
                        <?php } ?>
                        <div class="news-list-item-player-name">
                            <?= $item['NAME'] ?>
                        </div>
                        <?php if ($item['DATA']['INFORMATION']['SHOW']) { ?>
                            <div class="news-list-item-player-information">
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
                                    <?php if ($key === $item['DATA']['RATING']['VALUE'])
                                        $bRatingActive = false;
                                    ?>
                                <?php } ?>
                                <?php unset($bRatingActive, $key, $value) ?>
                            <?= Html::endTag('div') ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    </div>
<?php };