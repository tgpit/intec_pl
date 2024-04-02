<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

if (empty($arResult['ITEMS']))
    return;

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$iCounter = 0;

$sSvg = FileHelper::getFileData(__DIR__.'/svg/item.decoration.svg');

?>
<div class="widget c-videos c-videos-template-5" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="widget-content">
                <div class="widget-content-wrapper intec-grid intec-grid-a-v-center intec-grid-768-wrap">
                    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-header-wrap intec-grid-item-3 intec-grid-item-768-1">
                            <div class="widget-header">
                                <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                        <div class="widget-title-container intec-grid-item">
                                            <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                                'class' => [
                                                    'widget-title',
                                                    'align-'.$arBlocks['HEADER']['POSITION'],
                                                    $arBlocks['BUTTON_ALL']['SHOW'] ? 'widget-title-margin' : null
                                                ]
                                            ]) ?>
                                        </div>
                                        <?php if ($arBlocks['BUTTON_ALL']['SHOW']) { ?>
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
                                                    'href' => $arBlocks['BUTTON_ALL']['LINK']
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
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-button-all-wrap' => true,
                                        'align-' . $arBlocks['BUTTON_ALL']['POSITION'] => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['BUTTON_ALL']['SHOW']
                                    ], true)
                                ]) ?>
                                    <?php if ($arBlocks['BUTTON_ALL']['SHOW']) { ?>
                                        <?= Html::tag('a', $arBlocks['BUTTON_ALL']['TEXT'], [
                                            'href' => $arBlocks['BUTTON_ALL']['LINK'],
                                            'class' => [
                                                'widget-button-all',
                                                'intec-ui' => [
                                                    '',
                                                    'size-5',
                                                    'scheme-current',
                                                    'control-button',
                                                    'mod' => [
                                                        'transparent',
                                                        'round-2'
                                                    ]
                                                ]
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="widget-items-wrap intec-grid-item intec-grid-item-768-1" data-role="items">
                        <div class="widget-viewport-wrapper">
                            <?php $arFirstItem = ArrayHelper::getFirstValue($arResult['ITEMS']); ?>
                            <?php
                                $sPicture = $arFirstItem['PICTURE'];

                                if ($sPicture['SOURCE'] === 'detail' || $sPicture['SOURCE'] === 'preview') {
                                    $sPicture = CFile::ResizeImageGet($sPicture, [
                                        'width' => 1000,
                                        'height' => 1000
                                    ]);

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];
                                } else if ($sPicture['SOURCE'] === 'service') {
                                    $sPicture = $sPicture['SRC'];
                                } else {
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                                }
                            ?>
                            <?= Html::tag('div', $sSvg, [
                                'class' => 'widget-viewport-item',
                                'title' => $arFirstItem['NAME'],
                                'data-role' => 'view',
                                'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                'data-src' => !empty($arFirstItem['URL']) ? $arFirstItem['URL']['embed'] : null,
                                'style' => [
                                    'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] :'url(\''.$sPicture.'\')'
                                ]
                            ]) ?>
                            <?php unset($arFirstItem) ?>
                        </div>
                        <div class="widget-items">
                            <div class="widget-items-wrapper owl-carousel" data-role="slider">
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
                                            'active' => 'false',
                                            'picture-src' => $sPicture
                                        ]
                                    ]) ?>
                                        <div class="widget-item-wrapper">
                                            <?= Html::tag('div', '', [
                                                'class' => 'widget-item-picture',
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                ],
                                                'style' => [
                                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                                ]
                                            ]) ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>