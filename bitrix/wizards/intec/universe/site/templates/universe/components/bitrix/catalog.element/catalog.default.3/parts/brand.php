<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\Html;
use intec\template\Properties;

/**
 * @var array $arResult
 * @var array $arVisual
 */

if (empty($arResult['BRAND']['PICTURE']))
    return;

$sPicture = $arResult['BRAND']['PICTURE'];
$sPicture = CFile::ResizeImageGet($sPicture, [
    'width' => 200,
    'height' => 60
], BX_RESIZE_IMAGE_PROPORTIONAL);

if (!empty($sPicture))
    $sPicture = $sPicture['src'];

if (empty($sPicture)) {
    unset($sPicture);
    return;
}

if (!defined('EDITOR') && $arVisual['LAZYLOAD']['USE'])
    $sStub = Properties::get('template-images-lazyload-stub');
else
    $sStub = null;

?>
<?= Html::beginTag('a', [
    'class' => [
        'catalog-element-brand',
        'intec-ui-picture'
    ],
    'href' => $arResult['BRAND']['DETAIL_PAGE_URL']
]) ?>
    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $sStub : $sPicture, [
        'alt' => $arResult['BRAND']['NAME'],
        'title' => $arResult['BRAND']['NAME'],
        'loading' => 'lazy',
        'data' => [
            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
        ]
    ]) ?>
<?= Html::endTag('a') ?>
<?php unset($sPicture, $sStub) ?>