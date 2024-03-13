<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 * @var array $bDescriptionFull
 * @var CBitrixComponent $component
 * @var array $arForms
 */

$arGallery = ArrayHelper::getValue($arResult, 'GALLERY');

if (!empty($arResult['PREVIEW_PICTURE'])) {
    $arGallery = ArrayHelper::merge([$arResult['PREVIEW_PICTURE']], $arGallery);
}

if (!empty($arResult['DETAIL_PICTURE'])) {
    $arGallery = ArrayHelper::merge([$arResult['DETAIL_PICTURE']], $arGallery);
}

$sDescription = '';

if (!empty($arResult['PREVIEW_TEXT']))
    $sDescription = $arResult['PREVIEW_TEXT'];
elseif (!empty($arResult['DETAIL_TEXT']))
    $sDescription = $arResult['DETAIL_TEXT'];

?>

<div class="intec-content">
    <div class="intec-content-wrapper">
        <div class="news-detail-content-header intec-grid intec-grid-wrap">
            <?php include(__DIR__.'/header/previews.php') ?>
            <?php include(__DIR__.'/header/gallery.php') ?>
            <div class="news-detail-content-header-info intec-grid-item">
                <?php include(__DIR__.'/header/properties.php') ?>
                <?= Html::tag('div', $sDescription, [
                    'class' => Html::cssClassFromArray([
                        'news-detail-content-header-info-description' => true,
                        'short' => !$bDescriptionFull,
                        'intec-ui-markup-text' => true
                    ], true)
                ]) ?>
                <?php include(__DIR__.'/header/buttons.php') ?>
            </div>
        </div>
    </div>
</div>
