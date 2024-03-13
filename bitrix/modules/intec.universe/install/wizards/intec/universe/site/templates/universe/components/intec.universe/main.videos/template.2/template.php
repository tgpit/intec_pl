<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;
use intec\core\helpers\FileHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

$iCounter = 0;

?>
<div class="widget c-videos c-videos-template-2" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
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
            <?php } ?>
            <div class="widget-content">
                <div class="widget-content-wrapper intec-grid intec-grid-a-v-stretch intec-grid-768-wrap">
                    <div class="widget-viewport intec-grid-item intec-grid-item-768-1">
                        <div class="widget-viewport-wrapper">
                            <?php $arFirstItem = ArrayHelper::getFirstValue($arResult['ITEMS']); ?>
                            <?= Html::tag('iframe', '', [
                                'class' => 'widget-viewport-item',
                                'src' => $arFirstItem['URL']['embed'],
                                'allow' => 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture',
                                'allowfullscreen' => '',
                                'data-role' => 'view'
                            ]) ?>
                            <?php unset($arFirstItem) ?>
                        </div>
                    </div>
                    <div class="widget-items-wrap intec-grid-item-auto intec-grid-item-768-1">
                        <div class="widget-items scroll-mod-hiding scrollbar-inner" data-role="items">
                            <?php foreach ($arResult['ITEMS'] as $arItem) {

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $iCounter++;

                                if (defined('EDITOR') && $iCounter > 4)
                                    break;

                                $sPicture = $arItem['PICTURE'];

                                if ($sPicture['SOURCE'] === 'detail' || $sPicture['SOURCE'] === 'preview') {
                                    $sPicture = CFile::ResizeImageGet($sPicture, [
                                        'width' => 200,
                                        'height' => 200
                                    ]);

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];
                                } else if ($sPicture['SOURCE'] === 'service') {
                                    $sPicture = $sPicture['SRC'];
                                } else {
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                }

                            ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sAreaId,
                                    'class' => [
                                        'widget-item',
                                        'intec-cl-text-hover'
                                    ],
                                    'data' => [
                                        'role' => 'item',
                                        'id' => $arItem['URL']['ID'],
                                        'active' => 'false'
                                    ]
                                ]) ?>
                                    <div class="widget-item-wrapper intec-grid intec-grid-a-v-center">
                                        <div class="intec-grid-item-auto">
                                            <?= Html::beginTag('div', [
                                                'class' => 'widget-item-picture',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                ],
                                                'style' => [
                                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                                ]
                                            ]) ?>
                                                <div class="widget-item-picture-decoration">
                                                    <div class="widget-video-button-wrapper">
                                                        <?= $arSvg['VIDEO'] ?>
                                                    </div>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                        <div class="intec-grid-item">
                                            <div class="widget-item-name">
                                                <?= $arItem['NAME'] ?>
                                            </div>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
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
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>