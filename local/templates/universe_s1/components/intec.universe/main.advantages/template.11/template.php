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
$iCounter = 0;

?>

<?= Html::beginTag('div', [
    'class' => [
        'widget',
        'c-advantages',
        'c-advantages-template-11'
    ],
    'id' => $sTemplateId,
    'data' => [
        'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
        'original' => $arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? $arVisual['BACKGROUND']['PATH'] : null
    ],
    'style' => [
        'background-image' => !$arVisual['LAZYLOAD']['USE'] && !empty($arVisual['BACKGROUND']['PATH']) ? 'url(\''.$arVisual['BACKGROUND']['PATH'].'\')' : null,
        'background-color' => '#17171d'
    ]
]) ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <div class="intec-grid intec-grid-1024-wrap intec-grid-i-18">
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="intec-grid-item-auto intec-grid-item-shrink-1 intec-grid-item-1024-1">
                        <div class="widget-header">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title">
                                    <?= Html::encode($arBlocks['HEADER']['TEXT']) ?>
                                </div>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="widget-description">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="intec-grid-item intec-grid-item-1024-1">
                    <div class="widget-content">
                        <?= Html::beginTag('div', [
                            'class' => [
                                'intec-grid' => [
                                    '',
                                    'wrap',
                                    'a-v-stretch',
                                    'i-h-7',
                                    'i-v-8'
                                ]
                            ]
                        ]) ?>
                            <?php foreach ($arResult['ITEMS'] as $arItem) {
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

                                $iCounter++;
                            ?>
                                <?= Html::beginTag('div', [
                                    'id' => $sAreaId,
                                    'class' => Html::cssClassFromArray([
                                        'widget-item' => true,
                                        'intec-grid-item' => [
                                            $arVisual['COLUMNS'] => true,
                                            '768-2' => $arVisual['COLUMNS'] >= 3,
                                            '600-1' => $arVisual['COLUMNS'] >= 2
                                        ]
                                    ], true)
                                ]) ?>
                                    <div class="widget-item-wrapper">
                                        <?php if ($sPicture !== null) { ?>
                                            <?= Html::tag($sTag, '', [
                                                'class' => [
                                                    'widget-item-icon',
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
                                        <?php } else if ($arVisual['VIEW'] == 'number') { ?>
                                            <div class="widget-item-counter">
                                                <?= $iCounter ?>
                                            </div>
                                        <?php } ?>
                                        <?= Html::tag($sTag, $arItem['NAME'], [
                                            'class' => Html::cssClassFromArray([
                                                'widget-item-name' => true,
                                                'intec-cl-text-hover' => $sTag === 'a'
                                            ], true),
                                            'href' => $sTag === 'a' ? $arItem['DATA']['LINK'] : null
                                        ]) ?>
                                        <?php if (!empty($arItem['PREVIEW_TEXT']) && $arVisual['PREVIEW']['SHOW']) { ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                            </div>
                                        <?php } ?>
                                    </div>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?= Html::endTag('div') ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?= Html::endTag('div') ?>