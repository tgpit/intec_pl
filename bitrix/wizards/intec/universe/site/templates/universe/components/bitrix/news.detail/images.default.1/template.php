<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\helpers\Html;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\Core;

/**
 * @var CMain $APPLICATION
 * @var CBitrixComponent $component
 * @var array $arResult
 * @var array $arParams
 */

$this->setFrameMode(true);
$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

include(__DIR__.'/parts/products.php');

$oRequest = Core::$app->request;
$bIsAjax = false;

if ($oRequest->getIsAjax()) {
    $bIsAjax = $oRequest->get('images');
    $bIsAjax = ArrayHelper::getValue($bIsAjax, 'ajax') === 'Y';
}

$sPicture = null;

if (!empty($arResult['DETAIL_PICTURE'])) {
    $sPicture = $arResult['DETAIL_PICTURE'];
} else if (!empty($arResult['PREVIEW_PICTURE'])) {
    $sPicture = $arResult['PREVIEW_PICTURE'];
}

$sPicture = CFile::ResizeImageGet($sPicture, [
    'width' => 1920,
    'height' => 1920
], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

if (!empty($sPicture)) {
    $sPicture = $sPicture['src'];
} else {
    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
}
?>

<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'ns-bitrix',
        'c-news-detail',
        'c-news-detail-images-detail-1'
    ]
]) ?>
    <?php if ($arVisual['SLIDER']['SHOW']) { ?>
        <div class="news-detail-slide-wrapper">
            <div class="news-detail-slide-title">
                <?= Loc::getMessage('C_NEWS_DETAIL_IMAGES_DETAIL_1_SLIDE_TITLE'); ?>
            </div>
            <div class="intec-grid intec-grid-i-h-16 news-detail-slide">
                <?php foreach ($arResult['ITEMS'] as $arItem) {
                    $sItemImage = null;

                    if (!empty($arItem['DETAIL_PICTURE'])) {
                        $sItemImage = $arItem['DETAIL_PICTURE'];
                    } else if (!empty($arItem['PREVIEW_PICTURE'])) {
                        $sItemImage = $arItem['PREVIEW_PICTURE'];
                    }

                    $sItemImage = CFile::ResizeImageGet($sItemImage, [
                        'width' => 80,
                        'height' => 108
                    ], BX_RESIZE_IMAGE_EXACT);

                    if (!empty($sItemImage)) {
                        $sItemImage = $sItemImage['src'];
                    } else {
                        $sItemImage = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                    } ?>

                    <?= Html::beginTag('div', [
                        'class' => [
                            'news-detail-slide-image-wrapper',
                            'intec-grid-item-auto'
                        ]
                    ]) ?>
                        <?= Html::beginTag($arItem['ID'] === $arResult['ID'] ? 'div' : 'a', [
                            'class' => Html::cssClassFromArray([
                                'news-detail-slide-image-link' => true,
                                'intec-cl-border' => $arItem['ID'] === $arResult['ID'] ? true : false
                            ], true),
                            'href' => $arItem['ID'] !== $arResult['ID'] ? $arItem['DETAIL_PAGE_URL'] : null
                        ]) ?>
                            <?= Html::tag('div', null, [
                                'class' => 'news-detail-slide-image',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sItemImage : null
                                ],
                                'style' => [
                                    'background-image' => 'url(\''.($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sItemImage).'\')'
                                ]
                            ]) ?>
                        <?= Html::endTag($arItem['ID'] === $arResult['ID'] ? 'div' : 'a') ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
    <div class="intec-grid intec-grid-550-wrap intec-grid-i-16">
        <div class="intec-grid-item-3 intec-grid-item-768-2 intec-grid-item-550-1">
            <div class="news-detail-image intec-ui-picture">
                <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                    'alt' => $arResult['NAME'],
                    'title' => $arResult['NAME'],
                    'loading' => 'lazy',
                    'data' => [
                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                    ]
                ]) ?>
            </div>
        </div>
        <div class="intec-grid-item intec-grid-item-768-2 intec-grid-item-550-1">
            <div class="intec-grid intec-grid-a-h-start intec-grid-a-v-start intec-grid-i-8">
                <div class="intec-grid-item">
                    <h1 class="news-detail-name"><?= $arResult['NAME'] ?></h1>
                </div>
                <?php if ($arVisual['SHARES']['SHOW']) { ?>
                    <div class="intec-grid-item-auto">
                        <?php include(__DIR__.'/parts/shares.php') ?>
                    </div>
                <?php } ?>
            </div>
            <?php if (!empty($arResult['DETAIL_TEXT'])) { ?>
                <div class="news-detail-description intec-ui-markup-text">
                    <?= $arResult['DETAIL_TEXT'] ?>
                </div>
            <?php } ?>
            <?php if ($arVisual['PROPERTY']['SHOW']) { ?>
                <div class="news-detail-properties">
                    <?php foreach ($arVisual['PROPERTY']['VALUES'] as $value) { ?>
                        <div class="news-detail-properties-item">
                            <span class="news-detail-properties-name">
                                <?= $value['NAME'] ?>
                            </span>
                            <span class="news-detail-properties-separator">
                                &#8212;
                            </span>
                            <span class="news-detail-properties-value">
                                <?= $value['VALUE'] ?>
                            </span>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="news-detail-products" data-role="content">
        <?php if ($bIsAjax) $APPLICATION->RestartBuffer() ?>
            <?php if ($arVisual['PRODUCTS']['SHOW']) { ?>
                <?php $APPLICATION->IncludeComponent(
                    'bitrix:catalog.section',
                    $arResult['PRODUCTS']['TEMPLATE'],
                    $arResult['PRODUCTS']['PARAMETERS'],
                    $component
                ) ?>
            <?php } ?>
        <?php if ($bIsAjax) exit() ?>
    </div>
<?= Html::endTag('div') ?>
