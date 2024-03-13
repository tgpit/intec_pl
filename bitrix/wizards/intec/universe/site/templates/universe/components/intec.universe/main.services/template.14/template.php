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

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-services c-services-template-14">
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="intec-grid intec-grid-a-v-start intec-grid-i-20 intec-grid-wrap">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-1">
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
                    </div>
                <?php } ?>
                <div class="widget-buttons intec-grid-item intec-grid-item-1" data-role="buttons">
                    <?= Html::beginTag('div', [
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-h' . $arVisual['TABS']['POSITION']
                        ]
                    ])?>
                        <?= Html::beginTag('ul', [
                            'class' => [
                                'widget-tabs',
                                'intec-ui' => [
                                    '',
                                    'control-tabs',
                                    'mod-block',
                                    'mod-position-'.$arVisual['TABS']['POSITION'],
                                    'scheme-current',
                                    'view-2'
                                ]
                            ],
                            'data' => [
                                'ui-control' => 'tabs'
                            ]
                        ]) ?>
                            <?php $iCounter = 0 ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                                <?= Html::beginTag('li', [
                                    'class' => 'intec-ui-part-tab',
                                    'data' => [
                                        'active' => $iCounter === 0 ? 'true' : 'false'
                                    ]
                                ]) ?>
                                    <a href="<?= '#'.$sTemplateId.'-tab-'.$iCounter ?>" data-type="tab">
                                        <?= $arItem['NAME'] ?>
                                    </a>
                                <?= Html::endTag('li') ?>
                                <?php $iCounter++ ?>
                            <?php } ?>
                        <?= Html::endTag('ul') ?>
                    <?= Html::endTag('div') ?>
                </div>
            </div>

            <div class="widget-content">
                <div class="widget-items">
                    <div class="widget-tabs-content intec-ui intec-ui-control-tabs-content">
                        <?php $iCounter = 0 ?>
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $arData = $arItem['DATA'];

                            ?>
                            <?= Html::beginTag('div', [
                                'id' => $sTemplateId.'-tab-'.$iCounter,
                                'class' => 'intec-ui-part-tab',
                                'data' => [
                                    'active' => $iCounter === 0 ? 'true' : 'false'
                                ]
                            ]) ?>
                                <div class="widget-item" id="<?= $sAreaId ?>">
                                    <div class="widget-item-description">
                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                    </div>
                                    <?php if ($arVisual['PROPERTIES']['SHOW']) { ?>
                                        <div class="widget-item-properties">
                                            <div class="intec-grid intec-grid-wrap intec-grid-i-h-25 intec-grid-i-v-25">
                                                <?php foreach ($arData['TEXT'] as $arElement) { ?>
                                                    <div class="widget-item-property intec-grid-item-2 intec-grid-item-768-1">
                                                        <?= $arElement ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                            <?php $iCounter++ ?>
                        <?php } ?>
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
</div>
