<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
<?php

use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arBlocks
 * @var array $arVisual
 * @var string $sTemplateId
 * @var Closure $renderItems
 */

?>
<div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-h-8 intec-grid-i-v-12">
    <div class="intec-grid-item">
        <div class="widget-tabs">
            <?= Html::beginTag('div', [
                'class' => [
                    'intec-ui' => [
                        '',
                        'control-tabs',
                        'scheme-current',
                        'mod-block',
                        'mod-position-'.$arVisual['TABS']['POSITION'],
                        'view-1'
                    ]
                ],
                'data-ui-control' => 'tabs'
            ]) ?>
                <?php foreach ($arResult['SECTIONS'] as $arSection) {
                    if (empty($arSection['ITEMS']))
                        continue;

                    $sId = $sTemplateId.'_'.$arSection['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arSection['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arSection['DELETE_LINK']);

                ?>
                    <div class="intec-ui-part-tab">
                        <?= Html::tag('a', $arSection['NAME'], [
                            'id' => $sAreaId,
                            'href' => '#'.$sTemplateId.'-collection-'.$arSection['ID'],
                            'data' => [
                                'code' => $sTemplateId.'-collection-'.$arSection['ID'],
                                'type' => 'tab'
                            ]
                        ]) ?>
                    </div>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php if ($arResult['BLOCKS']['MORE']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'intec-grid-item-auto' => true,
                'widget-items-footer' => true,
                'mobile' => !$isSlider && $arBlocks['HEADER']['SHOW']
            ], true)
        ]) ?>
            <?= Html::tag('a', $arResult['BLOCKS']['MORE']['TEXT'], [
                'class' => [
                    'widget-items-footer-more',
                    'intec-cl-text-hover',
                    'intec-grid-item-auto',
                    'intec-grid-item-600-1'
                ],
                'href' => $arResult['BLOCKS']['MORE']['URL'],
                'target' => $arResult['BLOCKS']['MORE']['BLANK'] ? '_blank' : null
            ]); ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>
<div class="widget-tabs-content intec-ui intec-ui-control-tabs-content intec-ui-m-t-25">
    <?php foreach ($arResult['SECTIONS'] as $arSection) { ?>
        <?= Html::beginTag('div', [
            'id' => $sTemplateId.'-collection-'.$arSection['ID'],
            'class' => 'intec-ui-part-tab'
        ]) ?>
            <?php $renderItems($arSection['ITEMS']) ?>
        <?= Html::endTag('div') ?>
    <?php } ?>
</div>