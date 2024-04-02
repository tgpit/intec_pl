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

/**
 * @var Closure $vItems()
 */

include (__DIR__.'/parts/items.php');

?>
<div class="widget c-gallery c-gallery-template-3" id="<?= $sTemplateId ?>">
    <div class="intec-content">
        <div class="intec-content-wrapper" data-nav="<?= $arVisual['SLIDER']['NAV'] ? 'true' : 'false' ?>">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-use-slider' => $arVisual['SLIDER']['USE'] && $arVisual['SLIDER']['NAV'],
                    'widget-no-header' => !$arBlocks['HEADER']['SHOW'] && !$arBlocks['DESCRIPTION']['SHOW'],
                ],true)
            ])?>
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
                <?= Html::beginTag('div', [
                    'class' => 'widget-content',
                ]) ?>
                    <?php $vItems($arResult['ITEMS']); ?>
                <?= Html::endTag('div') ?>
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
            <?= Html::endTag('div') ?>
        </div>
    </div>
</div>
<?php include(__DIR__.'/parts/script.php') ?>