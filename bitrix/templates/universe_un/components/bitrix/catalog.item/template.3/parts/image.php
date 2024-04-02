<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\Type;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php $vImage = function (&$arItem) use (&$arResult, &$arVisual, &$arParams) { ?>
    <?php $arParentValues = [
        'NAME' => $arItem['NAME'],
        'URL' => $arItem['DETAIL_PAGE_URL']
    ] ?>
    <?php $fRender = function (&$arItem, $bOffer = false) use (&$arResult, &$arVisual, &$arParentValues, &$arParams) {
        $bSlider = false;
        $arPictures = $arItem['PICTURES']['VALUES'];

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

        $sPercent = ArrayHelper::getFirstValue($arItem['ITEM_PRICES']);

        if (!empty($sPercent['PERCENT']))
            $sPercent = $sPercent['PERCENT'];

        ?>
        <div class="catalog-item-picture-container">
            <?= Html::beginTag('div', [
                'class' => 'catalog-item-picture',
                'data' => [
                    'role' => 'gallery',
                    'offer' => $bOffer ? $arItem['ID'] : 'false',
                    'view' => 'default'
                ],
                'style' => 'padding-top:' . $arVisual['IMAGE']['ASPECT_RATIO'] . '%'
            ]) ?>
            <?php if ($bSlider) { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'catalog-item-picture-wrapper',
                        'catalog-item-picture-slider',
                        'owl-carousel'
                    ]
                ]) ?>
                    <?php foreach ($arPictures as $sPicture) { ?>
                        <?= Html::beginTag('a', [
                            'class' => [
                                'catalog-item-picture-element',
                                'intec-image-effect'
                            ],
                            'href' => $arParentValues['URL']
                        ]) ?>
                            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                                'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                                'loading' => 'lazy',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                ]
                            ]) ?>
                        <?= Html::endTag('a') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            <?php } else { ?>
                <?php $sPicture = ArrayHelper::getFirstValue($arPictures) ?>
                <?= Html::beginTag('a', [
                    'class' => [
                        'catalog-item-picture-wrapper',
                        'intec-image-effect'
                    ],
                    'href' => $arParentValues['URL']
                ]) ?>
                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                        'alt' => Html::encode($arItem['PICTURES']['PROPERTIES']['ALT']),
                        'title' => Html::encode($arItem['PICTURES']['PROPERTIES']['TITLE']),
                        'loading' => 'lazy',
                        'data' => [
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ]
                    ]) ?>
                <?= Html::endTag('a') ?>
            <?php } ?>
            <?= Html::endTag('div') ?>

            <?php if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' || $arItem['LABEL']) { ?>
                <div class="catalog-item-sticker">
                    <?php if ($arParams['SHOW_DISCOUNT_PERCENT'] === 'Y' && !empty($sPercent)) { ?>
                        <div class="catalog-item-sticker-item catalog-item-sticker-percent">
                    <span>
                        <?= '-'.$sPercent.'%' ?>
                    </span>
                        </div>
                    <?php } ?>
                    <?php if ($arItem['LABEL']) { ?>
                        <div class="catalog-item-sticker-item catalog-item-sticker-label">
                            <?php if (!empty($arItem['LABEL_ARRAY_VALUE'])) { ?>
                                <?php foreach ($arItem['LABEL_ARRAY_VALUE'] as $value) { ?>
                                    <div class="catalog-item-sticker-label-item">
                                <span>
                                    <?= $value ?>
                                </span>
                                    </div>
                                <?php } ?>
                                <?php unset($value) ?>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>

        </div>
    <?php } ?>
    <?php $fRender($arItem) ?>
<?php } ?>