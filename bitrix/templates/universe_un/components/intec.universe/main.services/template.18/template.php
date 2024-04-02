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

$sTag = $arVisual['LINK']['USE'] ? 'a' : 'span';

/**
 * @var Closure $vItem(&$arItem)
 */
$vItem = include(__DIR__.'/parts/item.php');

?>
<div class="widget c-services c-services-template-18" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['HEADER']['TEXT'], [
                            'class' => [
                                'widget-title',
                                'align-'.$arBlocks['HEADER']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <?= Html::tag('div', $arBlocks['DESCRIPTION']['TEXT'], [
                            'class' => [
                                'widget-description',
                                'align-'.$arBlocks['DESCRIPTION']['POSITION']
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <?php if ($arVisual['SLIDER']['USE']) { ?>
                    <div class="widget-content-dynamic owl-carousel" data-role="slider">
                        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                            <div class="widget-content-item">
                                <?php $vItem($arItem) ?>
                            </div>
                        <?php } ?>
                    </div>
                    <?= Html::tag('div', null, [
                        'class' => 'widget-navigation',
                        'data' => [
                            'role' => 'navigation',
                            'view' => $arVisual['SLIDER']['NAV']['VIEW']
                        ]
                    ]) ?>
                <?php } else { ?>
                    <div class="widget-content-static intec-grid intec-grid-wrap intec-grid-a-v-stretch">
                        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-content-item' => true,
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1024-3' => $arVisual['COLUMNS'] >= 4,
                                        '768-2' => $arVisual['COLUMNS'] >= 3,
                                        '800-1' => $arVisual['COLUMNS'] <= 2,
                                        '500-1' => $arVisual['COLUMNS'] >= 3
                                    ]
                                ], true)
                            ]) ?>
                                <?php $vItem($arItem) ?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
    <?php if ($arVisual['SLIDER']['USE'])
        include(__DIR__.'/parts/script.php');
    ?>
</div>
