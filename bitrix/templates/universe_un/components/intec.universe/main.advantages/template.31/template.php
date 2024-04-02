<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

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

$iCounter = 0;
?>
<?= Html::beginTag('div', [
    'id' => $sTemplateId,
    'class' => 'widget c-advantages c-advantages-template-31',
    'data' => [
        'theme' => $arVisual['THEME'],
        'number-align' => $arVisual['NUMBER']['ALIGN'],
        'preview-align' => $arVisual['PREVIEW']['ALIGN']
    ],
    'style' => $arVisual['BACKGROUND']['SHOW'] ? 'background: '.$arVisual['BACKGROUND']['COLOR'] : null
]) ?>
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => [
                    Html::cssClassFromArray([
                        'widget-wrapper' => true
                    ], true)
                ]
            ]) ?>
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
                    <div class="widget-items intec-grid intec-grid-wrap">
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $arData = $arItem['DATA'];
                        ?>
                            <?= Html::beginTag('div',[
                                'id' => $sAreaId,
                                'class' => [
                                    Html::cssClassFromArray([
                                        'widget-item' => true,
                                        'intec-grid-item' => [
                                            $arVisual['COLUMNS']['DESKTOP'] => true,
                                            '500-1' => $arVisual['COLUMNS']['MOBILE'] == 1,
                                            '768-2' => $arVisual['COLUMNS']['DESKTOP'] >= 2,
                                            '900-3' => $arVisual['COLUMNS']['DESKTOP'] >= 3,
                                            '1000-4' => $arVisual['COLUMNS']['DESKTOP'] >= 4
                                        ],
                                    ], true)
                                ]
                            ]) ?>
                                <div class="widget-item-wrapper">
                                    <div class="widget-item-wrapper-2">
                                        <?php if (!empty($arData['NUMBER']) && $arVisual['NUMBER']['SHOW']) { ?>
                                            <?= Html::beginTag('div',[
                                                'class' => [
                                                    Html::cssClassFromArray([
                                                        'widget-item-number' => true,
                                                        'intec-cl-text' => $arVisual['THEME'] == 'light',
                                                    ], true)
                                                ]
                                            ]) ?>
                                                <?= $arData['NUMBER'] ?>
                                            <?= Html::endTag('div') ?>
                                        <?php } ?>
                                        <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    </div>
                </div>
            <?= Html::endTag('div') ?>
        </div>
    </div>
<?= Html::endTag('div') ?>