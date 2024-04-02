<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['SECTIONS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$vRenderMenu = include(__DIR__.'/parts/menu.php');
$vRenderSections = include(__DIR__.'/parts/sections.php');

?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-services',
        'c-services-template-19'
    ]
]) ?>
    <div class="intec-content">
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
                <div class="intec-grid intec-grid-wrap intec-grid-i-24">
                    <div class="intec-grid-item-4 intec-grid-item-768-1">
                        <?php $vRenderMenu($arResult['SECTIONS']) ?>
                    </div>
                    <div class="intec-grid-item intec-grid-item-768-1">
                        <?php $vRenderSections($arResult['SECTIONS']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
