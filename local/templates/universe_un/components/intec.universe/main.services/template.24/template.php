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

$iElementCounter = 0;
$bElementCounterUse = $arVisual['ELEMENTS']['COUNT'] > 0;

?>
<div class="widget c-services c-services-template-24" id="<?= $sTemplateId ?>">
    <?php if ($arBlocks['HEADER']['SHOW'] || $arBlocks['DESCRIPTION']['SHOW']) { ?>
        <div class="widget-header">
            <div class="intec-content">
                <div class="intec-content-wrapper">
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
            </div>
        </div>
    <?php } ?>
    <div class="widget-content">
        <div class="intec-content">
            <div class="intec-content-wrapper">
                <div class="widget-menu">
                    <?= Html::beginTag('div', [
                        'class' => [
                            'widget-menu-desktop-items',
                            'intec-grid' => [
                                '',
                                'a-v-center',
                                'a-h-center',
                            ],
                            'owl-carousel'
                        ],
                        'data' => [
                            'role' => 'services-menu',
                            'mobile-column' => $arVisual['MOBILE']['MENU']['USE'] ? 'true' : 'false'
                        ]
                    ]) ?>
                    <?php foreach ($arResult['SECTIONS'] as $arItem) { ?>
                        <?= Html::beginTag('div', [
                            'class' => [
                                'widget-menu-item',
                                'intec' => [
                                    'grid-item',
                                    'cl-text-hover',
                                    'cl-border-hover'
                                ]
                            ],
                            'data' => [
                                'role' => 'menu-item',
                                'menu-id' => $arItem['ID'],
                            ]
                        ]) ?>
                        <div class="widget-menu-item-name">
                            <?= $arItem['NAME'] ?>
                        </div>
                        <?= Html::endTag('div') ?>
                    <?php } ?>
                    <?= Html::endTag('div') ?>
                    <?php if ($arVisual['MOBILE']['MENU']['USE']) { ?>
                        <?php include(__DIR__.'/parts/mobile_menu.php'); ?>
                    <?php } ?>
                </div>
            </div>
        </div>
        <div class="widget-content-wrapper" data-mobile-status="<?= $arVisual['MOBILE']['MENU']['USE'] ? 'true' : 'false'; ?>">
            <div class="intec-content intec-content-visible">
                <div class="intec-content-wrapper">
                    <?php foreach ($arResult['SECTION_ITEMS'] as $sKey => $arItems) { ?>
                        <?php $iElementCounter = 0; ?>
                        <div class="section-content intec-grid intec-grid-wrap intec-grid-i-15" data-role="section" data-content-id="<?= $sKey ?>">
                            <?php foreach ($arItems as $arItem) {
                                if ($bElementCounterUse) {
                                    $iElementCounter++;
                                    if ($iElementCounter > $arVisual['ELEMENTS']['COUNT'])
                                        break;
                                }

                                $sId = $sTemplateId.'_'.$arItem['ID'];
                                $sAreaId = $this->GetEditAreaId($sId);
                                $this->AddEditAction($sId, $arItem['EDIT_LINK']);
                                $this->AddDeleteAction($sId, $arItem['DELETE_LINK']);

                                $sPicture = $arItem['PREVIEW_PICTURE'];

                                if (empty($sPicture))
                                    $sPicture = $arItem['DETAIL_PICTURE'];

                                if (!empty($sPicture)) {
                                    $sPicture = CFile::ResizeImageGet(
                                        $sPicture, [
                                        'width' => 900,
                                        'height' => 900
                                    ],
                                        BX_RESIZE_IMAGE_PROPORTIONAL_ALT
                                    );

                                    if (!empty($sPicture))
                                        $sPicture = $sPicture['src'];
                                }

                                if (empty($sPicture))
                                    $sPicture = SITE_TEMPLATE_PATH.'/images/picture.missing.png';

                                ?>
                                <?= Html::beginTag('div', [
                                    'class' => Html::cssClassFromArray([
                                        'widget-item' => true,
                                        'intec-grid-item' => [
                                            '' => true,
                                            $arVisual['COLUMNS'] => true,
                                            '1000-3' => $arVisual['COLUMNS'] >= 4,
                                            '800-2' => $arVisual['COLUMNS'] >= 3,
                                            '600-1' => $arVisual['COLUMNS'] >= 2
                                        ]
                                    ], true)
                                ]) ?>
                                    <?= Html::beginTag($arVisual['LINK']['USE'] && $arVisual['LINK']['WHOLE'] ? 'a' : 'div', [
                                        'class' => Html::cssClassFromArray([
                                            'widget-item-wrapper' => true,
                                            'intec-cl-text-hover' => $arVisual['LINK']['USE'] && $arVisual['LINK']['WHOLE'],
                                            'intec-grid' => [
                                                '' => true,
                                                'o-vertical' => true,
                                            ]
                                        ], true),
                                        'href' => $arVisual['LINK']['WHOLE'] ? $arItem['DETAIL_PAGE_URL'] : null
                                    ]) ?>
                                        <div class="widget-item-picture-wrapper">
                                            <?= Html::tag($arVisual['LINK']['USE'] && !$arVisual['LINK']['WHOLE'] ? 'a' : 'div', '', [
                                                'class' => Html::cssClassFromArray([
                                                    'widget-item-picture' => true,
                                                    'widget-item-picture-effect' => $arVisual['PICTURE']['EFFECT'],
                                                    'intec-image-effect' => !$arVisual['PICTURE']['EFFECT']
                                                ], true),
                                                'data' => [
                                                    'lazyload-use' => $arVisual['LAZYLOAD']['USE'] ? 'true' : 'false',
                                                    'original' => $arVisual['LAZYLOAD']['USE'] ? $sPicture : null
                                                ],
                                                'style' => [
                                                    'background-image' => $arVisual['LAZYLOAD']['USE'] ? 'url(\'' . $arVisual['LAZYLOAD']['STUB'] . '\')' : 'url(\'' . $sPicture . '\')'
                                                ]
                                            ]) ?>
                                        </div>
                                        <div class="widget-item-information intec-grid intec-grid-o-vertical">
                                            <?= Html::tag($arVisual['LINK']['USE'] && !$arVisual['LINK']['WHOLE'] ? 'a' : 'div',
                                                $arItem['NAME'], [
                                                'class' => [
                                                    'widget-item-name',
                                                    $arVisual['LINK']['USE'] && !$arVisual['LINK']['WHOLE'] ? 'intec-cl-text-hover' : null
                                                ],
                                                'href' => $arVisual['LINK']['USE'] && !$arVisual['LINK']['WHOLE'] ? $arItem['DETAIL_PAGE_URL'] : null,

                                            ]) ?>
                                            <div class="widget-item-description">
                                                <?= $arItem['DATA']['DESCRIPTION'] ?>
                                            </div>
                                            <?php if ($arVisual['PRICE']['CURRENT']['SHOW'] && $arItem['DATA']['PRICE']['CURRENT']['SHOW']) { ?>
                                                <div class="widget-item-price">
                                                    <?php if ($arVisual['PRICE']['OLD']['SHOW'] && $arItem['DATA']['PRICE']['OLD']['SHOW']) { ?>
                                                        <div class="widget-item-price-old">
                                                            <?= $arItem['DATA']['PRICE']['OLD']['PRINT'] ?>
                                                        </div>
                                                    <?php } ?>
                                                    <div class="widget-item-price-current">
                                                        <?= $arItem['DATA']['PRICE']['CURRENT']['PRINT'] ?>
                                                    </div>
                                                </div>
                                            <?php } ?>
                                            <?php if ($arVisual['ELEMENTS']['BUTTON']['SHOW'] && !$arVisual['LINK']['WHOLE']) {?>
                                                <?= Html::tag('a', $arVisual['ELEMENTS']['BUTTON']['TEXT'], [
                                                    'class' => [
                                                        'widget-item-button',
                                                        'intec-ui' => [
                                                            '',
                                                            'control-button',
                                                            'mod-round-2',
                                                            'scheme-current'
                                                        ]
                                                    ],
                                                    'href' => $arItem['DETAIL_PAGE_URL']
                                                ])?>
                                            <?php } ?>
                                        </div>
                                    <?= Html::endTag($arVisual['LINK']['USE'] && $arVisual['LINK']['WHOLE'] ? 'a' : 'div') ?>
                                <?= Html::endTag('div') ?>
                            <?php } unset($arItem); ?>
                        </div>
                    <?php } unset($arItems); ?>
                </div>
            </div>
        </div>
    </div>
    <?php if ($arBlocks['FOOTER']['SHOW']) { ?>
        <?= Html::beginTag('div', [
            'class' => Html::cssClassFromArray([
                'widget-footer' => true,
                'align-' . $arBlocks['FOOTER']['POSITION'] => true,
                'mobile' => $arBlocks['HEADER']['SHOW'] && $arBlocks['FOOTER']['BUTTON']['SHOW']
            ], true)
        ]) ?>
            <div class="intec-content">
                <div class="intec-content-wrapper">
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
                                        'round-2'
                                    ]
                                ]
                            ]
                        ]) ?>
                    <?php } ?>
                </div>
            </div>
        <?= Html::endTag('div') ?>
    <?php } ?>
    <?php include(__DIR__.'/parts/script.php'); ?>
</div>

