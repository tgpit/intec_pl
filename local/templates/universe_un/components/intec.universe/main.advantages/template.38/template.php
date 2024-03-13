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

?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-advantages',
        'c-advantages-template-38'
    ],
    'id' => $sTemplateId
]) ?>
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
        <div class="widget-header intec-content">
            <div class="widget-header-wrapper intec-content-wrapper">
                <div class="widget-header-wrapper-2">
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
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <div class="widget-control-tabs">
            <div class="intec-content widget-control-tabs-wrapper">
                <div class="intec-content-wrapper widget-control-tabs-wrapper-2">
                    <ul class="intec-ui intec-ui-control-tabs intec-ui-scheme-current intec-ui-view-1" data-ui-control="tabs">
                        <?php $bActive = true; ?>
                        <?php foreach ($arResult['ITEMS'] as $arItem) { ?>
                        <?php
                            $sId = $sTemplateId.'_'.$arItem['ID'];
                        ?>
                            <?= Html::beginTag('li', [
                                'class' => [
                                    'widget-item-name-wrap',
                                    'intec-ui-part-tab'
                                ],
                                'data' => [
                                    'active' => $bActive ? 'true' : 'false'
                                ]
                            ]) ?>
                                <?= Html::tag('a', $arItem['NAME'], [
                                    'href' => '#'.$sId,
                                    'class' => [
                                        'widget-item-name'
                                    ],
                                    'data' => [
                                        'type' => 'tab'
                                    ]
                                ]) ?>
                            <?= Html::endTag('li') ?>
                            <?php $bActive = false; ?>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </div>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="intec-ui intec-ui-control-tabs-content">
                    <?php $bActive = true; ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sDescription = $arItem['DETAIL_TEXT'];

                        if (empty($sDescription))
                            $sDescription = $arItem['PREVIEW_TEXT'];

                    ?>
                        <?= Html::beginTag('div', [
                            'id' => $sId,
                            'class' => 'intec-ui-part-tab',
                            'data' => [
                                'active' => $bActive ? 'true' : 'false'
                            ]
                        ]) ?>
                        <div class="widget-item-description" id="<?= $sAreaId ?>">
                            <?= $sDescription ?>
                        </div>
                        <?= Html::endTag('div') ?>
                        <?php $bActive = false; ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>