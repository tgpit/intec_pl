<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

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
        $sPicture = $arItem['DATA']['PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 450,
                'height' => 450
            ], BX_RESIZE_IMAGE_PROPORTIONAL);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture) && $bOffer)
            return;

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-item-image-look',
                'intec-ui-picture',
                'intec-image-effect'
            ],
            'data' => [
                'offer' => $bOffer ? $arItem['ID'] : 'false',
                'role' => 'item.gallery'
            ]
        ]) ?>
            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                'alt' => !empty($arItem['PICTURE']['ALT']) ? $arItem['PICTURE']['ALT'] : $arParent['NAME'],
                'title' => !empty($arItem['PICTURE']['TITLE']) ? $arItem['PICTURE']['TITLE'] : $arParent['NAME'],
                'loading' => 'lazy',
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ]
            ]) ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
        'class' => 'catalog-section-item-image-items',
        'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $arItem['DETAIL_PAGE_URL'] : null,
        'data' => [
            'role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : 'offer.link',
            'id' => $arItem['ID']
        ]
    ]) ?>
        <?php

            $fRender($arItem);

            if ($arVisual['OFFERS']['USE'] && $arItem['DATA']['OFFER'])
                foreach ($arItem['OFFERS'] as &$arOffer)
                    $fRender($arOffer, true);

        ?>
    <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
<?php } ?>