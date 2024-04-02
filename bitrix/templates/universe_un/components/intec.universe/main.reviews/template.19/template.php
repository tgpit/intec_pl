<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use \Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

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
    'QUOTE' => FileHelper::getFileData(__DIR__.'/svg/quote.svg'),
    'RATING' => FileHelper::getFileData(__DIR__.'/svg/rating.svg')
];

?>
<div class="widget c-reviews c-reviews-template-19" id="<?= $sTemplateId ?>">
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
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_19_TEMPLATE_SEND_BUTTON_DEFAULT') ?>
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
                                                    <?= Loc::getMessage('C_MAIN_REVIEW_TEMPLATE_19_TEMPLATE_FOOTER_TEXT_DEFAULT') ?>
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
                <div class="widget-items">
                    <?php $i = 1 ?>
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
                                'width' => 128,
                                'height' => 128
                            ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        $sTag = !empty($arItem['DETAIL_PAGE_URL']) && $arVisual['LINK']['USE'] ? 'a' : 'div';

                    ?>
                        <div class="widget-item" id="<?= $sAreaId ?>">
                            <div class="widget-item-wrapper intec-grid intec-grid-a-v-start intec-grid-i-h-12 intec-grid-i-v-5 intec-grid-450-wrap intec-grid-a-h-450-center">
                                <?php if ($i % 2) { ?>
                                    <div class="widget-item-image-wrap intec-grid-item-auto">
                                        <?= Html::tag($sTag, null, [
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
                                <?php } ?>
                                <div class="widget-item-content-wrap intec-grid-item intec-grid-item-450-1">
                                    <div class="widget-item-content">
                                        <div class="widget-item-description">
                                            <div class="widget-item-description-quote intec-cl-svg-path-stroke">
                                                <?= $arSvg['QUOTE'] ?>
                                            </div>
                                            <div class="widget-item-description-text">
                                                <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid intec-grid-a-v-center intec-grid-a-h-between intec-grid-i-h-5 intec-grid-i-v-5 intec-grid-wrap">
                                            <?php if ($arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT'])) { ?>
                                                <div class="widget-item-link-detail intec-grid-item-auto">
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
                                            <div class="intec-grid-item-auto">
                                                <div class="widget-item-name">
                                                    <?= Html::tag($sTag, $arItem['NAME'], [
                                                        'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                                        'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                                    ]) ?>
                                                </div>
                                                <?php if ($arItem['DATA']['RATING']['SHOW']) {

                                                    $isMatched = false;

                                                ?>
                                                    <div class="widget-item-rating">
                                                        <?= Html::beginTag('div', [
                                                            'class' => Html::cssClassFromArray([
                                                                'intec-grid' => [
                                                                    '' => true,
                                                                    'i-h-2' => true,
                                                                    'a-h-end' => $arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT']),
                                                                    'a-h-550-start' => $arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT'])
                                                                ]
                                                            ], true)
                                                        ])?>
                                                            <?php foreach ($arResult['RATING'] as $key => $value) { ?>
                                                                <?= Html::beginTag('div', [
                                                                    'class' => [
                                                                        'widget-item-rating-item',
                                                                        'intec-grid-item-auto',
                                                                        'intec-ui-picture'
                                                                    ],
                                                                    'title' => ArrayHelper::getValue(
                                                                        $arResult['RATING'],
                                                                        $arItem['DATA']['RATING']['VALUE']
                                                                    ),
                                                                    'data-active' => !$isMatched ? 'true' : 'false'
                                                                ])?>
                                                                    <?= $arSvg['RATING'] ?>
                                                                <?= Html::endTag('div') ?>
                                                                <?php if (!$isMatched)
                                                                    $isMatched = $key == $arItem['DATA']['RATING']['VALUE'];
                                                                ?>
                                                            <?php } ?>
                                                        <?= Html::endTag('div') ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php if (!($i % 2)) { ?>
                                    <div class="widget-item-image-wrap intec-grid-item-auto">
                                        <?= Html::tag($sTag, null, [
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
                                <?php } ?>
                                <?php $i++ ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>