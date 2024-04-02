<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var CAllMain $APPLICATION
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$bSliderUse = count($arResult['ITEMS']) > 1;
$bDesktop = Core::$app->browser->isDesktop;
$bItemsFirst = true;

/**
 * @var Closure $hText($arData, $bHeaderH1, $arForm)
 * @var Closure $vImage($arData)
 * @var Closure $vAdditional()
 */
$vText = include(__DIR__.'/parts/text.php');
$vPicture = include(__DIR__.'/parts/picture.php');
$vVideo = include(__DIR__.'/parts/video.php');
$vAdditional = include(__DIR__.'/parts/additional.php');

$sPrefix = 'BANNER_PRODUCTS_';
$arProductsSmallParameters = [];

foreach ($arParams as $sKey => $sValue) {
    if (StringHelper::startsWith($sKey, $sPrefix)) {
        $sKey = StringHelper::cut($sKey, StringHelper::length($sPrefix));
        $arProductsSmallParameters[$sKey] = $sValue;
    }
}

$arProductsSmallParameters = ArrayHelper::merge($arProductsSmallParameters, [
    'IBLOCK_TYPE' => $arParams['BANNER_PRODUCTS_IBLOCK_TYPE'],
    'IBLOCK_ID' => $arParams['BANNER_PRODUCTS_IBLOCK_ID'],
    'COMPARE_NAME' => $arParams['BANNER_PRODUCTS_COMPARE_NAME'],
    'LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'QUICK_VIEW_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'QUICK_VIEW_TIMER_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE'],
    'ORDER_FAST_LAZYLOAD_USE' => $arParams['LAZYLOAD_USE']
]);

?>
<div class="widget c-slider c-slider-template-4" id="<?= $sTemplateId ?>">
<?php if ($arVisual['BANNER_PRODUCTS']['SHOW']) { ?>
    <div class="intec-content">
<?php } ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-content-wrapper' => true
            ], true)
        ]) ?>
            <div class="widget-content-wrapper intec-grid intec-grid-768-wrap" data-role="content.wrapper" data-products-use="<?= $arVisual['BANNER_PRODUCTS']['SHOW'] ? 'true' : 'false' ?>">
                <div class="intec-grid-item-768-1 intec-grid-item">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-content',
                            $arVisual['BANNER_PRODUCTS']['SHOW'] ? 'widget-content-banner' : null,
                            $arVisual['MOBILE']['BANNER']['SEPARATED']['USE'] ? 'widget-items-mobile-banner-separate' : null
                        ],
                        'data' => [
                            'role' => 'content',
                            'scheme' => 'white'
                        ],
                        'data-nav-view' => $bSliderUse && $arVisual['SLIDER']['NAV']['SHOW'] ? $arVisual['SLIDER']['NAV']['VIEW'] : null,
                        'data-dots-view' => $bSliderUse && $arVisual['SLIDER']['DOTS']['SHOW'] ? $arVisual['SLIDER']['DOTS']['VIEW'] : null
                    ]) ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-items' => true,
                            'owl-carousel' => $bSliderUse
                        ], true),
                        'data' => [
                            'role' => 'slider-container'
                        ]
                    ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];

                        $sItemName = $arItem['NAME'];

                        $sTag = !empty($arData['LINK']['VALUE']) && !$arData['BUTTON']['SHOW'] ? 'a' : 'div';
                        $sPicture = ArrayHelper::getValue($arItem, ['PREVIEW_PICTURE', 'SRC']);

                        if (empty($sPicture))
                            $sPicture = ArrayHelper::getValue($arItem, ['DETAIL_PICTURE', 'SRC']);

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        ?>

                        <?php if ($arVisual['MOBILE']['BANNER']['SEPARATED']['USE']) { ?>
                            <div class="widget-item-wrapper">
                        <?php } ?>

                        <?= Html::beginTag($sTag, [
                        'href' => $sTag === 'a' ? $arData['LINK']['VALUE'] : null,
                        'class' => 'widget-item',
                        'target' => $sTag === 'a' && $arData['LINK']['BLANK'] ? '_blank' : null,
                        'data' => [
                            'item-scheme' => $arData['SCHEME'],
                            'text-position' => $arData['TEXT']['POSITION'],
                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                        ],
                        'style' => [
                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                        ]
                    ]) ?>
                        <?php if ($arData['MOBILE']['PICTURE']['USE']) { ?>
                            <?php $sMobilePicture = $arData['MOBILE']['PICTURE']['VALUE']['SRC']; ?>
                            <?= Html::tag('div', '', [
                                'class' => 'widget-item-picture-mobile',
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sMobilePicture : null
                                ],
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sMobilePicture.'\')' : null
                                ]
                            ]) ?>
                        <?php } ?>

                        <?php if ($bDesktop && $arVisual['VIDEO']['SHOW']) {
                            $vVideo($arData, $sPicture);
                        } ?>

                        <?php if ($arData['FADE']) { ?>
                            <div class="widget-item-fade"></div>
                        <?php } ?>

                        <div class="widget-item-content-wrap intec-content intec-content-visible intec-content-primary">
                            <div class="widget-item-content-wrap-2 intec-content-wrapper">
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-content',
                                    'id' => $sAreaId,
                                    'data-banner-product' => $arVisual['BANNER_PRODUCTS']['SHOW'] ? 'true' : 'false'
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-content-body' => true,
                                            'intec-grid' => [
                                                '' => true,
                                                'i-h-20' => true,
                                                'a-h-center' => $arData['TEXT']['POSITION'] === 'center' && $arData['TEXT']['HALF']
                                            ]
                                        ], true),
                                        'style' => [
                                            'height' => $arVisual['BANNER_PRODUCTS']['SHOW'] ? '100%' : $arVisual['HEIGHT'].'px'
                                        ]
                                    ]) ?>

                                    <?php if ($arData['TEXT']['POSITION'] === 'right') {
                                        $vPicture($arData);
                                    } ?>

                                    <?php $vText($arData, $bItemsFirst && $arVisual['HEADER']['H1'], $arResult['FORM']) ?>

                                    <?php if ($arData['TEXT']['POSITION'] === 'left') {
                                        $vPicture($arData);
                                    } ?>

                                    <?= Html::endTag('div') ?>

                                    <?php $vAdditional($arData) ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        </div>
                        <?= Html::endTag($sTag) ?>
                        <?php if ($arVisual['MOBILE']['BANNER']['SEPARATED']['USE']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => 'widget-item-content-mobile',
                                'data-align' => $arVisual['MOBILE']['BANNER']['SEPARATED']['LEFT'] ? 'left' : null
                            ]) ?>
                            <div class="widget-item-content-wrap intec-content intec-content-visible intec-content-primary">
                                <div class="widget-item-content-wrap-2 intec-content-wrapper">
                                    <?= Html::beginTag('div', [
                                        'class' => 'widget-item-content',
                                        'id' => $sAreaId,
                                    ]) ?>
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'widget-item-content-body' => true,
                                                'intec-grid' => [
                                                    '' => true,
                                                    'i-h-20' => true,
                                                    'a-h-center' => $arData['TEXT']['POSITION'] === 'center' && $arData['TEXT']['HALF']
                                                ]
                                            ], true)
                                        ]) ?>
                                        <?php $vText($arData, false, $arResult['FORM']); ?>
                                        <?= Html::endTag('div') ?>
                                        <?php $vAdditional($arData) ?>
                                    <?= Html::endTag('div') ?>
                                </div>
                            </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?php if ($arVisual['MOBILE']['BANNER']['SEPARATED']['USE']) { ?>
                            </div>
                        <?php } ?>
                        <?php $bItemsFirst = false ?>
                    <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php include(__DIR__.'/parts/special.buttons.php') ?>
                    <?php include(__DIR__.'/parts/navigation.php') ?>
                    <?= Html::endTag('div') ?>
                </div>
                <?php if ($arVisual['BANNER_PRODUCTS']['SHOW']) { ?>
                    <div class="widget-item-banner-products intec-grid-item-768 intec-grid-item-auto" data-role="banner.products">
                        <?php $APPLICATION->IncludeComponent(
                            'intec.universe:main.widget',
                            'products.small.1',
                            $arProductsSmallParameters,
                            $component
                        ); ?>
                    </div>
                <?php } ?>
            </div>
        <?= Html::endTag('div') ?>
<?php if ($arVisual['BANNER_PRODUCTS']['SHOW']) { ?>
    </div>
<?php } ?>
</div>
<?php include(__DIR__.'/parts/script.php') ?>