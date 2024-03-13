<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
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
$arSvg = [
    'RATING' => FileHelper::getFileData(__DIR__.'/svg/rating.svg')
];

/**
 * @var Closure $vRating
 */
include(__DIR__.'/parts/rating.php');

?>
<div class="widget c-reviews c-reviews-template-13" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-title',
                                'align-'.$arBlocks['HEADER']['POSITION'],
                                $arBlocks['HEADER']['POSITION'] === 'center' && $arBlocks['FOOTER']['SHOW'] ? 'widget-title-margin' : null
                            ]
                        ]) ?>
                            <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-end intec-grid-i-h-12">
                                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                    <div class="intec-grid-item">
                                        <?= $arBlocks['HEADER']['TEXT'] ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arVisual['SEND']['USE']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::beginTag('div', [
                                            'class' => [
                                                'widget-send',
                                                'intec-cl' => [
                                                    'text-hover',
                                                    'border-hover',
                                                    'svg-path-stroke-hover'
                                                ]
                                            ],
                                            'data-role' => 'review.send'
                                        ]) ?>
                                            <div class="intec-grid intec-grid-a-v-center intec-grid-i-h-4">
                                                <div class="widget-send-icon intec-ui-picture intec-grid-item-auto">
                                                    <?= FileHelper::getFileData(__DIR__.'/svg/send.svg') ?>
                                                </div>
                                                <div class="widget-send-content intec-grid-item">
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_13_TEMPLATE_SEND_BUTTON_DEFAULT') ?>
                                                </div>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                <?php } ?>
                                <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                                    <div class="intec-grid-item-auto">
                                        <?= Html::beginTag('a', [
                                            'class' => 'widget-all',
                                            'href' => $arBlocks['FOOTER']['LINK']
                                        ]) ?>
                                            <span class="widget-all-desktop intec-cl-text-hover">
                                                <?php if (empty($arBlocks['FOOTER']['TEXT'])) { ?>
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_13_TEMPLATE_FOOTER_TEXT_DEFAULT') ?>
                                                <?php } else { ?>
                                                    <?= $arBlocks['FOOTER']['TEXT'] ?>
                                                <?php } ?>
                                            </span>
                                            <span class="widget-all-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                                <?= FileHelper::getFileData(__DIR__.'/svg/all.mobile.svg') ?>
                                            </span>
                                        <?= Html::endTag('a') ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-'.(
                                    $arBlocks['FOOTER']['SHOW'] || $arVisual['SEND']['USE'] ? 'left' : $arBlocks['DESCRIPTION']['POSITION']
                                )
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'owl-carousel'
                    ],
                    'data' => [
                        'role' => 'container'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        if (!$arItem['DATA']['PREVIEW']['SHOW'])
                            continue;

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 64,
                                    'height' => 64
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                            );

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        $sTag = !empty($arItem['DETAIL_PAGE_URL']) && $arVisual['LINK']['USE'] ? 'a' : 'div';

                    ?>
                        <div class="widget-item intec-grid-item" id="<?= $sAreaId ?>">
                            <div class="intec-grid intec-grid-nowrap intec-grid-o-vertical intec-grid-a-h-between widget-item-wrapper">
                                <div class="intec-grid-item-auto intec-grid intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-5 intec-grid-wrap">
                                    <div class="widget-item-image-wrap intec-grid-item-auto">
                                        <?= Html::tag($sTag, '', [
                                            'class' => [
                                                'widget-item-image',
                                                'intec-image-effect'
                                            ],
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null,
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]) ?>
                                    </div>
                                    <div class="intec-grid-item">
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-wrap intec-grid-i-6">
                                            <div class="intec-grid-item-1">
                                                <div class="intec-grid intec-grid-wrap intec-grid-a-h-between intec-grid-a-v-center intec-grid-i-4">
                                                    <?php if ($arVisual['ACTIVE_DATE']['SHOW']) { ?>
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-date">
                                                                <?= $arItem['DATA']['DATE'] ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                    <?php if ($arItem['DATA']['RATING']['SHOW']) {?>
                                                        <div class="intec-grid-item-auto">
                                                            <div class="widget-item-rating-desktop">
                                                                <?php $vRating($arItem['DATA'], $arResult['RATING']) ?>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="intec-grid-item-1">
                                                <?= Html::tag($sTag, $arItem['NAME'], [
                                                    'class' => 'widget-item-name',
                                                    'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                                    'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($arItem['DATA']['RATING']['SHOW']) {?>
                                        <div class="widget-item-rating-mobile intec-grid-item-1">
                                            <?php $vRating($arItem['DATA'], $arResult['RATING']) ?>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="intec-grid-item-auto widget-item-description">
                                    <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                </div>
                                <?php if ($arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT'])) { ?>
                                    <div class="intec-grid-item-auto widget-item-link-detail">
                                        <?= Html::tag('a', $arParams['LINK_TEXT'], [
                                            'class' => [
                                                'widget-item-link-detail-button',
                                                'intec-ui' => [
                                                    '',
                                                    'control-button',
                                                    'scheme-current',
                                                    'mod-transparent',
                                                    'mod-round-2'
                                                ]
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL'],
                                            'target' => $arVisual['LINK']['BLANK'] ? '_blank' : null
                                        ]) ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>