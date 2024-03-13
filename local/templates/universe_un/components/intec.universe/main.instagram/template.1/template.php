<?php if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\bitrix\Component;
use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 */


$this->setFrameMode(true);

if (empty($arResult['ITEMS']))
    return;

$sTemplateId = Html::getUniqueId(null, Component::getUniqueId($this));

$arVisual = $arResult['VISUAL'];

?>
<div class="widget c-instagram c-instagram-template-1" id="<?= $sTemplateId ?>">
    <?php if (!$arVisual['ITEMS']['WIDE']) { ?>
        <div class="widget-wrapper intec-content">
            <div class="widget-wrapper-2 intec-content-wrapper">
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
                                        'target' => $arVisual['ITEMS']['BLANK'] ? '_blank' : null,
                                        'href' => $arVisual['FOOTER']['LINK']
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
        <?= Html::beginTag('div', [
            'class' => [
                'widget-elements',
                'intec-grid' => [
                    '',
                    'wrap',
                    $arVisual['ITEMS']['PADDING'] ? 'i-15' : null
                ],
            ]
        ]) ?>
            <?php foreach ($arResult['ITEMS'] as $arItem) {

                $sImage = null;
                $bVideo = $arItem['VIDEO']['IS'];
                $bVideo ? $sImage = $arItem['VIDEO']['IMAGES'] : $sImage = $arItem['IMAGES'];
                $sDescription = ArrayHelper::getValue($arItem, 'DESCRIPTION');
                $sLink = ArrayHelper::getValue($arItem, 'LINK');

                ?>
                <?= Html::beginTag('div', [
                    'class' => [
                        Html::cssClassFromArray([
                            'widget-element' => true,
                            'intec-grid-item' => [
                                $arVisual['ITEMS']['COUNT']['DESKTOP'] => true,
                                '425-1' => $arVisual['ITEMS']['COUNT']['MOBILE'] == 1,
                                '600-2' => $arVisual['ITEMS']['COUNT']['DESKTOP'] >= 3,
                                '800-3' => $arVisual['ITEMS']['COUNT']['DESKTOP'] >= 4,
                                '1000-4' => $arVisual['ITEMS']['COUNT']['DESKTOP'] >= 5
                            ]
                        ], true)
                    ]
                ]) ?>
                    <div class="widget-element-wrapper">
                        <?= Html::beginTag('a', [
                            'class' => [
                                'widget-element-image'
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
                        <div class="widget-element-description">
                            <?php if ($arVisual['ITEMS']['DESCRIPTION']) { ?>
                                <?= TruncateText($sDescription, '200') ?>
                            <?php } ?>
                        </div>
                        <?= Html::endTag('a') ?>
                    </div>

                <?= Html::endTag('div') ?>
            <?php } ?>
        <?= Html::endTag('div')?>
    </div>
    <?php if ($arVisual['FOOTER']['SHOW'] && !$arVisual['FOOTER']['ON_HEADER']) { ?>
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <?= Html::beginTag('div', [
                    'class' => Html::cssClassFromArray([
                        'widget-footer' => true,
                        'align-' . $arVisual['FOOTER']['POSITION'] => true,
                        'mobile' => $arVisual['HEADER']['SHOW'] && $arVisual['FOOTER']['SHOW']
                    ], true)
                ]) ?>
                    <a class="widget-footer-all intec-cl-border intec-cl-background-hover" href="<?= $arVisual['FOOTER']['LINK'] ?>">
                        <?= $arVisual['FOOTER']['TEXT'] ?>
                    </a>
                <?= Html::endTag('div') ?>
            </div>
        </div>
    <?php } ?>
    <?php if (!$arVisual['ITEMS']['WIDE']) { ?>
            </div>
        </div>
    <?php } ?>
</div>