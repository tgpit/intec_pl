<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\FileHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var CBitrixComponentTemplate $this
 */

$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arBlocks = $arResult['BLOCKS'];
$arVisual = $arResult['VISUAL'];

$sSvg = FileHelper::getFileData(__DIR__.'/svg/item.decoration.svg');

?>
<div class="widget c-videos c-videos-template-3" id="<?= $sTemplateId ?>">
    <div class="intec-content intec-content-visible">
        <div class="intec-content-wrapper">
            <?= Html::beginTag('div', [
                'class' => Html::cssClassFromArray([
                    'widget-use-slider' => $arVisual['SLIDER']['USE'] && $arVisual['SLIDER']['NAV'],
                    'widget-no-header' => !$arBlocks['HEADER']['SHOW'] && !$arBlocks['DESCRIPTION']['SHOW'],
                ],true)
            ])?>
                <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
                    <div class="widget-header">
                        <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                            <?php if ($arBlocks['HEADER']['SHOW']) { ?>
                                <div class="widget-title-container intec-grid-item">
                                    <?= Html::tag('div', Html::encode($arBlocks['HEADER']['TEXT']), [
                                        'class' => [
                                            'widget-title',
                                            'align-'.$arBlocks['HEADER']['POSITION'],
                                            $arBlocks['FOOTER']['BUTTON']['SHOW'] ? 'widget-title-margin' : null
                                        ]
                                    ]) ?>
                                </div>
                                <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
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
                                            'href' => $arBlocks['FOOTER']['BUTTON']['LINK']
                                        ])?>
                                            <i class="fal fa-angle-right"></i>
                                        <?= Html::endTag('a')?>
                                    <?= Html::endTag('div') ?>
                                <?php } ?>
                            <?php } ?>
                            <?php if ($arBlocks['DESCRIPTION']['SHOW']) { ?>
                                <div class="intec-grid-item-1">
                                    <div class="widget-description align-<?= $arBlocks['DESCRIPTION']['POSITION'] ?>">
                                        <?= Html::encode($arBlocks['DESCRIPTION']['TEXT']) ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                <?php } ?>
                <div class="widget-content">
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-items' => true,
                            'owl-carousel' => $arVisual['SLIDER']['USE'],
                            'intec-grid' => [
                                '' => true,
                                'wrap' => true,
                                'i-h-5' => true,
                                'i-v-12' => true
                            ]
                        ], true),
                        'data-role' => 'items'
                    ]) ?>
                        <?php foreach ($arResult['ITEMS'] as $arItem) {

                            $sId = $sTemplateId.'_'.$arItem['ID'];
                            $sAreaId = $this->GetEditAreaId($sId);
                            $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                            $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                            $sPicture = $arItem['PICTURE'];

                            if ($sPicture['SOURCE'] === 'detail' || $sPicture['SOURCE'] === 'preview') {
                                $sPicture = CFile::ResizeImageGet($sPicture, [
                                    'width' => 500,
                                    'height' => 500
                                ], BX_RESIZE_IMAGE_PROPORTIONAL_ALT);

                                if (!empty($sPicture))
                                    $sPicture = $sPicture['src'];
                            } else if ($sPicture['SOURCE'] === 'service')
                                $sPicture = $sPicture['SRC'];
                            else
                                $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                        ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-item' => true,
                                    'intec-grid-item' => [
                                        $arVisual['COLUMNS'] => true,
                                        '1200-3' => $arVisual['COLUMNS'] >= 4,
                                        '768-2' => true,
                                        '500-1' => true
                                    ]
                                ], true)
                            ]) ?>
                                <div class="widget-item-body" id="<?= $sAreaId ?>">
                                    <?= Html::tag('div', $sSvg, [
                                        'class' => 'widget-item-picture',
                                        'title' => $arItem['NAME'],
                                        'data-role' => 'item',
                                        'data-lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                        'data-original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null,
                                        'data-src' => !empty($arItem['URL']) ? $arItem['URL']['embed'] : null,
                                        'style' => [
                                            'background-image' => $arVisual['LAZYLOAD']['USE'] ? $arVisual['LAZYLOAD']['STUB'] :'url(\''.$sPicture.'\')'
                                        ]
                                    ]) ?>
                                    <?php if ($arVisual['NAME']['SHOW']) { ?>
                                        <div class="widget-item-name">
                                            <?= $arItem['NAME'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php if ($arVisual['SLIDER']['NAV']) { ?>
                        <?= Html::tag('div','', [
                            'class'=>'widget-items-navigation',
                            'data'=>[
                                'role'=>'slider.navigation'
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
                <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
                    <?= Html::beginTag('div', [
                        'class' => Html::cssClassFromArray([
                            'widget-footer' => true,
                            'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                            'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
                        ], true)
                    ]) ?>
                        <?php if ($arBlocks['FOOTER']['BUTTON']['SHOW']) { ?>
                            <?= Html::tag('a', $arBlocks['FOOTER']['BUTTON']['TEXT'], [
                                'href' => $arBlocks['FOOTER']['BUTTON']['LINK'],
                                'class' => [
                                    'widget-footer-button',
                                    'intec-ui' => [
                                        '',
                                        'size-5',
                                        'scheme-current',
                                        'control-button',
                                        'mod' => [
                                            'transparent',
                                            'round-half'
                                        ]
                                    ]
                                ]
                            ]) ?>
                        <?php } ?>
                    <?= Html::endTag('div') ?>
                <?php } ?>
            <?= Html::endTag('div') ?>
        </div>
    </div>
    <?php if (!defined('EDITOR'))
        include(__DIR__.'/parts/script.php');
    ?>
</div>
