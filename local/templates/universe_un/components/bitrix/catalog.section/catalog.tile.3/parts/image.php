<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die;

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vImage = function (&$arItem) use (&$arResult, &$arVisual) { ?>
    <?php $arParent = [
        'NAME' => $arItem['NAME'],
        'URL' => $arItem['DETAIL_PAGE_URL']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParent) {
        $arPictures = $arItem['PICTURES']['VALUES'];
        $bSlider = false;

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

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-item-image'
            ],
            'data' => [
                'role' => 'item.gallery',
                'offer' => $bOffer ? $arItem['ID'] : 'false',
            ]
        ]) ?>
            <?php if ($bSlider) { ?>
                <div class="catalog-section-item-image-wrapper catalog-section-item-image-slider owl-carousel">
                    <?php foreach ($arPictures as $sPicture) { ?>
                        <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                            'class' => [
                                'catalog-section-item-image-element',
                                'intec-ui-picture',
                                'intec-image-effect'
                            ],
                            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                            'data' => [
                                'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                                'id' => $arItem['ID']
                            ]
                        ]) ?>
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                'alt' => !empty($arItem['PICTURES']['PROPERTIES']['ALT']) ? $arItem['PICTURES']['PROPERTIES']['ALT'] : $arParent['NAME'],
                                'title' => !empty($arItem['PICTURES']['PROPERTIES']['TITLE']) ? $arItem['PICTURES']['PROPERTIES']['TITLE'] : $arParent['NAME'],
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]
                            ]) ?>
                        <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
                    <?php } ?>
                </div>
            <?php } else { ?>
                <?php $sPicture = ArrayHelper::getFirstValue($arPictures) ?>
                <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
                    'class' => [
                        'catalog-section-item-image-wrapper',
                        'intec-ui-picture',
                        'intec-image-effect'
                    ],
                    'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arParent['URL'] : null,
                    'data' => [
                        'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
                        'id' => $arItem['ID']
                    ]
                ]) ?>
                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                        'alt' => !empty($arItem['PICTURES']['PROPERTIES']['ALT']) ? $arItem['PICTURES']['PROPERTIES']['ALT'] : $arParent['NAME'],
                        'title' => !empty($arItem['PICTURES']['PROPERTIES']['TITLE']) ? $arItem['PICTURES']['PROPERTIES']['TITLE'] : $arParent['NAME'],
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
    <?php

        $fRender($arItem);

        if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER']) {
            foreach ($arItem['OFFERS'] as &$arOffer)
                $fRender($arOffer, true);

            unset($arOffer);
        }

    ?>
<?php } ?>