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
        'c-brands-template-4'
    ],
    'data' => [
        'effect-1' => $arVisual['EFFECTS']['PRIMARY'],
        'effect-2' => $arVisual['EFFECTS']['SECONDARY'],
        'border' => $arVisual['BORDER']['SHOW'] ? 'true' : 'false',
        'columns' => $arVisual['COLUMNS']
    ]
]) ?>
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="widget-wrapper-3 intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-20">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arButtons['SHOW_ALL']['SHOW']) { ?>
                    <div class="widget-header intec-grid-item-3 intec-grid-item-1200-1">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="intec-grid-item">
                                    <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                        'class' => [
                                            'widget-title',
                                            'align-'.$arBlocks['HEADER']['POSITION'],
                                            $arButtons['SHOW_ALL']['SHOW'] ? 'widget-title-margin' : null
                                        ]
                                    ]) ?>
                                </div>
                                <?php if ($arButtons['SHOW_ALL']['SHOW']) { ?>
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
                                                'intec-cl-text-light-hover',
                                            ],
                                            'href' => $arButtons['SHOW_ALL']['LINK']
                                        ])?>
                                            <i class="fal fa-angle-right"></i>
                                        <?= Html::endTag('a')?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="intec-grid-item-1">
                                    <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                        <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($arButtons['SHOW_ALL']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-button-wrap' => true,
                                    'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                                    'mobile' => $arBlocks['HEADER']['SHOW'],
                                ], true)
                            ]) ?>
                                <a href="<?= $arButtons['SHOW_ALL']['LINK'] ?>" class="<?= Html::cssClassFromArray([
                                    'widget-button' => true,
                                    'widget-button-rectangular' => $arButtons['SHOW_ALL']['BORDER'] === 'rectangular',
                                    'widget-button-rounded' => $arButtons['SHOW_ALL']['BORDER'] === 'rounded',
                                    'intec-ui' => [
                                        '' => true,
                                        'control-button' => true,
                                        'mod-transparent' => true,
                                        'scheme-current' => true
                                    ]
                                ], true) ?>">
                                    <?= Html::encode($arButtons['SHOW_ALL']['TEXT']) ?>
                                </a>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="widget-content intec-grid-item intec-grid-item-1200-1">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-items',
                            'intec-grid' => [
                                '',
                                'wrap',
                                'a-v-start',
                                'a-h-'.$arVisual['ALIGNMENT']
                            ]
                        ])
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
                                        $arVisual['COLUMNS'] => true,
                                        '1024-4' => $arVisual['COLUMNS'] >= 5,
                                        '768-3' => $arVisual['COLUMNS'] >= 4,
                                        '600-2' => $arVisual['COLUMNS'] >= 3
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
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>