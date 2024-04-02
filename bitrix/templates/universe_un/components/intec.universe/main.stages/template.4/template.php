<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use intec\core\bitrix\Component;
use intec\core\helpers\Html;

/**
 * @var array $arParams
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$iCount = 0;
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => [
        'widget',
        'c-stages',
        'c-stages-template-4'
    ]
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                        <div class="widget-title align-<?= $arBlocks['HEADER']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                        </div>
                    <?php } ?>
                    <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                        <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                            <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="widget-items" data-role="items">
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $iCount++;

                        ?>
                        <div id="<?= $sAreaId ?>" class="widget-item" data-role="item" data-expanded="false">
                            <div class="widget-item-title" data-role="item.title">
                                <div class="widget-item-title-wrapper">
                                    <span class="widget-item-title-text">
                                        <?= $iCount.'. '.$arItem['NAME'] ?>
                                    </span>
                                    <span class="widget-item-title-icon"></span>
                                </div>
                            </div>
                            <div class="widget-item-description" data-role="item.content">
                                <div class="widget-item-description-wrapper">
                                    <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                    <?php } else { ?>
                                        <?= $arItem['DETAIL_TEXT'] ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <?php unset($iCount) ?>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>