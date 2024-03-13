<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
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
    'ARROW' => FileHelper::getFileData(__DIR__.'/svg/arrow.svg'),
    'SEE_ALL' => FileHelper::getFileData(__DIR__.'/svg/header.list.mobile.svg')
];
$iCounter = 0;

?>
<div class="widget c-faq c-faq-template-4" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper">
            <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-16">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['SEE_ALL']['SHOW']) { ?>
                    <div class="intec-grid-item-4 intec-grid-item-950-3 intec-grid-item-800-1">
                        <div class="widget-header" data-link-all="<?= $arVisual['SEE_ALL']['SHOW'] ? 'true' : 'false' ?>">
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'widget-title',
                                    'align-' . $arBlocks['HEADER']['POSITION'],
                                    'intec-grid' => [
                                        '',
                                        'a-h-end',
                                        'a-v-center'
                                    ]
                                ]
                            ]) ?>
                                <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                    <div class="intec-grid-item">
                                        <?= $arBlocks['HEADER']['TEXT'] ?>
                                    </div>
                                <?php } ?>
                                    <?php if ($arVisual['SEE_ALL']['SHOW']) { ?>
                                        <div class="intec-grid-item-auto">
                                            <?= Html::tag('a', $arSvg['SEE_ALL'], [
                                                'class' => [
                                                    'widget-list-mobile',
                                                    'intec-ui-picture',
                                                    'intec-cl-svg-path-stroke-hover'
                                                ],
                                                'href' => $arVisual['SEE_ALL']['LINK']
                                            ])?>
                                        </div>
                                    <?php } ?>
                            <?= Html::endTag('div') ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            <?php } ?>
                        </div>
                        <?php if ($arVisual['SEE_ALL']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => [
                                    'intec-ui-m-t-30',
                                    'widget-list-desktop',
                                    'align-' . $arVisual['SEE_ALL']['POSITION']
                                ]
                            ]) ?>
                                <?= Html::tag('a', $arVisual['SEE_ALL']['TEXT'], [
                                    'href' => $arVisual['SEE_ALL']['LINK'],
                                    'class' => [
                                        'widget-button',
                                        'intec-cl-text',
                                        'intec-ui' => [
                                            '',
                                            'scheme-current',
                                            'control-button',
                                            'mod' => [
                                                'transparent',
                                                'round-2'
                                            ]
                                        ]
                                    ]
                                ]) ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?php } ?>
                <div class="intec-grid-item">
                    <div class="widget-content">
                        <?= Html::beginTag('div', [
                            'class' => 'widget-items',
                            'data' => [
                                'role' => 'container',
                                'expanded' => 'false'
                            ]
                        ]) ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {
                                if (empty($arItem['DATA']['TEXT']))
                                    continue;

                                $sId = $sTemplateId . '_' . $arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $iCounter++;
                                $bHideItem = $arVisual['LIMITED_ITEMS']['USE'] && $iCounter > $arVisual['LIMITED_ITEMS']['COUNT'];

                                ?>
                                <?= Html::beginTag('div', [
                                    'class' => 'widget-item',
                                    'id' => $sAreaId,
                                    'data' => [
                                        'role' => 'item',
                                        'expanded' => $arItem['DATA']['EXPANDED'] ? 'true' : 'false',
                                        'action' => $bHideItem ? 'hide' : 'none'
                                    ],
                                    'style' => [
                                        'display' => $bHideItem ? 'none' : null
                                    ]
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => [
                                            'widget-item-name',
                                            'intec-cl-text-hover'
                                        ],
                                        'data' => [
                                            'role' => 'item.button'
                                        ]
                                    ]) ?>
                                        <?= $arItem['NAME'] ?>
                                        <div class="widget-item-name-icon intec-ui-picture">
                                            <?= $arSvg['ARROW'] ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                    <div class="widget-item-content" data-role="item.content">
                                        <div class="widget-item-description">
                                            <?= $arItem['DATA']['TEXT'] ?>
                                        </div>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>
                    <?php if ($arVisual['LIMITED_ITEMS']['USE']) { ?>
                        <div class="intec-ui-m-t-30" style="text-align: right;">
                            <?= Html::tag('div', Loc::getMessage('C_MAIN_FAQ_TEMPLATE_4_TEMPLATE_BUTTON_SHOW'), [
                                'class' => [
                                    'widget-button',
                                    'intec-ui' => [
                                        '',
                                        'scheme-current',
                                        'control-button',
                                        'mod' => [
                                            'transparent',
                                            'round-2'
                                        ]
                                    ]
                                ],
                                'data' => [
                                    'role' => 'container.toggle'
                                ]
                            ]) ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
</div>