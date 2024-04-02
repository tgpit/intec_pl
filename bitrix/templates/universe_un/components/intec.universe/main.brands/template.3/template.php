<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arButtons = $arResult['BUTTONS'];
$arVisual = $arResult['VISUAL'];

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-brands',
        'c-brands-template-3'
    ],
    'data' => [
        'effect-1' => $arVisual['EFFECTS']['PRIMARY'],
        'effect-2' => $arVisual['EFFECTS']['SECONDARY'],
        'border' => $arVisual['BORDER']['SHOW'] ? 'true' : 'false',
        'slider' => $arVisual['SLIDER']['USE'] ? 'true' : 'false',
        'slider-dots' => $arVisual['SLIDER']['DOTS'] ? 'true' : 'false',
        'slider-navigation' => $arVisual['SLIDER']['NAVIGATION'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arButtons['SHOW_ALL']['DISPLAY'] === 'top') { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arButtons['SHOW_ALL']['DISPLAY'] === 'top' ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arButtons['SHOW_ALL']['DISPLAY'] === 'top') { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-all-container' => true,
                                    'mobile' => $arBlocks['HEADER']['SHOW'],
                                    'intec-grid-item' => [
                                        'auto' => $arBlocks['HEADER']['SHOW'],
                                        '1' => !$arBlocks['HEADER']['SHOW']
                                    ]
                                ], true)
                            ]) ?>
                            <?= Html::beginTag('a', [
                                    'class' => [
                                        'widget-all-button',
                                        'widget-header-button',
                                        'intec-cl-text-light-hover',
                                    ],
                                    'href' => $arButtons['SHOW_ALL']['LINK']
                                ])?>
                                    <span><?= Html::encode($arButtons['SHOW_ALL']['TEXT']) ?></span>
                                    <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items' => true,
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-start' => !$arVisual['SLIDER']['USE'],
                            'a-h-'.$arVisual['ALIGNMENT'] => !$arVisual['SLIDER']['USE']
                        ],
                        'owl-carousel' => $arVisual['SLIDER']['USE']
                    ], true),
                    'data' => [
                        'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';
                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 400,
                                'height' => 400
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => !$arVisual['SLIDER']['USE'],
                                    '1024-4' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 5,
                                    '768-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 4,
                                    '600-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] >= 3
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag($sTag, [
                                'id' => $sAreaId,
                                'class' => 'widget-item-wrapper',
                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                'target' => $arVisual['LINK']['USE'] && $arVisual['LINK']['BLANK'] ? '_blank' : null
                            ]) ?>
                                <?= Html::tag('div', null, [
                                    'class' => 'widget-item-picture',
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'opacity' => $arVisual['TRANSPARENCY'],
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                            <?= Html::endTag($sTag) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arButtons['SHOW_ALL']['DISPLAY'] === 'bottom') { ?>
                <div class="widget-footer align-<?= $arButtons['SHOW_ALL']['ALIGNMENT'] ?>">
                    <a href="<?= $arButtons['SHOW_ALL']['LINK'] ?>" class="<?= Html::cssClassFromArray([
                        'widget-footer-button' => true,
                        'widget-footer-button-rectangular' => $arButtons['SHOW_ALL']['BORDER'] === 'rectangular',
                        'widget-footer-button-rounded' => $arButtons['SHOW_ALL']['BORDER'] === 'rounded',
                        'intec-ui' => [
                            '' => true,
                            'control-button' => true,
                            'mod-transparent' => true,
                            'scheme-current' => true
                        ]
                    ], true) ?>">
                        <?= Html::encode($arButtons['SHOW_ALL']['TEXT']) ?>
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php include (__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>