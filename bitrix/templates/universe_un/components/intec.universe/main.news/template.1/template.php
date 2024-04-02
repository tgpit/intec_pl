<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

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
$arNavigation = $arResult['NAVIGATION'];

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'div';

?>
<div class="widget c-news c-news-template-1" id="<?= $sTemplateId ?>">
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
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-items',
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-stretch',
                            'i-5'
                        ]
                    ]),
                    'data' => [
                        'role' => 'items',
                        'date' => $arVisual['DATE']['SHOW'] ? 'true' : 'false'
                    ]
                ]) ?>
                    <!--items-->
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-item' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1024-3' => $arVisual['COLUMNS'] >= 4,
                                    '768-2' => $arVisual['COLUMNS'] >= 3,
                                    '500-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-item-wrapper intec-cl-border-hover" id="<?= $sAreaId ?>">
                                <?= Html::tag($sTag, $arItem['NAME'], [
                                    'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                                    'class' => [
                                        'widget-item-name',
                                        'intec-cl-text-hover'
                                    ]
                                ]) ?>
                                <?php if ($arVisual['DATE']['SHOW']) { ?>
                                    <div class="widget-item-date">
                                        <?php if (!empty($arItem['DATE_ACTIVE_FROM_FORMATTED'])) { ?>
                                            <?= $arItem['DATE_ACTIVE_FROM_FORMATTED'] ?>
                                        <?php } else { ?>
                                            <?= $arItem['DATE_CREATE_FORMATTED'] ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <!--items-->
                <?= Html::endTag('div') ?>
            </div>
            <?php if ($arNavigation['USE']) { ?>
                <div class="widget-pagination" data-role="navigation">
                    <!--navigation-->
                    <?= $arNavigation['PRINT'] ?>
                    <!--navigation-->
                </div>
            <?php } ?>
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
    <?php if ($arNavigation['USE'] && $arNavigation['MODE'] === 'ajax' && !defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>