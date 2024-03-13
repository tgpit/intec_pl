<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\Html;
use intec\core\helpers\JavaScript;
use intec\core\helpers\StringHelper;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$arPictureSize = [
    'width' => 200,
    'height' => 200
];
?>
<div class="widget c-staff c-staff-template-6" id="<?= $sTemplateId ?>">
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
                        'widget-content-wrapper'
                    ],
                    'data-picture-size' => $arVisual['PICTURE']['SIZE']
                ]) ?>
                    <div class="intec-content intec-content-visible">
                        <div class="intec-content-wrapper widget-content">
                            <div class="widget-items intec-grid intec-grid-wrap intec-grid-i-15">
                                <?php foreach ($arResult['ITEMS'] as $arItem) {

                                    $sId = $sTemplateId.'_'.$arItem['ID'];
                                    $sAreaId = $this->GetEditAreaId($sId);
                                    $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                    $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                    $sPicture = $arItem['PREVIEW_PICTURE'];

                                    if (empty($sPicture))
                                        $sPicture = $arItem['DETAIL_PICTURE'];

                                    if (!empty($sPicture)) {
                                        $sPicture = CFile::ResizeImageGet(
                                            $sPicture,
                                            $arPictureSize,
                                            BX_RESIZE_IMAGE_PROPORTIONAL
                                        );

                                        if (!empty($sPicture))
                                            $sPicture = $sPicture['src'];
                                    }

                                    if (empty($sPicture))
                                        $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                    $arData = $arItem['DATA'];

                                    $arForm['PARAMETERS']['fields'][$arForm['FIELD']] = $arItem['NAME'];
                                    ?>
                                    <?= Html::beginTag('div', [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item' => true,
                                            'intec-grid-item' => [
                                                $arVisual['COLUMNS'] => true,
                                                '1000-3' => $arVisual['COLUMNS'] > 3,
                                                '768-2' => $arVisual['COLUMNS'] >= 3,
                                                '1000-2' => $arVisual['COLUMNS'] < 3,
                                                '500-1' => true
                                            ]
                                        ], true)
                                    ])?>
                                        <div class="widget-item-wrapper" id="<?= $sAreaId ?>">
                                            <?= Html::beginTag('div', [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-wrapper-2' => true,
                                                    'intec-grid' => [
                                                        '' => true,
                                                        'wrap' => true,
                                                        'a-v-end' => $arVisual['COLUMNS'] > 2 && $arVisual['PREVIEW']['SHOW'] ? false : true
                                                    ]
                                                ], true)
                                            ]) ?>
                                                <div class="widget-item-picture-wrap intec-grid-item-auto intec-grid-item-500-1">
                                                    <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', '', [
                                                        'class' => [
                                                            'widget-item-picture',
                                                            'intec-image-effect'
                                                        ],
                                                        'data' => [
                                                            'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                            'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                        ],
                                                        'style' => [
                                                            'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                                        ],
                                                        'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                                    ]) ?>
                                                </div>
                                                <div class="intec-grid-item-768-1 ">
                                                    <div class="widget-item-information">
                                                        <?php if ($arVisual['POSITION']['SHOW'] && !empty($arData['POSITION']['VALUE'])) { ?>
                                                            <div class="widget-item-position">
                                                                <?= $arData['POSITION']['VALUE'] ?>
                                                            </div>
                                                        <?php } ?>
                                                        <?= Html::tag($arVisual['LINK']['USE'] ? 'a' : 'div', $arItem['NAME'], [
                                                            'class' => [
                                                                'widget-item-name',
                                                                $arVisual['LINK']['USE'] ? 'intec-cl-text-hover' : null
                                                            ],
                                                            'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                                        ]) ?>
                                                        <?php if ($arVisual['PREVIEW']['SHOW'] && !empty($arItem['PREVIEW_TEXT'])) { ?>
                                                            <div class="widget-item-description">
                                                                <?= $arItem['PREVIEW_TEXT'] ?>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            <?= Html::endTag('div') ?>
                                        </div>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    </div>
</div>