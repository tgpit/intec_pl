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

$iCounter = 0;
?>
<div class="widget c-advantages c-advantages-template-36" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper">
        <div class="widget-wrapper-2">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-content">
                        <div class="intec-content-wrapper">
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
                <?= Html::beginTag('div', [
                    'class' => [
                        'widget-items'
                    ]
                ]) ?>
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sPicture = $arItem['PREVIEW_PICTURE'];

                        if (empty($sPicture))
                            $sPicture = $arItem['DETAIL_PICTURE'];

                        if (!empty($sPicture)) {
                            $sPicture = CFile::ResizeImageGet($sPicture, [
                                'width' => 1000,
                                'height' => 1000
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        $iCounter++;
                    ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-item',
                            ],
                            'id' => $sAreaId
                        ]) ?>
                            <div class="widget-item-wrapper">
                                <?php if ($arVisual['PICTURE']['SHOW']) { ?>
                                    <?= Html::tag('div', '', [
                                        'class' => 'widget-item-picture',
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ]
                                    ]) ?>
                                <?php } ?>
                                <div class="intec-content">
                                    <div class="intec-content-wrapper">
                                        <?= Html::beginTag('div', [
                                            'class' => Html::cssClassFromArray([
                                                'intec-grid' => [
                                                    '' => true,
                                                    'wrap' => true,
                                                    'o-horizontal-reverse' => $iCounter % 2 == 0
                                                ]
                                            ], true)
                                        ]) ?>
                                            <div class="intec-grid-item"></div>
                                            <div class="intec-grid-item-auto intec-grid-item-768-1 widget-item-text-wrap">
                                                <div class="widget-item-text">
                                                    <div class="widget-item-name">
                                                        <?= $arItem['NAME'] ?>
                                                    </div>
                                                    <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                                        <div class="widget-item-description">
                                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                            </div>
                                        <?= Html::endTag('div') ?>
                                    </div>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>