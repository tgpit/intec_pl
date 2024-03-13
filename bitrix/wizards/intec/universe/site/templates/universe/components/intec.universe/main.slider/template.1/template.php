<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

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
$arProductParameters = $arResult['PRODUCT']['PARAMETERS'];

/**
 * @var Closure $hText($arData, $bHeaderH1, $arForm)
 * @var Closure $vImage($arData)
 * @var Closure $vAdditional()
 */

$vText = include(__DIR__.'/parts/text.php');
$vPicture = include(__DIR__.'/parts/picture.php');
$vVideo = include(__DIR__.'/parts/video.php');
$vAdditional = include(__DIR__.'/parts/additional.php');
$vProduct = include(__DIR__.'/parts/product.php');

?>
<div class="widget c-slider c-slider-template-1" id="<?= $sTemplateId ?>">
    <?= Html::beginTag('div', [
        'class' => [
            'widget-content'
        ],
        'data' => [
            'role' => 'content',
            'scheme' => 'white',
            'nav-view' => $bSliderUse && $arVisual['SLIDER']['NAV']['SHOW'] ? $arVisual['SLIDER']['NAV']['VIEW'] : null,
            'dots-view' => $bSliderUse && $arVisual['SLIDER']['DOTS']['SHOW'] ? $arVisual['SLIDER']['DOTS']['VIEW'] : null,
            'mobile-separated' => $arVisual['MOBILE']['SEPARATED']['USE'] ? 'true' : 'false',
            'mobile-picture' => $arVisual['MOBILE']['PICTURE']['USE'] ? 'true' : 'false'
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
                $sItemName = $arItem['NAME'];
                $sTag = !empty($arData['LINK']['VALUE']) && !$arData['BUTTON']['SHOW'] && !$arData['PRODUCT']['USE'] && !$arResult['FORM']['SHOW'] ? 'a' : 'div';
                $sPicture = ArrayHelper::getValue($arItem, ['PREVIEW_PICTURE', 'SRC']);

                if (empty($sPicture))
                    $sPicture = ArrayHelper::getValue($arItem, ['DETAIL_PICTURE', 'SRC']);

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            ?>
                <div class="widget-item" data-item-scheme="<?= $arData['SCHEME'] ?>">
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
                            'text-position' => $arData['TEXT']['POSITION'],
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
                        <div class="intec-content intec-content-visible intec-content-primary">
                            <div class="intec-content-wrapper">
                                <div class="widget-item-content" id="<?= $sAreaId ?>">
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
                                            'height' => $arVisual['HEIGHT'].'px'
                                        ]
                                    ]) ?>
                                        <?php if ($arData['TEXT']['POSITION'] === 'right')
                                            $vPicture($arData);

                                            if ($arData['PRODUCT']['USE']) {
                                                $vProduct($arData);
                                            } else {
                                                $vText($arData, $bItemsFirst && $arVisual['HEADER']['H1'], $arResult['FORM']);
                                            }

                                            if ($arData['TEXT']['POSITION'] === 'left')
                                                $vPicture($arData);
                                        ?>
                                    <?= Html::endTag('div') ?>
                                    <?php $vAdditional($arData) ?>
                                </div>
                            </div>
                        </div>
                    <?= Html::endTag($sTag) ?>
                </div>
                <?php $bItemsFirst = false ?>
            <?php } ?>
        <?= Html::endTag('div') ?>
        <?php include(__DIR__.'/parts/special.buttons.php') ?>
        <?php include(__DIR__.'/parts/navigation.php') ?>
    <?= Html::endTag('div') ?>
</div>
<?php include(__DIR__.'/parts/script.php') ?>