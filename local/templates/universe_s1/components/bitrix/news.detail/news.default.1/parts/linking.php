<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arVisual
 * @var array $arData
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

?>
<?= Html::beginTag('div', [
    'class' => 'news-detail-navigation',
    'data-picture-show' => $arVisual['LINKING']['PICTURE']['SHOW'] ? 'true' : 'false'
]) ?>
    <div class="news-detail-navigation-wrapper intec-grid intec-grid-wrap intec-grid-a-v-stretch">
        <?php $iCounter = 0 ?>
        <?php foreach ($arResult['LINKING'] as $arNavItem) {
            $iCounter++;

            $sPicture = $arNavItem['PREVIEW_PICTURE'];

            if (empty($sPicture))
                $sPicture = $arNavItem['DETAIL_PICTURE'];

            if (!empty($sPicture)) {
                $sPicture = CFile::ResizeImageGet($sPicture, [
                    'width' => 200,
                    'height' => 200
                ], BX_RESIZE_IMAGE_EXACT,
                true);

                if (!empty($sPicture))
                    $sPicture = $sPicture['src'];
            }

            if (empty($sPicture))
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            $sTag = 'div';

            if (!empty($arNavItem['DETAIL_PAGE_URL']))
                $sTag = 'a';
            ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'news-detail-navigation-item-wrap' => true,
                    'intec-grid-item' => [
                        '' => true,
                        '2' => true,
                        '950-1' => true
                    ]
                ], true)
            ]) ?>
                <?= Html::beginTag($sTag, [
                    'class' => 'news-detail-navigation-item',
                    'data-position' => $iCounter % 2 == 0 ? 'right' : 'left',
                    'href' => $sTag === 'a' ? $arNavItem['DETAIL_PAGE_URL'] : null,
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'news-detail-navigation-item-wrapper' => true,
                            'intec-grid' => [
                                '' => true,
                                'a-v-center' => true,
                                'o-horizontal-reverse' => $iCounter % 2 == 0
                            ]
                        ], true)
                    ]) ?>
                        <?php if ($arVisual['LINKING']['PICTURE']['SHOW']) {?>
                            <div class="intec-grid-item-auto">
                                <?= Html::tag('div', '', [
                                    'class' => 'news-detail-navigation-item-picture',
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null,
                                        'background-size' => $arVisual['BACKGROUND']['SIZE']
                                    ],
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                        <div class="intec-grid-item">
                            <?= Html::tag('div', $arNavItem['SUBTITLE'], [
                                'class' => 'news-detail-navigation-item-subtitle'
                            ]) ?>
                            <?= Html::tag('div', $arNavItem['NAME'], [
                                'class' => 'news-detail-navigation-item-title'
                            ]) ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag($sTag) ?>
            <?= Html::endTag('div') ?>
        <?php } ?>
    </div>
<?= Html::endTag('div') ?>