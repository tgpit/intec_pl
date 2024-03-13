<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
 * @var Closure $vVideo($arData)
 * @var Closure $vBlocks($arBlocks)
 */
$vText = include(__DIR__.'/parts/text.php');
$vVideo = include(__DIR__.'/parts/video.php');
$vNavigation = include(__DIR__.'/parts/navigation.php');
$vProduct = include(__DIR__.'/parts/product.php');

if ($arVisual['PICTURE']['SHOW'])
    $vPicture = include(__DIR__.'/parts/picture.php');

if ($arVisual['BLOCKS']['USE'] && !empty($arResult['BLOCKS']))
    $vBlocks = include(__DIR__.'/parts/blocks.php');

?>
<div class="widget c-slider c-slider-template-3" id="<?= $sTemplateId ?>">
    <?php if (!$arVisual['WIDE']) { ?>
        <div class="intec-content intec-content-visible">
            <div class="intec-content-wrapper">
    <?php } ?>
    <?= Html::beginTag('div', [
        'class' => [
            'widget-content'
        ],
        'data' => [
            'role' => 'content',
            'wide' => $arVisual['WIDE'] ? 'true' : 'false',
            'blocks-use' => $arVisual['BLOCKS']['USE'] && !empty($arResult['BLOCKS']) ? 'true' : 'false',
            'blocks-position' => $arVisual['BLOCKS']['USE'] ? $arVisual['BLOCKS']['POSITION'] : null,
            'nav-view' => $bSliderUse && $arVisual['SLIDER']['NAV']['SHOW'] ? $arVisual['SLIDER']['NAV']['VIEW'] : null,
            'dots-view' => $bSliderUse && $arVisual['SLIDER']['DOTS']['SHOW'] ? $arVisual['SLIDER']['DOTS']['VIEW'] : null,
            'mobile-separated' => $arVisual['MOBILE']['SEPARATED']['USE'] ? 'true' : 'false',
            'mobile-picture' => $arVisual['MOBILE']['PICTURE']['USE'] ? 'true' : 'false',
            'scheme' => 'white'
        ]
    ]) ?>
        <?= Html::beginTag('div', [
            'class' => 'widget-slider'
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
                    <div id="<?=$sAreaId?>" class="widget-item" data-item-scheme="<?= $arData['SCHEME'] ?>">
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
                                'item-scheme' => $arData['SCHEME'],
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
                            <?php if (!$arVisual['WIDE'] || !$arVisual['BLOCKS']['USE'] || $arVisual['BLOCKS']['POSITION'] !== 'right') { ?>
                                <div class="intec-content intec-content-primary intec-content-visible">
                                    <div class="intec-content-wrapper">
                            <?php } ?>
                                        <div class="widget-item-content" data-product="<?= $arData['PRODUCT']['USE'] ? 'true' : 'false' ?>">
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-content-body' => true,
                                                    'intec-grid' => [
                                                        '' => true,
                                                        'i-h-12' => true,
                                                        'a-h-center' => $arData['TEXT']['POSITION'] === 'center'
                                                    ]
                                                ], true),
                                                'style' => [
                                                    'height' => $arVisual['HEIGHT'].'px'
                                                ]
                                            ]) ?>
                                                <?php if ($arData['PICTURE']['SHOW'] && $arData['TEXT']['POSITION'] === 'right') {
                                                    $vPicture($arData);
                                                } ?>
                                                <?php if ($arData['PRODUCT']['USE']) {
                                                    $vProduct($arData);
                                                } else {
                                                    $vText($arData);
                                                } ?>
                                                <?php if ($arData['PICTURE']['SHOW'] && $arData['TEXT']['POSITION'] === 'left') {
                                                    $vPicture($arData);
                                                } ?>
                                            <?= Html::endTag('div') ?>
                                        </div>
                            <?php if (!$arVisual['WIDE'] || !$arVisual['BLOCKS']['USE'] || $arVisual['BLOCKS']['POSITION'] !== 'right') { ?>
                                    </div>
                                </div>
                            <?php } ?>
                        <?= Html::endTag($sTag) ?>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
            <?php $vNavigation() ?>
        <?= Html::endTag('div') ?>
        <?php if ($arVisual['BLOCKS']['USE'] && !empty($arResult['BLOCKS'])) {
            $vBlocks($arResult['BLOCKS']);
        } ?>
    <?= Html::endTag('div') ?>
    <?php if (!$arVisual['WIDE']) { ?>
            </div>
        </div>
    <?php } ?>
</div>
<?php include(__DIR__.'/parts/script.php') ?>