<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

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
<div class="widget c-advantages c-advantages-template-1" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
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
                <div class="widget-items">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-start intec-grid-i-h-20 intec-grid-i-v-10">
                        <?php foreach ($arResult['ITEMS'] as $arItem) {
                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sTag = ($arVisual['LINK']['USE'] && !empty($arItem['DATA']['LINK']) && $arItem['DATA']['LINK'] !== '/')? 'a' : 'div';
                            $sPicture = !empty($arItem['PREVIEW_PICTURE']) ? $arItem['PREVIEW_PICTURE']['SRC'] : null;

                            if (!empty($arItem['PREVIEW_PICTURE'])) {
                                $sPicture = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], [
                                    'width' => 80,
                                    'height' => 80
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($sPicture)) {
                                    $sPicture = $sPicture['src'];
                                } else {
                                    $sPicture = null;
                                }
                            }
                        ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1024-3' => $arVisual['COLUMNS'] > 2,
                                        '768-2' => true,
                                        '500-1' => true
                                    ]
                                ], true)
                            ]) ?>
                                <div class="widget-item" id="<?= $sAreaId ?>">
                                    <div class="intec-grid intec-grid-nowrap intec-grid-a-v-start intec-grid-i-h-8">
                                        <?php if (!empty($sPicture)) { ?>
                                            <div class="intec-grid-item-auto">
                                                <?= Html::beginTag($sTag, [
                                                    'class' => [
                                                        'widget-item-picture',
                                                        'intec-ui-picture',
                                                        'intec-image-effect'
                                                    ],
                                                    'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                                ]) ?>
                                                    <?= Html::img($arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] : $sPicture, [
                                                        'loading' => 'lazy',
                                                        'alt' => $arItem['NAME'],
                                                        'title' => $arItem['NAME'],
                                                        'data' => [
                                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                        ]
                                                    ]) ?>
                                                <?= Html::endTag($sTag) ?>
                                            </div>
                                        <?php } ?>
                                        <div class="intec-grid-item">
                                            <?= Html::beginTag($sTag, [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-name' => true,
                                                    'intec-cl-text-hover' => $sTag === 'a'
                                                ], true),
                                                'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                            ]) ?>
                                                <?= $arItem['NAME'] ?>
                                                <? if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                                    <div class="widget-item-description">
                                                        <?= Html::encode($arItem['PREVIEW_TEXT']) ?>
                                                    </div>
                                                <? } ?>
                                            <?= Html::endTag($sTag) ?>
                                        </div>
                                    </div>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                     </div>
                 </div>
            </div>
         </div>
    </div>
</div>