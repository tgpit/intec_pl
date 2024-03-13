<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die(); ?>
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

<div class="widget c-advantages c-advantages-template-2" id="<?= $sTemplateId ?>">
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
                <?= Html::beginTag('div', [
                    'class' => [
                        'intec-grid' => [
                            '',
                            'wrap',
                            'a-v-start',
                            'a-h-center',
                            'i-h-25',
                            'i-v-15'
                        ]
                    ]
                ]) ?>
                    <?php foreach ($arResult ['ITEMS'] as $arItem) {
                        $sId = $sTemplateId.'_'.$arItem['ID'];
                        $sAreaId = $this->GetEditAreaId($sId);
                        $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                        $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                        $sTag = ($arVisual['LINK']['USE'] && !empty($arItem['DATA']['LINK']) && $arItem['DATA']['LINK'] !== '/')? 'a' : 'div';
                        $sPicture = null;

                        if ($arVisual['VIEW'] === 'icon') {
                            $sPicture = $arItem['PREVIEW_PICTURE'];

                            if (empty($sPicture))
                                $sPicture = $arItem['DETAIL_PICTURE'];

                            if (!empty($sPicture)) {
                                $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 160,
                                    'height' => 160
                                ], BX_RESIZE_IMAGE_PROPORTIONAL);

                                if (!empty($sPicture))
                                    $sPicture = $sPicture['src'];
                            }

                            if (empty($sPicture))
                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';
                        }

                        $iCount++;

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1150-4' => $arVisual['COLUMNS'] >= 5,
                                    '900-3' => $arVisual['COLUMNS'] >= 4,
                                    '750-2' => $arVisual['COLUMNS'] >= 3,
                                    '500-1' => $arVisual['COLUMNS'] >= 2
                                ]
                            ], true)
                        ]) ?>
                            <div class="widget-element" id="<?= $sAreaId ?>">
                                <?php if ($sPicture !== null) { ?>
                                    <?= Html::tag($sTag, '', [
                                        'class' => [
                                            'widget-element-icon',
                                            'intec-image-effect'
                                        ],
                                        'data' => [
                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                        ],
                                        'style' => [
                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                        ],
                                        'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                    ]) ?>
                                <?php } else { ?>
                                    <div class="widget-element-counter">
                                        <?= $iCount.'.' ?>
                                    </div>
                                <?php } ?>
                                <?= Html::tag($sTag, $arItem['NAME'], [
                                    'class' => Html::cssClassFromArray([
                                        'widget-element-name' => true,
                                        'intec-cl-text-hover' => $sTag === 'a'
                                    ], true),
                                    'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                ]) ?>
                                <div class="widget-element-description">
                                    <?= strip_tags($arItem['PREVIEW_TEXT']) ?>
                                </div>
                            </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>