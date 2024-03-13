<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arVisual) {
    $sPicture = $arItem['PICTURE'];

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 300,
            'height' => 300
        ], BX_RESIZE_IMAGE_PROPORTIONAL);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <?= Html::beginTag('a', [
        'class' => [
            'catalog-section-item-picture-wrap',
            'intec-image-effect'
        ],
        'href' => $arItem['DETAIL_PAGE_URL']
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => [
                'catalog-section-item-picture',
                'intec-ui-picture'
            ]
        ]) ?>
            <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                'alt' => !empty($arItem['PICTURE']['ALT']) ? $arItem['PICTURE']['ALT'] : $arItem['NAME'],
                'title' => !empty($arItem['PICTURE']['TITLE']) ? $arItem['PICTURE']['TITLE'] : $arItem['NAME'],
                'loading' => 'lazy',
                'data' => [
                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                ]
            ]) ?>
        <?= Html::endTag('div') ?>
    <?= Html::endTag('a') ?>
<?php } ?>