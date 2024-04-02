<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die() ?>
<?php

use Bitrix\Main\Localization\Loc;
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
<div class="widget c-staff c-staff-template-2" id="<?= $sTemplateId ?>">
    <div class="widget-wrapper intec-content intec-content-visible">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW'] || $arVisual['BUTTON']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arBlocks['HEADER']['POSITION'],
                                        $arVisual['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['BUTTON']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-all-container' => true,
                                    'mobile' => $arBlocks['HEADER']['SHOW'],
                                    'intec-grid-item' => [
                                        'auto' => $arBlocks['HEADER']['SHOW'],
                                        '1' => !$arBlocks['HEADER']['SHOW']
                                    ]
                                ], true)
                            ]) ?>
                                <?= Html::beginTag('a', [
                                    'class' => [
                                        'widget-all-button',
                                        'intec-cl-text-light-hover',
                                    ],
                                    'href' => $arVisual['BUTTON']['LINK']
                                ])?>
                                    <span><?= $arVisual['BUTTON']['TEXT'] ?></span>
                                    <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                            <div class="widget-description-container intec-grid-item-1">
                                <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-content' => true,
                    'intec-grid' => [
                        '' => !$arVisual['SLIDER']['USE'],
                        'wrap' => !$arVisual['SLIDER']['USE'],
                        'a-v-stretch' => !$arVisual['SLIDER']['USE'],
                        'a-h-center' => !$arVisual['SLIDER']['USE']
                    ]
                ], true)
            ]) ?>
                <?php if ($arVisual['SLIDER']['USE']) { ?>
                    <div class="owl-carousel" data-role="slider">
                <?php } ?>
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
                                'width' => 350,
                                'height' => 350
                            ], BX_RESIZE_IMAGE_PROPORTIONAL);

                            if (!empty($sPicture))
                                $sPicture = $sPicture['src'];
                        }

                        if (empty($sPicture))
                            $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                    ?>
                        <?= Html::beginTag('div', [
                            'class' => Html::cssClassFromArray([
                                'widget-element' => true,
                                'intec-grid-item' => [
                                    $arVisual['COLUMNS'] => true,
                                    '1000-4' => $arVisual['COLUMNS'] >= 5,
                                    '800-3' => $arVisual['COLUMNS'] >= 4,
                                    '600-2' => $arVisual['COLUMNS'] >= 3,
                                    '400-1' => true
                                ]
                            ], true)
                        ]) ?>
                            <?= Html::beginTag($arVisual['LINK']['USE'] ? 'a' : 'div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-element-wrapper' => true,
                                    'intec-grid' => [
                                        '' => true,
                                        'o-vertical' => true
                                    ]
                                ], true),
                                'id' => $sAreaId,
                                'href' => $arVisual['LINK']['USE'] ? $arItem['DETAIL_PAGE_URL'] : null,
                            ])?>

                                    <div class="intec-grid-item-auto widget-element-image-wrap" >
                                        <?= Html::tag('div', '', [
                                            'class' => [
                                                'widget-element-image',
                                                'intec-image-effect'
                                            ],
                                            'data' => [
                                                'role' => 'image',
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]) ?>
                                    </div>
                                    <?php if ($arVisual['ELEMENT']['POSITION']['SHOW'] && !empty($arItem['DATA']['POSITION'])) { ?>
                                        <?= Html::tag('div', $arItem['DATA']['POSITION'], [
                                            'class'=> [
                                                'intec-grid-item-auto',
                                                'widget-element-position'
                                            ]
                                        ]) ?>
                                    <?php } ?>
                                    <?= Html::tag('div', $arItem['NAME'], [
                                        'class'=> Html::cssClassFromArray([
                                            'intec-grid-item-auto' => true,
                                            'widget-element-name' => true,
                                            'intec-cl-text-hover' => $arVisual['LINK']['USE']
                                        ], true)
                                    ]) ?>

                            <?= Html::endTag($arVisual['LINK']['USE'] ? 'a' : 'div') ?>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                <?php if ($arVisual['SLIDER']['USE']) { ?>
                    </div>
                <?php } ?>
                <?php if ($arVisual['SLIDER']['NAV']) { ?>
                    <?= Html::tag('div','', [
                        'class'=>'widget-items-navigation',
                        'data'=>[
                            'role'=>'slider.navigation'
                        ]
                    ]) ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php include(__DIR__ . '/parts/script.php') ?>
</div>