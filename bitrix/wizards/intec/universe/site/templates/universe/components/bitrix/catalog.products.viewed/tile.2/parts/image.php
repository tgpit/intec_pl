<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vImage = function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $arParentValues = [
        'NAME' => $arItem['NAME'],
        'URL' => $arItem['DETAIL_PAGE_URL']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParentValues) {
        $bSlider = false;
        $arPictures = $arItem['PICTURES'];

        if (!empty($arPictures) && Type::isArray($arPictures)) {
            foreach ($arPictures as $key => $arPicture) {
                $arPicture = CFile::ResizeImageGet(
                    $arPicture, [
                        'width' => 450,
                        'height' => 450
                    ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                );

                if (!empty($arPicture))
                    $arPictures[$key] = $arPicture['src'];
            }
        }

        if (empty($arPictures) && $bOffer)
            return;

        if (empty($arPictures))
            $arPictures[] = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

        if ($arVisual['IMAGE']['SLIDER'] && count($arPictures) > 1)
            $bSlider = true;

        $sPicture = null;

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-products-viewed-item-image'
            ],
            'data' => [
                'role' => 'gallery',
                'offer' => $bOffer ? $arItem['ID'] : 'false',
                'view' => $arVisual['OFFERS']['VIEW']
            ],
            'style' => 'padding-top:' . $arVisual['IMAGE']['ASPECT_RATIO'] . '%'
        ]) ?>
            <?php if ($bSlider) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-products-viewed-item-image-wrapper',
                        'catalog-products-viewed-item-image-slider',
                        'owl-carousel'
                    ]
                ]) ?>
                    <?php foreach ($arPictures as $sPicture) { ?>
                        <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                            'class' => [
                                'catalog-products-viewed-item-image-element',
                                'intec-ui-picture',
                                'intec-image-effect'
                            ],
                            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParentValues['URL'] : null,
                            'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                        ]) ?>
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                'alt' => $arParentValues['NAME'],
                                'title' => $arParentValues['NAME'],
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]
                            ]) ?>
                        <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?php $sPicture = ArrayHelper::getFirstValue($arPictures) ?>
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'class' => [
                        'catalog-products-viewed-item-image-wrapper',
                        'intec-ui-picture',
                        'intec-image-effect'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParentValues['URL'] : null,
                    'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
                ]) ?>
                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                        'alt' => $arParentValues['NAME'],
                        'title' => $arParentValues['NAME'],
                        'loading' => 'lazy',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ]
                    ]) ?>
                <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php $fRender($arItem) ?>
    <?php if ($arVisual['OFFERS']['USE'] && !empty($arItem['OFFERS']))
        foreach ($arItem['OFFERS'] as &$arOffer)
            $fRender($arOffer, true);
    ?>
<?php } ?>