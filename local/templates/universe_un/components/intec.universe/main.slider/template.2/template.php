<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\Core;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

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
$arProductParameters = $arResult['PRODUCT']['PARAMETERS'];

/**
 * @var Closure $vText($arData)
 * @var Closure $vPicture($arData)
 * @var Closure $vNavigation()
 * @var Closure $vBlocks($arBlocks, $position, $half)
 */
$vText = include(__DIR__.'/parts/text.php');
$vPicture = include(__DIR__.'/parts/picture.php');
$vNavigation = include(__DIR__.'/parts/navigation.php');
$vProduct = include(__DIR__.'/parts/product.php');

if ($arVisual['VIDEO']['SHOW'])
    $vVideo = include(__DIR__.'/parts/video.php');


if ($arResult['BLOCKS']['USE'])
    $vBlocks = include(__DIR__.'/parts/blocks.php');

?>
<div class="widget c-slider c-slider-template-2" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => 'widget-slider',
                    'data' => [
                        'role' => 'content',
                        'indent-left' => !empty($arResult['BLOCKS']['LEFT']) ? 'true' : 'false',
                        'indent-right' => !empty($arResult['BLOCKS']['RIGHT']) ? 'true' : 'false',
                        'rounded' => $arVisual['ROUNDED'] ? 'true' : 'false',
                        'nav-view' => $bSliderUse && $arVisual['SLIDER']['NAV']['SHOW'] ? $arVisual['SLIDER']['NAV']['VIEW'] : 'none',
                        'dots-view' =>  $bSliderUse && $arVisual['SLIDER']['DOTS']['SHOW'] ? $arVisual['SLIDER']['DOTS']['VIEW'] : 'none',
                        'mobile-separated' => $arVisual['MOBILE']['SEPARATED']['USE'] ? 'true' : 'false',
                        'mobile-picture' => $arVisual['MOBILE']['PICTURE']['USE'] ? 'true' : 'false',
                        'scheme' => 'dark'
                    ]
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

                            $sTag = !empty($arData['LINK']['VALUE']) && !$arData['BUTTON']['SHOW'] && !$arData['PRODUCT']['USE'] ? 'a' : 'div';
                            $sPicture = ArrayHelper::getValue($arItem, ['PREVIEW_PICTURE', 'SRC']);

                            if (empty($sPicture))
                                $sPicture = ArrayHelper::getValue($arItem, ['DETAIL_PICTURE', 'SRC']);

                            if (empty($sPicture))
                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        ?>
                            <div class="widget-item" data-item-scheme="<?= $arData['SCHEME'] ?>" id="<?= $sAreaId ?>">
                                <?php
                                $sPictureMobile = $arData['MOBILE']['PICTURE']['USE'] ? $arData['MOBILE']['PICTURE']['VALUE']['SRC'] : $sPicture;
                                ?>
                                <?= Html::beginTag($sTag, [
                                    'class' => 'widget-item-block-mobile',
                                    'href' => $sTag === 'a' ? $arData['LINK']['VALUE'] : null,
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPictureMobile : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPictureMobile.'\')' : null
                                    ]
                                ]) ?>
                                    <?php if ($arData['PICTURE']['SHOW'] && $arVisual['MOBILE']['SEPARATED']['USE']) { ?>
                                        <?= Html::tag('div', '', [
                                            'class' => 'widget-item-block-mobile-small-picture',
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $arData['PICTURE']['VALUE']['SRC'] : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$arData['PICTURE']['VALUE']['SRC'].'\')' : null
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                <?= Html::endTag($sTag) ?>
                                <?= Html::beginTag($sTag, [
                                    'href' => $sTag === 'a' ? $arData['LINK']['VALUE'] : null,
                                    'class' => 'widget-item-block-desktop',
                                    'target' => $sTag === 'a' && $arData['LINK']['BLANK'] ? '_blank' : null,
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                    <?php if ($bDesktop && $arVisual['VIDEO']['SHOW']) {
                                        $vVideo($arData, $sPicture);
                                    } ?>
                                    <?php if ($arData['FADE']) { ?>
                                        <div class="widget-item-fade"></div>
                                    <?php } ?>
                                    <div class="widget-item-content">
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'widget-item-wrapper' => true,
                                                'intec-grid' => [
                                                    '' => true,
                                                    'i-h-20' => true,
                                                    'a-h-center' => $arData['TEXT']['POSITION'] === 'center' && $arData['TEXT']['HALF']
                                                ]
                                            ], true),
                                            'style' => [
                                                'height' => $arVisual['HEIGHT'].'px'
                                            ]
                                        ]) ?>
                                            <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arData['PICTURE']['VALUE']) && $arData['TEXT']['POSITION'] === 'right') {
                                                $vPicture($arData);
                                            } ?>
                                            <?php if ($arData['PRODUCT']['USE']) {
                                                $vProduct($arData);
                                            } else {
                                                $vText($arData);
                                            } ?>
                                            <?php if ($arVisual['PICTURE']['SHOW'] && !empty($arData['PICTURE']['VALUE']) && $arData['TEXT']['POSITION'] === 'left') {
                                                $vPicture($arData);
                                            } ?>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?= Html::endTag($sTag) ?>
                            </div>



                        <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php $vNavigation() ?>
                <?= Html::endTag('div') ?>
                <?php if ($arResult['BLOCKS']['USE']) { ?>
                    <?php $vBlocks($arResult['BLOCKS']['LEFT'], 'left', $arVisual['BLOCKS']['HALF']) ?>
                    <?php $vBlocks($arResult['BLOCKS']['RIGHT'], 'right', $arVisual['BLOCKS']['HALF']) ?>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php') ?>