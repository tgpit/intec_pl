<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;
use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */

$this->setFrameMode(true);

$bError = false;

if (empty($arResult['ITEMS']))
    $bError = true;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

$iCounter = 0;
$bAdditionally = false;

?>
<div class="widget c-instagram c-instagram-template-2" id="<?= $sTemplateId ?>">
    <?php if (!$arVisual['ITEMS']['WIDE']) { ?>
    <div class="widget-wrapper intec-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
    <?php } ?>
    <?php if ($bError) { ?>
        <div class="widget-error">
            <?= Loc::getMessage('C_MAIN_INSTAGRAM_TEMPLATE_2_ERROR'); ?>
        </div>
        <?php return; ?>
    <?php } ?>
    <?php if ($arVisual['HEADER']['SHOW'] || $arVisual['DESCRIPTION']['SHOW']) { ?>
        <div class="widget-header">
            <div class="intec-content">
                <div class="intec-content-wrapper">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arVisual['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arVisual['HEADER']['TEXT']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arVisual['HEADER']['POSITION'],
                                        $arVisual['FOOTER']['SHOW'] && $arVisual['FOOTER']['ON_HEADER'] ? 'widget-title-margin' : null,
                                        $arVisual['FOOTER']['SHOW'] ? 'widget-title-margin-mobile' : null
                                    ]
                                ]) ?>
                            </div>
                            <?php if ($arVisual['FOOTER']['SHOW']) { ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-all-container' => true,
                                        'mobile' => $arVisual['HEADER']['SHOW'] && $arVisual['FOOTER']['ON_HEADER'],
                                        'intec-grid-item' => [
                                            'auto' => $arVisual['HEADER']['SHOW'],
                                            '1' => !$arVisual['HEADER']['SHOW']
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag('a', [
                                        'class' => [
                                            'widget-all-button',
                                            'intec-cl-text-light-hover',
                                            'intec-cl-svg-path-fill-hover'
                                        ],
                                        'href' => $arVisual['FOOTER']['LINK'],
                                        'target' => $arVisual['ITEMS']['BLANK'] ? '_blank' : null,
                                    ])?>
                                        <?php if ($arVisual['FOOTER']['SHOW'] && $arVisual['FOOTER']['ON_HEADER']) { ?>
                                            <span><?= $arVisual['SVG']['INSTAGRAM_ICON'] ?></span>
                                            <span><?= $arVisual['FOOTER']['TEXT'] ?></span>
                                        <?php } ?>
                                        <i class="fal fa-angle-right"></i>
                                    <?= Html::endTag('a')?>
                                <?= Html::endTag('div') ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                            <div class="widget-description-container intec-grid-item-1">
                                <div class="widget-description align-<?= $arVisual['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arVisual['DESCRIPTION']['TEXT']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <div class="intec-grid">
            <?php if ($arVisual['ITEMS']['BIG']) {
                $arItem = ArrayHelper::getFirstValue($arResult['ITEMS']);
                $sImage = null;
                $bVideo = $arItem['VIDEO']['IS'];
                $bVideo ? $sImage = $arItem['VIDEO']['IMAGES'] : $sImage = $arItem['IMAGES'];

                $sDescription = ArrayHelper::getValue($arItem, 'DESCRIPTION');
                $sLink = ArrayHelper::getValue($arItem, 'LINK');
                ?>
                <div class="widget-items-first-big">
                    <div class="widget-item">
                        <div class="widget-item-wrapper">
                            <?= Html::beginTag('a', [
                                'class' => [
                                    'widget-item-image'
                                ],
                                'target' => $arVisual['ITEMS']['BLANK'] ? '_blank' : null,
                                'href' => $sLink,
                                'data' => [
                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sImage : null
                                ],
                                'style' => [
                                    'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sImage.'\')' : null
                                ]
                            ]) ?>
                                <?php if ($bVideo) { ?>
                                    <i class="fas fa-play"></i>
                                <?php } ?>
                                <?php if ($arVisual['ITEMS']['DESCRIPTION']['SHOW']) { ?>
                                    <div class="widget-item-fade scrollbar-inner">
                                        <?php if ($arVisual['ITEMS']['DATE']['SHOW']) { ?>
                                            <div class="widget-item-date">
                                                <?= $arItem['DATE']['FORMATTED'] ?>
                                            </div>
                                        <?php } ?>
                                        <div class="widget-item-description">
                                            <?php if ($arVisual['ITEMS']['DESCRIPTION']['CUT']) { ?>
                                                <?= TruncateText($sDescription, '200') ?>
                                            <?php } else { ?>
                                                <?= $sDescription ?>
                                            <?php } ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            <?= Html::endTag('a') ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-items intec-grid intec-grid-wrap">
                <?php $sRoleAdditionally = false; ?>
                <?php include(__DIR__.'/parts/items.php'); ?>
            </div>
        </div>
        <?php if ($arVisual['ITEMS']['BIG'] && $arVisual['ITEMS']['MORE']['SHOW'] && $arVisual['ITEMS']['MORE']['VIEW']['DESKTOP']) { ?>
            <div class="widget-items-more intec-grid intec-grid-wrap">
                <?php
                    $sRoleAdditionally = true;
                    $bAdditionally = true;
                ?>
                <?php include(__DIR__.'/parts/items.php'); ?>
            </div>
        <?php } ?>
    </div>
    <?php if ($arVisual['FOOTER']['SHOW'] && !$arVisual['FOOTER']['ON_HEADER']) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-footer' => true,
                'align-' . $arVisual['FOOTER']['POSITION'] => true,
                'mobile' => $arVisual['HEADER']['SHOW'] && $arVisual['FOOTER']['SHOW']
            ], true)
        ]) ?>
            <div class="intec-content">
                <div class="intec-content-wrapper">
                    <a class="widget-footer-all intec-cl-border intec-cl-background-hover" href="<?= $arVisual['FOOTER']['LINK'] ?>">
                        <?= $arVisual['FOOTER']['TEXT'] ?>
                    </a>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php if (!$arVisual['ITEMS']['WIDE']) { ?>
        </div>
    </div>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php'); ?>
</div>