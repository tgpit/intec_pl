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
$iCount = 0;

?>
<div class="widget c-advantages c-advantages-template-3" id="<?= $sTemplateId ?>">
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
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'intec-grid' => [
                                '' => true,
                                'wrap' => true,
                                'i-v-16' => $arVisual['INDENT']['USE']
                            ]
                        ], true)
                    ]) ?>
                        <?php foreach ($arResult['ITEMS'] as $arItem) {
                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sTag = ($arVisual['LINK']['USE'] && !empty($arItem['DATA']['LINK']) && $arItem['DATA']['LINK'] !== '/') ? 'a' : 'div';
                            $sPicture = $arItem['PREVIEW_PICTURE'];

                            if (empty($sPicture))
                                $sPicture = $arItem['DETAIL_PICTURE'];

                            if (!empty($sPicture)) {
                                $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 700,
                                    'height' => 700
                                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                if (!empty($sPicture))
                                    $sPicture = $sPicture['src'];
                            }

                            if (empty($sPicture))
                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                            $iCount++;
                            $bLeft = $iCount % 2 != 1;

                        ?>
                            <div class="intec-grid-item-1">
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item' => [
                                            '' => true,
                                            'underlined' => $arVisual['INDENT']['USE']
                                        ]
                                    ], true),
                                    'id' => $sAreaId
                                ]) ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'intec-grid' => [
                                                '' => true,
                                                'wrap' => true,
                                                'a-v-center' => true,
                                                'o-horizontal-reverse' => $bLeft
                                            ]
                                        ], true)
                                    ]) ?>
                                        <div class="intec-grid-item-2 intec-grid-item-1024-1">
                                            <div class="widget-item-content">
                                                <?= Html::tag($sTag, $arItem['NAME'], [
                                                    'class' => Html::cssClassFromArray([
                                                        'widget-item-name' => true,
                                                        'intec-cl-text-hover' => $sTag === 'a'
                                                    ], true),
                                                    'href' => ($sTag === 'a') ? $arItem['DATA']['LINK'] : null
                                                ]) ?>
                                                <?php if (!empty($arItem['PREVIEW_TEXT'])) { ?>
                                                    <div class="widget-item-description">
                                                        <?= $arItem['PREVIEW_TEXT'] ?>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="intec-grid-item-2 intec-grid-item-1024-1">
                                            <?= Html::tag($sTag, '', [
                                                'class' => [
                                                    'widget-item-image',
                                                    'intec-image-effect'
                                                ],
                                                'style' => [
                                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null,
                                                    'background-size' => $arVisual['BACKGROUND']['SIZE']
                                                ],
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                ],
                                                'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                            ]) ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?= Html::endTag('div') ?>
                            </div>
                        <?php } ?>



                    <?= Html::endTag('div') ?>



                </div>
            </div>
        </div>
    </div>
</div>