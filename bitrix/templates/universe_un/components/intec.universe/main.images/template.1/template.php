<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

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
    'NAVIGATION' => [
        'LEFT' => FileHelper::getFileData(__DIR__.'/svg/collections.arrow.left.svg'),
        'RIGHT' => FileHelper::getFileData(__DIR__.'/svg/collections.arrow.right.svg')
    ]
];

$renderItems = include(__DIR__.'/parts/items.php');

?>
<div class="widget c-images c-images-template-1" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arResult['BLOCKS']['MORE']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arResult['BLOCKS']['MORE']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-more-container' => true,
                                        'mobile' => $arBlocks['HEADER']['SHOW'],
                                        'intec-grid-item' => [
                                            'auto' => $arBlocks['HEADER']['SHOW'],
                                            '1' => !$arBlocks['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-more-button',
                                            'intec-cl-text-light-hover',
                                        ],
                                        'href' => $arResult['BLOCKS']['MORE']['URL'],
                                        'target' => $arResult['BLOCKS']['MORE']['BLANK'] ? '_blank' : null
                                    ])?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="intec-grid-item-1">
                                <?= Html::tag('div', Html::encode($arBlocks['DESCRIPTION']['TEXT']), [
                                    'class' => [
                                        'widget-description',
                                        'align-'.$arBlocks['DESCRIPTION']['POSITION']
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?php if ($arVisual['TABS']['USE'])
                    include(__DIR__.'/parts/tabs.php');
                else
                    $renderItems($arResult['ITEMS']);
                ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>