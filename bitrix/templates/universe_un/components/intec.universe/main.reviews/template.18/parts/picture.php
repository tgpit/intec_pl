<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arVisual
 * @var array $arSvg
 * @var string $sTag
 */

?>
<?php $vPicture = function (&$arItem) use (&$arVisual, &$arSvg, &$sTag) { ?>
    <?php if ($arItem['DATA']['VIDEO']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => [
                'widget-item-picture',
                'intec-cl-svg-path-stroke',
                'intec-cl-svg-path-fill'
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arItem['DATA']['VIDEO']['VALUE'][$arVisual['VIDEO']['QUALITY']].'\')' : null
            ],
            'data' => [
                'role' => 'video',
                'src' => $arItem['DATA']['VIDEO']['VALUE']['iframe'],
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $arItem['DATA']['VIDEO']['VALUE'][$arVisual['VIDEO']['QUALITY']] : null
            ]
        ]) ?>
            <?= $arSvg['PLAY'] ?>
        <?= Html::endTag('div') ?>
    <?php } else {

        $sPicture = $arItem['PREVIEW_PICTURE'];

        if (empty($sPicture))
            $sPicture = $arItem['DETAIL_PICTURE'];

        if (!empty($sPicture)) {
            $sPicture = CFile::ResizeImageGet($sPicture, [
                'width' => 300,
                'height' => 300
            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

            if (!empty($sPicture))
                $sPicture = $sPicture['src'];
        }

        if (empty($sPicture))
            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

    ?>
        <?= Html::tag($sTag, null, [
            'class' => [
                'widget-item-picture',
                'intec-image-effect'
            ],
            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
            'data' => [
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
            ]
        ]) ?>
    <?php } ?>
<?php } ?>