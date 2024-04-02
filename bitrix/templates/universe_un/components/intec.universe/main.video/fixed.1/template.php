<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Json;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);
$arVisual = $arResult['VISUAL'];

if (!$arVisual['SHOW'] || isset($_COOKIE['VIDEO_WIDGET_CLOSE']) && $_COOKIE['VIDEO_WIDGET_CLOSE'] === 'Y')
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$sId = $sTemplateId.'_'.$arResult['ITEM']['ID'];
$sAreaId = $this->GetEditAreaId($sId);
$this->AddEditAction($sId, $arResult['ITEM']['EDIT_LINK']);
$this->AddDeleteAction($sId, $arResult['ITEM']['DELETE_LINK']);

$sTag = $arVisual['BUTTON']['MODE'] === 'link' ? 'a' : 'div';
$vVideo = include(__DIR__.'/parts/video.php');
$arSvg = [
    'CLOSE' => FileHelper::getFileData(__DIR__.'/svg/close.svg'),
    'ROLL_UP' => FileHelper::getFileData(__DIR__.'/svg/roll.up.svg'),
    'MUTE' => [
        'ON' => FileHelper::getFileData(__DIR__.'/svg/mute.on.svg'),
        'OFF' => FileHelper::getFileData(__DIR__.'/svg/mute.off.svg')
    ]
];

?>

<div class="widget c-video c-video-fixed-1" id="<?= $sTemplateId ?>">
    <div class="widget-container">
        <?= Html::beginTag('div', [
            'id' => $sAreaId,
            'class' => 'widget-item',
            'data' => [
                'role' => 'item',
                'position' => $arVisual['POSITION'],
                'scaled' => 'false',
                'state' => 'hidden'
            ]
        ]) ?>
            <div class="widget-item-video">
                <?php $vVideo($arResult['ITEM']['DATA']) ?>
            </div>
            <?php if ($arVisual['BUTTON']['USE']) { ?>
                <?= Html::tag($sTag, $arVisual['BUTTON']['TEXT'], [
                    'class' => [
                        'widget-button',
                        'intec-ui' => [
                            '',
                            'control-button',
                            'scheme-current',
                            'mod-round-2'
                        ]
                    ],
                    'href' => $sTag === 'a' ? $arVisual['BUTTON']['LINK'] : null,
                    'target' => $sTag === 'a' ? '_blank' : null,
                    'style' => [
                        'animation-delay' => $arVisual['BUTTON']['DELAY'].'s'
                    ],
                    'data' => [
                        'role' => 'button',
                        'mode' => $arVisual['BUTTON']['MODE'],
                        'quick-view' => Json::encode($arResult['ITEM']['DATA']['QUICK_VIEW'], JSON_UNESCAPED_UNICODE | JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_APOS, true)
                    ]
                ]) ?>
            <?php } ?>
            <?= Html::tag('div', $arSvg['CLOSE'], [
                'class' => [
                    'widget-button-icon',
                    'intec-ui-picture'
                ],
                'data' => [
                    'role' => 'close'
                ]
            ]) ?>
            <?= Html::tag('div', $arSvg['ROLL_UP'], [
                'class' => [
                    'widget-button-icon',
                    'intec-ui-picture'
                ],
                'data' => [
                    'role' => 'roll.up'
                ]
            ]) ?>
            <?= Html::beginTag('div', [
                'class' => [
                    'widget-button-icon',
                    'intec-ui-picture'
                ],
                'data' => [
                    'role' => 'volume',
                    'state' => 'off'
                ]
            ]) ?>
                <?= Html::tag('span', $arSvg['MUTE']['ON'], [
                    'class' => '',
                    'data' => [
                        'code' => 'volume.on'
                    ]
                ]) ?>
                <?= Html::tag('span', $arSvg['MUTE']['OFF'], [
                    'class' => '',
                    'data' => [
                        'code' => 'volume.off'
                    ]
                ]) ?>
            <?= Html::endTag('div') ?>
        <?= Html::endTag('div') ?>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>