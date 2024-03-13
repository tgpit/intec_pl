<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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
$arVisual = $arResult['VISUAL'];

$iCounter = 0;
$iItemsTotal = count($arResult['ITEMS']);
$iItemCurrent = 0;

?>
<div class="widget c-services c-services-template-4" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
        <div class="intec-content intec-content-visible widget-header">
            <div class="intec-content-wrapper">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title-container intec-grid-item">
                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                'class' => [
                                    'widget-title',
                                    'align-'.$arBlocks['HEADER']['POSITION'],
                                    $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                ]
                            ]) ?>
                        </div>
                        <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
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
                                    'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
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
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <div class="widget-items">
            <?php foreach ($arResult['ITEMS'] as $arItem) {

                $sId = $sTemplateId.'_'.$arItem['ID'];
                $sAreaId = $this->GetEditAreaId($sId);
                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                $iCounter ++;
                $iItemCurrent++;

                $arData = $arItem['DATA'];

                $sPicture = $arItem['DETAIL_PICTURE']['SRC'];

                if (empty($sPicture))
                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

            ?>
                <?= Html::beginTag('div', [
                    'class' => 'widget-item',
                    'data-parallax-ratio' => $arVisual['PARALLAX']['USE'] ? $arVisual['PARALLAX']['RATIO'] : null,
                    'data' => [
                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                    ],
                    'style' => [
                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                    ]
                ]) ?>
                    <div class="intec-content intec-content-visible">
                        <div class="intec-content-wrapper" id="<?= $sAreaId ?>">
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item-wrapper' => true,
                                    'intec' => [
                                        'grid' => [
                                            '' => true,
                                            'important' => true,
                                            'wrap' => true,
                                            'a-v-center' => true,
                                            'a-h' => [
                                                'start' => $iCounter % 2 == 1,
                                                'end' => $iCounter % 2 != 1
                                            ]
                                        ]
                                    ]
                                ], true)
                            ]) ?>
                                <div class="widget-item-content intec-grid-item intec-grid-item-auto">
                                    <?php if ($arVisual['ICON']['SHOW'] && !empty($arData['ICON']) || $arVisual['NUMBER']['SHOW']) { ?>
                                        <div class="widget-item-decoration intec-grid">
                                            <?php if ($arVisual['ICON']['SHOW'] && !empty($arData['ICON'])) {

                                                $sIcon = CFile::ResizeImageGet($arData['ICON'], [
                                                    'width' => 150,
                                                    'height' => 150
                                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                                $sIcon = $sIcon['src'];

                                            ?>
                                                <div class="intec-grid-item-auto">
                                                    <?= Html::tag('div', '', [
                                                        'class' => 'widget-item-decoration-icon',
                                                        'style' => [
                                                            'background-image' => 'url("'.$sIcon.'")'
                                                        ]
                                                    ]) ?>
                                                </div>
                                            <?php } ?>
                                            <div class="intec-grid-item">
                                                <?php if ($arVisual['NUMBER']['SHOW']) { ?>
                                                    <div class="widget-item-decoration-count">
                                                        <span class="current">
                                                            <?= $iItemCurrent ?>
                                                        </span>
                                                        <span class="all">
                                                            <?= ' / '.$iItemsTotal ?>
                                                        </span>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="widget-item-name">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                    <?php if ($arVisual['DESCRIPTION']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                                        <div class="widget-item-description">
                                            <?= TruncateText(Html::stripTags($arItem['PREVIEW_TEXT']), 140) ?>
                                        </div>
                                    <?php } ?>
                                    <?php if ($arVisual['DETAIL']['SHOW']) { ?>
                                        <div class="widget-item-detail-wrap">
                                            <a class="widget-item-detail intec-ui intec-ui-control-button intec-ui-size-2 intec-ui-mod-round-2 intec-ui-scheme-current" href="<?= $arItem['DETAIL_PAGE_URL'] ?>">
                                                <span class="widget-item-detail-text">
                                                    <?= $arVisual['DETAIL']['TEXT'] ?>
                                                </span>
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
    <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
        <div class="intec-content">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-footer' => true,
                    'intec-content-wrapper' => true,
                    'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                    'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
                ], true)
            ]) ?>
                <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                    <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                        'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                        'class' => [
                            'widget-footer-button',
                            'intec-ui' => [
                                '',
                                'size-5',
                                'scheme-current',
                                'control-button',
                                'mod' => [
                                    'transparent',
                                    'round-half'
                                ]
                            ]
                        ]
                    ]) ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    <?php } ?>
</div>