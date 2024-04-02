<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
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
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <div class="catalog-element-gallery-items">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'catalog-element-gallery-items-wrapper' => true,
                    'owl-carousel' => !empty($arItem['PICTURES']['VALUES'])
                ], true),
                'data' => [
                    'role' => !empty($arItem['PICTURES']['VALUES']) ? 'gallery.pictures' : 'gallery.empty'
                ]
            ]) ?>
                <?php if (!empty($arItem['PICTURES']['VALUES'])) { ?>
                    <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {

                        $arPictureResize = CFile::ResizeImageGet($arPicture, [
                            'width' => 500,
                            'height' => 500
                        ], BX_RESIZE_IMAGE_PROPORTIONAL);

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'catalog-element-gallery-item',
                                'intec-ui-picture'
                            ],
                            'data' => [
                                'role' => 'gallery.picture'
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
                    <?php } ?>
                <?php } else { ?>
                    <div class="catalog-element-gallery-item intec-ui-picture">
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
        </div>
        <?php if ($arVisual['GALLERY']['PREVIEW'] && count($arItem['PICTURES']['VALUES']) > 1) { ?>
            <div class="catalog-element-gallery-previews owl-carousel" data-role="gallery.previews">
                <?php foreach ($arItem['PICTURES']['VALUES'] as $arPicture) {

                    $arPictureResize = CFile::ResizeImageGet($arPicture, [
                        'width' => 100,
                        'height' => 100
                    ], BX_RESIZE_IMAGE_PROPORTIONAL);

                ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-gallery-preview',
                            'intec-ui-picture'
                        ],
                        'data' => [
                            'role' => 'gallery.preview',
                            'active' => 'false'
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
                <?php } ?>
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