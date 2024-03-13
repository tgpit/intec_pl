<?php if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use intec\core\helpers\ArrayHelper;
use intec\core\helpers\Html;

/**
 * @var array $arResult
 * @var array $arParams
 */

$this->setFrameMode(true);

$arVisual = $arResult['VISUAL'];

$bFirstBig = $arVisual['ELEMENT']['FIRST_BIG'];

?>
<div class="widget c-articles c-articles-template-1">
    <div class="widget-wrapper intec-content intec-content-visible widget-articles-content">
        <div class="widget-wrapper-2 intec-content-wrapper">
            <?php if ($arVisual['HEADER']['SHOW'] || $arVisual['DESCRIPTION']['SHOW'] || $arVisual['SEE_ALL']['SHOW']) { ?>
                <div class="widget-header">
                    <div class="intec-grid intec-grid-wrap intec-grid-a-v-center intec-grid-i-8">
                        <?php if ($arVisual['HEADER']['SHOW']) { ?>
                            <div class="widget-title-container intec-grid-item">
                                <?= Html::tag('div', Html::encode($arVisual['HEADER']['VALUE']), [
                                    'class' => [
                                        'widget-title',
                                        'align-'.$arVisual['HEADER']['POSITION'],
                                        $arVisual['SEE_ALL']['SHOW'] ? 'widget-title-margin' : null
                                    ]
                                ]) ?>
                            </div>
                        <?php } ?>
                        <?php if ($arVisual['SEE_ALL']['SHOW']) { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'widget-all-container' => true,
                                    'align-'.$arVisual['SEE_ALL']['POSITION'] => true,
                                    'mobile' => $arVisual['HEADER']['SHOW'],
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
                                    ],
                                    'href' => $arVisual['SEE_ALL']['URL']
                                ])?>
                                    <span><?= $arVisual['SEE_ALL']['TEXT'] ?></span>
                                    <i class="fal fa-angle-right"></i>
                                <?= Html::endTag('a')?>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                        <?php if ($arVisual['DESCRIPTION']['SHOW']) { ?>
                            <div class="widget-description-container intec-grid-item-1">
                                <div class="widget-description align-<?= $arVisual['DESCRIPTION']['POSITION'] ?>">
                                    <?= Html::encode($arVisual['DESCRIPTION']['VALUE']) ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            <div class="widget-content">
                <div class="intec-grid intec-grid-wrap intec-grid-a-v-stratch intec-grid-i-10">
                    <?php foreach ($arResult['ITEMS'] as $arItem) {

                        if ($arParams['HIDDE_NON_ACTIVE'] && $arItem['ACTIVE'] === 'N')
                            continue;

                        $header = ArrayHelper::getValue($arItem, 'NAME');
                        $description = ArrayHelper::getValue($arItem, 'PREVIEW_TEXT');
                        $bShowDescription = $bElementDescriptionShow && !empty($description);

                        $sPicture = ArrayHelper::getValue($arItem, ['PREVIEW_PICTURE', 'SRC']);
                    ?>
                        <?php if ($bFirstBig) { ?>
                            <div class="intec-grid-item-2 intec-grid-item-900-1">
                                <div class="widget-element element-big">
                                    <?= Html::tag('a', null, [
                                            'class' => [
                                                'picture'
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL'],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]
                                    ) ?>
                                    <div class="fade-bg"></div>
                                    <div class="text-wrapper">
                                        <?php if ($arVisual['ELEMENT']['HEADER']) { ?>
                                            <span class="header">
                                                <?= $arItem['NAME'] ?>
                                            </span>
                                        <?php } ?>
                                        <?php if ($arVisual['ELEMENT']['DESCRIPTION'] ) { ?>
                                            <span class="description">
                                                <?= $description ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                            <?php $bFirstBig = false ?>
                        <?php } else { ?>
                            <?= Html::beginTag('div', [
                                'class' => Html::cssClassFromArray([
                                    'intec-grid-item' => [
                                        '4' => true,
                                        '900-3' => !$arVisual['ELEMENT']['FIRST_BIG'],
                                        '900-2' => $arVisual['ELEMENT']['FIRST_BIG'],
                                        '650-2' => !$arVisual['ELEMENT']['FIRST_BIG'],
                                        '450-1' => true
                                    ]
                                ], true)
                            ])?>
                                <div class="widget-element">
                                    <?= Html::tag('a', '', [
                                            'class' => [
                                                'picture',
                                                'intec-image-effect'
                                            ],
                                            'href' => $arItem['DETAIL_PAGE_URL'],
                                            'data' => [
                                                'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                            ],
                                            'style' => [
                                                'background-image' => !$arVisual['LAZYLOAD']['USE'] ? 'url(\''.$sPicture.'\')' : null
                                            ]
                                        ]
                                    ) ?>
                                    <?php if ($arVisual['ELEMENT']['HEADER']) { ?>
                                        <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="header intec-cl-text-hover">
                                            <span><?= $arItem['NAME'] ?></span>
                                        </a>
                                    <?php } ?>
                                    <?php if ($arVisual['ELEMENT']['DESCRIPTION']) { ?>
                                        <div class="description">
                                            <?= $arItem['PREVIEW_TEXT'] ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            <?= Html::endTag('div') ?>
                        <?php } ?>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>