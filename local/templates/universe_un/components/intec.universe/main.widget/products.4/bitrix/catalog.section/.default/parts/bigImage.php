<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arVisual
 */

?>
<?php return function (&$arItem) use (&$arResult, &$arVisual) {
    $sPicture = $arItem['BANNER']['PICTURE'];

    if (empty($sPicture) && !empty($arItem['PICTURES']))
        $sPicture = reset($arItem['PICTURES']);

    if (!empty($sPicture)) {
        $sPicture = CFile::ResizeImageGet($sPicture, [
            'width' => 700,
            'height' => 700
        ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

        if (!empty($sPicture))
            $sPicture = $sPicture['src'];
    }

    if (empty($sPicture))
        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

?>
    <div class="widget-item-big-image">
        <?= Html::tag('div', null, [
            'class' => [
                'widget-item-big-image-wrapper',
                'intec-image-effect'
            ],
            'data' => [
                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
            ],
            'style' => [
                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
            ]
        ]) ?>
    </div>
<?php } ?>