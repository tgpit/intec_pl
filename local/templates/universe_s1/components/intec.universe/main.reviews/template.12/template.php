<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
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

?>
<div class="widget c-reviews c-reviews-template-12" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper intec-grid intec-grid-important intec-grid-800-wrap">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTON_SHOW_ALL']['SHOW']) { ?>
                <div class="widget-header widget-content-left intec-grid-item-4 intec-grid-item-950-3 intec-grid-item-800-1">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arVisual['BUTTON_SHOW_ALL']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arVisual['BUTTON_SHOW_ALL']['SHOW']) { ?>
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
                    <?php if ($arVisual['BUTTON_SHOW_ALL']['SHOW']) { ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-link-all' => true,
                                'mobile' => $arBlocks['HEADER']['SHOW'] && $arVisual['BUTTON_SHOW_ALL']['SHOW']
                            ], true),
                            'style' => [
                                'text-align' => $arParams['BUTTON_ALL_POSITION']
                            ]
                        ]) ?>
                            <?= Html::tag('a', $arVisual['BUTTON_SHOW_ALL']['TEXT'], [
                                'href' => $arVisual['BUTTON_SHOW_ALL']['LINK'],
                                'class' => [
                                    'intec-ui' => [
                                        '',
                                        'control-button',
                                        'mod-transparent',
                                        'scheme-current'
                                    ]
                                ]
                            ]) ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content-right intec-grid-item">
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items',
                        'owl-carousel'
                    ],
                    'data-role' => 'container'
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        if (!$arItem['DATA']['PREVIEW']['SHOW'])
                            continue;

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $arData = $arItem['DATA'];
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

                        $sTag = !empty($arItem['DETAIL_PAGE_URL']) && $arVisual['LINK']['USE'] ? 'a' : 'div';

                    ?>
                        <div class="widget-item intec-grid-item" id="<?= $sAreaId ?>">
                            <div class="intec-grid intec-grid-nowrap intec-grid-o-vertical intec-grid-a-h-start widget-item-wrapper">
                                <div class="intec-grid-item-auto intec-grid intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-5 intec-grid-wrap">
                                    <div class="widget-item-image-wrap intec-grid-item-auto">
                                        <?= Html::tag($sTag, '', [
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'class' => [
                                                'widget-item-image',
                                                'intec-image-effect'
                                            ],
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
                                    <div class="widget-item-name-wrap intec-grid-item">
                                        <?php if ($arVisual['ACTIVE_DATE']['SHOW']) { ?>
                                            <div class="widget-item-date">
                                                <?= $arItem['DISPLAY_ACTIVE_FROM'];?>
                                            </div>
                                        <?php } ?>
                                        <?= Html::tag($sTag, $arItem['NAME'], [
                                            'href' => $sTag === 'a' ? $arItem['DETAIL_PAGE_URL'] : null,
                                            'class' => 'widget-item-name',
                                            'target' => $sTag === 'a' && $arVisual['LINK']['BLANK'] ? '_blank' : null
                                        ]) ?>
                                    </div>
                                    <?php if ($arData['RATING']['SHOW']) {

                                        $isMatch = false;

                                    ?>
                                        <div class="intec-grid-item-auto intec-grid-item-600-1">
                                            <div class="widget-item-rating">
                                                <div class="intec-grid intec-grid-i-h-2">
                                                    <?php foreach ($arResult['RATING'] as $key => $value) { ?>
                                                        <?= Html::tag('div', $arSvg['RATING'], [
                                                            'class' => [
                                                                'intec-grid-item-auto',
                                                                'widget-item-rating-item',
                                                                'intec-ui-picture'
                                                            ],
                                                            'title' => ArrayHelper::getValue(
                                                                $arResult['RATING'],
                                                                $arData['RATING']['VALUE']
                                                            ),
                                                            'data-active' => !$isMatch ? 'true' : 'false'
                                                        ]) ?>
                                                        <?php if ($key == $arData['RATING']['VALUE'])
                                                            $isMatch = true;
                                                        ?>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="intec-grid-item-auto widget-item-description">
                                    <?= $arItem['DATA']['PREVIEW']['VALUE'] ?>
                                </div>
                                <?php if ($arVisual['LINK']['USE'] && !empty($arParams['LINK_TEXT'])) { ?>
                                    <div class="intec-grid-item-auto widget-item-link-detail">
                                        <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="widget-item-link-detail-button intec-ui intec-ui-control-button intec-ui-mod-transparent intec-ui-scheme-current intec-ui-mod-round-2">
                                            <?= $arParams['LINK_TEXT'] ?>
                                        </a>
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