<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 * @var bool $bOffers
 */

?>
<?php $vPanelGallery = function (&$arItem, $bOffer = false) use (&$arVisual) { ?>
    <?php if (empty($arItem['GALLERY']['VALUES']) && $bOffer) return ?>
    <?= Html::beginTag('div', [
        'class' => 'catalog-element-panel-gallery',
        'data' => [
            'role' => 'gallery',
            'offer' => $bOffer ? $arItem['ID'] : 'false'
        ]
    ]) ?>
        <div class="catalog-element-panel-gallery-picture intec-ui-picture intec-image-effect">
            <?php if (!empty($arItem['GALLERY']['VALUES'])) {

                $arPicture = CFile::ResizeImageGet(ArrayHelper::getFirstValue($arItem['GALLERY']['VALUES']), [
                    'width' => 74,
                    'height' => 64
                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

            ?>
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $arPicture['src'], [
                    'alt' => Html::encode($arItem['GALLERY']['PROPERTIES']['ALT']),
                    'title' => Html::encode($arItem['GALLERY']['PROPERTIES']['TITLE']),
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? $arPicture['src'] : null
                ]) ?>
            <?php } else { ?>
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : SITE_TEMPLATE_PATH.'/images/picture.missing.png', [
                    'alt' => Html::encode($arItem['GALLERY']['PROPERTIES']['ALT']),
                    'title' => Html::encode($arItem['GALLERY']['PROPERTIES']['TITLE']),
                    'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'data-original' => $arVisual['LAZYLOAD']['USE'] ? SITE_TEMPLATE_PATH.'/images/picture.missing.png' : null
                ]) ?>
            <?php } ?>
        </div>
    <?= Html::endTag('div') ?>
<?php } ?>
<?php $vPanelGallery($arResult);

if ($bOffers) {
    foreach ($arResult['OFFERS'] as &$arOffer)
        $vPanelGallery($arOffer, true);

    unset($arOffer);
}

unset($vPanelGallery) ?>
