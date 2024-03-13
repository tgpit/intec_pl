<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 * @var CMain $APPLICATION
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$itemsCount = count($arResult['ITEMS']);

if ($itemsCount > 8)
    $itemsGrid = 8;
else
    $itemsGrid = $itemsCount;

$currentPage = $APPLICATION->GetCurPage(false);

?>
<?php $oFrame = $this->createFrame()->begin() ?>
<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-panel',
        'c-panel-template-1'
    ],
    'id' => $sTemplateId,
    'data' => [
        'svg-mode' => $arVisual['SVG']['MODE']
    ]
]) ?>
    <div class="intec-content intec-content-primary">
        <div class="scrollbar scrollbar-inner" data-role="scrollbar">
            <div class="widget-body intec-grid intec-grid-a-v-start">
                <?php foreach ($arResult['ITEMS'] as $arItem) {

                    $sId = $sTemplateId.'_'.$arItem['ID'];
                    $sAreaId = $this->GetEditAreaId($sId);
                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                    $bActive = $arItem['DATA']['URL']['VALUE'] === $currentPage;

                    $sTag = $arItem['DATA']['URL']['USE'] ? 'a' : 'div';

                ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-item-container' => true,
                            'intec-grid-item' => [
                                $itemsGrid => true,
                                '700-7' => $itemsGrid >= 8,
                                '650-6' => $itemsGrid >= 7,
                                '600-5' => $itemsGrid >= 6,
                                '400-4' => $itemsGrid >= 5
                            ]
                        ], true),
                        'data' => [
                            'active' => $bActive ? 'true' : 'false',
                            'icon' => $arItem['DATA']['ICON']['SHOW'] ? 'true' : 'false'
                        ]
                    ]) ?>
                        <?= Html::beginTag($bActive ? 'div' : $sTag, [
                            'id' => $sAreaId,
                            'class' => 'widget-item',
                            'href' => !$bActive && $sTag === 'a' ? $arItem['DATA']['URL']['VALUE'] : null
                        ]) ?>
                            <?php if ($arItem['DATA']['COMPARE']) { ?>
                                <div class="widget-item-count-container">
                                    <div class="widget-item-count intec-cl-background" data-role="panel.compare" data-state="disabled"></div>
                                </div>
                            <?php } else if ($arItem['DATA']['BASKET']) { ?>
                                <div class="widget-item-count-container">
                                    <div class="widget-item-count intec-cl-background" data-role="panel.basket" data-state="disabled"></div>
                                </div>
                            <?php } else if ($arItem['DATA']['DELAY']) { ?>
                                <div class="widget-item-count-container">
                                    <div class="widget-item-count intec-cl-background" data-role="panel.delayed" data-state="disabled"></div>
                                </div>
                            <?php } ?>
                            <?php if ($arItem['DATA']['ICON']['SHOW']) { ?>
                                <?php if ($arItem['DATA']['ICON']['VALUE']['CONTENT_TYPE'] === 'image/svg+xml') { ?>
                                    <?= Html::tag('span', FileHelper::getFileData($_SERVER['DOCUMENT_ROOT'].$arItem['DATA']['ICON']['VALUE']['SRC']), [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-icon' => true,
                                            'intec-ui-picture' => true,
                                            'intec-cl-svg-path-'.$arVisual['SVG']['MODE'] => $bActive
                                        ], true)
                                    ]) ?>
                                <?php } else { ?>
                                    <?= Html::tag('span', null, [
                                        'class' => 'widget-item-icon',
                                        'style' => [
                                            'background-image' => 'url(\''.$arItem['DATA']['ICON']['VALUE']['SRC'].'\')'
                                        ]
                                    ]) ?>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arVisual['NAME']['SHOW']) { ?>
                                <?= Html::tag('div', $arItem['NAME'], [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item-name' => true,
                                        'intec-cl-text' => $bActive
                                    ], true)
                                ]) ?>
                            <?php } ?>
                        <?= Html::endTag($bActive ? 'div' : $sTag) ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
                <?php unset($arItem) ?>
            </div>
        </div>
    </div>
    <?php include(__DIR__.'/parts/script.php') ?>
<?= Html::endTag('div') ?>
<?php unset($itemsCount, $itemsGrid) ?>
<?php $oFrame->beginStub() ?>
<?php $oFrame->end() ?>
