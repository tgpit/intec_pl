<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<div class="catalog-element-gallery" data-role="product.gallery.content">
    <?php if (!empty($arResult['GALLERY']['VALUES'])) { ?>
        <?php if (count($arResult['GALLERY']['VALUES']) > 1) { ?>
            <div class="catalog-element-gallery-content owl-carousel" data-role="product.gallery.slider" data-gallery>
                <?php foreach ($arResult['GALLERY']['VALUES'] as $arPicture) {

                    $sPicture = CFile::ResizeImageGet($arPicture, [
                        'width' => 800,
                        'height' => 800
                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                    if (empty($sPicture))
                        continue;
                    else
                        $sPicture = $sPicture['src'];

                ?>
                    <?= Html::beginTag('div', [
                        'class' => [
                            'catalog-element-gallery-item',
                            'intec-ui-align'
                        ],
                        'data' => [
                            'role' => 'product.gallery.item',
                            'src' => $arPicture['SRC']
                        ]
                    ]) ?>
                        <div class="catalog-element-gallery-item-content intec-ui-picture">
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                'alt' => $arResult['GALLERY']['PROPERTIES']['ALT'],
                                'title' => $arResult['GALLERY']['PROPERTIES']['TITLE'],
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]
                            ]) ?>
                        </div>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php unset($arPicture, $sPicture) ?>
            </div>
            <div class="catalog-element-gallery-dots" data-role="product.gallery.dots"></div>
        <?php } else {

            $arPicture = ArrayHelper::getFirstValue($arResult['GALLERY']['VALUES']);

            $sPicture = CFile::ResizeImageGet($arPicture, [
                'width' => 800,
                'height' => 800
            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
            else
                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

        ?>
            <div class="catalog-element-gallery-content" data-gallery>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-element-gallery-item',
                        'intec-ui-align'
                    ],
                    'data' => [
                        'role' => 'product.gallery.item',
                        'src' => $arPicture['SRC']
                    ]
                ]) ?>
                    <div class="catalog-element-gallery-item-content intec-ui-picture">
                        <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                            'alt' => $arResult['GALLERY']['PROPERTIES']['ALT'],
                            'title' => $arResult['GALLERY']['PROPERTIES']['TITLE'],
                            'loading' => 'lazy',
                            'data' => [
                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                            ]
                        ]) ?>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
            <?php unset($arPicture, $sPicture) ?>
        <?php } ?>
    <?php } else {

        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    ?>
        <div class="catalog-element-gallery-content" data-gallery>
            <?= Html::beginTag('div', [
                'class' => [
                    'catalog-element-gallery-item',
                    'intec-ui-align'
                ],
                'data' => [
                    'role' => 'product.gallery.item',
                    'src' => $sPicture
                ]
            ]) ?>
                <div class="catalog-element-gallery-item-content intec-ui-picture">
                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                        'alt' => $arResult['GALLERY']['PROPERTIES']['ALT'],
                        'title' => $arResult['GALLERY']['PROPERTIES']['TITLE'],
                        'loading' => 'lazy',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ]
                    ]) ?>
                </div>
            <?= Html::endTag('div') ?>
        </div>
        <?php unset($sPicture) ?>
    <?php } ?>
    <?php if ($arVisual['MARKS']['SHOW'])
        include(__DIR__.'/marks.php');
    ?>
    <?php if ($arVisual['QUICK_VIEW']['USE'])
        include(__DIR__.'/buttons/quick.view.php');
    ?>
</div>