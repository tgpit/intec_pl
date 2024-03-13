<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var array $arParams
 */

?>
<?php $vImage = function (&$arItem) use (&$arResult, &$arVisual, &$arParams) { ?>
    <?php $fRender = function (&$arItem) use (&$arResult, &$arVisual, &$arParams) {
        foreach ($arItem['OFFERS'] as $offer) {
            $sLink = $arItem['DETAIL_PAGE_URL'];
            if (!empty($arItem['OFFERS'])){
                $sLink = $sLink.'?'.$arParams['SKU_DETAIL_ID'].'='.$offer['ID'];
                break;
            }
        }
        $sPicture = $arItem['PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 450,
                'height' => 450
            ], BX_RESIZE_IMAGE_PROPORTIONAL);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    ?>
        <?= Html::beginTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a', [
            'class' => [
                'catalog-section-item-image-look',
                'intec-ui-picture',
                'intec-image-effect'
            ],
            'href' => !$arResult['QUICK_VIEW']['DETAIL'] ? $sLink : null,
            'data-role' => $arResult['QUICK_VIEW']['DETAIL'] ? 'quick.view' : null
        ]) ?>
            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                'alt' => $arItem['NAME'],
                'title' => $arItem['NAME'],
                'loading' => 'lazy',
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ]
            ]) ?>
        <?= Html::endTag($arResult['QUICK_VIEW']['DETAIL'] ? 'div' : 'a') ?>
    <?php } ?>
    <?php $fRender($arItem) ?>
<?php } ?>