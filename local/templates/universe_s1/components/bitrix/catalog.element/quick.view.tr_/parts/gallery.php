<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\core\helpers\Type;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arVisual
 */

$sStub = Properties::get('template-images-lazyload-stub');

$vGallery = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$sStub) {

    if ($bOffer && empty($arItem['PICTURES']['VALUES']))
        return;

?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false',
            'preview' => $arVisual['GALLERY']['PREVIEW'] ? 'true' : 'false'
        ]
    ]) ?>
        <div class="catalog-element-gallery-pictures">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-element-gallery-pictures-wrapper' => true,
                    'owl-carousel' => !empty($arItem['PICTURES']['VALUES'])
                ], true),
                'data' => [
                    'role' => !empty($arItem['PICTURES']['VALUES']) ? 'gallery.pictures' : 'gallery.empty'
                ]
            ]) ?>
                <?php if (!empty($arItem['PICTURES']['VALUES'])) {

                    $bPictureFirst = true;

                ?>
                    <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {

                        $arPictureResize = CFile::ResizeImageGet($arPicture, [
                            'width' => 500,
                            'height' => 500
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                    ?>
                        <?= Html::beginTag('a', [
                            'class' => [
                                'catalog-element-gallery-picture',
                                'intec-ui-picture'
                            ],
                            'data' => [
                                'active' => $bPictureFirst ? 'true' : 'false',
                                'role' => 'gallery.picture',
                                'src' => $arPicture['SRC']
                            ]
                        ]) ?>
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $arPictureResize['src'], [
                                'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $arPictureResize['src'] : null
                                ]
                            ]) ?>
                        <?= Html::endTag('a') ?>
                        <?php $bPictureFirst = false ?>
                    <?php } ?>
                <?php } else { ?>
                    <div class="catalog-element-gallery-picture intec-ui-picture" data-active="true">
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                            'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                            'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                            'loading' => 'lazy',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                            ]
                        ]) ?>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
            <?php if ($arVisual['GALLERY']['PREVIEW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-gallery-previews',
                        'intec-grid',
                        'intec-grid-a-v-center'
                    ],
                    'data' => [
                        'role' => 'gallery.previews'
                    ]
                ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-gallery-previews-wrapper',
                            'catalog-element-scroll',
                            'intec-grid-item-1'
                        ],
                        'data' => [
                            'scroll' => 'true'
                        ]
                    ]) ?>
                        <?php if (count($arItem['PICTURES']['VALUES']) > 1) {

                            $index = 0

                        ?>
                            <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {

                                $arPictureResize = CFile::ResizeImageGet($arPicture, [
                                    'width' => 50,
                                    'height' => 50
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            ?>
                                <div class="catalog-element-gallery-preview" data-role="gallery.preview" data-active="false">
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'catalog-element-gallery-preview-image',
                                            'intec-ui-picture'
                                        ]
                                    ]) ?>
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
                                </div>
                                <?php $index++ ?>
                            <?php } ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
        <?php if ($arVisual['GALLERY']['PANEL'] && Type::isArray($arItem['PICTURES']['VALUES']) && count($arItem['PICTURES']['VALUES']) > 1) { ?>
            <div class="catalog-element-gallery-panel" data-role="gallery.panel">
                <div class="catalog-element-gallery-panel-item" data-role="gallery.previous">
                    <i class="far fa-chevron-left"></i>
                </div>
                <div class="catalog-element-gallery-panel-item" data-role="gallery.current"></div>
                <div class="catalog-element-gallery-panel-item" data-role="gallery.next">
                    <i class="far fa-chevron-right"></i>
                </div>
            </div>
        <?php } ?>
    <?= Html::endTag('div') ?>
<?php };

$vGallery($arResult);

if (!empty($arResult['OFFERS']))
    foreach ($arResult['OFFERS'] as &$arOffer) {
        $vGallery($arOffer, true);

        unset($arOffer);
    }

unset($vGallery, $sStub);