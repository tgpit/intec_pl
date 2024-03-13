<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;


$arVisual = $arResult['VISUAL'];
$arBlocks = $arResult['BLOCKS'];
$arHeader = $arResult['BLOCKS']['HEADER'];
$arDescription = $arResult['BLOCKS']['DESCRIPTION'];
$arContent = $arResult['BLOCKS']['CONTENT'];
$arFooter = $arResult['BLOCKS']['FOOTER'];

$arSvg = [
    'VIDEO' => FileHelper::getFileData(__DIR__.'/svg/video.play.svg')
];

?>
<div class="widget c-videos c-videos-template-1" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'top')) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW'] || ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'top')) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-title',
                                'align-'.(
                                    $arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'top' ? 'left' : $arBlocks['HEADER']['POSITION']
                                )
                            ]
                        ]) ?>
                        <div class="intec-grid intec-grid-a-h-end intec-grid-a-v-center intec-grid-i-h-8">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="intec-grid-item">
                                    <?= $arBlocks['HEADER']['TEXT'] ?>
                                </div>
                            <?php } ?>
                            <?php if ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'top') { ?>
                                <div class="intec-grid-item-auto">
                                    <?= Html::beginTag('a', [
                                        'class' => 'widget-all',
                                        'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                    ]) ?>
                                        <span class="widget-all-desktop intec-cl-text-hover">
                                            <?php if (!empty($arBlocks['FOOTER']['BUTTON']['TEXT'])) { ?>
                                                <?= $arBlocks['FOOTER']['BUTTON']['TEXT'] ?>
                                            <?php } else { ?>
                                                <?= Loc::getMessage('C_VIDEOS_TEMP1_TEMPLATE_FOOTER_BUTTON_TEXT') ?>
                                            <?php } ?>
                                        </span>
                                        <span class="widget-all-mobile intec-ui-picture intec-cl-svg-path-stroke-hover">
                                            <?= FileHelper::getFileData(__DIR__.'/svg/list.arrow.svg') ?>
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
                                    $arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'top' ? 'left' : $arBlocks['DESCRIPTION']['POSITION']
                                )
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'owl-carousel' => $arVisual['SLIDER']['USE'],
                        'intec-grid' => [
                            '' => !$arVisual['SLIDER']['USE'],
                            'wrap' => !$arVisual['SLIDER']['USE'],
                            'a-v-start' => !$arVisual['SLIDER']['USE'],
                            'a-h-start' => !$arVisual['SLIDER']['USE'] && $arContent['POSITION'] === 'left',
                            'a-h-center' => !$arVisual['SLIDER']['USE'] && $arContent['POSITION'] === 'center',
                            'a-h-end' => !$arVisual['SLIDER']['USE'] && $arContent['POSITION'] === 'right',
                            'i-16' => true
                        ]
                    ], true),
                    'data' => [
                        'role' => $arVisual['SLIDER']['USE'] ? 'slider' : null,
                        'entity' => 'gallery'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PICTURE'];

                        if ($sPicture['SOURCE'] === 'preview' || $sPicture['SOURCE'] === 'detail') {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 800,
                                'height' => 800
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
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-4' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 4,
                                    '768-3' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 3,
                                    '600-2' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 2,
                                    '400-1' => !$arVisual['SLIDER']['USE'] && $arVisual['COLUMNS'] > 1
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'widget-item-content',
                                    'intec-cl-text-hover'
                                ],
                                'data' => [
                                    'src' => !empty($arItem['URL']) ? $arItem['URL']['embed'] : null,
                                    'play' => !empty($arItem['URL']) ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item-picture',
                                    'title' => !$arVisual['NAME']['SHOW'] ? $arItem['NAME'] : null,
                                    'data' => [
                                        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                    ],
                                    'style' => [
                                        'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                    ]
                                ]) ?>
                                    <div class="widget-item-fade"></div>
                                    <div class="widget-item-decoration intec-cl-background intec-ui-picture">
                                        <?= $arSvg['VIDEO'] ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                                <?php if ($arVisual['NAME']['SHOW']) { ?>
                                    <div class="widget-item-name">
                                        <?= $arItem['NAME'] ?>
                                    </div>
                                <?php } ?>
                            <?= Html::endTag('div') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arBlocks['FOOTER']['SHOW'] && $arBlocks['FOOTER']['POSITION'] === 'bottom') { ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-footer',
                        'align-'.$arBlocks['FOOTER']['ALIGN']
                    ]
                ]) ?>
                    <?= Html::tag('a', $arFooter['BUTTON']['TEXT'], [
                        'class' => [
                            'widget-footer-button',
                            'intec-ui' => [
                                '',
                                'control-button',
                                'scheme-current',
                                'mod-transparent',
                                'mod-round-2'
                            ]
                        ],
                        'href' => $arFooter['BUTTON']['LINK']
                    ]) ?>
                <?= Html::endTag('div') ?>
            <?php } ?>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>